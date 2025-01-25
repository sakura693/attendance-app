<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; 
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Attendance;

class AttendanceListTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠一覧画面の取得・現在の月が確認できる
    public function test_attendance_list_is_displayed_correctly(){
        $user = User::find(1);
        \Carbon\Carbon::setTestNow('2025-01-01');
        $response = $this->actingAs($user)->get('/attendance/list');       
        $response->assertStatus(200);

        $attendances = Attendance::where('user_id', $user->id)->get();
        foreach ($attendances as $attendance){
            $response->assertSee($attendance->formatted_date);
        }
        $response->assertStatus(200);
        $response->assertSee('2025/01');
    }

    //前月ボタンが機能する
    public function test_previous_month_button_functions_correctly(){
        $user = User::find(1);
        \Carbon\Carbon::setTestNow('2025-01-01');
        $response = $this->actingAs($user)->get('/attendance/list'); 
        $response = $this->get('/attendance/list?action=prev');
        $response->assertSee('2024/12');

        $attendances = Attendance::where('user_id', $user->id)->whereBetween('date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->get();

        foreach ($attendances as $attendance){
            $response->assertSee($attendance->formatted_date);
        }
    }

    //翌月ボタンが機能する
    public function test_next_month_button_functions_correctly(){
        $user = User::find(1);
        \Carbon\Carbon::setTestNow('2024-12-01');
        $response = $this->actingAs($user)->get('/attendance/list'); 
        $response = $this->get('/attendance/list?action=next');
        $response->assertSee('2025/01');

        $attendances = Attendance::where('user_id', $user->id)->whereBetween('date', [
            now()->addMonth()->startOfMonth(),
            now()->addMonth()->endOfMonth(),
        ])->get();

        foreach ($attendances as $attendance){
            $response->assertSee($attendance->formatted_date);
        }
    }

    //詳細ボタンが機能する
    public function test_detail_button_functions_correctly(){
        $user = User::find(1);
        \Carbon\Carbon::setTestNow('2024-12-01');
        $attendance = Attendance::find(1);
        $response = $this->actingAs($user)->get('/attendance/list');    
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee('2024年');
        $response->assertSee('12月21日');
    }
}
