<?php

namespace App\Http\Controllers\Calendarización;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use Datatables;
use App\Models\User;
use Auth;
use DB;
use Log;

class CalendarizacionCargaMasivaController extends Controller
{
     //Obtener plantilla para descargar
	public function getExcel(Request $request)	{
     $file='plantilla.xlsx';
    return response()->download(storage_path("templates/{$file}"));
	}
    
     //Obtener datos del excel
     public function getDataPlantilla(Request $request)	{
        $file='plantilla.xlsx';
       return response()->download(storage_path("templates/{$file}"));
       }

}