<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminTaskVerificationController extends Controller
{
    /**
     * =====================================================
     * LIST TASKS (ALL / PENDING / VERIFIED / REJECTED)
     * =====================================================
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        // -------------------------
        // COUNTS (cards)
        // -------------------------
        $pendingCount = Task::where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->count();

        $verifiedCount = Task::where('status', 'verified')->count();

        $rejectedCount = Task::whereNotNull('rejected_at')->count();

        $totalCount = Task::count();

        // -------------------------
        // TASK QUERY
        // -------------------------
        $query = Task::query();

        if ($status == 'pending') {
            $query->where('status', 'submitted')
                  ->whereNotNull('submitted_at');
        } elseif ($status == 'verified') {
            $query->where('status', 'verified');
        } elseif ($status == 'rejected') {
            $query->whereNotNull('rejected_at');
        }

        // -------------------------
        // PAGINATION
        // -------------------------
        $tasks = $query->latest()->get();

        // -------------------------
        // 🔥 DEBUG (REMOVE AFTER FIX)
        // -------------------------
        // dd([
        //     'status_from_url'        => $status,
        //     'total_tasks_in_db'      => $totalCount,
        //     'tasks_after_filter'     => $tasks->total(),
        //     'current_page_count'     => $tasks->count(),
        //     'current_page'           => $tasks->currentPage(),
        //     'last_page'              => $tasks->lastPage(),
        // ]);

        return view(
            'admin.tasks.submitted',
            compact(
                'tasks',
                'status',
                'pendingCount',
                'verifiedCount',
                'rejectedCount',
                'totalCount'
            )
        );
    }

    /**
     * =====================================================
     * SHOW TASK DETAILS (AJAX)
     * =====================================================
     */
    public function show($taskId)
    {
        $task = Task::with([
            'documents',
            'messages.sender',
            'assignedStaff',
            'service',
            'verifiedBy',
            'rejectedBy',
        ])->findOrFail($taskId);

        $html = view('admin.tasks.partials.task-details-modal', compact('task'))->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
            'task'    => [
                'id'        => $task->id,
                'task_code' => $task->task_code,
                'status'    => $task->status,
            ]
        ]);
    }

    /**
     * =====================================================
     * VERIFY TASK
     * =====================================================
     */
    public function verify(Request $request, $taskId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $admin = Auth::user();

        DB::transaction(function () use ($taskId, $admin, $request) {

            $task = Task::with('documents')
                ->lockForUpdate()
                ->findOrFail($taskId);

            if (!$task->isSubmitted()) {
                throw new \Exception('Task already processed');
            }

            foreach ($task->documents as $document) {

                if ($document->status !== 'submitted') continue;

                $oldPath  = $document->file_path;
                $fileName = basename($oldPath);
                $newPath  = 'Backoffice_verified_Documents/' . $task->task_code . '/' . $fileName;

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->makeDirectory(
                        'Backoffice_verified_Documents/' . $task->task_code
                    );
                    Storage::disk('public')->move($oldPath, $newPath);
                }

                $document->update([
                    'file_path'   => $newPath,
                    'status'      => 'verified',
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ]);
            }

            $task->update([
                'status'       => 'verified',
                'verified_at'  => now(),
                'verified_by'  => $admin->id,
                'admin_notes'  => $request->admin_notes,
                'rejected_at'  => null,
                'rejected_by'  => null,
            ]);

            TaskMessage::create([
                'task_id'     => $task->id,
                'sender_id'   => $admin->id,
                'sender_role' => 'admin',
                'message'     => 'Task verified successfully',
                'is_read'     => false,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Task verified successfully',
        ]);
    }

    /**
     * =====================================================
     * REJECT TASK
     * =====================================================
     */
    public function reject(Request $request, $taskId)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $admin = Auth::user();

        DB::transaction(function () use ($taskId, $admin, $request) {

            $task = Task::with('documents')
                ->lockForUpdate()
                ->findOrFail($taskId);

            if (!$task->isSubmitted()) {
                throw new \Exception('Task already processed');
            }

            foreach ($task->documents as $document) {
                if ($document->status === 'submitted') {
                    $document->update([
                        'status'  => 'rejected',
                        'remarks' => $request->admin_notes,
                    ]);
                }
            }

            $task->update([
                'status'           => 'assigned',
                'submitted_at'     => null,
                'verified_at'      => null,
                'verified_by'      => null,
                'admin_notes'      => $request->admin_notes,
                'rejected_at'      => now(),
                'rejected_by'      => $admin->id,
                'is_new_for_staff' => 1,
            ]);

            TaskMessage::create([
                'task_id'     => $task->id,
                'sender_id'   => $admin->id,
                'sender_role' => 'admin',
                'message'     => 'Task rejected: ' . $request->admin_notes,
                'is_read'     => false,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Task rejected successfully',
        ]);
    }
}