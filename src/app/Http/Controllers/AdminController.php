<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; //追加
use Carbon\Carbon;
use App\Models\Attendance; //追加
use App\Traits\AttendanceFormatter; //traitファイルをインポート
use App\Traits\AttendanceTrait; //もう一つのtraitファイルをインポート

class AdminController extends Controller
{
    use AttendanceFormatter, AttendanceTrait;

    //特定の日付の退勤情報を取得
    public function getAdminAttendanceList(Request $request){
        //セッションから今日の日付を取得
        $currentDay = session('currentDay', Carbon::now()->format('Y/m/d'));
        
        //前日と翌日の処理（private functionを埋め込む）
        $currentDay = $this->dayChange($currentDay, $request->input('action'));
        //セッションに今日の日付を保存
        session(['currentDay' => $currentDay]);

        $attendances = Attendance::with('user')->where('date', $currentDay)->get();
        
        //traitを使う
        $attendances = $this->formatAttendanceData($attendances);

        return view('admin-attendance-list', compact('currentDay', 'attendances'));
    }

    //前日と翌日の切り替え
    private function dayChange(string $currentDay, ? string $action){
        $date = Carbon::createFromFormat('Y/m/d', $currentDay);

        if($action === 'next_day'){
            $date = $date->addDay();
        }
        elseif($action === 'prev_day'){
            $date = $date->subDay();
        }
        return $date->format('Y/m/d');
    }


    //スタッフ一覧画面取得
    public function getStaffList(Request $request){
        $users = User::where('role', 'staff')->get();
        return view('staff-list', compact('users'));
    }

    //特定のスタッフの勤怠情報取得
    public function getStaffAttendanceList(Request $request, $user_id){
        $user = User::find($user_id);

        //現在の月をセッションから取得（今月をデフォに設定）
        $currentMonth = session('currentMonth', Carbon::now()->format('Y/m'));
        $currentMonth = $this->monthChange($currentMonth, $request->input('action'));
        session(['currentMonth' => $currentMonth]);

        //月の範囲を取得
        [$startOfMonth, $endOfMonth] = $this->getMonthRange($currentMonth);

        $attendances = Attendance::where('user_id', $user->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->orderBy('date', 'asc')->get();

        //最後に$attendancesのフォーマットを整える
        $attendances = $this->formatAttendanceData($attendances);
        
        return view('staff-attendance-list', compact('user', 'currentMonth', 'attendances'));
    }

}
