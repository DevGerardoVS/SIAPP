<?php
namespace App\Http\Controllers\utils;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use JasperPHP\JasperPHP as PHPJasper;
use Illuminate\Support\Facades\File;


class ReportesJasper
{
    public static function claves($request)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', true);
		ob_end_clean();
		ob_start();
		date_default_timezone_set('America/Mexico_City');
		setlocale(LC_TIME, 'es_VE.UTF-8', 'esp');
		$fecha = date('d-m-Y');
		$marca = strtotime($fecha);
		$fechaCompleta = strftime('%A %e de %B de %Y', $marca);
		$report = '';
		if ($request['tipo'] == 0) {
			$report = "reporte_calendario_upp_autografa";
			// $file = sys_get_temp_dir(). $report;
		} else {
			$report = "Reporte_Calendario_UPP";
			// $file = sys_get_temp_dir(). $report;
		}
		$ruta = sys_get_temp_dir();
		//Eliminación si ya existe reporte
		if (File::exists($ruta . "/" . $report . ".pdf")) {
			Log::info('si existe el archivo lo elimina', [json_encode($ruta . "/" . $report . ".pdf")]);
			File::delete($ruta . "/" . $report . ".pdf");
		}
		$report_path = app_path() . "/Reportes/" . $report . ".jasper";
		$format = array('pdf');
		$output_file = sys_get_temp_dir()."/".time()."/";
		mkdir($output_file, 0777, true);
		$logoLeft = public_path() . "/img/escudoBN.png";
        $logoRight = public_path() . "/img/logo.png";
		Log::info('reuqest', [json_encode($request)]);
		$parameters = [
			"anio" => $request['anio'],
			"logoLeft" => $logoLeft,
            "logoRight" => $logoRight,
			"upp" => $request['UPP'],
		];

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
		$archivo = $output_file . '/' . $report . '.pdf';
		if (file_exists($output_file . '/' . $report . '.pdf')) {
			$archivo = $output_file . '/' . $report . '.pdf';
			$archivo2 = file_get_contents($archivo);
			$reportePDF = Response::make($archivo2, 200, [
				'Content-Type' => 'application/pdf'
			]);
		}
		
		// $reportePDF = Response::make(file_get_contents(public_path() . "/reportes/" . $report . ".pdf"), 200, [
		// 	'Content-Type' => 'application/pdf'
		// ]);

		if ($request['tipo'] == 0) {
			if (file_exists($archivo)) {
				return response()->download($archivo);
			}else {
				return response()->json('error', 200);
			}
			
		}
		if ($reportePDF != '') {
			return response()->json('done', 200);
		} else {
			return response()->json('error', 200);
		}
	}

	public static function Metas($upp, $anio, $tipo)
	{
		ob_end_clean();
		ob_start();
		date_default_timezone_set('America/Mexico_City');
		setlocale(LC_TIME, 'es_VE.UTF-8', 'esp');
		$fecha = date('d-m-Y');
		$date = $anio;
		$marca = strtotime($fecha);
		$fechaCompleta = strftime('%A %e de %B de %Y', $marca);

		$report = '';
		if ($tipo == 0) {
			$report = "proyecto_calendario_actividades_autografa";
			$file = sys_get_temp_dir(). $report;
		} else {
			$report = "proyecto_calendario_actividades";
			$file = sys_get_temp_dir(). $report;
		}

		$ruta = sys_get_temp_dir();
		//Eliminación si ya existe reporte
		if (File::exists($ruta . "/" . $report . ".pdf")) {
			File::delete($ruta . "/" . $report . ".pdf");
		}
		$report_path = app_path() . "/Reportes/" . $report . ".jasper";
		$format = array('pdf');
		$output_file = sys_get_temp_dir();
		$logoLeft = public_path() . "/img/escudoBN.png";
        $logoRight = public_path() . "/img/logo.png";

		$parameters = array(
			"anio" => $date,
			"logoLeft" => $logoLeft,
            "logoRight" => $logoRight,
			"upp" => $upp,
		);
		if($tipo != 0) $parameters["extension"] = "pdf";
		
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
		$archivo = $output_file . '/' . $report . '.pdf';
		if (file_exists($output_file . '/' . $report . '.pdf')) {
			$archivo = $output_file . '/' . $report . '.pdf';
			$archivo2 = file_get_contents($archivo);
			$reportePDF = Response::make($archivo2, 200, [
				'Content-Type' => 'application/pdf'
			]);
		}
		
		if ($tipo == 0) {
			if (file_exists($archivo)) {
				return response()->download($archivo);
			}else {
				return response()->json('error', 200);
			}
		} else {
			if ($reportePDF != '') {
				return response()->json('done', 200);
			} else {
				return response()->json('error', 400);
			}
		}

	}
}