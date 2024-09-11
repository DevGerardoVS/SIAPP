<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use File;
class LogController extends Controller
{
    public function logsView(){
        Controller::check_permission('getLogs');
        $auxRoute = 'Logs.logs';

        return view($auxRoute);
    }
    public function getLogs(){
        Controller::check_permission('getLogs');

        try{//-----------------------------
            $logFolder = \File::files(storage_path("logs/"));     

            $logList = array();

            foreach($logFolder as $path) { 
                $file = pathinfo($path);
                $a = array("filename"=>$file['filename'],"fecha"=>substr($file['filename'], 8, 10));
                array_push($logList, $a);
            }
            rsort($logList);
            return response()->json($logList, 200);

        } catch (\Exception $e) {
            return back()->withErrors(['msg'=>$e]);
        }
    }

    public function downloadLogs($file){
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
    public function clearLog(Request $request){
        $name = $request->selected;
       /* Local
        $file=storage_path("logs\\". $name.".log"); */
        /* prod qa */
        $file=storage_path("logs/". $name.".log");
            $fp = fopen($file,'r');
            // place the pointer at END - 125000
            fseek($fp,-125000,SEEK_END);
            // read data from (END - 125000) to END
            $data = fgets($fp,125000);
            // close the file handle
            fclose($fp);

            // overwrite the file content with data
            file_put_contents($file,$data);
       
    }
    public function DeleteLog(Request $request){
        $name = $request->selected;
       /* Local
        $file=storage_path("logs\\". $name.".log"); */
        /* prod qa */
        $file=storage_path("logs/". $name.".log");
        if (File::exists($file)) {
            File::delete($file);
        }
       
    }
}
