<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\administracion\TipoActividadUpp;
use App\Helpers\BitacoraHelper;

class ConfiguracionesController extends Controller
{
    //
    public function getIndex()
	{
		return view('administracion.configuraciones.index');
	}

    public function GetUpps(){
        try {
            $dataSet = array();

            $dataSet = DB::table('catalogo')
                ->select('clave', 'descripcion')
                ->where('grupo_id', 6)
                ->get();

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function GetConfiguraciones(Request $request){
        try {
            $dataSet = array();
            $array_where = [];
            $filter = $request->filter;

            if($filter!=null || $filter!='') array_push($array_where, ['clave','=',$filter]);

            $data = DB::table('tipo_actividad_upp')
                ->select('clave', 'descripcion','Acumulativa','Continua','Especial')
                ->join('catalogo','tipo_actividad_upp.clv_upp','=','catalogo.clave')
                ->where('grupo_id', 6)
                ->where($array_where)
                ->get();

            $i=0;
            foreach ($data as $d) {
                //$d->tipo 
                $acumulativa = $d->Acumulativa==1? ' checked' : '';
                $continua = $d->Continua==1? ' checked' : '';
                $especial = $d->Especial==1? ' checked' : '';

                $ds = array($d->clave , $d->descripcion, 
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->clave.'\',\'acumulativa\')" id="'.$d->clave.'_a" '.$acumulativa.'></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->clave.'\',\'continua\')" id="'.$d->clave.'_c" '.$continua.'></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->clave.'\',\'especial\')" id="'.$d->clave.'_e" '.$especial.'></div>');
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function updateUpps(Request $request){
        try {
            $dataSet = array();
            $array_data_act = [];
            $data_old_act = [];

            $tipo_actividad = TipoActividadUpp::where('clv_upp',$request->id)->firstOrFail();

            if(!empty($tipo_actividad)){

                $data_old_act = array(
                    'id' => $tipo_actividad->id,
                    'clv_upp' => $tipo_actividad->clv_upp,
                    'Continua' => $tipo_actividad->continua,
                    'Acumulativa' => $tipo_actividad->acumulativa,
                    'Especial' => $tipo_actividad->especial,
                    'created_at' => $tipo_actividad->usuario_creacion,
                    'updated_user' => $tipo_actividad->usuario_modificacion,
                    'created_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->created_at)),
                    'updated_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->updated_at)),
                );

                if($request->value=='true') $request->value = 1;
                else $request->value = 0;

                switch($request->field){
                    case "continua":
                        $tipo_actividad->Continua = $request->value;
                        break;
                    case "acumulativa":
                        $tipo_actividad->Acumulativa = $request->value;
                        break;
                    case "especial":
                        $tipo_actividad->Especial = $request->value;
                        break;
                    default:
                        break;
                }
                

            }

            
            $tipo_actividad->updated_user = Auth::user()->username;
            $tipo_actividad->save();
           


            $data_new_act = array(
                'id' => $tipo_actividad->id,
                'clv_upp' => $tipo_actividad->descripcion,
                'Continua' => $tipo_actividad,
                'Acumulativa' => $tipo_actividad,
                'Especial' => $tipo_actividad->estatus,
                'created_at' => $tipo_actividad->created_at,
                'updated_user' => $tipo_actividad->updated_user,
                'created_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->updated_at)),
            );
           
            $array_data_act = array(
                'tabla'=>'tipo_actividad_upp',
                'anterior'=>$data_old_act,
                'nuevo'=>$data_new_act
            );

            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"tipo_actividad_upp", "Edicion",json_encode($array_data_act));
            
            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
}
