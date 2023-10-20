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
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
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
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        $upps = DB::select('SELECT clave,descripcion FROM catalogo WHERE grupo_id = 6 GROUP BY clave ORDER BY clave ASC');
        return view("reportes.administrativos.indexAdministrativo", [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
            'upps' => $upps,
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
        // $draw = $request->get('draw');
        // $drawin = $request->get('filtros');
        // $search_arr = $request->get('search');
        // $searchValue = $search_arr && array_key_exists('value', $search_arr) ? $search_arr['value'] : ''; 
        // $start = $request->get("start");
        // $rowperpage = $request->get("length"); 

        // Log::info("resultado de array");
        // log::debug($start);
        // Log::info("fin resultado de array");

        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        if (Auth::user()->clv_upp != null) $upp = "'" . Auth::user()->clv_upp . "'";
        else $upp = $request->upp != "null" ? "'" . $request->upp . "'"  : "null";
        $dataSet = array();
        // $countData = count(DB::select("CALL calendario_general(".$anio.", ".$fecha.", ".$upp.",0,1000000)"));
        // $data = DB::select("CALL calendario_general(".$anio.", ".$fecha.", ".$upp.",0,1000000)");

        // $data = DB::select("CALL calendario_general(".$anio.", ".$fecha.", ".$upp.",".$start.",".$rowperpage.")");
        $data = DB::select("CALL calendario_general(" . $anio . ", " . $fecha . ", " . $upp . ")");

        foreach ($data as $d) {
            $ds = array($d->upp, $d->clave, number_format($d->monto_anual), number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
            // "totalRecords" => $countData,
        ]);
    }

    public function proyectoCalendarioGeneralActividad(Request $request)
    {
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'" . $request->fecha . "'"  : "null";
        if (Auth::user()->clv_upp != null) $upp = "'" . Auth::user()->clv_upp . "'";
        else $upp = $request->upp != "null" ? "'" . $request->upp . "'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL proyecto_calendario_actividades(" . $anio . ", " . $upp . ", " . $fecha . ")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp, $d->clv_ur, $d->clv_programa, $d->clv_subprograma, $d->clv_proyecto, $d->clv_fondo, $d->actividad, $d->cantidad_beneficiarios, $d->beneficiario, $d->unidad_medida, $d->tipo, number_format($d->meta_anual), number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre));
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
            $ds = array($d->clv_upp . " " . $d->upp, $d->proyectos, $d->actividades, $d->avance, $d->estatus);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }
    // Administrativos

    public function getFechaCorte($anio)
    {
        $fechaCorte = DB::select('select distinct version, DATE_FORMAT(deleted_at, "%Y-%m-%d") as deleted_at from programacion_presupuesto_hist pp where ejercicio = ? and deleted_at is not null', [$anio]);
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
    public function indexMML()
    {
        Controller::check_permission('getAdmon');
        $anios = DB::select('SELECT ejercicio FROM mml_avance_etapas_pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        $anios = $anios == null ? Date("Y") : $anios;
        $dataSet = array();
        return view('reportes.avanceMIR', [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
        ]);
    }

    public function getAvanceMIR(Request $request)
    {
        $anio = $request->anio;
        $upp = $request->upp;
        $estatus = $request->estatus;   
        session(["anioMIR"=>$anio]);
        $dataSet = array();
        $data = DB::table("mml_avance_etapas_pp as ae")
            ->join("v_epp as ve", function ($join) {
                $join->on("ae.clv_upp", "=", "ve.clv_upp");
                $join->on("ae.clv_pp", "=", "ve.clv_programa");
            })
            ->select("ae.clv_upp", "ve.upp", "ae.clv_pp", "ve.programa", "ae.estatus", "ae.ejercicio")
            ->where("ae.ejercicio", $anio)
            ->groupBy("ve.clv_upp", "ve.clv_programa")
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

    public function getComprobacion(Request $request)
    {
        $anio = $request->anio;
        $upp = $request->upp;
        $programa = $request->programa;
        $mir = $request->mir;
        $dataSet = array();
        $data = DB::select(DB::raw("
        SELECT DISTINCT(mm.area_funcional),
        concat(
            ve.clv_finalidad,
            ve.clv_funcion,
            ve.clv_subfuncion,
            ve.clv_eje,
            ve.clv_linea_accion,
            ve.clv_programa_sectorial,
            ve.clv_tipologia_conac,
            ve.clv_programa,
            ve.clv_subprograma,
            ve.clv_proyecto) AS area_funcional_epp,
        ve.clv_upp,
        ve.clv_programa,
        ve.clv_ur,
        ve.proyecto
        FROM v_epp ve left JOIN mml_mir mm ON ve.id = mm.id_epp AND mm.nivel = 11
        WHERE ve.ejercicio = $anio AND mm.deleted_at IS NULL 
        ORDER BY ve.clv_upp, ve.clv_ur;"));
        
        foreach ($data as $d) {
            $conMir = "-";
            if ($d->area_funcional != null) $conMir = '<p><i class="fa fa-check"></i></p>';
            
            $ds = array($d->clv_upp, $d->clv_programa, $d->clv_ur, $d->area_funcional_epp, $d->proyecto, $conMir);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function getUPP($anio){
        
        $upp = DB::table("v_epp")->select("clv_upp", "upp")
        ->where("ejercicio", $anio)
        ->groupBy("clv_upp")->get();
        return $upp;
    }

    public function getPrograma($clv_upp){
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
