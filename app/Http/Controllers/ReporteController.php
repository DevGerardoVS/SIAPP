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

    public function index(){
        $reportes = DB::select("CALL calendario_fondo_mensual('23', NULL)");
        // ***********************************************************************************************************
        // $array_concepto = array('predios_registrados' => __('messages.predios_registrados'),
        // 'predios_pagados'     => __('messages.predios_pagados'),
        // 'predios_pagados_rezago' => __('messages.predios_pagados_rezago'), 
        // 'rec_potencial_anual' => __('messages.rec_potencial_anual'),
        //  'rec_potencial_anual_ant' => __('messages.rec_potencial_anual_ant'));
        // $data = DB::select("CALL calendario_fondo_mensual('23', NULL)");

        // $dataSet = array();
        // $dataSet[] = array(__('messages.datos_padron'), '', '', '', '', '', '');
        // $total_t1=0;
        // $total_t2=0;
        // $total_t3=0;
        // $total_t4=0;
        // $total=0;
        // foreach ($array_concepto as $key => $concepto) {
        // $total_suma = 0;
        // $trimestre1 = 0;
        // $trimestre2 = 0;
        // $trimestre3 = 0;
        // $trimestre4 = 0;
        // $ds = array();
        // array_push($ds, $array_concepto[$key]);

        // foreach ($data as $k => $d) {

        // $suma = 0;
        // array_push($ds, $data[$k]->$key);
        // $suma = str_replace(',', '', $data[$k]->$key);
        // if($d->trimestre==1) $trimestre1 += str_replace(',', '', $data[$k]->$key);
        // else if($d->trimestre==2) $trimestre2 += str_replace(',', '', $data[$k]->$key);
        // else if($d->trimestre==3) $trimestre3 += str_replace(',', '', $data[$k]->$key);
        // else if($d->trimestre==4) $trimestre4 += str_replace(',', '', $data[$k]->$key);
        // $total_suma += $suma;
        // }

        // // sumar solo predios pagados actual y rezago
        // if($array_concepto[$key] == __('messages.predios_pagados') || $array_concepto[$key] == __('messages.predios_pagados_rezago')){
        // $total_t1 += $trimestre1;
        // $total_t2 += $trimestre2;
        // $total_t3 += $trimestre3;
        // $total_t4 += $trimestre4;
        // $total += $total_suma;
        // }

        // if (count($data) > 0) {


        // $ds[1] = number_format($trimestre1, 2);
        // $ds[2] = number_format($trimestre2, 2);
        // $ds[3] = number_format($trimestre3, 2);
        // $ds[4] = number_format($trimestre4, 2);

        // if($key == "rec_potencial_anual" || $key == "rec_potencial_anual_ant" ){
        // $ds[5] = number_format($trimestre1, 2);
        // }
        // else{
        // $ds[5] = number_format($total_suma, 2);
        // } 
        // }
        // if($key == "rec_potencial_anual")
        // $dataSet[] =  array(__('messages.total_predios_pagados'), number_format($total_t1,2), number_format($total_t2,2), number_format($total_t3,2), number_format($total_t4,2), number_format($total,2));


        // $dataSet[] = $ds;


        // }


        // return response()->json([
        // "dataSet" => $dataSet,
        // ]);
        // ***********************************************************************************************************
        return view('reportes.administrativos.calendarioBaseMensual', compact('reportes'));
    }
}
