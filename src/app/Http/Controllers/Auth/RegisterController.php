<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Actions\Fortify\CreateNewUser; 
use Illuminate\Support\Facades\Auth; 

class RegisterController extends Controller
{
    protected $createNewUser;

    public function __construct(CreateNewUser $createNewUser){
        $this->createNewUser = $createNewUser;
    }

    public function register(Request $request){
        $user = $this->createNewUser->create($request->all());

        Auth::login($user);
        return redirect('/attendance');
    }
}
