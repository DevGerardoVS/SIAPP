<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\administracion\Bitacora;
use Carbon\Carbon;

class BitacoraController extends Controller
{
    public function getIndex() {
    	Controller::check_permission('getBitacora');
    	return view("administracion.bitacora.index");
    }

    public function getData($fecha) {
    	if ($fecha == 0) {
    		$fecha = Carbon::now()->format('Y-m-d');
    	}
    	$query = DB::table('adm_bitacora')
    			->select('adm_bitacora.username', 'adm_bitacora.accion', 'adm_bitacora.modulo', 'adm_bitacora.ip_origen', 'adm_bitacora.fecha_movimiento', 'adm_bitacora.created_at')
    			->where('adm_bitacora.fecha_movimiento', '=', $fecha)
    			->orderBy('adm_bitacora.id', 'DESC')
    			->get();

    	return response()->json($query, 200);
    }
}
