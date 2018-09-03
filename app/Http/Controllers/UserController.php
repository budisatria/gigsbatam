<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
    public function __construct()
    {
        if(Auth::guard('user')->user()!=null){
            $this->middleware('auth');
        }else if(Auth::guard('musician')->user()!=null){
            $this->middleware('musician');
        }else{
            $this->middleware('auth');
        }
    }
}
