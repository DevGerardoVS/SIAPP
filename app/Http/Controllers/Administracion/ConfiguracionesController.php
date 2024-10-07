<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\administracion\TipoActividadUpp;
use App\Models\administracion\UppAutorizadascpNomina;
use App\Models\calendarizacion\TechosFinancieros;
use App\Helpers\BitacoraHelper;
use App\Exports\ArchivosCarga\AreasFuncionales;
use App\Exports\ArchivosCarga\ArchivosCarga;

class ConfiguracionesController extends Controller
{
    //
    public function getIndex()
	{
        Controller::check_permission('viewPostUpps', false);
		return view('administracion.configuraciones.index');
	}

    public function GetUpps(Request $request){
        try {
            $validated = $request->validate([
                'ejercicio' => 'required|integer|digits:4|min:2022',
            ]);
            
            $dataSet = array();

            $dataSet = DB::table('catalogo')
                ->select('clave', 'descripcion')
                ->where('grupo_id', 6)
                ->where('ejercicio','=',$request->ejercicio)
                ->orderByRaw('clave')
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

    public function GetEjercicios(){
        try {

            $ejercicios = DB::table('catalogo')
                ->select('ejercicio')
                ->where('grupo_id', 6)
                ->groupBy('ejercicio')
                ->orderByRaw('ejercicio DESC')
                ->get();

            return response()->json([
                "ejercicios"=>$ejercicios,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public function GetUppsAuto(Request $request){
        try {
            $validated = $request->validate([
                'ejercicio' => 'required|integer|digits:4|min:2022',
            ]);

            $dataSet = array();
            //select epp.upp_id, catalogo.descripcion from epp inner join catalogo on catalogo.clave=epp.upp_id where catalogo.grupo_id=6 group by upp_id order by upp_id;
            $dataSet = DB::table('epp')
                ->select('catalogo.clave', 'catalogo.descripcion')
                ->join('catalogo', 'catalogo.id','=','epp.upp_id')
                ->where('grupo_id', 6)
                ->where('catalogo.ejercicio','=',$request->ejercicio)
                ->groupBy('upp_id')
                ->orderBy('catalogo.clave')
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
            $ejercicio = $request->ejercicio;

            if($filter!=null || $filter!='') array_push($array_where, ['clave','=',$filter]);
            if($ejercicio!=null || $ejercicio!='') array_push($array_where, ['ejercicio','=',$ejercicio]);
            else array_push($array_where, ['ejercicio','=',date('Y')]);

            $data = DB::table('tipo_actividad_upp')
                ->select('catalogo.id','clave', 'descripcion','Acumulativa','Continua','Especial')
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

                if(Auth::user()->id_grupo==1){
                    $ds = array($d->clave , $d->descripcion, 
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'acumulativa\')" id="'.$d->id.'_a" '.$acumulativa.'></div>',
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'continua\')" id="'.$d->id.'_c" '.$continua.'></div>',
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'especial\')" id="'.$d->id.'_e" '.$especial.'></div>');
                    $dataSet[] = $ds;
                }else{
                    $ds = array($d->clave , $d->descripcion, 
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'acumulativa\')" id="'.$d->id.'_a" '.$acumulativa.' disabled></div>',
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'continua\')" id="'.$d->id.'_c" '.$continua.' disabled></div>',
                    '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData(\''.$d->id.'\',\'especial\')" id="'.$d->id.'_e" '.$especial.' disabled></div>');
                    $dataSet[] = $ds;
                }
                
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

    public static function GetAutorizadas(Request $request){
        try {
            $dataSet = array();
            $array_where = [];
            $filter = $request->filter;
            $ejercicio = $request->ejercicio;

            if($ejercicio!=null || $ejercicio!='') array_push($array_where, ['catalogo.ejercicio','=',$ejercicio]);
            else array_push($array_where, ['catalogo.ejercicio','=',date('Y')]);

            //if($filter!=NULL && $filter!='' && $filter != 'undefined') array_push($array_where, ['upp_id','=',$filter]);

            $sub = DB::table('uppautorizadascpnomina')
                ->select('clv_upp');

            $complemento = DB::table('epp')
                ->select('catalogo.clave','catalogo.descripcion',DB::raw('0 as autorizado'))
                ->join('catalogo','catalogo.id','=','epp.upp_id')
                ->whereNotIn("clave", $sub)
                ->groupByRaw('upp_id');

            $data = DB::table('epp')
                ->select('catalogo.clave','catalogo.descripcion',DB::raw('if(uppautorizadascpnomina.deleted_at is null,1,0) as autorizado'))
                ->join('catalogo','catalogo.id','=','epp.upp_id')
                ->join('uppautorizadascpnomina','uppautorizadascpnomina.clv_upp','=','catalogo.clave')
                ->where('grupo_id', 6)
                ->where($array_where)
                ->groupBy('upp_id')
                ->union($complemento)
                ->orderBy('clave')
                ->get();

            $disabled = Auth::user()->id_grupo==1 ? '' : 'disabled';

            foreach ($data as $d) {
                //$d->tipo 
                $autorizado = $d->autorizado==1? ' checked' : '';

                $ds = array($d->clave , $d->descripcion, '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateAutoUpps(\''.$d->clave.'\')" id="'.$d->clave.'"'.$autorizado.' '.$disabled.' ></div>');

                if($filter==NULL || $filter=='' || $filter == 'undefined'){
                    $dataSet[] = $ds;
                }else if($filter == $d->clave){
                    $dataSet[] = $ds;
                    break;
                }
                
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

    public static function updateAutoUpps(Request $request){
        Controller::check_permission('updateUppsAuto');
        try {
            $dataSet = array();
            $array_data_act = [];
            $data_old_act = [];

            DB::beginTransaction();

            $upp_autorizada = UppAutorizadascpNomina::where('clv_upp',$request->id)->first();

            if(!empty($upp_autorizada)){

                $data_old_act = array(
                    'id' => $upp_autorizada->id,
                    'clv_upp' => $upp_autorizada->clv_upp,
                    'deleted_at'=> date("Y/m/d H:i:s", strtotime($upp_autorizada->deleted_at)),
                    'deleted_user'=> $upp_autorizada->deleted_user,
                    'created_at' => $upp_autorizada->usuario_creacion,
                    'updated_user' => $upp_autorizada->usuario_modificacion,
                    'created_at'=>date("d/m/Y H:i:s", strtotime($upp_autorizada->created_at)),
                    'updated_at'=>date("d/m/Y H:i:s", strtotime($upp_autorizada->updated_at)),
                );

                //Log::channel('daily')->debug('exp '.date("Y/m/d H:i:s"));

                if($request->value=='false'){
                    $upp_autorizada->deleted_at = date("Y-m-d H:i:s");
                } 
                else{
                    $upp_autorizada->deleted_at = NULL;
                } 
                //Log::channel('daily')->debug('upp '.$upp_autorizada->deleted_at);
                $upp_autorizada->updated_user = Auth::user()->username;
                $upp_autorizada->updated_at = date("Y-m-d H:i:s");
                $upp_autorizada->deleted_user = Auth::user()->username;
                
                $upp_autorizada->save();

            }else{
                $upp_autorizada = new UppAutorizadascpNomina();
                $upp_autorizada->clv_upp=$request->id;
                $upp_autorizada->created_user = Auth::user()->username;
                $upp_autorizada->created_at = date("Y-m-d H:i:s");
                $upp_autorizada->save();
                //Log::channel('daily')->debug('log ');
            }

            $data_new_act = array(
                'id' => $upp_autorizada->id,
                'clv_upp' => $upp_autorizada->clv_upp,
                'deleted_at'=> date("d/m/Y H:i:s", strtotime($upp_autorizada->deleted_at)),
                'deleted_user'=> $upp_autorizada->deleted_user,
                'created_at' => $upp_autorizada->usuario_creacion,
                'updated_user' => $upp_autorizada->usuario_modificacion,
                'created_at'=>date("d/m/Y H:i:s", strtotime($upp_autorizada->created_at)),
                'updated_at'=>date("d/m/Y H:i:s", strtotime($upp_autorizada->updated_at)),
            );
           
            $array_data_act = array(
                'tabla'=>'tipo_actividad_upp',
                'anterior'=>$data_old_act,
                'nuevo'=>$data_new_act
            );

            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"uppautorizadascpnomina", "Edicion",json_encode($array_data_act));
            
            //validar en techos financieros si tiene recursos de RH
            $array_data_act = [];
            $data_old_act = [];
            
            if($request->value=='false'){
                $techo = TechosFinancieros::where('clv_upp',$request->id)->where('tipo','RH')->where('ejercicio',date('Y'))->first();

                if(!empty($techo)){
                    $ds = "El presupuesto para RH se eliminará, se debe agregar este presupuesto a Operativo";
                    $dataSet[] = $ds;
                    
                    $data_old_act = array(
                        'id' => $techo->id,
                        'clv_upp' => $techo->clv_upp,
                        'clv_fondo' => $techo->clv_fondo,
                        'presupuesto' => $techo->presupuesto,
                        'tipo' => $techo->tipo,
                        'deleted_at'=> date("Y/m/d H:i:s", strtotime($techo->deleted_at)),
                        'deleted_user'=> $techo->deleted_user,
                        'created_at' => $techo->usuario_creacion,
                        'updated_user' => $techo->usuario_modificacion,
                        'created_at'=>date("d/m/Y H:i:s", strtotime($techo->created_at)),
                        'updated_at'=>date("d/m/Y H:i:s", strtotime($techo->updated_at)),
                    );

                    $techo->deleted_at = date("Y-m-d H:i:s");
                    $techo->deleted_user =  Auth::user()->username;
                    $techo->save();

                    $data_new_act = array(
                        'id' => $techo->id,
                        'clv_upp' => $techo->clv_upp,
                        'clv_fondo' => $techo->clv_fondo,
                        'presupuesto' => $techo->presupuesto,
                        'tipo' => $techo->tipo,
                        'deleted_at'=> date("Y/m/d H:i:s", strtotime($techo->deleted_at)),
                        'deleted_user'=> $techo->deleted_user,
                        'created_at' => $techo->usuario_creacion,
                        'updated_user' => $techo->usuario_modificacion,
                        'created_at'=>date("d/m/Y H:i:s", strtotime($techo->created_at)),
                        'updated_at'=>date("d/m/Y H:i:s", strtotime($techo->updated_at)),
                    );

                    $array_data_act = array(
                        'tabla'=>'techos_financieros',
                        'anterior'=>$data_old_act,
                        'nuevo'=>$data_new_act
                    );

                    BitacoraHelper::saveBitacora(BitacoraHelper::getIp(),"uppautorizadascpnomina", "Edicion",json_encode($array_data_act));
                }
            } 

            DB::commit();

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
        Controller::check_permission('updateUpps');
        try {
            $response = "";
            $array_data_act = [];
            $data_old_act = [];

            $tipo_actividad = TipoActividadUpp::where('clv_upp',$request->id)->firstOrFail();

            if(!empty($tipo_actividad)){

                $data_old_act = array(
                    'id' => $tipo_actividad->id,
                    'clv_upp' => $tipo_actividad->clv_upp,
                    'Continua' => $tipo_actividad->Continua,
                    'Acumulativa' => $tipo_actividad->Acumulativa,
                    'Especial' => $tipo_actividad->Especial,
                    'created_at' => $tipo_actividad->usuario_creacion,
                    'updated_user' => $tipo_actividad->usuario_modificacion,
                    'created_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->created_at)),
                    'updated_at'=>date("d/m/Y H:i:s", strtotime($tipo_actividad->updated_at)),
                );

                if($request->value=='true') $request->value = 1;
                else $request->value = 0;

                switch($request->field){
                    case "continua":
                        if($tipo_actividad->Acumulativa == 0 && $tipo_actividad->Especial == 0 && $request->value == 0){
                            $response = "error";

                            return response()->json([
                                "response" => "error",
                                "dataSet" => $tipo_actividad,
                                "catalogo" => "Configuraciones",
                            ]);
                        }

                        $tipo_actividad->Continua = $request->value;
                        break;
                    case "acumulativa":
                        if($tipo_actividad->Continua == 0 && $tipo_actividad->Especial == 0 && $request->value == 0){
                            
                            return response()->json([
                                "response" => "error",
                                "dataSet" => $tipo_actividad,
                                "catalogo" => "Configuraciones",
                            ]);
                        }
                        $tipo_actividad->Acumulativa = $request->value;
                        break;
                    case "especial":
                        if($tipo_actividad->Acumulativa == 0 && $tipo_actividad->Continua == 0 && $request->value == 0){
                            
                            return response()->json([
                                "response" => "error",
                                "dataSet" => $tipo_actividad,
                                "catalogo" => "Configuraciones",
                            ]);
                        }
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
                'clv_upp' => $tipo_actividad->clv_upp,
                'Continua' => $tipo_actividad->Continua,
                'Acumulativa' => $tipo_actividad->Acumulativa,
                'Especial' => $tipo_actividad->Especial,
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
                "dataSet" => $response,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
    public function getArchivosDeCarga2024($id,$ejercicio){
        ob_end_clean();
        ob_start();
        $nombreArchivo = '';
        switch ($id) {
            case 1:
                $nombreArchivo = 'Áreas funcionales '.$ejercicio;
            break;
            case 2:
                $nombreArchivo = 'Fondos '.$ejercicio;
            break;
            case 3:
                $nombreArchivo = 'CeCo-Be y CeGe-Descripcion '.$ejercicio;
            break;
            case 4:
                $nombreArchivo = 'Centro gestor '.$ejercicio;          
            break;
            case 5:
                $nombreArchivo = 'Pospre '.$ejercicio;        
            break;
            case 6:
                $nombreArchivo = 'LAYOUT PRESUPUESTO '.$ejercicio;                
            break;
            default:
                
            break;
        }
        $b = array(
            'username'=>Auth::user()->username,
            'accion'=>'Descarga de archivo '.$nombreArchivo,
            'modulo'=>'configuracion'
        );
        Controller::bitacora($b);
        return Excel::download(new ArchivosCarga($id,$ejercicio), $nombreArchivo.'.xlsx',\Maatwebsite\Excel\Excel::XLSX);
    }
}
