<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Traits\AttendanceFormatter; //traitファイル
use App\Traits\AttendanceTrait; 
use Illuminate\Support\Facades\Response; 

class AdminController extends Controller
{
    use AttendanceFormatter, AttendanceTrait;

    //特定の日付の退勤情報を取得
    public function getAdminAttendanceList(Request $request){
        $currentDay = session('currentDay', Carbon::now()->format('Y/m/d'));
        $currentDay = $this->dayChange($currentDay, $request->input('action'));
        session(['currentDay' => $currentDay]);

        $attendances = Attendance::with('user')->where('date', $currentDay)->get();
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

        $currentMonth = session('currentMonth', Carbon::now()->format('Y/m'));
        $currentMonth = $this->monthChange($currentMonth, $request->input('action'));
        session(['currentMonth' => $currentMonth]);

        [$startOfMonth, $endOfMonth] = $this->getMonthRange($currentMonth);

        $attendances = Attendance::where('user_id', $user->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->orderBy('date', 'asc')->get();
        $attendances = $this->formatAttendanceData($attendances);
        
        return view('staff-attendance-list', compact('user', 'currentMonth', 'attendances'));
    }

    //csv出力機能
    public function exportCsv($user_id){
        $attendances = Attendance::where('user_id', $user_id)->orderBy('date', 'asc')->get();
        $attendances = $this->formatAttendanceData($attendances);

        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩', '合計']; 
        
        foreach ($attendances as $attendance){
            $csvData[] = [
                $attendance->formatted_date,
                $attendance->formatted_clock_in_time,
                $attendance->formatted_clock_out_time,
                $attendance->break_hours,
                $attendance->total_hours,
            ];
        }

        $fileName = 'attendances_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ];
        $callback = function () use ($csvData){
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            foreach ($csvData as $row){
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
