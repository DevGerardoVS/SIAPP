<?php

namespace App\Imports\utils;

use App\Http\Controllers\Controller;
use App\Imports\utils\FunFormats;
use Log;
use App\Models\notificaciones;


class InsertCMActividades 
{
    public static function handle($arreglo,$user)
    {
    /*     DB::beginTransaction();
		try { */
            Log::debug("InsertCMActividades");
            Log::debug( $arreglo);
        $datos = json_decode($arreglo);
            $meta = 0;
                foreach ($datos as $key) {
                $key->entidad_ejecutora = getEntidadEje($key->clv_upp, $key->clv_ur, $key->ejercicio);
                Log::debug(json_encode($key));
                    switch ($key->tipoMeta) {
                        case 'C':
                            $key->nombre_actividad = null;
                            $act = FunFormats::createMml_Ac($key);
                            $key->actividad_id = $act;
                            break;
                        case 'O':
                            $key->actividad_id = null;
                            $act = FunFormats::createMml_Ac($key);
                            $key->actividad_id = $act;
                            break;
                        default:
                        $key->actividad_id = null;
                            break;
                    }
                   FunFormats::guardarMeta($key);
                $meta++;
                }
				
			if ($meta) {
				$b = array(
                    "username" =>  $user->username,
                    "accion" => 'Carga masiva metas',
                    "modulo" => 'Metas'
                );
                Controller::bitacora($b);
				
			} else {
				$res = ["status" => false, "mensaje" => ["icon" => 'error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
                Log::debug(json_encode($res));
                //return response()->json($res, 200);
			}

	/* 	} catch (\Exception $e) {
			DB::rollback();
		} */

    }
}
