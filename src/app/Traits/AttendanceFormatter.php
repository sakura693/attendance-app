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

            //全体時間
            $totalDuration = $clockOut->diffInMinutes($clockIn);
            //休憩時間
            $breakDuration = $attendance->breakRecords->sum(function($breakRecord){
                return carbon::parse($breakRecord->break_end)->diffInMinutes(Carbon::parse($breakRecord->break_start));
            });

            //実働時間
            $workingDuration = $totalDuration - $breakDuration;

            // 日付と時間のフォーマット
            $attendance->formatted_date = Carbon::parse($attendance->date)->format('m/d') . ' (' . Carbon::parse($attendance->date)->translatedFormat('D') . ')';
            $attendance->formatted_clock_in_time = Carbon::parse($attendance->clock_in_time)->format('H:i');
            $attendance->formatted_clock_out_time = Carbon::parse($attendance->clock_out_time)->format('H:i');

            //実働時間を"h:m"で設定
            $attendance->break_hours = gmdate('H:i', $breakDuration*60);
            $attendance->total_hours = gmdate('H:i', $workingDuration*60);

            return $attendance;
        });
    }
}