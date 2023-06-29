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
use App\Exports\ImportErrorsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\catalogos\CatEntes;
use App\Models\uppautorizadascpnomina;

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

        //verificar que el usuario tenga permiso
        $usuario=auth::user()->id_ente;//si es vacio es administrador
        if($usuario==NULL){
            $autorizado=1;
        }else{
            $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();

            $autorizado = uppautorizadascpnomina::where($uppUsuario->cve_upp)->count();
        }

        if($autorizado>0 ){
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
         //wea para identificar usuario

        if ( $xlsx = SimpleXLSX::parse(storage_path($filename)) ) {
            $filearray =$xlsx->rows();
            $error=0;
            array_shift($filearray);
            foreach($filearray as $k){
                //buscar en el array de upps 
                $var= array_search($k['5'], $arrayupps);
                


                //buscar en el array de totales 
               if(array_key_exists($k['5'].$k['16'].$k['24'], $arraypresupuesto) && $k['27']!=''){

                $arraypresupuesto[$k['5'].$k['16'].$k['24']] =  $arraypresupuesto[$k['5'].$k['16'].$k['24']]+$k['27']; 

               }else{
                if($k['27']!='' && $k['5'].$k['24'] !='' ){
                    $arraypresupuesto[$k['5'].$k['16'].$k['24']] = $k['27']; 
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
                    $deleted= ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->forceDelete();

                    }
            }

             //validacion de totales
             foreach($arraypresupuesto as $key=>$value){
              $arraysplit = str_split($key, 3);
              $tipoFondo='';
               if($arraysplit[1]=='UUU'){
                $tipoFondo='RH';
               }
               else{
                $tipoFondo='Operativo';
               }
                 $valuepresupuesto= TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('tipo','RH')->where('clv_fondo', $arraysplit[2])->value('presupuesto');
                 if($valupp=!$value){
                 $error++;
                }

            }
            if($error>0){
                return redirect()->back()->withErrors('error','El total presupuestado en las upp no es igual al techo financiero');

            }
        } else {
            Log::debug(SimpleXLSX::parseError());
        }    
          
    try {
            (new ClavePresupuestaria)->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX);

              if(File::exists(storage_path($filename))){
                File::delete(storage_path($filename));
                
            }
            return redirect()->back()->withSuccess('Se cargaron correctamente los datos');
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             if(File::exists(storage_path($filename))){
                File::delete(storage_path($filename));
            }
            foreach ($failures as $key=>$failure) {
                $valuesar=$failure->values();
                if(!$valuesar['total']){
                        unset($failures[$key]);
                } 
            } 
       
             return redirect()->back()->withErrors($failures);



            
            } 
        }else{
            return redirect()->back()->withErrors('error','No tiene permisos para hacer carga masiva');

        }
        
            
       }

       //pendiente no funciona
       public function DownloadErrors(Request $request)	{

              $response = Excel::download(new ImportErrorsExport($request->failures), 'Errores.xlsx', \Maatwebsite\Excel\Excel::XLSX);
             ob_end_clean();
         
             return $response;   
       }
       
}