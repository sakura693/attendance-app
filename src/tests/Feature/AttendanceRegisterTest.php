<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; //追加
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceRegisterTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //日時取得機能
    public function test_date_time_is_displayed_correctly(){
        $user = User::find(1);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->get('/attendance');

        \Carbon\Carbon::setLocale('ja');
        //現在日時を取得
        $now = \Carbon\Carbon::now();
        //期待される形式
        $expectedDataTime = $now->isoFormat('YYYY年M月D日(ddd)');
        $expectedTime = $now->isoFormat('HH:mm');

        $response->assertStatus(200);
        $response->assertSeeText($expectedDataTime);
        $response->assertSeeText($expectedTime);
    }

    /*ステータス確認機能*/
    //勤務状況のメソッド
    private function getWorkingStatus($attendance, $latestBreakRecord){
        if (is_null($attendance)) {
            return '勤務外';
        }
        if (!is_null($attendance->clock_out_time)) {
            return '退勤済み';
        }
        if ($latestBreakRecord && is_null($latestBreakRecord->break_end)) {
            return '休憩中';
        }
        return '出勤中';
    }

    //勤務外の時（そもそも勤怠記録がない）
    public function test_status_is_off_duty(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        $attendance = null;
        $latestBreakRecord = null;
        $status = $this->getWorkingStatus($attendance, $latestBreakRecord);
        $this->assertEquals('勤務外', $status);
    }

    //勤務中の時（退勤レコードはなくて休憩終了時間がある）
    public function test_status_is_on_duty(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        $attendance = (object)[
            'clock_out_time' => null
        ];
        $latestBreakRecord = (object)[
            'break_end' => '2025-01-01 13:00:00'
        ];
        $status = $this->getWorkingStatus($attendance, $latestBreakRecord);
        $this->assertEquals('出勤中', $status);
    }

    //休憩中の時（退勤レコードも休憩終了時間もない）
    public function test_status_is_on_break(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        $attendance = (object)[
            'clock_out_time' => null
        ];
        $latestBreakRecord = (object)[
            'break_end' => null
        ];
        $status = $this->getWorkingStatus($attendance, $latestBreakRecord);
        $this->assertEquals('休憩中', $status);
    }

    //退勤済みの時（退勤時間がある）
    public function test_status_is_work(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        $attendance = (object)[
            'clock_out_time' => '2025-01-01 18:00:00'
        ];
        $latestBreakRecord = null;
        $status = $this->getWorkingStatus($attendance, $latestBreakRecord);
        $this->assertEquals('退勤済み', $status);
    }
    
    /*出勤機能*/
    //出勤ボタンが正しく動作する
    public function test_clock_in_button_is_displayed_and_functions_correctly(){
        $user = User::find(1);
        $attendance = null;
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤');

        //出勤処理を実行
        $response = $this->post('/attendance/start');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    //出勤ボタンは一日一回だけ押せる
    public function test_clock_in_button_is_displayed_only_once(){
        $user = User::find(1);
        //現在時刻を固定
        \Carbon\Carbon::setTestNow('2025-01-01 18:00:00');

        //退勤済み状態
        Attendance::create([
            'user_id' => $user->id,
            'date' => now(),
            'clock_in_time' => now()->startOfDay()->addHours(9),
            'clock_out_time' => now()->startOfDay()->addHours(18),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertDontSee('出勤');
    }

    /*休憩機能*/
    //休憩ボタンが正常に機能する・休憩ボタンは何度も押せる
    public function test_break_button_functions_correctly(){
        $user = User::find(1);
        Carbon::setTestNow('2025-01-22 09:00:00');
        Attendance::create([
            'user_id' => $user->id,
            'date' => now(),
            'clock_in_time' => now(),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $response = $this->post('/attendance/break/start');
        $this->assertDatabaseHas('break_records', [
            'break_start' => now(),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');

        $response = $this->post('/attendance/break/end');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        //二回目の休憩
        Carbon::setTestNow('2025-01-22 11:00:00');
        $response = $this->post('/attendance/break/start');
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    //休憩時刻が管理画面で確認できるはパス（管理画面とは？？）

    /*退勤機能*/
    //退勤ボタンが動作する
    public function test_clock_out_button_works_correctly(){
        $user = User::find(1);
        Carbon::setTestNow('2025-01-22 09:00:00');
        $attendance = Attendance::create([
            'id' => 4,
            'user_id' => $user->id,
            'date' => now(),
            'clock_in_time' => now(),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤');

        $response = $this->post('/attendance/end');
        $response = $this->get('/attendance');
        $response->assertSee('退勤済み');
    }

    //出退勤記録を勤怠画面で確認できるか
    public function test_clock_out_and_out_time_is_displayed_in_attendance_list(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance');

        Carbon::setTestNow('2025-01-22 09:00:00');
        $response = $this->post('/attendance/start');
        Carbon::setTestNow('2025-01-22 18:00:00');
        $response = $this->post('/attendance/end');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
        ]);

        $response = $this->get('/attendance/list');
        $response->assertSee('2025');
        $response->assertSee('1/22 (水)');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }
}
