<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequestBreak extends Model
{
    use HasFactory;
    public const UPDATED_AT = null; //updated_atを無効可
    
    protected $fillable = [
        'attendance_request_id',
        'new_break_start',
        'new_break_end'
    ];

    // AttendanceRequestモデルを定義（AttendanceRequestとAttendanceRequestBreakは１対N）
    public function attendanceRequest(){
        return $this->belongsTo(AttendanceRequest::class);
    }
}
