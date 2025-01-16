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
    //申請一覧画面を取得
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
        $attendance = Attendance::with('breakRecords')->findOrFail($request->attendance_id); 

        //管理者判定
        $isAdmin = auth()->user()->role === 'admin';
        
        //date部分
        $dateString = $request->year . $request->monthDay;
        $date = Carbon::createFromFormat('Y年m月d日', $dateString)->format('Y-m-d');
        

        if ($isAdmin){
            //管理者の場合直接attendanceを更新
            $this->updateAttendance($attendance, $request, $date);
            }else { 
            //一般ユーザーの場合attendanceRequestを作成
            $attendanceRequest = AttendanceRequest::create([
                'attendance_id' => $attendance->id,
                'status_id' => 1, //承認待ち
            ]);

            $this->makeAttendanceRequest($attendanceRequest, $attendance, $request, $date);
        }
        return redirect('/attendance/list');
    }

    //管理者によるattendanceの直接更新処理
    private function updateAttendance($attendance, $request, $date){
        // 日付の更新
        if ($date !== $attendance->date) {
            $attendance->update([
                'date' => $date,
            ]);
        }

         //出勤・退勤時間の更新
        foreach(['clock_in_time', 'clock_out_time'] as $field){
            $requestValue = $request->$field;
            //テーブルの値を取得しリクエストと同じフォーマットに変換⇩
            $attendanceValue = substr($attendance->$field ?? '',  0, 5);
            
            //値が異なる場合のみ変更を検出
            if ($requestValue && $requestValue !== $attendanceValue){
                $attendance->update([
                    $field => $requestValue,
                ]);
            }
        }

        //備考欄（reason）の更新
        if (!empty($request->reason)){
            $attendance->update([
                'reason' => $request->reason
            ]);
        }

        foreach ($request->break_start as $index => $start){
            //$startと$endはそれぞれリクエストのbreak_startとbreak_endを指す
            $end = $request->break_end[$index] ?? null;

            //データベースの値をとリクエストを比較
            $existingBreakRecord = $attendance->breakRecords[$index] ?? null;

            //既にbreakレコードがある時
            if($existingBreakRecord){
                $existingStart = substr($existingBreakRecord->break_start, 0,5);
                $existingEnd = substr($existingBreakRecord->break_end, 0,5);

                if ($start !== $existingStart || $end !== $existingEnd){
                    $existingBreakRecord->update([
                        'break_start' => $start,
                        'break_end' => $end
                    ]);
                }
            }else{
                //breakレコードがない時
                $attendance->breakRecords()->create([
                    'break_start' => $start,
                    'break_end' => $end
                ]);
            }
        }
    }
        

    //一般ユーザーの承認申請処理
    private function makeAttendanceRequest($attendanceRequest, $attendance, $request, $date){
        //日付の変更
        if ($date !== $attendance->date) {
            $attendanceRequest->update([
                'new_date' => $date,
            ]);
        }

        //出勤・退勤時間の変更
        foreach(['clock_in_time', 'clock_out_time'] as $field){
            $requestValue = $request->$field;
            //テーブルの値を取得しリクエストと同じフォーマットに変換⇩
            $attendanceValue = substr($attendance->$field ?? '',  0, 5);
            
            //値が異なる場合のみ変更を検出
            if ($requestValue && $requestValue !== $attendanceValue){
                $attendanceRequest->update([
                    "new_{$field}" => $requestValue,
                ]);
            }
        }

        //reason部分
        if (!empty($request->reason)){
            $attendanceRequest->update([
                'pending_reason' => $request->reason
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
    }      
}