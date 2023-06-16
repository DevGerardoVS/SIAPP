<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramacionPresupuesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programacion_presupuesto';

    protected $fillable = [
    	'clasificacion_administrativa_id',
    	'clasificacion_geografica_id',
    	'entidad_ejecutora_id',
    	'area_funcional_id',
    	'mes_afectacion',
    	'posicion_presupuestaria_id ',
    	'tipo_gasto_id ',
    	'ejercicio',
    	'etiquetado_id ',
		'fondo_id ',
    	'proyecto_presupuestal_id ',
		'clave_presupuestal',
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
		'unidad_medida_id',
		'beneficiarios_id',
		'estado',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}

