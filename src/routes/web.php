<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\Auth\RegisterController; //追加
use App\Http\Controllers\Auth\LoginController; //追加
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController; //追加
use App\Http\Controllers\AttendanceRegisterController; //追加
use App\Http\Controllers\AdminController; //追加



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

Route::get('/attendance', [AttendanceRegisterController::class, 'attendanceRegister']);

//管理者ログイン画面取得
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->middleware(['guest'])->name('admin.login');

//管理者ログイン情報保存
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
->middleware(['guest'])->name('admin.login.post');

//勤怠一覧画面を取得
Route::get('/attendance/list', [AttendanceController::class, 'getAttendanceList']);

//勤怠詳細画面を取得
Route::get('/attendance/{attendance_id}', [AttendanceController::class, 'getAttendanceDetail']);

//申請一覧画面を取得
Route::get('/stamp_correction_request/list', [CorrectionController::class, 'correctionRequest']);

/*勤怠登録のルート⇩*/
//出勤開始
Route::post('/attendance/start', [AttendanceRegisterController::class, 'clockIn']);
//退勤
Route::post('/attendance/end', [AttendanceRegisterController::class, 'clockOut']);
//休憩開始
Route::post('/attendance/break/start', [AttendanceRegisterController::class, 'breakStart']);
//休憩終了
Route::post('/attendance/break/end', [AttendanceRegisterController::class, 'breakEnd']);

//修正フォーム送信先
Route::post('/attendance/list', [CorrectionController::class, 'request']);


//当日の勤怠情報を取得
Route::get('/admin/attendance/list', [AdminController::class, 'getAdminAttendanceList']);

//スタッフ一覧画面を取得
Route::get('/admin/staff/list', [AdminController::class, 'getStaffList']);
//特定のスタッフの詳細画面を取得
Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'getStaffAttendanceList']);




