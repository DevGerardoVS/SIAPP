<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosUpp extends Model
{
    use HasFactory;

    protected $table = 'permisos_funciones';

    protected $fillable = [
		'id_user',
    'id_permiso',
    'descripcion'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
