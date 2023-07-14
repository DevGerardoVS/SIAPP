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
use App\Models\cierreEjercicio;
use Carbon\Carbon;
use App\Exports\PlantillaExport;
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
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();


   return Excel::download(new PlantillaExport, 'Plantilla.xlsx');
 

}

    
    
     //Obtener datos del excel
     public function loadDataPlantilla(Request $request)	{
        $request->tipo ? $tipoAdm=$request->tipo: $tipoAdm=NULL;
        $uppUsuario = auth::user()->clv_upp;
        $message=[
            'file'=> 'El archivo debe ser tipo xlsx' 
          ];
   
          $request->validate([
             'file'=> 'required|mimes:xlsx'
          ], $message );

        ini_set('max_execution_time', 1200);

        //verificar si tiene un registro antes 0 es guardado 1 confirmado
        $file=$request->file->storeAs(
            'plantillas', Auth::user()->username.'.xlsx'
            );
           $filename='\/app\/plantillas/'.Auth::user()->username.'.xlsx';
        //verificar que el usuario tenga permiso
        try {
            //Validaciones para administrador
        if($tipoAdm!=NULL ){

           $arrayupps= array();
           $arraypresupuesto= array();
           $errores=0;
           $countO=0;
           $CountR=0;
          if ( $xlsx = SimpleXLSX::parse(storage_path($filename)) ) {
            $filearray =$xlsx->rows();
             if(count($filearray)<=1){
                return redirect()->back()->withErrors(['error' => 'El excel esta vacio']);
            }
            array_shift($filearray);
            $ejercicio=date("Y");
            foreach($filearray as $k){

                //buscar en el array de upps 
                $var= array_search($k['5'], $arrayupps);
                
               if($k['16']=='UUU'){
                $CountR++;
    
               }
               else{
                $countO++;
    
               }
    
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
              $var ==false ? array_push($arrayupps,$k['5']) :  NULL ; 
    
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
                 $VerifyEjercicio = cierreEjercicio::select()->where('clv_upp', $arraysplit[0])->where('estatus','Abierto')->where('ejercicio',$ejercicio+1)->count();
                 $valuepresupuesto = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('tipo',$tipoFondo)->where('clv_fondo', $arraysplit[2])->value('presupuesto');
                 if($valuepresupuesto=!$value){
                    return redirect()->back()->withErrors(['error' => 'El total presupuestado  no es igual al techo financiero']);
                }

                if($VerifyEjercicio<0){
                    return redirect()->back()->withErrors(['error' => 'El año del ejercicio  seleccionado no es valido']);

                } 

            }

            switch($tipoAdm){
                case 1:
                    if($CountR>0){
                     return redirect()->back()->withErrors(['error' => 'Hay claves de RH en el archivo de cargas masivas']);

                    }
                     //validacion para eliminar registros no confirmados 
                    foreach($arrayupps as $u){
                     $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                     if($valupp>0){
                      $deleted= ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario','!=','UUU')->where('estado', 0)->forceDelete();
                     }
                    }
    
                    break;
    
                case 2:
                    if($CountO>0){
                        return redirect()->back()->withErrors(['error' => 'Hay claves Operativas en el archivo de cargas masivas']);

                       }
                    //validacion para eliminar registros no confirmados 
                    foreach($arrayupps as $key=>$u){
                     $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                     if($valupp>0){
                      $deleted= ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario','UUU')->where('estado', 0)->forceDelete();
                     }
                    }
    
                    break;
                
                case 3:
                     //validacion para eliminar registros no confirmados 
                     foreach($arrayupps as $u){
                
                     $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
           
                      if($valupp>0){
                       $deleted= ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->forceDelete();
                      }
                    }
                    break;
            }
            
    
         }     
        }
        //Validaciones para usuarios upps 
        else{
                $tipousuario=auth::user()->id_grupo;

                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp',$uppUsuario)->count();
                // Checar permiso
                if(Controller::check_assignFront(1)){
                }
                else{
                    return redirect()->back()->withErrors(['error' => 'No tiene permiso para subir carga masiva']);


                }
                


           $arrayupps= array();
           $arraypresupuesto= array();
           $errores=0;
           $OperativoCount=0;
           $CountR=0;
           $ObraCount=0;
           $DiferenteUpp=0;
          if ( $xlsx = SimpleXLSX::parse(storage_path($filename)) ) {
            $filearray =$xlsx->rows();
            if(count($filearray)<=1){
                return redirect()->back()->withErrors(['error' => 'El excel esta vacio']);
            }
            array_shift($filearray);
            $ejercicio=date("Y");
            foreach($filearray as $k){
                //buscar en el array de upps 

                $var= array_search($k['5'], $arrayupps);
                
               if($k['16']=='UUU'){
                $CountR++;
    
               }
               else{
                $OperativoCount++;
    
               }
                if($k['5']!=$uppUsuario){
                    $DiferenteUpp++;  
                }
                if($k['26']!='000000'){
                    
                   $ObraCount++; 
                }
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
              $var ==false ? array_push($arrayupps,$k['5']) :  NULL ; 
    
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

                 $VerifyEjercicio = cierreEjercicio::select()->where('clv_upp', $arraysplit[0])->where('estatus','Abierto')->where('ejercicio',$ejercicio+1)->count();
                 $valuepresupuesto= TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('tipo',$tipoFondo)->where('ejercicio',$ejercicio+1)->where('clv_fondo', $arraysplit[2])->value('presupuesto');
                 if($valuepresupuesto=!$value ){
                return redirect()->back()->withErrors(['error' => 'El total presupuestado en las upp no es igual al techo financiero']);
                }
                if($VerifyEjercicio<0){
                    return redirect()->back()->withErrors(['error' => 'El año del ejercicio  seleccionado no es valido']);

                }
    
            }

            switch($tipousuario){
                case 4:

                    if($DiferenteUpp>0){
                        return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar de  otras upps']);
                    }
                    if($ObraCount>0 ){
                        if(Controller::check_assignFront(3)){
                        
                        }
                        else{
                            return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar obras']);

                        }


                    }
                    switch($uppsautorizadas){
                    case 0: 
                     //validacion para eliminar registros no confirmados 
                     foreach($arrayupps as $u){
                     $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                      if($valupp>0){
                       $deleted= ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->forceDelete();
                      }
                    }
                     break;

                     case 1:
                        if($CountR>0){
                            return redirect()->back()->withErrors(['error' => 'Hay claves de RH en el archivo de cargas masivas']);
                           }
                           if($DiferenteUpp>0){
                            return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar de  otras upps']);

                        } 
                           //validacion para eliminar registros no confirmados 
                           foreach($arrayupps as $u){
                           $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                           if($valupp>0){
                            $deleted= ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario','!=','UUU')->where('estado', 0)->forceDelete();
                            }
                           }
                        break;
                    }

    
                   break;
    
                case 5:
                    if($CountO>0){
                        return redirect()->back()->withErrors(['error' => 'Hay claves Operativas en el archivo de cargas masivas']);

                       }
                    //validacion para eliminar registros no confirmados 
                    foreach($arrayupps as $key=>$u){
                     $valupp= ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();
                     if($valupp>0){
                      $deleted= ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario','UUU')->where('estado', 0)->forceDelete();
                     }
                    }
                    break;
                

            }
            
    
         }  
            }      
          } catch (\Throwable $th) {
            Log::debug($th);
            return redirect()->back()->withErrors(['error' => 'La información introducida es invalida']);

        }
        //si todo sale bien procedemos al import
        try {
            (new ClavePresupuestaria)->import($request->file, 'local', \Maatwebsite\Excel\Excel::XLSX);
    
              if(File::exists(storage_path($filename))){
                File::delete(storage_path($filename));
                
            }
            return redirect()->back()->withSuccess('Se cargaron correctamente los datos');
             } 
         catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
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

        
            
       }


       

}