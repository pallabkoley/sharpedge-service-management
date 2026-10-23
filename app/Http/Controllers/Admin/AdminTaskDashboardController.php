<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;

class AdminTaskDashboardController extends Controller
{
    public function index()
    {
        $recentTasks = Task::with([
                'service:id,name',
                'order:id,client_id',
                'order.client:id,name',
                'assignedStaff:id,name'
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'recentTasks' => $recentTasks,
        ]);
    }
}
