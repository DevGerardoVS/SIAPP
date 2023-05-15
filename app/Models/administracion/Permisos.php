<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use HasFactory;

    protected $table = 'adm_rel_funciones_grupos';

    protected $fillable = [
    	'id_grupo',
    	'id_funcion'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
