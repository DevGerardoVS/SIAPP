<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Imports\utils\FunFormats;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MetasExport;
use App\Exports\MetasExportErr;
use App\Exports\Calendarizacion\MetasCargaM;
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



class MetasController extends Controller
{
	//Consulta Vista Usuarios
	public function getIndex()
	{
		Controller::check_permission('getMetas');
		return view('calendarizacion.metas.index');
	}
	public function getProyecto()
	{
		Controller::check_permission('getMetas');
		return view('calendarizacion.metas.proyecto');
	}
	public static function getActiv($upp, $anio)
	{

		Controller::check_permission('getMetas');
		$query = MetasHelper::actividades($upp, $anio);
		$dataSet = [];
		foreach ($query as $key) {
			$accion = Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3 ? '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>' : '';

			if ($key->estatus == 1 && Auth::user()->id_grupo == 1) {
				$button = $accion;
			} else {
				if ($key->estatus == 0) {
					$button = $accion;
				} else {
					$button = '';
				}
			}
			$area = str_split($key->area);
			$entidad = str_split($key->entidad);
			$i = array(
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
	public function getMetasP($upp_filter,$ur_filter)
	{
		Controller::check_permission('getMetas');
		Log::debug($upp_filter);
		Log::debug($ur_filter);
		$dataSet = [];
		$upp = isset($upp_filter) ? $upp_filter : auth::user()->clv_upp;

		if ($ur_filter != null && $upp != '') {
			$check = $this->checkClosing($upp);
			if ($check['status']) {
				$activs = DB::table("programacion_presupuesto")
					->leftJoin('v_epp', 'v_epp.clv_proyecto', '=', 'programacion_presupuesto.proyecto_presupuestario')
					->select(
						'programacion_presupuesto.finalidad',
						'programacion_presupuesto.funcion',
						'programacion_presupuesto.subfuncion',
						'programacion_presupuesto.eje',
						'programacion_presupuesto.linea_accion AS linea',
						'programacion_presupuesto.programa_sectorial AS programaSec',
						'programacion_presupuesto.tipologia_conac AS tipologia',
						'programacion_presupuesto.id',
						'programa_presupuestario as programa',
						'subprograma_presupuestario as subprograma',
						'proyecto_presupuestario AS  clv_proyecto',
						'programacion_presupuesto.subsecretaria AS subsec',
						DB::raw('CONCAT(proyecto_presupuestario, " - ", v_epp.proyecto) AS proyecto')
					)
					->where('programacion_presupuesto.ur', '=', $ur_filter)
					->where('programacion_presupuesto.upp', '=', $upp)
					->where('programacion_presupuesto.ejercicio', '=', $check['anio'])
					->where('v_epp.ejercicio', '=', $check['anio'])
					->where('v_epp.presupuestable', '=',1)
					->orderBy('programacion_presupuesto.upp')
					->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
					->distinct()
					->get();

				foreach ($activs as $key) {
					$area = '"' . strval($key->finalidad) . '-' . strval($key->funcion) . '-' . strval($key->subfuncion) . '-' . strval($key->eje) . '-' . strval($key->linea) . '-' . strval($key->programaSec) . '-' . strval($key->tipologia) . '-' . strval($key->programa) . '-' . strval($key->subprograma) . '-' . strval($key->clv_proyecto) . '"';
					$entidad = '"' . strval($upp) . '-' . strval($key->subsec) . '-' . strval($ur_filter) . '"';
					$clave = '"' . strval($upp) . strval($key->subsec) . strval($ur_filter) . '-' . strval($key->finalidad) . strval($key->funcion) . strval($key->subfuncion) . strval($key->eje) . strval($key->linea) . strval($key->programaSec) . strval($key->tipologia) . strval($key->programa) . strval($key->subprograma) . strval($key->clv_proyecto) . '"';
					$accion = "<div class'form-check'><input class='form-check-input clave' type='radio' name='clave' id='" . $clave . "' value='" . $clave . "' onchange='dao.getFyA(" . $area . "," . $entidad . ")' ></div>";
					$dataSet[] = [$key->finalidad, $key->funcion, $key->subfuncion, $key->eje, $key->linea, $key->programaSec, $key->tipologia, $key->programa, $key->subprograma, $key->proyecto, $accion];
				}
			}
			return response()->json(["dataSet" => $dataSet], 200);
		}

	}
	public function getUrs($_upp)
	{
		$urs = [];
		$tAct = [];
		if ($_upp != 0) {
			$upp = $_upp != null ? $_upp : auth::user()->clv_upp;
			$check = $this->checkClosing($upp);
			if ($check['status']) {
				$urs = DB::table('v_epp')
					->select(
						'id',
						'clv_ur',
						DB::raw('CONCAT(clv_ur, " - ",ur) AS ur')
					)->distinct()
					->where('deleted_at', null)
					->groupByRaw('clv_ur')
					->orderBy('clv_ur')
					->where('clv_upp', $upp)
					->where('ejercicio', $check['anio'])->get();
				$Act = DB::table('tipo_actividad_upp')
					->select(
						'Continua',
						'Acumulativa',
						'Especial'
					)
					->where('deleted_at', null)
					->orderBy('clv_upp')
					->where('clv_upp', $upp)
					->get();
				$tAct = $Act[0];
			}
		}

		return ["urs" => $urs, "tAct" => $tAct];
	}
	public function getUpps()
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$upps = DB::table('v_epp')
			->select(
				'id',
				'clv_upp',
				DB::raw('CONCAT(clv_upp, " - ", upp) AS upp')
			)->distinct()
			->orderBy('clv_upp')
			->groupByRaw('clv_upp')
			->where('ejercicio', $anio)->get();
		return ["upp" => $upps];
	}
	public function getFyA($area, $entidad)
	{
		$areaAux = explode('-', $area);
		$entidadAux = explode('-', $entidad);
		$check = $this->checkClosing($entidadAux[0]);
		if ($check['status']) {
			$fondos = DB::table('programacion_presupuesto')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', 'programacion_presupuesto.fondo_ramo')
				->select(
					'fondo.id',
					'programacion_presupuesto.fondo_ramo as clave',
					DB::raw('CONCAT(programacion_presupuesto.fondo_ramo, " - ", fondo.ramo) AS ramo')
				)
				->where('fondo.deleted_at', null)
				->where('programacion_presupuesto.finalidad', $areaAux[0])
				->where('programacion_presupuesto.funcion', $areaAux[1])
				->where('programacion_presupuesto.subfuncion', $areaAux[2])
				->where('programacion_presupuesto.eje', $areaAux[3])
				->where('programacion_presupuesto.linea_accion', $areaAux[4])
				->where('programacion_presupuesto.programa_sectorial', $areaAux[5])
				->where('programacion_presupuesto.tipologia_conac', $areaAux[6])
				->where('programacion_presupuesto.upp', $entidadAux[0])
				->where('programacion_presupuesto.ur', $entidadAux[2])
				->where('programa_presupuestario', $areaAux[7])
				->where('subprograma_presupuestario', $areaAux[8])
				->where('proyecto_presupuestario', $areaAux[9])
				->groupByRaw('clave')
				->where('ejercicio', $check['anio'])
				->get();
			$activ = DB::table('mml_mir')
				->select(
					'mml_mir.id',
					'mml_mir.id as clave',
					DB::raw('CONCAT(mml_mir.id, " - ",indicador) AS actividad')
				)
				->where('mml_mir.deleted_at', null)
				->where('mml_mir.nivel', 11)
				->where('mml_mir.area_funcional', str_replace("-", '', $area))
				->where('mml_mir.entidad_ejecutora', str_replace("-", '', $entidad))
				->groupByRaw('clave')->get();

			$meses = MetasController::meses($area, $entidad, $check['anio']);

		}
		return ['fondos' => $fondos, "activids" => $activ, "mese" => $meses /* ,"mir"=>$mir */];
	}
	public static function meses($area, $entidad, $anio)
	{

		$areaAux = explode('-', $area);
		$entidadAux = explode('-', $entidad);

		$meses = DB::table('programacion_presupuesto')
			->select(
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
			->where('programacion_presupuesto.finalidad', $areaAux[0])
			->where('programacion_presupuesto.funcion', $areaAux[1])
			->where('programacion_presupuesto.subfuncion', $areaAux[2])
			->where('programacion_presupuesto.eje', $areaAux[3])
			->where('programacion_presupuesto.linea_accion', $areaAux[4])
			->where('programacion_presupuesto.programa_sectorial', $areaAux[5])
			->where('programacion_presupuesto.tipologia_conac', $areaAux[6])
			->where('programacion_presupuesto.upp', $entidadAux[0])
			->where('programacion_presupuesto.ur', $entidadAux[2])
			->where('programa_presupuestario', $areaAux[7])
			->where('subprograma_presupuestario', $areaAux[8])
			->where('proyecto_presupuestario', $areaAux[9])
			->where('ejercicio', $anio)
			->get();

		$dataSet = count($meses) >= 1 ? $meses[0] : [];
		return $dataSet;
	}
	public function getSelects()
	{
		$uMed = DB::table('unidades_medida')
			->select(
				'id as clave',
				'unidad_medida'
			)
			->where('deleted_at', null)
			->get();

		$bene = DB::table('beneficiarios')
			->select(
				'id',
				'clave',
				'beneficiario'
			)
			->where('deleted_at', null)
			->get();


		return ["unidadM" => $uMed, "beneficiario" => $bene];
	}
	public static function getTcalendar($upp)
	{
		$Act = DB::table('tipo_actividad_upp')
			->select(
				'Continua',
				'Acumulativa',
				'Especial'
			)
			->where('deleted_at', null)
			->orderBy('clv_upp')
			->where('clv_upp', $upp)
			->get();
		$tAct = $Act[0];
		return $tAct;
	}
	public function createMeta(Request $request)
	{
		$username = Auth::user()->username;
		Controller::check_permission('postMetas');

		$metaexist = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				'mml_mir.clv_upp'
			)
			->where('metas.clv_fondo', $request->sel_fondo)
			->where('metas.mir_id', intval($request->sel_actividad))
			->where('mml_mir.deleted_at', null)
			->where('metas.deleted_at', null)->get();
		if (count($metaexist) == 0) {
			$meta = Metas::create([
				'mir_id' => intval($request->sel_actividad),
				'clv_fondo' => $request->sel_fondo,
				'estatus' => 0,
				'tipo' => $request->tipo_Ac,
				'beneficiario_id' => $request->tipo_Be,
				'unidad_medida_id' => intval($request->medida),
				'cantidad_beneficiarios' => $request->beneficiario,
				'total' => $request->sumMetas,
				'enero' => $request[1] != NULL ? $request[1] : 0,
				'febrero' => $request[2] != NULL ? $request[2] : 0,
				'marzo' => $request[3] != NULL ? $request[3] : 0,
				'abril' => $request[4] != NULL ? $request[4] : 0,
				'mayo' => $request[5] != NULL ? $request[5] : 0,
				'junio' => $request[6] != NULL ? $request[6] : 0,
				'julio' => $request[7] != NULL ? $request[7] : 0,
				'agosto' => $request[8] != NULL ? $request[8] : 0,
				'septiembre' => $request[9] != NULL ? $request[9] : 0,
				'octubre' => $request[10] != NULL ? $request[10] : 0,
				'noviembre' => $request[11] != NULL ? $request[11] : 0,
				'diciembre' => $request[12] != NULL ? $request[12] : 0,
				'created_user' => $username
			]);
			if ($meta) {
				$b = array(
					"username" => $username,
					"accion" => 'Crear Meta',
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
			$res = ["status" => false, "mensaje" => ["icon" => 'info', "text" => 'El programa ya cuenta con una meta ', "title" => "La meta ya existe"]];
			return response()->json($res, 200);
		}
	}
	public function putMeta(Request $request)
	{
		Log::debug($request);
		Controller::check_permission('putMetas');
		$meta = Metas::where('id', $request->id_meta)->firstOrFail();
		$user = Auth::user()->username;
		$fecha = Carbon::now()->toDateTimeString();
		if ($meta) {
			$meta->tipo = $request->tipo_Ac;
			$meta->beneficiario_id = $request->tipo_Be;
			$meta->unidad_medida_id = $request->medida;
			$meta->cantidad_beneficiarios = $request->beneficiario;
			$meta->total = $request->sumMetas;
			$meta->enero = $request[1] != NULL ? $request[1] : 0;
			$meta->febrero = $request[2] != NULL ? $request[2] : 0;
			$meta->marzo = $request[3] != NULL ? $request[3] : 0;
			$meta->abril = $request[4] != NULL ? $request[4] : 0;
			$meta->mayo = $request[5] != NULL ? $request[5] : 0;
			$meta->junio = $request[6] != NULL ? $request[6] : 0;
			$meta->julio = $request[7] != NULL ? $request[7] : 0;
			$meta->agosto = $request[8] != NULL ? $request[8] : 0;
			$meta->septiembre = $request[9] != NULL ? $request[9] : 0;
			$meta->octubre = $request[10] != NULL ? $request[10] : 0;
			$meta->noviembre = $request[11] != NULL ? $request[11] : 0;
			$meta->diciembre = $request[12] != NULL ? $request[12] : 0;
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
			$res = ["status" => false, "mensaje" => ["icon" => 'Error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
			return response()->json($res, 200);
		}

	}
	public function deleteMeta(Request $request)
	{
		Controller::check_permission('deleteMetas');
		$mDelete = Metas::where('id', $request->id)->delete();
		if ($mDelete) {
			$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
			$b = array(
				"username" => Auth::user()->username,
				"accion" => 'Eliminar meta',
				"modulo" => 'Metas'
			);
			Controller::bitacora($b);
			return response()->json($res, 200);
		} else {
			$res = ["status" => false, "mensaje" => ["icon" => 'Error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
			return response()->json($res, 200);
		}
	}
	public function updateMeta($id)
	{
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
		$data = [];
		$areaAux = str_split($metas[0]->area_funcional);
		$area = '' . strval($areaAux[0]) . '-' . strval($areaAux[1]) . '-' . strval($areaAux[2]) . '-' . strval($areaAux[3]) . '-' . strval($areaAux[4]) . strval($areaAux[5]) . '-' . strval($areaAux[6]) . '-' . strval($areaAux[7]) . '-' . strval($areaAux[8]) . strval($areaAux[9]) . "-" . strval($areaAux[10]) . strval($areaAux[11]) . strval($areaAux[12]) . "-" . strval($areaAux[13]) . strval($areaAux[14]) . strval($areaAux[15]) . '';
		$meses = MetasController::meses($area, "" . $metas[0]->clv_upp . "-" . '0' . "-" . $metas[0]->clv_ur . "", $metas[0]->ejercicio);

		foreach ($metas as $key) {
			$area = str_split($key->area_funcional);
			$entidad = str_split($key->entidad_ejecutora);
			$i = array(
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
	public function exportExcel($upp, $anio)
	{
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar Metas Excel',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return Excel::download(new MetasExport($upp, $anio), 'Proyecto con actividades.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	public function exportExcelErr($err)
	{
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar Metas Excel',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return Excel::download(new MetasExportErr($err), 'Proyecto con actividades.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	public function proyExcel()
	{
		Controller::check_permission('getMetas');
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
		return Excel::download(new MetasCargaM(), 'CargaMasiva.xlsx');
	}
	public function pdfView($upp)
	{
		$date = Carbon::now();
		$year = $date->format('Y');
		Controller::check_permission('getMetas');
		$data = $this->getActiv($upp, $year);
		for ($i = 0; $i < count($data); $i++) {
			unset($data[$i][19]);
			$data = array_values($data);
		}
		return view('calendarizacion.metas.proyectoPDF', compact('data'));
	}

	public function exportPdf($upp, $year)
	{
		ini_set('max_execution_time', 5000);
		ini_set('memory_limit', '1024M');
		Controller::check_permission('getMetas');
		$data = $this->getActiv($upp, $year);
		for ($i = 0; $i < count($data); $i++) {
			unset($data[$i][19]);
			$data = array_values($data);
		}
		view()->share('data', $data);
		$pdf = PDF::loadView('calendarizacion.metas.proyectoPDF')->setPaper('a4', 'landscape');
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar metas PDF',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return $pdf->download('Proyecto con actividades.pdf');
	}
	public function downloadActividades($upp, $year)
	{
		$request = array(
			"anio" => $year,
			// "corte" => $date->format('Y-m-d'),
			"logoLeft" => public_path() . '\img\logo.png',
			"logoRight" => public_path() . '\img\escudoBN.png',
			"UPP" => $upp,
		);
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar metas Formato',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return $this->jasper($request);
	}
	public function jasper($request)
	{
		date_default_timezone_set('America/Mexico_City');

		setlocale(LC_TIME, 'es_VE.UTF-8', 'esp');
		$fecha = date('d-m-Y');
		$marca = strtotime($fecha);
		$fechaCompleta = strftime('%A %e de %B de %Y', $marca);
		$report = "Reporte_Calendario_UPP";

		$ruta = public_path() . "/reportes";
		//Eliminación si ya existe reporte
		if (File::exists($ruta . "/" . $report . ".pdf")) {
			File::delete($ruta . "/" . $report . ".pdf");
		}
		$report_path = app_path() . "/reportes/" . $report . ".jasper";
		$format = array('pdf');
		$output_file = public_path() . "/reportes";

		$parameters = $request;

		$database_connection = \Config::get('database.connections.mysql');


		$jasper = new PHPJasper;
		$jasper->process(
			$report_path,
			$output_file,
			$format,
			$parameters,
			$database_connection
		)->execute();
		//dd($jasper);
		$reportePDF = Response::make(file_get_contents(public_path() . "/reportes/" . $report . ".pdf"), 200, [
			'Content-Type' => 'application/pdf'
		]);

		if ($reportePDF != '') {
			return response()->json('done', 200);
		} else {
			return response()->json('error', 200);
		}
	}
	public function importPlantilla(Request $request)
	{
		DB::beginTransaction();
		try {
			$flag = false;
			if (Auth::user()->id_grupo == 4) {
				$check = $this->checkClosing(Auth::user()->clv_upp);
				$isMir = DB::table("mml_avance_etapas_pp")
                            ->select('id', 'estatus')
                            ->where('clv_upp', '=', Auth::user()->clv_upp)
                            ->where('ejercicio', '=', $check['anio'])
                            ->where('estatus', 3)->get();
				if(count($isMir)==0){
					$error = array(
						"icon" => 'error',
						"title" => 'MIR no confirmadas',
						"text" => 'Los registros de la MIR no estan confirmadas en el sistema MML, acércate a CPLADEM'
					);
					return response()->json($error);
				}
				$flag = $check['status'];
			} else if (Auth::user()->id_grupo == 1) {
				$flag = true;
			}
			if ($flag) {
				Schema::create('metas_temp', function (Blueprint $table) {
					$table->temporary();
					$table->increments('id');
					$table->string('clave', 25)->nullable(false);
					$table->string('upp', 25)->nullable(false);
					$table->string('fila', 10)->nullable(false);
				});
				$assets = $request->file('cmFile');
				if ($xlsx = SimpleXLSX::parse($assets)) {
					$filearray = $xlsx->rows();
					array_shift($filearray);
					$resul = FunFormats::saveImport($filearray);
					Log::debug($resul);
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
			}else{
				$error = array(
					"icon" => 'error',
					"title" => 'Metas cerradas',
					"text" => 'La captura de metas esta cerrada'
				);
				return response()->json($error);
			}
		} catch (\Exception $e) {
			DB::rollback();
		}
	}
	public function checkCombination($upp)
	{
		$check = $this->checkClosing($upp);

		$metas = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.id AS mir_id',
				'mml_mir.area_funcional AS area',
				'mml_mir.clv_upp',
				'metas.id as metas_id',
				'metas.estatus'
			)
			->where('mml_mir.deleted_at', null)
			->where('mml_mir.deleted_at', null)
			->where('metas.estatus', 2)
			->where('mml_mir.clv_upp', $upp)->get();
		if ($check['status']) {
			if (count($metas) == 0 || Auth::user()->id_grupo == 1) {
				//ver si esta confirmada la mir
				$isMir = DB::table("mml_avance_etapas_pp")
					->select('id', 'estatus')
					->where('clv_upp', '=', $upp)
					->where('ejercicio', '=', $check['anio'])
					->where('estatus', 3)->get();
				if (count($isMir)) {
					$activs = DB::table("programacion_presupuesto")
						->select(
							'programa_presupuestario AS programa',
							DB::raw('CONCAT(upp,subsecretaria,ur) AS area'),
							DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS clave')
						)
						->where('programacion_presupuesto.upp', '=', $upp)
						->where('programacion_presupuesto.ejercicio', '=', $check['anio'])
						->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
						->distinct()
						->where('estado', 1)
						->groupByRaw('programa_presupuestario')->get();
					if (count($activs)) {
						$auxAct = count($activs);
						$index = 0;
						foreach ($activs as $key) {
							$proyecto = DB::table('mml_mir')
								->select(
									'mml_mir.id',
									'mml_mir.area_funcional AS area'
								)
								->where('mml_mir.deleted_at', null)
								->where('mml_mir.nivel', 11)
								->where('mml_mir.clv_upp', $upp)
								->where('mml_mir.area_funcional', $key->clave)
								->get();
							if (count($proyecto)) {
								$index++;
							}
						}
						if ($index >= $auxAct) {
							return ["status" => true, "mensaje" => '', "estado" => true];
						} else {
							return ["status" => false, "mensaje" => 'MIR incompleta acercate a CPLADEM', "estado" => true];
						}

					} else {
						return ["status" => false, "mensaje" => 'Es necesario capturar y confirmar tus claves presupuestarias', "estado" => false, "url" => '/calendarizacion/claves'];
					}
				} else {
					return ["status" => false, "mensaje" => 'Los registros de la MIR no estan confirmadas en el sistema MML, acércate a CPLADEM', "estado" => true];
				}
				//ver si esta confirmada la mir
			} else {
				return ["status" => false, "mensaje" => 'Las metas ya estan confirmadas', "title" => 'Metas confirmadas', "estado" => false, "url" => '/calendarizacion/proyecto'];
			}
		} else {
			return ["status" => false, "mensaje" => 'La captura de metas esta cerrada', "title" => 'Metas cerradas', "estado" => false, "url" => '/calendarizacion/proyecto'];
		}
	}

	public function descargaReporteFirma(Request $request)
	{
		try {
			//generamos el nombre del archivo a guardar
			$nameCer = substr(str_replace(" ", "_", $request->cer->getClientOriginalName()), 0, -4);
			//si el nombre es mayor a 55 caracteres se toman solo los primeros 55
			if (strlen($nameCer) > 55) {
				$nameCer = substr($nameCer, 0, 55);
			}
			$fileExtCer = $request->cer->getClientOriginalExtension();
			$nameSaveCer = $nameCer . "." . $fileExtCer;
			//generamos el nombre del archivo a guardar
			$nameKey = substr(str_replace(" ", "_", $request->key->getClientOriginalName()), 0, -4);
			//si el nombre es mayor a 55 caracteres se toman solo los primeros 55
			if (strlen($nameKey) > 55) {
				$nameKey = substr($nameKey, 0, 55);
			}
			$fileExtKey = $request->key->getClientOriginalExtension();
			$nameSaveKey = $nameKey . "." . $fileExtKey;
			//crear un path para los reportes... storage\app\public\reportes\Claveprivada_FIEL_HEHF7712015Z2_20220324_105350.key
			$cerPath = Storage::path('public/reportes/' . $nameSaveCer);
			$keyPath = Storage::path('public/reportes/' . $nameSaveKey);
			//revisamos si existe  y lo eliminamos...
			if (File::exists($cerPath)) {
				Storage::delete($cerPath);
			}
			if (File::exists($keyPath)) {
				Storage::delete($keyPath);
			}
			//guardamos los archivos...
			$key = $request->key->storeAs('public/reportes/', $nameSaveKey);
			$cer = $request->cer->storeAs('public/reportes/', $nameSaveCer);
			$cerFile = '';
			$keyFile = '';
			//obtenemos el contenido de los archivos...
			if (File::exists($cerPath)) {
				$cerFile = file_get_contents($cerPath);
			}
			if (File::exists($keyPath)) {
				$keyFile = file_get_contents($keyPath);
			}
			$pdf = '';
			if ($request->tipoReporte == 2) {
				$ruta = public_path() . "/reportes/proyecto_calendario_actividades.pdf";
			} else {
				$ruta = public_path() . "/reportes/Reporte_Calendario_UPP.pdf";
			}

			if (File::exists($ruta)) {
				$pdf = file_get_contents($ruta);
			}
			//Hacemos la conexion con la api del login para obtener el token de verificacion...
			$token = Http::post('http://10.0.250.55/firmaElectronica/firmaElectronica/public/api/login', [
				'email' => 'pruebasinfraestructura@gmail.com',
				'password' => 'z2&CS53y',
			]);
			//una vez que tenemos el token hacemos la conexion con la api de firmado...
			if ($token && $token['token'] && $token['token'] != '') {
				$header = array();
				$response = Http::withToken($token['token'])
					->withHeaders($header);
				$response = $response->attach('pdf[]', $pdf, 'Reporte_Calendario_UPP.pdf');
				$response = $response->attach('cer', $cerFile, $nameSaveCer);
				$response = $response->attach('key', $keyFile, $nameSaveKey);
				$response = $response->post('http://10.0.250.55/firmaElectronica/firmaElectronica/public/api/firmarPDF', [
					'pass' => '12345678a',
					'cadenaOrigen' => 'prueba',
					'clave_tramite' => 'IAP01',
					'encabezado' => 1
				]);
				if ($response && $response[0]['pdfFirmado']) {
					$file = $response[0]['pdfFirmado'];
					$response = ['estatus' => 'done', 'data' => $file];
					return $response;
				} else {
					$responseError = ['estatus' => 'error', 'data' => $response];
					return $responseError;
				}
			} else {
				$responseError = ['estatus' => 'error', 'data' => $token];
				return $responseError;
			}
		} catch (\Exception $exp) {
			Log::debug('exp ' . $exp->getMessage());
			throw new \Exception($exp->getMessage());
			return response()->json('error', 200);
		}

	}
	public static function checkClosing($upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')
			->select(
				'estatus',
				'ejercicio'
			)
			->where('clv_upp', '=', $upp)
			->get();
		if (count($anio)) {
			if (Auth::user()->id_grupo == 1 || $anio[0]->estatus == 'Abierto') {
				return ["status" => true, "anio" => $anio[0]->ejercicio];
			} else {
				return ["status" => false, "anio" => $anio[0]->ejercicio];

			}
		} else {
			return ["status" => false, "anio" => 0000];
		}

	}
	function checkGoals($upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')
			->select(
				'estatus',
				'ejercicio'
			)
			->where('clv_upp', '=', $upp)
			->get();
		if (count($anio)) {
			if (Auth::user()->id_grupo == 1 || $anio[0]->estatus == 'Abierto') {
				return ["status" => true];
			} else {
				return ["status" => false];

			}
		} else {
			return ["status" => false];
		}
	}

	public function jasperMetas($upp, $anio)
	{
		date_default_timezone_set('America/Mexico_City');

		setlocale(LC_TIME, 'es_VE.UTF-8', 'esp');
		$fecha = date('d-m-Y');
		$date = $anio;
		Log::debug($date);
		$marca = strtotime($fecha);
		$fechaCompleta = strftime('%A %e de %B de %Y', $marca);
		$report = "proyecto_calendario_actividades";

		$ruta = public_path() . "/reportes";
		//Eliminación si ya existe reporte
		if (File::exists($ruta . "/" . $report . ".pdf")) {
			File::delete($ruta . "/" . $report . ".pdf");
		}
		$report_path = app_path() . "/Reportes/" . $report . ".jasper";
		$format = array('pdf');
		$output_file = public_path() . "/reportes";

		$parameters = array(
			"anio" => $date,
			"logoLeft" => public_path() . '\img\logo.png',
			"logoRight" => public_path() . '\img\escudoBN.png',
			"upp" => $upp,
		);

		$database_connection = \Config::get('database.connections.mysql');


		$jasper = new PHPJasper;
		$jasper->process(
			$report_path,
			$output_file,
			$format,
			$parameters,
			$database_connection
		)->execute();
		//dd($jasper);
		$reportePDF = Response::make(file_get_contents(public_path() . "/reportes/" . $report . ".pdf"), 200, [
			'Content-Type' => 'application/pdf'
		]);
		if ($reportePDF != '') {
			return response()->json('done', 200);
		} else {
			return response()->json('error', 200);
		}
	}
	public static function keyMonth($n)
	{
		$meses = [
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
		];

		return $meses[$n];
	}
	public static function validateMonth()
	{
		//$meses=MetasController::meses($area,$entidad,$check['anio']);

	}
	public static function confirmar($upp, $anio)
	{

		try {
			Controller::check_permission('putMetas');
			DB::beginTransaction();
			$user = Auth::user()->username;
			$metas = DB::table('metas')
				->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
				->select(
					'metas.id',
					'mml_mir.entidad_ejecutora',
					'mml_mir.area_funcional',
					'mml_mir.clv_upp'
				)
				->where('mml_mir.clv_upp', $upp)
				->where('mml_mir.ejercicio', $anio)
				->where('mml_mir.deleted_at', null)
				->where('metas.deleted_at', null)
				->where('metas.estatus', 0)->get();
			log::debug($metas);
			$i = 0;
			foreach ($metas as $key) {
				$meta = Metas::where('id', $key->id)->firstOrFail();
				$fecha = Carbon::now()->toDateTimeString();
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
					"accion" => 'confirmacion de meta',
					"modulo" => 'Metas'
				);
				Controller::bitacora($b);
				$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
				return response()->json($res, 200);
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'Error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
				return response()->json($res, 200);
			}
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

	}
	public static function cmetas($upp, $anio)
	{
		$metas = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.entidad_ejecutora',
				'mml_mir.area_funcional',
				'mml_mir.clv_upp'
			)
			->where('mml_mir.clv_upp', $upp)
			->where('mml_mir.ejercicio', $anio)
			->where('mml_mir.deleted_at', null)
			->where('metas.deleted_at', null)
			->where('metas.estatus', 0)->get();
		$pp = [];
		foreach ($metas as $key) {
			$area = str_split($key->area_funcional);
			$entidad = str_split($key->entidad_ejecutora);
			$activs = DB::table('programacion_presupuesto')
				->select(
					'upp AS clv_upp',
					DB::raw('CONCAT(upp,subsecretaria,ur) AS area_funcional'),
					DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS entidad_ejecutora')
				)
				->where('deleted_at', null)
				->where('finalidad', $area[0])
				->where('funcion', $area[1])
				->where('subfuncion', $area[2])
				->where('eje', $area[3])
				->where('linea_accion', '' . strval($area[4]) . strval($area[5]) . '')
				->where('programa_sectorial', $area[6])
				->where('tipologia_conac', $area[7])
				->where('upp', $key->clv_upp)
				->where('ur', '' . strval($entidad[4]) . strval($entidad[5]) . '', )
				->where('programa_presupuestario', '' . strval($area[8]) . strval($area[9]) . '', )
				->where('subprograma_presupuestario', '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '')
				->where('proyecto_presupuestario', '' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '')
				->where('programacion_presupuesto.ejercicio', '=', $anio)
				->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
				->distinct()
				->get();
			if ($activs) {
				$pp[] = json_encode($activs[0]);
			}
		}
		$activsPP = DB::table('programacion_presupuesto')
			->select(
				'upp AS clv_upp',
				DB::raw('CONCAT(upp,subsecretaria,ur) AS area_funcional'),
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS entidad_ejecutora')
			)
			->where('upp', $upp)
			->where('deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
			->get();

		if (count($metas) == count($pp) && count($metas) >= 1 && count($activs) >= 1 && count($metas) >= count($activsPP)) {
			return ["status" => true];
		} else {
			return ["status" => false];
		}
	}
	public static function getAnios()
	{
		$anio = DB::table('mml_mir')
			->select(
				DB::raw("IFNULL(mml_mir.ejercicio," . date('Y') . ") AS ejercicio")
			)
			->groupBy('mml_mir.ejercicio')
			->get();
		return $anio;
	}
	public static function cmetasUpp($upp,$anio)
	{
		$_upp = $upp=null?Auth::user()->clv_upp:$upp;

		$metas = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.clv_upp'
			)
			->where('mml_mir.clv_upp', $_upp)
			->where('mml_mir.ejercicio', $anio)
			->where('mml_mir.deleted_at', null)
			->where('metas.deleted_at', null)
			->where('metas.estatus', 1)->get();
		if (count($metas) >= 1) {
			return ["status" => true];
		} else {
			return ["status" => false];
		}
	}
	public static function cmetasadd($_upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$metas = DB::table('metas')
			->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
			->select(
				'mml_mir.clv_upp'
			)
			->where('mml_mir.clv_upp', $_upp)
			->where('mml_mir.ejercicio', $anio)
			->where('mml_mir.deleted_at', null)
			->where('metas.deleted_at', null)
			->where('metas.estatus', 1)->get();
		if (count($metas) >= 1) {
			return ["status" => true];
		} else {
			return ["status" => false];
		}
	}

}