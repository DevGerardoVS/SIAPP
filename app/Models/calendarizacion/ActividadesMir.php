<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActividadesMir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades_mir';

    protected $fillable = [
        'proyecto_mir_id',
        'clv_actividad',
        'actividad',
        'estatus',
        'objetivo',
        'indicador',
        'definicion_indicador',
        'metodo_calculo',
        'descripcion_metodo',
        'tipo_indicador',
        'unidad_medida',
        'dimension',
        'comportamiento_indicador',
        'frecuencia_medicion',
        'medios_verificacion',
        'lb_valor_absoluto',
        'lb_valor_relativo',
        'lb_anio',
        'lb_periodo_i',
        'lb_periodo_f',
        'mp_valor_absoluto',
        'mp_valor_relativo',
        'mp_anio',
        'mp_periodo_i',
        'mp_periodo_f',
        'supuestos',
        'estrategias',
        'ejercicio'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}