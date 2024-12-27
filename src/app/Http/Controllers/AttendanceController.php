<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //追加
use Carbon\Carbon; //追加
use App\Models\Attendance;



class AttendanceController extends Controller
{
    //一般ユーザーの勤怠一覧画面を取得
    public function getAttendanceList(Request $request){
        //現在の月をセッションから取得（今月をデフォに設定）
        $currentMonth = session('currentMonth', Carbon::now()->format('Y/m'));

        //翌月と前月の処理（private functionを埋め込む）
        $currentMonth = $this->monthChange($currentMonth, $request->input('action'));
        //セッションに現在の月を保存
        session(['currentMonth' => $currentMonth]);
        
        //月の開始日と終了日を計算
        $startOfMonth = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

        //ログインユーザーの勤怠データを特定の月の日付範囲で取得して日付順に並べる
        /*・attendances()はUserモデルのリレーションメソッド
          ・ascは古い順で並べる（古いものが上に来る）*/
        $attendances = Auth::user()->attendances()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();

        //実際のデータの計算（private function）
        $attendances = $this->formatAttendanceData($attendances);

        return view('attendance-list', compact('currentMonth', 'attendances'));
    }

    //翌月と前月の切り替え処理
    private function monthChange(string $currentMonth, ?string $action): string
    {
        if($action === 'next'){
            $currentMonth = Carbon::createFromFormat('Y/m', $currentMonth)->addMonth()->format('Y/m');
        }elseif($action === 'prev'){
            $currentMonth = Carbon::createFromFormat('Y/m', $currentMonth)->subMonth()->format('Y/m');
        }
        return $currentMonth;
    }
    
    //実際のデータの取得と整形
    private function formatAttendanceData($attendances)
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


    public function getAttendanceDetail($attendance_id){
        $attendance = Attendance::with(['user', 'breakRecords', 'attendanceRequest'])->findOrFail($attendance_id);

        //日付を年と月日に分ける
        $year = Carbon::parse($attendance->date)->format('Y年');
        $monthDay = Carbon::parse($attendance->date)->format('m月d日');

        return view('attendance-detail', compact('attendance', 'year', 'monthDay'));
    }

    
    //仮
    public function attendanceRegister(){
        return view('attendance-register');
    }

    public function getAdminAttendanceList(){
        return view('admin-attendance-list');
    }

    public function getStaffList(){
        return view('staff-list');
    }

    public function getStaffAttendanceList(){
        return view('staff-attendance-list');
    }

    

    


    
}
