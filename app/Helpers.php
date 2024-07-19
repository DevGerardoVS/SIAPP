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

function getEntidadEje($upp,$ur,$anio) {
    $resul = DB::table('programacion_presupuesto')
        ->select(DB::raw('CONCAT(programacion_presupuesto.upp,programacion_presupuesto.subsecretaria,programacion_presupuesto.ur) AS entidad'))
        ->where('programacion_presupuesto.ur', '=', $ur)
        ->where('programacion_presupuesto.upp', '=', $upp)
        ->where('programacion_presupuesto.ejercicio', '=',$anio)
        ->first();
    return $resul->entidad;
}
function getCatUpp($ejercicio)
{
    $upp = DB::table('catalogo as c06')
    ->select(
        'c06.clave as clv_upp',
        DB::raw('CONCAT(c06.clave, "  ",c06.descripcion) as upp'))
        ->where(['c06.deleted_at'=>null,
        'c06.grupo_id'=>6,
        'c06.ejercicio'=>$ejercicio
        ])
    ->distinct()
    ->orderBy('clv_upp');
    if(auth::user()->id_grupo==4 || auth::user()->id_grupo==7){
        $upp = $upp->where('c06.clave', auth::user()->clv_upp);
    }

    $upp = $upp->get();
    return $upp;
}
function getCatUr($ejercicio,$upp){
    if(auth::user()->id_grupo==4 || auth::user()->id_grupo==7){
        $upp = auth::user()->clv_upp;
    }
    $ur = DB::table('entidad_ejecutora as ej')
    ->leftJoin('catalogo as c06', 'ej.upp_id', '=', 'c06.id')
    ->leftJoin('catalogo as c08', 'ej.ur_id', '=', 'c08.id')
    ->select(
        'c08.clave as clv_ur',
        DB::raw('CONCAT(c08.clave, "  ",c08.descripcion) as ur')
    )->where([   
            'ej.deleted_at' => null,
            'ej.ejercicio' => $ejercicio,
            'c06.clave' => $upp
        ])->whereIn('ej.estatus',[0,3,4])
        ->whereNotNull('ej.upp_id')
        ->whereNotNull('ej.subsecretaria_id')
        ->whereNotNull('ej.ur_id')
    ->distinct();
        if(auth::user()->id_grupo==7){
            $ur = $ur->where('c08.clave', auth::user()->clv_ur);
        }
        $ur =$ur->orderBy('clv_ur')->get();
    return $ur;
 }


