<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\administracion\Bitacora;
use App\Models\administracion\PermisosUpp;
use Request;
use Auth;
use DB;
use Session;
use Response;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //Validacion de Permisos de Usuario
    public static function check_permission($module, $bt = true) {
        $permiso = DB::select('SELECT p.id
        FROM adm_rel_funciones_grupos p
        INNER JOIN adm_funciones f ON f.id = p.id_funcion
        WHERE f.funcion = ?
        AND f.id_sistema = ?
        AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = ?);', [$module, Session::get('sistema'), Auth::user()->id]);
    	if($permiso) {
            if($bt) {
                $ip = Request::getClientIp();
                $estructura = DB::select('SELECT modulo, tipo FROM adm_funciones WHERE funcion=? AND id_sistema = ?', [$module, Session::get('sistema')]);
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
    public static function check_assign($name,$bt = true) {
        $permiso = DB::table('permisos_funciones')
            ->leftJoin('cat_permisos','cat_permisos.id','permisos_funciones.id_permiso')
            ->select(
                'id_user',
                'permisos_funciones.id',
                'cat_permisos.nombre as permiso')
            ->where('id_user', Auth::user()->id)
            ->orWhere('cat_permisos.nombre', $name)->get();
    	if($permiso) {
            if($bt) {
                $ip = Request::getClientIp();
                $estructura = $permiso;
                if(count($estructura) > 0){
                    $estructura = $estructura[0];
                    $fecha_movimiento = \Carbon\Carbon::now()->toDateTimeString();
                    $bitacora = new Bitacora();
                    $bitacora->username = Auth::user()->username;
                    $bitacora->accion = $estructura->permiso;
                    $bitacora->modulo = 'calendario';
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
    public static function check_assignFront($name) {
        $permiso = DB::table('permisos_funciones')
            ->select(
                'id_user',
                'id_permiso',
                )
        ->where('id_user', auth::user()->id)
        ->where('id_permiso', $name)->get();
    	if(count($permiso)) {
    		return true;
        }
    	else
        return false;
    }
}
