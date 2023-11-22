<?php

namespace App\Http\Controllers;

use App\Helpers\QueryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class ReporteController extends Controller
{
    public function indexPlaneacion()
    {
        Controller::check_permission('getCaptura');
        $db = $_ENV['DB_DATABASE'];
        $dataSet = array();
        $names = DB::select("SELECT ROUTINE_NAME AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='$db' AND ROUTINE_NAME LIKE 'reporte_art_20%' AND ROUTINE_NAME NOT LIKE '%a_num_1_%'");
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto_hist pph UNION SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC'); 
        return view("reportes.leyHacendaria", [
            'dataSet' => json_encode($dataSet),
            'names' => $names,
            'anios' => $anios,
        ]);
    }

    public function indexAdministrativo()
    {
        Controller::check_permission('getAdmon');
        $dataSet = array();
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto_hist pph UNION SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        $upps = DB::select('SELECT clave,descripcion FROM catalogo WHERE grupo_id = 6 GROUP BY clave ORDER BY clave ASC');
        return view("reportes.administrativos.indexAdministrativo", [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
            'upps' => $upps,
        ]);
    }

    public function indexAnalisisMML()
    {
        Controller::check_permission('getAnalisis');
        $anios = DB::select('SELECT ejercicio FROM mml_avance_etapas_pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        $anios = $anios == null ? Date("Y") : $anios;
        $dataSet = array();
        return view('reportes.analisisInformativoMML', [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
        ]);
    }

    // Administrativos
    public function calendarioFondoMensual(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL calendario_fondo_mensual(" . $anio . ", " . $fecha . ")");
        foreach ($data as $d) {

            $suma = $d->enero + $d->febrero + $d->marzo + $d->abril + $d->mayo + $d->junio + $d->julio + $d->agosto + $d->septiembre + $d->octubre + $d->noviembre + $d->diciembre;

            $ds = array($d->ramo, $d->fondo_ramo, number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre), number_format($suma));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function resumenCapituloPartida(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL reporte_resumen_por_capitulo_y_partida(" . $anio . ", " . $fecha . ")");
        foreach ($data as $d) {
            $ds = array($d->capitulo, $d->partida, number_format($d->importe));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function proyectoAvanceGeneral(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL proyecto_avance_general(" . $anio . ", " . $fecha . ")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp . " " . $d->upp, $d->clv_fondo_ramo . " " . $d->fondo_ramo, $d->clv_capitulo . " " . $d->capitulo, number_format($d->monto_anual), number_format($d->calendarizado), number_format($d->disponible), number_format($d->avance), $d->estatus);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function proyectoCalendarioGeneral(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        if (Auth::user()->clv_upp != null) $upp = "'" . Auth::user()->clv_upp . "'";
        else $upp = $request->upp != "null" ? "'" . $request->upp . "'"  : "null";

        if(Auth::user()->id_grupo == 1) $tipo = $request->tipo == "RH" ? "'RH'" : ($request->tipo == "Operativo" ? "'Operativo'": "null"); 
        else $tipo = Auth::user()->id_grupo == 5 ? "'RH'" : "'Operativo'";

        $dataSet = array();
        $data = DB::select("CALL calendario_general(" . $anio . ", " . $fecha . ", " . $upp . ",". $tipo. ")");

        foreach ($data as $d) {
            $ds = array($d->upp, $d->clave, number_format($d->monto_anual), number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function proyectoCalendarioGeneralActividad(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        if (Auth::user()->clv_upp != null) $upp = "'" . Auth::user()->clv_upp . "'";
        else $upp = $request->upp != "null" ? "'" . $request->upp . "'"  : "null";
       
        if(Auth::user()->id_grupo == 1) $tipo = $request->tipo == "RH" ? "'RH'" : ($request->tipo == "Operativo" ? "'Operativo'": "null"); 
        else $tipo = Auth::user()->id_grupo == 5 ? "'RH'" : "'Operativo'";
        
        $dataSet = array();
        $data = DB::select("CALL proyecto_calendario_actividades(" . $anio . ", " . $upp . ", " . $fecha . ", " . $tipo . ")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp, $d->clv_ur, $d->clv_programa, $d->clv_subprograma, $d->clv_proyecto, $d->clv_fondo, $d->actividad, number_format(floatval($d->cantidad_beneficiarios)), $d->beneficiario, $d->unidad_medida, $d->tipo, number_format(floatval($d->meta_anual)), number_format(floatval($d->enero)), number_format(floatval($d->febrero)), number_format(floatval($d->marzo)), number_format(floatval($d->abril)), number_format(floatval($d->mayo)), number_format(floatval($d->junio)), number_format(floatval($d->julio)), number_format(floatval($d->agosto)), number_format(floatval($d->septiembre)), number_format(floatval($d->octubre)), number_format(floatval($d->noviembre)), number_format(floatval($d->diciembre)));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function avanceProyectoActividadUPP(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL avance_proyectos_actividades_upp(" . $anio . ", " . $fecha . ")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp . " " . $d->upp, $d->claves, $d->mir, $d->avance."%", $d->estatus_claves, $d->estatus_mir);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function getFechaCorte($anio)
    {
        $fechaCorte = DB::select('SELECT DISTINCT version, MAX(DATE_FORMAT(deleted_at, "%Y-%m-%d")) AS deleted_at FROM programacion_presupuesto_hist pp WHERE ejercicio = ? AND deleted_at IS NOT NULL GROUP BY version', [$anio]);
        return $fechaCorte;
    }

    public function downloadReport(Request $request, $nombre)
    {
        ini_set('max_execution_time', 600); // Tiempo máximo de ejecución 

        $report =  $nombre;
        $anio = !$request->input('anio') ? (int)$request->anio_filter : (int)$request->input('anio');
        $fechaCorte = !$request->input('fechaCorte') ? $request->fechaCorte_filter : $request->input('fechaCorte');
        $upp = Auth::user()->clv_upp != null ? Auth::user()->clv_upp : $request->upp_filter;

        // Comprobar si el reporte es administrativo o de ley hacendaria
        if (str_contains($report, "reporte_art_20")) $tipoReporte = "Reportes de ley hacendaria";
        else  $tipoReporte = "Reportes administrativos";

        try {
            $logoLeft = public_path() . "/img/escudoBN.png";
            $logoRight = public_path() . "/img/logo.png";
            $report_path = app_path() . "/Reportes/" . $report . ".jasper";
            $format = array($request->action);
            $output_file =  public_path() . "/reportes";
            $file = public_path() . "/reportes/" . $report;
            $nameFile = "EF_" . $anio . "_" . $report;
            $parameters = array(
                "anio" => $anio,
                "logoLeft" => $logoLeft,
                "logoRight" => $logoRight,
                "extension" => $request->action,
            );

            if ($fechaCorte != null) {
                $parameters["fecha"] = $fechaCorte;
                $nameFile = $nameFile . "_" . $fechaCorte;
            }

            if ($nombre == "calendario_clave_presupuestaria" || $nombre == "proyecto_calendario_actividades") {
                if (Auth::user()->clv_upp != null || $upp != null) {
                    $parameters["upp"] = $upp;
                    $nameFile = $nameFile . "_UPP_" . $upp;
                }

                if(Auth::user()->id_grupo == 1){
                    if($request->tipo_filter !=null) $parameters["tipo"] = $request->tipo_filter;
                }
                else $parameters["tipo"] = Auth::user()->id_grupo == 5 ? "RH" : "Operativo";
            }

            $database_connection = \Config::get('database.connections.mysql');

            $jasper = new PHPJasper;
            $jasper->process(
                $report_path,
                $output_file,
                $format,
                $parameters,
                $database_connection
            )->execute();

            if ($request->action == 'xlsx') { // Verificar el tipo de archivo
                if (File::exists($output_file . "/" . $report . ".xlsx") && filesize($file . ".xlsx") < 4097) { // Verificar si el archivo generado está vacío y Verificar si existe el archivo guardado en caso de existir lo elimina
                    File::delete($output_file . "/" . $report . ".xlsx");
                    return back()->withErrors(['msg' => "$nameFile.xlsx está vacío."]); // Regresar un mensaje para dar a entender al usuario que el archivo esta vacío
                }
            } else {
                if (File::exists($output_file . "/" . $report . ".pdf") && filesize($file . ".pdf") < 4097) {
                    File::delete($output_file . "/" . $report . ".pdf");
                    return back()->withErrors(['msg' => "$nameFile.pdf está vacío."]);
                }
            }

            ob_end_clean();
            // Bitácora
            $b = array(
                "username" => Auth::user()->username,
                "accion" => $nameFile . "." . $request->action,
                "modulo" => $tipoReporte,
            );
            Controller::bitacora($b);

            return $request->action == 'pdf' ? response()->download($file . ".pdf", $nameFile . ".pdf")->deleteFileAfterSend() : response()->download($file . ".xlsx", $nameFile . ".xlsx")->deleteFileAfterSend();
        } catch (\Exception $exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            return back()->withErrors(['msg' => '¡Ocurrió un error al descargar el archivo!']);
        }
    }

    // Reportes MML
    public function getAvanceMIR(Request $request)
    {
        $anio = $request->anio;
        $upp = $request->upp;
        $estatus = $request->estatus;   
        $array_where = [];
        session(["anioMIR"=>$anio]);
        
        if($upp != null && $upp != "null" && $upp != "") array_push($array_where, ['ae.clv_upp', $upp]);
        if($estatus != null && $estatus != "null" && $estatus != "") array_push($array_where, ['ae.estatus', $estatus]);

        $dataSet = array();
        $data = DB::table("mml_avance_etapas_pp as ae")
            ->join("v_epp as ve", function ($join) {
                $join->on("ae.clv_upp", "=", "ve.clv_upp");
                $join->on("ae.clv_pp", "=", "ve.clv_programa");
            })
            ->select("ae.clv_upp", "ve.upp", "ae.clv_pp", "ve.programa", "ae.estatus", "ae.ejercicio")
            ->where("ae.ejercicio", $anio)
            ->whereNull("ae.deleted_at")
            ->where($array_where)
            ->groupBy("ve.clv_upp", "ve.clv_programa")
            ->orderBy("ae.estatus","desc")
            ->orderBy("ve.clv_upp","asc")
            ->get();
        $estado = "";
        foreach ($data as $d) {
            $estado = $d->estatus == 3 ? "Validado" : "Pendiente";
            $ds = array($d->clv_upp, $d->upp, $d->clv_pp, $d->programa, $estado);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function getMIR(Request $request)
    {
        $anio = $request->anio;
        $upp = $request->upp;
        $estatus = $request->estatus == "3" ? "Abierto" : ($request->estatus == "0" ? "Cerrado" : ""); 
        $array_where = [];
        
        if($upp != null && $upp != "null" && $upp != "") array_push($array_where, ['ce.clv_upp', $upp]);
        if($estatus != null && $estatus != "null" && $estatus != "") array_push($array_where, ['ce.estatus', $estatus]);

        $dataSet = array();
        $data = DB::table("mml_cierre_ejercicio as ce")
            ->join("v_epp as ve", function ($join) {
                $join->on("ce.clv_upp", "=", "ve.clv_upp");
            })
            ->select("ce.clv_upp", "ve.upp","ce.estatus", "ce.ejercicio")
            ->where("ce.ejercicio", $anio)
            ->whereNull("ce.deleted_at")
            ->where($array_where)
            ->groupBy("ve.clv_upp")
            ->orderBy("ce.estatus","desc")
            ->orderBy("ve.clv_upp","asc")
            ->get();

        foreach ($data as $d) {
            $ds = array($d->clv_upp, $d->upp, $d->estatus);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function getProyectoPresupuestal(Request $request)
    {
        $anio = $request->anio;
        $upp = $request->upp;
        $programa = $request->programa;
        $mir = $request->mir;
        $array_where = [];
        
        if($upp != null && $upp != "null" && $upp != "") array_push($array_where, ['ve.clv_upp', $upp]);
        if($programa != null && $programa != "null" && $programa != "") array_push($array_where, ['ve.clv_programa', $programa]);
        if($mir != null && $mir != "null" && $mir != "" && $mir == "1") array_push($array_where, ['mm.area_funcional','!=','null']);
        $dataSet = array();
        
        $data = DB::table("v_epp as ve")
        ->leftJoin("mml_mir as mm", function($join){
            $join->on("ve.id", "=", "mm.id_epp")
            ->where("mm.nivel", "=", 11);
        })
        ->select(DB::raw("distinct(mm.area_funcional), concat( ve.clv_finalidad, ve.clv_funcion, ve.clv_subfuncion, ve.clv_eje, ve.clv_linea_accion, ve.clv_programa_sectorial, ve.clv_tipologia_conac, ve.clv_programa, ve.clv_subprograma, ve.clv_proyecto) as area_funcional_epp,
        ve.clv_upp,
        ve.clv_programa,
        ve.clv_ur,
        ve.proyecto"))
        ->where("ve.ejercicio", "=", $anio)
        ->where($array_where)
        ->whereNull("mm.deleted_at")
        ->orderBy("ve.clv_upp", "asc")
        ->orderBy("ve.clv_ur", "asc")
        ->orderBy("ve.clv_programa", "asc");

        if($mir == "0") $data->whereRaw('mm.area_funcional IS NULL'); // Comprobar si el valor en la variable MIR corresponde a los datos sin MIR
        $data = $data->get();
        
        foreach ($data as $d) {
            $conMir = "-";
            if ($d->area_funcional != null) $conMir = '<p><i class="fa fa-check"></i></p>';
            
            $ds = array($d->clv_upp, $d->clv_ur, $d->clv_programa, $d->area_funcional_epp, $d->proyecto, $conMir);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function getUPP($anio){ // Obtener las UPP para llenar el select del mismo
        $upp = DB::table("v_epp")->select("clv_upp", "upp")
        ->where("ejercicio", $anio)
        ->groupBy("clv_upp")->get();
        return $upp;
    }

    public function getPrograma($clv_upp){ // Obtener los programas para llenar el select del mismo
        $anio = session("anioMIR");
        $array_where=[];

        if( $clv_upp != "0" ){
            array_push($array_where,["clv_upp", $clv_upp]);
        }
        $programa = DB::table("v_epp")->select("clv_programa", "programa")
        ->where("ejercicio", $anio)
        ->where($array_where)
        ->groupBy("clv_programa")->get();
        return $programa;
    }
}
