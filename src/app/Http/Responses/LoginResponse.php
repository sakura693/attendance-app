<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request){
        $user = $request->user();

        if(!$user->hasVerifiedEmail()){
            return redirect('/email/verify');
        }

        if ($request->is('admin/*')){
            return redirect('/admin/attendance/list');
        }
        
        return redirect('/attendance');
    }
}