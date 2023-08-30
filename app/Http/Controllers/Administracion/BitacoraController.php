<?php

namespace App\Http\Controllers\Administracion;

use App\Exports\BitacoraExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Models\administracion\Bitacora;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BitacoraController extends Controller
{
    public function getIndex() {
    	Controller::check_permission('getBitacora');
    	return view("administracion.bitacora.index");
    }

	public static function getBitacora($anio,$mes){
        Controller::check_permission('getBitacora');
        Log::debug($anio);
        Log::debug($mes);
        $data = Bitacora::select(
            'username',
            'ip_origen',
            'modulo',
            'accion',
            'fecha_movimiento',
			'created_at'
        )
            ->whereYear('fecha_movimiento','=', $anio);
			if($mes !=00||$mes !='00'){
				$data=$data->whereMonth('created_at', '=',$mes);
			}
         /*    ->whereMonth('fecha_presentacion', '=', $mes);
            if ($year != $anio) {
                $query = $query->whereYear('fecha_presentacion', '=', $anio);
            } */
			
            $data =$data->get();
        $dataSet=array();

        foreach($data as $d){
            $format_date= date("d/m/Y H:i:s", strtotime($d->created_at));
            $ds = array($d->username, $d->accion, $d->modulo, $d->ip_origen, $d->fecha_movimiento,$format_date);
            $dataSet[]=$ds;
        }
        return $dataSet;
    }
	public function getBitAnios(){
        $data = Bitacora::select(
            DB::raw('YEAR(fecha_movimiento) AS anio'),
        )->groupBy('fecha_movimiento')
        ->orderBy('fecha_movimiento')
		->distinct()
		->get();
            Log::debug($data);
        $newdata=array();
        foreach($data as $d){
            $ds = array(
                $d->anio
            );
            $dataSet[]=$ds;
        }
        /* $newdata=array_unique($dataSet); */
        return $dataSet;
    }
    public function exportBitacora($anio,$mes) {
        /*Si no coloco estas lineas Falla*/
		ob_end_clean();
		ob_start();
		/*Si no coloco estas lineas Falla*/

        return Excel::download(new BitacoraExport($anio,$mes), 'bitacora_export.xlsx');
    }
}
