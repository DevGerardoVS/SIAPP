<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    // public function index(){
    //     $names = DB::select('SELECT ROUTINE_NAME AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="PROCEDURE" AND ROUTINE_SCHEMA="fondos_db" AND ROUTINE_NAME LIKE "%art_20%"');
    //     $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
    //     return view('reportes.leyHacendaria',compact('names','anios'));
    // }

    public function downloadReport($name, $anio, $date, Request $request){ 
        date_default_timezone_set('America/Mexico_City');
        
        setlocale(LC_TIME, 'es_VE.UTF-8','esp');
        $report =  $name;

        $ruta = public_path()."/reportes";
        //Eliminación si ya existe reporte
        if(File::exists($ruta."/".$report.".pdf")) {
            File::delete($ruta."/".$report.".pdf");
        }
        $logo = public_path()."/img/logo.png";
        $report_path = app_path() ."/Reportes/".$report.".jasper";
        $format = array('pdf');
        $output_file =  public_path()."/reportes";
        $viewFile = public_path()."/reportes/".$report;

        $parameters = array(
            "anio" => $anio,
            "logoLeft" => $logo,
            "logoRight" => $logo,
        );
        
        if($date != 0) $parameters["fecha"] = $date;

        $database_connection = \Config::get('database.connections.mysql');

        $jasper = new PHPJasper;
        $jasper->process(
          $report_path,
          $output_file,
          $format,
          $parameters,
          $database_connection
        )->execute();

        return response()->file($viewFile.".pdf")->deleteFileAfterSend(); 
    }

    // public function index(){
    //     // $reportes = DB::select("CALL calendario_fondo_mensual('23', NULL)");
    //     return view('reportes.administrativos.calendarioBaseMensual');
    // }

    public function index(){
        $dataSet = array();
        $anios = DB::select('SELECT ejercicio FROM programacion_presupuesto pp GROUP BY ejercicio ORDER BY ejercicio DESC');
        return view("reportes.administrativos.calendarioBaseMensual", [
            'dataSet' => json_encode($dataSet),
            'anios' => $anios,
        ]);
    }

    public function reporte(Request $request){
        if ($request->input('anio_filter') != null) {
            // array_push($array_where, [$tabla . '.municipio_id', '=', $request->input('municipio_filter')]);
        }
        // log::info($request->input('anio_filter'));
        $data = DB::select("CALL calendario_fondo_mensual(".$request->input('anio_filter').", NULL)");
        // log::info("CALL calendario_fondo_mensual(".$request->input('anio_filter').", ". NULL .")");
        $dataSet = array();

        foreach ($data as $d) {

            $suma = $d->enero + $d->febrero + $d->marzo + $d->abril + $d->mayo + $d->junio + $d->julio + $d->agosto + $d->septiembre + $d->octubre + $d->noviembre + $d->diciembre;

            $ds = array($d->ramo, $d->fondo, number_format($d->enero), number_format($d->febrero), number_format($d->marzo), number_format($d->abril), number_format($d->mayo), number_format($d->junio), number_format($d->julio), number_format($d->agosto), number_format($d->septiembre), number_format($d->octubre), number_format($d->noviembre), number_format($d->diciembre), number_format($suma));
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
        ]);
    }
}
