<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Imports\utils\FunFormatsDel;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MetasExport;
use App\Exports\MetasExportErr;
use App\Exports\Calendarizacion\MetasCargaMDelegacion;
use App\Models\calendarizacion\Metas;
use Auth;
use DB;
use Log;
use App\Helpers\Calendarizacion\MetasHelper;
use Illuminate\Support\Facades\Schema;
use PDF;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Http;
use Storage;
use App\Models\calendarizacion\CierreMetas;


class MetasDelController extends Controller
{
	public function getMetasDelegacion()
	{
		Controller::check_permission('viewGetMetasDel');
		if (Auth::user()->id_grupo == 5) {
			return view('calendarizacion.metasDelegacion.index');
		}else{
			return abort(401);
		}
	}
    public function getProyecto()
	{
		Controller::check_permission('viewGetMetasDel');
		return view('calendarizacion.metasDelegacion.proyecto');
	}
    public static function getActivDelegacion($upp, $anio)
	{
		Controller::check_permission('viewGetMetasDel');
		$query = MetasHelper::actividadesDel($upp, $anio);
		$anioMax = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$dataSet = [];
		foreach ($query as $key) {
            $area = str_split($key->area);
			$entidad = str_split($key->entidad);
			$accion = Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3 ? '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>' : '';
			if ($key->estatus == 1 && Auth::user()->id_grupo == 1) {
				if ($anio == $anioMax) {
					$button = $accion;
				} else {
					$button = '';
				}
			} else {
				if ($key->estatus == 0) {
                        $button = $accion;
				} else {
					$button = '';
				}
			}

			$i = array(
				$key->id,
				$area[0],
				$area[1],
				$area[2],
				$area[3],
				'' . strval($area[4]) . strval($area[5]) . '',
				$area[6],
				$area[7],
				'' . strval($entidad[0]) . strval($entidad[1]) . strval($entidad[2]) . '',
				'' . strval($entidad[4]) . strval($entidad[5]) . '',
				'' . strval($area[8]) . strval($area[9]) . '',
				'' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '',
				'' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
				$key->fondo,
				$key->actividad,
				$key->tipo,
				$key->total,
				$key->cantidad_beneficiarios,
				$key->beneficiario,
				$key->unidad_medida,
				$button
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getPlantillaExcel()
	{
		if (Auth::user()->id_grupo == 5) {
		ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
		Schema::create('pptemp', function (Blueprint $table) {
			$table->temporary();
			$table->increments('id');
			$table->string('clv_upp', 25)->nullable(false);
			$table->string('entidad_ejecutora', 55)->nullable(false);
			$table->string('area_funcional', 55)->nullable(false);
			$table->string('clv_actadmon', 55)->nullable(false);
			$table->string('mir_act', 55)->nullable(false);
			$table->string('actividad', 55)->nullable(false);
			$table->string('fondo', 55)->nullable(false);
			$table->integer('enero')->default(null);
            $table->integer('febrero')->default(null);
            $table->integer('marzo')->default(null);
            $table->integer('abril')->default(null);
            $table->integer('mayo')->default(null);
            $table->integer('junio')->default(null);
            $table->integer('julio')->default(null);
            $table->integer('agosto')->default(null);
            $table->integer('septiembre')->default(null);
            $table->integer('octubre')->default(null);
            $table->integer('noviembre')->default(null);
            $table->integer('diciembre')->default(null);
		});
		//Controller::check_permission('getMetasDelegacion');
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();

		/*Si no coloco estas lineas Falla*/
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar plantilla de metas carga masiva',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return Excel::download(new MetasCargaMDelegacion(), 'CargaMasiva.xlsx');
	} else {
	/* 	$error = array(
			"icon" => 'error',
			"title" => 'Movimiento no autorizado',
			"text" => 'No cuenta con los permisos suficientes para realizar esta acción'
		); */
		return abort(401);
	}
	}
	public function importPlantilla(Request $request)
	{

		Controller::check_assign(1);
		DB::beginTransaction();
		try {
			if (Auth::user()->id_grupo == 5) {
				Schema::create('metas_temp_Nomir', function (Blueprint $table) {
					$table->temporary();
					$table->increments('id');
					$table->string('clave', 25)->nullable(false);
					$table->string('upp', 25)->nullable(false);
					$table->string('ur', 25)->nullable(false);
					$table->string('fila', 10)->nullable(false);
				});
				$assets = $request->file('cmFile');
				if ($xlsx = SimpleXLSX::parse($assets)) {
					$filearray = $xlsx->rows();
					array_shift($filearray);
					$resul = FunFormatsDel::saveImport($filearray);
					if ($resul['icon'] == 'success') {
						DB::commit();
						$b = array(
							"username" => Auth::user()->username,
							"accion" => 'Carga masiva metas',
							"modulo" => 'Metas'
						);
						Controller::bitacora($b);
					}
					return response()->json($resul);
				}
			} else {
				$error = array(
					"icon" => 'error',
					"title" => 'Movimiento no autorizado',
					"text" => 'No cuenta con los permisos suficientes para realizar esta acción'
				);
				return response()->json($error);
			}
		} catch (\Exception $e) {
			DB::rollback();
		}
	}
		public function updateMeta($id)
	{
		$metas = [];
		$m = Metas::where('deleted_at', null)->where('id', $id)->get();
		if ($m[0]->mir_id != null) {
			$metas = DB::table('metas')
				->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
				->select(
					DB::raw('CONCAT(mml_mir.id, " - ", mml_mir.indicador) AS actividad'),
					'mml_mir.area_funcional',
					'mml_mir.entidad_ejecutora',
					'mml_mir.clv_upp',
					'mml_mir.clv_ur',
					'mml_mir.id as mir_id',
					'metas.id',
					'metas.clv_fondo',
					'metas.tipo',
					'metas.beneficiario_id',
					'metas.unidad_medida_id',
					'metas.cantidad_beneficiarios',
					'metas.enero',
					'metas.febrero',
					'metas.marzo',
					'metas.abril',
					'metas.mayo',
					'metas.junio',
					'metas.julio',
					'metas.agosto',
					'metas.septiembre',
					'metas.octubre',
					'metas.noviembre',
					'metas.diciembre',
					'metas.total',
					'mml_mir.ejercicio'

				)
				->where('mml_mir.deleted_at', null)
				->where('metas.deleted_at', null)
				->where('metas.id', $id)->get();
		}
		if($m[0]->actividad_id != null){
			$metas = DB::table('metas')
				->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
				->leftJoin('catalogo', 'catalogo.id', 'mml_actividades.id_catalogo')
				->select(
					DB::raw('CONCAT(mml_actividades.id, " - ", IFNULL(mml_actividades.nombre,catalogo.descripcion)) AS actividad'),
					'mml_actividades.area_funcional',
					'mml_actividades.entidad_ejecutora',
					'mml_actividades.clv_upp',
					DB::raw('"" AS clv_ur'),
					'mml_actividades.id as actividad_id',
					'metas.id',
					'metas.clv_fondo',
					'metas.tipo',
					'metas.beneficiario_id',
					'metas.unidad_medida_id',
					'metas.cantidad_beneficiarios',
					'metas.enero',
					'metas.febrero',
					'metas.marzo',
					'metas.abril',
					'metas.mayo',
					'metas.junio',
					'metas.julio',
					'metas.agosto',
					'metas.septiembre',
					'metas.octubre',
					'metas.noviembre',
					'metas.diciembre',
					'metas.total',
					'metas.ejercicio'

				)
				->where('mml_actividades.deleted_at', null)
				->where('metas.deleted_at', null)
				->where('metas.id', $id)->get();

		}
		$data = [];
		$areaAux = str_split($metas[0]->area_funcional);
		$entiAux = str_split($metas[0]->entidad_ejecutora);
		$area = '' . strval($areaAux[0]) . '-' . strval($areaAux[1]) . '-' . strval($areaAux[2]) . '-' . strval($areaAux[3]) . '-' . strval($areaAux[4]) . strval($areaAux[5]) . '-' . strval($areaAux[6]) . '-' . strval($areaAux[7]) . '-' . strval($areaAux[8]) . strval($areaAux[9]) . "-" . strval($areaAux[10]) . strval($areaAux[11]) . strval($areaAux[12]) . "-" . strval($areaAux[13]) . strval($areaAux[14]) . strval($areaAux[15]) . '';
		$ar = "".$area."$". $metas[0]->clv_upp ."-". strval($entiAux[3]) . "-". strval($entiAux[4]) . strval($entiAux[5]) ."$". $metas[0]->ejercicio;
		$meses = MetasController::meses($area, "" . $metas[0]->clv_upp . "-" . strval($entiAux[3]) . "-" . strval($entiAux[4]) . strval($entiAux[5]) . "", $metas[0]->ejercicio, $metas[0]->clv_fondo);
		foreach ($metas as $key) {
			$area = str_split($key->area_funcional);
			$entidad = str_split($key->entidad_ejecutora);
			$i = array(
				"ar"=>$ar,
				"area" => $key->area_funcional,
				"entidad" => $key->entidad_ejecutora,
				"clv_upp" => $key->clv_upp,
				"clv_ur" => '' . strval($entidad[4]) . strval($entidad[5]) . '',
				"clv_programa" => '' . strval($area[8]) . strval($area[9]) . '',
				"subprograma" => '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '',
				"proyecto" => '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
				"id" => $key->id,
				"actividad" => $key->actividad,
				"clv_fondo" => $key->clv_fondo,
				"tipo" => $key->tipo,
				"beneficiario_id" => $key->beneficiario_id,
				"unidad_medida_id" => $key->unidad_medida_id,
				"cantidad_beneficiarios" => $key->cantidad_beneficiarios,
				"enero" => $key->enero,
				"febrero" => $key->febrero,
				"marzo" => $key->marzo,
				"abril" => $key->abril,
				"mayo" => $key->mayo,
				"junio" => $key->junio,
				"julio" => $key->julio,
				"agosto" => $key->agosto,
				"septiembre" => $key->septiembre,
				"octubre" => $key->octubre,
				"noviembre" => $key->noviembre,
				"diciembre" => $key->diciembre,
				"total" => $key->total,
				"meses" => $meses
			);
			$data[] = $i;
		}

		return $data[0];
	}
	public function putMeta(Request $request)
	{
		$meta = Metas::where('id', $request->id_meta)->firstOrFail();
		$user = Auth::user()->username;
		$fecha = Carbon::now()->toDateTimeString();
		if ($meta) {
			$meta->cantidad_beneficiarios = $request->beneficiario;
			$meta->updated_at = $fecha;
			$meta->updated_user = $user;
			$meta->save();
		}

		if ($meta->wasChanged()) {
			$b = array(
				"username" => $user,
				"accion" => 'Editar meta',
				"modulo" => 'Metas'
			);
			Controller::bitacora($b);
			$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
			return response()->json($res, 200);
		} else {
			$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
			return response()->json($res, 200);
		}

	}
	public static function confirmardel($anio)
	{
		try {
			Controller::check_permission('putMetas');
			$s = MetasDelController::cmetasdel($anio);
			$fecha = Carbon::now()->toDateTimeString();
			$user = Auth::user()->username;
		/* 	$check = MetasHelper::validateMesesfinal($anio);
			if (!$check["status"]) {
				$foot = "<a type='button' class='btn btn-success col-md-5 ml-auto ' href=/actividades/meses/error/$upp/$anio > <i class='fa fa-download' aria-hidden='true'></i>Descargar index</a>";
				$res = ["status" => false, "mensaje" => ["icon" => 'warning', "text" => 'No puedes confirmar las metas, existen diferencias en los meses autorizados por las claves presupuestales', "title" => "Diferencias en las metas" ,"footer"=>$foot]];
				return response()->json($res, 200);

			} */
			if ($s['status']) {
				DB::beginTransaction();
				$m = MetasDelController::metasDelegacionConf($anio);
				$metas=$m->get();
				$i = 0;
				foreach ($metas as $key) {
					
					$meta = Metas::where('id', $key->id)->firstOrFail();
					if ($meta) {
						$meta->estatus = 1;
						$meta->updated_user = $user;
						$meta->updated_at = $fecha;
						$meta->save();
						$i++;
					}
				}
				if (count($metas) == $i && count($metas) >= 1 && $i >= 1) {
					DB::commit();
					$b = array(
						"username" => $user,
						"accion" => 'confirmacion de metas',
						"modulo" => 'Metas'
					);
					Controller::bitacora($b);
					$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
					return response()->json($res, 200);
				} else {
					$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
					return response()->json($res, 200);
				}
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'No puedes confirmar las metas', "title" => "Metas incompletas"]];
				return response()->json($res, 200);
			}
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

	}
	public static function cmetasdel($anio)
	{
		$metas = DB::table('metas')
				->leftJoin('mml_actividades', 'mml_actividades.id', '=', 'metas.actividad_id')
			->select(
				'metas.id',
				'mml_actividades.clv_upp as upp',
				'metas.actividad_id',
				'mml_actividades.entidad_ejecutora AS entidad',
				'mml_actividades.area_funcional AS area',
				'metas.ejercicio',
				'metas.clv_fondo'
			)
			->where('metas.ejercicio', $anio)
			->where('metas.deleted_at', '=', null)
			->where('mml_actividades.deleted_at', '=', null)
			->where('metas.tipo_meta', '=', 'RH')
			->groupByRaw('area_funcional,entidad_ejecutora,metas.clv_fondo')->get();
		$pp = [];

		foreach ($metas as $key) {
			$area = str_split($key->area);
			$entidad = str_split($key->entidad);
			$activs = DB::table('programacion_presupuesto')
				->select(
					'upp AS clv_upp',
					DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
					DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
					'fondo_ramo AS fondo'
				)
				->where('deleted_at', null)
				->where('finalidad', $area[0])
				->where('funcion', $area[1])
				->where('subfuncion', $area[2])
				->where('eje', $area[3])
				->where('linea_accion', '' . strval($area[4]) . strval($area[5]) . '')
				->where('programa_sectorial', $area[6])
				->where('tipologia_conac', $area[7])
				->where('upp', $key->upp)
				->where('ur', '' . strval($entidad[4]) . strval($entidad[5]) . '', )
				->where('programa_presupuestario', '' . strval($area[8]) . strval($area[9]) . '', )
				->where('subprograma_presupuestario', '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '')
				->where('proyecto_presupuestario', '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '')
				->where('programacion_presupuesto.fondo_ramo', '=', $key->clv_fondo)
				->where('programacion_presupuesto.ejercicio', '=', $anio)
				->where('programacion_presupuesto.tipo', '=', 'RH')
				->groupByRaw('ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
				->distinct()
				->get();
			if (count($activs)) {
				$pp[] = json_encode($activs);
			}
		}
		$activsPP = DB::table('programacion_presupuesto')
			->select(
				'upp AS clv_upp',
				'fondo_ramo',
				DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad_ejecutora'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS area_funcional'),
				'fondo_ramo AS fondo',
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,fondo_ramo) AS clave'),

			)
			->where('deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
			->where('programacion_presupuesto.tipo', '=', 'RH')
			->groupByRaw('ur,fondo_ramo ,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,fondo_ramo')
			->distinct()->get();
		Log::debug("metas: ".count($metas));
		Log::debug("acpp: ".count($activsPP));
		Log::debug("pp: ".count($pp));
		if (count($metas) >= 1) {
			if (count($metas) >= count($activsPP) && count($activsPP) == count($pp)) {
				return ["status" => true];

			} else {
				return ["status" => false];
			}
		} else {
			return ["status" => false];
		}
	}
	public static function metasDelegacion($upp,$anio){
		$metas = DB::table('metas')
		->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
		->leftJoin('catalogo', 'catalogo.id', 'mml_actividades.id_catalogo')
		->select(
			'metas.id',
			DB::raw('CONCAT(mml_actividades.id, " - ", IFNULL(mml_actividades.nombre,catalogo.descripcion)) AS actividad'),
			'mml_actividades.area_funcional AS area',
			'mml_actividades.entidad_ejecutora AS entidad',
			'mml_actividades.clv_upp',
			DB::raw('"" AS clv_ur'),
			'mml_actividades.id as actividad_id',
			'metas.ejercicio'

		)
		->where('metas.tipo_meta', '=', 'RH')
		->where('mml_actividades.clv_upp',$upp)
		->where('metas.ejercicio',$anio)
		->where('catalogo.clave', 'UUU')
		->where('mml_actividades.deleted_at', null)
		->where('metas.deleted_at', null);
		return $metas;

	}
	public static function checkConfirmadas($anio){
		$metas = DB::table('metas')
		->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
		->select(
			'metas.id',
			'mml_actividades.area_funcional AS area',
			'mml_actividades.entidad_ejecutora AS entidad',
			'mml_actividades.clv_upp',
			DB::raw('"" AS clv_ur'),
			'mml_actividades.id as actividad_id',
			'metas.ejercicio',
			'metas.estatus'

		)
		->where('metas.estatus',1)
		->where('metas.ejercicio',$anio)
		->where('metas.tipo_meta', '=', 'RH')
		->where('mml_actividades.deleted_at', null)
		->where('metas.deleted_at', null)->get();
		if (count($metas) >= 1) {
			return ["status" => true];
		}else{
			return ["status" => false];
		}


	}public function getUpps()
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		if (auth::user()->id_grupo != 5) {
			$upps = DB::table('v_epp')
				->select(
					'id',
					'clv_upp',
					DB::raw('CONCAT(clv_upp, " - ", upp) AS upp')
				)->distinct()
				->orderBy('clv_upp')
				->groupByRaw('clv_upp')
				->where('ejercicio', $anio)->get();
		}else{
			$upps= DB::table('uppautorizadascpnomina')
			->leftJoin('v_epp', 'v_epp.clv_upp', '=', 'uppautorizadascpnomina.clv_upp')
			->select(
				'uppautorizadascpnomina.clv_upp',
				DB::raw('CONCAT(uppautorizadascpnomina.clv_upp, " - ", upp) AS upp')
				)
				->groupBy('uppautorizadascpnomina.clv_upp')
			->where('uppautorizadascpnomina.deleted_at', null)
			->get();
		}
		return ["upp" => $upps];
	}
	public static function metasDelegacionConf($anio){
		$metas = DB::table('metas')
		->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
		->leftJoin('catalogo', 'catalogo.id', 'mml_actividades.id_catalogo')
		->select(
			'metas.id',
			DB::raw('CONCAT(mml_actividades.id, " - ", IFNULL(mml_actividades.nombre,catalogo.descripcion)) AS actividad'),
			'mml_actividades.area_funcional AS area',
			'mml_actividades.entidad_ejecutora AS entidad',
			'mml_actividades.clv_upp',
			DB::raw('"" AS clv_ur'),
			'mml_actividades.id as actividad_id',
			'metas.ejercicio'

		)
		->where('metas.tipo_meta','RH')
		->where('metas.ejercicio',$anio)
		->where('catalogo.clave', 'UUU')
		->where('mml_actividades.deleted_at', null)
		->where('metas.deleted_at', null);
		return $metas;

	}

	public static function actividadesCargaMasDel($upp,$username,$anio){
		$metas = DB::table('metas')
		->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
		->leftJoin('catalogo', 'catalogo.id', 'mml_actividades.id_catalogo')
		->select(
			'metas.id as idm',
			DB::raw('CONCAT(mml_actividades.id, " - ", IFNULL(mml_actividades.nombre,catalogo.descripcion)) AS actividad'),
			'mml_actividades.area_funcional AS area',
			'mml_actividades.entidad_ejecutora AS entidad',
			'mml_actividades.clv_upp',
			DB::raw('"" AS clv_ur'),
			'mml_actividades.id as actividad_id',
			'metas.ejercicio',
			'metas.estatus'

		)
		->where('metas.ejercicio',$anio)
		->where('mml_actividades.clv_upp',$upp)
		->where('catalogo.clave', 'UUU')
		->where('mml_actividades.deleted_at', null)
		->where('metas.deleted_at', null)
		->where ('metas.created_user',$username)->get();
	
		return $metas;

	}
}