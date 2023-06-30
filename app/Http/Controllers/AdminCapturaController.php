<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCapturaController extends Controller
{
    //
    public function index(){
        $dataSet = array();
        $anio_activo = DB::select('SELECT ejercicio FROM cierre_ejercicio_claves WHERE activos = 1 LIMIT 1');
        $anio = $anio_activo[0]->ejercicio;
        $upps = DB::select('SELECT c.clave, c.descripcion FROM catalogo c join cierre_ejercicio_claves cec on c.clave = cec.clv_upp WHERE grupo_id = 6 AND activos = 1 ORDER BY clave ASC');
        return view("captura.adminCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'upps' => $upps,
        ]);
    }  

    public function clavesPresupuestarias(Request $request){
        $estatus = $request->estatus;
        $dataSet = array();
        $array_where = [];
        
        if($estatus != null){
            array_push($array_where,['cec.estatus','=',$estatus]);
        }
        
        $data = DB::table("catalogo as c")
        ->join("cierre_ejercicio_claves as cec", function($join){
            $join->on("cec.clv_upp", "=", "c.clave");
        })
        ->select("cec.clv_upp", "c.descripcion", "cec.estatus", "cec.updated_at", "cec.updated_user")
        ->where("c.grupo_id", "=", 6)
        ->where("cec.activos", "=", 1)
        ->where($array_where)
        ->orderBy("cec.estatus","desc")
        ->orderBy("cec.clv_upp","asc")
        ->get();

        foreach ($data as $d) {
            
            $ds = array($d->clv_upp, $d->descripcion, Carbon::parse($d->updated_at)->format('d/m/Y'), $d->estatus, $d->updated_user);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function metasActividades(Request $request){
        $estatus = $request->estatus;
        $dataSet = array();
        $array_where = [];
        
        if($estatus != null){
            array_push($array_where,['cem.estatus','=',$estatus]);
        }

        
        $data = DB::table("catalogo as c")
        ->join("cierre_ejercicio_metas as cem", function($join){
            $join->on("cem.clv_upp", "=", "c.clave");
        })
        ->select("cem.clv_upp", "c.descripcion", "cem.estatus", "cem.updated_at", "cem.updated_user")
        ->where("c.grupo_id", "=", 6)
        ->where("cem.activos", "=", 1)
        ->where($array_where)
        ->orderBy("cem.estatus","desc")
        ->orderBy("cem.clv_upp","asc")
        ->get();

        
        foreach ($data as $d) {
            
            $ds = array($d->clv_upp, $d->descripcion, Carbon::parse($d->updated_at)->format('d/m/Y') , $d->estatus, $d->updated_user);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function update(Request $request){
        // dd($request);
    }
}
