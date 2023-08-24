<?php

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\Controller;

function verifyRole($role){
    $validation = Auth::user()->hasRole($role);

    return $validation;
}

function verifyPermission($permission){
    $validation = Auth::user()->can($permission);

    return $validation;
}

function IsNullOrEmpty($value){

    if(isset($value)){
        return $value;
    }

    return 0;
}

function getExplodePartiqcipacion($data){

    $fecha = "Sin registro";
    $valor = 0;

    $array = explode("_",$data);

    if(count($array) == 2){
        $fecha = $array[0];
        $valor = $array[1];
    }

    return [$fecha,$valor];
}
 function check_assignFront($name) {
    $permiso = DB::table('permisos_funciones')
        ->select(
            'id_user',
            'id_permiso',
            )
        ->where('id_user', auth::user()->id)
        ->where('id_permiso', $name)->get();
    if(count($permiso)) {
        return true;
    }
    else
    return false;
}
function getAnios()
{
    $anio = DB::table('mml_mir')
        ->select(
            DB::raw("IFNULL(mml_mir.ejercicio," . date('Y') . ") AS ejercicio")
        )
        ->groupBy('mml_mir.ejercicio')
        ->get();
    return $anio;
}
function bitacoraRcont($email)
{
    $user = User::where('email', $email)->select('username')->first();
    $b = array(
        "username"=>$user->username,
        "accion"=>'Restablecer contraseña',
        "modulo"=>'Cambio de contraseña'
     );
     Controller::bitacora($b);
}

function cmetas($upp,$anio)
{
    $metas = DB::table('metas')
        ->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
        ->select(
            'mml_mir.entidad_ejecutora',
            'mml_mir.area_funcional'.
            'mml_mir.clv_upp'
        )
        ->where('mml_mir.clv_upp',$upp)
        ->where('mml_mir.ejercicio',$anio)
        ->where('mml_mir.deleted_at',null)
        ->where('mml_mir.estatus',0)->get();
        $activs = DB::table("programacion_presupuesto")
        ->select(
            'programa_presupuestario AS programa',
            DB::raw('CONCAT(upp,subsecretaria,ur) AS area'),
            DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS clave')
        )
        ->where('programacion_presupuesto.upp', '=', $upp)
        ->where('programacion_presupuesto.ejercicio', '=', $anio)
        ->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
        ->distinct()
        ->where('estado', 1)
        ->groupByRaw('programa_presupuestario')->get();
    if (count($metas) == count($activs)) {
        return ["status" => true];
    } else {
        return ["status" => true];
    }
}

