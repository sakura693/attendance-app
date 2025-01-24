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
use App\Models\AttendanceRequest;

class AdminAttendanceCorrectionTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //承認待ち申請が全て表示される
    public function test_admin_pending_requests_are_displayed_properly(){
        $admin = User::find(2);      
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $response = $this->get('/stamp_correction_request/list/?tab=pending');
        $response->assertSee('承認待ち');
        $response->assertSee('一般ユーザー');
        $response->assertSee('2024/12/21');
    }

    //承認済みの申請が全て表示される
    public function test_admin_approved_requests_are_displayed_properly(){
        $admin = User::find(2);      
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $response = $this->get('/stamp_correction_request/list/?tab=approved');
        $response->assertSee('承認済み');
    }

    //修正申請の詳細内容が正しく取得される・承認機能
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
    }
}

