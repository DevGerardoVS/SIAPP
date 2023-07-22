<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Imports\utils\FunFormats;
use App\Http\Controllers\Controller;
use App\Models\calendarizacion\ProyectosMir;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MetasExport;
use App\Exports\Calendarizacion\MetasCargaM;
use App\Models\calendarizacion\Metas;
use App\Models\catalogos\CatPermisos;
use Auth;
use DB;
use Log;
use Illuminate\Database\Query\JoinClause;
use App\Helpers\Calendarizacion\MetasHelper;
use Illuminate\Support\Facades\Schema;
use Mockery\Undefined;
use PDF;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Shuchkin\SimpleXLSX;
use Symfony\Component\Console\Helper\Table;



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
	public function getActiv($upp)
	{
		$query = MetasHelper::actividades($upp);
		$dataSet = [];
		foreach ($query as $key) {
			$accion = '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>';
			$i = array(
				$key->finalidad,
				$key->funcion,
				$key->subfuncion,
				$key->eje,
				$key->linea,
				$key->programaSec,
				$key->tipologia,
				$key->upp,
				$key->ur,
				$key->programa,
				$key->subprograma,
				$key->proyecto,
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
		$upp = isset($request->upp_filter) ?$request->upp_filter:auth::user()->clv_upp;
		
		if ($request->ur_filter != null && $upp !='') {
			//$this->checkCombination($upp);
			 $anio = DB::table('cierre_ejercicio_metas')
				->select('ejercicio')
				->where('estatus','=','Abierto')
				->where('clv_upp','=',$upp)
				->get();
				if(count($anio)){
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
				->where('programacion_presupuesto.ejercicio', '=', 2023)
				->where('v_epp.ejercicio', '=', 2023)
				->orderBy('programacion_presupuesto.upp')
				->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
				->distinct()
				->get();
				
			foreach ($activs as $key) {
				$area= '"'.strval($key->finalidad).'-'.strval($key->funcion) .'-'.strval($key->subfuncion).'-'.strval($key->eje).'-'.strval($key->linea).'-'.strval($key->programaSec) .'-'.strval($key->tipologia).'-'.strval($key->programa) .'-'.strval($key->subprograma).'-'. strval($key->clv_proyecto).'"';
					$entidad = '"' . strval($upp).'-'. strval($key->subsec) .'-'. strval($request->ur_filter) . '"';
				$clave ='"'.strval($upp) .strval($key->subsec) .strval($request->ur_filter).'-'.strval($key->finalidad).strval($key->funcion) . strval($key->subfuncion) . strval($key->eje).strval($key->linea). strval($key->programaSec) . strval($key->tipologia) . strval($key->programa) . strval($key->subprograma). strval($key->clv_proyecto).'"';
				Log::debug($clave);
				$accion = "<div class'form-check'><input class='form-check-input clave' type='radio' name='clave' id='".$clave."' value='".$clave."' onchange='dao.getFyA(".$area.",".$entidad.")' ></div>";
				$dataSet[] = [$key->finalidad,$key->funcion, $key->subfuncion, $key->eje, $key->linea, $key->programaSec, $key->tipologia, $key->programa, $key->subprograma, $key->proyecto, $accion];
			}
		}
		}
		return response()->json(["dataSet" => $dataSet], 200);
	}
	public function getUrs($_upp)
	{
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
			->where('ejercicio', 2023)->get();

			$tAct = DB::table('tipo_actividad_upp')
			->select(
				'Continua',
				'Acumulativa',
				'Especial'
			)
			->where('deleted_at', null)
			->orderBy('clv_upp')
			->where('clv_upp', $upp)
			->get();
		return ["urs"=>$urs,"tAct"=>$tAct[0]];
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
			->where('ejercicio', 2023)->get();
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
			->where('ejercicio',2023)
			->get();
			Log::debug($fondos);
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
			Log::debug($activ);
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
		return $meta;

	}
	public function deleteMeta(Request $request)
	{
		Controller::check_permission('deleteMetas');
		Metas::where('id', $request->id)->delete();


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
		$data = MetasHelper::actividades($upp);
		return view('calendarizacion.metas.proyectoPDF', compact('data'));
	}

	public function exportPdf($upp)
	{
		ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
		Controller::check_permission('getClaves');
		$data = MetasHelper::actividades($upp);
		view()->share('data', $data);
		$pdf = PDF::loadView('calendarizacion.metas.proyectoPDF')->setPaper('a4', 'landscape');
		return $pdf->download('Proyecto con actividades.pdf');
	}
	public function downloadActividades($upp)
	{
		$date = Carbon::now();
		$request = array(
			"anio" => $date->year,
			"corte" => $date->format('Y-m-d'),
			"logoLeft" => public_path() . 'img\escudo.png',
			"logoRight" => public_path() . 'img\escudo.png',
			"UPP" => $upp,
		);
		log::debug($request);
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

		$ruta = public_path() . "/Reportes";
		//Eliminación si ya existe reporte
		if (File::exists($ruta . "/" . $report . ".pdf")) {
			File::delete($ruta . "/" . $report . ".pdf");
		}
		$report_path = app_path() . "/Reportes/" . $report . ".jasper";
		$format = array('pdf');
		$output_file = public_path() . "/Reportes";

		$parameters = $request;

		$database_connection = \Config::get('database.connections.mysql');


		$jasper = new PHPJasper;
		$jasper->process(
			$report_path,
			$output_file,
			$format,
			$parameters,
			$database_connection
		)->output();
		dd($jasper);
		return Response::make(file_get_contents(public_path() . "/Reportes/" . $report . ".pdf"), 200, [
			'Content-Type' => 'application/pdf'
		]);
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
		$proyecto = DB::table('proyectos_mir')
			->select(
                DB::raw('CONCAT(clv_finalidad, "-",clv_funcion,"-",clv_subfuncion,"-",clv_eje,"-",clv_linea_accion,"-",clv_programa_sectorial,"-",clv_tipologia_conac,"-",clv_upp,"-",clv_ur,"-",clv_programa,"-",clv_subprograma,"-",clv_proyecto) AS clave')
			)->where('deleted_at', null)
			->where('clv_upp',$upp)
			->get();
		Log::debug($proyecto);
		$activs = DB::table("programacion_presupuesto")
			->select(
				DB::raw('CONCAT(finalidad, "-",funcion,"-",subfuncion,"-",eje,"-",linea_accion,"-",programa_sectorial,"-",tipologia_conac,"-",upp,"-",ur,"-",programa_presupuestario,"-",subprograma_presupuestario,"-",proyecto_presupuestario) AS clave')
			)
			->where('programacion_presupuesto.upp', '=', $upp)
			->where('programacion_presupuesto.ejercicio', '=', 2023)
			->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
			->distinct()
			->groupByRaw('programa_presupuestario')->get();
			Log::debug($activs);
		/* $name = 'temp' . Auth::user()->username; 
		Schema::create($name, function (Blueprint $table) {
			$table->increments('id');
			$table->string('clave');
			$table->string('clv_upp', 3)->nullable(false);
			$table->string('clv_ur', 2)->nullable(false);
			$table->string('clv_finalidad', 1)->nullable(false);
			$table->string('clv_funcion', 1)->nullable(false);
			$table->string('clv_subfuncion', 1)->nullable(false);
			$table->string('clv_eje', 1)->nullable(false);
			$table->string('clv_linea_accion', 2)->nullable(false);
			$table->string('clv_programa_sectorial', 1)->nullable(false);
			$table->string('clv_tipologia_conac', 1)->nullable(false);
			$table->string('clv_programa', 2)->nullable(false);
			$table->string('clv_subprograma', 3)->nullable(false);
			$table->string('clv_proyecto', 3)->nullable(false);
			$table->integer('ejercicio')->default(null);
		}); */
	/* 	$activs = DB::table("programacion_presupuesto")
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
				'programacion_presupuesto.ur',
			)
			->where('programacion_presupuesto.upp', '=', $upp)
			->where('programacion_presupuesto.ejercicio', '=', 2023)
			->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario')
			->distinct()
			->groupByRaw('programa_presupuestario')->get();

	 	foreach ($activs as $key) {
			$clave =''. strval($key->finalidad) . '-' .strval($key->funcion) . '-' . strval($key->subfuncion) . '-' . strval($key->eje). '-' .strval($key->linea).'-'. strval($key->programaSec) . '-' .strval($key->tipologia) .'-'. strval($upp) . '-' .strval($key->ur,) . '-' . strval($key->programa) . '-' . strval($key->subprograma). '-' .strval($key->clv_proyecto).'';
 
			ProyectosMir::create([
				'clv_upp' => $upp,
				'clv_ur' => $key->ur,
				'clv_finalidad' => $key->finalidad,
				'clv_funcion' => $key->funcion,
				'clv_subfuncion' => $key->subfuncion,
				'clv_eje' => $key->eje,
				'clv_linea_accion' => $key->linea,
				'clv_programa_sectorial' => $key->programaSec,
				'clv_tipologia_conac' => $key->tipologia,
				'clv_programa' => $key->programa,
				'clv_subprograma' => $key->subprograma,
				'clv_proyecto' => $key->clv_proyecto,
				'ejercicio'=>2023
			]); */
			/*  	DB::table($name)->insert([
				'clave'=>$clave,
				'clv_upp' => $upp,
				'clv_ur' => $key->ur,
				'clv_finalidad' => $key->finalidad,
				'clv_funcion' => $key->funcion,
				'clv_subfuncion' => $key->subfuncion,
				'clv_eje' => $key->eje,
				'clv_linea_accion' => $key->linea,
				'clv_programa_sectorial' => $key->programaSec,
				'clv_tipologia_conac' => $key->tipologia,
				'clv_programa' => $key->programa,
				'clv_subprograma' => $key->subprograma,
				'clv_proyecto' => $key->clv_proyecto,
			]); 
		}  */

		//in_array ($proyecto, 'b');
	}

}
