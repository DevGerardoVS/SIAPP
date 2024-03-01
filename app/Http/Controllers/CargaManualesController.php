<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Manual;
use App\Helpers\ManualHelper;
use DateTime;
use Auth;
use DB;


class CargaManualesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Función para mostrar la vista
     */
    public function index(){
        return view('manuales.index');
    }

    public function getManuales(){
        try{
            
            $data = DB::connection('mysql')->table('manuales')->where('estatus',1)->get();

            $dataSet = array();
            foreach ($data as $d) {
                //$button = '<button class="btn btn-sm" title="Descargar archivo" data-toggle="modal" onclick="descargar('.strval($d->id).')"><i class="fas fa-file" style="color: blue;"></i></button>';
                $button = '<a class="btn btn-sm btn_file_confirm" href="'.route('download_manual',['action'=>'download_file_confirm','id'=>$d->id]).'" title="'.__('messages.descarga_archivo_confirm').'"><i class="fa fa-download" style="color: #267E15;" ></i></a> / ';
                $button = $button. '<button class="btn btn-sm" title="Editar archivo" data-toggle="modal" onclick="getManual('.strval($d->id).')" data-target="#modalNuevoM"><i class="fa fa-pencil" style="color: #267E15;"></i></button>';
                if($d->estatus == 1){
                    $button = $button.'/<button data-toggle="modal" onclick="deleteManual('.strval($d->id).')" data-placement="top" title="Eliminar archivo" class="btn btn-sm" data-toggle="modal" data-target="#modal_delete"><i class="fa fa-trash" style="color: #ad0b00;" ></i></button>';
                }

                $ds = array($d->nombre, substr($d->ruta, 16), $button);
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Manuales_de_usuario"
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public function getEnlacesMenu(){
        try{
            $data = DB::connection('mysql')->table('manuales')->where('estatus',1)->get();

            $dataSet = array();
            foreach ($data as $d) {
                $users = json_decode($d->usuarios);
                
                for ($i=0; $i < count($users); $i++) { 
                    //Log::channel('daily')->debug('value '.$users[$i]->value);
                    if(Auth::user()->perfil_id==$users[$i]->id && $users[$i]->value==true){
                        $button = '<a class="dropdown-item" href="'.route('download_manual',['action'=>'download_file_confirm','id'=>$d->id]).'">'.'Descargar '.$d->nombre.'</a>';
                        $ds = array($button);
                        $dataSet[] = $ds;
                        break;
                    }
                }

                
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Manuales_de_usuario"
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public function getManual(Request $request){
        $roles = DB::connection('mysql')->table('adm_grupos')->select('id','nombre_grupo')->get();
        $data = DB::connection('mysql')->table('manuales')->where('id',$request->id)->first();

        return response()->json([
            "manual" => $data,
            "roles" => $roles,
            "catalogo" => "Manuales_de_usuario"
        ]);
    }

    public function getUsers(){
        try{
            
            $roles = DB::connection('mysql')->table('adm_grupos')->select('id','nombre_grupo')->get();
            return response()->json([
                "roles" => $roles,
                "catalogo" => "Manuales_de_usuario"
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public function saveManual(Request $request){
            //$users = json_decode($request->users);
            //Log::channel('daily')->debug('users ' . $users[0]->id);
            //funcion de agregar
        if($request->id_act==null){
            //validaciones
            if($request->name=="" || $request->name==null){
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, tiene que capturar un nombre.',
                );
                return response()->json($returnData, 500);
            }
            if($request->archivo=='undefined'){
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, tiene que seleccionar un archivo.',
                );
                return response()->json($returnData, 500);
            }

            $nameOriginal = substr(str_replace(" ", "_", $request->archivo->getClientOriginalName()), 0, -4);

            if (strlen($nameOriginal) > 50) {
                $nameOriginal = substr($nameOriginal, 0, 50);
            }

            $fileExt = $request->archivo->getClientOriginalExtension();
            /*$fecha = new DateTime();
            $fecha = strval($fecha->getTimestamp());*/
            //$nameSave = $nameOriginal . "_" . $dia . "_" . $mes . "_" . $anio . "_" . $fecha . "." . $fileExt;
            $nameSave = $nameOriginal . "." . $fileExt;
            //almacenamos el archivo
            $archivo = $request->archivo->storeAs('public/archivos', $nameSave);
            
            $exist = Storage::disk('s3')->has($archivo);
            
            if ($exist == 1 && $exist != null && $exist != '') {
                
                try {
                    DB::beginTransaction();
                    $id="";
                    ManualHelper::saveFile($request,$archivo);

                    DB::commit();
                } catch (\Exception$exp) {
                    DB::rollBack();
                    Storage::delete($archivo);
                    
                    Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => 'Hubo un error, no se pudo capturar su respuesta.',
                    );
                    return response()->json($returnData, 500);
                }

                $returnData = array(
                    'status' => 'success',
                    'title' => 'Éxito',
                    'message' => 'Manual guardado con éxito',
                );
                return response()->json($returnData);
            } else {
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error al guardar',
                    'message' => 'Error al guardar el archivo, intente nuevamente.',
                );
                return response()->json($returnData);
            }

        }else{
            $manual = DB::connection('mysql')->table('manuales')->where('id',$request->id_act)->first();
            
            $archivo = $manual->ruta;
            if ($request->archivo != 'undefined'){

                $exist = Storage::disk('s3')->has($archivo);
                if ($exist == 1 && $exist != null && $exist != '') Storage::delete($archivo);
                
                
                $nameOriginal = substr(str_replace(" ", "_", $request->archivo->getClientOriginalName()), 0, -4);

                if (strlen($nameOriginal) > 50) {
                    $nameOriginal = substr($nameOriginal, 0, 50);
                }
    
                $fileExt = $request->archivo->getClientOriginalExtension();

                $nameSave = $nameOriginal . "." . $fileExt;
                //almacenamos el archivo
                $archivo = $request->archivo->storeAs('public/archivos', $nameSave);
                $exist = Storage::disk('s3')->has($archivo);
            
                if ($exist == 1 && $exist != null && $exist != '') {}
                else {
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => 'Hubo un error, no se pudo almacenar el archivo.',
                    );
                    return response()->json($returnData, 500);
                }
            }

            try {
                DB::beginTransaction();

                $id="";
                ManualHelper::saveFile($request,$archivo);

                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Storage::delete($archivo);
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo capturar su respuesta.',
                );
                return response()->json($returnData, 500);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Manual actualizado con éxito',
            );
            return response()->json($returnData, 200);
        }

    }

    public function deleteManual(Request $request){
        $data = DB::connection('mysql')->table('manuales')->where('id',$request->id)->first();
        
        $exist = Storage::disk('s3')->has($data->ruta);

        if ($exist == 1 && $exist != null && $exist != '') {
            try {
                DB::beginTransaction();
                $id="";
                ManualHelper::deleteManual($request);
                Storage::delete($data->ruta);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo borrar el archivo.',
                );
                return response()->json($returnData, 500);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Manual eliminado con éxito',
            );
            return response()->json($returnData);
        }else{
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Hubo un error, el archivo no existe.',
            );
            return response()->json($returnData, 500);
        }
        
    }

    public function getDownload(Request $request)
    {
        $data = DB::connection('mysql')->table('manuales')->where('id',$request->id)->first();
        //PDF file is stored under project/public/download/info.pdf   
        $exist = Storage::disk('s3')->has($data->ruta);
        if($exist == 1 && $exist != null && $exist != ''){
            $headers = array(
                'Content-Type: application/pdf',
            );
            Log::channel('daily')->debug('archivo ' . $data->nombre);
            return Storage::download($data->ruta,$data->nombre.'.pdf',$headers);
        }
    }
}
