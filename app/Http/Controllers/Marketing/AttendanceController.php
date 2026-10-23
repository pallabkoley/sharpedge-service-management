<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // already clocked in today
        $existing = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('marketing.dashboard')
                ->with('error', 'You already clocked in today!');
        }

        $staffId = $user->staff ? $user->staff->id : null;

        AttendanceLog::create([
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'date' => $today,
            'clock_in_time' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->route('marketing.dashboard')
            ->with('success', 'Clock In successful!');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = AttendanceLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->route('marketing.dashboard')
                ->with('error', 'You must Clock In first!');
        }

        if ($attendance->clock_out_time) {
            return redirect()->route('marketing.dashboard')
                ->with('error', 'You already Clocked Out today!');
        }

        $clockOutTime = Carbon::now();
        $clockInTime = Carbon::createFromFormat('H:i:s', $attendance->clock_in_time);

        $totalSeconds = $clockOutTime->diffInSeconds($clockInTime);

        $attendance->update([
            'clock_out_time' => $clockOutTime->format('H:i:s'),
            'total_work_seconds' => $totalSeconds,
            'auto_clock_out' => false,
        ]);

        return redirect()->route('marketing.dashboard')
            ->with('success', 'Clock Out successful!');
    }
}
