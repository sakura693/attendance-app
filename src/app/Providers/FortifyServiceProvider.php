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
use App\Http\Responses\LoginResponse; 
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract; 
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Hash; 
use App\Models\User; 


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function(){
            return view('auth.register');
        });

        Fortify::loginView(function(){
            if (request()->is('admin/*')){
                return view('auth.admin-login');
            }
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            $user = \App\Models\User::where('email', $request->email)->where(function ($query) {
                if (request()->is('admin/*')) {
                $query->where('role', 'admin'); 
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
