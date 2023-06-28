<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCapturaController extends Controller
{
    //
    public function index(){
        return view("captura.adminCaptura");
    }
}
