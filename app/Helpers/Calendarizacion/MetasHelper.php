<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MetasHelper{
	
    public static function actividades(){
        try {
			

            $proyecto = DB::table('actividades_mir')
			->leftJoin('proyectos_mir', 'proyectos_mir.id','actividades_mir.proyecto_mir_id' )
			->select(
				'actividades_mir.id',
				'proyectos_mir.clv_ur as ur',
				'proyectos_mir.clv_programa as programa',
				'proyectos_mir.clv_subprograma as subprograma',
				'proyectos_mir.clv_proyecto as proyecto',
				'actividades_mir.actividad as actividad'
			)
			->where('proyectos_mir.deleted_at', '=', null);
			$query = DB::table('metas')
			->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
			->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
			->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
			->leftJoinSub($proyecto, 'pro', function ($join) {
				$join->on('metas.actividad_id', '=', 'pro.id');
			})
			->select(
				'metas.id',
				'pro.ur',
				'pro.programa',
				'pro.subprograma',
				'pro.proyecto',
				'metas.clv_fondo as fondo',
				'pro.actividad',
				'metas.tipo',
				'metas.total',
				'metas.cantidad_beneficiarios',
				'beneficiarios.beneficiario',
				'unidades_medida.unidad_medida',
			)
			->where('metas.deleted_at', '=', null)->get();
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