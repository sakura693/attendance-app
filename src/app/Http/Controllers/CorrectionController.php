<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest; //追加
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\AttendanceRequestBreak;

class CorrectionController extends Controller
{
    public function correctionRequest(){
        $corrections = AttendanceRequest::with([
            'attendance',
            'attendance.user', 
            'status'
        ])->get();
        return view('correction-request', compact('corrections'));
    }

    //修正ボタンを押した後の動作
    public function request(Request $request){
        //テーブルにY-m-dの形で保存するため、yearとmonthDayを結合する
        $dateString = $request->year . $request->monthDay;
        $date = Carbon::createFromFormat('Y年m月d日', $dateString)->format('Y-m-d');

        $attendance = Attendance::findOrFail($request->attendance_id);

        //日付の変更を検出
        $changes = [];
        if ($date !== $attendance->date){
            $changes['new_date'] = $date;
        }

        //出勤・退勤時間の変更を検出
        foreach(['clock_in_time', 'clock_out_time'] as $field){
            $requestValue = $request->$field;
            $attendanceValue = $attendance->$field;
            //データベースの値をリクエストデータと同じフォーマットに変換
            if (!empty($attendanceValue)){
                $attendanceValue = substr($attendanceValue, 0, 5); //'H:i'に変換
            }

            //値が異なる場合のみ変更を検出
            if (!empty($request) && $requestValue !== $attendanceValue){
                $changes["new_{$field}"] = $requestValue;
            }
        }

        //変更がある場合のみ保存
        if(!empty($changes)){
            $attendanceRequest = AttendanceRequest::create(array_merge([
                'attendance_id' => $attendance->id,
                'status_id' => 1, //承認待ち
                'pending_reason' => $request->reason, //reasonカラムがnullを許容しないからnullの場合空値を表示すると（nullではなく空の値を表示すると）明示的に示す
            ], $changes));
        }

        // attendance_request_breaksテーブルに保存
        foreach ($request->break_start as $index => $start){
            $end = $request->break_end[$index] ?? null;

            if ($start || $end){
                AttendanceRequestBreak::create([
                    'attendance_request_id' => $attendanceRequest->id,
                    'new_break_start' => $start,
                    'new_break_end' => $end,
                ]);
            }
        }

        return redirect('/attendance/list');
    }
    //フォームリクエストも作る
}