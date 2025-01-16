<?php 

namespace App\Traits;

use Carbon\Carbon;

trait DetailFormatter
{
    //時刻をフォーマット化
    public function formatTime($time): ?string
    {
        return $time ? Carbon::parse($time)->format('H:i') : null;
    }

    //出勤時間・退勤時間
    public function formattedClockInOut($clockIn, $clockOut): array
    {
        return[
            'clock_in' => $this->formatTime($clockIn),
            'clock_out' => $this->formatTime($clockOut),
        ];
    }

    //休憩時間
    public function FormattedBreakTime($attendanceRequestBreaks, $breakRecords): array{
        //attendanceRequestBreaksに値がある時
        if($attendanceRequestBreaks && $attendanceRequestBreaks->isNotEmpty()){
            return $attendanceRequestBreaks->map(function ($break){
                return [
                    'break_start' => $this->formatTime($break->new_break_start),
                    'break_end' =>$this->formatTime($break->new_break_end),
                ];
            })->toArray();
        }     
        //値がない時
        return $breakRecords->map(function ($break) {
            return [
                'break_start' => $this->formatTime($break->break_start),
                'break_end' => $this->formatTime($break->break_end),
            ];
        })->toArray();      
    }
}



