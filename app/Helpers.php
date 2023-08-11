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
    $anio = DB::table('actividades_mir')
        ->select(
            DB::raw("IFNULL(actividades_mir.ejercicio," . date('Y') . ") AS ejercicio")
        )
        ->groupBy('actividades_mir.ejercicio')
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
function confirmGoals($upp, $anio)
{

    $proyecto = DB::table('actividades_mir')
        ->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
        ->select(
            'actividades_mir.id',
            'proyectos_mir.area_funcional AS area',
            'proyectos_mir.clv_upp'
        )
        ->where('actividades_mir.deleted_at', null)
        ->where('proyectos_mir.deleted_at', null)
        ->where('proyectos_mir.clv_upp', $upp);
    $metas = DB::table('metas')
        ->leftJoinSub($proyecto, 'pro', function ($join) {
            $join->on('metas.actividad_id', '=', 'pro.id');
        })
        ->select(
            'metas.id',
            'metas.estatus'
        )
        ->where('metas.estatus', 1)
        ->where('pro.clv_upp', '=', $upp)
        ->get();

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
    if (count($activs)) {
        $auxAct = count($activs);
        $index = 0;
        foreach ($activs as $key) {
            $proyecto = DB::table('actividades_mir')
                ->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
                ->select(
                    'actividades_mir.id',
                    'proyectos_mir.area_funcional AS area'
                )
                ->where('actividades_mir.deleted_at', null)
                ->where('proyectos_mir.deleted_at', null)
                ->where('proyectos_mir.clv_upp', $upp)
                ->where('proyectos_mir.area_funcional', $key->clave)
                ->get();
            if (count($proyecto)) {
                $index++;
            }
        }
    }
    return ["status" => false, "mensaje" => 'La captura de metas esté cerrada', "estado" => true];

}

