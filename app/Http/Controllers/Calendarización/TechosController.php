<?php

namespace App\Http\Controllers\calendarizacion;

use App\Http\Controllers\Controller;
use App\Models\calendarizacion\TechosFinancieros;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use function Psy\debug;
use App\Exports\PlantillaTechosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Techos;

class TechosController extends Controller
{
    //Consulta Vista Techos
    public function getIndex()
    {
        return view('calendarizacion.techos.index');
    }

    public function getTechos(){
        $dataSet = [];

        $data = DB::table('techos_financieros as tf')
            ->select('tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp from v_entidad_ejecutora','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->get()
        ;

        foreach ($data as $d){
            $button2 = '<a class="btn btn-secondary" onclick="" data-toggle="modal" data-target="#createGroup" data-backdrop="static" data-keyboard="false"><i class="fa fa-pencil" style="font-size: large; color: white"></i></a>';
            $button3 = '<button onclick="" title="Eliminar grupo" class="btn btn-danger"><i class="fa fa-trash" style="font-size: large"></i></button>';

            array_push($dataSet,[$d->clv_upp, $d->descPre, $d->tipo,$d->clv_fondo,$d->fondo_ramo,'$'.number_format($d->presupuesto),$d->ejercicio,'pendiente',$button2.' '.$button3]);
        }

        return [
            'dataSet'=>$dataSet,
            'data' => json_encode($data)
        ];
    }

    public function getFondos(){
        $fondos = DB::table('fondo')
            ->select('clv_fondo_ramo','fondo_ramo')
            ->distinct()
            ->get();

        return json_encode($fondos);
    }

    public function addTecho(Request $request){
        $data = array_chunk(array_slice($request->all(),3),3);
        $aKeys = array_keys(array_slice($request->all(),3));
        $validaForm = [];

        foreach ($aKeys as $a){
            $validaForm[$a] = 'required';
        }

        $upp = $request->uppSelected;
        $ejercicio = $request->anio;

        $request->validate($validaForm);

        if(count($data) != 0){
            try {
                DB::beginTransaction();
                foreach ($data as $d){
                    DB::table('techos_financieros')->insert([
                        'clv_upp' => $upp,
                        'clv_fondo' => $d[1],
                        'tipo' => $d[0],
                        'presupuesto' => $d[2],
                        'ejercicio' => $ejercicio,
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_user' => Auth::user()->username,
                        'created_user' => Auth::user()->username
                    ]);
                }
                DB::commit();
                return [
                    'status' => 200
                ];
            }catch (Throwable $e){
                DB::rollBack();
                report($e);
                return [
                    'status' => 400,
                    'error' => $e
                ];
            }
        }else{
            return [
                'status' => 400
            ];
        }
    }

    public function exportView(){
        $upps = DB::table('v_entidad_ejecutora')->select('clv_upp','upp')->distinct()->get();
        return view('calendarizacion.techos.plantillaCargaTechos',[
            "upps" => $upps
        ]);
    }

    public function exportPlantilla(){
        //Si no coloco estas lineas Falla/
        ob_end_clean();
        ob_start();
        //Si no coloco estas lineas Falla/
        return Excel::download(new PlantillaTechosExport(), 'Plantilla Techos Financieros.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function importPlantilla(Request $request)
    {
        DB::beginTransaction();
        try {
            ini_set('max_execution_time', 1200);
            Log::debug($request->file('cmFile'));
            Excel::import(new Techos, $request->file('cmFile'));
            DB::commit();
            return response()->json("done", 200); 
        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            $failures = $e->failures();
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
}
