<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;
use Response;
use DateTime;

class ReporteController extends Controller
{
    public function index(){
        // $names = DB::select('SELECT ROUTINE_NAME AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="PROCEDURE" AND ROUTINE_SCHEMA="siapp" and ROUTINE_NAME LIKE "%art_20%"');
        $names = DB::select('SELECT REPLACE(REPLACE(REPLACE(ROUTINE_NAME,"_"," "), "reporte ",""), "num","numeral") AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="PROCEDURE" AND ROUTINE_SCHEMA="fondos_db" and ROUTINE_NAME LIKE "%art_20%"');
        return view('reportes.leyHacendaria',compact('names'));
    }

    public function downloadReport(){ 
        date_default_timezone_set('America/Mexico_City');
        
        setlocale(LC_TIME, 'es_VE.UTF-8','esp');
        $fecha = date('d-m-Y');
        $marca = strtotime($fecha);
        $fechaCompleta = strftime('%A %e de %B de %Y', $marca);
        $report =  "Reporte_B_11";
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
            "anio" => 23,
            "logoLeft" => $logo,
            "logoRight" => $logo,
            // "fecha_hoy" => $fechaCompleta 
        );

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
}
