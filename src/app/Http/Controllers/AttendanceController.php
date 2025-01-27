<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Models\Attendance;
use App\Traits\AttendanceFormatter; 
use App\Traits\AttendanceTrait;
use App\Traits\DetailFormatter; 
use App\Models\AttendanceRequestBreak;


class AttendanceController extends Controller
{
    use AttendanceFormatter, AttendanceTrait, DetailFormatter; //traitファイル

    //一般ユーザーの勤怠一覧画面を取得
    public function getAttendanceList(Request $request){
        $currentMonth = session('currentMonth', Carbon::now()->format('Y/m'));
        $currentMonth = $this->monthChange($currentMonth, $request->input('action'));
        session(['currentMonth' => $currentMonth]);
        
        [$startOfMonth, $endOfMonth] = $this->getMonthRange($currentMonth);

        $attendances = Auth::user()->attendances()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();
        $attendances = $this->formatAttendanceData($attendances);

        return view('attendance-list', compact('currentMonth', 'attendances'));
    }


    //勤怠詳細を取得
    public function getAttendanceDetail($attendance_id){
        $attendance = Attendance::with(['user', 'breakRecords', 'attendanceRequest'])->findOrFail($attendance_id);
        
        $attendanceRequestBreaks = collect();

        if($attendance->attendanceRequest){
            $attendanceRequestBreaks = AttendanceRequestBreak::where('attendance_request_id', $attendance->attendanceRequest->id)->get();
        }
        $attendance->attendanceRequestBreaks = $attendanceRequestBreaks;

        $year = Carbon::parse($attendance->date)->format('Y年');
        $monthDay = Carbon::parse($attendance->date)->format('m月d日');

        $clockInOut = $this->FormattedClockInOut($attendance->attendanceRequest->new_clock_in_time ?? $attendance->clock_in_time,$attendance->attendanceRequest->new_clock_out_time ?? $attendance->clock_out_time);

        $breakTime = $this->FormattedBreakTime($attendance->attendanceRequestBreaks, $attendance->breakRecords);

        for ($i = count($breakTime); $i < 2; $i++) {
            $breakTime[] = [
                'break_start' => '',
                'break_end' => '',
            ];
        }

        return view('attendance-detail', compact('attendance', 'year', 'monthDay', 'clockInOut', 'breakTime'));
    }





    

    
    

    


    
}
