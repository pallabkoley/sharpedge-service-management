<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Order;
use App\Models\AttendanceLog;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\PaymentFollowUp;

// ✅ NEW: Client Master models for reminders
use App\Models\ClientCase;
use App\Models\ClientCompliance;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show Admin Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->startOfMonth();

        /* =======================
           ATTENDANCE (TODAY)
        ======================= */
        $attendance = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        /* =======================
           TOTAL SERVICES
        ======================= */
        $totalServices = Service::count();

        $lastMonthServices = Service::where(
            'created_at',
            '<',
            $thisMonth
        )->count();

        $serviceIncrease = $totalServices - $lastMonthServices;

        /* =======================
           ACTIVE CLIENTS
        ======================= */
        $activeClients = Client::where('status', 'active')->count();

        $lastMonthClients = Client::where('status', 'active')
            ->where('created_at', '<', $thisMonth)
            ->count();

        $clientIncrease = $activeClients - $lastMonthClients;

        /* =======================
           VERIFIED TASKS
        ======================= */
        $verifiedTasks = Task::where('status', 'verified')->count();

        /* =======================
           RECENT TASKS
        ======================= */
        $recentTasks = Task::with([
            'service:id,service_name',
            'order:id,client_id',
            'order.client:id,name',
            'assignedStaff:id,name'
        ])
        ->latest()
        ->limit(5)
        ->get();

        /* =======================
           MONTHLY REVENUE
        ======================= */
        $currentMonthRevenue = Payment::where('status', 'approved')
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'approved')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');

        $revenueIncrease = $currentMonthRevenue - $lastMonthRevenue;

        /* =======================
           PAYMENT STATS
        ======================= */
        $totalApprovedPayments = Payment::where('status', 'approved')->count();

        $pendingPaymentsCount = Order::whereHas('payments', function ($q) {
            $q->where('status', 'submitted');
        })->count();

        $uniqueClientsWithPayments = Order::whereHas('payments', function ($q) {
            $q->where('status', 'approved');
        })
        ->distinct('client_id')
        ->count('client_id');

        /* =======================
           MEETING STATS
        ======================= */
        $pendingMeetings = Meeting::whereIn('status', [
            'availability_requested',
            'availability_provided'
        ])->count();

        $latestMeetings = Meeting::with([
            'client',
            'requester'
        ])
        ->latest()
        ->limit(5)
        ->get();

        $unreadNotifications = Meeting::whereIn('status', [
            'availability_requested',
            'availability_provided'
        ])
        ->where('created_at', '>=', now()->subDays(7))
        ->count();

        /* =======================
           TODAY'S PAY LATER
           FOLLOW-UP NOTIFICATIONS
        ======================= */

        $todayFollowUps = PaymentFollowUp::with([
            'payment.client'
        ])
        ->whereDate('next_follow_up_date', Carbon::today())
        ->whereHas('payment', function ($query) {
            $query->whereRaw(
                'UPPER(payment_method) = ?',
                ['PAY_LATER']
            )
            ->where('payment_received', false);
        })
        ->get();

        $todayFollowUpCount = $todayFollowUps->count();

        /* ========================================================
           🆕 CLIENT MASTER REMINDERS
           CASE FOLLOW-UP + COMPLIANCE DUE
        ======================================================== */

        $clientMasterReminders = [];
        $todayCarbon = Carbon::today();

        // ---- CASE FOLLOW-UP REMINDERS ----
        $cases = ClientCase::with('client')
            ->where('status', '!=', 'Closed')
            ->whereNotNull('next_follow_up_date')
            ->get();

        foreach ($cases as $case) {
            $followUpDate = Carbon::parse($case->next_follow_up_date)->startOfDay();
            $reminderStartDate = $followUpDate->copy()->subDays(10);

            if ($todayCarbon->gte($reminderStartDate)) {
                $daysRemaining = $todayCarbon->diffInDays($followUpDate, false);

                if ($daysRemaining < 0) {
                    $status = 'overdue';
                } elseif ($daysRemaining == 0) {
                    $status = 'today';
                } else {
                    $status = 'upcoming';
                }

                $clientMasterReminders[] = [
                    'type'        => 'Case Follow-up',
                    'client_name' => $case->client->name ?? 'N/A',
                    'title'       => $case->case_type ?? 'Case',
                    'period'      => $case->financial_year ?? null,
                    'target_date' => $followUpDate->format('d-m-Y'),
                    'days'        => $daysRemaining,
                    'status'      => $status,
                    'url'         => route('admin.client-master.show', $case->client_id) . '?tab=cases',
                ];
            }
        }

        // ---- COMPLIANCE DUE REMINDERS ----
        $compliances = ClientCompliance::with('client')
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->get();

        foreach ($compliances as $compliance) {
            $dueDate = Carbon::parse($compliance->due_date)->startOfDay();
            $reminderStartDate = $dueDate->copy()->subDays(10);

            if ($todayCarbon->gte($reminderStartDate)) {
                $daysRemaining = $todayCarbon->diffInDays($dueDate, false);

                if ($daysRemaining < 0) {
                    $status = 'overdue';
                } elseif ($daysRemaining == 0) {
                    $status = 'today';
                } else {
                    $status = 'upcoming';
                }

                $clientMasterReminders[] = [
                    'type'            => 'Compliance Due',
                    'client_name'     => $compliance->client->name ?? 'N/A',
                    'title'           => $compliance->compliance_type ?? 'Compliance',
                    'period'          => $compliance->period ?? null,
                    'target_date'     => $dueDate->format('d-m-Y'),
                    'days'            => $daysRemaining,
                    'status'          => $status,
                    'url'             => route('admin.client-master.show', $compliance->client_id) . '?tab=compliances',
                ];
            }
        }

        // Sort by nearest date (overdue first)
        usort($clientMasterReminders, function ($a, $b) {
            return $a['days'] <=> $b['days'];
        });

        $clientMasterReminderCount = count($clientMasterReminders);

        /* =======================
           REVENUE CHART
        ======================= */
        $revenueData = $this->getRevenueChartData();

        /* =======================
           RETURN DASHBOARD
        ======================= */
        return view('admin.dashboard', compact(
            'attendance',

            'totalServices',
            'serviceIncrease',

            'activeClients',
            'clientIncrease',

            'verifiedTasks',

            'currentMonthRevenue',
            'revenueIncrease',

            'totalApprovedPayments',
            'pendingPaymentsCount',
            'uniqueClientsWithPayments',

            'revenueData',
            'recentTasks',

            'pendingMeetings',
            'latestMeetings',
            'unreadNotifications',

            // Follow-up notification data (Pay Later)
            'todayFollowUps',
            'todayFollowUpCount',

            // 🆕 Client Master reminders
            'clientMasterReminders',
            'clientMasterReminderCount'
        ));
    }

    /**
     * Clock In
     */
    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $existing = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You already clocked in today!');
        }

        AttendanceLog::create([
            'user_id' => $user->id,
            'staff_id' => $user->staff?->id,
            'date' => $today,
            'clock_in_time' => Carbon::now()->format('H:i:s'),
            'clock_out_time' => null,
            'total_work_seconds' => 0,
            'auto_clock_out' => false,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Clocked in successfully!');
    }

    /**
     * Clock Out
     */
    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = AttendanceLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNull('clock_out_time')
            ->first();

        if (!$attendance) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'No active clock-in found for today!');
        }

        $clockInTime = Carbon::parse($attendance->clock_in_time);
        $clockOutTime = Carbon::now();
        $totalWorkSeconds = $clockInTime->diffInSeconds($clockOutTime);

        $attendance->update([
            'clock_out_time' => $clockOutTime->format('H:i:s'),
            'total_work_seconds' => $totalWorkSeconds,
            'auto_clock_out' => false,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Clocked out successfully!');
    }

    /**
     * Revenue chart data – Last 6 months
     */
    private function getRevenueChartData()
    {
        $months = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);

            $revenue = Payment::where('status', 'approved')
                ->whereBetween('created_at', [
                    $date->copy()->startOfMonth(),
                    $date->copy()->endOfMonth()
                ])
                ->sum('amount');

            $months[] = $date->format('M Y');
            $revenues[] = (float) $revenue;
        }

        return [
            'months' => $months,
            'revenues' => $revenues
        ];
    }
}