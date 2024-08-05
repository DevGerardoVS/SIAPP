<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Epp extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'epp';

    protected $fillable = [
        'ejercicio',
        'mes_i',
        'mes_f',
        'upp_id',
        'clasificacion_administrativa_id',
        'entidad_ejecutora_id',
        'clasificacion_funcional_id',
        'pladiem_id',
        'conac_id',
        'programa_id',
        'subprograma_id',
        'proyecto_id',
        'estatus',
        'presupuestable',
        'con_mir',
        'confirmado',
        'tipo_presupuesto',
        'created_user',
        'updated_user',
        'deleted_user'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at',
    ];
}
