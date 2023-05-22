<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemaGrupo extends Model
{
    use HasFactory;

    protected $table = 'adm_rel_sistema_grupo';

    protected $fillable = [
    	'id_grupo',
    	'id_sistema'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
