<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminConcesionesExport;
use App\Helpers\AdminPolizasConcesionesHelper;
use App\Helpers\BitacoraHelper;
use App\Helpers\QueryHelper;
use App\Models\Aseguradoras;
use App\Models\DetalleConcesion;
use App\Models\DetallePago;
use App\Models\PolizasSeguro;
use Carbon\Carbon;

class AdminConcesionesController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAseguradoras(Request $request){
        $aseguradoras = Aseguradoras::select('id','nombre')->get();

        return response()->json(["aseguradoras" => $aseguradoras,]);
    }

    public function exportAdminConcesiones(Request $request) {
        $datos_name = Carbon::now()->format('d-m-Y');
        
        return Excel::download(new AdminConcesionesExport($request), 'AdminConcesiones_'.$datos_name.'.xlsx');
    }

    public function getAdminConcesiones(Request $request){   
        $data = AdminPolizasConcesionesHelper::getPolizasConcesionQueryPaginado($request);
        
        $dataSet = array();
        foreach ($data['query'] as $d) {
            $button = verifyPermission('concesiones.administrador_de_concesiones.ver_detalle') ? '<button class="btn btn-sm btn_detalle" data-id="' . $d->id . '" data-route="' . route('detalle_datos_concesion') . '" title="' . __("messages.ver_detalle") . '" data-bs-toggle="modal"><i class="fas fa-eye" style="color: #0d6efd;"></i></button>' : '';
            $file_poliza = verifyPermission('concesiones.administrador_de_concesiones.ver_poliza') ? '<a href="" class="btnViewFile" data-id="' . $d->id . '" title="' . __("messages.ver_poliza") . '">' . __("messages.ver_poliza") . '</a>' : '';
            $estatus_p = "";
            $estatus_pago = "";
            $button = verifyPermission('concesiones.administrador_de_concesiones.reemplazar_poliza') ? $button.'<button class="btn btn-sm btn_add_poliza" data-concesion="'.$d->no_concesion.'" data-route="'.route('agregar_poliza').'" title="' . __("messages.agregar_poliza") . '" data-bs-toggle="modal"><i class="fas fa-plus" style="color: #267E15;"></i></button>' : '';
            $button = verifyPermission('concesiones.administrador_de_concesiones.reemplazar_archivo_poliza') ? $button.'<button class="btn btn-sm btn_replace_file" data-id="'.$d->id.'" title="' . __("messages.replace_file_poliza") . '" data-bs-toggle="modal"><i class="fas fa-upload" style="color: #267E15;"></i></button>' : '';

            /*switch($d->verificado){
                case 0:
                    $estatus_p = '<p>'.__("messages.pendiente").'</p>';
                    if(verifyPermission('concesiones.administrador_de_concesiones.validar_poliza')){
                        $button = $button.'<button class="btn btn-sm btn_change_status" data-id="'.$d->id.'" data-route="'.route('change_estatus_poliza').'" title="' . __("messages.rev_poliza") . '" data-bs-toggle="modal"><i class="fas fa-square-check" style="color: #267E15;"></i></button>';
                    }
                    break;
                case 1:
                    $estatus_p = '<p style="color:red;">'.__("messages.inconsistente").'</p>';
                    if(verifyPermission('concesiones.administrador_de_concesiones.validar_poliza')){
                        $button = $button.'<button class="btn btn-sm btn_change_status" data-id="'.$d->id.'" data-route="'.route('change_estatus_poliza').'" title="' . __("messages.rev_poliza") . '" data-bs-toggle="modal"><i class="fas fa-square-check" style="color: #267E15;"></i></button>';
                    }
                    break;
                case 2:
                    $estatus_p = '<p style="color:#267E15;">'.__("messages.revisada").'</p>';
                    break;
            }*/
            
            switch($d->estatus_pago){
                case 0:
                    $estatus_pago = 'Pendiente de pago';
                    $tiene_adeudo = QueryHelper::tieneadeudo($d->no_placas,$d->no_concesion,$d->no_serie_vehiculo);
                    
                    if($tiene_adeudo == 'SIN ADEUDO'){
                        $estatus_pago = $d->orden_pago != 'N/A' ? 'Pagado' : 'Sin adeudos';
                        $data_old = array(
                            "id"=>$d->id_dp,
                            "no_concesion"=>$d->no_concesion,
                            "aseguradora"=>$d->aseguradora,
                            "fecha_vencimiento"=>$d->fecha_vencimiento,
                            "no_poliza"=>$d->no_poliza,
                            "no_placas"=>$d->no_placas,
                            "no_serie_vehiculo"=>$d->no_serie_vehiculo,
                            "estatus_pago"=>$d->estatus_pago,
                            "created_at"=>$d->fecha_creacion_poliza,
                        );
                        DB::table('spcl_detalle_pago')
                            ->where('id','=',$d->id_dp)
                            ->limit(1)
                            ->update(['estatus_pago' => 1,"updated_by"=> "Sistema",]);
                            
                        $data_new = array(
                            "id"=>$d->id_dp,
                            "no_concesion"=>$d->no_concesion,
                            "aseguradora"=>$d->aseguradora,
                            "fecha_vencimiento"=>$d->fecha_vencimiento,
                            "no_poliza"=>$d->no_poliza,
                            "no_placas"=>$d->no_placas,
                            "no_serie_vehiculo"=>$d->no_serie_vehiculo,
                            "estatus_pago"=>1,
                            "created_at"=>$d->fecha_creacion_poliza,
                            "updated_at"=> Carbon::now()->format('d-m-Y H:i:s'),
                            "updated_by"=> "Sistema",
                        );
                        $array_data = array(
                            'tabla'=>'spcl_detalle_pago',
                            'anterior'=>$data_old,
                            'nuevo'=>$data_new
                        );
                        BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Detalle de pago","Actualización estatus",json_encode($array_data));
                        
                    }
                    break;
                case 1:
                    $estatus_pago = $d->orden_pago != 'N/A' ? 'Pagado' : 'Sin adeudos';
                    break;
            }
            
            $ds = array($d->no_concesion, $d->no_placas, $d->no_serie_vehiculo, $d->rfc, $d->tipo_servicio, date("d/m/Y", strtotime($d->fecha_creacion_poliza)), $d->modalidad, $d->no_poliza, $d->aseguradora, $file_poliza, date("d/m/Y", strtotime($d->fecha_vencimiento)), $d->user_creacion, $estatus_pago, $button);
            $dataSet[] = $ds;
        }
        
        return response()->json([
            "aaData" => $dataSet,
            "draw" => $data['draw'],
            "iTotalRecords" => $data['totalRecords'],
            "iTotalDisplayRecords" => $data['totalRecordswithFilter'],
            "catalogo" => "admin_concesiones",
        ]);
    }

    public function adminConcesiones(Request $request)
    {
        $tipo_servicio = DetalleConcesion::select('tipo_servicio')->groupBy('tipo_servicio')->get();
        $grupo = array("GRUPO 1"=>"GRUPO 1","GRUPO 2"=>"GRUPO 2","GRUPO 3"=>"GRUPO 3",);
        $modalidad = DetalleConcesion::select('modalidad')->groupBy('modalidad')->get();
        $aseguradora = Aseguradoras::select('id','nombre')->get();
        $users_creacion = PolizasSeguro::select('created_by')->groupBy('created_by')->get();
        $estatus_pago = array(0=>"Pendiente de pago",1=>"Pagado",2=>"Sin adeudos");
        $dataSet = array();

        return view("admin_concesiones.admin_concesiones", [
            'dataSet' => json_encode($dataSet),
            'tipo_servicio' => $tipo_servicio,
            'grupo' => $grupo,
            'modalidad' => $modalidad,
            'aseguradora' => $aseguradora,
            'users_creacion' => $users_creacion,
            'estatus_pago' => $estatus_pago,
        ]);
    }

    public function previewFilePoliza(Request $request)
    {
        $id = $request->input('id_hidden');
        $obj_poliza = PolizasSeguro::where('id',$id)->firstOrFail();
        $path = Storage::url($obj_poliza->archivo_poliza);
        $ruta = asset($path);
        $path_info = pathinfo($path);
        $extension = $path_info['extension'];
        
        $contentTypes = [
            "pdf" =>    "application/pdf",
            "xls" =>    "application/vnd.ms-excel",
            "xlsx" =>   "application/vnd.ms-excel",
            "doc" =>    "application/msword",
            "docx" =>   "application/msword",
            "jpg" =>    "image/jpeg",
            "png" =>    "image/png",
        ];
        
        $returnData = array(
            'Content-Type'=>'application/json; charset=utf-8',
            "ruta" => $ruta,
            "content_type" => $contentTypes[$extension],
            "extension" => $extension,
        );

        return response()->json($returnData,200);
    }

    public function changeEstatusPoliza(Request $request)
    {
        try {
            DB::beginTransaction();
            AdminPolizasConcesionesHelper::changeEstatusPoliza($request);
            DB::commit();
        } catch(\Exception $exp) {
            DB::rollBack();
            Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, no se pudo cambiar el estatus de la póliza.'
            );
            return response()->json($returnData,500);
        }

        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Se cambió el estatus de la póliza con éxito'
        );

        return response()->json($returnData,200);
    }

    public function agregarPolizaSeguro(Request $request){
        try {
            DB::beginTransaction();
            $returnData = AdminPolizasConcesionesHelper::agregarPolizaSeguro($request,'b');
            DB::commit();
            
            if($returnData['status'] == 'error'){
                return response()->json($returnData,500);
            }
        } catch(\Exception $exp) {
            DB::rollBack();
            Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, no se pudo agregar la póliza.'
            );
            return response()->json($returnData,500);
        }

        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Se cambió el estatus de la póliza con éxito'
        );

        return response()->json($returnData,200);
    }

    public function detalleDatosConcesion(Request $request){
        $objDetalle = AdminPolizasConcesionesHelper::getDetalleDatosConcesion($request);

        if($request->input('action') == 'detalle'){
            $returnData = array(
                'objPoliza'=>$objDetalle['objPoliza'],
                'objConcesion'=>$objDetalle['objConcesion'],
                'objDetallePago'=>$objDetalle['objDetallePago'],
            );
            return response()->json($returnData);
        }
        else{
            $returnData = array(
                'title' => __('messages.detalle_poliza'),
                'objPoliza' => $objDetalle['objPoliza'],
                'objConcesion' => $objDetalle['objConcesion'],
                'objDetallePago' => $objDetalle['objDetallePago'],
            );
            $pdf = \PDF::loadView('admin_concesiones.pdf.detalle_poliza_concesiones',$returnData);
            return $pdf->download('detalle_poliza_concesiones_'.$objDetalle['objPoliza']->no_poliza.'_'.$objDetalle['objPoliza']->no_concesion.'_'.$objDetalle['objDetallePago']->ejercicio.'.pdf');
        }
    }

    public function replaceFilePoliza(Request $request){
        try {
            DB::beginTransaction();
            $returnData = AdminPolizasConcesionesHelper::replaceFilePoliza($request,'b');
            DB::commit();
            
            if($returnData['status'] == 'error'){
                return response()->json($returnData,500);
            }
        } catch(\Exception $exp) {
            DB::rollBack();
            Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, no se pudo reemplazar el archivo de la póliza.'
            );
            return response()->json($returnData,500);
        }

        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Se reemplazo el archivo de la póliza con éxito'
        );

        return response()->json($returnData,200);
    }
}