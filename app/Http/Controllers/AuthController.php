<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    function store(Request $resquest){
        dd($resquest->all());
        return "ttotot";
    }
}
