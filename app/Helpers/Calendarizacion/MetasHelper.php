<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MetasHelper{
	
    public static function actividades($upp,$anio){
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
        $proyectoMir = DB::connection('mml')
			->table('matriz_indicadores_resultados')
			->leftJoin('fondos_db.epp', 'fondos_db.epp.id', 'matriz_indicadores_resultados.id_epp')
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
				'matriz_indicadores_resultados.id AS id_matriz',
			 	'matriz_indicadores_resultados.clv_upp',
				'matriz_indicadores_resultados.clv_ur',
				'matriz_indicadores_resultados.clv_pp',
				'matriz_indicadores_resultados.nivel',
				'matriz_indicadores_resultados.id_epp',
				'matriz_indicadores_resultados.componente_padre',
				'matriz_indicadores_resultados.objetivo',
				'matriz_indicadores_resultados.indicador',
				'matriz_indicadores_resultados.definicion_indicador',
				'matriz_indicadores_resultados.metodo_calculo',
				'matriz_indicadores_resultados.descripcion_metodo',
				'matriz_indicadores_resultados.tipo_indicador',
				'matriz_indicadores_resultados.unidad_medida',
				'matriz_indicadores_resultados.dimension',
				'matriz_indicadores_resultados.comportamiento_indicador',
				'matriz_indicadores_resultados.frecuencia_medicion',
				'matriz_indicadores_resultados.medios_verificacion',
				'matriz_indicadores_resultados.lb_valor_absoluto',
				'matriz_indicadores_resultados.lb_valor_relativo',
				'matriz_indicadores_resultados.lb_anio',
				'matriz_indicadores_resultados.lb_periodo_i',
				'matriz_indicadores_resultados.lb_periodo_f',
				'matriz_indicadores_resultados.mp_valor_absoluto',
				'matriz_indicadores_resultados.mp_valor_relativo',
				'matriz_indicadores_resultados.mp_anio',
				'matriz_indicadores_resultados.mp_anio_meta',
				'matriz_indicadores_resultados.mp_periodo_i',
				'matriz_indicadores_resultados.mp_periodo_f',
				'matriz_indicadores_resultados.supuestos',
				'matriz_indicadores_resultados.estrategias',
				'matriz_indicadores_resultados.ejercicio',
			)
			->where('matriz_indicadores_resultados.deleted_at', null)
			->where('matriz_indicadores_resultados.id_epp', null)
			->where('epp.deleted_at', null)
			->get();
        return $proyectoMir;
    }
}