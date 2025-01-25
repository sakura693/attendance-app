<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Validation\ValidationException;
use App\Models\AttendanceRequest;

class AttendanceDetailTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠詳細情報を取得
    public function test_get_attendance_detail(){
        $user = User::find(1);
        $attendance = Attendance::find(1);
        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");               
        $response->assertStatus(200);
        $response->assertSee('一般ユーザー');
        $response->assertSee('2024年');
        $response->assertSee('12月21日');
        $response->assertSee('09:30');
        $response->assertSee('17:30');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }   
    
    /*修正機能*/
    /** 
     * @dataProvider validationDataProvider
    */
    //修正機能のバリデーション確認
    public function test_validation_rules($invalidData, $expectedErrorMessage){
        $user = User::find(1);
        $attendance = Attendance::find(3);
        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");  

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
    public function test_reason_is_required(){
        $user = User::find(1);
        $attendance = Attendance::find(3);
        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}"); 

        $invalidData = [
            'reason' => ''
        ];
        $response = $this->post('/attendance/list', $invalidData);
        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください。'
        ]);
    } 


    //修正処理の実行
    public function test_correction_request_functions_correctly(){
        $user = User::find(1);
        $attendance = Attendance::find(3);
        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}"); 

        $request = [
            'year' => '2024年', 
            'monthDay' => '12月01日',
            'attendance_id' => $attendance->id,
            'clock_in_time' => '09:30',
            'clock_out_time' => '18:30',
            'break_start' => ['12:10'],
            'break_end' => ['13:00'],
            'reason' => '電車遅延のため'
        ];
        $response = $this->post('/attendance/list', $request); 
        $response->assertStatus(302);

        $this->assertDatabaseHas('attendance_requests', [
            'new_clock_in_time' => '09:30',
            'new_clock_out_time' => '18:30',
            'pending_reason' => '電車遅延のため'
        ]);
        //ログインユーザー画面で承認待ちの確認
        $response = $this->get("/stamp_correction_request/list/?tab=pending");
        $response->assertSee('2024/12/21');
        $response->assertSee('2024/12/01');

        //詳細部分の確認
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('2024年');
        $response->assertSee('12月01日');
        
        //管理ユーザー画面で確認
        $admin = User::find(2);
        $attendanceRequest = AttendanceRequest::find(1);
        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$attendanceRequest->id}");
        $response->assertSee('承認');

        //管理ユーザー画面で承認待ちの確認
        $response = $this->get("/stamp_correction_request/list/?tab=pending");
        $response->assertSee('2024/12/01');
    }
}
