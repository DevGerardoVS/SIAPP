<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermisosUpp extends Model
{
  use HasFactory, SoftDeletes;
    protected $table = 'permisos_funciones';

    protected $fillable = [
		'id_user',
    'id_permiso',
    'descripcion'
    ];
    protected $dates = ['deleted_at'];


    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
