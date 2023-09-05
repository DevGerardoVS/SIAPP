<?php

namespace App\Http\Controllers\Administracion;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\InicioExport;


class InicioController extends Controller
{
    //
    public static function GetInicioA(){
        try {
            $anio_act = date('Y')-1;
            $dataSet = array();
            $data = DB::table('inicio_a')
            ->where("ejercicio", "=", function($query){
            $query->from("inicio_b")
            ->select("ejercicio")
            ->limit(1)
            ->orderBy("ejercicio","desc")
            ->groupBy("ejercicio");
            })->get();

            foreach ($data as $d) {
                $ds = array(number_format($d->presupuesto_asignado, 2, '.', ',') , number_format($d->presupuesto_calendarizado, 2, '.', ','), number_format($d->disponible, 2, '.', ',') , number_format($d->avance, 2, '.', ','));
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "inicio",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function GetInicioB(){
        try {
            $anio_act = date('Y')-1;
            $dataSet = array();
            $data = DB::table('inicio_b')
            ->where("ejercicio", "=", function($query){
            $query->from("inicio_b")
            ->select("ejercicio")
            ->limit(1)
            ->orderBy("ejercicio","desc")
            ->groupBy("ejercicio");
            })->get();

            foreach ($data as $d) {
                $ds = array($d->clave, $d->fondo, number_format($d->asignado, 2, '.', ','), number_format($d->programado, 2, '.', ','), number_format($d->avance, 2, '.', ','));
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "inicio",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function getLinks(){
        try {

            $data = DB::table('configuracion')
            ->where("descripcion", "=", "enlaces")->first();

            return response()->json([
                "dataSet" => $data,
                "catalogo" => "portada",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    
    public function getFondos(){
        $fondos = DB::table("techos_financieros as tf")
        ->join("fondo as f", function($join){
            $join->on("f.clv_fondo_ramo", "tf.clv_fondo");
        })
        ->select("tf.clv_fondo", "tf.ejercicio", "f.fondo_ramo")
        ->where("ejercicio", "=", function($query){
                $query->from("pp_aplanado")
                ->select("ejercicio")
            ->limit(1)
            ->orderBy("ejercicio","desc")
            ->groupBy("ejercicio");
            })
        ->whereNull("tf.deleted_at")
        ->groupBy("tf.clv_fondo")
        ->get();
        return $fondos;
    }

    public function exportPdf()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
        $data = DB::table('inicio_b')
        ->select(DB::raw('
            clave,
            fondo,
            FORMAT(asignado,"Currency") as asignado,
            FORMAT(programado,"Currency") as programado,
            FORMAT(avance, 2) as avance '))
        ->get();
        view()->share('data', $data);
        $pdf = PDF::loadView('inicioPdf')->setPaper('a4', 'landscape');
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar Inicio PDF',
            "modulo" => 'Inicio'
        );
        Controller::bitacora($b);
        return $pdf->download('Presupuesto por fondo.pdf');
    }

    public function exportExcel()
    {
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();
        /*Si no coloco estas lineas Falla*/
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar Inicio Excel',
            "modulo" => 'Inicio'
        );
        Controller::bitacora($b);
        return Excel::download(new InicioExport(), 'Presupuesto por fondo..xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
