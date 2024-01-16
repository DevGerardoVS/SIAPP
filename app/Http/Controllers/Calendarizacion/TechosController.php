<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Models\administracion\Bitacora;
use App\Models\calendarizacion\TechosFinancieros;

use App\Exports\PlantillaTechosExport;
use App\Exports\TechosExport;
use App\Exports\TechosExportPresupuestos;
use App\Exports\TechosExportPDF;
use App\Http\Controllers\Controller;
use App\Imports\TechosValidate;
use App\Http\Controllers\BitacoraController as ControllersBitacoraController;
use App\Helpers\Calendarizacion\MetasHelper;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Dompdf\Exception;
use Shuchkin\SimpleXLSX;
use Throwable;
use function Psy\debug;
use Maatwebsite\Excel\Facades\Excel;

class TechosController extends Controller
{
    //Consulta Vista Techos
    public function getIndex(){
        Controller::check_permission('getTechos');
        return view('calendarizacion.techos.index');
    }
    
    public function getTechos(Request $request){
        Controller::check_permission('getTechos');
        $dataSet = [];

        $data = DB::table('techos_financieros as tf')
            ->select('tf.id','tf.clv_upp','vee.upp as descPre','tf.tipo','tf.clv_fondo','f.fondo_ramo','tf.presupuesto','tf.ejercicio','tf.updated_user')
            ->leftJoinSub('select distinct clv_upp, upp, ejercicio as Ej from v_epp','vee','tf.clv_upp','=','vee.clv_upp')
            ->leftJoinSub('select distinct clv_fondo_ramo, fondo_ramo from fondo','f','tf.clv_fondo','=','f.clv_fondo_ramo')
            ->where('tf.deleted_at','=',null);
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
        Controller::check_permission('getTechos');
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
        Controller::check_permission('getTechos');
        $fondos = DB::table('fondo')
            ->select('clv_fondo_ramo','fondo_ramo')
            ->distinct()
            ->get();

        return json_encode($fondos);
    }

    public function getEjercicio(){
        Controller::check_permission('getTechos');
        $ejercicio = DB::table('epp') 
        ->select('ejercicio')
        ->groupBy('ejercicio')
        ->orderByDesc('ejercicio')
        ->limit(1)
        ->get();

        return $ejercicio;
    }

