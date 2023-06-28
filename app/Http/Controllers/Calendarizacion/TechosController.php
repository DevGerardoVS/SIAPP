<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechosController extends Controller
{
    //Consulta Vista Techos
    public function getIndex()
    {
        return view('calendarizacion.techos.index');
    }
}
