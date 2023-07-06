<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Models\Catalogo;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use App\Exports\MetasExport;
use App\Exports\Calendarizacion\MetasCargaM;

use App\Models\calendarizacion\Metas;
use App\Models\catalogos\CatPermisos;
use Auth;
use DB;
use Log;
use Illuminate\Database\Query\JoinClause;
use App\Helpers\Calendarizacion\MetasHelper;
use PDF;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use App\Imports\MetasImport;



class MetasController extends Controller
{
	//Consulta Vista Usuarios
	public function getIndex()
	{
		return view('calendarizacion.metas.index');
	}
	public function getProyecto()
	{
		return view('calendarizacion.metas.proyecto');
	}
	public function getActiv()
	{
		$query = MetasHelper::actividades();
		$dataSet = [];
		foreach ($query as $key) {
			$accion = '<button title="Modificar meta" class="btn btn-sm"onclick="dao.editarMeta(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></button>&nbsp;' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>&nbsp;';
			$i = array(
				$key->ur,
				$key->programa,
				$key->subprograma,
				$key->proyecto,
				$key->fondo,
				$key->actividad,
				$this->actividad($key->tipo),
				$key->total,
				$key->cantidad_beneficiarios,
				$key->beneficiario_id,
				$key->unidad_medida_id,
				$accion
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getNames($id)
	{
		$cvl = DB::table('proyectos_mir')
			->select(
				'id',
				'clv_upp',
				'clv_ur',
				'clv_programa',
				'clv_subprograma',
				'clv_proyecto',
			)
			->where('proyectos_mir.id', '=', $id)
			->where('proyectos_mir.deleted_at', '=', null)->get();
		$query = DB::table('v_epp')
			->select(
				'ur',
				'programa',
				'subprograma',
				'proyecto'
			)
			->where('v_epp.clv_ur', '=', $cvl[0]->clv_ur)
			->where('v_epp.clv_upp', '=', $cvl[0]->clv_upp)
			->where('v_epp.clv_programa', '=', $cvl[0]->clv_programa)
			->where('v_epp.clv_subprograma', '=', $cvl[0]->clv_subprograma)
			->where('v_epp.clv_proyecto', '=', $cvl[0]->clv_proyecto)->get();
		$dataSet = [];
		foreach ($query as $key) {
			$i = array(
				$key->ur,
				$key->programa,
				$key->subprograma,
				$key->proyecto,
				" ",
			);
			$dataSet[] = $i;
		}
		return $dataSet[0];
	}
	public function getMetasP(Request $request)
	{
		$dataSet = [];
		$upp = auth::user()->clv_upp;
		if ($request->ur_filter != null) {
			$activs = DB::table("programacion_presupuesto")
				->leftJoin('v_epp', 'v_epp.clv_proyecto', '=', 'programacion_presupuesto.proyecto_presupuestario')
				->select(
					'programacion_presupuesto.id',
					'programa_presupuestario as programa',
					'subprograma_presupuestario as subprograma',
					'v_epp.proyecto as proyecto'
				)
				->where('programacion_presupuesto.ur', '=', $request->ur_filter)
				->groupByRaw('programa_presupuestario');
			if ($upp != null) {
				$activs = $activs->where('programacion_presupuesto.upp', '=', $upp);
			}
			$activs = $activs->get();
			log::debug("UPP:".$upp."- UR:".$request->ur_filter);

			foreach ($activs as $key) {
				$accion = '<div class="form-check"><input class="form-check-input" type="radio" name="proyecto" id="proyecto" value="' . $key->id . '" checked><label class="form-check-label" for="exampleRadios1"></label></div>';
				$dataSet[] = [$key->programa, $key->subprograma, $key->proyecto, $accion];
			}
		}
		return response()->json(["dataSet" => $dataSet], 200);
	}
	public function getMetas()
	{
		$activs = DB::table("actividades_mir")
			->select(
				'actividades_mir.id',
				'proyecto_mir_id',
				'clv_actividad',
				'actividad',
				'metas.total AS total'
			)
			->leftJoin('metas', 'metas.actividad_id', '=', 'actividades_mir.id')
			->where('metas.total', '!=', null)
			->where('actividades_mir.deleted_at', '=', null);

		$query = DB::table('proyectos_mir')
			->leftJoinSub($activs, 'act', function ($join) {
				$join->on('proyectos_mir.id', '=', 'act.proyecto_mir_id');
			})
			->select(
				'proyectos_mir.id',
				'proyectos_mir.clv_upp AS upp',
				'proyectos_mir.clv_ur AS ur',
				'proyectos_mir.clv_programa AS programa',
				'proyectos_mir.clv_subprograma AS subprograma',
				'proyectos_mir.clv_proyecto AS proyecto',
				'catalogo.descripcion',
				'programacion_presupuesto.fondo_ramo AS fondo',
				DB::raw(
					"SUM(enero
				+ febrero
				+ marzo 
				+ abril
				+ mayo
				+ junio
				+ julio
				+ agosto
				+ septiembre
				+ octubre
				+ noviembre
				+ diciembre) 
				AS presupuesto"
				),
				'act.actividad',
				'act.total'
			)
			->leftJoin('catalogo', 'catalogo.clave', '=', 'proyectos_mir.clv_proyecto')
			->where('catalogo.grupo_id', '=', 18)
			->leftJoin("actividades_mir", 'actividades_mir.proyecto_mir_id', '=', 'proyectos_mir.id')
			->join('programacion_presupuesto', function (JoinClause $join) {
				$join->on('proyectos_mir.clv_upp', '=', 'programacion_presupuesto.upp')
					->orOn('programacion_presupuesto.programa_presupuestario', '=', 'proyectos_mir.clv_programa')
					->orOn('programacion_presupuesto.subprograma_presupuestario', '=', 'proyectos_mir.clv_subprograma')
					->orOn('programacion_presupuesto.proyecto_presupuestario', '=', 'proyectos_mir.clv_proyecto');
			})->groupByRaw('proyectos_mir.clv_upp,proyectos_mir.clv_ur,proyectos_mir.clv_programa,proyectos_mir.clv_subprograma,proyectos_mir.clv_proyecto,catalogo.descripcion,programacion_presupuesto.fondo_ramo')
			->where('proyectos_mir.deleted_at', '=', null)->get();
		$dataSet = [];

		foreach ($query as $key) {
			$accion = $key->actividad != "" ? '<buttton type="button" class="btn btn-success"  onclick="dao.editarMeta(' . $key->id . ')" class="button"><i  class="fa fa-pencil"></i></buttton>' :
				'<a type="button" class="btn btn-primary"  href="/calendarizacion/proyecto/' . $key->id . '" class="button"><i class="fa-plus">Agregar</i></a>';
			$i = array(
				$key->ur,
				$key->programa,
				$key->subprograma,
				$key->descripcion,
				$key->fondo,
				$key->presupuesto,
				$key->actividad,
				$key->total,
				$accion,
			);
			$dataSet[] = $i;
		}
		return ['dataSet' => $dataSet];
	}
	public function getProyect($id = 0)
	{
		$query = DB::table('adm_users')
			->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', DB::raw('CONCAT(adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as nombre_completo'), 'adm_users.celular', DB::raw('ifnull(adm_grupos.nombre_grupo, "Sudo") as perfil'), 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo'))
			->leftJoin('adm_rel_user_grupo', 'adm_users.id', '=', 'adm_rel_user_grupo.id_usuario')
			->leftJoin('adm_grupos', 'adm_rel_user_grupo.id_grupo', '=', 'adm_grupos.id')
			->where('adm_users.deleted_at', '=', null)
			->orderby('adm_users.estatus');

		if ($id != 0) {
			$query = $query->where('adm_users.id', '=', $id);
		}

		$query = $query->get();
		$dataSet = [];

		foreach ($query as $key) {
			$accion = '<button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false" ><i class="fa-plus"></i>Agregar</button>';
			$i = array(
				$key->username,
				$key->email,
				$key->nombre_completo,
				$key->celular,
				$key->perfil,
				$key->estatus == 1 ? "Activo" : "Inactivo",
				" ",
				" ",
				$accion,
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getUrs()
	{
		$upp = auth::user()->clv_upp;
		$urs = DB::table('v_epp')
			->select(
				'id',
				'clv_ur',
				'ur'
			)->distinct()
			->groupByRaw('clv_ur');
			if($upp!=NULL){
			$urs = $urs->where('clv_upp', $upp);
			}
			$urs =$urs->get();
		return $urs;
	}
	public function getProgramas($ur)
	{
		$urs = DB::table('v_epp')
			->select(
				'id',
				'clv_programa',
				'programa'
			)
			->where('clv_ur', $ur)
			->get();
		return $urs;
	}
	public function getSelects()
	{
		$upp = auth::user()->clv_upp;
		$uMed = DB::table('unidades_medida')
			->select(
				'id as clave',
				'unidad_medida'
			)
			->where('deleted_at', null)
			->get();
		$fondos = DB::table('programacion_presupuesto')
			->leftJoin('fondo', 'fondo.clv_fondo_ramo', 'programacion_presupuesto.fondo_ramo')
			->select(
				'fondo.id',
				'programacion_presupuesto.fondo_ramo as clave',
				'fondo.ramo',
			)

			->where('fondo.deleted_at', null)
			->distinct();
			if($upp!= NULL){
				$fondos =$fondos->where('programacion_presupuesto.upp', '=', $upp);
			}
			$fondos =$fondos->get();
		/* $activ = Http::acceptJson()->get('https://pokeapi.co/api/v2/pokemon/');
			  $res = json_decode($activ->body()); */
		$activ = DB::table('actividades_mir')
			->select(
				'id',
				'clv_actividad',
				'actividad'
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
		$tAct = ["Acumulativa", "Continua", "Especial"];
		return ["unidadM" => $uMed, "fondos" => $fondos, "beneficiario" => $bene, "actividades" => $activ, "activids" => $tAct];
	}
	public function createMeta(Request $request)
	{
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
	public function getMetasXp()
	{
		$query = DB::table('metas')
			->leftJoin('actividades_mir', 'actividades_mir.id', '=', 'metas.actividad_id')
			->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
			->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
			->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medida_id')
			->select(
				'metas.id',
				'actividades_mir.actividad',
				'total',
				'fondo.ramo',
				'tipo',
				'cantidad_beneficiarios',
				'beneficiarios.beneficiario',
				'unidades_medida.unidad_medida',

			)->where('metas.deleted_at', '=', null);
		$query = $query->get();
		$dataSet = [];
		foreach ($query as $key) {
			$accion = '<a data-toggle="modal" data-target="#addActividad" data-backdrop="static" data-keyboard="false" title="Modificar meta"
			class="btn btn-sm"onclick="dao.editarUsuario(' . $key->id . ')">' .
				'<i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' .
				'<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar(' . $key->id . ')">' .
				'<i class="fa fa-trash" style="color:B40000;" ></i></button>&nbsp;';
			$i = array(
				$key->actividad,
				$key->total,
				$this->actividad($key->tipo),
				$key->cantidad_beneficiarios,
				$key->beneficiario,
				$key->unidad_medida,
				$key->ramo,
				$accion,
			);
			$dataSet[] = $i;
		}
		return ['dataSet' => $dataSet];
	}
	public function actividad($id)
	{
		switch ($id) {
			case 0:
				return 'Acumulativa';
			case 1:
				return 'Continua';
			case 2:
				return 'Especial';
			default:
				break;
		}

	}
	public function deleteMeta(Request $request)
	{
		//Controller::check_permission('deleteUsuarios');
		Metas::where('id', $request->id)->delete();
		
		 
	}
	public function updateMeta($id)
	{
		//Controller::check_permission('putUsuarios', false);
		Log::debug($id);
		$query = Metas::where('id', $id)->get();
		return $query;
	}
	public function exportExcel(Request $request)
    {
		   /*Si no coloco estas lineas Falla*/
		   ob_end_clean();
		   ob_start();
		   /*Si no coloco estas lineas Falla*/
        return Excel::download(new MetasExport(), 'Proyecto con actividades.xlsx',\Maatwebsite\Excel\Excel::XLSX);
    }
	public function proyExcel()
    {
		   /*Si no coloco estas lineas Falla*/
		   ob_end_clean();
		   ob_start();
		   /*Si no coloco estas lineas Falla*/
        return Excel::download(new MetasCargaM(), 'CargaMasiva.xlsx');
    }
	public function pdfView()
    {
		$data = MetasHelper::actividades();
		return view('calendarizacion.metas.proyectoPDF', compact('data'));
    }
	
	public function exportPdf(Request $request)
    {
		$data = MetasHelper::actividades();
		  view()->share('data',$data);
		$pdf = PDF::loadView('calendarizacion.metas.proyectoPDF');
		return $pdf->download('Proyecto con actividades.pdf');
    }
 	public function downloadActividades()
	{
		$date=Carbon::now();
		$upp = CatPermisos::where('id', auth::user()->id_ente)->firstOrFail();
		$request=array(
			"anio"=>$date->year,
			"corte"=>$date->format('Y-m-d'),
			"logoLeft"=> public_path().'img\escudo.png',
			"logoRight"=>public_path().'img\escudo.png',
			"UPP"=>$upp->clv_upp,
            );
		log::debug($request);
		return $this->jasper($request);

	} 
	public function jasper($request){ 
        date_default_timezone_set('America/Mexico_City');
        
        setlocale(LC_TIME, 'es_VE.UTF-8','esp');
        $fecha = date('d-m-Y');
        $marca = strtotime($fecha);
        $fechaCompleta = strftime('%A %e de %B de %Y', $marca);
        $report =  "Reporte_Calendario_UPP";
      
        $ruta = public_path()."/Reportes";
        //Eliminación si ya existe reporte
        if(File::exists($ruta."/".$report.".pdf")) {
            File::delete($ruta."/".$report.".pdf");
        }
        $report_path = app_path() ."/Reportes/".$report.".jasper";
        $format = array('pdf');
        $output_file =  public_path()."/Reportes";

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
        return Response::make(file_get_contents(public_path()."/Reportes/".$report.".pdf"), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }
	public function importPlantilla(Request $request)
	{
		
		DB::beginTransaction();
		try {
			ini_set('max_execution_time', 1200);
			$assets = $request->file('cmFile');
			$import = new MetasImport();
			$import->onlySheets('Metas');
			Excel::import($import, $assets, 'UTF-8');
			DB::commit();
			return redirect('/')->with('success', 'All good!');
		} catch (\Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}

	}
}
