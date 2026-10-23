<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the marketing dashboard
     */
public function index()
{
    $user = Auth::user();
    $staffId = $user->staff ? $user->staff->id : null;
    $today = Carbon::today()->toDateString();

    /* =========================
       CARD 1: Total Clients
       (unique clients via orders)
    ========================== */
    $totalClients = Order::where('marketing_staff_id', $staffId)
        ->distinct('client_id')
        ->count('client_id');

    /* =========================
       CARD 2: Active Orders / Campaigns
    ========================== */
    $activeOrdersCount = Order::where('marketing_staff_id', $staffId)
        ->whereIn('order_status', [
            'APPROVED',
            'IN_PROGRESS',
            'ACTIVE',
        ])
        ->count();

    /* =========================
       CARD 3: Pending Invoices (Amount)
       (invoice generated but not paid)
    ========================== */
    $pendingInvoiceAmount = Order::where('marketing_staff_id', $staffId)
        ->where('invoice_generated', 1)
        ->where('payment_status', '!=', 'PAID')
        ->sum('final_amount');

    /* =========================
       CARD 4: Approved Amount
       (actual paid business)
    ========================== */
    $approvedAmount = Order::where('marketing_staff_id', $staffId)
        ->where('payment_status', 'PAID')
        ->sum('final_amount');

    /* =========================
       CARD 5: Conversion Rate
       (paid orders / total orders)
    ========================== */
    $totalOrders = Order::where('marketing_staff_id', $staffId)->count();

    $paidOrders = Order::where('marketing_staff_id', $staffId)
        ->where('payment_status', 'PAID')
        ->count();

    $conversionRate = $totalOrders > 0
        ? round(($paidOrders / $totalOrders) * 100, 1)
        : 0;

    /* =========================
       CHART: Monthly Client Growth
    ========================== */
    $monthlyData = $this->getMonthlyClientData($staffId);

    /* =========================
       RECENT ACTIVITIES (Latest 5 Orders)
    ========================== */
    $recentActivities = Order::with(['client', 'items.service'])
        ->where('marketing_staff_id', $staffId)
        ->latest()
        ->take(5)
        ->get();

    /* =========================
       Attendance (Clock In / Out)
    ========================== */
    $attendance = AttendanceLog::where('user_id', $user->id)
        ->where('date', $today)
        ->first();

    return view('Marketing.dashboard', [
        'totalClients'         => $totalClients,
        'activeOrdersCount'    => $activeOrdersCount,
        'pendingInvoiceAmount' => $pendingInvoiceAmount,
        'approvedAmount'       => $approvedAmount,
        'conversionRate'       => $conversionRate,

        'monthlyData'          => $monthlyData,
        'recentActivities'     => $recentActivities,
        'attendance'           => $attendance,
    ]);
}





    /**
     * Clock In
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $existing = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('marketing.dashboard')
                ->with('error', 'You have already clocked in today!');
        }

        $staffId = $user->staff ? $user->staff->id : null;

        AttendanceLog::create([
            'user_id'            => $user->id,
            'staff_id'           => $staffId,
            'date'               => $today,
            'clock_in_time'      => Carbon::now()->format('H:i:s'),
            'clock_out_time'     => null,
            'total_work_seconds' => 0,
            'auto_clock_out'     => false,
        ]);

        return redirect()->route('marketing.dashboard')
            ->with('success', 'Clocked in successfully!');
    }

    /**
     * Clock Out
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
        return redirect()->route('marketing.dashboard')
            ->with('error', 'No active clock-in found for today!');
    }

    // Fix date format
    $dateOnly = Carbon::parse($attendance->date)->format('Y-m-d');

    $clockInTime = Carbon::parse($dateOnly . ' ' . $attendance->clock_in_time);

    $clockOutTime = Carbon::now();

    $totalWorkSeconds = $clockInTime->diffInSeconds($clockOutTime);

    $attendance->update([
        'clock_out_time' => $clockOutTime->format('H:i:s'),
        'total_work_seconds' => $totalWorkSeconds,
        'auto_clock_out' => false,
    ]);

    return redirect()->route('marketing.dashboard')
        ->with('success', 'Clocked out successfully!');
}
    /**
     * Monthly Client Data (for chart)
     */
private function getMonthlyClientData($staffId)
{
    $currentYear = date('Y');
    $monthlyCounts = [];

    for ($month = 1; $month <= 12; $month++) {
        $count = Order::where('marketing_staff_id', $staffId)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $month)
            ->distinct('client_id')
            ->count('client_id');

        $monthlyCounts[] = $count;
    }

    return $monthlyCounts;
}

}
