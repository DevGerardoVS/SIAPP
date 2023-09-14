<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class MetasHelper{
	
    public static function actividades($upp,$anio){
        try {
			$proyecto = DB::table('mml_mir')
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
			$actv = DB::table('mml_actividades')
			->leftJoin('mml_catalogos', 'mml_catalogos.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(nombre,IFNULL(mml_catalogos.valor,nombre)) AS actividad"),
					'ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('mml_catalogos.deleted_at', '=', null)
				->where('mml_actividades.clv_upp',$upp)
				->where('mml_actividades.ejercicio', $anio);
			$query2 = DB::table('metas')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($actv, 'act', function ($join) {
					$join->on('metas.actividad_id', '=', 'act.id');
				})
				->select(
					'metas.id',
					'metas.estatus',
					'act.entidad',
					'act.area',
					'metas.ejercicio',
					'metas.clv_fondo as fondo',
					'act.actividad AS actividad',
					'metas.tipo',
					'metas.total',
					'metas.cantidad_beneficiarios',
					'beneficiarios.beneficiario',
					'unidades_medida.unidad_medida',
				)
				->where('metas.mir_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('act.clv_upp',$upp)
				->where('metas.ejercicio', $anio);
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
					'metas.ejercicio',
					'metas.clv_fondo as fondo',
					'pro.actividad AS actividad',
					'metas.tipo',
					'metas.total',
					'metas.cantidad_beneficiarios',
					'beneficiarios.beneficiario',
					'unidades_medida.unidad_medida',
				)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio',$anio)
				->where('pro.upp',$upp)
				->unionAll($query2)->get();
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
}