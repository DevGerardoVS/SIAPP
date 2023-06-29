<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCapturaController extends Controller
{
    //
    public function index(){
        $dataSet = array();
        $anio = date('Y');
        $upps = DB::select('SELECT clave,descripcion FROM catalogo WHERE grupo_id = 6 ORDER BY clave ASC');
        $estatus = DB::select('SELECT distinct estatus FROM cierre_ejercicio_claves');
        return view("captura.adminCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'estatus' => $estatus,
            'upps' => $upps,
        ]);
    }  

    public function clavesPresupuestarias(Request $request){
        $estatus = $request->estatus;
        $upp = $request->upp;
        $dataSet = array();
        $array_where = [];
        
        if($estatus != null){
            array_push($array_where,['cec.estatus','=',$estatus]);
        }

        if($upp != null){
            array_push($array_where,['cec.clv_upp','=',$upp]);
        }

        
        $data = DB::table("catalogo as c")
        ->join("cierre_ejercicio_claves as cec", function($join){
            $join->on("cec.clv_upp", "=", "c.clave");
        })
        ->select("cec.clv_upp", "c.descripcion", "cec.estatus", "cec.updated_at", "cec.updated_user")
        ->where("c.grupo_id", "=", 6)
        ->where($array_where)
        ->orderBy("cec.clv_upp","asc")
        ->get();
        // $data = DB::select("SELECT cec.clv_upp, c.descripcion, cec.estatus, cec.updated_at, cec.updated_user FROM catalogo c JOIN cierre_ejercicio_claves cec ON cec.clv_upp = c.clave WHERE c.grupo_id = 6 AND cec.estatus = 'cerrado' AND cec.ejercicio = '2023'  ORDER BY cec.clv_upp ASC");
        foreach ($data as $d) {
            // Log::info($d->descripcion);
            
            $ds = array($d->clv_upp, $d->descripcion, $d->updated_at, $d->estatus, $d->updated_user);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }
}
