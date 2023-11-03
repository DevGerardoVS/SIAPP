<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmUsers extends Model
{
    use HasFactory;

    protected $table = 'adm_users';

    protected $fillable = [
        'id_grupo',
        'nombre',
        'p_apellido',
        's_apellido',
        'email',
        'celular',
        'username',
        'password',
        'clv_upp',
        'estatus',
        'created_user',
        'updated_user',
        'deleted_user',
        'remember_token'
    ];
    protected $dates = ['deleted_at'];
    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
