<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function correctionRequest(){
        return view('correction-request');
    }
}
