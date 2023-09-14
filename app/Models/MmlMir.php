<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MmlMir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mml_actividades';

    protected $fillable = [ 
        'clv_upp',
        'entidad_ejecutora',    
        'area_funcional',
        'id_catalogo',
        'nombre',
        'ejercicio',
        'updated_user', 
        'created_user', 
        'deleted_user'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}

