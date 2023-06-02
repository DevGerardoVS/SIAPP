<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(){
        $names = DB::select('SELECT ROUTINE_NAME AS name FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="PROCEDURE" AND ROUTINE_SCHEMA="siapp" and ROUTINE_NAME LIKE "reporte%"');
        return view('reportes.leyHacendaria',compact('names'));
    }

    public function inde($id , $esVinculacion){ 
        date_default_timezone_set('America/Mexico_City');
        
        setlocale(LC_TIME, 'es_VE.UTF-8','esp');
        $fecha = date('d-m-Y');
        $marca = strtotime($fecha);
        $fechaCompleta = strftime('%A %e de %B de %Y', $marca);
        $report =  "";
        if ($esVinculacion == 0) {
            $report =  "reporte1";
        }else {
            $report =  "Reporte2";
        }
        $ruta = public_path()."/Reportes";
        //Eliminación si ya existe reporte
        if(File::exists($ruta."/".$report.".pdf")) {
            File::delete($ruta."/".$report.".pdf");
        }
        $logo = public_path()."/assets/img/Membretado.jpg";
        $report_path = app_path() ."/Reportes/".$report.".jasper";
        $format = array('pdf');
        $output_file =  public_path()."/Reportes";

        $parameters = array(
                "logo_left" => $logo,
                "id_expediente" => $id,
                "fecha_hoy" => $fechaCompleta 
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
        //dd($jasper);

        return Response::make(file_get_contents(public_path()."/Reportes/".$report.".pdf"), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
