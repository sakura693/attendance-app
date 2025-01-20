<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; //追加
use Carbon\Carbon;
use App\Models\Attendance; //追加
use App\Traits\AttendanceFormatter; //traitファイルをインポート
use App\Traits\AttendanceTrait; //もう一つのtraitファイルをインポート
use Illuminate\Support\Facades\Response; //csv出力

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

    //csv出力機能
    public function exportCsv($user_id){
        /*1.データを取得*/
        $attendances = Attendance::where('user_id', $user_id)->orderBy('date', 'asc')->get();
        $attendances = $this->formatAttendanceData($attendances);

        /*2.csvデータを作成*/
        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩', '合計']; //csvデータのヘッダー部分
        
        //各値を取り出して配列形式にする
        foreach ($attendances as $attendance){
            $csvData[] = [
                $attendance->formatted_date,
                $attendance->formatted_clock_in_time,
                $attendance->formatted_clock_out_time,
                $attendance->break_hours,
                $attendance->total_hours,
            ];
        }

        /*3.CSVを生成*/
        $fileName = 'attendances_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ];

        //コールバック関数：CSVデータを生成し、ストリーム（データをリアルタイムで生成・出力）を処理する
        $callback = function () use ($csvData){
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            foreach ($csvData as $row){
                fputcsv($file, $row);
            }
            fclose($file);
        };

        /*4.レスポンスを返す*/
        return Response::stream($callback, 200, $headers);
    }
}
