<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class ReporteController extends Controller
{
    public function indexPlaneacion(){
        $dataSet = array();
        $names = DB::select('SELECT ROUTINE_NAME AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="PROCEDURE" AND ROUTINE_SCHEMA="fondos_db" AND ROUTINE_NAME LIKE "%art_20%" AND ROUTINE_NAME NOT LIKE "%a_num_1_%"');
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        return view("reportes.leyHacendaria", [
            'dataSet' => json_encode($dataSet),
            'names' => $names,
            'anios' => $anios,
        ]);
    }

    public function indexAdministrativo(){
        $dataSet = array();
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        $upps = DB::select('SELECT clave,descripcion FROM catalogo WHERE grupo_id = 6 ORDER BY clave ASC');
        return view("reportes.administrativos.indexAdministrativo", [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
            'upps' => $upps,
        ]);
    }

    // Administrativos
    public function calendarioFondoMensual(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $dataSet = array();
        // $data = DB::select("CALL calendario_fondo_mensual(".$anio.", null)");
        $data = DB::select("CALL calendario_fondo_mensual(".$anio.", ".$fecha.")");
        foreach ($data as $d) {

            $suma = $d->enero + $d->febrero + $d->marzo + $d->abril + $d->mayo + $d->junio + $d->julio + $d->agosto + $d->septiembre + $d->octubre + $d->noviembre + $d->diciembre;

            $ds = array($d->ramo, $d->fondo_ramo, number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre), number_format($suma));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function resumenCapituloPartida(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL reporte_resumen_por_capitulo_y_partida(".$anio.", ".$fecha.")");
        foreach ($data as $d) {
            $ds = array($d->capitulo, $d->partida, number_format($d->importe));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }
    
    public function proyectoAvanceGeneral(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL proyecto_avance_general(".$anio.", ".$fecha.")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp." ".$d->upp, $d->clv_fondo_ramo." ".$d->fondo_ramo, $d->clv_capitulo." ".$d->capitulo, number_format($d->monto_anual), number_format($d->calendarizado), number_format($d->disponible), number_format($d->avance), $d->estatus);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function proyectoCalendarioGeneral(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $upp = $request->upp != "null" ? "'".$request->upp."'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL calendario_general(".$anio.", ".$fecha.", ".$upp.")");
        foreach ($data as $d) {
            $ds = array($d->upp,$d->clave, number_format($d->monto_anual), number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function proyectoCalendarioGeneralActividad(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $upp = $request->upp != "null" ? "'".$request->upp."'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL proyecto_calendario_actividades(".$anio.", ".$upp.", ".$fecha.")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp, $d->clv_ur, $d->clv_programa, $d->clv_subprograma, $d->clv_proyecto, $d->clv_fondo, $d->actividad, $d->cantidad_beneficiarios, $d->beneficiario, $d->unidad_medida, $d->tipo, number_format($d->meta_anual), number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre));
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }

    public function avanceProyectoActividadUPP(Request $request){
        $anio = $request->anio;
        $fecha = $request->fecha != "null" ? "'".$request->fecha."'"  : "null";
        $dataSet = array();
        $data = DB::select("CALL avance_proyectos_actividades_upp(".$anio.", ".$fecha.")");
        foreach ($data as $d) {
            $ds = array($d->clv_upp." ".$d->upp, $d->proyectos, $d->actividades, $d->avance, $d->estatus);
            $dataSet[] = $ds;
        }
        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }
    // Administrativos

    public function getFechaCorte($anio){
        $fechaCorte = DB::select('select distinct DATE_FORMAT(deleted_at, "%Y-%m-%d") as deleted_at from programacion_presupuesto pp where ejercicio = ? and deleted_at is not null',[$anio]);
        return $fechaCorte;
    }

    public function downloadReport(Request $request, $nombre){ 
        ini_set('max_execution_time', 300);
        date_default_timezone_set('America/Mexico_City');
        setlocale(LC_TIME, 'es_VE.UTF-8','esp');

        $report =  $nombre;
        $anio = !$request->input('anio') ? (int)$request->anio_filter : (int)$request->input('anio');
        $fechaCorte = !$request->input('fechaCorte') ? $request->fechaCorte_filter : $request->input('fechaCorte');
        $upp = $request->upp_filter;

        $ruta = public_path()."/reportes";
        //Eliminación si ya existe reporte
        if(File::exists($ruta."/".$report.".pdf")) {
            File::delete($ruta."/".$report.".pdf");
        }
        $logo = public_path()."/img/logo.png";
        $report_path = app_path() ."/Reportes/".$report.".jasper";
        $format = array($request->action);
        $output_file =  public_path()."/reportes";
        $routeFile = public_path()."/reportes/".$report;
        $downloadFile = "EF_".$anio."_".$report;
        $parameters = array(
            "anio" => $anio,
            "logoLeft" => $logo,
            "logoRight" => $logo,
        );
       
        if($fechaCorte != null) {
            $parameters["fecha"] = $fechaCorte;
            $downloadFile = $downloadFile."_".$fechaCorte;
        }
        if($nombre == "calendario_general" || $nombre == "proyecto_calendario_actividades_upp"){
            if($upp != null){
                $parameters["upp"] = $upp;
                $downloadFile = $downloadFile."_UPP_".$upp;
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

        return $request->action == 'pdf' ? response()->download($routeFile.".pdf", $downloadFile.".pdf")->deleteFileAfterSend() : response()->download($routeFile.".xls", $downloadFile.".xls")->deleteFileAfterSend(); 
    }
}
