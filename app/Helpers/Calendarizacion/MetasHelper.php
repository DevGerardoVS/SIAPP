<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Auth;
use Config;
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
					DB::raw("IFNULL(nombre,IFNULL(catalogo.descripcion,nombre)) AS actividad"),
					'mml_actividades.ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('catalogo.ejercicio', $anio)
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
				->where('metas.actividad_id', '=', null)
				->where('metas.deleted_at', '=', null)
				->where('pro.ejercicio', $anio)
				->where('pro.upp', $upp)
				->unionAll($query2)->get();
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
					'ejercicio',
				)
				->where('mml_actividades.deleted_at', '=', null)
				->where('catalogo.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
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
				->unionAll($query2)->get();
			return $query;
		} catch (\Exception $exp) {
			Log::channel('daily')->debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
		}
	}

	public static function MetasIndex(){
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
            ->groupByRaw('mml_mir.indicador')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data3 = $data3->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'mml_mir.clv_upp')
                ->where('mml_mir.clv_upp', $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
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
            ->groupByRaw('pp.ur,pp.fondo_ramo,pp.finalidad,pp.funcion,pp.subfuncion,pp.eje,pp.linea_accion,pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,pp.subprograma_presupuestario,pp.proyecto_presupuestario')
            ->distinct();
        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data2 = $data2->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'pp.upp')
                ->where("pp.upp", $upp)
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
            ->unionAll( $data3 )
            ->unionAll($newdata2)
            ->distinct();

        if (Auth::user()->id_grupo == 4) {
            $upp = Auth::user()->clv_upp;
            $data = $data->leftJoin('cierre_ejercicio_metas', 'cierre_ejercicio_metas.clv_upp', '=', 'programacion_presupuesto.upp')
                ->where("programacion_presupuesto.upp", $upp)
                ->where('cierre_ejercicio_metas.deleted_at', null)
                ->where('cierre_ejercicio_metas.ejercicio', $anio)
                ->where('cierre_ejercicio_metas.estatus', 'Abierto');
        }
        $data = $data->get();
		return $data;
	}
}