<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServiceWorkflowHealth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ServiceWorkflowHealthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Workflow Health Dashboard
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Only orders that have service and no health record
        $orders = Order::whereNotNull('service_id')
            ->doesntHave('workflowHealth')
            ->with(['tasks', 'service.workProcess'])
            ->get();

        foreach ($orders as $order) {
            $this->calculateWorkflowHealth($order->id);
        }

        $healthRecords = ServiceWorkflowHealth::with([
            'order.client',
            'service',
            'workProcess'
        ])
        ->latest('last_evaluated_at')
        ->paginate(20);

        $stats = [
            'ready' => ServiceWorkflowHealth::where('health_status', 'READY')->count(),
            'attention' => ServiceWorkflowHealth::where('health_status', 'ATTENTION')->count(),
            'not_ready' => ServiceWorkflowHealth::where('health_status', 'NOT_READY')->count(),
            'broken' => ServiceWorkflowHealth::where('order_ok', false)->count(),
        ];

        return view('admin.workflow-health.index', compact('healthRecords', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show Workflow Health
    |--------------------------------------------------------------------------
    */

    public function show(int $orderId)
    {
        $health = ServiceWorkflowHealth::with([
            'order',
            'order.client',
            'order.tasks',
            'order.service',
            'service',
            'workProcess'
        ])
        ->where('order_id', $orderId)
        ->firstOrFail();

        $canUpload = $this->canUploadCertificate($orderId);

        return response()->json([
            'success' => true,
            'health' => $health,
            'can_upload_certificate' => $canUpload
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Recalculate Workflow
    |--------------------------------------------------------------------------
    */

    public function recalculate(int $orderId)
    {
        $health = $this->calculateWorkflowHealth($orderId);

        return response()->json([
            'success' => true,
            'message' => 'Workflow recalculated successfully',
            'health' => $health
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Core Workflow Health Logic
    |--------------------------------------------------------------------------
    */

    public function calculateWorkflowHealth(int $orderId): ServiceWorkflowHealth
    {
        $order = Order::with([
            'tasks',
            'service.workProcess'
        ])->findOrFail($orderId);

        // Safety guard
        if (!$order->service_id) {
            throw new \Exception('Order has no service attached');
        }

        $tasks = $order->tasks;

        $serviceId = $order->service_id;
        $workProcessId = data_get($order, 'service.workProcess.id');

        /*
        |--------------------------------------------------------------------------
        | No Tasks Case
        |--------------------------------------------------------------------------
        */

        if ($tasks->isEmpty()) {
            return ServiceWorkflowHealth::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'service_id' => $serviceId,
                    'work_process_id' => $workProcessId,
                    'total_tasks' => 0,
                    'verified_tasks' => 0,
                    'pending_tasks' => 0,
                    'rejected_tasks' => 0,
                    'order_ok' => false,
                    'health_status' => 'NOT_READY',
                    'last_evaluated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Task Statistics
        |--------------------------------------------------------------------------
        */

        $totalTasks = $tasks->count();

        $verifiedTasks = $tasks->where('status', 'verified')->count();

        $rejectedTasks = $tasks->where('status', 'rejected')->count();

        $pendingTasks = $tasks->whereIn('status', [
            'unassigned',
            'assigned',
            'in_progress',
            'submitted',
            'waiting_client',
        ])->count();

        /*
        |--------------------------------------------------------------------------
        | Workflow Sequence Integrity
        |--------------------------------------------------------------------------
        */

        $orderOk = true;

        $tasksByStep = $tasks->sortBy('work_process_step_id');

        $previousVerified = true;

        foreach ($tasksByStep as $task) {
            if ($task->status === 'verified') {
                if (!$previousVerified) {
                    $orderOk = false;
                    break;
                }
            } else {
                $previousVerified = false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Health Status
        |--------------------------------------------------------------------------
        */

        $healthStatus = 'NOT_READY';

        if ($rejectedTasks > 0) {
            $healthStatus = 'ATTENTION';
        } elseif (
            $totalTasks > 0 &&
            $totalTasks === $verifiedTasks &&
            $pendingTasks === 0 &&
            $orderOk === true
        ) {
            $healthStatus = 'READY';
        }

        /*
        |--------------------------------------------------------------------------
        | Save Health Record
        |--------------------------------------------------------------------------
        */

        return ServiceWorkflowHealth::updateOrCreate(
            ['order_id' => $order->id],
            [
                'service_id' => $serviceId,
                'work_process_id' => $workProcessId,
                'total_tasks' => $totalTasks,
                'verified_tasks' => $verifiedTasks,
                'pending_tasks' => $pendingTasks,
                'rejected_tasks' => $rejectedTasks,
                'order_ok' => $orderOk,
                'health_status' => $healthStatus,
                'last_evaluated_at' => Carbon::now(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Certificate Upload Guard
    |--------------------------------------------------------------------------
    */

    public function canUploadCertificate(int $orderId): bool
    {
        $health = ServiceWorkflowHealth::where('order_id', $orderId)->first();

        if (!$health) {
            return false;
        }

        return (
            $health->health_status === 'READY' &&
            $health->order_ok === true &&
            $health->verified_tasks === $health->total_tasks
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Certificate (first time)
    |--------------------------------------------------------------------------
    */

    public function uploadCertificate(Request $request, int $orderId)
    {
        return $this->replaceCertificate($request, $orderId);
    }

    /*
    |--------------------------------------------------------------------------
    | Replace Certificate
    |--------------------------------------------------------------------------
    */

    public function replaceCertificate(Request $request, int $orderId)
    {
        $request->validate([
            'certificate' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120'
        ]);

        $order = Order::findOrFail($orderId);

        $health = ServiceWorkflowHealth::where('order_id', $orderId)->first();

        if (!$health ||
            $health->health_status !== 'READY' ||
            !$health->order_ok ||
            $health->verified_tasks !== $health->total_tasks
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Workflow not ready for certificate upload/replacement'
            ], 403);
        }

        if ($order->certificate_path) {
            Storage::disk('public')->delete($order->certificate_path);
        }

        $file = $request->file('certificate');

        $extension = $file->getClientOriginalExtension();

        $filename = 'CERT-' . $order->order_id . '-' . time() . '.' . $extension;

        Storage::disk('public')->putFileAs(
            'final_certificate',
            $file,
            $filename
        );

        $order->certificate_path = 'final_certificate/' . $filename;

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Certificate uploaded/replaced successfully',
            'path' => asset('storage/' . $order->certificate_path)
        ]);
    }
}