<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'status_id',
        'reason',
        'approved_at'
    ];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
}
