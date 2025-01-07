<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AttendanceRequest extends Model
{
    use HasFactory;

    public const UPDATED_AT = null; //updated_atを無効可

    protected $fillable = [
        'attendance_id',
        'status_id',
        'new_date',
        'new_clock_in_time',
        'new_clock_out_time',
        'new_break_start',
        'new_break_end',
        'reason',
        'pending_reason',
        'approved_at'
    ];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function attendanceRequestBreaks(){
        return $this->hasMany(AttendanceRequestBreak::class);
    }
}
