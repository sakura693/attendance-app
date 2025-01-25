<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; 
use Database\Seeders\DatabaseSeeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AdminAttendanceDetailTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //管理者勤怠詳細画面の取得
    public function test_admin_get_attendance_detail(){
        $admin = User::find(2);
        $attendance = Attendance::find(1);        Carbon::setTestNow('2024-12-21');
        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response = $this->get("attendance/{$attendance->id}");
        $response->assertSee('2024年');
        $response->assertSee('12月21日');
    }

    /** 
     * @dataProvider validationDataProvider
    */
    //管理者修正機能のバリデーションチェック
    public function test_admin_validation_rules($invalidData, $expectedErrorMessage){
        $admin = User::find(2);
        $attendance = Attendance::find(3);
        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}"); 

        $mockRequest = new class extends CorrectionRequest {
            public function passedValidation()
            {
                parent::passedValidation(); 
            }
        };
        $mockRequest->merge($invalidData);

        try {
            $mockRequest->passedValidation();
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('validation_error', $e->errors());
            $this->assertEquals(
                [$expectedErrorMessage],
                $e->errors()['validation_error']
            );
            return;
        }
        $this->fail('ValidationException was not thrown.'); 
    }

    public function validationDataProvider(){
        return [
            'case 1: clock_in_time_after_clock_out_time'=> [
                [
                    'clock_in_time' => '18:00',
                    'clock_out_time' => '09:00',
                    'reason' => '電車遅延のため'
                ],
                '出勤時間もしくは退勤時間が不適切な値です。'
            ],
            'case 2: break_start_after_clock_out_time'=>[
                [
                    'break_start' => ['18:00'],
                    'clock_out_time' => '09:00',
                    'reason' => '電車遅延のため'
                ],
                '休憩時間が勤務時間外です。'
            ],
            'case 3: break_end_after_clock_out_time'=>[
                [
                    'break_start' => ['12:00'],
                    'break_end' => ['18:00'],
                    'clock_out_time' => '17:00',
                    'reason' => '電車遅延のため'
                ],
                '休憩時間が勤務時間外です。'
            ],
        ];
    }

    //備考欄が未記入の場合
    public function test_admin_reason_is_required(){
        $admin = User::find(2);
        $attendance = Attendance::find(3);
        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}"); 

        $invalidData = [
            'reason' => ''
        ];
        $response = $this->post('/attendance/list', $invalidData);

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください。'
        ]);
    } 
}
