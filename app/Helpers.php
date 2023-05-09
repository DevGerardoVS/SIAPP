<?php

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

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


