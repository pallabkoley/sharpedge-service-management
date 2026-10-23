<?php

namespace App\Services\Attendance;

use App\Models\AttendanceLog;
use Carbon\Carbon;

class AutoClockOutService
{
    public static function run(?Carbon $date = null): int
    {
        $date = $date ?? Carbon::today('Asia/Kolkata');
        $clockOutTime = Carbon::createFromTime(19, 0, 0, 'Asia/Kolkata');

        $logs = AttendanceLog::whereDate('date', $date)
            ->whereNotNull('clock_in_time')
            ->whereNull('clock_out_time')
            ->where('auto_clock_out', 0)
            ->get();

        foreach ($logs as $log) {

            $clockIn = Carbon::createFromFormat('H:i:s', $log->clock_in_time, 'Asia/Kolkata');

            // If clock-in after 7 PM, skip
            if ($clockIn->gte($clockOutTime)) {
                continue;
            }

            $workedSeconds = $clockIn->diffInSeconds($clockOutTime);

            $log->update([
                'clock_out_time' => '19:00:00',
                'total_work_seconds' => $workedSeconds,
                'auto_clock_out' => 1,
            ]);
        }

        return $logs->count();
    }
}
