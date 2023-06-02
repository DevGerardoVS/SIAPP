<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;

use Config;
use App\Models\administracion\Bitacora;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BitacoraHelper{

    /**
     * Función para ingresar datos a tabla de bitácora
     * @param host: IP de la PC que realiza acción 
     * @param modulo: Módulo/Catálogo desde donde se realiza la acción
     * @param accion: La acción realizada (Registro, Edición, Eliminación, etc.)
     * @param datos: datos que se igresaron/modificaron
     * @version 1.0
     * @author Luis Fernando Zavala 21-04-2022
     */
    public static function saveBitacora($host,$modulo,$accion,$datos){
        try {
            $bitacora = new Bitacora();
            $bitacora->username = Auth::user()->username;
            $bitacora->ip_origen = $host;
            $bitacora->accion = $accion;
            $bitacora->modulo = $modulo;
            $bitacora->fecha_movimiento = $datos;
            $bitacora->save();
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function saveBitacoracont($host,$modulo,$accion,$datos,$usuario){
        try {
            $bitacora = new Bitacora();
            $bitacora->usuario = $usuario;
            $bitacora->ip_origen = $host;
            $bitacora->modulo = $modulo;
            $bitacora->accion = $accion;
            $bitacora->fecha_movimiento = $datos;
            $bitacora->save();
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    /**
     * Función para obtener IP de la PC desde donde se realiza la acción
     * @return: IP estatica de la PC o retorna request()->ip() en caso de no encontrar la del PC
     * @version 1.0
     * @author Luis Fernando Zavala 21-04-2022
     */
    public static function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }

    public static function getUsuBitacora(){
        $query = Bitacora::select('bitacora.usuario')
            ->where('bitacora.usuario','not like','%@%')
            ->groupBy('bitacora.usuario')
            ->get();
        return $query;
    }

    public static function getAccionBitacora(){ 
        $query = Bitacora::select('bitacora.accion')
            ->groupBy('bitacora.accion')
            ->get();
        
        return $query;
    }
}