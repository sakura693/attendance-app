<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 
use App\Models\Attendance;
use App\Models\AttendanceRequest; 
use App\Models\AttendanceRequestBreak; 
use App\Models\BreakRecord; 
use App\Traits\AttendanceFormatter; 
use App\Traits\AttendanceTrait;
use App\Traits\DetailFormatter; 

class ApprovalController extends Controller
{
    use AttendanceFormatter, AttendanceTrait, DetailFormatter; //traitファイル

    //承認画面を取得
    public function getApprovalPage($attendance_correct_request){
        $attendance = Attendance::with(['user', 'breakRecords', 'attendanceRequest'])->findOrFail($attendance_correct_request);

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

        return view('correction-approval', compact('attendance', 'year', 'monthDay', 'clockInOut', 'breakTime'));
    }


    //承認ボタンを押したときの挙動
    public function update($attendance_id){
        $attendance = Attendance::findOrFail($attendance_id);
        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance_id)->firstOrFail();

        if (!empty($attendanceRequest->new_date)){
            $attendance->date = $attendanceRequest->new_date;
        }
        if (!empty($attendanceRequest->new_clock_in_time)){
            $attendance->clock_in_time = $attendanceRequest->new_clock_in_time;
        }
        if (!empty($attendanceRequest->new_clock_out_time)){
            $attendance->clock_out_time = $attendanceRequest->new_clock_out_time;
        }
        $attendance -> save();

        foreach($attendanceRequest->attendanceRequestBreaks as $requestBreak){
            $breakRecord = BreakRecord::firstOrNew([
                'attendance_id' => $attendance_id,
                'break_start' => $requestBreak->new_break_start, 
            ]);
            if (!empty($requestBreak->new_break_start)){
                $breakRecord->break_start = $requestBreak->new_break_start;
            }
            if (!empty($requestBreak->new_break_end)){
                $breakRecord->break_end = $requestBreak->new_break_end;
            }
            $breakRecord->save();
        }

        if (!empty($attendanceRequest->pending_reason)){
            $attendance->update([
                'reason' => $attendanceRequest->pending_reason
            ]); 
        }

        $attendanceRequest->update([
            'status_id' => 2,
        ]);

        return redirect()->back();
    }

}
