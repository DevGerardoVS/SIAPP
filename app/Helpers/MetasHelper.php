<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers;

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
				'metas.beneficiario_id',
				'metas.unidad_medidad_id'
			)
			->where('metas.deleted_at', '=', null)->get();
            return $query;
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
}