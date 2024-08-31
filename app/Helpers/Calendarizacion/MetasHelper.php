<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Auth;
use Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MmlActividades;
use App\Models\calendarizacion\Metas;
use App\Http\Controllers\Calendarizacion\MetasController;


class MetasHelper
{

	public static function actividades($upp, $ur, $anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.clv_ur',
					'mml_mir.clv_pp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.objetivo as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp)
				->orderByRaw('upp,clv_ur,clv_pp');
			if ($ur != 0) {
				$proyecto = $proyecto->where('mml_mir.clv_ur', $ur);
			}
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp AS upp',
					'clv_ur',
					'clv_pp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(mml_actividades.nombre,catalogo.descripcion) AS actividad"),
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)

				->where('mml_actividades.ejercicio', $anio)
				->orderByRaw('upp,clv_ur,clv_pp');
			if ($ur != 0) {
				$actv = $actv->where('mml_actividades.clv_ur', $ur);
			}

			$query2 = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($actv, 'act', function ($join) {
					$join->on('metas.actividad_id', '=', 'act.id');
				})
				->select(
					'metas.id',
					'act.upp',
					'act.clv_ur',
					'act.clv_pp',
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
				->where('act.upp', $upp)
				->where('metas.ejercicio', $anio);
			if ($ur != 0) {
				$query2 = $query2->where('act.clv_ur', $ur);
			}
			$query = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.mir_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'pro.upp',
					'pro.clv_ur',
					'pro.clv_pp',
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
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
				->unionAll($query2)
				->orderByRaw('upp,clv_ur,clv_pp');
			if ($ur != 0) {
				$query = $query->where('pro.clv_ur', $ur);
			}
			$query = $query->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function actividadesUpp($upp, $anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.objetivo as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(mml_actividades.nombre,catalogo.descripcion) AS actividad"),
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.ejercicio', $anio);
			$query2 = DB::table('metas')
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
				->where('act.clv_upp', $upp)
				->where('metas.ejercicio', $anio);
			$query = DB::table('metas')
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
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
				->unionAll($query2)
				->orderBy('id');
				if(Auth::user()->id_grupo == 4 ){
					$upp= DB::table('uppautorizadascpnomina')->select('clv_upp')->where('uppautorizadascpnomina.deleted_at', null)->get();
					$upps = [];
					foreach ($upp as $key) {
						$upps[]=$key->clv_upp;
					}

				$query = $query->where('pro.upp','!=' ,$upps);
				}
				$query=$query->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function beneficiarios()
	{
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

	public static function unidadMedida()
	{
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

	public static function tCalendario($upp)
	{
		$tpc = DB::table('tipo_actividad_upp')
			->select(
				'Acumulativa',
				'Continua',
				'Especial'
			)->where("clv_upp",$upp)
			->where("deleted_at",null)->get();

			$tipo = [];
			foreach ($tpc as $key) {
				if($key->Acumulativa == 1){
					$tipo[] = ['0', 'Acumulativa'];
				}
				if($key->Continua == 1){
					$tipo[] = ['1', 'Continua'];
				}
				if($key->Especial == 1){
					$tipo[] = ['2', 'Especial'];
				}
			}
		return $tipo;
	}
	public static function actividadesConf($upp, $anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.objetivo as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(nombre,IFNULL(catalogo.descripcion,nombre)) AS actividad"),
					'catalogo.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.ejercicio', $anio)
				->where('catalogo.ejercicio', $anio);
				$upps= DB::table('uppautorizadascpnomina')
				->select('uppautorizadascpnomina.clv_upp')
				->where('uppautorizadascpnomina.clv_upp', $upp)
				->where('uppautorizadascpnomina.deleted_at', null)
				->get();
				if(count($upps)) {
				$actv = $actv->where('mml_actividades.id_catalogo', '!=',2367);
				}
			$query2 = DB::table('metas')
				->leftJoin('catalogo AS cat', 'cat.clave', '=', 'metas.clv_fondo')
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
				)->where([
						'metas.mir_id' => null,
						'metas.deleted_at' => null,
						'act.clv_upp' => $upp,
						'metas.ejercicio'=>$anio,
						'cat.grupo_id'=>'FONDO DEL RAMO'
					]);
			$query = DB::table('metas')
			->leftJoin('catalogo AS cat', 'cat.clave', '=', 'metas.clv_fondo')
				/* ->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo') */
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
				->where([
					'metas.estatus' => 1,
					'metas.actividad_id' => null,
					'metas.deleted_at'=>null,
					'pro.upp' => $upp,
					'pro.ejercicio'=>$anio,
					'cat.grupo_id'=>'FONDO DEL RAMO'
				])
				->unionAll($query2);
			$query=$query->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}

	public static function MetasIndex($upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$claves = DB::table('catalogo')
			->select('clave AS sub')
			->where('deleted_at', null)
			->where('grupo_id', 20)
			->get();
		$c = [];
		foreach ($claves as $key) {
			$c[] = $key->sub;
		}
		MetasHelper::llenadoTemp($upp,$anio);
		$mirdatos = DB::table('mirtemp')
			->leftJoin('pptemp', 'pptemp.clave', '=', 'mirtemp.clave')
			->select(
				'mirtemp.clv_upp',
				'mirtemp.clv_ur',
				'mirtemp.clv_pp',
				'mirtemp.entidad_ejecutora',
				'mirtemp.area_funcional',
				DB::raw('"N/A" AS clv_actadmon'),
				DB::raw('mirtemp.mir_id AS mir_act'),
				DB::raw('mirtemp.objetivo AS actividad'),
				'pptemp.fondo',
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
				'diciembre'
			)->where(function ($query) use ($c) {
				foreach ($c as $sub) {
					$query->where('pptemp.sub_pp', '!=', $sub);
				}
			});

		$dataCat = DB::table('pptemp')
			->leftJoin('catalogo', 'catalogo.clave', '=', 'pptemp.sub_pp')
			->select(
				'pptemp.clv_upp',
				'pptemp.clv_ur',
				'pptemp.clv_pp',
				'pptemp.entidad_ejecutora',
				'pptemp.area_funcional',
				DB::raw('IFNULL(catalogo.id,"N/A") AS clv_actadmon'),
				DB::raw('"N/A" AS mir_act'),
				DB::raw('IFNULL(catalogo.descripcion," ") AS actividad'),
				'pptemp.fondo',
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
				'diciembre'

			)
			->where('catalogo.grupo_id', 20)
			->where('catalogo.deleted_at', null)
			->where('catalogo.ejercicio', $anio)
			->whereIn('pptemp.sub_pp', $c);
		$data = DB::table('pptemp')
			->select(
				'pptemp.clv_upp',
				'pptemp.clv_ur',
				'pptemp.clv_pp',
				'pptemp.entidad_ejecutora',
				'pptemp.area_funcional',
				DB::raw('"OT" AS clv_actadmon'),
				DB::raw('"N/A" AS mir_act'),
				DB::raw('"" AS actividad'),
				'pptemp.fondo',
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
				'diciembre'

			)
			->where(function ($query) use ($c) {
				foreach ($c as $sub) {
					$query->where('pptemp.sub_pp', '!=', $sub);
				}
			})
			->where('pptemp.clv_upp', $upp)
		/* 	->where(function ($query) use ($dataMir) {
				foreach ($dataMir as $sub) {
					$query->where('pptemp.area_funcional', '!=', $sub->area_funcional);
				}
			}) */
			->unionAll($dataCat)
			->unionAll($mirdatos)
			->orderByRaw('clv_upp,clv_ur,clv_pp');
	
		$data = $data->get();
		return $data;
	}

	public static function MetasIndexDel()
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$data = DB::table('programacion_presupuesto')
			->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
			->select(
				'upp AS clv_upp',
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
				DB::raw('IFNULL(catalogo.id,"N/A") AS clv_actadmon'),
				DB::raw('"N/A"AS mir_act'),
				DB::raw('IFNULL(catalogo.descripcion," ") AS actividad'),
				DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
			)
			->where('programacion_presupuesto.tipo', 'RH')
			->where('catalogo.grupo_id', 20)
			->where('programacion_presupuesto.estado', 1)
			->where('programacion_presupuesto.deleted_at', null)
			->where('catalogo.deleted_at', null)
			->where('catalogo.ejercicio', $anio)
			->groupByRaw('ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
			->orderByRaw('upp,ur,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
			->distinct()->get();
		return $data;
	}
	public static function actividadesMeses($upp, $anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.ejercicio', $anio);
				$upps= DB::table('uppautorizadascpnomina')
				->select('uppautorizadascpnomina.clv_upp')
				->where('uppautorizadascpnomina.clv_upp', $upp)
				->where('uppautorizadascpnomina.deleted_at', null)
				->get();
				if(count($upps)) {
				$actv = $actv->where('catalogo.clave', '!=','UUU' );
				}
			$query2 = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($actv, 'act', function ($join) {
					$join->on('metas.actividad_id', '=', 'act.id');
				})
				->select(
					'metas.id',
					'act.entidad',
					'act.area',
					'metas.clv_fondo as fondo',
					DB::raw('IF(enero>=1,1,0) AS enero'),
					DB::raw('IF(febrero>=1,1,0) AS febrero'),
					DB::raw('IF(marzo>=1,1,0) AS marzo'),
					DB::raw('IF(abril>=1,1,0) AS abril'),
					DB::raw('IF(mayo>=1,1,0) AS mayo '),
					DB::raw('IF(junio>=1,1,0) AS junio'),
					DB::raw('IF(julio>=1,1,0) AS julio'),
					DB::raw('IF(agosto>=1,1,0) AS agosto'),
					DB::raw('IF(septiembre>=1,1,0) AS septiembre '),
					DB::raw('IF(octubre>=1,1,0) AS octubre'),
					DB::raw('IF(noviembre>=1,1,0) AS noviembre'),
					DB::raw('IF(diciembre>=1,1,0) AS diciembre')

				)
				->where('metas.mir_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('act.clv_upp', $upp)
				->where('metas.ejercicio', $anio);
			$query = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.mir_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'pro.entidad',
					'pro.area',
					'metas.clv_fondo as fondo',
					DB::raw('IF(enero>=1,1,0) AS enero'),
					DB::raw('IF(febrero>=1,1,0) AS febrero'),
					DB::raw('IF(marzo>=1,1,0) AS marzo'),
					DB::raw('IF(abril>=1,1,0) AS abril'),
					DB::raw('IF(mayo>=1,1,0) AS mayo '),
					DB::raw('IF(junio>=1,1,0) AS junio'),
					DB::raw('IF(julio>=1,1,0) AS julio'),
					DB::raw('IF(agosto>=1,1,0) AS agosto'),
					DB::raw('IF(septiembre>=1,1,0) AS septiembre '),
					DB::raw('IF(octubre>=1,1,0) AS octubre'),
					DB::raw('IF(noviembre>=1,1,0) AS noviembre'),
					DB::raw('IF(diciembre>=1,1,0) AS diciembre')
				)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
				->unionAll($query2)
				->orderBy('id')
				->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function actividadesMesesTotal($anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.ejercicio', $anio);
				$upps= DB::table('uppautorizadascpnomina')
				->select('uppautorizadascpnomina.clv_upp')
				->where('uppautorizadascpnomina.deleted_at', null)
				->get();
				if(count($upps)) {
				$actv = $actv->where('catalogo.clave', '!=','UUU' );
				}
			$query2 = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($actv, 'act', function ($join) {
					$join->on('metas.actividad_id', '=', 'act.id');
				})
				->select(
					'metas.id',
					'act.entidad',
					'act.area',
					'metas.clv_fondo as fondo',
					DB::raw('IF(enero>=1,1,0) AS enero'),
					DB::raw('IF(febrero>=1,1,0) AS febrero'),
					DB::raw('IF(marzo>=1,1,0) AS marzo'),
					DB::raw('IF(abril>=1,1,0) AS abril'),
					DB::raw('IF(mayo>=1,1,0) AS mayo '),
					DB::raw('IF(junio>=1,1,0) AS junio'),
					DB::raw('IF(julio>=1,1,0) AS julio'),
					DB::raw('IF(agosto>=1,1,0) AS agosto'),
					DB::raw('IF(septiembre>=1,1,0) AS septiembre '),
					DB::raw('IF(octubre>=1,1,0) AS octubre'),
					DB::raw('IF(noviembre>=1,1,0) AS noviembre'),
					DB::raw('IF(diciembre>=1,1,0) AS diciembre')

				)
				->where('metas.mir_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('metas.ejercicio', $anio);
			$query = DB::table('metas')
				->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
				->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.mir_id', '=', 'pro.id');
				})
				->select(
					'metas.id',
					'pro.entidad',
					'pro.area',
					'metas.clv_fondo as fondo',
					DB::raw('IF(enero>=1,1,0) AS enero'),
					DB::raw('IF(febrero>=1,1,0) AS febrero'),
					DB::raw('IF(marzo>=1,1,0) AS marzo'),
					DB::raw('IF(abril>=1,1,0) AS abril'),
					DB::raw('IF(mayo>=1,1,0) AS mayo '),
					DB::raw('IF(junio>=1,1,0) AS junio'),
					DB::raw('IF(julio>=1,1,0) AS julio'),
					DB::raw('IF(agosto>=1,1,0) AS agosto'),
					DB::raw('IF(septiembre>=1,1,0) AS septiembre '),
					DB::raw('IF(octubre>=1,1,0) AS octubre'),
					DB::raw('IF(noviembre>=1,1,0) AS noviembre'),
					DB::raw('IF(diciembre>=1,1,0) AS diciembre')
				)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->unionAll($query2)
				->orderBy('id')
				->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function clavesPpMeses($upp, $anio)
	{
		$meses = DB::table('programacion_presupuesto')
			->select(
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area'),
				'fondo_ramo as fondo',
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
			->where('programacion_presupuesto.upp', $upp)
			->where('ejercicio', $anio)
			->where('programacion_presupuesto.deleted_at', null)
			->groupByRaw('programacion_presupuesto.ur,finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario');
			if (Auth::user()->id_grupo == 5) {
			$meses = $meses->where('programacion_presupuesto.tipo', 'RH');
			}
			$meses=$meses->get();
		return $meses;
	}
	public static function clavesPpMesesTotal($anio)
	{
		$meses = DB::table('programacion_presupuesto')
			->select(
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area'),
				'fondo_ramo as fondo',
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
			->where('ejercicio', $anio)
			->where('programacion_presupuesto.deleted_at', null)
			->groupByRaw('fondo_ramo,upp,ur,finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario');
			if (Auth::user()->id_grupo == 5) {
			$meses = $meses->where('programacion_presupuesto.tipo', 'RH');
			}
			$meses=$meses->get();
		return $meses;
	}
	public static function validateMesesfinalTotal($anio)
	{
		$metas = MetasHelper::actividadesMesesTotal($anio);
		Log::debug("METAS".count($metas));
		$claves = MetasHelper::clavesPpMesesTotal($anio);
		Log::debug("claves".count($claves));
		$aux = 0;
		$ids = [];
		foreach ($metas as $k) {
			$cont = $aux;
			foreach ($claves as $key) {
				if ($key->entidad == $k->entidad && $key->area == $k->area && $key->fondo == $k->fondo) {
					if ($key->enero != $k->enero) {
						if($k->enero !=0){
							$aux++;
						}
						
					}
					if ($key->febrero != $k->febrero ) {
						if($k->febrero !=0){
							$aux++;
						}
					}

					if ($key->marzo != $k->marzo) {
						if($k->marzo !=0){
							$aux++;
						}
					}

					if ($key->abril != $k->abril) {
						if($k->abril !=0){
							$aux++;
						}
					}

					if ($key->mayo != $k->mayo) {
						if($k->mayo !=0){
							$aux++;
						}
					}

					if ($key->junio != $k->junio) {
						if($k->junio !=0){
							$aux++;
						}
					}

					if ($key->julio != $k->julio) {
						if($k->julio !=0){
							$aux++;
						}
					}

					if ($key->agosto != $k->agosto) {
						if($k->agosto !=0){
							$aux++;
						}
					}

					if ($key->septiembre != $k->septiembre) {
						if($k->septiembre !=0){
							$aux++;
						}
					}

					if ($key->octubre != $k->octubre) {
						if($k->octubre !=0){
							$aux++;
						}
					}

					if ($key->noviembre != $k->noviembre) {
						if($k->noviembre !=0){
							$aux++;
						}
					}

					if ($key->diciembre != $k->diciembre) {
						if($k->diciembre !=0){
							$aux++;
						}
					}
				}
			}
			if ($aux > $cont) {
				$ids[] = ["ID" => $k->id];
			}
		}
		return ["status" => $aux == 0 ? true : false, "ids" => $ids];
	}

	public static function validateMesesfinal($upp, $anio)
	{

		$metas = MetasHelper::actividadesMeses($upp, $anio);
		$claves = MetasHelper::clavesPpMeses($upp, $anio);
		$aux = 0;
		$ids = [];
		foreach ($metas as $k) {
			$cont = $aux;
			foreach ($claves as $key) {
				if ($key->entidad == $k->entidad && $key->area == $k->area && $key->fondo == $k->fondo) {
					if ($key->enero != $k->enero) {
						if($k->enero !=0){
							$aux++;
						}
						
					}
					if ($key->febrero != $k->febrero ) {
						if($k->febrero !=0){
							$aux++;
						}
					}

					if ($key->marzo != $k->marzo) {
						if($k->marzo !=0){
							$aux++;
						}
					}

					if ($key->abril != $k->abril) {
						if($k->abril !=0){
							$aux++;
						}
					}

					if ($key->mayo != $k->mayo) {
						if($k->mayo !=0){
							$aux++;
						}
					}

					if ($key->junio != $k->junio) {
						if($k->junio !=0){
							$aux++;
						}
					}

					if ($key->julio != $k->julio) {
						if($k->julio !=0){
							$aux++;
						}
					}

					if ($key->agosto != $k->agosto) {
						if($k->agosto !=0){
							$aux++;
						}
					}

					if ($key->septiembre != $k->septiembre) {
						if($k->septiembre !=0){
							$aux++;
						}
					}

					if ($key->octubre != $k->octubre) {
						if($k->octubre !=0){
							$aux++;
						}
					}

					if ($key->noviembre != $k->noviembre) {
						if($k->noviembre !=0){
							$aux++;
						}
					}

					if ($key->diciembre != $k->diciembre) {
						if($k->diciembre !=0){
							$aux++;
						}
					}
				}
			}
			if ($aux > $cont) {
				$ids[] = ["ID" => $k->id];
			}
		}
		return ["status" => $aux == 0 ? true : false, "ids" => $ids];
	}
	public static function actividadesDel($upp, $anio)
	{
		try {
			log::debug("upp: ".$upp." anio: ". $anio);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(mml_actividades.nombre,catalogo.descripcion) AS actividad"),
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.id_catalogo', '!=', null)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('catalogo.clave', 'UUU')
				->where('mml_actividades.ejercicio', $anio)
				->where('catalogo.ejercicio', $anio);
			$query = DB::table('metas')
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
				->where('metas.tipo_meta', '=', 'RH')
				->where('metas.deleted_at', '=', null)
				->where('act.clv_upp', $upp)
				->where('metas.ejercicio', $anio)->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function actividadesFinal($upp, $anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.objetivo as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.clv_upp', $upp);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(nombre,IFNULL(catalogo.descripcion,nombre)) AS actividad"),
					'catalogo.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.ejercicio', $anio)
				->where('catalogo.ejercicio', $anio);
				$upps= DB::table('uppautorizadascpnomina')
				->select('uppautorizadascpnomina.clv_upp')
				->where('uppautorizadascpnomina.clv_upp', $upp)
				->where('uppautorizadascpnomina.deleted_at', null)
				->get();
				if(count($upps)) {
				$actv = $actv->where('catalogo.clave', '!=','UUU' );
				}
			$query2 = DB::table('metas')
				->leftJoin('catalogo AS cat', 'cat.clave', '=', 'metas.clv_fondo')
				/* 	->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo') */
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
				)->where([
					'metas.mir_id'=>null,
					'metas.deleted_at'=>null,
					'act.clv_upp'=>$upp,
					'metas.ejercicio'=>$anio,
					'cat.grupo_id' => 'FONDO DEL RAMO'
					]);
				/* ->where('metas.mir_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('act.clv_upp', $upp)
				->where('metas.ejercicio', $anio); */
			$query = DB::table('metas')
			->leftJoin('catalogo AS cat', 'cat.clave', '=', 'metas.clv_fondo')
				/* ->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')  */
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
				->where([
					'metas.estatus'=> 1,
					'metas.actividad_id'=>null,
					'metas.deleted_at'=>null,
					'pro.upp'=>$upp,
					'metas.ejercicio'=>$anio,
					'cat.grupo_id' => 'FONDO DEL RAMO'
					])/* ;
				->where('metas.estatus', '=', 1)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp) */
				->unionAll($query2);
			$query=$query->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function actividadesAdm($anio)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.clv_upp AS upp',
					'mml_mir.entidad_ejecutora AS entidad',
					'mml_mir.area_funcional AS area',
					'mml_mir.ejercicio',
					'mml_mir.objetivo as actividad'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio);
			$actv = DB::table('mml_actividades')
				->leftJoin('catalogo', 'catalogo.id', '=', 'mml_actividades.id_catalogo')
				->select(
					'clv_upp',
					'mml_actividades.id',
					'entidad_ejecutora AS entidad',
					'area_funcional AS area',
					DB::raw("IFNULL(mml_actividades.nombre,catalogo.descripcion) AS actividad"),
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.ejercicio', $anio);
				
			$query2 = DB::table('metas')
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
				->where('metas.ejercicio', $anio);
			$query = DB::table('metas')
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
				->where('pro.ejercicio', $anio)
				->unionAll($query2)
				->orderBy('id')->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
	public static function llenadoTemp($upp, $anio)
	{
		$data2 = DB::table('programacion_presupuesto AS pp')
			->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'pp.upp')
			->leftJoin('v_epp', 'v_epp.clv_upp', '=', 'pp.upp')
			->select(
				DB::raw('CONCAT(pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario,pp.upp,pp.subsecretaria,pp.ur) AS clave'),
				'pp.upp AS clv_upp',
				'pp.ur AS clv_ur',
				'pp.programa_presupuestario AS clv_pp',
				DB::raw('CONCAT(pp.upp,pp.subsecretaria,pp.ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario) AS area_funcional'),
				DB::raw('pp.fondo_ramo AS fondo'),
				'subprograma_presupuestario AS sub_pp',
				DB::raw("IF(SUM(enero)>=1,1,'0') AS enero"),
				DB::raw("IF(SUM(febrero)>=1,1,'0') AS febrero"),
				DB::raw("IF(SUM(marzo)>=1,1,'0') AS marzo"),
				DB::raw("IF(SUM(abril)>=1,1,'0') AS abril"),
				DB::raw("IF(SUM(mayo)>=1,1,'0') AS mayo"),
				DB::raw("IF(SUM(junio)>=1,1,'0') AS junio"),
				DB::raw("IF(SUM(julio)>=1,1,'0') AS julio"),
				DB::raw("IF(SUM(agosto)>=1,1,'0') AS agosto"),
				DB::raw("IF(SUM(septiembre)>=1,1,'0') AS septiembre"),
				DB::raw("IF(SUM(octubre)>=1,1,'0') AS octubre"),
				DB::raw("IF(SUM(noviembre)>=1,1,'0') AS noviembre"),
				DB::raw("IF(SUM(diciembre)>=1,1,'0') AS diciembre")
			)
			->where("pp.upp", $upp)
			->where('pp.estado', 1)
			->where('pp.deleted_at', null)
			->where('mml_cierre_ejercicio.deleted_at', null)
			->where('pp.ejercicio', '=', $anio)
			->where('mml_cierre_ejercicio.statusm', 1)
			->where('presupuestable', '=', 1)
			->orderBy('pp.ur')
			->groupByRaw('pp.ur,pp.fondo_ramo,pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario')
			->distinct();
		if (Auth::user()->id_grupo == 4) {
			$data2 = $data2->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'pp.upp')
				->where('pp.tipo', 'Operativo')
				->where('cierre_ejercicio_metas.ejercicio', $anio)
				->where('cierre_ejercicio_metas.estatus', 'Abierto');
		}
		$data2 = $data2->get();
		foreach ($data2 as $key) {
			DB::table('pptemp')->insert(get_object_vars($key));
		}

		$dataMir = DB::table('mml_mir')
			->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'mml_mir.clv_upp')
			->select(
				DB::raw('CONCAT(mml_mir.area_funcional,mml_mir.entidad_ejecutora) AS clave'),
				'mml_mir.clv_upp',
				'mml_mir.clv_ur',
				'mml_mir.clv_pp',
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				DB::raw('mml_mir.id AS mir_id'),
				'objetivo'
			)
			->where('mml_mir.deleted_at', null)
			->where('mml_mir.nivel', 11)
			->where('mml_cierre_ejercicio.ejercicio', $anio)
			->where('mml_cierre_ejercicio.statusm', 1)
			->where('mml_mir.ejercicio', $anio)
			->where('mml_mir.clv_upp', $upp)
			->groupByRaw('mml_mir.id')
			->orderByRaw('mml_mir.clv_upp,mml_mir.clv_ur');
		if (Auth::user()->id_grupo == 4) {
			$dataMir = $dataMir->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'mml_mir.clv_upp')
				->where('cierre_ejercicio_metas.ejercicio', $anio)
				->where('cierre_ejercicio_metas.estatus', 'Abierto');
		}
		$dataMir = $dataMir->get();
		foreach ($dataMir as $key) {
			DB::table('mirtemp')->insert(get_object_vars($key));
		}
	}
	public static function isExistMmir($entidad_ejecutora, $area_funcional, $fondo, $actividad, $anio)
	{
		$metaexist = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				'mml_mir.clv_upp',

			)
			->where('mml_mir.entidad_ejecutora', $entidad_ejecutora)
			->where('mml_mir.area_funcional', $area_funcional)
			->where('metas.clv_fondo', $fondo)
			->where('metas.mir_id', intval($actividad))
			->where('metas.ejercicio', $anio)
			->where('metas.deleted_at', null)->get();
		$res = count($metaexist) ? true : false;
		return $res;
	}
	public static function isExistMoT($entidad_ejecutora, $area_funcional, $fondo, $anio)
	{
		$metaOt = DB::table('metas')
			->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
			->select(
				'metas.id',
				'mml_actividades.entidad_ejecutora',
				'mml_actividades.area_funcional',
				'mml_actividades.clv_upp',
				'mml_actividades.id'

			)
			->where('mml_actividades.entidad_ejecutora', $entidad_ejecutora)
			->where('mml_actividades.area_funcional', $area_funcional)
			->where('metas.ejercicio', $anio)
			->where('metas.clv_fondo', $fondo)
			->where('mml_actividades.id_catalogo', null)
			->where('metas.mir_id', null)
			->where('mml_actividades.deleted_at', null)
			->where('metas.deleted_at', null)->get();
		$res = count($metaOt) ? true : false;
		return $res;
	}
	public static function isExistCat($entidad_ejecutora, $area_funcional, $fondo, $actividad, $anio)
	{
		$metaCat = DB::table('metas')
			->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
			->select(
				'metas.id',
				'mml_actividades.entidad_ejecutora',
				'mml_actividades.area_funcional',
				'mml_actividades.clv_upp'
			)
			->where('mml_actividades.entidad_ejecutora', $entidad_ejecutora)
			->where('mml_actividades.area_funcional', $area_funcional)
			->where('metas.clv_fondo', $fondo)
			->where('mml_actividades.id_catalogo', $actividad)
			->where('metas.ejercicio', $anio)
			->where('metas.mir_id', null)
			->where('mml_actividades.deleted_at', null)
			->where('metas.deleted_at', null)->get();
		$res = count($metaCat) ? true : false;
		return $res;
	}
	public static function createMml_Ac($upp,$entidad_ejecutora, $area_funcional,$actividad, $nombre, $anio)
	{
		$ur = str_split($entidad_ejecutora);
		$pp = str_split($area_funcional);
		$mml_act = new MmlActividades();
		$mml_act->clv_upp =$upp;
		$mml_act->clv_ur =''.$ur[4].$ur[5].'';
		$mml_act->clv_pp =''.$pp[8].$pp[9].'';
		$mml_act->entidad_ejecutora = $entidad_ejecutora;
		$mml_act->area_funcional = $area_funcional;
		$mml_act->id_catalogo = $actividad=='ot'? null:$actividad;
		$mml_act->nombre =$actividad=='ot'? $nombre:null;
		$mml_act->ejercicio = $anio;
		$mml_act->created_user = Auth::user()->username;
		$mml_act->save();
		return $mml_act->id;
	}
	public static function createMeta($request,$actividad,$fondo,$act,$meses,$anio,$flagSubPp)
	{
		try {
			$confirm = MetasController::cmetasUpp($request->upp, $anio);
			$clv = explode('/', $request->area);
			$pp = explode('-', $clv[0]);
			$meta = new Metas();
			$meta->mir_id = $request->tipoAct == 'M'?$actividad:null;
			$meta->actividad_id = $request->tipoAct !='M'?$act:null;
			$meta->clv_fondo = $fondo;
			$meta->tipo = $request->tipo_Ac;
			$meta->beneficiario_id =  intval($request->tipo_Be);
			$meta->unidad_medida_id = intval($request->medida);
			$meta->cantidad_beneficiarios =  intval($request->beneficiario);
			$meta->ejercicio = $anio;
			$meta->created_user =Auth::user()->username;
			$meta->enero = $flagSubPp ==1 ? $meses["enero"] : "2";
			$meta->febrero = $flagSubPp ==1 ? $meses["febrero"] : "2";
			$meta->marzo = $flagSubPp ==1 ? $meses["marzo"] : "2";
			$meta->abril = $flagSubPp ==1 ? $meses["abril"] : "2";
			$meta->mayo = $flagSubPp ==1 ? $meses["mayo"] : "2";
			$meta->junio = $flagSubPp ==1 ? $meses["junio"] : "2";
			$meta->julio = $flagSubPp ==1 ? $meses["julio"] : "2";
			$meta->agosto = $flagSubPp ==1 ? $meses["agosto"] : "2";
			$meta->septiembre = $flagSubPp ==1 ? $meses["septiembre"] : "2";
			$meta->octubre = $flagSubPp ==1 ? $meses["octubre"] : "2";
			$meta->noviembre = $flagSubPp ==1 ? $meses["noviembre"] : "2";
			$meta->diciembre = $flagSubPp ==1 ? $meses["diciembre"] : "3";
			$meta->total = $flagSubPp ==1 ? $request->sumMetas : "25";
			$meta->tipo_meta = 'Operativo';
			/* PROGRAMA:7 SUBPRO:8 PROYECTO:9 */
			$meta->save();
			$meta->clv_actividad = "" . $request->upp . "-" . $pp[9] . "-" . $meta->id . "-" . $anio;
			if (!$confirm["status"] & Auth::user()->id_grupo == 1) {
				$meta->estatus = 1;
			}
			$meta->save();
			return $meta;
		} catch (\Throwable $th) {
			throw $th;
		}
	
	}

	public static function existMeta($area, $entidad,$anio,$fondo)
	{
		try {
			$proyecto = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.ejercicio',
					'mml_mir.entidad_ejecutora',
					'mml_mir.area_funcional'
				)
				->where('mml_mir.deleted_at', '=', null)
				->where('mml_mir.nivel', '=', 11)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.entidad_ejecutora', $entidad)
				->where('mml_mir.area_funcional', $area);
			$actv = DB::table('mml_actividades')
				->select(
					'mml_actividades.id',
					'mml_actividades.ejercicio',
					'mml_actividades.entidad_ejecutora',
					'mml_actividades.area_funcional'
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('mml_actividades.entidad_ejecutora', $entidad)
				->where('mml_actividades.area_funcional', $area)
				->where('mml_actividades.ejercicio', $anio);
		
			$query2 = DB::table('metas')
				->leftJoinSub($actv, 'act', function ($join) {
					$join->on('metas.actividad_id', '=', 'act.id');
				})
				->select(
					'metas.clv_fondo  as fondo',
				)
				->where('metas.mir_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('metas.ejercicio', $anio)
				->where('act.entidad_ejecutora', $entidad)
				->where('act.area_funcional', $area)
				->where('metas.clv_fondo', $fondo)
				->groupByRaw('metas.clv_fondo,act.entidad_ejecutora,act.area_funcional')
				->distinct();
			$query = DB::table('metas')
				->leftJoinSub($proyecto, 'pro', function ($join) {
					$join->on('metas.mir_id', '=', 'pro.id');
				})
				->select(
					'metas.clv_fondo  as fondo',
				)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.entidad_ejecutora', $entidad)
				->where('pro.area_funcional', $area)
				->where('metas.clv_fondo', $fondo)
				->unionAll($query2)
				->groupByRaw('metas.clv_fondo,pro.entidad_ejecutora,pro.area_funcional')
				->distinct()
				->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}

	public static function fondos($area, $entidad,$anio)
	{
		$areaAux = str_split($area);
		$entidadAux = str_split($entidad);
		$fondos = DB::table('programacion_presupuesto')
		->select(
			'programacion_presupuesto.fondo_ramo as fondo',
		)
		->where('programacion_presupuesto.deleted_at', null)
		->where('programacion_presupuesto.finalidad', intval($areaAux[0]))
		->where('programacion_presupuesto.funcion', intval($areaAux[1]))
		->where('programacion_presupuesto.subfuncion', intval($areaAux[2]))
		->where('programacion_presupuesto.eje', intval($areaAux[3]))
		->where('programacion_presupuesto.linea_accion', strval($areaAux[4]. $areaAux[5]))
		->where('programacion_presupuesto.programa_sectorial', $areaAux[6])
		->where('programacion_presupuesto.tipologia_conac', $areaAux[7])
		->where('programacion_presupuesto.upp', strval($entidadAux[0].$entidadAux[1].$entidadAux[2]))
		->where('programacion_presupuesto.ur', strval($entidadAux[4].$entidadAux[5]))
		->where('programa_presupuestario', strval($areaAux[8].$areaAux[9]))
		->where('subprograma_presupuestario',  strval($areaAux[10].$areaAux[11].$areaAux[12]))
		->where('proyecto_presupuestario',  strval($areaAux[13].$areaAux[14].$areaAux[15]))
		->groupByRaw('programacion_presupuesto.fondo_ramo')
		->where('programacion_presupuesto.ejercicio',$anio)
		->get();
		$fond = new \stdClass;
		$str = '';
		$arr = [];
		foreach ($fondos as $key) {
			$str =$str.' '.$key->fondo;
			$arr[]=$key->fondo;
		}
		$fond->fondoStr=$str;
		$fond->fondoArr=$arr;
		return $fond;
	}

}