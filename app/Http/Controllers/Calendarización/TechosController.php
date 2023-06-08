<?php

namespace App\Http\Controllers\Calendarización;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechosController extends Controller
{
    //Consulta Vista Techos
    public function getIndex()
    {
        return view('calendarización.techos.index');
    }
}
