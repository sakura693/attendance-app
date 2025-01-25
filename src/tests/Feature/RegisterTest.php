<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void{
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    //名前が未入力の場合
    public function test_name_is_required(){
        $response = $this->post('/register', [
            'name' => "",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ]);
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    //メールアドレスが未入力の場合
    public function test_email_is_required(){
        $response = $this->post('/register', [
            'name' => "テストユーザー",
            'email' => "",
            'password' => "password",
            'password_confirmation' => "password"
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    //パスワードが８文字未満の場合
    public function test_password_is_minimum_8_characters(){
        $response = $this->post('/register', [
            'name' => "テストユーザー",
            'email' => "test@gmail.com",
            'password' => "1234567",
            'password_confirmation' => "1234567"
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    //パスワードと確認パスが一致しない場合
    public function test_password_confirmation_must_match(){
        $response = $this->post('/register', [
            'name' => "テストユーザー",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "differntpassword"
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    //パスワードが未入力の場合
    public function test_password_is_required(){
        $response = $this->post('/register', [
            'name' => "テストユーザー",
            'email' => "test@gmail.com",
            'password' => "",
            'password_confirmation' => ""
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    //会員情報登録機能
    public function test_register_user(){
        $response = $this->post('/register', [
            'name' => "テストユーザー",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ]);
        
        $response->assertRedirect('/attendance');
        $this->assertDatabaseHas(User::class, [
            'name' => "テストユーザー",
            'email' => "test@gmail.com",
        ]);
    }
}
