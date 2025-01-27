<?php 

namespace App\Traits;

use Carbon\Carbon;

trait DetailFormatter
{
    public function formatTime($time): ?string
    {
        return $time ? Carbon::parse($time)->format('H:i') : null;
    }

    public function formattedClockInOut($clockIn, $clockOut): array
    {
        return[
            'clock_in' => $this->formatTime($clockIn),
            'clock_out' => $this->formatTime($clockOut),
        ];
    }

    public function FormattedBreakTime($attendanceRequestBreaks, $breakRecords): array{
        if($attendanceRequestBreaks && $attendanceRequestBreaks->isNotEmpty()){
            return $attendanceRequestBreaks->map(function ($break){
                return [
                    'break_start' => $this->formatTime($break->new_break_start),
                    'break_end' =>$this->formatTime($break->new_break_end),
                ];
            })->toArray();
        }     
        return $breakRecords->map(function ($break) {
            return [
                'break_start' => $this->formatTime($break->break_start),
                'break_end' => $this->formatTime($break->break_end),
            ];
        })->toArray();      
    }
}



