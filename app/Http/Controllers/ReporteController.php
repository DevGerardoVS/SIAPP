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

    public function downloadReport($name, $anio, $date){ 
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

    public function index(){
        return view('reportes.administrativos.calendarioBaseMensual');
    }
}
