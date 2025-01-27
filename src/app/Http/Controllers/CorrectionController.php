<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest; 
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
        $tab = $request->query('tab', 'pending');
        
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
        $isAdmin = auth()->user()->role === 'admin';
        $dateString = $request->year . $request->monthDay;
        $date = Carbon::createFromFormat('Y年m月d日', $dateString)->format('Y-m-d');
        
        if ($isAdmin){
            $this->updateAttendance($attendance, $request, $date);
            }else { 
            $attendanceRequest = AttendanceRequest::create([
                'attendance_id' => $attendance->id,
                'status_id' => 1, 
            ]);
            $this->makeAttendanceRequest($attendanceRequest, $attendance, $request, $date);
        }
        return redirect('/attendance/list');
    }

    //管理者によるattendanceの直接更新処理
    private function updateAttendance($attendance, $request, $date){
        if ($date !== $attendance->date) {
            $attendance->update([
                'date' => $date,
            ]);
        }

        foreach(['clock_in_time', 'clock_out_time'] as $field){
            $requestValue = $request->$field;
            $attendanceValue = substr($attendance->$field ?? '',  0, 5);
            if ($requestValue && $requestValue !== $attendanceValue){
                $attendance->update([
                    $field => $requestValue,
                ]);
            }
        }

        if (!empty($request->reason)){
            $attendance->update([
                'reason' => $request->reason
            ]);
        }

        foreach ($request->break_start as $index => $start){
            $end = $request->break_end[$index] ?? null;
            $existingBreakRecord = $attendance->breakRecords[$index] ?? null;
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
                $attendance->breakRecords()->create([
                    'break_start' => $start,
                    'break_end' => $end
                ]);
            }
        }
    }
        

    //一般ユーザーの承認申請処理
    private function makeAttendanceRequest($attendanceRequest, $attendance, $request, $date){
        if ($date !== $attendance->date) {
            $attendanceRequest->update([
                'new_date' => $date,
            ]);
        }

        foreach(['clock_in_time', 'clock_out_time'] as $field){
            $requestValue = $request->$field;
            $attendanceValue = substr($attendance->$field ?? '',  0, 5);
            if ($requestValue && $requestValue !== $attendanceValue){
                $attendanceRequest->update([
                    "new_{$field}" => $requestValue,
                ]);
            }
        }

        if (!empty($request->reason)){
            $attendanceRequest->update([
                'pending_reason' => $request->reason
            ]);
        }

        foreach ($request->break_start as $index => $start){
            $end = $request->break_end[$index] ?? null;
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