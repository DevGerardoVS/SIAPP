<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
class LogController extends Controller
{
    public function logsView(){
        Controller::check_permission('getLogs');
        $auxRoute = 'Logs.logs';

        try{//-----------------------------
            $logFolder = \File::files(storage_path("logs/"));     

            $logList = array();

            foreach($logFolder as $path) { 
                $file = pathinfo($path);
                array_push($logList, $file);
            }
            
            // Log::debug($logList);
            
        
            return view($auxRoute)->with(["logs" => $logList]);
        } catch (\Exception $e) {
            $errLocation1 = 'LogController'; $errLocation2 = 'logsView';
                
            $logMSG = LogHelper::generarLog($errLocation1, $errLocation2, $e, '');

            if(str_contains($e, 'SQLSTATE')) $logMSG = 'Ocurrió un error en base de datos';
            if(str_contains($e, 'on null')) $logMSG = 'Asegúrese de ingresar todos los parametros';

            return back()->withErrors(['msg'=>$logMSG]);
        }
    }

    public function downloadLogs(Request $request){
        //Log::debug($request->selected);
        $file = $request->selected;

        try{//-----------------------------
            return response()->download(storage_path("logs/{$file}.log"));
        } catch (\Exception $e) {
            $errLocation1 = 'LogController'; $errLocation2 = 'downloadLogs';
                
            $logMSG = LogHelper::generarLog($errLocation1, $errLocation2, $e, '');

            if(str_contains($e, 'SQLSTATE')) $logMSG = 'Ocurrió un error en base de datos';
            if(str_contains($e, 'on null')) $logMSG = 'Asegúrese de ingresar todos los parametros';

            return back()->withErrors(['msg'=>$logMSG]);
        }
    }
}
