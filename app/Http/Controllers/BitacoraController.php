<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\BitacoraHelper;
use App\Models\Bitacora;
use App\Exports\BitacoraExport;

class BitacoraController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getBitacora(Request $request){
        $usua = $request->input('usuario_filter');
        $accion = $request->input('accion_filter');
        $fecha_inicio = $request-> input('anio_filter');
        $fecha_fin = $request-> input('anio_filter_fin');
        $array_where = [];

        if($accion != null ){
            array_push($array_where,['bitacora.accion','=',$accion]);
        }
        if($usua != null){
            array_push($array_where,['bitacora.usuario','=',$usua]);
        }
        if ($fecha_inicio != null) {
            array_push($array_where, ['bitacora.created_at', '>=', $fecha_inicio]);
        }
        if ($fecha_fin != null) {
            array_push($array_where, ['bitacora.created_at', '<=', $fecha_fin]);
        }

        $data = Bitacora::select('bitacora.usuario',
            'bitacora.host','bitacora.modulo','bitacora.accion',
            'bitacora.datos','bitacora.created_at')
            ->where($array_where)
            ->get();
    
        $dataSet=array();
        foreach($data as $d){
            $format_date= date("d/m/Y H:i:s", strtotime($d->created_at));
            $ds = array($d->usuario, $d->host, $d->modulo, $d->accion, $d->datos, $format_date);
            $dataSet[]=$ds;
        }
        
        return response()->json([
            "dataSet"=> $dataSet,
            "catalogo"=>"bitacora",
            'filters'=>$array_where
        ]);
    }

    public function bitacoras(Request $request){
        $dataSet = array();
        $usuarios = BitacoraHelper::getUsuBitacora();
        $acciones = BitacoraHelper::getAccionBitacora();
    
        return view("Bitacora.Bitacora",[
            'dataSet'=>json_encode($dataSet),
            'usuarios'=>$usuarios,
            'acciones'=>$acciones
        ]);
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getBitacora2(Request $request){
        $fecha_inicio = $request->input('anio_filter');
        $fecha_fin = $request->input('anio_filter_fin');
        $data = Bitacora::select('bitacora.id','bitacora.usuario',
            'bitacora.host','bitacora.modulo','bitacora.accion',
            'bitacora.datos','bitacora.created_at')
            ->where('bitacora.created_at','>=',$fecha_inicio)
            ->where('bitacora.created_at','<=',$fecha_fin)
            ->get();

        $dataSet=array();
        foreach($data as $d){
            $format_date= date("d/m/Y H:i:s", strtotime($d->created_at));
            $ds=array($d->usuario, $d->host, $d->modulo, $d->accion,$d->datos,$format_date);
            $dataSet[]=$ds;
        }
        
        return response()->json([
            "dataSet"=> $dataSet,
        ]);
    }

    public function exportBitacora(Request $request) {
        return Excel::download(new BitacoraExport($request), 'bitacora_export.xlsx');
    }

}
