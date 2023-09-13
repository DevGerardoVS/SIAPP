<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'metas';

    protected $fillable = [
        'clv_fondo',
        'mir_id',
        'tipo',
        'beneficiario_id',
        'unidad_medida_id',
        'cantidad_beneficiarios',
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre',
        'total',
        'estatus',
        'ejercicio',
        'actividad_id',
        'created_user',
        'updated_user'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}