<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Imports\utils\FunFormats;
use App\Http\Controllers\Controller;
use App\Models\calendarizacion\ProyectosMir;
use App\Models\calendarizacion\ActividadesMir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MetasExport;
use App\Exports\Calendarizacion\MetasCargaM;
use App\Models\calendarizacion\Metas;
use Auth;
use DB;
use Log;
use App\Helpers\Calendarizacion\MetasHelper;
use PDF;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Shuchkin\SimpleXLSX;
use Symfony\Component\Console\Helper\Table;
use Illuminate\Support\Facades\Http;
use Storage;



class MetasController extends Controller
{
	//Consulta Vista Usuarios
	public function getIndex()
	{
		Controller::check_permission('getClaves');
		return view('calendarizacion.metas.index');
	}
	public function getProyecto()
	{
		Controller::check_permission('getClaves');
		return view('calendarizacion.metas.proyecto');
	}
	public static function getActiv($upp)
	{
		$query = MetasHelper::actividades($upp);
		$dataSet = [];
		foreach ($query as $key) {
			$accion = Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3 ? '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>':'';
			$area = str_split($key->area);
			$entidad = str_split($key->entidad);
			$i = array(
				$area[0],
				$area[1],
				$area[2],
				$area[3],
				''.strval($area[4]).strval($area[5]).'',
				$area[6],
				$area[7],
				''.strval($entidad[0]).strval($entidad[1]).strval($entidad[2]).'',
				''.strval($entidad[4]).strval($entidad[5]).'',
				''.strval($area[8]).strval($area[9]).'',
				''.strval($area[10]).strval($area[11]).strval($area[12]).'',
				''.strval($area[13]).strval($area[14]).strval($area[15]).'',
				$key->fondo,
				$key->actividad,
				$key->tipo,
				$key->total,
				$key->cantidad_beneficiarios,
				$key->beneficiario,
				$key->unidad_medida,
				$accion
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getMetasP(Request $request)
	{
		$dataSet = [];
			$upp = isset($request->upp_filter) ? $request->upp_filter : auth::user()->clv_upp;
			
			if ($request->ur_filter != null && $upp != '') {
				
				$anio = DB::table('cierre_ejercicio_metas')
					->select(
						'estatus',
						'ejercicio')
					->where('clv_upp', '=', $upp)
					->get();
					Log::debug($anio);
				if (count($anio)) {
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
						->where('programacion_presupuesto.ur', '=', $request->ur_filter)
						->where('programacion_presupuesto.upp', '=', $upp)
						->where('programacion_presupuesto.ejercicio', '=', $anio[0]->ejercicio)
						->where('v_epp.ejercicio', '=', $anio[0]->ejercicio)
						->orderBy('programacion_presupuesto.upp')
						->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
						->distinct()
						->get();
	
					foreach ($activs as $key) {
						$area = '"' . strval($key->finalidad) . '-' . strval($key->funcion) . '-' . strval($key->subfuncion) . '-' . strval($key->eje) . '-' . strval($key->linea) . '-' . strval($key->programaSec) . '-' . strval($key->tipologia) . '-' . strval($key->programa) . '-' . strval($key->subprograma) . '-' . strval($key->clv_proyecto) . '"';
						$entidad = '"' . strval($upp) . '-' . strval($key->subsec) . '-' . strval($request->ur_filter) . '"';
						$clave = '"' . strval($upp) . strval($key->subsec) . strval($request->ur_filter) . '-' . strval($key->finalidad) . strval($key->funcion) . strval($key->subfuncion) . strval($key->eje) . strval($key->linea) . strval($key->programaSec) . strval($key->tipologia) . strval($key->programa) . strval($key->subprograma) . strval($key->clv_proyecto) . '"';
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
		if($_upp != 0){
			$upp = $_upp != null?$_upp:auth::user()->clv_upp;
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
				->where('ejercicio', 2024)->get();
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
			
		return ["urs"=>$urs,"tAct"=>$tAct];
	}
	public function getUpps()
	{
		$upps = DB::table('v_epp')
			->select(
				'id',
				'clv_upp',
				DB::raw('CONCAT(clv_upp, " - ", upp) AS upp')
			)->distinct()
			->orderBy('clv_upp')
			->groupByRaw('clv_upp')
			->where('ejercicio', 2024)->get();
		return $upps;
	}
	public function getFyA($area,$entidad)
	{
		$areaAux=explode( '-', $area);
		$entidadAux=explode( '-', $entidad);
		 $fondos = DB::table('programacion_presupuesto')
			->leftJoin('fondo', 'fondo.clv_fondo_ramo', 'programacion_presupuesto.fondo_ramo')
			->select(
				'fondo.id',
				'programacion_presupuesto.fondo_ramo as clave',
				DB::raw('CONCAT(programacion_presupuesto.fondo_ramo, " - ", fondo.ramo) AS ramo')
			)
			->where('fondo.deleted_at', null)
			->where('programacion_presupuesto.finalidad',$areaAux[0])
			->where('programacion_presupuesto.funcion',$areaAux[1])
			->where('programacion_presupuesto.subfuncion',$areaAux[2])
			->where('programacion_presupuesto.eje',$areaAux[3])
			->where('programacion_presupuesto.linea_accion',$areaAux[4])
			->where('programacion_presupuesto.programa_sectorial',$areaAux[5])
			->where('programacion_presupuesto.tipologia_conac',$areaAux[6])
			->where('programacion_presupuesto.upp',$entidadAux[0])
            ->where('programacion_presupuesto.ur', $entidadAux[2])
            ->where('programa_presupuestario', $areaAux[7])
            ->where('subprograma_presupuestario', $areaAux[8])
			->where('proyecto_presupuestario', $areaAux[9])
			->groupByRaw('clave')
			->where('ejercicio',2024)
			->get();
			$activ = DB::table('actividades_mir')
			->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
			->select(
				'actividades_mir.id',
				'actividades_mir.clv_actividad as clave',
				DB::raw('CONCAT(clv_actividad, " - ",actividad) AS actividad')
			)
			->where('actividades_mir.deleted_at', null)
			->where('proyectos_mir.area_funcional',str_replace ( "-", '', $area))
            ->where('proyectos_mir.entidad_ejecutora',str_replace ( "-", '', $entidad))
			->groupByRaw('clave')->get(); 
		return ['fondos'=> $fondos ,"activids"=>$activ ];
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
		/* $activ = Http::acceptJson()->get('https://pokeapi.co/api/v2/pokemon/');
					$res = json_decode($activ->body()); */
		
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
	public function createMeta(Request $request)
	{
		Controller::check_permission('postMetas');
		$meta = Metas::create([
			'actividad_id' => $request->sel_actividad,
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
		]);
		if ($meta) {
			$res=["status" => true, "mensaje" => ["icon"=>'success',"text"=>'La acción se ha realizado correctamente',"title"=>"Éxito!"]];
			return response()->json($res,200);
		}else {
			$res=["status" => false, "mensaje" => ["icon"=>'Error',"text"=>'Hubo un problema al querer realizar la acción, contacte a soporte',"title"=>"Error!"]];
			return response()->json($res,200);
		}

	}
	public function deleteMeta(Request $request)
	{
		Controller::check_permission('deleteMetas');
		$mDelete=Metas::where('id', $request->id)->delete();
		if ($mDelete) {
			$res=["status" => true, "mensaje" => ["icon"=>'success',"text"=>'La acción se ha realizado correctamente',"title"=>"Éxito!"]];
			return response()->json($res,200);
		}else {
			$res=["status" => false, "mensaje" => ["icon"=>'Error',"text"=>'Hubo un problema al querer realizar la acción, contacte a soporte',"title"=>"Error!"]];
			return response()->json($res,200);
		}
	}
	public function updateMeta($id)
	{
		Controller::check_permission('putMetas', false);
		$query = Metas::where('id', $id)->get();
		return $query;
	}
	public function exportExcel($upp)
	{
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/
		return Excel::download(new MetasExport($upp), 'Proyecto con actividades.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	public function proyExcel()
	{
		Controller::check_permission('getClaves');
		/*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/
		return Excel::download(new MetasCargaM(), 'CargaMasiva.xlsx');
	}
	public function pdfView($upp)
	{
		Controller::check_permission('getClaves');
		$data = $this->getActiv($upp);
		for ($i=0; $i <count($data); $i++) { 
			unset($data[$i][19]);
			$data=array_values($data);
		}
		return view('calendarizacion.metas.proyectoPDF', compact('data'));
	}

	public function exportPdf($upp)
	{
		ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
		Controller::check_permission('getClaves');
		$data = $this->getActiv($upp);
		for ($i=0; $i <count($data); $i++) { 
			unset($data[$i][19]);
			$data=array_values($data);
		}
		view()->share('data', $data);
		$pdf = PDF::loadView('calendarizacion.metas.proyectoPDF')->setPaper('a4', 'landscape');
		return $pdf->download('Proyecto con actividades.pdf');
	}
	public function downloadActividades($upp)
	{
		$date = Carbon::now();
		$request = array(
			"anio" => $date->year,
			// "corte" => $date->format('Y-m-d'),
			// "logoLeft" => public_path() . 'img\escudo.png',
			// "logoRight" => public_path() . 'img\escudo.png',
			"UPP" => $upp,
		);
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
		$report_path = app_path() . "/Reportes/" . $report . ".jasper";
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
			return response()->json('done',200);
		}else {
			return response()->json('error',200);
		}

	}
	public function importPlantilla(Request $request)
	{
		DB::beginTransaction();
		try {
			$assets = $request->file('cmFile');
			if ($xlsx = SimpleXLSX::parse($assets)) {
				$filearray = $xlsx->rows();
				array_shift($filearray);
				$resul = FunFormats::saveImport($filearray);
				if($resul['icon']=='success'){
					DB::commit();
				}
				return response()->json($resul);
			}
			
		} catch (\Exception $e) {
			DB::rollback();
		}


	}
	public function checkCombination($upp)
	{
		$anio = DB::table('cierre_ejercicio_metas')
			->select(
				'estatus',
				'ejercicio'
			)
			->where('clv_upp', '=', $upp)
			->get();

			$proyecto = DB::table('actividades_mir')
				->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
				->select(
					'actividades_mir.id',
					'proyectos_mir.area_funcional AS area',
					'proyectos_mir.clv_upp'
				)
				->where('actividades_mir.deleted_at', null)
				->where('proyectos_mir.deleted_at', null)
				->where('proyectos_mir.clv_upp', $upp);
		$metas=DB::table('metas')
			->leftJoinSub($proyecto, 'pro', function ($join) {
				$join->on('metas.actividad_id', '=', 'pro.id');
			})
			->select(
				'metas.id',
				'metas.estatus'
			)
			->where('metas.estatus',1)
			->where('pro.clv_upp', '=', $upp)
			->get();
		if (Auth::user()->id_grupo == 1 || $anio[0]->estatus == 'Abierto') {
			if (count($metas)==0 || Auth::user()->id_grupo == 1 ) {
				$activs = DB::table("programacion_presupuesto")
					->select(
						'programa_presupuestario AS programa',
						DB::raw('CONCAT(upp,subsecretaria,ur) AS area'),
						DB::raw('CONCAT(finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario) AS clave')
					)
					->where('programacion_presupuesto.upp', '=', $upp)
					->where('programacion_presupuesto.ejercicio', '=', 2024)
					->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
					->distinct()
					->where('estado', 1)
					->groupByRaw('programa_presupuestario')->get();
				if (count($activs)) {
					$auxAct = count($activs);
					$index = 0;
					foreach ($activs as $key) {
						$proyecto = DB::table('actividades_mir')
							->leftJoin('proyectos_mir', 'proyectos_mir.id', 'actividades_mir.proyecto_mir_id')
							->select(
								'actividades_mir.id',
								'proyectos_mir.area_funcional AS area'
							)
							->where('actividades_mir.deleted_at', null)
							->where('proyectos_mir.deleted_at', null)
							->where('proyectos_mir.clv_upp', $upp)
							->where('proyectos_mir.area_funcional', $key->clave)
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
					return ["status" => false, "mensaje" => 'Es necesario capturar y confirmar tus claves presupuestarias', "estado" => false];
				}
			}else{
				return ["status" => false, "mensaje" => 'Las metas ya estan confirmadas', "estado" => true];
			}
		} else {
			return ["status" => false, "mensaje" => 'La captura de metas esté cerrada', "estado" => true];
		}
	}

	/* 		foreach ($activs as $key ) {
				ProyectosMir::create([
					'clv_upp'=>$upp,
					'entidad_ejecutora'=>$key->area,
					'clv_programa'=>$key->programa,
					'area_funcional'=>$key->clave,
					'nivel'=>1,
					'objetivo'=>1,
					'indicador'=>1,
					'definicion_indicador'=>1,
					'metodo_calculo'=>1,
					'descripcion_metodo'=>1,
					'tipo_indicador'=>'Estratégico',
					'unidad_medida'=>'Porcentaje',
					'dimension'=>'Eficada',
					'comportamiento_indicador'=>'Ascendente',
					'frecuencia_medicion'=>'Quincenal',
					'medios_verificacion'=>1,
					'lb_valor_absoluto'=>1,
					'lb_valor_relativo'=>1,
					'lb_anio'=>1,
					'lb_periodo_i'=>1,
					'lb_periodo_f'=>1,
					'mp_valor_absoluto'=>1,
					'mp_valor_relativo'=>1,
					'mp_anio'=>1,
					'mp_periodo_i'=>1,
					'mp_periodo_f'=>1,
					'supuestos'=>1,
					'estrategias'=>1,
					'ejercicio'=>2024
				]);
			} */

			
		

		/* 	$proyecto = DB::table('proyectos_mir')
			->select('id')
			->where('deleted_at', null)
			->where('ejercicio',2024)
			->get();
			for ($i=0; $i <count($proyecto); $i++) {
			ActividadesMir::create([
				'proyecto_mir_id'=>$proyecto[$i]->id,
				'clv_actividad'=> $i>=10?$i:'0'.$i.'-2024',
				'actividad'=>'Prueba'.$i.'2024',
				'objetivo'=>$i,
				'indicador'=>$i,
				'definicion_indicador'=>$i,
				'metodo_calculo'=>$i,
				'descripcion_metodo'=>$i,
				'tipo_indicador'=>'Estratégico',
				'unidad_medida'=>'Porcentaje',
				'dimension'=> 'Ascendente',
				'comportamiento_indicador'=>'Quincenal',
				'frecuencia_medicion'=>$i,
				'medios_verificacion'=>$i,
				'lb_valor_absoluto'=>$i,
				'lb_valor_relativo'=>$i,
				'lb_anio'=>$i,
				'lb_periodo_i'=>$i,
				'lb_periodo_f'=>$i,
				'mp_valor_absoluto'=>$i,
				'mp_valor_relativo'=>$i,
				'mp_anio'=>$i,
				'mp_periodo_i'=>$i,
				'mp_periodo_f'=>$i,
				'supuestos'=>$i,
				'estrategias'=>$i,
				'ejercicio'=>2024
			]);
			}*/
	


	public function descargaReporteFirma(Request $request){
		try {
		//generamos el nombre del archivo a guardar
		$nameCer = substr(str_replace(" ", "_", $request->cer->getClientOriginalName()), 0, -4);
		//si el nombre es mayor a 55 caracteres se toman solo los primeros 55
		if (strlen($nameCer) > 55) {
			$nameCer = substr($nameCer, 0, 55);
		}
		$fileExtCer = $request->cer->getClientOriginalExtension();
		$nameSaveCer = $nameCer.".".$fileExtCer;
		//generamos el nombre del archivo a guardar
		$nameKey = substr(str_replace(" ", "_", $request->key->getClientOriginalName()), 0, -4);
		//si el nombre es mayor a 55 caracteres se toman solo los primeros 55
		if (strlen($nameKey) > 55) {
			$nameKey = substr($nameKey, 0, 55);
		}
		$fileExtKey = $request->key->getClientOriginalExtension();
		$nameSaveKey = $nameKey .".".$fileExtKey;
		//crear un path para los reportes... storage\app\public\reportes\Claveprivada_FIEL_HEHF7712015Z2_20220324_105350.key
		$cerPath = Storage::path('public/reportes/'.$nameSaveCer);
		$keyPath = Storage::path('public/reportes/'.$nameSaveKey);
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
		$cerFile ='';
		$keyFile = '';
		//obtenemos el contenido de los archivos...
		if (File::exists($cerPath)) {
			$cerFile = file_get_contents($cerPath);
		}
		if (File::exists($keyPath)) {
			$keyFile = file_get_contents($keyPath);
		}
		$pdf ='';
		$ruta = public_path() . "/reportes/Reporte_Calendario_UPP.pdf";
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
			$response = $response->attach('pdf[]',$pdf,'Reporte_Calendario_UPP.pdf');
			$response = $response->attach('cer',$cerFile,$nameSaveCer);
			$response = $response->attach('key',$keyFile,$nameSaveKey);
			$response = $response->post('http://10.0.250.55/firmaElectronica/firmaElectronica/public/api/firmarPDF',[
			'pass' =>'12345678a',
			'cadenaOrigen'=>'prueba',
			'clave_tramite'=>'IAP01',
			'encabezado'=>1]);
			if ($response &&  $response[0]['pdfFirmado']) {
				$file = $response[0]['pdfFirmado'];
				$response = ['estatus'=>'done','data'=>$file];
				return $response;
			}else {
				$responseError = ['estatus'=>'error','data'=>$response];
				return $responseError;
			}
		}else {
			$responseError = ['estatus'=>'error','data'=>$token];
			return $responseError;
		}
		} catch (\Exception $exp) {
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
		 
	}
}
