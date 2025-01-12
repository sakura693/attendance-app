<?php 

namespace App\Traits;

use Carbon\Carbon;

trait AttendanceTrait
{
    //月の変更
    public function monthChange(string $currentMonth, ?string $action): string
    {
        if($action === 'next'){
            $currentMonth = Carbon::createFromFormat('Y/m', $currentMonth)->addMonth()->format('Y/m');
        }elseif($action === 'prev'){
            $currentMonth = Carbon::createFromFormat('Y/m', $currentMonth)->subMonth()->format('Y/m');
        }
        return $currentMonth;
    }

    //月の開始日と終了日を計算
    public function getMonthRange(string $currentMonth):array{
        $startOfMonth = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

        return [$startOfMonth, $endOfMonth];
    }

}