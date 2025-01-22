<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations; //追加
use Database\Seeders\DatabaseSeeder;
use App\Models\User;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    /*一般ユーザーのログイン認証*/
    //メールが未入力の場合
    public function test_email_is_required(){
        $user = User::find(1);

        $response = $this->post('/login', [
            'email' => "",
            'password' => "password"
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    //パスワードが未入力の場合
    public function test_password_is_required(){
        $user = User::find(1);

        $response = $this->post('/login', [
            'email' => "general@example.com",
            'password' => ""
        ]);
        $response->assertSessionHasErrors(['password'=> 'パスワードを入力してください']);
    }

    //登録内容と一致しない場合
    public function test_login_fails_with_invalid_email(){
        $user = User::find(1);

        $response = $this->post('/login', [
            'email' => "general2@example.com",
            'password' => "password"
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    //ログイン機能
    public function test_login(){
        $user = User::find(1);

        $response = $this->post('/login', [
            'email' => "general@example.com",
            'password' => "password"
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }
    

    /*管理者のログイン認証*/
    //メールが未入力の場合
    public function test_admin_email_is_required(){
        $user = User::find(2);

        $response = $this->post('/admin/login', [
            'email' => "",
            'password' => "password"
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    //パスワードが未入力の場合
    public function test_admin_password_is_required(){
        $user = User::find(2);

        $response = $this->post('/admin/login', [
            'email' => "admin@example.com",
            'password' => ""
        ]);
        $response->assertSessionHasErrors(['password'=> 'パスワードを入力してください']);
    }

    //登録内容と一致しない場合
    public function test_admin_login_fails_with_invalid_email(){
        $user = User::find(2);

        $response = $this->post('/admin/login', [
            'email' => "admin2@example.com",
            'password' => "password"
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    //ログイン機能
    public function test_admin_login(){
        $user = User::find(2);

        $response = $this->post('/admin/login', [
            'email' => "admin@example.com",
            'password' => "password"
        ]);
        $response->assertRedirect('/admin/attendance/list');
        $this->assertAuthenticatedAs($user);
    }
    
}
