<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; //追加
use App\Models\Attendance;
use App\Models\BreakRecord;

class AttendanceRegisterController extends Controller
{
    public function attendanceRegister(){
        //現在の日付や時刻を取得し表示形式を変更
        $currentDate = Carbon::now();
        $formattedData = $currentDate->isoFormat('YYYY年M月D日(ddd)');
        $formattedTime = $currentDate->isoFormat('HH:mm');

        //ログインユーザー情報を取得
        $userId = auth()->id();

        //当日の出勤記録を取得（存在しない場合はnull）
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();

        //最新の休憩記録を取得
        $latestBreakRecord = $attendance ? BreakRecord::where('attendance_id', $attendance->id)->latest('break_start')->first()
        :null;

        //勤務外:当日の勤務記録が存在しない
        $status = is_null($attendance) ? '勤務外' : $this->getWorkingStatus($attendance, $latestBreakRecord);

        return view('attendance-register', compact('formattedData', 'formattedTime', 'status'));
    }

    //出勤中、休憩中などの勤務状態を取得
    public function getWorkingStatus($attendance, $latestBreakRecord){
        //退勤済み:当日の勤務記録があり、退勤時間が保存されてる
        if (!is_null($attendance->clock_out_time)){
            return '退勤済み';
        }

         //休憩中:最新の休憩記録が存在し、終了時刻が保存されてない
        if ($latestBreakRecord && is_null($latestBreakRecord->break_end)){
            return '休憩中';
        }

        //それ以外は「出勤中」
        return '出勤中';
    }

    //出勤ボタンの処理
    public function clockIn(){
        $userId = auth()->id();

        //今日の出勤記録があるか確認
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();
        //既に出勤記録があったらもう出勤登録できない（出勤は一日に一度だけだから）
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

        //今日の出勤記録を取得
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

        //今日の出勤記録を取得
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

        //今日の出勤情報を取得
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', Carbon::today())->first();

        //退勤データがなかったら戻る
        if(!$attendance){
            return back();
        }
        //既に退勤済みだったら(clock_out_timeカラムが空じゃない時)戻る
        if (!is_null($attendance->clock_out_time)){
            return back();
        }

        $attendance->update([
            'clock_out_time' => Carbon::now()
        ]);
        return redirect('/attendance');
    }
}
