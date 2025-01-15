<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //追加
use Carbon\Carbon; //追加
use App\Models\Attendance;
//traitファイルをインポート
use App\Traits\AttendanceFormatter; 
use App\Traits\AttendanceTrait;
use App\Traits\DetailFormatter; 


class AttendanceController extends Controller
{
    use AttendanceFormatter, AttendanceTrait, DetailFormatter; //traitファイルを使う

    //一般ユーザーの勤怠一覧画面を取得
    public function getAttendanceList(Request $request){
        //現在の月をセッションから取得（今月をデフォに設定）
        $currentMonth = session('currentMonth', Carbon::now()->format('Y/m'));
        //翌月と前月の処理（private functionを埋め込む）
        $currentMonth = $this->monthChange($currentMonth, $request->input('action'));
        //セッションに現在の月を保存
        session(['currentMonth' => $currentMonth]);
        
        //月の範囲を取得
        [$startOfMonth, $endOfMonth] = $this->getMonthRange($currentMonth);

        //ログインユーザーの特定の月の勤怠データを取得し日付順（asc:古い順で並べる）に並べる
        /*・attendances()はUserモデルのリレーションメソッド*/
        $attendances = Auth::user()->attendances()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();

        //実際のデータの計算（traitファイル）
        $attendances = $this->formatAttendanceData($attendances);

        return view('attendance-list', compact('currentMonth', 'attendances'));
    }


    //勤怠詳細を取得
    public function getAttendanceDetail($attendance_id){
        $attendance = Attendance::with(['user', 'breakRecords', 'attendanceRequest'])->findOrFail($attendance_id);

        //日付を年と月日に分ける
        $year = Carbon::parse($attendance->date)->format('Y年');
        $monthDay = Carbon::parse($attendance->date)->format('m月d日');

        $clockInOut = $this->FormattedClockInOut($attendance->attendanceRequest->new_clock_in_time ?? $attendance->clock_in_time,$attendance->attendanceRequest->new_clock_out_time ?? $attendance->clock_out_time);

        $breakTime = $this->FormattedBreakTime($attendance->breakRecords);

        for ($i = count($breakTime); $i < 2; $i++) {
            $breakTime[] = [
                'break_start' => '',
                'break_end' => '',
            ];
        }

        return view('attendance-detail', compact('attendance', 'year', 'monthDay', 'clockInOut', 'breakTime'));
    }





    

    
    

    


    
}
