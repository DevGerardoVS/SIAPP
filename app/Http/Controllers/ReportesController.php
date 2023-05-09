<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportePolizasXConcesionExport;
use App\Exports\ReporteMovimientosXConcesionExport;
use App\Helpers\ReportesHelper;
use App\Helpers\BitacoraHelper;
use App\Models\Aseguradoras;
use App\Models\Bitacora;
use App\Models\DetalleConcesion;
use App\Models\DetallePago;
use App\Models\PolizasSeguro;
use Carbon\Carbon;

class ReportesController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Reporte de polizas de seguro por concesion
    public function exportReportePolizasXConcesion(Request $request) {
        $datos_name = Carbon::now()->format('d-m-Y');

        return Excel::download(new ReportePolizasXConcesionExport($request), 'ReportePolizasXConcesion_'.$datos_name.'.xlsx');
    }

    public function exportReportePolizasXConcesionPdf(Request $request) {
        $datos_name = Carbon::now()->format('d-m-Y');
        $data = ReportesHelper::getReportePolizasXConcesionQuery($request,true);
        $returnData = array('title'=>__('messages.reporte_polizas_x_concesion'),'data'=>$data);
        $pdf = \PDF::loadView('reportes.pdf.reporte_polizas_x_concesion_pdf',$returnData);
        return $pdf->download('reporte_polizas_x_concesion_'.$datos_name.'.pdf');
    }
    
    public function getReportePolizasXConcesion(Request $request) {
        $data = ReportesHelper::getReportePolizasXConcesionQuery($request,false);

        $dataSet = array();
        foreach ($data as $d) {
            $estatus_p = "";

            /*switch($d->estatus){
                case 0:
                    $estatus_p = '<p>'.__("messages.pendiente").'</p>';
                    break;
                case 1:
                    $estatus_p = '<p style="color:red;">'.__("messages.inconsistente").'</p>';
                    break;
                case 2:
                    $estatus_p = '<p style="color:#267E15;">'.__("messages.revisada").'</p>';
                    break;
            }*/
            
            $ds = array($d->propietario, $d->no_concesion, $d->tipo_servicio, $d->grupo, $d->modalidad, $d->no_poliza, $d->aseguradora, date("d/m/Y", strtotime($d->fecha_vencimiento_poliza)), $d->user_creacion, $d->observaciones);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "reporte_polizas_x_concesion",
        ]);
    }
    
    public function reportePolizasXConcesion(Request $request) {
        //$estatus = array(0=>"Pendiente",1=>"Inconsistente",2=>"Revisada");
        $users_creacion = PolizasSeguro::select('created_by')->groupBy('created_by')->get();
        $tipo_servicio = DetalleConcesion::select('tipo_servicio')->groupBy('tipo_servicio')->get();
        $grupo = array("GRUPO 1"=>"GRUPO 1","GRUPO 2"=>"GRUPO 2","GRUPO 3"=>"GRUPO 3",);
        $modalidad = DetalleConcesion::select('modalidad')->groupBy('modalidad')->get();
        $aseguradora = Aseguradoras::select('id','nombre')->get();
        $dataSet = array();

        return view("reportes.reporte_polizas_x_concesion", [
            'dataSet' => json_encode($dataSet),
            'tipo_servicio' => $tipo_servicio,
            'grupo' => $grupo,
            'modalidad' => $modalidad,
            'aseguradora' => $aseguradora,
            'users_creacion' => $users_creacion,
        ]);
    }

    //Reporte de movimientos de concesion
    public function exportReporteMovimientosXConcesion(Request $request) {
        $datos_name = Carbon::now()->format('d-m-Y');

        return Excel::download(new ReporteMovimientosXConcesionExport($request), 'ReporteMovimientosXConcesion_'.$datos_name.'.xlsx');
    }

    public function exportReporteMovimientosXConcesionPdf(Request $request) {
        $datos_name = Carbon::now()->format('d-m-Y');
        $data = ReportesHelper::getReporteMovimientosXConcesionQuery($request,true);

        $dataSet = array();
        foreach ($data as $d) {
            $json = json_decode($d->datos);
            $no_concesion = "";
            $aseguradora = "";
            $id_aseguradora = "";
            $no_poliza = "";
            $vencimiento = "";
            
            switch($d->modulo){
                case 'Detalle de pago': case 'Poliza seguro':
                    $datos = $json->nuevo;
                    $no_concesion = $datos->no_concesion;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = isset($datos->id_aseguradora) ? $datos->id_aseguradora : '';
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    break;
                case 'Nueva poliza seguro': case 'Polizas seguro historico':
                    $datos = $json->nuevo;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = $datos->id_aseguradora;
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    $no_concesion = isset($datos->update_detalle_concesion) ? $datos->update_detalle_concesion->no_concesion : '';
                    break;
                /*case 'getpagos':
                    $no_concesion = $json->no_concesion;
                    break;*/
            }

            if($aseguradora == "" && $id_aseguradora != ""){
                $qaseguradora = Aseguradoras::select('id','nombre')->where('id',$id_aseguradora)->first();
                $aseguradora = $qaseguradora->nombre;
            }

            $ds = array("no_concesion"=>$no_concesion, "usuario"=>$d->usuario, "accion"=>$d->accion, "aseguradora"=>$aseguradora, "no_poliza"=>$no_poliza, "vencimiento"=>$vencimiento, "created_at"=>date("d/m/Y H:i:s", strtotime($d->created_at)));
            $dataSet[] = $ds;
        }
        
        $returnData = array('title'=>__('messages.reporte_movimientos_x_concesion'),'data'=>$dataSet);
        $pdf = \PDF::loadView('reportes.pdf.reporte_movimientos_x_concesion_pdf',$returnData);
        return $pdf->download('reporte_movimientos_x_concesion_'.$datos_name.'.pdf');
    }
    
    public function getReporteMovimientosXConcesion(Request $request) {
        $data = ReportesHelper::getReporteMovimientosXConcesionQuery($request,false);

        $dataSet = array();
        foreach ($data as $d) {
            $json = json_decode($d->datos);
            $no_concesion = "";
            $aseguradora = "";
            $id_aseguradora = "";
            $no_poliza = "";
            $vencimiento = "";
            
            switch($d->modulo){
                case 'Detalle de pago': case 'Poliza seguro':
                    $datos = $json->nuevo;
                    $no_concesion = $datos->no_concesion;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = isset($datos->id_aseguradora) ? $datos->id_aseguradora : '';
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    break;
                case 'Nueva poliza seguro': case 'Polizas seguro historico':
                    $datos = $json->nuevo;
                    $no_poliza = $datos->no_poliza;
                    $id_aseguradora = $datos->id_aseguradora;
                    if($id_aseguradora == 11){
                        $aseguradora = $datos->otro_aseguradora;
                    }
                    $vencimiento = isset($datos->fecha_vencimiento) ? date("d/m/Y", strtotime($datos->fecha_vencimiento)) : '';
                    $no_concesion = isset($datos->update_detalle_concesion) ? $datos->update_detalle_concesion->no_concesion : '';
                    break;
                /*case 'getpagos':
                    $no_concesion = $json->no_concesion;
                    break;*/
            }

            if($aseguradora == "" && $id_aseguradora != ""){
                $qaseguradora = Aseguradoras::select('id','nombre')->where('id',$id_aseguradora)->first();
                $aseguradora = $qaseguradora->nombre;
            }

            $dts = '<p><b>Aseguradora: </b>'.$aseguradora.'</p><p><b>Num. de póliza: </b>'.$no_poliza.'</p><p><b>Vencimiento: </b>'.$vencimiento.'</p>';

            $ds = array($no_concesion, $d->usuario, $d->accion, $dts, date("d/m/Y H:i:s", strtotime($d->created_at)));
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "reporte_movimientos_x_concesion",
        ]);
    }
    
    public function reporteMovimientosXConcesion(Request $request) {
        $users = Bitacora::select('usuario')
            ->whereIn('modulo',['Detalle de pago','Nueva poliza seguro','Polizas seguro historico','Poliza seguro'])
            ->groupBy('usuario')
            ->get();
        $dataSet = array();

        return view("reportes.reporte_movimientos_x_concesion", [
            'dataSet' => json_encode($dataSet),
            'users' => $users,
        ]);
    }
}