<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Auth;
use Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class MetasCmHelper
{

	public static function ProgPresupuestoMeses($obj,$perfil){
		$meses = DB::table('programacion_presupuesto')
		->select(
			DB::raw("IF(SUM(enero)>=1,1,0) AS enero"),
			DB::raw("IF(SUM(febrero)>=1,1,0) AS febrero"),
			DB::raw("IF(SUM(marzo)>=1,1,0) AS marzo"),
			DB::raw("IF(SUM(abril)>=1,1,0) AS abril"),
			DB::raw("IF(SUM(mayo)>=1,1,0) AS mayo"),
			DB::raw("IF(SUM(junio)>=1,1,0) AS junio"),
			DB::raw("IF(SUM(julio)>=1,1,0) AS julio"),
			DB::raw("IF(SUM(agosto)>=1,1,0) AS agosto"),
			DB::raw("IF(SUM(septiembre)>=1,1,0) AS septiembre"),
			DB::raw("IF(SUM(octubre)>=1,1,0) AS octubre"),
			DB::raw("IF(SUM(noviembre)>=1,1,0) AS noviembre"),
			DB::raw("IF(SUM(diciembre)>=1,1,0) AS diciembre")
		)
		->where([
			'finalidad'=> $obj->finalidad,
			'funcion'=> $obj->funcion,
			'subfuncion'=> $obj->subfuncion,
			'eje'=> $obj->eje,
			'linea_accion'=> $obj->linea_accion,
			'programa_sectorial'=> $obj->programa_sectorial,
			'tipologia_conac'=> $obj->tipologia_conac,
			'programa_presupuestario'=> $obj->programa_presupuestario,
			'subprograma_presupuestario'=> $obj->subprograma_presupuestario,
			'proyecto_presupuestario'=> $obj->proyecto_presupuestario,
			'upp'=> $obj->clv_upp,
			'ur'=> $obj->clv_ur,
			'fondo_ramo'=>$obj->clv_fondo,
			'ejercicio'=>$obj->ejercicio,
			'deleted_at'=>null,
			])
		->groupByRaw('ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario');
		if (Auth::user()->id_grupo == 5) {
		$meses = $meses->where('tipo', 'RH');
		}
		$meses=$meses->get();
		return $meses;

	}


}