<?php

namespace App\Http\Controllers\Calendarizacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;

class ClavePreController extends Controller
{
    public function getPanel(){
        return view('clavePresupuestaria.index');
    }
    public function getClaves(Request $request){
        $claves = DB::table('programacion_presupuesto')
        ->SELECT('programacion_presupuesto.id','clave_presupuestal', DB::raw ('SUM(enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre) AS totalByClave'),'catalogo.clave','catalogo.descripcion')
        ->leftJoin('entidad_ejecutora','programacion_presupuesto.entidad_ejecutora_id', '=', 'entidad_ejecutora.id')
        ->leftJoin('catalogo', 'entidad_ejecutora.upp_id','=','catalogo.id')
        ->where('programacion_presupuesto.deleted_at', '=', null)
        ->groupBy('catalogo.clave')
        ->get();
        //Log::debug($claves);
        return response()->json($claves, 200);

    }
    public function getRegiones(){
        $regiones = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.region_id','catalogo.clave','catalogo.descripcion')
        ->leftJoin('catalogo', 'clasificacion_geografica.region_id', '=', 'catalogo.id')
        ->where('catalogo.deleted_at', '=', null)
        ->groupBy('catalogo.clave')
        ->distinct()
        ->get();
        return response()->json($regiones, 200);
    }
}
