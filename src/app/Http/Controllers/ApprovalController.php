<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //追加
use Carbon\Carbon; 
use App\Models\Attendance;
//traitファイルをインポート
use App\Traits\AttendanceFormatter; 
use App\Traits\AttendanceTrait;
use App\Traits\DetailFormatter; 
use App\Models\AttendanceRequest; 
use App\Models\AttendanceRequestBreak; 
use App\Models\BreakRecord; 


class ApprovalController extends Controller
{
    use AttendanceFormatter, AttendanceTrait, DetailFormatter; //traitファイルを使う

    //承認画面を取得
    public function getApprovalPage($attendance_correct_request){
        $attendance = Attendance::with(['user', 'breakRecords', 'attendanceRequest'])->findOrFail($attendance_correct_request);

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

        return view('correction-approval', compact('attendance', 'year', 'monthDay', 'clockInOut', 'breakTime'));
    }


    //承認ボタンを押したときの挙動
    public function update($attendance_id){
        //対応するattendanceを取得
        $attendance = Attendance::findOrFail($attendance_id);

        //関連するattendanceRequestを取得
        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance_id)->firstOrFail();

        //attendanceの更新
        if ($attendanceRequest->new_date !== null){
            $attendance->date = $attendanceRequest->new_date;
        }

        if ($attendanceRequest->new_clock_in_time !== null){
            $attendance->clock_in_time = $attendanceRequest->new_clock_in_time;
        }
        
        if ($attendanceRequest->new_clock_out_time !== null){
            $attendance->clock_out_time = $attendanceRequest->new_clock_out_time;
        }

        $attendance -> save();

        //breakの更新
        $attendanceRequestBreaks = $attendanceRequest->attendanceRequestBreaks;

        foreach($attendanceRequestBreaks as $requestBreak){
            //対応するBreakRecordを取得または新規作成
            $breakRecord = BreakRecord::firstOrNew([
                'attendance_id' => $attendance_id,
                'break_start' => $requestBreak->new_break_start, //Start timeを基準に一意性を確保
            ]);

            if ($requestBreak->new_break_start !== null){
                $breakRecord->break_start = $requestBreak->new_break_start;
            }

            if ($requestBreak->new_break_end !== null){
                $breakRecord->break_end = $requestBreak->new_break_end;
            }

            $breakRecord->save();
        }

        
        if ($attendanceRequest->pending_reason !== null){
            $attendanceRequest->reason = $attendanceRequest->pending_reason;
        }


        //attendanceRequestを保存(status_idも承認済み（2）に変更）
        $attendanceRequest->status_id = 2;
        $attendanceRequest->save(); 

        return redirect()->back();
    }

}
