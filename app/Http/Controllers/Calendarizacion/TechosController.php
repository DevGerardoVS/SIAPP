<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Models\administracion\Bitacora;

use App\Exports\TechosExport;
use App\Exports\TechosExportPDF;
use App\Exports\TechosExportPresupuestos;
use App\Http\Controllers\Controller;
use App\Imports\TechosValidate;
use Carbon\Carbon;
use Dompdf\Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Shuchkin\SimpleXLSX;
use Throwable;
use function Psy\debug;
use App\Exports\PlantillaTechosExport;
use App\Http\Controllers\BitacoraController as ControllersBitacoraController;
use Maatwebsite\Excel\Facades\Excel;

class TechosController extends Controller
{
    //Consulta Vista Techos
    public function getIndex(){
        return view('calendarizacion.techos.index');
    }

    public function getTechos(Request $request){
        $dataSet = [];

        $data = DB::table('techos_financieros as tf')
            ->select('tf.id','tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio','tf.updated_user')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo');
            if($request->anio_filter != null){
                $data =  $data -> where('tf.ejercicio','=',$request->anio_filter);
                $data =  $data -> where('vee.Ej','=',$request->anio_filter);
            }
            if($request->upp_filter != null && $request->upp_filter != 0){
                $data = $data -> where('tf.clv_upp','=',$request->upp_filter);
            }
            if($request->fondo_filter != null && $request->fondo_filter != 0){
                $data = $data -> where('tf.clv_fondo','=',$request->fondo_filter);
            }
        $data = $data ->orderBy('vee.clv_upp','asc')->get();
        
        $max_ejercicio = DB::table('epp')
            ->select('ejercicio')
            ->groupBy('ejercicio')
            ->orderByDesc('ejercicio')
            ->limit(1)
            ->get();

        
            foreach ($data as $d){
            if($max_ejercicio[0]->ejercicio == $d->ejercicio){
                $button2 = '<a class="btn btn-secondary" onclick="getEdita('.$d->id.')" data-bs-toggle="modal" data-bs-target="#editar" ><i class="fa fa-pencil" style="font-size: large; color: white"></i></a>';
                /* $button3 = '<button id="eliminar" title="Eliminar" class="btn btn-danger"><i class="fa fa-trash" style="font-size: large"></i></button>'; */
                $button3 = '<a class="btn btn-danger" onclick="getElimina('.$d->id.')" data-bs-toggle="modal" data-bs-target="#eliminar" ><i class="fa fa-trash" style="font-size: large;color: white"></i></a>';
                array_push($dataSet,[$d->clv_upp, $d->descPre, $d->tipo,$d->clv_fondo,$d->fondo_ramo,'$'.number_format($d->presupuesto),$d->ejercicio,$d->updated_user,$button2.' '.$button3]);
            }else{
                array_push($dataSet,[$d->clv_upp, $d->descPre, $d->tipo,$d->clv_fondo,$d->fondo_ramo,'$'.number_format($d->presupuesto),$d->ejercicio,$d->updated_user,' ']);
            }
            
        }

        return [
            'dataSet'=>$dataSet,
            'data' => json_encode($data)
        ];
    }

