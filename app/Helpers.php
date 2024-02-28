<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\administracion\Sistema;
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
            'id_permiso')
        ->where('id_user', auth::user()->id)
        ->where('deleted_at',null)
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

function existMetas()
{
    $metas = DB::table('metas')->select(DB::raw("COUNT(*) AS datos"))->get();
    if ($metas[0]->datos >= 1) {
        return true;
    } else {
        return false;
    }
}
function SystemName(){
    $res = Sistema::where('id', Session::get('sistema'))->select('nombre_sistema')->get();
    return $res[0]->nombre_sistema;
}
function funcionessXsistemas($nombre)
{
    $func = DB::table('adm_funciones')
        ->select('id','tipo')
        ->where('modulo', $nombre)
        ->where('id_sistema',Session::get('sistema'))
        ->get();
    return $func;
}
function menuXsistema($nombre){
    $menu = DB::table('adm_funciones')
        ->select('id','tipo')
        ->where('padre', $nombre)
        ->where('id_sistema',Session::get('sistema'))
        ->orderBy('modulo')
        ->distinct()
        ->get();
    return $menu;

}
function check_sistema($id_group) {
    $permiso = DB::table('adm_rel_sistema_grupo')
        ->select(
            'id',
            )
        ->where('id_grupo', $id_group)
        ->where('id_sistema',1)->get();
    if(count($permiso)>=1) {
        return true;
    }
    else
    return false;
}


