<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\DetalleConcesion;
use App\Models\DetallePago;
use App\Models\PolizasSeguro;


class AdminPolizasConcesionesHelper{

    public static function changeEstatusPoliza($request){
        try{
            $id = $request->input('id_poliza');
            $obj_poliza = PolizasSeguro::where('id',$id)->firstOrFail();

            $data_old = array(
                "id"=>$obj_poliza->id,
                "no_concesion"=>$obj_poliza->no_concesion,
                "no_poliza"=>$obj_poliza->no_poliza,
                "verificado"=>$obj_poliza->verificado,
                "observaciones"=>$obj_poliza->observaciones,
                "created_by"=>$obj_poliza->created_by,
                "updated_by"=>$obj_poliza->updated_by,
                'created_at'=>date("d/m/Y H:i:s", strtotime($obj_poliza->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($obj_poliza->updated_at)),
            );

            $obj_poliza->verificado = $request->input('estatus');
            if($request->input('estatus') == 1){
                $obj_poliza->observaciones = $request->input('observaciones');
            }
            else{
                $obj_poliza->observaciones = "";
            }
            $obj_poliza->updated_by = Auth::user()->username;
            $obj_poliza->save();

            $data_new = array(
                "id"=>$obj_poliza->id,
                "no_concesion"=>$obj_poliza->no_concesion,
                "no_poliza"=>$obj_poliza->no_poliza,
                "verificado"=>$request->input('estatus'),
                "observaciones"=>$obj_poliza->observaciones,
                "created_by"=>$obj_poliza->created_by,
                "updated_by"=>Auth::user()->username,
                'created_at'=>date("d/m/Y H:i:s", strtotime($obj_poliza->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($obj_poliza->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla'=>'spcl_polizas_seguro',
                'anterior'=>$data_old,
                'nuevo'=>$data_new
            );
            //guardamos bitacora de registro en BitacoraHelper
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Polizas seguro","Cambio estatus",json_encode($array_data));
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function agregarPolizaSeguro($request,$tipo){
        try{
            $polizaexixtente = \DB::table('spcl_polizas_seguro')
            ->where('no_concesion', '!=', $request->No_Consesion)
            ->where('no_poliza', '=', $request->num_poliz)   
            ->count();
        //  return $polizaexixtente;
        if ($polizaexixtente > 0) {
            $returnData = array(
                'status' => 'error',
                'title' => 'Poliza ya existe',
                'message' => 'la poliza ingresada ya existe',
               
            );
            // return redirect()->back()->withErrors("Error al consumir servicio")->withInput($request->input());
            return response()->json($returnData);
        }





            $tipos_array = array('a'=>'Nueva poliza','b'=>'Reemplazo poliza');
            $detellesconcesion = \DB::table('spcl_polizas_seguro')->where('no_concesion', '=', $request->No_Consesion)->first();
            if ((isset($detellesconcesion->fecha_vencimiento) && $detellesconcesion->fecha_vencimiento < date("Y-m-d")) || (isset($detellesconcesion->verificado) && $detellesconcesion->verificado == '1')) {
                $archivo_poliza = $detellesconcesion->archivo_poliza;
                $pieces = explode(".", $archivo_poliza);
                DB::table('spcl_polizas_seguro_historico')->updateOrInsert([
                    'no_concesion' => $detellesconcesion->no_concesion,
                    'fecha_vencimiento' => $detellesconcesion->fecha_vencimiento,
                ], [
                    'id_aseguradora' => $detellesconcesion->id_aseguradora,
                    'no_poliza' => $detellesconcesion->no_poliza,
                    'otro_aseguradora' => $detellesconcesion->otro_aseguradora,
                    'archivo_poliza' => $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1],
                    'verificado' => $detellesconcesion->verificado,
                    'observaciones' => $detellesconcesion->observaciones,
                    "created_by" => $detellesconcesion->created_by,
                    'Extension_archivo_poliza' => $pieces[1],
    
                ]);
                $archivo = $detellesconcesion->archivo_poliza;
    
                if (\Storage::disk('s3')->exists($detellesconcesion->archivo_poliza)) {
                    \Storage::disk('s3')->move($detellesconcesion->archivo_poliza, $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1]);
                }
                $data_new = array(
                    'id_aseguradora' => $detellesconcesion->id_aseguradora,
                    'no_poliza' => $detellesconcesion->no_poliza,
                    'otro_aseguradora' => $detellesconcesion->otro_aseguradora,
                    'archivo_poliza' => $pieces[0] . $detellesconcesion->fecha_vencimiento . '.' . $pieces[1],
                    'verificado' => $detellesconcesion->verificado,
                    'observaciones' => $detellesconcesion->observaciones,
                    "created_by" => $detellesconcesion->created_by,
                    'Extension_archivo_poliza' => $pieces[1],
                );
                $array_data = array(
                    'tabla'=>'spcl_polizas_seguro_historico',
                    'anterior'=>array(),
                    'nuevo'=>$data_new
                );
                //guardamos bitacora de registro en BitacoraHelper
                if($tipo == 'a'){
                    BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(),"Polizas seguro historico",$tipos_array[$tipo],json_encode($array_data),$request->nombconses);
                }
                else{
                    BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Polizas seguro historico",$tipos_array[$tipo],json_encode($array_data));
                }
            }

            $fileExt = $request->archivo->getClientOriginalExtension();
            $archivo = $request->file('archivo')->storeAs('public/consesiones', $request->No_Consesion . "." . $fileExt);
            $exist = \Storage::disk('s3')->has('public/consesiones/' . $request->No_Consesion . "." . $fileExt);

            if ($exist == 1 && $exist != null && $exist != '') {
                DB::table('spcl_polizas_seguro')->updateOrInsert([
                    'no_concesion' => $request->No_Consesion], [
                    'id_aseguradora' => $request->aseg,
                    'no_poliza' => $request->num_poliz,
                    'otro_aseguradora' => $request->asegotro == null ? "NO" : $request->asegotro,
                    'fecha_vencimiento' => $request->aseg_vencim,
                    'archivo_poliza' => $archivo,
                    'verificado' => "1",
                    'observaciones' => $request->observaciones,
                    'Extension_archivo_poliza' => $fileExt,
                    "created_by" => $tipo == 'a' ? $request->nombconses : Auth::user()->username,
                ]);

                \DB::table('spcl_detalle_concesion')
                ->where('no_concesion','=',$request->No_Consesion)
                ->limit(1)
                ->update([
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                    "updated_by" => $tipo == 'a' ? $request->nombconses : Auth::user()->username,
                ]);

                $data_new = array(
                    'id_aseguradora' => $request->aseg,
                    'no_poliza' => $request->num_poliz,
                    'otro_aseguradora' => $request->asegotro == null ? "NO" : $request->asegotro,
                    'fecha_vencimiento' => $request->aseg_vencim,
                    'archivo_poliza' => $archivo,
                    'verificado' => "0",
                    'observaciones' => $request->observaciones,
                    'Extension_archivo_poliza' => $fileExt,
                    "created_by" => $tipo == 'a' ? $request->nombconses : Auth::user()->username,
                    "update_detalle_concesion" => array(
                        'no_concesion' => $request->No_Consesion,
                        'telefono' => $request->telefono,
                        'email' => $request->email,
                        "updated_by" => $tipo == 'a' ? $request->nombconses : Auth::user()->username,
                    ),
                );
                $array_data = array(
                    'tabla'=>'spcl_polizas_seguro',
                    'anterior'=>array(),
                    'nuevo'=>$data_new
                );
                //guardamos bitacora de registro en BitacoraHelper
                if($tipo == 'a'){
                    BitacoraHelper::saveBitacoracont(BitacoraHelper::getIp(),"Nueva poliza seguro",$tipos_array[$tipo],json_encode($array_data),$request->nombconses);
                }
                else{
                    BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Nueva poliza seguro",$tipos_array[$tipo],json_encode($array_data));
                }
    
                $returnData = array(
                    'status' => 'success',
                    'title' => 'Correcto',
                    'message' => 'Póliza guardada con éxito',
                );
                return $returnData;
            } else {
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'No se pudo cargar el archivo de póliza, intente de nuevo',
                );
                return $returnData;
            }
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function replaceFilePoliza($request){
        $obj_poliza = PolizasSeguro::where('id',$request->id_poliza)->firstOrFail();
        try{
            $existOldFile = Storage::disk('s3')->has($obj_poliza->archivo_poliza);
            if($existOldFile == 1 && $existOldFile != null && $existOldFile != ''){
                Storage::delete($obj_poliza->archivo_poliza);
            }

            $fileExt = $request->archivo->getClientOriginalExtension();
            $archivo = $request->file('archivo')->storeAs('public/consesiones', $obj_poliza->no_concesion . "." . $fileExt);
            $exist = \Storage::disk('s3')->has('public/consesiones/' . $obj_poliza->no_concesion . "." . $fileExt);

            if ($exist == 1 && $exist != null && $exist != '') {
                $data_old = array(
                    'no_poliza' => $obj_poliza->no_poliza,
                    'id_aseguradora' => $obj_poliza->id_aseguradora,
                    'otro_aseguradora' => $obj_poliza->otro_aseguradora,
                    'fecha_vencimiento' => $obj_poliza->fecha_vencimiento,
                    'archivo_poliza' => $obj_poliza->archivo_poliza,
                    'Extension_archivo_poliza' => $obj_poliza->Extension_archivo_poliza,
                    "updated_by" => $obj_poliza->updated_by,
                    "updated_at" => $obj_poliza->updated_at,
                    'no_concesion' => $obj_poliza->no_concesion,
                );

                $obj_poliza->archivo_poliza = $archivo;
                $obj_poliza->Extension_archivo_poliza = $fileExt;
                $obj_poliza->updated_by = Auth::user()->username;
                $obj_poliza->save();

                $data_new = array(
                    'no_poliza' => $obj_poliza->no_poliza,
                    'id_aseguradora' => $obj_poliza->id_aseguradora,
                    'otro_aseguradora' => $obj_poliza->otro_aseguradora,
                    'fecha_vencimiento' => $obj_poliza->fecha_vencimiento,
                    'archivo_poliza' => $archivo,
                    'Extension_archivo_poliza' => $fileExt,
                    "updated_by" => Auth::user()->username,
                    "updated_at" => $obj_poliza->updated_at,
                    'no_concesion' => $obj_poliza->no_concesion,
                );
                $array_data = array(
                    'tabla'=>'spcl_polizas_seguro',
                    'anterior'=>$data_old,
                    'nuevo'=>$data_new
                );
                //guardamos bitacora de registro en BitacoraHelper
                BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Poliza seguro","Reemplazo archivo",json_encode($array_data));
    
                $returnData = array(
                    'status' => 'success',
                    'title' => 'Correcto',
                    'message' => 'Archivo de póliza actualizado con éxito',
                );
                return $returnData;
            } else {
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'No se pudo cargar el archivo de póliza, intente de nuevo',
                );
                return $returnData;
            }
        } catch(\Exception $exp) {
            $fileExt = $request->archivo->getClientOriginalExtension();
            $exist = \Storage::disk('s3')->has('public/consesiones/' . $obj_poliza->no_consesion . "." . $fileExt);

            if ($exist == 1 && $exist != null && $exist != '') {
                Storage::delete('public/consesiones', $obj_poliza->no_consesion . "." . $fileExt);
            }
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function getPolizasConcesionQuery($request,$is_export){
        $array_where = [];
        $array_select = [];

        array_push($array_where, ['spcl_dp.ejercicio', '=',intval(Carbon::now()->format('Y'))]);
        if ($request->input('tipo_servicio_filter') != null) {
            array_push($array_where, ['spcl_dcon.tipo_servicio', '=', $request->input('tipo_servicio_filter')]);
        }
        if ($request->input('modalidad_filter') != null) {
            array_push($array_where, ['spcl_dcon.modalidad', '=', $request->input('modalidad_filter')]);
        }
        if ($request->input('aseguradora_filter') != null) {
            array_push($array_where, ['cat_a.id', '=', $request->input('aseguradora_filter')]);
        }
        /*if ($request->input('estatus_poliza_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.verificado', '=', $request->input('estatus_poliza_filter')]);
        }*/
        if ($request->input('user_creacion_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.created_by', '=', $request->input('user_creacion_filter')]);
        }
        if ($request->input('estatus_pago_filter') != null) {
            switch($request->input('estatus_pago_filter')){
                case 0: 
                    array_push($array_where, ['spcl_dp.estatus_pago', '=', $request->input('estatus_pago_filter')]);
                    break;
                case 1:
                    array_push($array_where, ['spcl_dp.estatus_pago', '=', $request->input('estatus_pago_filter')]);
                    array_push($array_where, ['spcl_dp.orden_pago', '!=', 'N/A']);
                    break;
                case 2:
                    array_push($array_where, ['spcl_dp.estatus_pago', '=', 1]);
                    array_push($array_where, ['spcl_dp.orden_pago', '=', 'N/A']);
                    break;
            }
        }
        if ($request->input('fecha_ini_poliza_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.created_at', '>=', $request->input('fecha_ini_poliza_filter')]);
        }
        if ($request->input('fecha_fin_poliza_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.created_at', '<=', $request->input('fecha_fin_poliza_filter')]);
        }

        if($is_export){
            $array_select = [
                'spcl_polizas_seguro.no_concesion',
                'spcl_dcon.no_placas',
                'spcl_dcon.no_serie_vehiculo',
                'spcl_dcon.rfc',
                'spcl_dcon.tipo_servicio',
                DB::raw('DATE_FORMAT(spcl_polizas_seguro.created_at,"%d/%m/%Y") as fecha_creacion_poliza'),
                'spcl_dcon.modalidad',
                'spcl_polizas_seguro.no_poliza',
                DB::raw('IF(cat_a.id = 11,spcl_polizas_seguro.otro_aseguradora,cat_a.nombre) as aseguradora'),
                DB::raw('DATE_FORMAT(spcl_polizas_seguro.fecha_vencimiento,"%d/%m/%Y") as fecha_vencimiento'),
                //DB::raw('IF(spcl_polizas_seguro.verificado=0,"Pendiente",IF(spcl_polizas_seguro.verificado=1,"Inconsistente","Revisada")) as verificado'),
                'spcl_polizas_seguro.created_by as user_creacion',
                DB::raw('IF(spcl_dp.estatus_pago=0,"Pendiente de pago",IF(spcl_dp.estatus_pago=1 && spcl_dp.orden_pago != "N/A","Pagado","Sin adeudos")) as estatus_pago'),
            ];
        }
        else{
            $array_select = [
                'spcl_polizas_seguro.id',
                'spcl_polizas_seguro.no_concesion',
                'spcl_dcon.no_placas',
                'spcl_dcon.no_serie_vehiculo',
                'spcl_dcon.rfc',
                'spcl_dcon.tipo_servicio',
                'spcl_polizas_seguro.fecha_vencimiento',
                'spcl_dcon.modalidad',
                'spcl_polizas_seguro.no_poliza',
                DB::raw('IF(cat_a.id = 11,spcl_polizas_seguro.otro_aseguradora,cat_a.nombre) as aseguradora'),
                'spcl_polizas_seguro.archivo_poliza',
                'spcl_polizas_seguro.created_at as fecha_creacion_poliza',
                'spcl_dcon.estatus',
                'spcl_polizas_seguro.created_by as user_creacion',
                'spcl_dp.estatus_pago',
                'spcl_dp.orden_pago'
            ];
        }

        $query = PolizasSeguro::select($array_select)
            ->join('spcl_detalle_concesion as spcl_dcon', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dcon.no_concesion')
            ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
            ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion')
            ->where($array_where)
            ->get();

        return $query;
    }

    public static function getPolizasConcesionQueryPaginado($request){
        $array_where = [];
        $array_select = [];

        $draw = $request->get('draw');
        $drawin = $request->get('filtros');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $search_arr = $request->get('search');
        $searchValue = $search_arr && array_key_exists('value',$search_arr) ? $search_arr['value'] : ''; // Search value*/

        $array_orwhere = [
            ['spcl_dcon.no_placas','like','%'.$searchValue.'%'],
            ['spcl_dcon.no_serie_vehiculo','like','%'.$searchValue.'%'],
            ['spcl_dcon.rfc','like','%'.$searchValue.'%'],
            ['spcl_dcon.tipo_servicio','like','%'.$searchValue.'%'],
            ['spcl_dcon.modalidad','like','%'.$searchValue.'%'],
            ['spcl_polizas_seguro.no_poliza','like','%'.$searchValue.'%'],
            ['cat_a.nombre','like','%'.$searchValue.'%'],
            ['spcl_polizas_seguro.created_at','like','%'.$searchValue.'%'],
            ['spcl_dcon.estatus','like','%'.$searchValue.'%'],
            ['spcl_polizas_seguro.created_by','like','%'.$searchValue.'%'],
            ['spcl_dp.estatus_pago','like','%'.$searchValue.'%'],
            ['spcl_dp.orden_pago','like','%'.$searchValue.'%'],
        ];
        array_push($array_where, ['spcl_dp.ejercicio', '=',intval(Carbon::now()->format('Y'))]);
        foreach ($drawin as $filtros) {
            if ($filtros['name'] == 'tipo_servicio_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_dcon.tipo_servicio', '=', $filtros['value']]);
            }
            if ($filtros['name'] == 'modalidad_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_dcon.modalidad', '=', $filtros['value']]);
            }
            if ($filtros['name'] == 'aseguradora_filter' && $filtros['value'] != null) {
                array_push($array_where, ['cat_a.id', '=', $filtros['value']]);
            }
            /*if ($filtros['name'] == 'estatus_poliza_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_polizas_seguro.verificado', '=', $filtros['value']]);
            }*/
            if ($filtros['name'] == 'user_creacion_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_polizas_seguro.created_by', '=', $filtros['value']]);
            }
            if ($filtros['name'] == 'estatus_pago_filter' && $filtros['value'] != null) {
                switch($filtros['value']){
                    case 0: 
                        array_push($array_where, ['spcl_dp.estatus_pago', '=', $filtros['value']]);
                        break;
                    case 1:
                        array_push($array_where, ['spcl_dp.estatus_pago', '=', $filtros['value']]);
                        array_push($array_where, ['spcl_dp.orden_pago', '!=', 'N/A']);
                        break;
                    case 2:
                        array_push($array_where, ['spcl_dp.estatus_pago', '=', 1]);
                        array_push($array_where, ['spcl_dp.orden_pago', '=', 'N/A']);
                        break;
                }
            }
            if ($filtros['name'] == 'fecha_ini_poliza_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_polizas_seguro.created_at', '>=', $filtros['value']]);
            }
            if ($filtros['name'] == 'fecha_fin_poliza_filter' && $filtros['value'] != null) {
                array_push($array_where, ['spcl_polizas_seguro.created_at', '<=', $filtros['value']]);
            }
        }

        $totalRecords = PolizasSeguro::select('count(*) as allcount')
            ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion' )
            ->where('spcl_dp.ejercicio', '=',intval(Carbon::now()->format('Y')))
            ->count();
        $totalRecordswithFilter = $totalRecords;
        
        $array_select = [
            'spcl_polizas_seguro.id',
            'spcl_polizas_seguro.no_concesion',
            'spcl_dcon.no_placas',
            'spcl_dcon.no_serie_vehiculo',
            'spcl_dcon.rfc',
            'spcl_dcon.tipo_servicio',
            'spcl_dcon.modalidad',
            'spcl_polizas_seguro.no_poliza',
            DB::raw('IF(cat_a.id = 11,spcl_polizas_seguro.otro_aseguradora,cat_a.nombre) as aseguradora'),
            'spcl_polizas_seguro.archivo_poliza',
            'spcl_polizas_seguro.fecha_vencimiento',
            'spcl_polizas_seguro.created_at as fecha_creacion_poliza',
            'spcl_dcon.estatus',
            'spcl_polizas_seguro.created_by as user_creacion',
            'spcl_dp.id as id_dp',
            'spcl_dp.estatus_pago',
            'spcl_dp.orden_pago'
        ];

        if (isset($searchValue)) {
            $totalRecordswithFilter = DB::table('spcl_polizas_seguro')
                ->select($array_select)
                ->join('spcl_detalle_concesion as spcl_dcon', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dcon.no_concesion')
                ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
                ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion')
                ->where($array_where)
                ->where('spcl_polizas_seguro.no_concesion', 'like', '%' . $searchValue . '%');

            foreach($array_orwhere as $item){
                $totalRecordswithFilter = $totalRecordswithFilter->orWhere($item[0],$item[1],$item[2]);
            }

            $totalRecordswithFilter = count($totalRecordswithFilter->groupBy('spcl_polizas_seguro.id')->get());
            
            $query = DB::table('spcl_polizas_seguro')
                ->select($array_select)
                ->join('spcl_detalle_concesion as spcl_dcon', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dcon.no_concesion')
                ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
                ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion')
                ->where($array_where)
                ->where('spcl_polizas_seguro.no_concesion', 'like', '%' . $searchValue . '%');

            foreach($array_orwhere as $item){
                $query = $query->orWhere($item[0],$item[1],$item[2]);
            }
            
            $query = $query->groupBy('spcl_polizas_seguro.id')
                ->skip($start)
                ->take($rowperpage)
                ->get();
                
            return array(
                "query" => $query,
                "draw" => intval($draw),
                "totalRecords" => $totalRecords,
                "totalRecordswithFilter" => $totalRecordswithFilter,
            );
        }
        else{
            $totalRecordswithFilter = count(DB::table('spcl_polizas_seguro')
                ->select($array_select)
                ->join('spcl_detalle_concesion as spcl_dcon', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dcon.no_concesion')
                ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
                ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion')
                ->where($array_where)
                ->groupBy('spcl_polizas_seguro.id')
                ->get());
                
            $query = DB::table('spcl_polizas_seguro')
                ->select($array_select)
                ->join('spcl_detalle_concesion as spcl_dcon', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dcon.no_concesion')
                ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
                ->join('spcl_detalle_pago as spcl_dp', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dp.no_concesion')
                ->where($array_where)
                ->groupBy('spcl_polizas_seguro.id')
                ->skip($start)
                ->take($rowperpage)
                ->get();
                
            return array(
                "query" => $query,
                "draw" => intval($draw),
                "totalRecords" => $totalRecords,
                "totalRecordswithFilter" => $totalRecordswithFilter,
            );
        }
    }

    public static function getDetalleDatosConcesion($request){
        $id = $request->input('id_poliza');
        $objPoliza = PolizasSeguro::select(
                'spcl_polizas_seguro.id', 
                'spcl_polizas_seguro.no_concesion', 
                DB::raw('IF(cat_a.id = 11,spcl_polizas_seguro.otro_aseguradora,cat_a.nombre) as aseguradora'), 
                'spcl_polizas_seguro.no_poliza', 
                'spcl_polizas_seguro.fecha_vencimiento as fecha_vencimiento_poliza', 
                'spcl_polizas_seguro.created_at as fecha_creacion_poliza', 
                'spcl_polizas_seguro.verificado as estatus', 
                'spcl_polizas_seguro.observaciones'
            )
            ->where('spcl_polizas_seguro.id','=',$id)
            ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
            ->firstOrFail();
            
        $objConcesion = DetalleConcesion::select(
                'objeto_contrato',
                'cuenta_contrato',
                'interlocutor',
                'rfc',
                'propietario',
                'no_placas',
                'no_serie_vehiculo',
                'grupo',
                'tipo_servicio',
                'modalidad'
            )
            ->where('no_concesion','=',$objPoliza->no_concesion)
            ->firstOrFail();

        $objDetallePago = DetallePago::select(
                'detalle_conceptos',
                'convenio_bancos',
                DB::raw('FORMAT(importe_total,2) as importe_total'),
                'linea_captura',
                'orden_pago',
                'fecha_vencimiento',
                'estatus_pago',
                'ejercicio',
                DB::raw('FORMAT(importe_concesion,2) as importe_concesion'),
                DB::raw('FORMAT(importe_refrendo,2) as importe_refrendo')
            )
            ->where('no_concesion','=',$objPoliza->no_concesion)
            ->firstOrFail();

        $returnData = array(
            'objPoliza'=>$objPoliza,
            'objConcesion'=>$objConcesion,
            'objDetallePago'=>$objDetallePago,
        );
        return $returnData;
    }
}