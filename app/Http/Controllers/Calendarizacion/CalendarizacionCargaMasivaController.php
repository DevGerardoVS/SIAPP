<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Http\Controllers\Controller;
use App\Imports\ClavePresupuestaria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\calendarizacion\clasificacion_geografica;
use App\Models\TechosFinancieros;
use Carbon\Carbon;

use Redirect;
use Datatables;
use App\Models\User;
use Auth;
use Shuchkin\SimpleXLSX;
use App\Models\ProgramacionPresupuesto;

use DB;
class CalendarizacionCargaMasivaController extends Controller
{
     //Obtener plantilla para descargar
	public function getExcel(Request $request)	{
     $file='plantilla.xlsx';
    return response()->download(storage_path("templates/{$file}"));
	}
    
     //Obtener datos del excel
     public function loadDataPlantilla(Request $request)	{
          ini_set('max_execution_time', 1200);
        
            $message=[
                'file'=> 'El archivo debe ser tipo xlsx' 
              ];
       
              $request->validate([
                 'file'=> 'required|mimes:xlsx'
              ], $message );
         
       //verificar si tiene un registro antes 0 es guardado 1 confirmado
        $file=$request->file->storeAs(
            'plantillas', Auth::user()->username.'.xlsx'
        );
        $filename='\/app\/plantillas/'.Auth::user()->username.'.xlsx';
         $arrayupps= array();
         $arraypresupuesto= array();
         $errores=0;
        if ( $xlsx = SimpleXLSX::parse(storage_path($filename)) ) {
            $filearray =$xlsx->rows();
            $error=0;
            array_shift($filearray);
            foreach($filearray as $k){
                //buscar en el array de upps 
                $var= array_search($k['5'], $arrayupps);
                


                //buscar en el array de totales 
               if(array_key_exists($k['5'].$k['24'], $arraypresupuesto) && $k['27']!=''){

                $arraypresupuesto[$k['5'].$k['24']] =  $arraypresupuesto[$k['5'].$k['24']]+$k['27']; 

               }else{
                if($k['27']!='' && $k['5'].$k['24'] !='' ){
                    $arraypresupuesto[$k['5'].$k['24']] = $k['27']; 
                }
               }

                //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                if($var=== 0){
                $var=true;
                }
              $var ==false ? array_push($arrayupps,$k['5']):  NULL ; 

            }

            
             //validacion para eliminar registros no confirmados 
            foreach($arrayupps as $u){
                $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                if($valupp>0){
                     $deleted = ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->delete();
                }
            }
             //validacion de totales
             foreach($arraypresupuesto as $key=>$value){
              $arraysplit = str_split($key, 3);
                 $valuepresupuesto= TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('clv_fondo', $arraysplit[1])->where('tipo','RH')->value('presupuesto');
                 if($valupp=!$value){
                 $error++;
                }

            }
            if($error>0){
                return redirect()->to('/calendarizacion/claves')->with('error','El total presupuestado en las upp no es igual al techo financiero');
            }
        } else {
            Log::debug(SimpleXLSX::parseError());
        }    
          
    try {
            (new ClavePresupuestaria)->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX);

              if(File::exists(storage_path($filename))){
                File::delete(storage_path($filename));
            }
          return redirect()->to('/calendarizacion/claves')->with('success','Se ha importado el excel ');
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             if(File::exists(storage_path($filename))){
                File::delete(storage_path($filename));
            }
             foreach ($failures as $failure) {
                 $failure->row(); // row that went wrong
                 $failure->attribute(); // either heading key (if using heading row concern) or column index
                 $failure->errors(); // Actual error messages from Laravel validator
                 $failure->values(); // The values of the row that has failed.
                 Log::debug($failure->row());
                 Log::debug($failure->attribute());
                 Log::debug($failure->errors());
                 Log::debug($failure->values());

             }
             $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'error de validacion',
              );
             return response()->json($returnData);
        } 

 
            
       }
