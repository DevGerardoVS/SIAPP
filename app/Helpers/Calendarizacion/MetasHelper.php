<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MetasHelper{
	
    public static function actividades($upp,$anio){
        try {
			$proyecto = DB::table('mml_mir')
				/* ->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')  */
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.indicador as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp);
		 	$query = DB::table('metas')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.mir_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'metas.estatus',
					'pro.entidad',
					'pro.area',
					'pro.ejercicio',
					'metas.clv_fondo as fondo',
					'pro.actividad',
					'metas.tipo',
					'metas.total',
					'metas.cantidad_beneficiarios',
					'beneficiarios.beneficiario',
					'unidades_medida.unidad_medida',
				)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio',$anio)->where('pro.upp',$upp)->get();
            return $query;
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

	public static function beneficiarios(){
		$result = DB::table('beneficiarios')
		->select(
			'id',
			'clave',
			'beneficiario'
		)
		->where('deleted_at', null)
		->get();

		return $result;
	}

	public static function unidadMedida(){
		$result = DB::table('unidades_medida')
		->select(
			'id',
			'clave',
			'unidad_medida'
		)
		->where('deleted_at', null)
		->get();

		return $result;
	}

	public static function tCalendario(){

		$tipo=[];
		$tipo[] = ['0', 'Acumulativa'];
		$tipo[] = ['1', 'Continua'];
		$tipo[] = ['2', 'Especial'];
		return  $tipo;
	}
	public static function apiMetas($anio,$upp)
	{
		try {
			$proyecto = DB::table('actividades_mir')
				->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
				->select(
					'actividades_mir.id',
					'actividades_mir.clv_actividad',
					'proyectos_mir.clv_upp AS upp',
					'proyectos_mir.entidad_ejecutora AS entidad',
					'proyectos_mir.area_funcional AS area',
					'proyectos_mir.ejercicio',
					'actividades_mir.actividad as actividad'
				)
				->where('proyectos_mir.deleted_at', '=', null)
				->where('proyectos_mir.ejercicio', $anio)
				->where('proyectos_mir.clv_upp', $upp);

			$query = DB::table('metas')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.actividad_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'pro.entidad',
					'pro.area',
					'pro.ejercicio',
					'metas.clv_fondo as fondo',
					'pro.clv_actividad',
					'pro.actividad',
					'metas.tipo',
					'metas.total',
					'metas.cantidad_beneficiarios',
					'beneficiarios.beneficiario',
					'unidades_medida.unidad_medida',
				)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp);
			$query = $query->get();
			return $query;
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
		
    }
	public static function apiMetasFull($anio)
	{
		try {
			$proyecto = DB::table('actividades_mir')
				->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
				->select(
					'actividades_mir.id',
					'actividades_mir.clv_actividad',
					'proyectos_mir.clv_upp AS upp',
					'proyectos_mir.entidad_ejecutora AS entidad',
					'proyectos_mir.area_funcional AS area',
					'proyectos_mir.ejercicio',
					'actividades_mir.actividad as actividad'
				)
				->where('proyectos_mir.deleted_at', '=', null)
				->where('proyectos_mir.ejercicio', $anio);

			$query = DB::table('metas')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.actividad_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'pro.entidad',
					'pro.area',
					'pro.ejercicio',
					'metas.clv_fondo as fondo',
					'pro.clv_actividad',
					'pro.actividad',
					'metas.tipo',
					'metas.total',
					'metas.cantidad_beneficiarios',
					'beneficiarios.beneficiario',
					'unidades_medida.unidad_medida',
				)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio);
			$query = $query->get();
			return $query;
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
	public static function mir(){
       // $proyectoMir = DB::connection('mml')
		$proyectoMir = DB::table('mml_mir')
			->leftJoin('epp', 'epp.id', 'mml_mir.id_epp')
			->select(
				'epp.id AS id_epp',
				'epp.sector_publico_id',
				'epp.sector_publico_f_id',
				'epp.sector_economia_id',
				'epp.subsector_economia_id',
				'epp.ente_publico_id',
				'epp.upp_id',
				'epp.subsecretaria_id',
				'epp.ur_id',
				'epp.finalidad_id',
				'epp.funcion_id',
				'epp.subfuncion_id',
				'epp.eje_id',
				'epp.linea_accion_id',
				'epp.programa_sectorial_id',
				'epp.tipologia_conac_id',
				'epp.programa_id',
				'epp.subprograma_id',
				'epp.proyecto_id',
				'epp.ejercicio',
				'epp.presupuestable',
				'epp.confirmado',
				'mml_mir.id AS id_matriz',
			 	'mml_mir.clv_upp',
				'mml_mir.clv_ur',
				'mml_mir.clv_pp',
				'mml_mir.nivel',
				'mml_mir.id_epp',
				'mml_mir.componente_padre',
				'mml_mir.objetivo',
				'mml_mir.indicador',
				'mml_mir.definicion_indicador',
				'mml_mir.metodo_calculo',
				'mml_mir.descripcion_metodo',
				'mml_mir.tipo_indicador',
				'mml_mir.unidad_medida',
				'mml_mir.dimension',
				'mml_mir.comportamiento_indicador',
				'mml_mir.frecuencia_medicion',
				'mml_mir.medios_verificacion',
				'mml_mir.lb_valor_absoluto',
				'mml_mir.lb_valor_relativo',
				'mml_mir.lb_anio',
				'mml_mir.lb_periodo_i',
				'mml_mir.lb_periodo_f',
				'mml_mir.mp_valor_absoluto',
				'mml_mir.mp_valor_relativo',
				'mml_mir.mp_anio',
				'mml_mir.mp_anio_meta',
				'mml_mir.mp_periodo_i',
				'mml_mir.mp_periodo_f',
				'mml_mir.supuestos',
				'mml_mir.estrategias',
				'mml_mir.ejercicio',
			)
			->where('mml_mir.deleted_at', null)
			->where('mml_mir.nivel', 11)
			->where('epp.deleted_at', null)
			->get();
        return $proyectoMir;
    }
}