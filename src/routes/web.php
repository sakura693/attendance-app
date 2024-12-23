<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\Auth\RegisterController; //追加
use App\Http\Controllers\Auth\LoginController; //追加
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController; //追加


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

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/attendance', [AttendanceController::class, 'attendanceRegister']);

//管理者ログイン画面取得
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->middleware(['guest'])->name('admin.login');

//管理者ログイン情報保存
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
->middleware(['guest'])->name('admin.login');

Route::get('/admin/attendance/list', [AttendanceController::class, 'getAdminAttendanceList']);



//仮
Route::get('/attendance/list', [AttendanceController::class, 'getAttendanceList']);
Route::get('/admin/staff/list', [AttendanceController::class, 'getStaffList']);
Route::get('/admin/attendance/staff/{id}', [AttendanceController::class, 'getStaffAttendanceList']);
Route::get('/attendance/{id}', [AttendanceController::class, 'getAttendanceDetail']);
Route::get('/stamp_correction_request/list', [CorrectionController::class, 'correctionRequest']);


