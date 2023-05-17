<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\administracion\Bitacora;
use Request;
use Auth;
use DB;
use Session;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //Validacion de Permisos de Usuario
    public static function check_permission($module, $bt = true) {
    	if(Auth::user()->sudo == 1)
    		$permiso = true;
    	else
    		$permiso = DB::select('CALL sp_check_permission(?, ?)', [Auth::user()->id, $module]);
    	if($permiso) {
            if($bt) {
                $ip = Request::getClientIp();
                $estructura = DB::select('SELECT modulo, tipo FROM adm_funciones  ' );
                if(count($estructura) > 0){
                    $estructura = $estructura[0];
                    $fecha_movimiento = \Carbon\Carbon::now()->toDateTimeString();
                    $bitacora = new Bitacora();
                    $bitacora->username = Auth::user()->username;
                    $bitacora->accion = $estructura->tipo;
                    $bitacora->modulo = $estructura->modulo;
                    $bitacora->ip_origen = $ip;
                    $bitacora->fecha_movimiento = $fecha_movimiento;
                    $bitacora->save();
                }else{
                    abort('401');
                }
            }
    		return true;
        }
    	else
    		abort('401');
    }
}