    public function getTechoEdit(Request $request){

        $max_ejercicio = DB::table('epp')
            ->select('ejercicio')
            ->groupBy('ejercicio')
            ->orderByDesc('ejercicio')
            ->limit(1)
            ->get();

        $data = DB::table('techos_financieros as tf')
            ->select('tf.id','tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.id','=',$request->id)
            ->where('tf.ejercicio','=',$max_ejercicio[0]->ejercicio)
            ->where('vee.Ej','=',$max_ejercicio[0]->ejercicio)
            ->get();

            return [
                'data' => $data
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
        $aRepetidos = array_chunk(array_slice($request->all(),3),3,true);
        $aKeys = array_keys(array_slice($request->all(),3));
        $validaForm = [];
        
        $upp = $request->uppSelected;
        $ejercicio = $request->anio;

        //Crea array con los validations
        foreach ($aKeys as $a){
            $validaForm[$a] = 'required';
        }
        
        $request->validate($validaForm);

        //Verifica que no se dupliquen los fondos en el mismo techo financiero
        // y envia el array con las keys del input duplicado
        $repeticion = $data;
        $c = 0;
        foreach ($data as $a){
            $repeticion = array_slice($repeticion,1);

            foreach ($repeticion as $r){
                if($r[0] === $a[0] && $r[1] === $a[1]){
                    return [
                        'status' => 'Repetidos',
                        'error' => "Campos repetidos",
                        'etiqueta' => array_keys($aRepetidos[$c])
                    ];
                }
            }
            $c += 1;
        }

        //Verifica que no se dupliquen los fondos en el mismo ejercicio
        // y envia el array con las keys del input duplicado
        $repeticion = $data;
        $array_data = DB::table('techos_financieros')
        ->select('clv_upp','clv_fondo','tipo','ejercicio')
        ->where('ejercicio','=',$ejercicio)
        ->get();
        
        $c = 0;
        foreach($data as $d){
            foreach($array_data as $ad){
                if($d[0] == $ad->tipo && $d[1] == $ad->clv_fondo && $upp == $ad->clv_upp){
                    return [
                        'status' => 'Ejercicio_Repetido',
                        'error' => "El registro ya existe en el ejercicio actual",
                        'etiqueta' => array_keys($aRepetidos[$c])
                    ];
                }
            }
            $c += 1;
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

                $b = array(
                    "username"=>Auth::user()->username,
                    "accion"=> 'Añadir',
                    "modulo"=>'Techos Financieros'
                );
                
                

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

    public function eliminar(Request $request){
        try{
            //se obtienen los datos del registro para buscarlo en las claves presupuestarias
            $data = DB::table('techos_financieros')
            ->select('clv_upp','clv_fondo','tipo','ejercicio')
            ->where('id','=',$request->id)
            ->get();

            //se busca el registro en claves
            $existe = DB::table('programacion_presupuesto')
            ->where('upp','=',$data[0]->clv_upp)
            ->where('fondo_ramo','=',$data[0]->clv_fondo)
            ->where('tipo','=',$data[0]->tipo)
            ->where('ejercicio','=',$data[0]->ejercicio)
            ->get();

            //si existe en la tabla quiere decir que ya esta asignado y no se puede eliminar
            if(count($existe) == 0){
                DB::beginTransaction();
                DB::table('techos_financieros')->where('id', '=', $request->id)->delete();
                DB::commit();

                return [
                    'status' => 200,
                    'mensaje' => "Se eliminó correctamente"
                ];
            }else{
                return [
                    'status' => 400,
                    'error' => "No se puede eliminar"
                ];
            }
        }catch (Throwable $e){
            DB::rollBack();
            report($e);
            return [
                'status' => 400,
                'error' => $e
            ];
        }
    }

    public function editar(Request $request){
        try{
            ///buscamos el registro en los techos para despues filtrarlo 
            $data = DB::table('techos_financieros')
            ->select('clv_upp','ejercicio')
            ->where('id','=',$request->id)
            ->get();

            //se busca el registro en claves para saber  el estado CONFIRMADO
            $confirmado = DB::table('programacion_presupuesto')
            ->select('estado')
            ->where('upp','=',$data[0]->clv_upp)
            ->where('ejercicio','=',$data[0]->ejercicio)
            ->limit(1)
            ->get();
            
            if(count($confirmado) == 0){
                DB::beginTransaction();
                DB::table('techos_financieros')
                ->where('id','=',$request->id)
                ->update(['presupuesto' => $request->presupuesto,'updated_user' =>Auth::user()->username ]);
                DB::commit();
                return [
                    'status' => 200,
                    'mensaje' => "Se editó correctamente"
                ];
            }else{
                DB::beginTransaction();

                DB::table('techos_financieros')
                ->where('id','=',$request->id)
                ->update(['presupuesto' => $request->presupuesto]);
                
                if($confirmado[0]->estado == 1){
                    DB::table('programacion_presupuesto')
                    ->where('upp','=',$data[0]->clv_upp)
                    ->where('ejercicio','=',$data[0]->ejercicio)
                    ->update(['estado' => 0]);
                }
                
                DB::commit();
                return [
                    'status' => 200,
                    'mensaje' => "Se editó correctamente"
                ];
            }
        }catch (Throwable $e){
            DB::rollBack();
            report($e);
            return [
                'status' => 400,
                'error' => $e
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
        $this->validate($request, [
            'cmFile' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('cmFile');
        DB::beginTransaction();
        ini_set('max_execution_time', 1200);
        try {
            if ($xlsx = SimpleXLSX::parse($the_file)) {
                $filearray = $xlsx->rows();
                if ($filearray[0][0] == 'EJERCICIO' && $filearray[0][1] == 'UPP' && $filearray[0][2] == 'FONDO' && $filearray[0][3] == 'OPERATIVO' && $filearray[0][4] == 'RECURSOS HUMANOS') {
                    array_shift($filearray);
                    $resul = TechosValidate::validate($filearray);
                    if ($resul == 'done') {
                        DB::commit();
                    }
                    return response()->json($resul);
                } else {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Ingresa la plantilla sin eliminar filas'
                    );
                    return response()->json($error);
                }
            }
        } catch (\Exception $e) {
			DB::rollback();
		}
    }
    
    public function exportExcel(Request $request){
        try{
            ob_end_clean();
            ob_start();
            return Excel::download(new TechosExport($request->anio_filter_export),'Techos_Financieros.xlsx');
        }catch (Throwable $e){
            DB::rollBack();
            report($e);
            return [
                'error' => $e
            ];
        }
    }
    
    public function exportPDF(Request $request){
        
        try{
            ob_end_clean();
            ob_start();
            return Excel::download(new TechosExportPDF($request->anio_filter_pdf),'Techos_Financieros.pdf');
        }catch (Throwable $e){
            DB::rollBack();
            report($e);
            return [
                'error' => $e
            ];
        }
    }

    public function exportPresupuestos(Request $request){
        try{
            ob_end_clean();
            ob_start();
            
            return Excel::download(new TechosExportPresupuestos($request->anio_filter_presupuestos),'Presupuestos_Techos_Financieros.xlsx');
        }catch (Throwable $e){
            DB::rollBack();
            report($e);
            return [
                'error' => $e
            ];
        }
    }
}
