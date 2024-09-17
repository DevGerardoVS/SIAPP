<?php // Code within app\Helpers\ArchivosCargaHelper.php

namespace App\Helpers;

use Auth;
use Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\calendarizacion\Metas;
use App\Http\Controllers\Calendarizacion\MetasController;


class ArchivosCargaHelper
{
	// ya esta actualizada...
	public static function getDataAreasFuncionales($ejercicio){

		try {
			$areasFun = [];
			$areas = DB::table('v_epp as ve')
				->select(
					've.ejercicio',
					(DB::raw('CONCAT(clv_finalidad,clv_funcion,clv_subfuncion,clv_eje,clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,clv_programa,clv_subprograma,clv_proyecto) as area_funcional')),
					(DB::raw("CONCAT((ve.ejercicio-2000),clv_upp,' ',ifnull(c.descripcion_corta, '')) col_3"))
				)
				->join('catalogo as c',function($join) use($ejercicio){
					$join->on('c.clave','=','ve.clv_proyecto')
						->where('c.ejercicio','=',$ejercicio)
						->where('c.grupo_id','=',18)
						->whereNull('c.deleted_at');
				})
				->where('ve.ejercicio',$ejercicio)
				->whereNull('ve.deleted_at')
				->orderByRaw('clv_upp,clv_ur')
				->get();

			foreach ($areas as $key => $value) {
				$cadena = $value->col_3;
				$rest = substr($cadena,0, 26);
				array_push($areasFun, ['ejercicio'=>$value->ejercicio,
										'area_funcional'=>$value->area_funcional,
										'col_3'=>$rest]);

			}
			
		} catch (\Throwable $th) {
			throw $th;
			return ['error'=>400];
		}

		return $areasFun;
	}
	//actualizado...
	public static function getDataFondos($ejercicio){
		try {
			$dataSet = [];
			$fondos = DB::select("WITH fondos as (
							select
								c1.clave clv_etiquetado,c1.descripcion etiquetado,
								c2.clave clv_fuente_financiamiento,c2.descripcion fuente_financiamiento,
								c3.clave clv_ramo,c3.descripcion ramo,
								c4.clave clv_fondo_ramo,c4.descripcion fondo_ramo,c4.descripcion_corta,c5.descripcion_larga descripcion,
								c5.clave clv_capital,c5.descripcion capital
							from fondo f
							join catalogo c1 on f.etiquetado_id = c1.id
							join catalogo c2 on f.fuente_financiamiento_id = c2.id
							join catalogo c3 on f.ramo_id = c3.id
							join catalogo c4 on f.fondo_ramo_id = c4.id
							join catalogo c5 on f.capital_id = c5.id
						)
						select distinct
							tf.ejercicio,
							CONCAT((tf.ejercicio - 2000),f.clv_etiquetado,f.clv_fuente_financiamiento,f.clv_ramo,f.clv_fondo_ramo,f.clv_capital) fondos,
							descripcion_corta,
							descripcion
						from fondos f
						left join techos_financieros tf on f.clv_fondo_ramo = tf.clv_fondo and tf.ejercicio = $ejercicio
						order by descripcion;");
	
			return $fondos;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	// ya esta actualizada...
	public static function getDataCostoBeneficio($ejercicio){
		try {
			$dataSet = [];
			$costoBen = DB::select("SELECT
				pa.entidad_federativa,pa.region,pa.municipio,pa.localidad,pa.upp,pa.subsecretaria,pa.ur,
				CONCAT((pa.ejercicio-2000),pa.upp) as codigo,
				CONCAT(pa.entidad_federativa,pa.region,pa.municipio,pa.localidad,pa.upp,pa.subsecretaria,pa.ur) as codigo_cege,
				CONCAT((pa.ejercicio-2000),'-',pa.ur,' ',ve.ur) as descripcionUr,
				CONCAT(cg.municipio,' ','-',' ',cg.localidad, ' ','-',' ',ve.ur) as descripcion_mun,
				CONCAT(cg.municipio,' ',ve.ur_larga) as descripcion_explicativa,
				ve.ur_corta as descripcion_breve
				from (
					select distinct
						pp.entidad_federativa,pp.region,pp.municipio,pp.localidad,pp.upp,pp.subsecretaria,pp.ur, pp.ejercicio
					from programacion_presupuesto pp
					where pp.ejercicio = $ejercicio and pp.deleted_at is null
				) pa
				left join (
					select distinct
						clv_upp,upp,
						clv_ur,ur,
						c1.descripcion_larga upp_larga,
						c1.descripcion_corta upp_corta,
						c2.descripcion_larga ur_larga,
						c2.descripcion_corta ur_corta
					from v_epp v
						left join catalogo c1 on c1.ejercicio = $ejercicio and c1.deleted_at is null
						and c1.grupo_id = 6 and clv_upp = c1.clave
						left join catalogo c2 on c2.ejercicio = $ejercicio and c2.deleted_at is null
						and c2.grupo_id = 8 and clv_ur = c2.clave and ur = c2.descripcion
						where v.ejercicio = $ejercicio and v.deleted_at is null
				) ve on pa.upp = ve.clv_upp and pa.ur = ve.clv_ur
				left join (
					select distinct
						c1.clave clv_region,c1.descripcion region,
						c2.clave clv_municipio,c2.descripcion municipio,
						c3.clave clv_localidad,c3.descripcion localidad
					from clasificacion_geografica cg
					join catalogo c1 on cg.region_id = c1.id
					join catalogo c2 on cg.municipio_id = c2.id
					join catalogo c3 on cg.localidad_id = c3.id
					where cg.deleted_at is null
					) cg on pa.region = cg.clv_region and pa.municipio = cg.clv_municipio and pa.localidad = cg.clv_localidad order by pa.upp, pa.ur;");

				$contador = 1;
				$codigo = '';
			foreach ($costoBen as $key => $value) {
				if ($contador < 10 ) {
					$codigo = $value->codigo.'0000'.$contador;
				}elseif ($contador >= 10 && $contador <100) {
					$codigo = $value->codigo.'000'.$contador;
				}elseif ($contador >= 100 && $contador < 1000) {
					$codigo = $value->codigo.'00'.$contador;
				}elseif ($contador >= 1000 && $contador < 10000) {
					$codigo = $value->codigo.'0'.$contador;
				}else {
					$codigo = $contador;
				}
				$contador = $contador + 1;
				$cadenaDes1 = $value->descripcion_mun;
				$restDescMun = $cadenaDes1;
				// $restDescMun = substr($cadenaDes1,0, 43);

				$cadenaDes2 = $value->descripcion_explicativa;
				$restDescExpl = substr($cadenaDes2,0, 43);

				$cadenaDes3 = $value->descripcion_breve;
				$restDescExpl = substr($cadenaDes3,0, 22);
				
				array_push($dataSet, [
					'entidad_federativa'=>$value->entidad_federativa,
					'region'=>$value->region,
					'municipio'=>$value->municipio,
					'localidad'=>$value->localidad,
					'secretaria'=>$value->upp,
					'sub_secretaria'=>$value->subsecretaria,
					'direccion'=>$value->ur,
					'codigo_costo'=>$codigo,
					'codigo_cege'=>$value->codigo_cege,
					'descripcio_ur'=>$value->descripcionUr,
					'descripcion_municipio'=>$restDescMun,
					'descripcion_explicativa'=>$restDescExpl ,
					'descripcion'=>$restDescExpl,
				]); 


			}
			
			return $dataSet;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	//actualizada...
	public static function getDataCentroGestor($ejercicio){
		try {
			$dataSet = [];
			$centroGestor = DB::select("WITH aux AS (
				SELECT DISTINCT 
					pp.ejercicio, 
					CONCAT(
						pp.entidad_federativa,region,municipio,localidad,pp.upp,pp.subsecretaria,pp.ur
					) AS centro_gestor, upp,ur
				FROM programacion_presupuesto pp
				WHERE pp.ejercicio = $ejercicio AND pp.deleted_at IS NULL
				ORDER BY upp,ur
			)
			SELECT DISTINCT 
				aux.ejercicio, aux.centro_gestor,
				c.descripcion_corta ur_corta
			FROM aux
			LEFT JOIN v_epp ve ON ve.clv_upp = aux.upp AND ve.clv_ur = aux.ur AND ve.ejercicio = $ejercicio AND ve.deleted_at IS NULL
			LEFT JOIN catalogo c ON c.ejercicio = $ejercicio AND c.deleted_at IS NULL AND c.grupo_id = 8
			AND ve.clv_ur = c.clave AND ve.ur = c.descripcion;");
			
			return $centroGestor;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	//ya se agregaron las descripciones cortas...
	public static function getDataPospre($ejercicio){
		try {
			$dataSet = [];
			$pospre = DB::table('clasificacion_economica as ce')
				->join('catalogo as c1','c1.id','=','ce.capitulo_id')
				->join('catalogo as c2','c2.id','=','ce.concepto_id')
				->join('catalogo as c3','c3.id','=','ce.partida_generica_id')
				->join('catalogo as c4','c4.id','=','ce.partida_especifica_id')
				->join('catalogo as c5','c5.id','=','ce.tipo_gasto_id')
				->select(
					(DB::raw('CONCAT(c1.clave,c2.clave,c3.clave,c4.clave,c5.clave) as pospre')),
					'c4.descripcion_corta as partida_especifica_desc_corta'
				)
				->whereNull('ce.deleted_at')
				->distinct()
				->orderBy('pospre')
				->get();
			foreach ($pospre as $key => $value) {
				array_push($dataSet, ['ejercicio'=>$ejercicio,
										'posicionPre'=>$value->pospre,
										'descripcion'=>$value->partida_especifica_desc_corta,
									]);

			}

			return $dataSet;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	public static function getDataClavesPresupuestales($ejercicio){
		try {
			$clave = DB::select("CALL presupuesto_sap($ejercicio)");
        
        	return $clave;
		} catch (\Throwable $th) {
			throw $th;
		}
	}

}