<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Requests\LoginRequest; 

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        $validated = $request->validated();

        if(Auth::attempt($request->only('email', 'password'))){
            return app(LoginResponse::class);
        }
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
}
