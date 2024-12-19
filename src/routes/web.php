<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//仮
Route::get('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register']);
Route::get('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/admin/attendance/list', [AttendanceController::class, 'getAdminAttendanceList']);
Route::get('/attendance/list', [AttendanceController::class, 'getAttendanceList']);
Route::get('/admin/staff/list', [AttendanceController::class, 'getStaffList']);
Route::get('/admin/attendance/staff/{id}', [AttendanceController::class, 'getStaffAttendanceList']);
Route::get('/attendance/{id}', [AttendanceController::class, 'getAttendanceDetail']);
Route::get('/stamp_correction_request/list', [CorrectionController::class, 'correctionRequest']);
Route::get('/attendance', [AttendanceController::class, 'attendanceRegister']);

