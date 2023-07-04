<?php

namespace App\Http\Controllers\Calendarizacion;

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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        $upp = $request->uppSelected;
        $ejercicio = $request->anio;

        //Crea array con los validations
        foreach ($aKeys as $a){
            $validaForm[$a] = 'required';
        }

        $request->validate($validaForm);

        //Verifica que no se dupliquen los fondos en el mismo ejercicio
        $repeticion = $data;
        foreach ($data as $a){
            $repeticion = array_slice($repeticion,1);
            foreach ($repeticion as $r){
                if($r[0] === $a[0] && $r[1] === $a[1]){
                    return [
                        'status' => 'Repetidos',
                        'error' => "Campos repetidos"
                    ];
                }
            }
        }

        //guarda el techo
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
            Schema::create('temp_techos', function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->string('clv_upp', 3)->nullable(false);
                $table->string('clv_fondo', 2)->nullable(false);
                $table->integer('ejercicio')->default(null);
                $table->enum('tipo', ['Operativo', 'RH'])->nulleable(false);
                $table->bigInteger('presupuesto')->nullable(false);
            });
            Excel::import(new Techos, $request->file('cmFile'));
            DB::commit();
            return response()->json("done", 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            $failures = $e->failures();
            $error = '';
            $value = '';
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $value = [$failure->values()]; // The values of the row that has failed.
                $error = [$failure->errors()]; // Actual error messages from Laravel validator
                /* Log::debug($failure->row());
                   Log::debug($failure->attribute());
                   Log::debug($failure->errors());
                   Log::debug($failure->values()); */
            }
            if ($error != '') {
                if (!empty($value[0]['fondo'])) {
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => $error,
                    );
                } else {
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => $error,
                    );
                }
            }
            if ($value != '') {
                if (!empty($value[0]['fondo'])) {
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => $error,
                    );
                } else {
                    $returnData = array(
                        'status' => 'error',
                        'title' => 'Error',
                        'message' => $value,
                    );
                }
            }

            return response()->json($returnData);
        }
    }
}
