<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;

use Config;
use App\Models\Manual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ManualHelper{

    //Cuestionario
    public static function saveFile($datos,$ruta){
        try {
            $id_act = $datos->id_act;

            //Log::channel('daily')->debug('id nombre '.$datos->name);
            //Log::channel('daily')->debug('id users '.$datos->users);            
            //Log::channel('daily')->debug('id ruta '.$ruta);            
            
            $array_data_act = [];
            $data_old_act = [];

            $manual = $id_act!=null ? Manual::where('id', $id_act)->firstOrFail() : new Manual();
           
            if(!empty($manual)){
                $data_old_act = array(
                    'nombre' => $manual->nombre,
                    'ruta' => $manual->ruta,
                    'usuarios' => $manual->usuarios,
                    'estatus' => $manual->estatus,
                    'usuario_creacion' => $manual->usuario_creacion,
                    'usuario_modificacion' => $manual->usuario_modificacion,
                    'created_at'=>date("d/m/Y H:i:s", strtotime($manual->created_at)),
                    'updated_at'=>date("d/m/Y H:i:s", strtotime($manual->updated_at)),
                );
                
                $manual->usuario_modificacion = Auth::user()->username;
            }else{
                $manual->usuario_creacion = Auth::user()->username;
                
            }
            $manual->nombre = $datos->name;
            $manual->estatus = 1;
            $manual->id_sistema = 1;
            $manual->ruta = $ruta;
            $manual->usuarios = $datos->users;
            
            $manual->save();

            $data_new_act = array(
                'nombre' => $manual->nombre,
                'ruta' => $manual->ruta,
                'usuarios' => $manual->usuarios,
                'estatus' => $manual->estatus,
                'usuario_creacion' => $manual->usuario_creacion,
                'usuario_modificacion' => $manual->usuario_modificacion,
                'created_at'=>date("d/m/Y H:i:s", strtotime($manual->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($manual->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data_act = array(
                'tabla'=>'manuales',
                'anterior'=>$data_old_act,
                'nuevo'=>$data_new_act
            );
            //guardamos bitacora de registro en BitacoraHelper
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Manual_de_usuario",$manual ? "Edicion" : "Registro",json_encode($array_data_act));

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function deleteManual($data){
        try {

            $id = $data->id;     
            
            $array_data_act = [];
            $data_old_act = [];

            $manual = Manual::where('id', $id)->firstOrFail();
           
            if(!empty($manual)){
                $data_old_act = array(
                    'nombre' => $manual->nombre,
                    'ruta' => $manual->ruta,
                    'usuarios' => $manual->usuarios,
                    'estatus' => $manual->estatus,
                    'usuario_creacion' => $manual->usuario_creacion,
                    'usuario_modificacion' => $manual->usuario_modificacion,
                    'created_at'=>date("d/m/Y H:i:s", strtotime($manual->created_at)),
                    'updated_at'=>date("d/m/Y H:i:s", strtotime($manual->updated_at)),
                );
                
                $manual->usuario_modificacion = Auth::user()->username;
                $manual->estatus = 0;
                $manual->save();
            }

            $data_new_act = array(
                'nombre' => $manual->nombre,
                'ruta' => $manual->ruta,
                'usuarios' => $manual->usuarios,
                'estatus' => $manual->estatus,
                'usuario_creacion' => $manual->usuario_creacion,
                'usuario_modificacion' => $manual->usuario_modificacion,
                'created_at'=>date("d/m/Y H:i:s", strtotime($manual->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($manual->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data_act = array(
                'tabla'=>'manuales',
                'anterior'=>$data_old_act,
                'nuevo'=>$data_new_act
            );
            //guardamos bitacora de registro en BitacoraHelper
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"Manual_de_usuario","Edicion",json_encode($array_data_act));

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
}