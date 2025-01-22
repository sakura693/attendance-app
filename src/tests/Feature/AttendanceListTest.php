<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; //追加
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

    public function test_attendance_list_is_displayed_correctly(){
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/attendance/list');
        
        $response->assertStatus(200);

        $attendances = Attendance::where('user_id', $user->id)->get();

        foreach ($attendances as $attendance){
            $response->assertSee($attendance->formatted_date);
        }
    }
}