    public function addTecho(Request $request){ 
        Controller::check_permission('putTechos'); 
        
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

        //VERIFICAR QUE LA UPP ESTE VALIDA Y PODER AGREGAR UN FONDO RH
        $verifica_upp_autorizada = DB::table('uppautorizadascpnomina')
        ->where('clv_upp','=',$upp)
        ->get();

        if(count($verifica_upp_autorizada) == 0){
            foreach($data as $d){
                if($d[0] == 'RH') {
                    return [
                        'status' => 'No autorizado',
                        'error' => "UPP no autorizada",
                        'etiqueta' => 'La UPP no puede añadir fondos de tipo RH'
                    ];
                }
            }
        }

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
        ->select('clv_upp','clv_fondo','tipo','ejercicio','deleted_at')
        ->where('ejercicio','=',$ejercicio)
        ->where('deleted_at','=',null)
        ->get();

        $c = 0;
        foreach($data as $d){
            foreach($array_data as $ad){
                if($d[0] == $ad->tipo && $d[1] == $ad->clv_fondo && $upp == $ad->clv_upp && $ad->deleted_at == null){
                    return [
                        'status' =>'Ejercicio_Repetido',
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
                    "accion"=> 'Crear',
                    "modulo"=>'Techos Financieros'
                );
                
                Controller::bitacora($b);

                //Desconfirmamos las claves y metas

                $data = DB::table('techos_financieros')
                ->select('clv_upp','clv_fondo','ejercicio')
                ->where('clv_upp','=',$upp)
                ->where('ejercicio','=',$ejercicio)
                ->where('deleted_at','=',null)
                ->get();
                
                //se busca el registro en claves para saber el estado CONFIRMADO
                $confirmadoClave = DB::table('programacion_presupuesto')
                ->select('estado')
                ->where('upp','=',$data[0]->clv_upp)
                ->where('ejercicio','=',$data[0]->ejercicio)
                ->where('deleted_at','=',null)
                ->limit(1)
                ->get();
                
                $confirmacionMeta = MetasHelper::actividades($data[0]->clv_upp, $data[0]->ejercicio);
                
                if(count($confirmadoClave) == 0){ //si no esta asignado a una clave presupuestaria se EDITA normalmente
                    DB::beginTransaction();
                    if(count($confirmacionMeta) != 0){
                        foreach($confirmacionMeta as $cm){ 
                                DB::table('metas')
                                ->where('id','=',$cm->id)
                                ->update(['estatus' => 0]);
                        }
                    }
                    DB::commit();
    
                    $b = array(
                        "username"=>Auth::user()->username,
                        "accion"=> 'Editar',
                        "modulo"=>'Techos Financieros'
                    );
                    
                    Controller::bitacora($b);
    
                    return [
                        'status' => 200,
                        'mensaje' => "Se guardó correctamente"
                    ];
                }else{
                    DB::beginTransaction();
                    
                    DB::table('programacion_presupuesto')
                    ->where('upp','=',$data[0]->clv_upp)
                    ->where('ejercicio','=',$data[0]->ejercicio)
                    ->update(['estado' => 0]);
    
                    if(count($confirmacionMeta) != 0){
                        foreach($confirmacionMeta as $cm){
                            if($data[0]->ejercicio == $cm->ejercicio){
                                DB::table('metas')
                                ->where('id','=',$cm->id)
                                ->update(['estatus' => 0]);
                            }
                        }
                    }
                    
                    DB::commit();
                    return [
                        'status' => 200,
                        'mensaje' => "Se guardó correctamente y las UPP correspondientes en las Claves Presupuestarias se desconfirmaron"
                    ];
                }
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
        Controller::check_permission('deleteTechos');
        try{
            //se obtienen los datos del registro para buscarlo en las claves presupuestarias
            $data = DB::table('techos_financieros')
            ->select('clv_upp','clv_fondo','tipo','ejercicio','deleted_at')
            ->where('id','=',$request->id)
            ->get();
            
            if(count($data)!= 0){
                $existe = DB::table('programacion_presupuesto')
                ->where('upp','=',$data[0]->clv_upp)
                ->where('fondo_ramo','=',$data[0]->clv_fondo)
                ->where('tipo','=',$data[0]->tipo)
                ->where('ejercicio','=',$data[0]->ejercicio)
                ->where('deleted_at','=',null)
                ->get();
                
                //si existe en la tabla quiere decir que ya esta asignado y no se puede eliminar
                if(count($existe) == 0 || $data[0]->deleted_at != null || $existe[0]->deleted_at != null){
                    
                    TechosFinancieros::where('id', $request->id)->delete();
    
                    $b = array(
                        "username"=>Auth::user()->username,
                        "accion"=> 'Eliminar',
                        "modulo"=>'Techos Financieros'
                    );
                    
                    Controller::bitacora($b);
    
                    return [
                        'status' => 200,
                        'mensaje' => "Se eliminó correctamente"
                    ];
                }else{
                    return [
                        'status' => 400,
                        'error' => "No se pueden eliminar techos cuando ya se está utilizando el presupuesto de esa UPP y fondo, es necesario ajustar las claves presupuestarias y dejar disponible"
                    ];
                }
            }else{
                return [
                    'status' => 400,
                    'error' => "No se puede eliminar"
                ];
            }
            //se busca el registro en claves
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
        log::debug($request);
        Controller::check_permission('putTechos');
        try{
            ///buscamos el registro en los techos para despues filtrarlo 
            $data = DB::table('techos_financieros')
            ->select('clv_upp','clv_fondo','ejercicio','presupuesto')
            ->where('id','=',$request->id)
            ->get();
            
            if($request->presupuesto > $data[0]->presupuesto){
                $result = $this->saveEdit($data,$request);

                //DESCONFIRMAR metas
                $resultDesconfirmacion = $this->desconfirmar($data);

                return [
                    'status' => $resultDesconfirmacion['status'],
                    'mensaje' => $resultDesconfirmacion['mensaje']
                ];
                
            }else{
                $claves_deleted = DB::table('programacion_presupuesto')
                ->select(
                    DB::raw('SUM(IFNULL(total,0)) AS total'),
                )
                ->where('upp','=',$data[0]->clv_upp)
                ->where('fondo_ramo','=',$data[0]->clv_fondo)
                ->where('ejercicio','=',$data[0]->ejercicio)
                ->where('deleted_at','=',null)
                ->get();
                log::debug($claves_deleted);
                if(count($claves_deleted) != 0){ 
                    if($request->presupuesto >= $claves_deleted[0]->total){
                        $result = $this->saveEdit($data,$request);
                        return [
                            'status' => $result['status'],
                            'mensaje' => $result['mensaje']
                        ];
                    }else{
                        return [
                            'status' => '400',
                            'mensaje' => 'No se puede reducir presupuesto que ya se está usando, es necesario primero ajustar las claves presupuestarias'
                        ];
                    }
                }else{
                    $result = $this->saveEdit($data,$request);
                        return [
                            'status' => $result['status'],
                            'mensaje' => $result['mensaje']
                        ];
                }
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

    private function desconfirmar($data){
        //se busca el registro en claves para saber el estado CONFIRMADO
        $confirmadoClave = DB::table('programacion_presupuesto')
        ->select('estado')
        ->where('upp','=',$data[0]->clv_upp)
        ->where('ejercicio','=',$data[0]->ejercicio)
        ->where('deleted_at','=',null)
        ->limit(1)
        ->get();

        $confirmacionMeta = MetasHelper::actividades($data[0]->clv_upp, $data[0]->ejercicio);

        if(count($confirmadoClave) == 0){ //si no esta asignado a una clave presupuestaria se EDITA normalmente
            DB::beginTransaction();
            if(count($confirmacionMeta) != 0){
                foreach($confirmacionMeta as $cm){ 
                        DB::table('metas')
                        ->where('id','=',$cm->id)
                        ->update(['estatus' => 0]);
                }
            }
            DB::commit();

            $b = array(
                "username"=>Auth::user()->username,
                "accion"=> 'Editar',
                "modulo"=>'Techos Financieros'
            );
            
            Controller::bitacora($b);

            return [
                'status' => 200,
                'mensaje' => "Se guardó correctamente"
            ];
        }else{
            DB::beginTransaction();
            
            DB::table('programacion_presupuesto')
            ->where('upp','=',$data[0]->clv_upp)
            ->where('ejercicio','=',$data[0]->ejercicio)
            ->update(['estado' => 0]);

            if(count($confirmacionMeta) != 0){
                foreach($confirmacionMeta as $cm){
                    if($data[0]->ejercicio == $cm->ejercicio){
                        DB::table('metas')
                        ->where('id','=',$cm->id)
                        ->update(['estatus' => 0]);
                    }
                }
            }
            
            DB::commit();
            return [
                'status' => 200,
                'mensaje' => "Se guardó correctamente y las UPP correspondientes en las Claves Presupuestarias se desconfirmaron"
            ];
        }
    }

    private function saveEdit($data, $request){
        $confirmadoClave = DB::table('programacion_presupuesto')
                ->select('estado')
                ->where('upp','=',$data[0]->clv_upp)
                ->where('ejercicio','=',$data[0]->ejercicio)
                ->where('deleted_at','=',null)
                ->limit(1)
                ->get();

                $confirmacionMeta = MetasHelper::actividades($data[0]->clv_upp, $data[0]->ejercicio);
                    
                if(count($confirmadoClave) == 0){ //si no esta asignado a una clave presupuestaria se EDITA normalmente
                    DB::beginTransaction();

                    DB::table('techos_financieros')
                    ->where('id','=',$request->id)
                    ->update(['presupuesto' => $request->presupuesto,'updated_user' =>Auth::user()->username ]);

                    if(count($confirmacionMeta) != 0){
                        foreach($confirmacionMeta as $cm){ 
                                DB::table('metas')
                                ->where('id','=',$cm->id)
                                ->update(['estatus' => 0]);
                        }
                    }
                    DB::commit();

                    $b = array(
                        "username"=>Auth::user()->username,
                        "accion"=> 'Editar',
                        "modulo"=>'Techos Financieros'
                    );
                    
                    Controller::bitacora($b);

                    return [
                        'status' => 200,
                        'mensaje' => "Se editó correctamente"
                    ];
                }else{
                    DB::beginTransaction();

                    DB::table('techos_financieros')
                    ->where('id','=',$request->id)
                    ->update(['presupuesto' => $request->presupuesto,'updated_user' =>Auth::user()->username ]);
                    
                    DB::table('programacion_presupuesto')
                    ->where('upp','=',$data[0]->clv_upp)
                    ->where('ejercicio','=',$data[0]->ejercicio)
                    ->update(['estado' => 0]);

                    if(count($confirmacionMeta) != 0){
                        foreach($confirmacionMeta as $cm){
                            if($data[0]->ejercicio == $cm->ejercicio){
                                DB::table('metas')
                                ->where('id','=',$cm->id)
                                ->update(['estatus' => 0]);
                            }
                        }
                    }
                    
                    DB::commit();
                    return [
                        'status' => 200,
                        'mensaje' => "Se editó correctamente y las UPP correspondientes en las Claves Presupuestarias se desconfirmaron"
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

    public function importPlantilla(Request $request){
        $this->validate($request, [
            'cmFile' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('cmFile');
        DB::beginTransaction();
        try {
            ini_set('max_execution_time', 1200);
            Schema::create('temp_techos', function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->string('clv_upp', 3)->nullable(false);
                $table->string('clv_fondo', 2)->nullable(false);
                $table->integer('ejercicio')->default(null);
            });
            if ($xlsx = SimpleXLSX::parse($the_file)) {
                $filearray = $xlsx->rows();
                for ($i = 0; $i < $filearray; $i++) {
                    if (count($filearray[$i]) > 5) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'No agregue datos a otras columnas. Siga las instrucciones.'
                        );
                        return response()->json($error);
                    } else {
                        if ($filearray[0][0] == 'EJERCICIO' && $filearray[0][1] == 'UPP' && $filearray[0][2] == 'FONDO' && $filearray[0][3] == 'OPERATIVO' && $filearray[0][4] == 'RECURSOS HUMANOS') {
                            array_shift($filearray);
                            $resul = TechosValidate::validate($filearray);
                            if ($resul == 'done') {
                                $b = array(
                                    "username" => Auth::user()->username,
                                    "accion" => 'Carga masiva',
                                    "modulo" => 'Techos financieros'
                                );
                                Controller::bitacora($b);

                                DB::commit();
                            }
                            return response()->json($resul);
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Ingresa la plantilla sin modificaciones. Siga las instrucciones.'
                            );
                            return response()->json($error);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    
    public function exportExcel(Request $request){
        Controller::check_permission('postTechos');
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
        Controller::check_permission('postTechos');
        try{
            ob_end_clean();
            ob_start();
            $b = array(
                "username"=>Auth::user()->username,
                "accion"=> 'Exportar PDF',
                "modulo"=>'Techos Financieros'
            );
            
            Controller::bitacora($b);
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
        Controller::check_permission('postTechos');
        try{
            ob_end_clean();
            ob_start();
            $b = array(
                "username"=>Auth::user()->username,
                "accion"=> 'Export presupuestos',
                "modulo"=>'Techos Financieros'
            );
            
            Controller::bitacora($b);
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
