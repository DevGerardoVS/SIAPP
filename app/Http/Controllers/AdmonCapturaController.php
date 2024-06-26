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
        $anioActivo = DB::select('SELECT ejercicio FROM cierre_ejercicio_claves order by ejercicio desc LIMIT 1');
        $anio = $anioActivo[0]->ejercicio;
        session(["anio"=>$anio]); //variable de sesión para usar en las demás funciones
        $comprobarAnioPP = DB::select("SELECT ejercicio FROM programacion_presupuesto WHERE ejercicio = $anio AND deleted_at IS NULL"); // Comprobar si existen datos en PP con el año dado y el campo deleted_at nulo

        $countData = count(DB::select("SELECT id FROM programacion_presupuesto_hist WHERE ejercicio = $anio AND deleted_at IS NOT NULL")); //Comprobar si hay datos en PPH
        $version = 0;
        if($countData > 0){ // Comprobar si hay datos en la tabla
            $getVersion = DB::select("SELECT distinct(version) FROM programacion_presupuesto_hist where ejercicio = $anio AND deleted_at IS NOT NULL ORDER BY version DESC LIMIT 1");
            log::info($version);
            $version = $getVersion[0]->version;
        }

        $comprobarEstadoPP = DB::select("SELECT upp, ejercicio, estado FROM programacion_presupuesto WHERE ejercicio = $anio AND deleted_at IS NULL GROUP BY upp");
        $comprobarEMM = DB::select("SELECT m.estatus, am.clv_upp, am.ejercicio FROM metas m JOIN mml_mir am ON m.mir_id = am.id WHERE am.ejercicio = $anio AND m.deleted_at IS NULL GROUP BY am.clv_upp"); // Variable para comprobar el estado de metas por mir
        $comprobarEMA = DB::select("SELECT m.estatus, act.clv_upp, act.ejercicio FROM metas m JOIN mml_actividades act ON m.actividad_id = act.id WHERE act.ejercicio = $anio AND m.deleted_at IS NULL GROUP BY act.clv_upp"); // Variable para comprobar el estado de metas por actividad  
        $upps = DB::select("SELECT c.clave, c.descripcion FROM catalogo c join cierre_ejercicio_claves cec on c.clave = cec.clv_upp WHERE grupo_id = 6 AND c.ejercicio = $anio AND cec.ejercicio = $anio AND c.deleted_at IS NULL ORDER BY clave ASC");

        return view("captura.admonCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'upps' => $upps,
            'comprobarEstadoPP' => $comprobarEstadoPP,
            'comprobarEMM' => $comprobarEMM,
            'comprobarEMA' => $comprobarEMA,
            'comprobarAnioPP' => $comprobarAnioPP,
            'version' => $version,
        ]);
    }  

    public function clavesPresupuestarias(Request $request){
        $estatus = $request->estatus;
        $anio = session("anio");
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
        ->whereNull("c.deleted_at")
        ->where("cec.ejercicio", "=", $anio)
        ->where("c.ejercicio", "=", $anio)
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
        $anio = session("anio");
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
        ->whereNull("c.deleted_at")
        ->where("cem.ejercicio", "=", $anio)
        ->where("c.ejercicio", "=", $anio)
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
        $checar_upp_PP = '';
        $checar_upp_metas = '';
        $checar_upp_metasA = '';
        $checar_clave = '';
        $checar_ambos = '';

        if($upp != null){
            $checar_ambos = "AND cec.clv_upp = '$upp' AND cem.clv_upp = '$upp'";
            $checar_clave = "AND clv_upp = '$upp'";
            $checar_upp_PP = "AND upp = '$upp'";
            $checar_upp_metas = "AND am.clv_upp = '$upp'";
            $checar_upp_metasA = "AND act.clv_upp = '$upp'";
        }  

        try {
            DB::beginTransaction();
            
            // Update a las tablas de cierre ejercicio
            str_contains($modulo,',') ? DB::update("UPDATE $modulo SET cec.estatus = '$habilitar', cec.updated_user = '$usuario', cem.estatus = '$habilitar', cem.updated_user = '$usuario' WHERE cec.ejercicio = $anio AND cem.ejercicio= $anio $checar_ambos") : DB::update("UPDATE $modulo SET estatus = '$habilitar', updated_user = '$usuario' WHERE ejercicio = $anio $checar_clave");
            
            // Update a las tablas de programación presupuesto (claves) y metas
            if($estado == "activo"){
                if($modulo == "cierre_ejercicio_claves cec" || $modulo == "cierre_ejercicio_claves cec, cierre_ejercicio_metas cem"){
                    DB::update("UPDATE programacion_presupuesto SET estado = 0 WHERE ejercicio = $anio  $checar_upp_PP");
                }
                if($modulo == "cierre_ejercicio_metas cem" || $modulo == "cierre_ejercicio_claves cec, cierre_ejercicio_metas cem"){
                    DB::update("UPDATE metas m JOIN mml_mir am ON m.mir_id = am.id SET m.estatus = 0 WHERE am.ejercicio = $anio AND m.estatus = 1 $checar_upp_metas");
                    DB::update("UPDATE metas m JOIN mml_actividades act ON m.actividad_id = act.id SET m.estatus = 0 WHERE act.ejercicio = $anio AND m.estatus = 1 $checar_upp_metasA");
                }
            }

            if($upp == "") $upp = "Todas";
            $b = array(
                "username"=>Auth::user()->username,
                "accion"=> "Editar: UPP ".$upp.", ".$habilitar,
                "modulo"=> $modulo,
            );
            Controller::bitacora($b);
            DB::commit();
        // try{
            return redirect()->route("index")->withSuccess('Los datos fueron modificados');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->debug('exp ' . $e->getMessage());
            return back()->withErrors(['msg'=>'¡Ocurrió un error al modificar los datos!']);
        }
    }

    public function updateProgramacionPH(Request $request){
        Controller::check_permission('getCaptura');
        
        $getAnio = session("anio");
        $usuario = Auth::user()->username;

        try {
            DB::beginTransaction();
            DB::select("CALL corte_anual(" . $getAnio . ",'". $usuario . "')");
            $b = array(
                "username"=>Auth::user()->username,
                "accion"=> "Editar programación Presupuesto Hist",
                "modulo"=> "programación Presupuesto Hist",
            );

            Controller::bitacora($b);
            DB::commit();
            return redirect()->route("index")->withSuccess('¡Corte hecho!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->debug('exp ' . $e->getMessage());
            return back()->withErrors(['msg'=>'¡Ocurrió un error al hacer el corte!']);
        }
    }


}
