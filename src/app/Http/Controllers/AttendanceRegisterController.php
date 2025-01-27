<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Models\Attendance;
use App\Models\BreakRecord;

class AttendanceRegisterController extends Controller
{
    public function attendanceRegister(){
        $currentDate = Carbon::now();
        $formattedData = $currentDate->isoFormat('YYYY年M月D日(ddd)');
        $formattedTime = $currentDate->isoFormat('HH:mm');

        $userId = auth()->id();
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();
        $latestBreakRecord = $attendance ? BreakRecord::where('attendance_id', $attendance->id)->latest('break_start')->first()
        :null;
        $status = is_null($attendance) ? '勤務外' : $this->getWorkingStatus($attendance, $latestBreakRecord);

        return view('attendance-register', compact('formattedData', 'formattedTime', 'status'));
    }

    //出勤中、休憩中などの勤務状態を取得
    public function getWorkingStatus($attendance, $latestBreakRecord){
        if (!is_null($attendance->clock_out_time)){
            return '退勤済み';
        }
        if ($latestBreakRecord && is_null($latestBreakRecord->break_end)){
            return '休憩中';
        }
        return '出勤中';
    }

    //出勤ボタンの処理
    public function clockIn(){
        $userId = auth()->id();
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();
        if ($attendance){
            return back();
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => Carbon::today(),
            'clock_in_time' => Carbon::now(),
        ]);
        return redirect('/attendance');
    }

    //休憩開始ボタンの処理
    public function breakStart(){
        $userId = auth()->id();
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();

        if (!$attendance){
            return back();
        }

        BreakRecord::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now()
        ]);
        return redirect('/attendance');
    }

    //休憩終了ボタンの処理
    public function breakEnd(){
        $userId = auth()->id();
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();

        if(!$attendance){
            return back();
        }

        $latestBreakRecord = BreakRecord::where('attendance_id', $attendance->id)->whereNull('break_end')->latest('break_start')->first();
        
        if (!$latestBreakRecord){
            return back();
        }

        $latestBreakRecord->update([
            'break_end' => Carbon::now()
        ]);
        return redirect('/attendance');
    }

    //退勤ボタンの処理
    public function clockOut(){
        $userId = auth()->id();

        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();

        if(!$attendance){
            return back();
        }
        if (!is_null($attendance->clock_out_time)){
            return back();
        }

        $attendance->update([
            'clock_out_time' => Carbon::now()
        ]);
        return redirect('/attendance');
    }
}
