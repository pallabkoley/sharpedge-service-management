<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\Auth;

class BackofficeAttendanceController extends Controller
{
    /**
     * CLOCK IN (only once per day)
     */
    public function clockIn()
    {
        $user = Auth::user();

        if (!$user->staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found'
            ], 403);
        }

        $staffId = $user->staff->id;
        $today = now()->toDateString();

        $attendance = StaffAttendance::where('staff_id', $staffId)
            ->where('attendance_date', $today)
            ->first();

        if ($attendance && $attendance->clock_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Already clocked in today'
            ], 400);
        }

        StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staffId,
                'attendance_date' => $today,
            ],
            [
                'clock_in_at' => now(),
                'status' => 'present',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Clock in successful',
            'clock_in_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * CLOCK OUT (only once per day, after clock-in)
     */
    public function clockOut()
    {
        $user = Auth::user();

        if (!$user->staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found'
            ], 403);
        }

        $staffId = $user->staff->id;
        $today = now()->toDateString();

        $attendance = StaffAttendance::where('staff_id', $staffId)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'You must clock in first'
            ], 400);
        }

        if ($attendance->clock_out_at) {
            return response()->json([
                'success' => false,
                'message' => 'Already clocked out today'
            ], 400);
        }

        $attendance->update([
            'clock_out_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clock out successful',
            'clock_out_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * TODAY STATUS (used by dashboard to enable/disable buttons)
     */
    public function todayStatus()
    {
        $user = Auth::user();

        if (!$user->staff) {
            return response()->json([
                'clocked_in' => false,
                'clocked_out' => false,
            ]);
        }

        $attendance = StaffAttendance::where('staff_id', $user->staff->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        return response()->json([
            'clocked_in' => (bool) optional($attendance)->clock_in_at,
            'clocked_out' => (bool) optional($attendance)->clock_out_at,
            'clock_in_at' => optional($attendance)->clock_in_at?->format('H:i:s'),
            'clock_out_at' => optional($attendance)->clock_out_at?->format('H:i:s'),
        ]);
    }
}
