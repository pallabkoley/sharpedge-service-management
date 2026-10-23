<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Monthly Attendance Report
     */
public function index(Request $request)
{
    /* =========================
       Month & Year
    ========================== */
    $month = (int) ($request->month ?? now()->month);
    $year  = (int) ($request->year ?? now()->year);

    $startDate   = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate     = Carbon::create($year, $month, 1)->endOfMonth();
    $daysInMonth = $startDate->daysInMonth;

    /* =========================
       Staff Filter Query
    ========================== */
    $staffQuery = Staff::query();

    if ($request->filled('staff_id')) {
        $staffQuery->where('id', $request->staff_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $staffQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('staff_code', 'like', "%{$search}%");
        });
    }

    if ($request->filled('department')) {
        $staffQuery->where('department', $request->department);
    }

    if ($request->filled('designation')) {
        $staffQuery->where('role_id', $request->designation);
    }

    $staffs   = $staffQuery->orderBy('name')->get();
    $staffIds = $staffs->pluck('id')->toArray();

    /* =========================
       Attendance Logs (For Grid)
    ========================== */
    $attendanceLogs = [];

    if (!empty($staffIds)) {
        $logs = AttendanceLog::whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
            ->whereIn('staff_id', $staffIds)
            ->get()
            ->groupBy([
                'staff_id',
                fn ($log) => $log->date->format('Y-m-d')
            ]);

        foreach ($logs as $staffId => $dates) {
            foreach ($dates as $date => $records) {
                $attendanceLogs[$staffId][$date] = $records->first();
            }
        }
    }

    /* =========================
       Approved Leaves
    ========================== */
    $approvedLeaves = [];

    if (!empty($staffIds)) {
        $leaves = LeaveRequest::where('status', 'approved')
            ->whereDate('from_date', '<=', $endDate)
            ->whereDate('to_date', '>=', $startDate)
            ->whereIn('staff_id', $staffIds)
            ->get();

        foreach ($leaves as $leave) {
            $from = Carbon::parse($leave->from_date);
            $to   = Carbon::parse($leave->to_date);

            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                if ($d->between($startDate, $endDate)) {
                    $approvedLeaves[$leave->staff_id][$d->format('Y-m-d')] = true;
                }
            }
        }
    }

    /* =========================
       Holidays
    ========================== */
    $holidayMap = Holiday::whereBetween('date', [
            $startDate->toDateString(),
            $endDate->toDateString()
        ])
        ->get()
        ->keyBy(fn ($h) => $h->date->format('Y-m-d'));

    /* =========================
       Attendance Grid + Stats
    ========================== */
    $attendanceGrid = [];
    $staffSummaries = [];

    $statistics = [
        'present' => 0,
        'holiday' => 0,
        'leave' => 0,
        'inprogress' => 0,
        'unmarked' => 0,
        'total_cells' => 0,
        'total_working_days' => 0,
    ];

    $todayDate = now()->toDateString();

    foreach ($staffs as $staff) {
        $staffId = $staff->id;

        $staffSummaries[$staffId] = [
            'present_days' => 0,
            'leave_days' => 0,
            'worked_hours' => 0,
            'inprogress_days' => 0,
            'unmarked_days' => 0,
        ];

        /* ========= GRID STATUS ========= */
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateKey = Carbon::create($year, $month, $day)->format('Y-m-d');

            $record  = $attendanceLogs[$staffId][$dateKey] ?? null;
            $holiday = $holidayMap[$dateKey] ?? null;

            $status = 'NM';

            if (isset($approvedLeaves[$staffId][$dateKey])) {
                $status = 'L';
                $statistics['leave']++;
                $staffSummaries[$staffId]['leave_days']++;
            }
            elseif ($holiday) {
                $status = 'H';
                $statistics['holiday']++;
            }
            elseif ($record) {
                if ($record->clock_in_time && $record->clock_out_time && $record->total_work_seconds > 0) {
                    $status = 'P';
                    $statistics['present']++;
                    $staffSummaries[$staffId]['present_days']++;
                } elseif ($record->clock_in_time) {
                    $status = 'IN';
                    $statistics['inprogress']++;
                    $staffSummaries[$staffId]['inprogress_days']++;
                } else {
                    $statistics['unmarked']++;
                    $staffSummaries[$staffId]['unmarked_days']++;
                }
            }
            elseif ($dateKey < $todayDate) {
                $status = 'A';
            }
            else {
                $statistics['unmarked']++;
                $staffSummaries[$staffId]['unmarked_days']++;
            }

         $attendanceGrid[$staffId][$day] = [
    'status' => $status,
    'date' => $dateKey,
    'clock_in' => $record?->clock_in_time,
    'clock_out' => $record?->clock_out_time,
    'seconds' => ($record && $record->clock_out_time)
        ? (int) $record->total_work_seconds
        : 0,
];

        }

        /* ========= WORKED HOURS (FINAL SOURCE OF TRUTH) ========= */
        $totalSeconds = AttendanceLog::where('staff_id', $staffId)
            ->whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
            ->whereNotNull('clock_out_time')
            ->where('total_work_seconds', '>', 0)
            ->sum('total_work_seconds');

        $staffSummaries[$staffId]['worked_hours'] = $totalSeconds;

        $staffSummaries[$staffId]['worked_hours_formatted'] =
            floor($totalSeconds / 3600) . 'h ' .
            floor(($totalSeconds % 3600) / 60) . 'm';
    }

    $statistics['total_cells'] =
        $staffs->count() * $daysInMonth;

    $statistics['total_working_days'] =
        ($daysInMonth - $holidayMap->count()) * $staffs->count();

    /* =========================
       Dropdown Data
    ========================== */
    $departments = Staff::whereNotNull('department')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    $roles = Role::orderBy('name')->get();

    $today = now();
    $currentDay = ($today->year == $year && $today->month == $month)
        ? $today->day
        : null;

    return view('admin.attendance.report', compact(
        'staffs',
        'attendanceGrid',
        'staffSummaries',
        'statistics',
        'month',
        'year',
        'daysInMonth',
        'departments',
        'roles',
        'currentDay'
    ))->with('filters', $request->only([
        'staff_id',
        'department',
        'designation',
        'month',
        'year'
    ]));
}

}
