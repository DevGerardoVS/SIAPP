<?php 

namespace App\Helpers;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Bitacora;
use App\Models\PolizasSeguro;

class ReportesHelper
{
    public static function getReportePolizasXConcesionQuery($request,$is_export){
        $array_where = [];
        $array_select = [];

        if ($request->input('tipo_servicio_filter') != null) {
            array_push($array_where, ['spcl_dc.tipo_servicio', '=', $request->input('tipo_servicio_filter')]);
        }
        if ($request->input('grupo_filter') != null) {
            array_push($array_where, ['spcl_dc.grupo', '=', $request->input('grupo_filter')]);
        }
        if ($request->input('modalidad_filter') != null) {
            array_push($array_where, ['spcl_dc.modalidad', '=', $request->input('modalidad_filter')]);
        }
        if ($request->input('aseguradora_filter') != null) {
            array_push($array_where, ['cat_a.id', '=', $request->input('aseguradora_filter')]);
        }
        if ($request->input('estatus_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.verificado', '=', $request->input('estatus_filter')]);
        }
        if ($request->input('fecha_ini_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.fecha_vencimiento', '>=', $request->input('fecha_ini_filter')]);
        }
        if ($request->input('fecha_fin_filter') != null) {
            array_push($array_where, ['spcl_polizas_seguro.fecha_vencimiento', '<=', $request->input('fecha_fin_filter')]);
        }

        if($is_export){
            $array_select = [
                'spcl_dc.propietario',
                'spcl_polizas_seguro.no_concesion', 
                'spcl_dc.tipo_servicio',
                'spcl_dc.grupo',
                'spcl_dc.modalidad',
                'spcl_polizas_seguro.no_poliza', 
                'cat_a.nombre as aseguradora', 
                DB::raw('DATE_FORMAT(spcl_polizas_seguro.fecha_vencimiento,"%d/%m/%Y") as fecha_vencimiento_poliza'), 
                //DB::raw('IF(spcl_polizas_seguro.verificado=0,"Pendiente",IF(spcl_polizas_seguro.verificado=1,"Inconsistente","Revisada")) as verificado'), 
                'spcl_polizas_seguro.created_by as user_creacion',
                'spcl_polizas_seguro.observaciones'
            ];
        }
        else{
            $array_select = [
                'spcl_polizas_seguro.id', 
                'spcl_dc.propietario',
                'spcl_polizas_seguro.no_concesion', 
                'spcl_dc.tipo_servicio',
                'spcl_dc.grupo',
                'spcl_dc.modalidad',
                'spcl_polizas_seguro.no_poliza', 
                'cat_a.nombre as aseguradora', 
                'spcl_polizas_seguro.fecha_vencimiento as fecha_vencimiento_poliza', 
                //'spcl_polizas_seguro.verificado as estatus', 
                'spcl_polizas_seguro.created_by as user_creacion',
                'spcl_polizas_seguro.observaciones'
            ];
        }

        $query = PolizasSeguro::select($array_select)
            ->join('spcl_detalle_concesion as spcl_dc', 'spcl_polizas_seguro.no_concesion', '=', 'spcl_dc.no_concesion')
            ->join('cat_aseguradoras as cat_a', 'spcl_polizas_seguro.id_aseguradora', '=', 'cat_a.id')
            ->where($array_where)
            ->get();

        return $query;
    }


    public static function getReporteMovimientosXConcesionQuery($request,$is_export){
        $array_where = [];
        $array_select = [];

        if ($request->input('no_concesion_filter') != null) {
            array_push($array_where, ['datos', 'like', '%'.$request->input('no_concesion_filter').'%']);
        }
        if ($request->input('user_filter') != null) {
            array_push($array_where, ['usuario', '=', $request->input('user_filter')]);
        }
        if ($request->input('fecha_ini_filter') != null) {
            array_push($array_where, ['created_at', '>=', $request->input('fecha_ini_filter')]);
        }
        if ($request->input('fecha_fin_filter') != null) {
            array_push($array_where, ['created_at', '<=', $request->input('fecha_fin_filter')]);
        }

        $query = Bitacora::select('usuario', 'modulo', 'accion', 'datos', 'created_at')
            ->whereIn('modulo',['Detalle de pago','Nueva poliza seguro',/*'getpagos',*/'Polizas seguro historico','Poliza seguro'])
            ->where($array_where)
            ->get();

        return $query;
    }
}