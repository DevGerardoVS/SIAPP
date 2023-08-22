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

        $countData = count(DB::select("SELECT id FROM programacion_presupuesto_hist WHERE ejercicio = $anio")); //Comprobar si hay datos en PPH
        $version = 0;
        if($countData > 0){ // Comprobar si hay datos en la tabla
            $getVersion = DB::select("SELECT distinct(version) FROM programacion_presupuesto_hist where ejercicio = $anio ORDER BY version DESC LIMIT 1");
            $version = $getVersion[0]->version;
            session(["version"=>$version]);
        }

        $comprobarEstadoPP = DB::select("SELECT upp, ejercicio, estado FROM programacion_presupuesto WHERE ejercicio = $anio GROUP BY upp");
        $comprobarEstadoMetas = DB::select("SELECT m.estatus, am.clv_upp, am.ejercicio FROM metas m JOIN mml_mir am ON m.mir_id = am.id WHERE am.ejercicio = $anio GROUP BY am.clv_upp");        
        $upps = DB::select("SELECT c.clave, c.descripcion FROM catalogo c join cierre_ejercicio_claves cec on c.clave = cec.clv_upp WHERE grupo_id = 6 AND ejercicio = $anio AND c.deleted_at is null ORDER BY clave ASC");
        return view("captura.admonCaptura", [
            'dataSet' => json_encode($dataSet),
            'anio' => $anio,
            'upps' => $upps,
            'comprobarEstadoPP' => $comprobarEstadoPP,
            'comprobarEstadoMetas' => $comprobarEstadoMetas,
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
        ->where("c.deleted_at", "=", null)
        ->where("cec.ejercicio", "=", $anio)
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
        ->where("c.deleted_at", "=", null)
        ->where("cem.ejercicio", "=", $anio)
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
        $checar_clave = '';
        $checar_ambos = '';

        if($upp != null){
            $checar_ambos = "AND cec.clv_upp = '$upp' AND cem.clv_upp = '$upp'";
            $checar_clave = "AND clv_upp = '$upp'";
            $checar_upp_PP = "AND upp = '$upp'";
            $checar_upp_metas = "AND am.clv_upp = '$upp'";
        }  

        try {
            DB::beginTransaction();
            
            str_contains($modulo,',') ? DB::update("UPDATE $modulo SET cec.estatus = '$habilitar', cec.updated_user = '$usuario', cem.estatus = '$habilitar', cem.updated_user = '$usuario' WHERE cec.ejercicio = $anio AND cem.ejercicio= $anio $checar_ambos") : DB::update("UPDATE $modulo SET estatus = '$habilitar', updated_user = '$usuario' WHERE ejercicio = $anio $checar_clave");
            
            if($estado == "activo"){
                if($modulo == "cierre_ejercicio_claves cec" || $modulo == "cierre_ejercicio_claves cec, cierre_ejercicio_metas cem"){
                    DB::update("UPDATE programacion_presupuesto SET estado = 0 WHERE ejercicio = $anio  $checar_upp_PP");
                }
                if($modulo == "cierre_ejercicio_metas cem" || $modulo == "cierre_ejercicio_claves cec, cierre_ejercicio_metas cem"){
                    DB::update("UPDATE metas m JOIN mml_mir am ON m.mir_id = am.id SET m.estatus = 0 WHERE am.ejercicio = $anio AND m.estatus = 1 $checar_upp_metas");
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
            return redirect()->route("index")->withSuccess('Los datos fueron modificados');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg'=>'¡Ocurrió un error al modificar los datos!']);
        }
    }

    public function updateProgramacionPH(Request $request){
        Controller::check_permission('getCaptura');
        
        $getAnio = session("anio");
        $getVersion = session("version");
        $countData = count(DB::select("SELECT id FROM programacion_presupuesto_hist WHERE ejercicio = $getAnio")); //Comprobar si hay datos en PPH

        $version =  $countData > 0 ? $getVersion + 1 : 1;

        try {
            DB::beginTransaction();

            DB::select("INSERT INTO programacion_presupuesto_hist (id_original, version, clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,estado,tipo,deleted_at,updated_at,created_at,deleted_user,updated_user,created_user) SELECT id, $version,clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,estado,tipo,now(),updated_at,created_at,deleted_user,updated_user,created_user FROM programacion_presupuesto WHERE ejercicio = $getAnio AND deleted_at IS NULL");

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
            return back()->withErrors(['msg'=>'¡Ocurrió un error al hacer el corte!']);
        }
    }


}
