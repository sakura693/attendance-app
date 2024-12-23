<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //仮
    public function attendanceRegister(){
        return view('attendance-register');
    }

    public function getAdminAttendanceList(){
        return view('admin-attendance-list');
    }

    public function getAttendanceList(){
        return view('attendance-list');
    }

    public function getStaffList(){
        return view('staff-list');
    }

    public function getStaffAttendanceList(){
        return view('staff-attendance-list');
    }

    public function getAttendanceDetail(){
        return view('attendance-detail');
    }

    


    
}
