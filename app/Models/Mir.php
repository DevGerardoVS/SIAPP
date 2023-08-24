<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mml_mir';

    protected $fillable = [
    	'entidad_ejecutora',
    	'area_funcional',
    	'clv_upp',
    	'clv_ur',
    	'clv_pp',
    	'nivel',
    	'id_epp',
    	'componente_padre',
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
        'mp_anio_meta',
        'ejercicio', 
        'fondo_id',
        'mp_periodo_i',
        'mp_periodo_f',  
        'supuestos',   
        'estrategias',  
        'ejercicio',   
        'created_user',    
        'updated_user',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}