/* 
       public function guardarCMAportacion(Request $request){
        ini_set('max_execution_time', 1200);

        $attributeNames = array(
            'cmFile' => 'plantilla'
        );

        $validator = Validator::make($request->all(), [
            'cmFile' => 'required|mimes:csv',
        ]);

        $validator->setAttributeNames($attributeNames);

        //Revisar que las comunidades tengan los factores para proceder
        $checkCoeficientes = DB::select("SELECT nombre FROM comunidads WHERE id NOT IN (SELECT idComunidad FROM coeficiente_municipals where idComunidad and habilitado = 1) and centralizada = 0");

        if($checkCoeficientes!=null){
            $comunidadesMensaje = "";

            foreach ($checkCoeficientes as $otro) {
                $comunidadesMensaje = $comunidadesMensaje.$otro->nombre.", ";
            }

            return back()->withInput()->withErrors("Asegurese de que todas las comunidades cuenten con sus factores correspondientes: " . $comunidadesMensaje."  con factores faltantes.");
        }

        if ($validator->fails()){   //check all validations are fine, if not then redirect and show error messages
            return back()->withInput()->withErrors($validator);
        }else{
            try{//-----------------------------
                $file = $request->file('cmFile');

                $filename = $file->getClientOriginalName();

                $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

                $tempPath = $file->getRealPath();

                $fileSize = $file->getSize(); //Get size of uploaded file in bytes

                //Where uploaded file will be stored on the server
                $location = 'cargaMasiva'; //Created an "uploads" folder for that
                // Upload file
                $file->move($location, $filename);
                // In case the uploaded file path is to be stored in the database
                $filepath = public_path($location . "/" . $filename);
                // Reading file
                $file = fopen($filepath, "r");
                $importData_arr = array();
                $i = 0;

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    //salta primera fila
                    if ($i == 0) {
                        $i++;
                        continue;
                    }

                    //salta segunda fila
                    if ($i == 1) {
                        $i++;
                        continue;
                    }

                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata[$c];
                    }

                    $i++;

                    $numeroLeidos = $i;
                }

                fclose($file);
                $j = 0;
                $auxRoute = "";
                $successMessage = "";

                //$idMunicipio,$idFondo,$anio,$montoFebrero,$montoPeriodo,$montoUltimoMes
                //$this->buscar_aportacion($importData[0], $importData[2], $importData[4], $importData[5], $importData[6], $importData[7]);

                $numeroRechazados = 0;

                foreach ($importData_arr as $importData) {
                    $anio = $importData[2];

                    if($anio<2000){
                        $anio = 2000;
                        $numeroRechazados = $numeroRechazados+1;
                    }else if($anio==""){
                        $anio = date("Y");
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $febFAISM = $importData[3];
                    if($febFAISM==""){
                        $febFAISM = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $marFAISM = $importData[4];
                    if($marFAISM==""){
                        $marFAISM = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $novFAISM = $importData[5];
                    if($novFAISM==""){
                        $novFAISM = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $febFORTAMUN = $importData[6];
                    if($febFORTAMUN==""){
                        $febFORTAMUN = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $marFORTAMUN = $importData[7];
                    if($marFORTAMUN==""){
                        $marFORTAMUN = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    $dicComFORTAMUN = $importData[8];
                    if($dicComFORTAMUN==""){
                        $dicComFORTAMUN = 0;
                        $numeroRechazados = $numeroRechazados+1;
                    }

                    if($importData[0]>=1 && $importData[0]<=113){
                        $this->buscar_aportacion($importData[0], 25, $anio, $febFAISM, $marFAISM, $novFAISM);//FAISM
                        $this->buscar_aportacion($importData[0], 26, $anio, $febFORTAMUN, $marFORTAMUN, $dicComFORTAMUN);//FORTAMUN
                    }else{
                        $numeroRechazados = $numeroRechazados+1;
                    }
                }

                $auxRoute = 'aportacion.index';
                $successMessage = "El archivo con las aportaciones de los Municipios ha sido cargado";

                $currentDate = Carbon::now();
                $currentYear = date("Y");

                //$numeroLeidos = '-';
                $cuerpoCorreo = "Con fecha del ".$currentDate." se procesó correctamente el archivo con nombre '".$filename."' correspondiente al ejercicio ".$currentYear." cargado con fecha ".$currentDate.". Registros leidos: ".$numeroLeidos."";
                $detallesAdministrador = 'Registros procesados: '.$numeroLeidos.'';
                $informacionAdministrador = 'Registros rechazados: '.$numeroRechazados;

                $adminUsers = User::select('email')->where('idRol', '1')->where('habilitado','1')->get();
                $totalAdm = (int)count($adminUsers);

                for($x=0; $x<$totalAdm; $x++){
                    $adminEmail = $adminUsers->get($x)->email;
                    $this->notificarAdministradores($cuerpoCorreo, $detallesAdministrador, $informacionAdministrador, $adminEmail, $filename);
                }

                if(File::exists($filepath)){
                    File::delete($filepath);
                }

                return redirect()->route($auxRoute)->withSuccess($successMessage);
            } catch (\Exception $e) {
                $errLocation1 = 'AportacioneController'; $errLocation2 = 'guardarCMAportacion';

                $logMSG = LogHelper::generarLog($errLocation1, $errLocation2, $e, '');

                if(str_contains($e, 'SQLSTATE')) $logMSG = 'Ocurrió un error en base de datos';
            if(str_contains($e, 'on null')) $logMSG = 'Asegúrese de ingresar todos los parametros';
                return back()->withErrors(['msg'=>$logMSG]);
            }
        }
        return "Subida archivo";
    }     */

}