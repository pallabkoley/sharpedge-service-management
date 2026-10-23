<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Backoffice dashboard page
     */
         public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // ✅ Attendance for today
        $attendance = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // staff_id for tasks assigned_to
        $staffId = $user->staff ? $user->staff->id : null;

        // ✅ If no staff linked, show empty stats safely
        if (!$staffId) {
            return view('Backoffice.Dashboard', [
                'attendance' => $attendance,
                'totalTasks' => 0,
                'inProgress' => 0,
                'overdue'    => 0,
                'completed'  => 0,
                'recentTasks' => [],
                'chartLabels' => [],
                'chartData'   => [],
            ])->with('error', 'Your account is not linked with a staff profile.');
        }

        // ✅ KPI Cards counts
        $totalTasks = Task::where('assigned_to', $staffId)->count();

        $inProgress = Task::where('assigned_to', $staffId)
            ->where('status', 'in_progress')
            ->count();

        $overdue = Task::where('assigned_to', $staffId)
            ->whereDate('due_date', '<', now())
            ->whereNotIn('status', ['verified', 'submitted'])
            ->count();

        $completed = Task::where('assigned_to', $staffId)
            ->where('status', 'verified')
            ->count();

        // ✅ Recent Tasks (5 most recent)
        $recentTasks = Task::where('assigned_to', $staffId)
            ->with(['order.client', 'service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ✅ Chart Data for last 6 months (including current month)
        $chartData = $this->getChartData($staffId);

        return view('Backoffice.Dashboard', [
            'attendance' => $attendance,
            'totalTasks' => $totalTasks,
            'inProgress' => $inProgress,
            'overdue'    => $overdue,
            'completed'  => $completed,
            'recentTasks' => $recentTasks,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get chart data for last 6 months
     */
    private function getChartData($staffId)
    {
        $labels = [];
        $completedData = [];
        $inProgressData = [];
        $overdueData = [];

        // Get data for last 6 months including current month
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $year = $date->year;
            $monthNum = $date->month;
            
            $labels[] = $month . ' ' . $date->format('y');

            // Completed tasks in this month
            $completedCount = Task::where('assigned_to', $staffId)
                ->where('status', 'verified')
                ->whereYear('verified_at', $year)
                ->whereMonth('verified_at', $monthNum)
                ->count();
            $completedData[] = $completedCount;

            // In Progress tasks created in this month
            $inProgressCount = Task::where('assigned_to', $staffId)
                ->where('status', 'in_progress')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();
            $inProgressData[] = $inProgressCount;

            // Overdue tasks (due in this month and still pending)
            $overdueCount = Task::where('assigned_to', $staffId)
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $monthNum)
                ->whereDate('due_date', '<', now())
                ->whereNotIn('status', ['verified', 'submitted'])
                ->count();
            $overdueData[] = $overdueCount;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Completed',
                    'data' => $completedData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'In Progress',
                    'data' => $inProgressData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Overdue',
                    'data' => $overdueData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }
    /**
     * Handle Clock In (Backoffice)
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $existing = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('backoffice.dashboard')
                ->with('error', 'You have already clocked in today!');
        }

        $staffId = $user->staff ? $user->staff->id : null;

        AttendanceLog::create([
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'date' => $today,
            'clock_in_time' => Carbon::now()->format('H:i:s'),
            'clock_out_time' => null,
            'total_work_seconds' => 0,
            'auto_clock_out' => false,
        ]);

        return redirect()->route('backoffice.dashboard')
            ->with('success', 'Clocked in successfully!');
    }

    /**
     * Handle Clock Out (Backoffice)
     */
public function clockOut(Request $request)
{
    $user = Auth::user();
    $today = Carbon::today()->toDateString();

    $attendance = AttendanceLog::where('user_id', $user->id)
        ->whereDate('date', $today)
        ->whereNull('clock_out_time')
        ->first();

    if (!$attendance) {
        return redirect()->route('backoffice.dashboard')
            ->with('error', 'No active clock-in found for today!');
    }

    // Fix datetime creation
    $clockInTime = Carbon::parse($attendance->clock_in_time);

    $clockOutTime = Carbon::now();

    $totalWorkSeconds = $clockInTime->diffInSeconds($clockOutTime);

    $attendance->update([
        'clock_out_time' => $clockOutTime->format('H:i:s'),
        'total_work_seconds' => $totalWorkSeconds,
        'auto_clock_out' => false,
    ]);

    return redirect()->route('backoffice.dashboard')
        ->with('success', 'Clocked out successfully!');
}
}
