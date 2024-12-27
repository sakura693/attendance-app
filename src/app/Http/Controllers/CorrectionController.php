<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest; //追加

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
}
