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
use App\Models\AttendanceRequest;

class AdminAttendanceCorrectionTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //承認待ちの申請が表示される
    public function test_admin_pending_requests_are_displayed_properly(){
        $admin = User::find(2);      
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $response = $this->get('/stamp_correction_request/list/?tab=pending');
        $response->assertSee('承認待ち');
        $response->assertSee('一般ユーザー');
        $response->assertSee('2024/12/21');
    }

    //承認済みの申請が表示される
    public function test_admin_approved_requests_are_displayed_properly(){
        $admin = User::find(2);      
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $response = $this->get('/stamp_correction_request/list/?tab=approved');
        $response->assertSee('承認済み');
    }

    //修正申請の詳細内容の取得・承認機能
    public function test_admin_approval_button_functions_properly(){
        $admin = User::find(2);  
        $attendanceRequest = AttendanceRequest::find(1);    
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $response = $this->get('/stamp_correction_request/list/?tab=pending');
        $response = $this->get("/stamp_correction_request/approve/{$attendanceRequest->id}");
        $response->assertSee('一般ユーザー');
        $response->assertSee('2024年');
        $response->assertSee('12月21日');
        $response->assertSee('承認');

        $response = $this->followingRedirects()->post("/stamp_correction_request/approve/{$attendanceRequest->id}");
        $response->assertStatus(200);
        $response->assertSee('承認済み');

        //申請一覧画面の承認済み部分の確認
        $response = $this->get('/stamp_correction_request/list/?tab=approved');
        $response->assertSee('2024/12/21');
    }
}

