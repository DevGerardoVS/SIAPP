<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmonCapturaController extends Controller
{
    //
    public function index(){
        Controller::check_permission('getCaptura');
        $dataSet = array();
        $anioActivo = DB::select('SELECT ejercicio FROM cierre_ejercicio_claves WHERE activos = 1 LIMIT 1');
        $anio = $anioActivo[0]->ejercicio;
        $comprobarEstado = DB::select("SELECT upp, ejercicio, estado FROM programacion_presupuesto WHERE ejercicio = $anio GROUP BY upp");
        $upps = DB::select('SELECT c.clave, c.descripcion FROM catalogo c join cierre_ejercicio_claves cec on c.clave = cec.clv_upp WHERE grupo_id = 6 AND activos = 1 AND c.deleted_at is null ORDER BY clave ASC');
        return view("captura.admonCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'upps' => $upps,
            'comprobarEstado' => $comprobarEstado,
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
        ->where("c.deleted_at", "=", null)
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
        ->where("c.deleted_at", "=", null)
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
        Controller::check_permission('getCaptura');
        $upp = $request->upp_filter;
        $modulo = $request->modulo_filter;
        $habilitar = $request->capturaRadio;
        $estado = $request->estado;
        $anio = $request->anio;
        $usuario = Auth::user()->username;
        $checar_upp_cierre = '';
        $checar_upp_PP = '';
        // $checar_upp_metas = '';
        if($upp != null){
            $checar_upp_cierre = "AND clv_upp = '$upp'";
            $checar_upp_PP = "AND upp = '$upp'";
            // $checar_upp_metas = "AND upp = '$upp'";
        }  

        try {
            DB::beginTransaction();
            
            $actualizarCierres = str_contains($modulo,',') ? DB::update("UPDATE $modulo SET cec.estatus = '$habilitar', cec.updated_user = '$usuario', cem.estatus = '$habilitar', cem.updated_user = '$usuario' WHERE cec.activos = 1 AND cem.activos = 1 $checar_upp_cierre") : DB::update("UPDATE $modulo SET estatus = '$habilitar', updated_user = '$usuario' WHERE activos = 1 $checar_upp_cierre");
            
            if($estado == "activo"){
                $actualizarPP = DB::update("UPDATE programacion_presupuesto SET estado = 0 WHERE ejercicio = $anio AND estado = 1 $checar_upp_PP");
                // $actualizarMetas = DB::update("UPDATE programacion_presupuesto SET estado = 0 $checar_upp_metas");
            }

            DB::commit();
            return redirect()->route("index")->withSuccess('Los datos fueron modificados');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return back()->withErrors(['msg'=>'Ocurrió un error al modificar los datos']);
        }
    }
}
