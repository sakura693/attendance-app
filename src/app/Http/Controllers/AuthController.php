<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //仮
    public function login(){
        return view('auth.login');
    }

    //仮
    public function register(){
        return view('auth.register');
    }

    //仮
    public function adminLogin(){
        return view('auth.admin-login');
    }
}
