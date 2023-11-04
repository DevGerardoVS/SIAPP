<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Auth;
use Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetasHelper
{

	public static function actividades($upp, $anio)
	{
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
				->orderBy('id')->get();
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
					'mml_mir.indicador as actividad'
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

	public static function tCalendario()
	{

		$tipo = [];
		$tipo[] = ['0', 'Acumulativa'];
		$tipo[] = ['1', 'Continua'];
		$tipo[] = ['2', 'Especial'];
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
					'mml_mir.indicador as actividad'
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
				->where('act.clv_upp', $upp)
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
				->where('metas.estatus', '=', 1)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
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
		$data3 = DB::table('mml_mir')
			->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'mml_mir.clv_upp')
			->leftJoin('programacion_presupuesto AS pp', 'pp.upp', '=', 'mml_mir.clv_upp')
			->select(
				'mml_mir.clv_upp',
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				DB::raw('"N/A" AS clv_actadmon'),
				DB::raw('mml_mir.id AS mir_act'),
				DB::raw('indicador AS actividad'),
				DB::raw('"" AS fondo'),
			)
			->where(function ($query) use ($c) {
				foreach ($c as $sub) {
					$query->where('pp.subprograma_presupuestario', '!=', $sub);
				}
			})
			->where('mml_mir.deleted_at', null)
			->where('pp.deleted_at', null)
			->where('mml_mir.nivel', 11)
			->where('mml_cierre_ejercicio.ejercicio', $anio)
			->where('mml_cierre_ejercicio.statusm', 1)
			->where('pp.estado', 1)
			->where('mml_mir.ejercicio', $anio)
			->where('pp.ejercicio', $anio)
			->where('mml_mir.clv_upp', $upp)
			->where('pp.upp', $upp)
			->groupByRaw('mml_mir.indicador')
			->orderByRaw('mml_mir.clv_upp,mml_mir.clv_ur,mml_mir.area_funcional')
			->distinct();
		if (Auth::user()->id_grupo == 4) {
			$data3 = $data3->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'mml_mir.clv_upp')
				->where('cierre_ejercicio_metas.deleted_at', null)
				->where('cierre_ejercicio_metas.estatus', 'Abierto');
		}
		$data2 = DB::table('programacion_presupuesto AS pp')
			->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'pp.upp')
			->leftJoin('v_epp', 'v_epp.clv_upp', '=', 'pp.upp')
			->select(
				'pp.upp AS clv_upp',
				DB::raw('CONCAT(pp.upp,pp.subsecretaria,pp.ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario) AS area_funcional'),
				DB::raw('"ot" AS clv_actadmon'),
				DB::raw('"N/A" AS mir_act'),
				DB::raw('"" AS actividad'),
				DB::raw('pp.fondo_ramo AS fondo'),
			)
			->where("pp.upp", $upp)
			->where('pp.estado', 1)
			->where('pp.deleted_at', null)
			->where('mml_cierre_ejercicio.deleted_at', null)
			->where('pp.ejercicio', '=', $anio)
			->where('mml_cierre_ejercicio.statusm', 1)
			->where('presupuestable', '=', 1)
			->where(function ($query) use ($c) {
				foreach ($c as $sub) {
					$query->where('pp.subprograma_presupuestario', '!=', $sub);
				}
			})
			->orderBy('pp.ur')
			->groupByRaw('pp.ur,pp.fondo_ramo,pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario')
			->distinct();
		if (Auth::user()->id_grupo == 4) {
			$data2 = $data2->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'pp.upp')
				->where('cierre_ejercicio_metas.ejercicio', $anio)
				->where('cierre_ejercicio_metas.estatus', 'Abierto');
		}
		$data2 = $data2->get();
		foreach ($data2 as $key) {
			DB::table('pptemp')->insert(get_object_vars($key));
		}
		$mirdatos = $data3->get();
		$newdata2 = DB::table('pptemp')
			->select(
				'pptemp.clv_upp',
				'pptemp.entidad_ejecutora',
				'pptemp.area_funcional',
				'pptemp.clv_actadmon',
				'pptemp.mir_act',
				'pptemp.actividad',
				'pptemp.fondo'

			)->where('pptemp.clv_actadmon', 'ot')
			->where(function ($query) use ($mirdatos) {
				foreach ($mirdatos as $sub) {
					$query->where('pptemp.area_funcional', '!=', $sub->area_funcional);
				}
			});
		$data = DB::table('programacion_presupuesto')
			->leftJoin('mml_cierre_ejercicio', 'mml_cierre_ejercicio.clv_upp', '=', 'programacion_presupuesto.upp')
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
			->where("programacion_presupuesto.upp", $upp)
			->where('programacion_presupuesto.estado', 1)
			->where('programacion_presupuesto.deleted_at', null)
			->where('mml_cierre_ejercicio.deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
			->where('catalogo.deleted_at', null)
			->where('catalogo.grupo_id', 20)
			->where('catalogo.ejercicio', $anio)
			->where('mml_cierre_ejercicio.ejercicio', $anio)
			->where('mml_cierre_ejercicio.statusm', 1)
			->groupByRaw('ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
			->orderByRaw('upp,ur,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
			->unionAll($data3)
			->unionAll($newdata2)
			->distinct();

		if (Auth::user()->id_grupo == 4) {
			$data = $data->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
				->where('cierre_ejercicio_metas.deleted_at', null)
				->where('cierre_ejercicio_metas.ejercicio', $anio)
				->where('cierre_ejercicio_metas.estatus', 'Abierto');
		}
		$data = $data->get();
		return $data;
	}

	public static function MetasIndexDel()
	{
		$upp= DB::table('uppautorizadascpnomina')->select('clv_upp')->where('uppautorizadascpnomina.deleted_at', null)->get();
        $upps = [];
        foreach ($upp as $key) {
            $upps[]=$key->clv_upp;
        }
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$data = DB::table('programacion_presupuesto')
			->leftJoin('catalogo', 'catalogo.clave', '=', 'programacion_presupuesto.subprograma_presupuestario')
			->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
			->select(
				'upp AS clv_upp',
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
				DB::raw('IFNULL(catalogo.id,"N/A") AS clv_actadmon'),
				DB::raw('"N/A"AS mir_act'),
				DB::raw('IFNULL(catalogo.descripcion," ") AS actividad'),
				DB::raw('programacion_presupuesto.fondo_ramo AS fondo'),
			)
			->whereIn('programacion_presupuesto.upp',  $upps)
			->where('programacion_presupuesto.subprograma_presupuestario', "UUU")
			->where('catalogo.grupo_id', 20)
			->where('cierre_ejercicio_metas.estatus', 'Abierto')
			->where('programacion_presupuesto.estado', 1)
			->where('cierre_ejercicio_metas.deleted_at', null)
			->where('programacion_presupuesto.deleted_at', null)
			->where('catalogo.deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
			->where('cierre_ejercicio_metas.ejercicio', $anio)
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
			->where('programacion_presupuesto.subprograma_presupuestario', 'UUU')
			->where('programacion_presupuesto.upp', $upp)
			->where('ejercicio', $anio)
			->where('programacion_presupuesto.deleted_at', null)
			->groupByRaw('programacion_presupuesto.ur,finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario');
			if (Auth::user()->id_grupo == 5) {
			$meses = $meses->where('programacion_presupuesto.subprograma_presupuestario', '!=', 'UUU');
			}
			$meses=$meses->get();
		return $meses;
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
						$aux++;
					}
					if ($key->febrero != $k->febrero) {
						$aux++;
					}

					if ($key->marzo != $k->marzo) {
						$aux++;
					}

					if ($key->abril != $k->abril) {
						$aux++;
					}

					if ($key->mayo != $k->mayo) {
						$aux++;
					}

					if ($key->junio != $k->junio) {
						$aux++;
					}

					if ($key->julio != $k->julio) {
						$aux++;
					}

					if ($key->agosto != $k->agosto) {
						$aux++;
					}

					if ($key->septiembre != $k->septiembre) {
						$aux++;
					}

					if ($key->octubre != $k->octubre) {
						$aux++;
					}

					if ($key->noviembre != $k->noviembre) {
						$aux++;
					}

					if ($key->diciembre != $k->diciembre) {
						$aux++;
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
				$p= $actv->get();
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
					'mml_mir.indicador as actividad'
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
				->where('act.clv_upp', $upp)
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
				->where('metas.estatus', '=', 1)
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
				->unionAll($query2);
			$query=$query->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}
}