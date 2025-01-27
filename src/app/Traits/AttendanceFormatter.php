<?php

namespace App\Traits;

use Carbon\Carbon;

trait AttendanceFormatter
{
    public function formatAttendanceData($attendances)
    {
        return $attendances = $attendances->map(function ($attendance){
            $clockIn = Carbon::parse($attendance->clock_in_time);
            $clockOut = Carbon::parse($attendance->clock_out_time);
            $totalDuration = $clockOut->diffInMinutes($clockIn);
            $breakDuration = $attendance->breakRecords->sum(function($breakRecord){
                return carbon::parse($breakRecord->break_end)->diffInMinutes(Carbon::parse($breakRecord->break_start));
            });
            $workingDuration = $totalDuration - $breakDuration;

            $attendance->formatted_date = Carbon::parse($attendance->date)->format('m/d') . ' (' . Carbon::parse($attendance->date)->translatedFormat('D') . ')';
            $attendance->formatted_clock_in_time = Carbon::parse($attendance->clock_in_time)->format('H:i');
            $attendance->formatted_clock_out_time = Carbon::parse($attendance->clock_out_time)->format('H:i');

            $attendance->break_hours = gmdate('H:i', $breakDuration*60);
            $attendance->total_hours = gmdate('H:i', $workingDuration*60);

            return $attendance;
        });
    }
}