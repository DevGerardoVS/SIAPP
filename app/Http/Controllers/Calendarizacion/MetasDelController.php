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
use App\Models\MmlMir;

use App\Models\Catalogo;


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
    public static function getActivDelegacion($upp, $anio){
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
		//Controller::check_permission('getMetasDelegacion');
		Controller::check_assign(1);
		DB::beginTransaction();
		try {

			$flag = false;
			if (Auth::user()->id_grupo == 5) {

				Schema::create('metas_temp', function (Blueprint $table) {
					$table->temporary();
					$table->increments('id');
					$table->string('clave', 25)->nullable(false);
					$table->string('upp', 25)->nullable(false);
					$table->string('fila', 10)->nullable(false);
				});
				Schema::create('metas_temp_Nomir', function (Blueprint $table) {
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
}