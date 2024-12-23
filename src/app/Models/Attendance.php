<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function breakRecords(){
        return $this->hasMany(BreakRecord::class);
    }

    public function attendanceRequests(){
        return $this->hasMany(AttendanceRequest::class);
    }
}
