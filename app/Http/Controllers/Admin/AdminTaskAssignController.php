<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTaskAssignController extends Controller
{
    /**
     * Show all unassigned tasks for admin allocation
     */
    public function index(Request $request)
    {
        $tasks = Task::with([
                'order.client',
                'service',
                'workProcess',
                'workProcess.steps',
                'workProcessStep',
                'assignedStaff'
            ])
            ->where('status', 'unassigned')
            ->orderBy('created_at', 'asc')
            ->get();

        $staffs = Staff::where('status', 'active')
            ->whereHas('role', function ($q) {
                $q->where('name', 'backoffice');
            })
            ->orderBy('name')
            ->get();

        return view('admin.tasks.assign', compact('tasks', 'staffs'));
    }

    /**
     * Assign a task to backoffice staff
     */
    public function assign(Request $request, Task $task)
    {
        $request->validate([
            'assigned_to'  => 'required|exists:staffs,id',
            'priority'     => 'required|in:low,medium,high',
            'due_date'     => 'nullable|date|after_or_equal:today',
            'admin_notes'  => 'nullable|string|max:2000',
        ]);

        // Safety check
        if ($task->status !== 'unassigned') {
            return back()->with('error', 'This task is already assigned.');
        }

        DB::transaction(function () use ($task, $request) {

            $updated = Task::where('id', $task->id)
    ->where('status', 'unassigned')
    ->update([
        'assigned_to' => $request->assigned_to,
        'priority'    => $request->priority,
        'due_date'    => $request->due_date,
        'admin_notes' => $request->admin_notes,
        'status'      => 'in_progress',

        // 🔥 THIS LINE MAKES SIDEBAR BLINK
        'is_new_for_staff' => 1,
    ]);


            if (! $updated) {
                throw new \Exception('Task assignment failed.');
            }
        });

        return back()->with('success', 'Task assigned successfully.');
    }

    /**
     * Reassign a task (admin override)
     */
    public function reassign(Request $request, Task $task)
    {
        $request->validate([
            'assigned_to' => 'required|exists:staffs,id',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($task, $request) {
            $task->update([
                'assigned_to' => $request->assigned_to,
                'admin_notes' => $request->admin_notes,
            ]);
        });

        return back()->with('success', 'Task reassigned successfully.');
    }
}
