<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGrupo extends Model
{
    use HasFactory;

    protected $table = 'adm_rel_menu_grupo';

    protected $fillable = [
    	'id_grupo',
    	'id_menu'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
