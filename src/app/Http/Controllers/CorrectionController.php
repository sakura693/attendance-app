<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest; //追加
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\AttendanceRequestBreak;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Auth; 

class CorrectionController extends Controller
{
    public function correctionRequest(Request $request){
        $user = Auth::user();
        $tab = $request->query('tab', 'pending');//初期値をpendingにしておき、ページにアクセスした際はpendingの方が表示されるようにする。
        
        $correctionsQuery = AttendanceRequest::with([
            'attendance',
            'attendance.user', 
            'status'
        ]);

        if($user->role !== 'admin'){
            $correctionsQuery->whereHas('attendance', function($query) use ($user){
                $query->where('user_id', $user->id);
            });
        }

        if ($tab === 'pending'){
            $correctionsQuery->where('status_id', 1);
        }elseif ($tab === 'approved'){
            $correctionsQuery->where('status_id', 2);
        }

        $corrections = $correctionsQuery->get();

        $corrections = $corrections->map(function($correction){
            $correction->display_reason = $correction->reason ?: $correction->pending_reason;
            return $correction;
        });
        return view('correction-request', compact('corrections'));
    }

    //修正ボタンを押した後の動作
    public function request(CorrectionRequest $request){
        $attendance = Attendance::findOrFail($request->attendance_id);    

        //フォームが送信される度に⇩のカラムには値が保存される
        $attendanceRequest = AttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'status_id' => 1, //承認待ち
        ]);

        //date部分
        $dateString = $request->year . $request->monthDay;
        $date = Carbon::createFromFormat('Y年m月d日', $dateString)->format('Y-m-d');

        // 日付の変更を検出
        if ($date !== $attendance->date) {
            $attendanceRequest->update([
                'new_date' => $date,
            ]);
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
                $attendanceRequest->update([
                    "new_{$field}" => $requestValue,
                ]);
            }
        }
        
        //reasonの比較
        $currentReason = $attendance->attendanceRequest->reason ?? null; // 存在しない場合は null を設定

        if (!empty($request->reason) && $request->reason !== $currentReason) {
            $attendanceRequest->update([
                'pending_reason' => $request->reason,
            ]);
        }

        //休憩時間部分
        foreach ($request->break_start as $index => $start){
            $end = $request->break_end[$index] ?? null;

            //データベースの値をとリクエストを比較
            $existingBreakRecord = $attendance->breakRecords[$index] ?? null;

            $existingStart = $existingBreakRecord ? substr($existingBreakRecord->break_start, 0,5) : null;
            $existingEnd = $existingBreakRecord ? substr($existingBreakRecord->break_end, 0,5) : null;

            $data = [
                'attendance_request_id' => $attendanceRequest->id,
                'new_break_start' => $start !== $existingStart ? $start : null,
                'new_break_end' => $end !== $existingEnd ? $end : null,
            ];
            if ($data['new_break_start'] || $data['new_break_end']){
                AttendanceRequestBreak::create(array_filter($data));
            }

            }
        return redirect('/attendance/list');
    }
}