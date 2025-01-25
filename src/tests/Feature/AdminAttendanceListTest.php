<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; 
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠情報の取得
    public function test_get_admin_attendance_list(){
        $admin = User::find(2);
        $user = User::find(1);
        Carbon::setTestNow('2024-12-21');
        $date = Carbon::now();
        $response = $this->actingAs($admin)->get('/admin/attendance/list'); 
        //その日の日付が表示される
        $response->assertSee('2024/12/21');

        $attendances = Attendance::where('date', $date)->get();
        $response->assertSee($user->name);
        $response->assertSee('09:30');
        $response->assertSee('17:30');
    }

    //前日ボタンが機能する
    public function test_previous_day_button_functions_correctly(){
        $admin = User::find(2);
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response = $this->get('/admin/attendance/list?action=prev_day');
        $response->assertSee('2024/12/20');
    }

    //翌日ボタンが機能する
    public function test_next_day_button_functions_correctly(){
        $admin = User::find(2);
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response = $this->get('/admin/attendance/list?action=next_day');
        $response->assertSee('2024/12/22');
    }
}
