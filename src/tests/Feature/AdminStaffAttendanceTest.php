<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; //追加
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffAttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //スタッフ一覧ページを取得・特定のスタッフの勤怠詳細を取得
    public function test_admin_get_staff_list(){
        $admin = User::find(2);
        $user = User::find(1);       
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response->assertSee('一般ユーザー');
        $response->assertSee('general@example.com');

        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertSee('12/01 (日)');
        $response->assertSee('12/21 (土)');
    } 

    //前月ボタンが機能する
    public function test_admin_previous_month_button_functions_correctly(){
        $admin = User::find(2);
        $user = User::find(1);       
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertSee('2024/12');

        $response = $this->get("/admin/attendance/staff/{$user->id}?action=prev");
        $response->assertSee('2024/11');
    } 

    //翌月ボタンが機能する
    public function test_admin_next_month_button_functions_correctly(){
        $admin = User::find(2);
        $user = User::find(1);       
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertSee('2024/12');

        $response = $this->get("/admin/attendance/staff/{$user->id}?action=next");
        $response->assertSee('2025/01');
    } 

    //詳細画面を取得
    public function test_admin_get_attendance_detail(){
        $admin = User::find(2);
        $user = User::find(1);    
        $attendance = Attendance::find(3);   
        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('2024年');
        $response->assertSee('12月01日');
    } 
}
