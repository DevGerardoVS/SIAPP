<?php
namespace App\Imports\utils;

use App\Models\calendarizacion\Metas;
use App\Models\MmlActividades;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Calendarizacion\MetasController;

class FunFormatsNew extends CargaMasivaMetas
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function saveImport($filearray,$user)
    {
        
        $index = 2;
        $conmir = 0;
        $sinmir = 0;
        $arrayError = [];
        $clv_metas_temp_Nomir=[];
        $clv_metas_temp=[];
        if (count($filearray) <= 0) {
            $error = array(
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
             $arrayError[]=$error;
        }
        foreach ($filearray as $k) {
            $status = FunFormats::isNULLOrEmpy($k, $index);
            if ($status['status']) {
                $error = array(
                    "icon" => 'error',
                    "title" => 'Error',
                    "text" => $status['error']
                );
                 $arrayError[]=$error;
            } else {
                $anio = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', $k[7])->where('deleted_at', null)->max('ejercicio');
                //checar si la mir esta confirmada
                $cM = new CargaMasivaMetas($k, $anio,$user);
                if ($user->id_grupo == 4) {
                    if ($cM->clv_upp != $user->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la upp ' . $user->clv_upp . '. Revisa la fila: ' . $index
                        );
                         $arrayError[]=$error;
                    }
                }
                $isMir = DB::table("mml_cierre_ejercicio")
                    ->select('id', 'estatus')
                    ->where('clv_upp', '=', $cM->clv_upp)
                    ->where('ejercicio', '=', $anio)
                    ->where('statusm', 1)->get();
                if (!count($isMir)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Los registros de la MIR no estan confirmados en el sistema MML, acércate a CPLADEM'
                    );
                     $arrayError[]=$error;
                }
                if ($cM->actividad_id == 'N/A' && $cM->mir_id == 'N/A') {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Las dos actividades Ingresadas en la fila : ' . $index . ', son "N/A" tienes que llenar una en N/A y la otra con los datos correspondientes',
                    );
                     $arrayError[]=$error;
                }
                if ($cM->actividad_id == 'OT' && is_numeric($cM->mir_id)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Las actividades Ingresadas en la fila : ' . $index . ', tienen valores, debes elegir llenar una en N/A y la otra con los datos correspondientes',
                    );
                     $arrayError[]=$error;
                }
                if ($cM->actividad_id != 'N/A' && is_numeric($cM->actividad_id)) {
                    if (is_numeric($cM->actividad_id)) {
                        $activ = DB::table('catalogo')->where('ejercicio', $anio)->where('deleted_at', null)->where('grupo_id', 20)->where('id', $cM->actividad_id)->get();
                        if ($activ) {
                            $cM->mir_id = null;
                            $cM->tipoMeta = 'C';
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Actividad SIN MIR Ingresada no existe en la fila: ' . $index
                            );
                             $arrayError[]=$error;

                        }
                    } else {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Actividad Ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado'
                        );
                         $arrayError[]=$error;
                    }
                }
                if ($cM->mir_id != 'N/A' && is_numeric($cM->mir_id)) {
                    if (is_numeric($cM->mir_id)) {
                        $actividad = MmlActividades::where('deleted_at', null)->where('id', $cM->mir_id)->first();
                        Log::debug(json_encode( $actividad ));
                        if ($actividad) {
                            $cM->tipoMeta = 'M';
                            Log::debug("ACTIVIDAD EXISTE");
                            if ($actividad->area_funcional != $cM->area_funcional) {
                                Log::debug("AREA FUNCIONAL DIFERENTES: ".$actividad->area_funcional.'-'.$cM->area_funcional);
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'El areafuncional no coinciden en las claves presupuestales, en la fila: ' . $index
                                );
                                 $arrayError[]=$error;
                            }else{
                                Log::debug('ES MIR');
                                $cM->actividad_id = null;
                                $cM->tipoMeta = 'M';
                            }
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Actividad Ingresada no existe en la fila: ' . $index
                            );
                             $arrayError[]=$error;
                        }
                    } else {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Actividad Ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado'
                        );
                         $arrayError[]=$error;
                    }
                }
                Log::debug('VALIDACION MIR O NOMIR FINALIZA');
                if ($cM->actividad_id == 'OT' && isset($cM->mir_id)) {
                    $cM->tipoMeta = 'O';
                }

                Log::debug('VALIDACION PROYECTO EXISTA');
                $pres = FunFormats::existPP($cM);
                if (!count($pres)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'El Proyecto Ingresado no tiene presupuesto en la fila: ' . $index
                    );
                     $arrayError[]=$error;
                }
          /*       Log::debug('VALIDACION SEGUIMIENTO NO EXISTA');
                $seg = FunFormats::existSapp_Seguimiento($cM);
                if (count($seg)>=1) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'La upp y la ur ya tienen registros en seguimientos en la fila: ' . $index
                    );
                     $arrayError[]=$error;
                } */
                
                Log::debug('VALIDACION  TIPO CALENDARIO');
                switch ($cM->clv_cal) {
                    case '0':

                        break;
                    case '1':

                        break;
                    case '2':

                        break;

                    default:
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos erróneos',
                            "text" => 'La clave del calendario no coincide con el catálogo usa los datos proporcionados, en la fila: ' . $index
                        );
                         $arrayError[]=$error;
                         break;
                }
                $s = FunFormats::validatecalendar($cM->clv_upp, $cM->clv_cal);
                if (!$s["status"]) {
                    $text = isset($s["mensaje"])?$s["mensaje"]:'El tipo de calendario ' . $s["a"] . ' no esta autorizado para la upp  ' . $s["upp"];
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => $text. ' en la fila ' . $index
                    );
                     $arrayError[]=$error;
                }
                $meses = [
                    "enero" => $cM->enero,
                    "febrero" => $cM->febrero,
                    "marzo" => $cM->marzo,
                    "abril" => $cM->abril,
                    "mayo" => $cM->mayo,
                    "junio" => $cM->junio,
                    "julio" => $cM->julio,
                    "agosto" => $cM->agosto,
                    "septiembre" => $cM->septiembre,
                    "octubre" => $cM->octubre,
                    "noviembre" => $cM->noviembre,
                    "diciembre" => $cM->diciembre,
                ];


                $mCeros = array_filter($meses, function ($var) {
                    return $var != 0;
                });
                $mletras = [];
                foreach ($meses as $key => $value) {
                    if (!is_numeric($value)) {
                        $mletras[] = $value;

                    }
                }
                if (count($mletras)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Datos incorrectos',
                        "text" => 'los meses solo deben ser numeros enteros positivos en la meta de la fila: ' . $index
                    );
                     $arrayError[]=$error;
                }

                if (!count($mCeros)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Datos incorrectos',
                        "text" => 'No pueden ir en cero todos los meses y deben ser numeros enteros positivos en la meta de la fila: ' . $index
                    );
                     $arrayError[]=$error;
                }
                Log::debug('VALIDACION  MESES');
                $m = FunFormats::validateMonth($cM, json_encode($meses), $anio);
                if (!$m["status"]) {
                    $err = implode(", ", $m["errorM"]);
                    $meses = implode(", ", $m["mV"]);
                    if (count($m["mV"]) == 1) {

                        $mesaje = '. Solo puede registrar en el mes de: ' . $meses;
                    } else {
                        $mesaje = '. Solo puede registrar en los meses: ' . $meses;
                    }
                    $ceros = '';
                    if (count($m["mesCero"])) {
                        $mesesCero = implode(", ", $m["mesCero"]);
                        $ceros = ", los meses " . $mesesCero . ", NO pueden ir en CERO o numeros negativos";
                    }

                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'Los meses: ' . $err . ' no coinciden en las claves presupuestales, en la fila ' . $index . $mesaje . $ceros
                    );
                     $arrayError[]=$error;


                }
                Log::debug('VALIDACION  META EXISTA');
                    $e = FunFormats::isExist($cM, $anio);
                    if ($e["status"]) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'La meta ya existe en la fila ' . $index
                        );
                        $arrayError[] = $error;
                    }

                    $unique = "";
                    $uniqueMir = "";
                    switch ($cM->tipoMeta) {
                        case 'M':
                            $uniqueMir = strval($cM->clv_upp . $cM->clv_ur . $cM->area_funcional . $cM->clv_fondo . $cM->mir_id);
                            break;
                        case 'O':
                            $unique = strval($cM->clv_upp . $cM->clv_ur . $cM->area_funcional . $cM->clv_fondo . $cM->actividad_id);
                            break;
                        case 'C':
                            $unique = strval($cM->clv_upp . $cM->clv_ur . $cM->area_funcional . $cM->clv_fondo . $cM->actividad_id);
                            break;
                    }

  

                Log::debug('VALIDACION  MEDIDA EXISTA');
                $medidas = DB::table('unidades_medida')->select('id as clave')->where('deleted_at', null)->where('id', $cM->unidad_medida_id)->get();
                if (!count($medidas)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'La unidad de medida no existe en la fila ' . $index
                    );
                     $arrayError[]=$error;

                }
                Log::debug('VALIDACION  BENEFICIARIO EXISTA');
                $bene = DB::table('beneficiarios')->select('id', 'clave')->where('deleted_at', null)->where('clave', $cM->beneficiario_id)->get();
                if (!count($bene)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => 'La clave de beneficiario no existe en la fila ' . $index
                    );
                     $arrayError[]=$error;
                }

                if (!is_numeric($cM->cantidad_beneficiarios)) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Datos incorrectos',
                        "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                    );
                     $arrayError[]=$error;

                } else {
                    if ($cM->cantidad_beneficiarios <= 0) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                        );
                         $arrayError[]=$error;

                    }
                }

                if ($uniqueMir != '') {
                    $conmir++;
                }
                if ($unique != '') {
                    $clv_metas_temp_Nomir[]=  $unique;
                    $sinmir++;
                }
                Log::debug('VALIDACION  calendario EXISTA');
                $type = FunFormats::typeTotal($k, $m["validos"]);
                if ($type == false) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Tipo de calendario CONTINUO',
                        "text" => 'los valores de los meses tienen que ser iguales en la fila ' . $index
                    );
                     $arrayError[]=$error;
                }
                $cM->total = $type;
              $aux[] =$cM;
                $index++;
            }

        }//FIN DEL FOREACH
                    /*VALIDACION REPETIDAS DENTRO DE EL EXCEL */
                    Log::debug('VALIDACION  REPETIDAS EN EL EXCEL');
                    $repsmir =array_unique($clv_metas_temp);
                    $reps =array_unique($clv_metas_temp_Nomir);
                    if (count($repsmir) == $conmir && count($reps) == $sinmir && count($arrayError)<=0) {
                        Log::debug('VALIDACION  UPPS EN EL EXCEL');
                        if ($user->id_grupo == 4) {
                            foreach ($aux as $key) {
                                if ($key->clv_upp != $user->clv_upp) {
                                    $error = array(
                                        "icon" => 'info',
                                        "title" => 'Cuidado',
                                        "text" => 'Solo puedes registrar metas de la upp ' . $user->clv_upp
                                    );
                                     $arrayError[]=$error;
                                }
                            }
                        }
                    } else {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Metas repetidas en el excel'
                        );
                        $arrayError[]=$error;
                    }
                    if(count($arrayError)>=1){
                        Log::debug('VALIDACION  RESPONSE ERROR');
                        $success = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'El excel tiene diferentes errores',
                            "footer"=> '<button type="button" class="btn btn-secondary" onclick="dao.exportEcmExcel()">Errores</button>',
                            "arreglo"=>$arrayError
                        );
                        return $success;
                    }

                    Log::debug('VALIDACION  GUARDANDO');
                    Log::debug(json_encode($aux));
                    $success = array(
                        "icon" => 'success',
                        "title" => 'Exito',
                        "text" => 'Las metas se registraron correctamente',
                        "arreglo"=> json_encode($aux)
                    );
                    return $success;
                   
    }
}