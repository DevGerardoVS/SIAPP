<?php

namespace App\Http\Controllers\Administracion;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\cierreEjercicio;

use Illuminate\Support\Facades\Storage;
use PDF;
use App\Exports\InicioExport;


class InicioController extends Controller
{
    //
    public static function GetInicioA(){
        try {

            $anio_act = date('Y');
            $dataSet = array();
           
            $anio_act = cierreEjercicio::max('ejercicio');
            //Log::channel('daily')->debug('anio '.$anio_act);

            $data = DB::select('CALL inicio_a('.$anio_act.')');

            /*$data = DB::table('inicio_a')
            ->where("ejercicio", "=", function($query){
            $query->from("inicio_b")
            ->select("ejercicio")
            ->limit(1)
            ->orderBy("ejercicio","desc")
            ->groupBy("ejercicio");
            })->get();*/

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
            $anio_act = date('Y');
            $dataSet = array();

            $anio_act = cierreEjercicio::max('ejercicio');

            $data = DB::select('CALL inicio_b('.$anio_act.')');



            /*$data = DB::table('inicio_b')
            ->where("ejercicio", "=", function($query){
            $query->from("inicio_b")
            ->select("ejercicio")
            ->limit(1)
            ->orderBy("ejercicio","desc")
            ->groupBy("ejercicio");
            })->get();*/

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

    public function getManual(){
        $file = "";
        $name = "";
        if(Auth::user()->id_grupo==1){
            $name = "CAP_Manual_de_Usuario_Administrador.pdf";
            $file= public_path(). "/manuales/". $name;
        } 
        if(Auth::user()->id_grupo==4){
            $name = "CAP_Manual_de_Usuario_UPP.pdf";
            $file= public_path()."/manuales/". $name;
        } 
        if(Auth::user()->id_grupo==5){
            $name = "CAP_Manual_de_Usuario_Delegacion.pdf";
            $file= public_path()."/manuales/". $name;
        } 
        
        //Log::channel('daily')->debug('exp '.public_path());
        $headers = array('Content-Type: application/pdf',);

        return response()->download($file,$name,$headers);
    }
    
    public function getFondos(){
        $fondos = DB::table("techos_financieros as tf")
        ->join("fondo as f", function($join){
            $join->on("f.clv_fondo_ramo", "tf.clv_fondo");
        })
        ->select("tf.clv_fondo", "tf.ejercicio", "f.fondo_ramo")
        ->where("ejercicio", "=", function($query){
                $query->from("techos_financieros")
                ->select("ejercicio")
            ->limit(1)
            ->whereNull("deleted_at")
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
            ejercicio,
            clave,
            fondo,
            FORMAT(asignado,"Currency") as asignado,
            FORMAT(programado,"Currency") as programado,
            FORMAT(avance, 2) as avance '))
            ->where("ejercicio", "=", function($query){
                $query->from("inicio_b")
                ->select("ejercicio")
                ->limit(1)
                ->orderBy("ejercicio","desc")
                ->groupBy("ejercicio");})
        ->get();
        view()->share(['data'=>$data,"anio"=>$data[0]->ejercicio]);
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
