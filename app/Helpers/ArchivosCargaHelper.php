<?php // Code within app\Helpers\ArchivosCargaHelper.php

namespace App\Helpers;

use Auth;
use Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MmlMir;
use App\Models\calendarizacion\Metas;
use App\Http\Controllers\Calendarizacion\MetasController;


class ArchivosCargaHelper
{

	public static function getDataAreasFuncionales(){

		try {
			$areasFun = [];
			$areas = DB::table('epp')
			->SELECT('epp.ejercicio',
			(DB::raw('CONCAT(c09.clave,c10.clave,c11.clave,c12.clave,c13.clave,c14.clave,c15.clave,c16.clave,c17.clave,c18.clave) area_funcional')),
			(DB::raw("CONCAT((epp.ejercicio-2000),c06.clave,' ',c18.descripcion) col_3"))
			)
			->leftJoin('catalogo as c06', 'epp.upp_id', '=', 'c06.id')  
			->leftJoin('catalogo as c09', 'epp.finalidad_id', '=', 'c09.id')  
			->leftJoin('catalogo as c10', 'epp.funcion_id', '=', 'c10.id')  
			->leftJoin('catalogo as c11', 'epp.subfuncion_id', '=', 'c11.id') 
			->leftJoin('catalogo as c12', 'epp.eje_id', '=', 'c12.id')  
			->leftJoin('catalogo as c13', 'epp.linea_accion_id', '=', 'c13.id')  
			->leftJoin('catalogo as c14', 'epp.programa_sectorial_id', '=', 'c14.id')  
			->leftJoin('catalogo as c15', 'epp.tipologia_conac_id', '=', 'c15.id')  
			->leftJoin('catalogo as c16', 'epp.programa_id', '=', 'c16.id')  
			->leftJoin('catalogo as c17', 'epp.subprograma_id', '=', 'c17.id')  
			->leftJoin('catalogo as c18', 'epp.proyecto_id', '=', 'c18.id') 
			->where('epp.ejercicio',2024)
			->orderByRaw('epp.upp_id,epp.ur_id')
			->get();

			foreach ($areas as $key => $value) {
				$cadena = $value->col_3;
				$rest = substr($cadena,0, 26);
				$value->col_3 = $rest;
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
	public static function getDataFondos(){
		try {
			$dataSet = [];
			$fondos = DB::table('fondo')
			->SELECT('techos_financieros.ejercicio',
			(DB::raw('CONCAT((techos_financieros.ejercicio - 2000),fondo.clv_etiquetado,fondo.clv_fuente_financiamiento,fondo.clv_ramo,fondo.clv_fondo_ramo,fondo.clv_capital) fondos')),
			'fondo_ramo AS descripcion_corta',
			'fondo_ramo AS descripcion'
			)
			->leftJoin('techos_financieros', 'fondo.clv_fondo_ramo', '=', 'techos_financieros.clv_fondo')  
			->DISTINCT()
			->where('techos_financieros.ejercicio',2024)
			->orderBy('descripcion')
			->get();
	
			foreach ($fondos as $key => $value) {
				$desCorta = $value->descripcion_corta;
				$rest = substr($desCorta,0, 22);
				$descripcion = $value->descripcion;
				$descLarga = substr($descripcion,0 ,43);
	
				array_push($dataSet, ['ejercicio'=>$value->ejercicio,
										'fondo'=>$value->fondos,
										'descripcionCorta'=>$rest,
										'descripcionLarga'=>$descLarga]);
	
			}
			return $dataSet;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	public static function getDataCostoBeneficio(){
		try {
			$dataSet = [];
			$costoBen = DB::select("SELECT 
			pa.entidad_federativa,pa.region,pa.municipio,pa.localidad,pa.upp,pa.subsecretaria,pa.ur,
			CONCAT((pa.ejercicio-2000),pa.upp) as codigo,
			CONCAT(pa.entidad_federativa,pa.region,pa.municipio,pa.localidad,pa.upp,pa.subsecretaria,pa.ur) as codigo_cege,
			CONCAT((pa.ejercicio-2000),'-',pa.ur,' ',ve.ur) as descripcionUr,
			CONCAT(cg.municipio,' ','-',' ',cg.localidad, ' ','-',' ',ve.ur) as descripcion_mun,
			CONCAT(cg.municipio,' ',ve.ur) as descripcion_explicativa,
			ve.ur as descripcion_breve
			FROM (
				SELECT distinct
					pp.entidad_federativa,pp.region,pp.municipio,pp.localidad,pp.upp,pp.subsecretaria,pp.ur, pp.ejercicio
				FROM `programacion_presupuesto` pp
				WHERE pp.ejercicio = 2024 AND pp.deleted_at is NULL
			) pa
			LEFT JOIN (
				SELECT distinct
					clv_upp,upp,
					clv_ur,ur
				FROM v_epp
				WHERE ejercicio = 2024 AND deleted_at IS null
			) ve ON pa.upp = ve.clv_upp AND pa.ur = ve.clv_ur
			LEFT JOIN (
				SELECT DISTINCT
					clv_region,region,clv_municipio,municipio,clv_localidad,localidad
				FROM clasificacion_geografica 
				WHERE deleted_at IS NULL
			) cg ON pa.region = cg.clv_region AND pa.municipio = cg.clv_municipio AND pa.localidad = cg.clv_localidad;");

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
				$restDescMun = substr($cadenaDes1,0, 43);

				$cadenaDes2 = $value->descripcion_explicativa;
				$restDescExpl = substr($cadenaDes2,0, 22);

				$cadenaDes3 = $value->descripcion_breve;
				$restDescExpl = substr($cadenaDes3,0, 26);
				
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
	public static function getDataCentroGestor(){
		try {
			$dataSet = [];
			$centroGestor = DB::select("WITH aux AS ( SELECT DISTINCT pp.ejercicio, CONCAT(pp.entidad_federativa,region,municipio,localidad,pp.upp,pp.subsecretaria,pp.ur) AS centro_gestor, upp,ur 
			FROM programacion_presupuesto pp WHERE pp.ejercicio = 2024 AND pp.deleted_at IS NULL ORDER BY centro_gestor ) 
			SELECT distinct aux.ejercicio, aux.centro_gestor, ve.ur FROM aux 
			LEFT JOIN v_epp ve ON ve.clv_upp = aux.upp AND ve.clv_ur = aux.ur AND ve.ejercicio = 2024 AND ve.deleted_at IS NULL;");
			
			return $centroGestor;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	public static function getDataPospre(){
		try {
			$dataSet = [];
			$pospre = DB::table('posicion_presupuestaria')
			->SELECT(
			(DB::raw('CONCAT(clv_capitulo,clv_concepto,clv_partida_generica,clv_partida_especifica, clv_tipo_gasto) as pospre')),
			'partida_especifica',
			)->DISTINCT()->where('deleted_at',null)->orderBy('pospre')->get();

			foreach ($pospre as $key => $value) {
				array_push($dataSet, ['ejercicio'=>2024,
										'posicionPre'=>$value->pospre,
										'descripcion'=>$value->partida_especifica,
									]);

			}

			return $dataSet;
		} catch (\Throwable $th) {
			throw $th;
		}
	}
	public static function getDataClavesPresupuestales(){
		try {
			$clave = DB::select("CALL presupuesto_sap(2024)");
        
        	return $clave;
		} catch (\Throwable $th) {
			throw $th;
		}
	}

}