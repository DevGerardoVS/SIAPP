<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioGrupo extends Model
{
    use HasFactory;

    protected $table = 'adm_rel_user_grupo';

    protected $fillable = [
    	'id_grupo',
    	'id_usuario'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
