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
use App\Exports\MetasExportErrTotal;
use App\Exports\Calendarizacion\MetasCargaM;
use App\Models\calendarizacion\Metas;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\Calendarizacion\MetasHelper;
use Illuminate\Support\Facades\Schema;
use PDF;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Http;
use Storage;
use App\Models\calendarizacion\CierreMetas;
use App\Models\MmlMir;
use App\Models\Catalogo;
use App\Http\Controllers\utils\ReportesJasper;


class MetasController extends Controller
{
	public function getManual(){
        $file = "";
        $name = "";
       
        if(Auth::user()->id_grupo==4){
            $name = "CAP_Manual_de_Usuario_UPP-CargaMasivaMetas.pdf";
            $file= public_path()."/manuales/". $name;
        } 
        $headers = array('Content-Type: application/pdf',);

        return response()->download($file,$name,$headers);
    }
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
		$u2p= DB::table('uppautorizadascpnomina')->select('clv_upp')->where('clv_upp', $upp)->where('uppautorizadascpnomina.deleted_at', null)->get();
		$aut = count($u2p) == 0?true:false;
		$query = MetasHelper::actividades($upp, $anio);
		$anioMax = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$dataSet = [];
		foreach ($query as $key) {
			$area = str_split($key->area);
			$entidad = str_split($key->entidad);
			$accion = Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3 ? '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>' : '';
				$sub = '' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '';
			$button = '';
			if ($key->estatus == 1 && Auth::user()->id_grupo == 1) {
				if ($anio == $anioMax) {
						$button = $accion;
				} else {
					$button = '';
				}
			} 
			if ($key->estatus == 0 && Auth::user()->id_grupo == 4) {
					if ($sub == 'UUU') {
						if ($aut) {
							$button = $accion;
						} else {
							$button = '';
						}

					} else {
						$button = $accion;
					}

			}
			if ($key->estatus == 0  && Auth::user()->id_grupo == 5 ) {
					if($sub =='UUU' && !$aut){
						$button = $accion;
					}else{
						$button = '';
					}
				}
			if ($key->estatus == 0  && Auth::user()->id_grupo == 1 ) {
					if ($anio == $anioMax) {
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
				$key->upp,
				$key->clv_ur,
				$key->clv_pp,
				'' . strval($area[10]) . strval($area[11]) . strval($area[12]) . '',
				'' . strval($area[13]) . strval($area[14]) . strval($area[15]) . '',
				$key->fondo,
				$key->actividad,
				$key->tipo,
				$key->total,
				$key->cantidad_beneficiarios,
				$key->beneficiario,
				$key->unidad_medida,
				$button,		
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getMetasP($upp_filter, $ur_filter)
	{
		Controller::check_permission('getMetas');
		$dataSet = [];
		$upp = isset($upp_filter) ? $upp_filter : auth::user()->clv_upp;
		if (auth::user()->id_grupo == 4) {
			$upp = auth::user()->clv_upp;
		}
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
						DB::raw('CONCAT(proyecto_presupuestario, " - ", v_epp.proyecto) AS proyecto'),
						DB::raw('CONCAT(programacion_presupuesto.upp,programacion_presupuesto.subsecretaria,programacion_presupuesto.ur) AS entidad'),
						DB::raw('CONCAT(programacion_presupuesto.finalidad,programacion_presupuesto.funcion,programacion_presupuesto.subfuncion,programacion_presupuesto.eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programacion_presupuesto.programa_presupuestario,programacion_presupuesto.subprograma_presupuestario,programacion_presupuesto.proyecto_presupuestario) AS area'),
						'v_epp.con_mir AS mir',
						'programacion_presupuesto.ejercicio',
						'programacion_presupuesto.fondo_ramo AS clv_fondo',
						DB::raw('CONCAT(programacion_presupuesto.fondo_ramo," ") AS fondo'),
					)
					->where('programacion_presupuesto.ur', '=', $ur_filter)
					->where('programacion_presupuesto.upp', '=', $upp)
					->where('programacion_presupuesto.ejercicio', '=', $check['anio'])
					->where('v_epp.ejercicio', '=', $check['anio'])
					->where('v_epp.presupuestable', '=', 1)
					->orderBy('programacion_presupuesto.upp')
					->where('programacion_presupuesto.deleted_at', null)
					->groupByRaw('programacion_presupuesto.ur,finalidad,programacion_presupuesto.funcion,programacion_presupuesto.subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
					->distinct();
					if(auth::user()->id_grupo == 4) {
					$activs = $activs->where('programacion_presupuesto.tipo', '=','Operativo');
					}
					$activs=$activs->get();
				foreach ($activs as $key) {
					$mirx = $key->mir;
					$area = '"' . strval($key->finalidad) . '-' . strval($key->funcion) . '-' . strval($key->subfuncion) . '-' . strval($key->eje) . '-' . strval($key->linea) . '-' . strval($key->programaSec) . '-' . strval($key->tipologia) . '-' . strval($key->programa) . '-' . strval($key->subprograma) . '-' . strval($key->clv_proyecto) . '"';
					$entidad = '"' . strval($upp) . '-' . strval($key->subsec) . '-' . strval($ur_filter) . '"';
					$clave = '"' . strval($upp) . strval($key->subsec) . strval($ur_filter) . '-' . strval($key->finalidad) . strval($key->funcion) . strval($key->subfuncion) . strval($key->eje) . strval($key->linea) . strval($key->programaSec) . strval($key->tipologia) . strval($key->programa) . strval($key->subprograma) . strval($key->clv_proyecto) . '"';
					$accion = "<div class'form-check'><input class='form-check-input clave' type='radio' name='clave' id='" . $clave . "' value='" . $clave . "' onchange='dao.getFyA(" . $area . "," . $entidad . "," . $mirx . "," . $key->ejercicio . ")' ></div>";
					$fondos=MetasHelper::fondos($key->area,$key->entidad,$check['anio']);
					$existM=MetasController::existMeta($key->area,$key->entidad,$check['anio'],$fondos->fondoArr);
					$dataSet[] = [$key->finalidad, $key->funcion, $key->subfuncion, $key->eje, $key->linea, $key->programaSec, $key->tipologia, $key->programa, $key->subprograma, $key->proyecto, $accion,$fondos->fondoStr,$existM->exist];
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

			if ($check['status'] && $upp != null) {
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
						'Acumulativa',
						'Especial',
						'Continua',
					)
					->where('deleted_at', null)
					->orderBy('clv_upp')
					->where('clv_upp', $upp)
					->get();

				if (count($Act) >= 1) {
					$tAct = $Act[0];
				}

			}
		}

		return ["urs" => $urs, "tAct" => $tAct];
	}
	public function getUpps()
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
	public function getFyA($area, $entidad)
	{
		$areaAux = explode('-', $area);
		$entidadAux = explode('-', $entidad);
		$check = $this->checkClosing($entidadAux[0]);
		if ($check['status']) {
			$fondos = DB::table('programacion_presupuesto')
				->leftJoin('fondo', 'fondo.clv_fondo_ramo', 'programacion_presupuesto.fondo_ramo')
				->leftJoin('v_epp', 'v_epp.clv_proyecto', '=', 'programacion_presupuesto.proyecto_presupuestario')
				->select(

					'fondo.id',
					'programacion_presupuesto.fondo_ramo as clave',
					DB::raw('CONCAT(programacion_presupuesto.fondo_ramo, " - ", fondo.ramo) AS ramo')
				)
				->where('fondo.deleted_at', null)
				->where('programacion_presupuesto.deleted_at', null)
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
				->where('v_epp.presupuestable', '=', 1)
				->groupByRaw('clave')
				->where('programacion_presupuesto.ejercicio', $check['anio'])
				->get();
			$tAct = MetasController::getTcalendar($entidadAux[0]);
		}

		return ['fondos' => $fondos,"tAct"=> $tAct];
	}
	public function getActividMir($area, $entidad,$fondo)
	{

		$areaAux = explode('-', $area);
		$entidadAux = explode('-', $entidad);
		$check = $this->checkClosing($entidadAux[0]);
		$tipo='';
		$framo33 = false;
		if ($check['status']) {
			
			$ramo33 = DB::table('v_ramo_33')
				->select('id')
				->where('clv_programa', $areaAux[7])
				->where('clv_fondo_ramo', $areaAux[8])
				->where('ejercicio', $check['anio'])
				->get();
				if($ramo33){
					$framo33 = true;
				}

			$m = DB::table('v_epp')
				->select(
					'con_mir',
					'tipo_presupuesto'
				)
				->where('v_epp.deleted_at', null)
				->where('clv_finalidad', $areaAux[0])
				->where('clv_funcion', $areaAux[1])
				->where('clv_subfuncion', $areaAux[2])
				->where('clv_eje', $areaAux[3])
				->where('clv_linea_accion', $areaAux[4])
				->where('clv_programa_sectorial', $areaAux[5])
				->where('clv_tipologia_conac', $areaAux[6])
				->where('clv_upp', $entidadAux[0])
				->where('clv_ur', $entidadAux[2])
				->where('clv_programa', $areaAux[7])
				->where('clv_subprograma', $areaAux[8])
				->where('clv_proyecto', $areaAux[9])
				->where('presupuestable', '=', 1)
				->groupByRaw('con_mir')
				->where('ejercicio', $check['anio'])
				->get();
			$activ = [];
			if ($m[0]->con_mir == 1) {
				$activ = DB::table('mml_mir')
					->select(
						'mml_mir.id',
						'mml_mir.id as clave',
						DB::raw('CONCAT(mml_mir.id, " - ",objetivo) AS actividad')
					)
					->where('mml_mir.deleted_at', null)
					->where('mml_mir.nivel', 11)
					->where('mml_mir.area_funcional', str_replace("-", '', $area))
					->where('mml_mir.entidad_ejecutora', str_replace("-", '', $entidad))
					->where('mml_mir.clv_upp', $entidadAux[0])
					->where('mml_mir.clv_ur', $entidadAux[2])
					->where('mml_mir.clv_pp', $areaAux[7])
					->where('mml_mir.ejercicio', $check['anio']);
					if($m[0]->tipo_presupuesto==1){
					$activ = $activ->whereIn('mml_mir.ramo33',[1,0]);
					}
					if($m[0]->tipo_presupuesto==0){
					$activ = $activ->where('mml_mir.ramo33', 0);
					}
					$activ =$activ->get();
				$tipo='M';
				if (count($activ) == 0) {
					$activ[] = ['id' => 'ot', 'clave' => 'ot', 'actividad' => 'Otra actividad'];
					$tipo='O';
				}
			} else {
				$activ = Catalogo::select('id', 'clave', DB::raw('CONCAT(clave, " - ",descripcion) AS actividad'))->where('ejercicio',  $check['anio'])->where('clave', $areaAux[8])->where('deleted_at', null)->where('grupo_id', 20)->get();
				$tipo='C';
			}
		}

		return ["activids" => $activ ,"con_mir"=>$m[0]->con_mir,"tipoAc"=>$tipo];
	}
	public static function meses($area, $entidad, $anio, $fondo)
	{
		$areaAux = explode('-', $area);
		$entidadAux = explode('-', $entidad);
		$meses = DB::table('programacion_presupuesto')
			->select(
				DB::raw("SUM(enero) AS enero"),
				DB::raw("SUM(febrero) AS febrero"),
				DB::raw("SUM(marzo) AS marzo"),
				DB::raw("SUM(abril) AS abril"),
				DB::raw("SUM(mayo) AS mayo"),
				DB::raw("SUM(junio) AS junio"),
				DB::raw("SUM(julio) AS julio"),
				DB::raw("SUM(agosto) AS agosto"),
				DB::raw("SUM(septiembre) AS septiembre"),
				DB::raw("SUM(octubre) AS octubre"),
				DB::raw("SUM(noviembre) AS noviembre"),
				DB::raw("SUM(diciembre) AS diciembre")
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
			->where('fondo_ramo', $fondo)
			->where('ejercicio', $anio)
			->where('programacion_presupuesto.deleted_at', null)
			->get();
		$dataSet = count($meses) >= 1 ? $meses[0] : [];
		return $dataSet;
	}
	public static function getSelects()
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
				'Acumulativa',
				'Especial',
				'Continua',
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
		DB::beginTransaction();
		try {
			$username = Auth::user()->username;
			Controller::check_permission('postMetas');
			$anio = DB::table('cierre_ejercicio_metas')->where('deleted_at', null)->max('ejercicio');
			$clv = explode('/', $request->area);
			$area_funcional = str_replace('-', "", $clv[0]);
			$rj = explode('$', $clv[1]);
			$entidad_ejecutora = str_replace('-', "", $rj[0]);
			$fondo = $request->sel_fondo;
			$actividad = $request->actividad_id;
			$act = '';
			switch ($request->tipoAct) {
				case 'M':
					$metaMir = MetasHelper::isExistMmir($entidad_ejecutora, $area_funcional, $fondo, intval($actividad), $anio);
					if ($metaMir) {
						$res = ["status" => false, "mensaje" => ["icon" => 'info', "text" => 'Esa actividad ya tiene metas para ese proyecto y fondo ', "title" => "La meta ya existe"]];
						return response()->json($res, 200);
					}
					$act = NULL;
					break;
				case 'O':
					$metaOt = MetasHelper::isExistMoT($entidad_ejecutora, $area_funcional, $fondo, $anio);
					if ($metaOt) {
						$res = ["status" => false, "mensaje" => ["icon" => 'info', "text" => 'Esa actividad ya tiene metas para ese proyecto y fondo ', "title" => "La meta ya existe"]];
						return response()->json($res, 200);
					} else {
						$act = MetasHelper::createMml_Ac($request->upp, $entidad_ejecutora, $area_funcional, $actividad, $request->inputAc, $anio);
						$actividad = NULL;
					}

					break;
				case 'C':
					$metaCat = MetasHelper::isExistCat($entidad_ejecutora, $area_funcional, $fondo, intval($actividad), $anio);
					if ($metaCat) {
						$res = ["status" => false, "mensaje" => ["icon" => 'info', "text" => 'Esa actividad ya tiene metas para ese proyecto y fondo ', "title" => "La meta ya existe"]];
						return response()->json($res, 200);
					} else {
						$act = MetasHelper::createMml_Ac($request->upp, $entidad_ejecutora, $area_funcional, $actividad, $request->inputAc, $anio);
						$actividad = NULL;
					}
					break;

				default:
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
				return response()->json($res, 200);
			}
			

			$meses = [];
			$subpp = explode('-', $clv[0]);
			$flagSubPp = $subpp[8] != 'UUU' ? 1 : 0;
			$meses = [
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
			];
						
				$meta = MetasHelper::createMeta($request,$actividad,$fondo,$act,$meses,$anio,$flagSubPp);			
			if ($meta) {
				$b = array(
					"username" => $username,
					"accion" => 'Crear Meta',
					"modulo" => 'Metas'
				);
				Controller::bitacora($b);
				DB::commit();
				$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
				return response()->json($res, 200);
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
				return response()->json($res, 200);
			}

		} catch (\Exception $e) {
			DB::rollback();
		}
	}
	public function putMeta(Request $request)
	{
		Controller::check_permission('putMetas');
		$meta = Metas::where('id', $request->id_meta)->firstOrFail();
		$user = Auth::user()->username;
		$fecha = Carbon::now()->toDateTimeString();
		if ($meta) {
			if($request->subp!='UUU'){
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
			}else{
				$meta->tipo = $request->tipo_Ac;
				$meta->beneficiario_id = 12;
				$meta->unidad_medida_id = 829;
				$meta->cantidad_beneficiarios = $request->beneficiario;
				$meta->total = 25;
				$meta->enero = 2;
				$meta->febrero = 2;
				$meta->marzo = 2;
				$meta->abril = 2;
				$meta->mayo = 2;
				$meta->junio = 2;
				$meta->julio = 2;
				$meta->agosto = 2;
				$meta->septiembre = 2;
				$meta->octubre =2;
				$meta->noviembre = 2;
				$meta->diciembre = 3;
				$meta->updated_at = $fecha;
				$meta->updated_user = $user;
				$meta->save();

			}
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
	public function deleteMeta(Request $request)
	{
		Controller::check_permission('deleteMetas');
		$meta = Metas::where('id', $request->id)->firstOrFail();
		$meta->estatus = 0;
		$meta->deleted_user = Auth::user()->username;
		$meta->save();
		$mDelete = Metas::where('id', $request->id)->delete();
		if ($mDelete) {
			if ($meta->actividad_id != null) {
				$actv = MmlMir::where('id', $meta->actividad_id)->firstOrFail();
				$actv->deleted_user = Auth::user()->username;
				$actv->save();
				$m = MmlMir::where('id', $meta->actividad_id)->delete();
			}

			$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
			$b = array(
				"username" => Auth::user()->username,
				"accion" => 'Eliminar meta',
				"modulo" => 'Metas'
			);
			Controller::bitacora($b);
			return response()->json($res, 200);
		} else {
			$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
			return response()->json($res, 200);
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
					DB::raw('CONCAT(mml_mir.id, " - ", mml_mir.objetivo) AS actividad'),
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
	

	public function exportExcelErrTotal($anio)	
	{
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/

		return Excel::download(new MetasExportErrTotal($anio), 'Metas con diferencias.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	public function exportExcelErr($upp, $anio)	
	{
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/

		return Excel::download(new MetasExportErr($upp,$anio), 'Metas con diferencias.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	public function proyExcel($upp)
	{
		ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
		Schema::create('pptemp', function (Blueprint $table) {
			$table->temporary();
			$table->increments('id');
			$table->string('clave', 55)->nullable(false);
			$table->string('clv_upp', 25)->nullable(false);
			$table->string('clv_ur', 25)->nullable(false);
			$table->string('clv_pp', 25)->nullable(false);
			$table->string('entidad_ejecutora', 55)->nullable(false);
			$table->string('area_funcional', 55)->nullable(false);
			$table->string('fondo', 55)->nullable(false);
			$table->string('sub_pp', 55)->nullable(false);
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

		Schema::create('mirtemp', function (Blueprint $table) {
			$table->temporary();
			$table->increments('id');
			$table->string('mir_id', 55)->nullable(false);
			$table->string('clave', 55)->nullable(false);
			$table->string('clv_upp', 25)->nullable(false);
			$table->string('clv_ur', 25)->nullable(false);
			$table->string('clv_pp', 25)->nullable(false);
			$table->string('entidad_ejecutora', 55)->nullable(false);
			$table->string('area_funcional', 55)->nullable(false);
			$table->string('objetivo', 255)->nullable(false);

		});
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
		return Excel::download(new MetasCargaM($upp), 'CargaMasiva_'.$upp.'.xlsx');
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
		if(Auth::user()->id_grupo ==4){
			$data = $this->getActiv($upp, $year);
		for ($i = 0; $i < count($data); $i++) {
			unset($data[$i][20]);
			$data = array_values($data);
		}
		}else{
			$data = MetasController::getActivAdm($year);
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
	public function downloadActividades($upp, $year, $tipo)
	{
		$request = array(
			"anio" => $year,
			// "corte" => $date->format('Y-m-d'),
			"logoLeft" => public_path() . '\img\logo.png',
			"logoRight" => public_path() . '\img\escudoBN.png',
			"UPP" => $upp,
			"tipo" => $tipo,
			"idGrupo" => Auth::user()->id_grupo,
		);
		$b = array(
			"username" => Auth::user()->username,
			"accion" => 'Descargar metas Formato',
			"modulo" => 'Metas'
		);
		Controller::bitacora($b);
		return ReportesJasper::claves($request);
	}


	public function importPlantilla(Request $request)
	{
		Controller::check_permission('putMetas');
		Controller::check_assign(1);
		DB::beginTransaction();
		try {

			$flag = false;
			if (Auth::user()->id_grupo == 4) {
				$check = $this->checkClosing(Auth::user()->clv_upp);
				$isMir = DB::table("mml_cierre_ejercicio")
					->select('id', 'estatus')
					->where('clv_upp', '=', Auth::user()->clv_upp)
					->where('ejercicio', '=', $check['anio'])
					->where('statusm', 1)->get();
				if (count($isMir) == 0) {
					$error = array(
						"icon" => 'error',
						"title" => 'MIR no confirmadas',
						"text" => 'Los registros de la MIR no estan confirmados en el sistema MML, acércate a CPLADEM'
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
					$table->string('ur', 25)->nullable(false);
					$table->string('fila', 10)->nullable(false);
				});
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
					$resul = FunFormats::saveImport($filearray);
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
		if (Auth::user()->id_grupo == 4) {
			$metas = MetasController::cmetasadd($upp);
			if(!$metas["status"]){
				return ["status" => false, "mensaje" => 'Las metas para la UPP: '.$upp.' ya estan confirmadas', "title" => 'Metas confirmadas', "estado" => false, "url" => '/calendarizacion/proyecto'];
			}
		}
		
		if ($check['status']) {
				//ver si esta confirmada la mir
				$isMir = DB::table("mml_cierre_ejercicio")
					->select('id', 'estatus')
					->where('clv_upp', '=', $upp)
					->where('ejercicio', '=', $check['anio'])
					->where('statusm', 1)->get();
				if (count($isMir)) {
					$activs = DB::table("programacion_presupuesto")
						->select(
							'programa_presupuestario AS programa',
							DB::raw('CONCAT(upp,subsecretaria,ur) AS entidad'),
							DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS clave')
						)
						->where('programacion_presupuesto.upp', '=', $upp)
						->where('programacion_presupuesto.ejercicio', '=', $check['anio'])
						->groupByRaw('ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
						->distinct()
						->where('programacion_presupuesto.deleted_at', null)
						->where('estado', 1)
						->groupByRaw('programa_presupuestario')->get();
					if (count($activs)) {
						/* 	$proyecto = DB::table('mml_mir')
												  ->select(
													  'mml_mir.id',
													  'mml_mir.area_funcional AS area'
												  )
												  ->where('mml_mir.deleted_at', null)
												  ->where('mml_mir.nivel', 11)
												  ->where('mml_mir.clv_upp', $upp)
												  ->get();
										  if (count($proyecto)) { */
						return ["status" => true, "mensaje" => '', "estado" => true];
						/* } else {
											  return ["status" => false, "mensaje" => 'No hay registros en MIR acercate a CPLADEM', "estado" => true];
										  } */
					} else {
						return ["status" => false, "mensaje" => 'Es necesario capturar y confirmar tus claves presupuestarias', "estado" => false, "url" => '/calendarizacion/claves'];
					}
				} else {
					return ["status" => false, "mensaje" => 'Los registros de la MIR no estan confirmadas en el sistema MML, acércate a CPLADEM', "estado" => true];
				}
				//ver si esta confirmada la mir
			
		} else {
			return ["status" => false, "mensaje" => 'Las metas para la UPP: '.$upp.' ya estan cerradas', "title" => 'La captura de metas esta cerrada', "estado" => false, "url" => '/calendarizacion/proyecto'];
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
				$ruta = sys_get_temp_dir() . "/proyecto_calendario_actividades.pdf";
			} else {
				$ruta = sys_get_temp_dir() . "/Reporte_Calendario_UPP.pdf";
			}
			if (File::exists($ruta)) {
				$pdf = file_get_contents($ruta);
			}
			//Hacemos la conexion con la api del login para obtener el token de verificacion...
			$token = Http::post(env('FIRMA_ELECTRONICA_LOGIN'), [
				'email' => env('FEL_EMAIL'),
				'password' => env('FEL_PASSWORD'),
			]);
			//una vez que tenemos el token hacemos la conexion con la api de firmado...
			if ($token && $token['token'] && $token['token'] != '') {
				$header = array();
				$response = Http::withToken($token['token'])
					->withHeaders($header);
				$response = $response->attach('pdf[]', $pdf, 'Reporte_Calendario_UPP.pdf');
				$response = $response->attach('cer', $cerFile, $nameSaveCer);
				$response = $response->attach('key', $keyFile, $nameSaveKey);
				$response = $response->post(env('FIRMA_ELECTRONICA'), [
					'pass' => env('FE_PASSWORD'),
					'cadenaOrigen' => 'prueba',
					'clave_tramite' => 'IAP01',
					'encabezado' => 1
				]);
				if ($response && $response[0]['pdfFirmado']) {
					$file = $response[0]['pdfFirmado'];
					$response = ['estatus' => 'done', 'data' => $file];
					if(File::exists($ruta)) {
                        File::delete($ruta);
                    }
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
			return response()->json('error', 200);
		}

	}
	public static function checkClosing($upp)
	{
		$date = Carbon::now();
		$year = $date->format('Y');
		$anioMax = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', $upp)->max('ejercicio');
		$anio = DB::table('cierre_ejercicio_metas')
			->select(
				'estatus',
				DB::raw("MAX(ejercicio) AS ejercicio")
			)
			->where('deleted_at', null)
			->where('clv_upp', '=', $upp)
			->where('ejercicio',$anioMax)
			->get();

		if (count($anio)) {
			if (Auth::user()->id_grupo == 1 || $anio[0]->estatus == 'Abierto') {
				return ["status" => true, "anio" => $anio[0]->ejercicio];
			} else {
				return ["status" => false, "anio" => $year];

			}
		} else {
			return ["status" => false, "anio" => $year];
		}

	}
	function checkGoals($upp)
	{
		$anioMax = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', $upp)->max('ejercicio');
		$anio = DB::table('cierre_ejercicio_metas')
			->select(
				'estatus',
				DB::raw("MAX(ejercicio) AS ejercicio")
			)
			->where('deleted_at', null)
			->where('clv_upp', '=', $upp)
			->where('ejercicio',$anioMax)
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

	public function jasperMetas($upp, $anio, $tipo)
	{
		return ReportesJasper::Metas($upp, $anio, $tipo);
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
	public static function confirmar($upp, $anio)
	{
		try {
			Controller::check_permission('putMetas');
			$s = MetasController::cmetas($upp, $anio);
			$fecha = Carbon::now()->toDateTimeString();
			$user = Auth::user()->username;
		 	$check = MetasHelper::validateMesesfinal($upp, $anio);
			if (!$check["status"]) {
				$foot = "<a type='button' class='btn btn-success col-md-5 ml-auto ' href=/actividades/meses/error/$upp/$anio > <i class='fa fa-download' aria-hidden='true'></i>Descargar index</a>";
				$res = ["status" => false, "mensaje" => ["icon" => 'warning', "text" => 'No puedes confirmar las metas, existen diferencias en los meses autorizados por las claves presupuestales', "title" => "Diferencias en las metas" ,"footer"=>$foot]];
				return response()->json($res, 200);

			} 
			if ($s['status']) {
				DB::beginTransaction();
				$metas = MetasHelper::actividades($upp, $anio);
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
	public static function desconfirmar($upp, $anio)
	{
		try {
			Controller::check_permission('putMetas');
			DB::beginTransaction();
			$user = Auth::user()->username;
			$metas = MetasHelper::actividades($upp, $anio);
			$fecha = Carbon::now()->toDateTimeString();
			$i = 0;
			foreach ($metas as $key) {
				$m = Metas::where('id', $key->id)->get();
				$meta = $m[0];
				if ($meta) {
					$meta->estatus = 0;
					$meta->updated_user = $user;
					$meta->updated_at = $fecha;
					$meta->save();
					$i++;
				}
			}
			if (count($metas) == $i && count($metas) >= 1 && $i >= 1) {
				$cierre = CierreMetas::where('deleted_at', null)->where('clv_upp', $upp)->where('estatus', 'Cerrado')->get();
				if ($cierre) {
					foreach ($cierre as $key ) {
						$key->estatus = 'Abierto';
						$key->updated_user = $user;
						$key->updated_at = $fecha;
						$key->save();
					}
					
				}
				DB::commit();
				$b = array(
					"username" => $user,
					"accion" => 'desconfirmacion de metas',
					"modulo" => 'Metas'
				);
				Controller::bitacora($b);
				$res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
				return $res;
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
				return $res;
			}
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

	}
	public static function cmetas($upp, $anio)
	{
		$proyecto = DB::table('mml_mir')
			->select(
				'mml_mir.id',
				'mml_mir.clv_upp AS upp',
				'mml_mir.clv_ur AS ur',
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
				'clv_upp as upp',
				'clv_ur as ur',
				'mml_actividades.id',
				'entidad_ejecutora AS entidad',
				'area_funcional AS area',
				DB::raw("IFNULL(nombre,IFNULL(catalogo.descripcion	,nombre)) AS actividad"),
				'mml_actividades.ejercicio',
			)
			->where('mml_actividades.deleted_at', '=', null)
			->where('catalogo.deleted_at', '=', null)
			->where('mml_actividades.clv_upp', $upp)
			->where('mml_actividades.ejercicio', $anio);

		$query2 = DB::table('metas')
			->leftJoinSub($actv, 'act', function ($join) {
				$join->on('metas.actividad_id', '=', 'act.id');
			})
			->select(
				DB::raw('CONCAT(act.area,act.entidad,metas.clv_fondo) AS clave'),
			)
			->where('metas.tipo_meta', '=', 'Operativo')
			->where('metas.deleted_at', '=', null)
			->where('metas.estatus', '=', 0)
			->where('act.upp', $upp)
			->where('metas.ejercicio', $anio)
			->groupByRaw('entidad,area,metas.clv_fondo')
			->distinct();
		$metas = DB::table('metas')
			->leftJoinSub($proyecto, 'pro', function ($join) {
				$join->on('metas.mir_id', '=', 'pro.id');
			})
			->select(
				DB::raw('CONCAT(pro.area,pro.entidad,metas.clv_fondo) AS clave'),
			)
			->where('metas.tipo_meta', '=', 'Operativo')
			->where('metas.deleted_at', '=', null)
			->where('metas.estatus', '=', 0)
			->where('pro.ejercicio', $anio)
			->where('pro.upp', $upp)
			->unionAll($query2)
			->groupByRaw('entidad,area,metas.clv_fondo')
			->distinct();

		$metas = $metas->get();
		$pp = [];

 
		$activsPP = DB::table('programacion_presupuesto')
			->select(
				DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,upp,subsecretaria,ur,fondo_ramo) AS clave'),
			)
			->where('programacion_presupuesto.tipo', '=', 'Operativo')
			->where('programacion_presupuesto.estado', '=', 1)
			->where('upp', $upp)
			->where('deleted_at', null)
			->where('programacion_presupuesto.ejercicio', '=', $anio)
			->groupByRaw('upp,ur,fondo_ramo,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,fondo_ramo')
			->distinct();
		
		$activsPP = $activsPP->get();

		foreach ($activsPP as $key) {
			foreach ($metas as $k) {
				if($key->clave==$k->clave){
					$pp[] = $key->clave;
				}
				
			}
			
		}
		$pp = array_unique($pp);

		if (count($metas) >= 1) {
			if (count($metas) >= count($activsPP) ) {
				if(count($activsPP) == count($pp)){
					return ["status" => true];
				}
			
			} else {
				return ["status" => false];
			}
		} else {
			return ["status" => false];
		}
	}
	public static function getAnios(){
			
		$anio = DB::table('cierre_ejercicio_metas')
			->select('ejercicio')
			->groupByRaw('ejercicio')
			->orderBy('ejercicio','DESC')
			->get();
		return response()->json(["anios"=>$anio]);
	}
	public static function cmetasUpp($upp, $anio)
	{
		$_upp = $upp = null ? Auth::user()->clv_upp : $upp;
		$metas = true;
		$query = MetasHelper::actividadesConf($_upp, $anio);
		if(count($query)){
			$metas = $query[0]->estatus == 1 ? false : true;
		}else{
			$actv = DB::table('metas')
				->leftJoin('mml_actividades','mml_actividades.id','=','metas.actividad_id')
				->select('mml_actividades.clv_upp','mml_actividades.id_catalogo','metas.estatus')
				->where('mml_actividades.id_catalogo', '=', null)
				->where('mml_actividades.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $upp)
				->where('mml_actividades.ejercicio', $anio)->get();
				if(count($actv)){
					$metas = $actv[0]->estatus == 1 ? false : true;
				}else{
					$metas = true;
				}
		}
		return ["status" => $metas];
	}
	public static function cmetasadd($_upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$metas = true;
		$query = MetasHelper::actividadesConf($_upp, $anio);
		if(count($query)){
			$metas = $query[0]->estatus == 1 ? false : true;
		}else{
			$actv = DB::table('metas')
				->leftJoin('mml_actividades','mml_actividades.id','=','metas.actividad_id')
				->select('mml_actividades.clv_upp','mml_actividades.id_catalogo','metas.estatus')
				->where('mml_actividades.id_catalogo', '=', null)
				->where('mml_actividades.deleted_at', '=', null)
				->where('mml_actividades.clv_upp', $_upp)
				->where('mml_actividades.ejercicio', $anio)->get();
				if(count($actv)){
					$metas = $actv[0]->estatus == 1 ? false : true;
				}else{
				$metas = true;
				}
		}
		return ["status" => $metas];
	}
	public static function getMeses($idAc, $idfondo)
	{

		if (isset($idAc)) {
			$clave = explode("$", $idAc);
			$meses = MetasController::meses($clave[0], $clave[1], $clave[2], $idfondo);
			return ['mese' => $meses];
		} else {
			return ['mese' => []];
		}

	}
	public static function getActivAdm($anio)
	{
		Controller::check_permission('getMetas');
		$query = MetasHelper::actividadesAdm($anio);
		$anioMax = DB::table('cierre_ejercicio_metas')->max('ejercicio');
		$dataSet = [];
		foreach ($query as $key) {
			$area = str_split($key->area);
			$entidad = str_split($key->entidad);
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
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public static function existMeta($area, $entidad, $anio, $fondo)
	{
		$exMeta = new \stdClass;
		$status = '';
		for ($i = 0; $i < count($fondo); $i++) {
			$query = MetasHelper::existMeta($area, $entidad, $anio,$fondo[$i]);
			if(count($query)){
				$status = $status .'<i class="fa fa-check" aria-hidden="true"></i>'.'<br>';
			}else{
				$status = $status .'<i class="fa fa-circle-o" aria-hidden="true"></i>' . '<br>';
			}
			$exMeta->exist = $status;
		}
		return $exMeta;
	}
}