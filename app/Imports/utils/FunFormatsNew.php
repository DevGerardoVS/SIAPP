<?php
namespace App\Imports\utils;

use App\Models\calendarizacion\Metas;
use App\Models\Mir;
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

    public static function saveImport($filearray, $user)
    {

        $index = 2;
        $conmir = 0;
        $sinmir = 0;
        $arrayError = [];
        $metas_temp_Nomir = [];
        $metas_temp = [];
        $clv_metas_temp_Nomir = [];
        $clv_metas_temp = [];
        $repetidas = [];
        if (count($filearray) <= 0) {
            $error = array(
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
            $arrayError[] = $error;

        }
        foreach ($filearray as $k) {
            $checkUpp = false;
            Log::debug( strval($k[7]));
            $anio = DB::table('cierre_ejercicio_metas')->where(['clv_upp'=>strval($k[7]),'deleted_at'=>null])->max('ejercicio');
            Log::debug( $anio);
            $status = FunFormats::isNULLOrEmpy($k, $index);
            Log::debug($status);
            if ($status['status']) {
                $error = array(
                    "icon" => 'error',
                    "title" => 'Error',
                    "text" => $status['error']
                );
                $arrayError[] = $error;

                $index++;
            } else {
                //checar si la mir esta confirmada
                    $cM = new CargaMasivaMetas($k, $anio, $user);
                Log::debug(json_encode($cM ));
                if ($user->id_grupo == 4) {
                    if ($cM->clv_upp != $user->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la UPP: ' . $user->clv_upp . '. Revisa la fila: ' . $index . '. No se revisaron los demás campos, ya que no corresponde la UPP.'
                        );
                        $arrayError[] = $error;
                    } else {
                        $checkUpp = true;
                    }
                } else {

                    $upp = DB::table('catalogo')->where(['clave' => $cM->clv_upp, 'deleted_at' => null, 'grupo_id' => 6, 'ejercicio' => $anio])->get();
                    Log::debug($upp);
                    if (count($upp) == 0) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'La upp ' . strval($cM->clv_upp) . ' NO se encuentra en el catálogo. Revisa la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    } else {
                        $checkUpp = true;
                    }
                }
                if ($checkUpp) {
                    $isMir = DB::table("mml_cierre_ejercicio")
                        ->select('id', 'estatus')
                        ->where('clv_upp', '=', $cM->clv_upp)
                        ->where('ejercicio', '=', $anio)
                        ->where('statusm', 1)->get();
                        Log::debug($isMir);
                    if (!count($isMir)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Los registros de la MIR no están confirmados en el sistema MML, acércate a CPLADEM Revisa la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }
                    if ($cM->actividad_id == 'N/A' && $cM->mir_id == 'N/A') {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Las dos actividades ingresadas en la fila: ' . $index . ', son "N/A" tienes que llenar una en N/A y la otra con los datos correspondientes.',
                        );
                        $arrayError[] = $error;
                    }
                    if ($cM->actividad_id == 'OT' && is_numeric($cM->mir_id)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Las actividades ingresadas en la fila: ' . $index . ', tienen valores, debes elegir llenar una en N/A y la otra con los datos correspondientes.',
                        );
                        $arrayError[] = $error;
                    }
                    Log::debug('actividad vacia' );
                    if ($cM->actividad_id != 'N/A' && is_numeric($cM->actividad_id)) {
                        if (is_numeric($cM->actividad_id)) {
                            $cM->tipoMeta = 'C';
                            $activ = DB::table('catalogo')->where('ejercicio', $anio)->where('deleted_at', null)->where('grupo_id', 20)->where('id', $cM->actividad_id)->get();
                            Log::debug($activ );
                            if ($activ) {
                                $cM->mir_id = null;

                            } else {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'Actividad SIN MIR ingresada no existe en la fila: ' . $index . '.'
                                );
                                $arrayError[] = $error;

                            }
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Actividad ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado.'
                            );
                            $arrayError[] = $error;
                        }
                    }
                    if ($cM->mir_id != 'N/A' && is_numeric($cM->mir_id)) {
                        if (is_numeric($cM->mir_id)) {
                            $cM->tipoMeta = 'M';
                            $actividad = Mir::where('deleted_at', null)->where('id', $cM->mir_id)->first();
                            Log::debug($actividad );

                            if ($actividad) {
                                if ($actividad->area_funcional != $cM->area_funcional) {
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Error',
                                        "text" => 'El área funcional no coinciden en las claves presupuestales, en la fila: ' . $index . '.'
                                    );
                                    $arrayError[] = $error;
                                } else {
                                    $cM->actividad_id = null;
                                    $cM->tipoMeta = 'M';
                                }
                            } else {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'Actividad ingresada no existe, en la fila: ' . $index . '.'
                                );
                                $arrayError[] = $error;
                            }
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Actividad ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado.'
                            );
                            $arrayError[] = $error;
                        }
                    }
                    if ($cM->actividad_id == 'OT' && isset($cM->mir_id)) {
                        $cM->tipoMeta = 'O';
                        Log::debug('ES OT');
                    }
                    $seg = FunFormats::existPP($cM);
                    if (!count($seg)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'El Proyecto ingresado no tiene presupuesto en la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }
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
                                "text" => 'La clave del calendario no coincide con el catálogo usa los datos proporcionados, en la fila: ' . $index . '.'
                            );
                            $arrayError[] = $error;
                            break;
                    }
                    $s = FunFormats::validatecalendar($cM->clv_upp, $cM->clv_cal);
                    Log::debug($s );
                    if (!$s["status"]) {
                        $text = isset($s["mensaje"]) ? $s["mensaje"] : 'El tipo de calendario ' . $s["a"] . ' no está autorizado para la UPP  ' . $s["upp"];
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => $text . ', en la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
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

                    $mletras = [];
                    $mplus = [];
                    $mInt = [];
                    $mCeros = array_filter($meses, function ($var) {
                        if ($var != 0) {
                            return $var;
                        } else {

                        }

                    });

                    foreach ($meses as $key => $value) {
                        if (is_numeric($value)) {
                            if (is_float($value + 0)) {
                                $mInt[] = $value;
                            }
                            if ($value < 0) {
                                $mplus[] = $value;
                            }
                        } else {
                            $mletras[] = $value;
                        }
                    }
                    if (count($mletras)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'Los meses solo deben ser números enteros positivos en la meta de la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }
                    if (count($mInt) >= 1) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'Los meses solo deben ser números enteros en la meta de la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }
                    if (count($mplus) >= 1) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'Los meses solo deben ser números positivos en la meta de la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }
                    if (!count($mCeros)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'No pueden ir en cero todos los meses y deben ser números enteros positivos en la meta de la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    } else {
                        $mCeros = [];
                    }

                    $resultado = array_merge($mInt, $mplus, $mletras);
                    if (count($resultado) == 0) {
                        $m = FunFormats::validateMonth($cM, json_encode($meses));
                        Log::debug($m );
                        if (!$m["status"]) {
                            $err = implode(", ", $m["errorM"]);
                            $meses = implode(", ", $m["mV"]);
                            if (count($m["mV"]) == 1) {

                                $mesaje = '. Solo puede registrar en el mes de: ' . $meses . '.';
                            } else {
                                $mesaje = '. Solo puede registrar en los meses: ' . $meses . '.';
                            }
                            $ceros = '';
                            if (count($m["mesCero"])) {
                                $mesesCero = implode(", ", $m["mesCero"]);
                                $ceros = ", los meses " . $mesesCero . ", NO pueden ir en CERO o numeros negativos.";
                            }

                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Los meses: ' . $err . ' no coinciden en las claves presupuestales, en la fila ' . $index . $mesaje . $ceros . '.'
                            );
                            $arrayError[] = $error;
                        } else {
                            $type = FunFormats::typeTotal($k, $m["validos"]);
                            if ($type == false) {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Tipo de calendario CONTINUO',
                                    "text" => 'Tipo de calendario CONTINUO. Los valores de los meses tienen que ser iguales en la fila: ' . $index . '.'
                                );
                                $arrayError[] = $error;
                            }
                            $cM->total = $type;

                        }
                    } else {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'No se puede continuar con las validaciones de los meses y calcular el total dependiendo el tipo de calendario, por que no cumplen las reglas de los datos aceptados en la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;

                    }
                    $e = FunFormats::isExist($cM, $anio);
                    Log::debug($e );
                    if ($e["status"]) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'La meta ya existe en la fila: ' . $index . '.'
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
                    $medidas = DB::table('unidades_medida')->select('id as clave')->where('deleted_at', null)->where('id', $cM->unidad_medida_id)->get();
                    Log::debug( $medidas );
                    if (!count($medidas)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'La unidad de medida no existe en la fila ' . $index . '.'
                        );
                        $arrayError[] = $error;

                    }
                    $bene = DB::table('beneficiarios')->select('id', 'clave')->where('deleted_at', null)->where('clave', $cM->beneficiario_id)->get();
                    Log::debug( $bene );
                    if (!count($bene)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'La clave de beneficiario no existe en la fila ' . $index . '.'
                        );
                        $arrayError[] = $error;
                    }

                    if (!is_numeric($cM->cantidad_beneficiarios)) {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Datos incorrectos',
                            "text" => 'El número de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index . '.'
                        );
                        $arrayError[] = $error;

                    } else {
                        if ($cM->cantidad_beneficiarios <= 0) {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Datos incorrectos',
                                "text" => 'El número de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index . '.'
                            );
                            $arrayError[] = $error;

                        }
                    }

                    if ($uniqueMir != '') {
                        $conmirData = ['clave' => $uniqueMir, 'fila' => $index, 'upp' => $cM->clv_upp, "ur" => $cM->clv_ur];
                        for ($i = 0; $i < count($metas_temp); $i++) {
                            if ($metas_temp[$i]['clave'] == $uniqueMir) {
                                $repetidas[] = [$metas_temp[$i]['fila'], $index];
                            }
                        }

                        $metas_temp[] = $conmirData;
                        $clv_metas_temp[] = $uniqueMir;
                        $conmir++;
                    }
                    if ($unique != '') {
                        $sinmirData = ['clave' => $unique, 'fila' => $index, 'upp' => $cM->clv_upp, 'ur' => $cM->clv_ur];
                        for ($i = 0; $i < count($metas_temp_Nomir); $i++) {
                            if ($metas_temp_Nomir[$i]['clave'] == $unique) {
                                $repetidas[] = [$metas_temp_Nomir[$i]['fila'], $index];
                            }
                        }
                        $metas_temp_Nomir[] = $sinmirData;
                        $clv_metas_temp_Nomir[] = $unique;
                        $sinmir++;
                    }

                }

                $aux[] = $cM;
                $index++;
            }

        }//FIN DEL FOREACH
        /*VALIDACION REPETIDAS DENTRO DE EL EXCEL */
        log::debug($arrayError);
        $repsmir = array_unique($clv_metas_temp);
        $reps = array_unique($clv_metas_temp_Nomir);
        if (count($repsmir) == $conmir && count($reps) == $sinmir && count($arrayError) <= 0) {
            if ($user->id_grupo == 4) {
                foreach ($aux as $key) {
                    if ($key->clv_upp != $user->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la UPP ' . $user->clv_upp . '.'
                        );
                        $arrayError[] = $error;
                    }
                }
            }
        } else {
            if (count($repetidas) >= 1) {
                $error = array(
                    "icon" => 'info',
                    "title" => 'Cuidado',
                    "text" => 'Metas repetidas en el excel en las filas: ' . implode(",", $repetidas) . '.'
                );

                $arrayError[] = $error;

            }
        }
        if (count($arrayError) >= 1) {
            $success = array(
                "icon" => 'info',
                "title" => 'Cuidado',
                "text" => 'El excel tiene diferentes errores',
                "footer" => '<button type="button" class="btn btn-secondary" onclick="dao.exportEcmExcel()">Errores</button>',
                "arreglo" => $arrayError
            );
            return $success;
        }
        $success = array(
            "icon" => 'success',
            "title" => 'Exito',
            "text" => 'Las metas se registraron correctamente.',
            "arreglo" => json_encode($aux)
        );
        return $success;

    }
}