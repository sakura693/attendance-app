<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request){
        $user = $request->user();

        //未認証の場合認証ページにリダイレクト
        if(!$user->hasVerifiedEmail()){
            return redirect('/email/verify');
        }

        if ($request->is('admin/*')){
            return redirect('/admin/attendance/list');
        }
        
        //一般ユーザーがログイン時のリダイレクト先
        return redirect('/attendance');
    }
}