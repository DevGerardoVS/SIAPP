<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seguimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sapp_seguimiento';

    protected $fillable = [
       
        'meta_id',
        'clv_upp',
        'clv_ur',
        'clv_programa',
        'clv_subprograma',
        'clv_proyecto',
        'realizado',
        'descripcion_act',
        'justificacion',
        'propuesta_mejora',
        'observaciones',
        'ejercicio',
        'mes',
        'estatus',
        'created_user',
        'updated_user',
        'deleted_user'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}