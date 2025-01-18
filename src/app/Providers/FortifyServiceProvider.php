<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Responses\LoginResponse; //追加　
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract; //追加　
use Illuminate\Support\Facades\Validator; // Validator ファサード
use Illuminate\Support\Facades\Hash; // パスワードのハッシュチェック
use App\Models\User; // User モデル


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class); //追加
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //カスタムLoginResponseを登録
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function(){
            return view('auth.register');
        });

        //一般ユーザーと管理者でログイン画面分ける
        Fortify::loginView(function(){
            if (request()->is('admin/*')){
                return view('auth.admin-login');
            }
            return view('auth.login');
        });

        //管理者ログインのロジック
        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            $user = \App\Models\User::where('email', $request->email)->where(function ($query) {
                if (request()->is('admin/*')) {
                $query->where('role', 'admin'); // 管理者のみ許可
                }
            })->first();

            if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });



        RateLimiter::for('login', function(Request $request){
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
        
    }
}
