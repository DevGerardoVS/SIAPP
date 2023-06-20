<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramacionPresupuesto extends Model
{
    use HasFactory;

    protected $fillable = ['clasificacion_administrativa','entidad_federativa','region','municipio',
    'localidad', 'upp', 'subsecretaria', 'ur', 'finalidad', 'funcion', 'subfuncion', 'eje', 'linea_accion',
    'programa_sectorial','tipologia_conac','programa_presupuestario','subprograma_presupuestario',
    'proyecto_presupuestario','periodo_presupuestal','posicion_presupuestaria','tipo_gasto',
    'anio', 'etiquetado', 'fuente_financiamiento','ramo','fondo_ramo','capital','proyecto_obra',
    'ejercicio','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre',
    'octubre', 'noviembre','diciembre','total','estado','tipo','created_user'
    ];

    protected $table = 'programacion_presupuesto';
}