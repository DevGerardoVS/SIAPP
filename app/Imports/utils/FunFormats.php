<?php
namespace App\Imports\utils;

use App\Models\calendarizacion\Metas;
use App\Models\MmlMir;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Mir;
use App\Http\Controllers\Calendarizacion\MetasController;

class FunFormats
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function typeTotal($value, $n)
    {
        $tipo = $value[16];
        $auxTotal = array(
            $value[18],
            $value[19],
            $value[20],
            $value[21],
            $value[22],
            $value[23],
            $value[24],
            $value[25],
            $value[26],
            $value[27],
            $value[28],
            $value[29]
        );
        switch ($tipo) {
            case 0:
                return FunFormats::totalAcum($auxTotal);
            case 1:
                return FunFormats::totalContinua($auxTotal, $n);
            //  return $this->totalAcum($auxTotal);
            case 2:
                return FunFormats::totalEspecial($auxTotal);
            default:
                # code...
                break;
        }
    }
    public static function totalEspecial($arreglo)
    {
        return max($arreglo);
    }
    public static function totalAcum($arreglo)
    {
        $suma = 0;
        for ($i = 0; $i < count($arreglo); $i++) {
            $suma = $suma + $arreglo[$i];
        }
        return $suma;
    }
    public static function totalContinua($arreglo, $n)
    {
        $newa = array_filter($arreglo, function ($var) {
            return $var != 0;
        });
        $newa = array_values($newa);
        $flag = count($newa) == $n ? true : false;
        $newa = array_unique($newa);
        if (count($newa) == 1 && $flag) {
            return $newa[0];
        } else {
            return false;
        }
    }
    public static function saveImport($filearray)
    {
        if (count($filearray) >= 1) {
            $index = 2;
            $conmir = 0;
            $sinmir = 0;
            foreach ($filearray as $k) {
                $status = FunFormats::isNULLOrEmpy($k, $index);

                if ($status['status']) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => $status['error']
                    );
                    return $error;
                } else {
                    //checar si la mir esta confirmada
                    if(Auth::user()->id_grupo==4){
                        if (strval($k[7]) != Auth::user()->clv_upp) {
                            $error = array(
                                "icon" => 'info',
                                "title" => 'Cuidado',
                                "text" => 'Solo puedes registrar metas de la upp ' . Auth::user()->clv_upp. '. Revisa la fila: '. $index
                            );
                            return $error;
                        }
                    }
                    $anio = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', strval($k[7]))->where('deleted_at', null)->max('ejercicio');
                    $isMir = DB::table("mml_cierre_ejercicio")
                        ->select('id', 'estatus')
                        ->where('clv_upp', '=', strval($k[7]))
                        ->where('ejercicio', '=',$anio )
                        ->where('statusm', 1)->get();
                    if (count($isMir)) {
                        $flg = false;
                        if (strtoupper($k[13]) == 'N/A' && strtoupper($k[14]) == 'N/A') {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Las dos actividades Ingresadas en la fila : ' . $index . ', son "N/A" tienes que llenar una en N/A y la otra con los datos correspondientes',
                            );
                            return $error;
                        }
                        if (strtolower($k[13]) == 'ot' && is_numeric($k[14])) {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Las actividades Ingresadas en la fila : ' . $index . ', tienen valores, debes elegir llenar una en N/A y la otra con los datos correspondientes',
                            );
                            return $error;
                        }
                        if (strtoupper($k[13]) != 'N/A' && is_numeric($k[13])) {
                            if (is_numeric($k[13])) {
                                $activ = DB::table('catalogo')->where('ejercicio',  $anio)->where('deleted_at', null)->where('grupo_id', 20)->where('id', $k[13])->get();
                                if ($activ) {

                                    $flg = true;
                                } else {
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Error',
                                        "text" => 'Actividad SIN MIR Ingresada no existe en la fila: ' . $index
                                    );
                                    return $error;

                                }
                            } else {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'Actividad Ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado'
                                );
                                return $error;
                            }
                        }
                        if (strtoupper($k[13]) == 'N/A' && is_numeric($k[14])) {
                            $flg = true;
                        }
                        if (strtolower($k[13]) == 'ot' && strtoupper($k[14]) == 'N/A') {
                            $flg = true;
                        }

                        if (is_numeric($k[14])) {
                            $actividad = Mir::where('deleted_at', null)->where('id', $k[14])->first();
                            if ($actividad) {
                                $flg = true;
                            } else {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'Actividad Ingresada en la fila: ' . $index . ', no es una clave usa los valores del catalogo proporcionado'
                                );
                                return $error;
                            }
                        }
                        if ($flg) {
                            $area = '' . strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[9]) . strval($k[10]) . strval($k[11]) . '';
                            $anio = isset($actividad->ejercicio) ? $actividad->ejercicio : $anio;
                            if (isset($actividad->area_funcional) && strtoupper($k[14]) != 'N/A') {
                                if ($actividad->area_funcional != $area) {
                                    log::debug($actividad->area_funcional);
                                    log::debug($area);
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Error',
                                        "text" => 'El areafuncional no coinciden en las claves presupuestales, en la fila: ' . $index
                                    );
                                    return $error;

                                }
                            }
                            $clave = '' . strval($k[0]) . '-' . strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]) . '-' . strval($k[4]) . '-' . strval($k[5]) . '-' . strval($k[6]) . '-' . strval($k[7]) . '-' . strval($k[8]) . '-' . strval($k[9]) . '-' . strval($k[10]) . '-' . strval($k[11]) . '';
                            $entidad = '' . strval($k[0]) . '-' . strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]) . '-' . strval($k[4]) . '-' . strval($k[5]) . '-' . strval($k[6]) . '-' . strval($k[9]) . '-' . strval($k[10]) . '-' . strval($k[11]) . '/' . strval($k[7]) . '-' . '0' . '-' . strval($k[8]) . '';

                            $pres = FunFormats::existPP($clave, $anio, $k[12]);
                            if (count($pres)) {
                               
                                if (is_string($k[16])) {
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Datos erróneos',
                                        "text" => 'La clave del calendario no coincide con el catálogo usa los datos proporcionados, en la fila: ' . $index
                                    );
                                    return $error;
                                    
                                }else{
                                    if ($k[16]<0 ) {
                                        $error = array(
                                            "icon" => 'error',
                                            "title" => 'Datos erróneos',
                                            "text" => 'La clave del calendario no coincide con el catálogo usa los datos proporcionados, en la fila: ' . $index
                                        );
                                        return $error;
                                    }else if($k[16]>=4){
                                        $error = array(
                                            "icon" => 'error',
                                            "title" => 'Datos erróneos',
                                            "text" => 'La clave del calendario no coincide con el catálogo usa los datos proporcionados, en la fila: ' . $index
                                        );
                                        return $error;
                                    }
                                }
                                $s = FunFormats::validatecalendar($k[7], $k[16]);
                                if ($s["status"]) {
                                    if($k[10]!='UUU'){
                                        $meses = [
                                            "enero" => $k[18],
                                            "febrero" => $k[19],
                                            "marzo" => $k[20],
                                            "abril" => $k[21],
                                            "mayo" => $k[22],
                                            "junio" => $k[23],
                                            "julio" => $k[24],
                                            "agosto" => $k[25],
                                            "septiembre" => $k[26],
                                            "octubre" => $k[27],
                                            "noviembre" => $k[28],
                                            "diciembre" => $k[29],
                                        ];

                                    }else{
                                        $meses = [
                                            "enero"=>2,
                                            "febrero"=>2,
                                            "marzo"=>2,
                                            "abril"=>2,
                                            "mayo"=>2,
                                            "junio"=>2,
                                            "julio"=>2,
                                            "agosto"=>2,
                                            "septiembre"=>2,
                                            "octubre"=>2,
                                            "noviembre"=>2,
                                            "diciembre"=>3,
                                        ];
                                    }
                               
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
                                        return $error;
                                    }

                                    if (!count($mCeros)) {
                                        $error = array(
                                            "icon" => 'error',
                                            "title" => 'Datos incorrectos',
                                            "text" => 'No pueden ir en cero todos los meses y deben ser numeros enteros positivos en la meta de la fila: ' . $index
                                        );
                                        return $error;
                                    }
                                    $m = FunFormats::validateMonth($entidad, json_encode($meses), $anio, $k[12]);
                                    if ($m["status"]) {
                                        $mir = is_numeric($k[14]) ? $k[14] : NULL;
                                        $noMir = $k[13];
                                        $e = FunFormats::isExist($entidad, $k[12], $mir, $noMir);
                                        $area_funcional = '' . strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[9]) . strval($k[10]) . strval($k[11]) . '';
                                        $entidad_ejecutora = '' . strval($k[7]) . '0' . strval($k[8]) . '';
                                        if ($e["status"]) {

                                            $unique = "";
                                            $uniqueMir = "";
                                            if (strtoupper($k[13]) == 'N/A' || is_string($k[13]) && is_numeric($k[14])) {

                                                $uniqueMir = $area_funcional . strval($k[12]) . strval($k[14]) . '';
                                            }
                                            if(strtoupper($k[13]) != 'N/A' && is_numeric($k[14])){
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Datos incorrectos',
                                                    "text" => 'No modificar la plantilla para su correcto funcionamiento, para dejar un campo sin datos utiliza N/A en la fila : ' . $index
                                                );
                                                return $error;
                                            }
                                            if (strtoupper($k[14]) == 'N/A' && is_numeric($k[13])) {

                                                $unique = $area_funcional . strval($k[12]) . strval($k[13]) . '';
                                            }
                                            if(strtoupper($k[14]) != 'N/A'&& is_numeric($k[13])){
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Datos incorrectos',
                                                    "text" => 'No modificar la plantilla para su correcto funcionamiento, para dejar un campo sin datos utiliza N/A en la fila : ' . $index
                                                );
                                                return $error;
                                            }

                                            $medidas = DB::table('unidades_medida')->select('id as clave')->where('deleted_at', null)->where('id', $k[33])->get();

                                            if (count($medidas)) {
                                                $bene = DB::table('beneficiarios')->select('id', 'clave')->where('deleted_at', null)->where('clave', $k[30])->get();
                                                if (count($bene)) {
                                                        if (!is_numeric($k[32])) {
                                                            $error = array(
                                                                "icon" => 'error',
                                                                "title" => 'Datos incorrectos',
                                                                "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                                                            );
                                                            return $error;
                                                           
                                                        }else{
                                                            if($k[32]<=0){
                                                                $error = array(
                                                                    "icon" => 'error',
                                                                    "title" => 'Datos incorrectos',
                                                                    "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                                                                );
                                                                return $error;

                                                            }
                                                        }
                                                    if ($uniqueMir != '') {
                                                        $conmirData = ['clave' => $uniqueMir, 'fila' => $index, 'upp' => strval($k[7])];
                                                        DB::table('metas_temp')->insert($conmirData);
                                                        $conmir++;
                                                    }
                                                    if ($unique != '') {
                                                        $sinmirData = ['clave' => $unique, 'fila' => $index, 'upp' => strval($k[7])];
                                                        DB::table('metas_temp_Nomir')->insert($sinmirData);
                                                        $sinmir++;
                                                    }
                                                  

                                                    $type = FunFormats::typeTotal($k, $m["validos"]);
                                                    if ($type != false) {
                                                        if (is_numeric($k[13])) {
                                                            $act = MmlMir::create([
                                                                'clv_upp' => strval($k[7]),
                                                                'entidad_ejecutora' => $entidad_ejecutora,
                                                                'area_funcional' => $area_funcional,
                                                                'id_catalogo' => $k[13],
                                                                'nombre' => null,
                                                                'ejercicio' => $anio,
                                                                'created_user' => auth::user()->username.'-'.'CM'
                                                            ]);
                                                        }
                                                        if (strtolower($k[13]) == 'ot') {
                                                            $act = MmlMir::create([
                                                                'clv_upp' => strval($k[7]),
                                                                'entidad_ejecutora' => $entidad_ejecutora,
                                                                'area_funcional' => $area_funcional,
                                                                'id_catalogo' => null,
                                                                'nombre' => $k[15],
                                                                'ejercicio' => $anio,
                                                                'created_user' => auth::user()->username.'-'.'CM'
                                                            ]);
                                                        }
                                                        if(strval($k[10])!='UUU'){
                                                            $aux[] = [
                                                                'pp' => strval($k[11]),
                                                                'upp' => strval($k[7]),
                                                                'meta_id' => $e["id"],
                                                                'clv_fondo' => $k[12],
                                                                'actividad_id' => is_numeric($k[14]) ? NULL : $act->id,
                                                                'mir_id' => is_numeric($k[13]) || strtolower($k[13]) == 'ot' ? NULL : $k[14],
                                                                'tipo' => $s['a'],
                                                                'beneficiario_id' => $k[30],
                                                                'unidad_medida_id' => $k[33],
                                                                'cantidad_beneficiarios' => $k[32],
                                                                'enero' => $k[18],
                                                                'febrero' => $k[19],
                                                                'marzo' => $k[20],
                                                                'abril' => $k[21],
                                                                'mayo' => $k[22],
                                                                'junio' => $k[23],
                                                                'julio' => $k[24],
                                                                'agosto' => $k[25],
                                                                'septiembre' => $k[26],
                                                                'octubre' => $k[27],
                                                                'noviembre' => $k[28],
                                                                'diciembre' => $k[29],
                                                                'total' => $type,
                                                                'ejercicio' => $anio,
                                                                'created_user' => auth::user()->username
                                                            ];

                                                        }else{
                                                            $aux[] = [
                                                                'pp' => strval($k[11]),
                                                                'upp' => strval($k[7]),
                                                                'meta_id' => $e["id"],
                                                                'clv_fondo' => $k[12],
                                                                'actividad_id' => is_numeric($k[14]) ? NULL : $act->id,
                                                                'mir_id' => is_numeric($k[13]) || strtolower($k[13]) == 'ot' ? NULL : $k[14],
                                                                'tipo' => $s['a'],
                                                                'beneficiario_id' => $k[30],
                                                                'unidad_medida_id' => $k[33],
                                                                'cantidad_beneficiarios' => $k[32],
                                                                'enero' => 2,
                                                                'febrero' => 2,
                                                                'marzo' => 2,
                                                                'abril' => 2,
                                                                'mayo' => 2,
                                                                'junio' => 2,
                                                                'julio' => 2,
                                                                'agosto' => 2,
                                                                'septiembre' => 2,
                                                                'octubre' => 2,
                                                                'noviembre' => 2,
                                                                'diciembre' => 3,
                                                                'total' => 25,
                                                                'ejercicio' => $anio,
                                                                'created_user' => auth::user()->username
                                                            ];

                                                        }

                                                    } else {
                                                        $error = array(
                                                            "icon" => 'error',
                                                            "title" => 'Tipo de calendario CONTINUO',
                                                            "text" => 'los valores de los meses tienen que ser iguales en la fila ' . $index
                                                        );
                                                        return $error;
                                                    }
                                                } else {
                                                    $error = array(
                                                        "icon" => 'error',
                                                        "title" => 'Error',
                                                        "text" => 'La clave de beneficiario no existe en la fila ' . $index
                                                    );
                                                    return $error;

                                                }
                                            } else {
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Error',
                                                    "text" => 'La unidad de medida no existe en la fila ' . $index
                                                );
                                                return $error;

                                            }

                                        } else {
                                            $error = array(
                                                "icon" => 'error',
                                                "title" => 'Error',
                                                "text" => 'La meta ya existe en la fila ' . $index
                                            );
                                            return $error;

                                        }
                                    } else {
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
                                        return $error;
                                    }
                                } else {
                                    $error = array(
                                        "icon" => 'error',
                                        "title" => 'Error',
                                        "text" => 'El tipo de calendario ' . $s["a"] . ' no esta autorizado para la upp  ' . $s["upp"] . ' en la fila ' . $index
                                    );
                                    return $error;
                                }
                            } else {
                                $error = array(
                                    "icon" => 'error',
                                    "title" => 'Error',
                                    "text" => 'El Proyecto Ingresado no tiene presupuesto en la fila: ' . $index
                                );
                                return $error;
                            }
                        } else {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Actividad Ingresada no existe en la fila: ' . $index
                            );
                            return $error;
                        }
                    } else {
                        $error = array(
                            "icon" => 'error',
                            "title" => 'Error',
                            "text" => 'Los registros de la MIR no estan confirmados en el sistema MML, acércate a CPLADEM'
                        );
                        return $error;
                    }


                }
                $index++;
            }
        } else {
            $error = array(
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
            return $error;
        }

        $repsmir = DB::table('metas_temp')
            ->select(
                DB::raw('COUNT(clave) AS rep'),
                'clave',
                'fila',
                'upp',
            )->groupBy('clave')
            ->get();
        $reps = DB::table('metas_temp_Nomir')
            ->select(
                DB::raw('COUNT(clave) AS rep'),
                'clave',
                'fila',
                'upp',
            )->groupBy('clave')
            ->get();
        if (count($repsmir) == $conmir && count($reps) == $sinmir) {
            if (Auth::user()->id_grupo == 4) {
                foreach ($repsmir as $key) {
                    if ($key->upp != Auth::user()->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la upp ' . Auth::user()->clv_upp
                        );
                        return $error;
                    }
                }
                foreach ($reps as $key) {
                    if ($key->upp != Auth::user()->clv_upp) {
                        $error = array(
                            "icon" => 'info',
                            "title" => 'Cuidado',
                            "text" => 'Solo puedes registrar metas de la upp ' . Auth::user()->clv_upp
                        );
                        return $error;
                    }
                }

            }
            foreach ($aux as $key) {
                try {
                    if (($key['meta_id']) == NULL) {
                        FunFormats::guardarMeta($key);

                    } else {

                        FunFormats::editarMeta($key);

                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }

            }

            $success = array(
                "icon" => 'success',
                "title" => 'Exito',
                "text" => 'Las metas se registraron correctamente'
            );
            return $success;

        } else {
            $filas = [];
            foreach ($repsmir as $key) {
                if ($key->rep > 1) {
                    $reps = DB::table('metas_temp')->select('clave', 'fila')->where('clave', $key->clave)->get();
                    foreach ($reps as $a) {
                        $filas[] = $a->fila;
                    }
                }
            }
            foreach ($reps as $key) {
                if ($key->rep > 1) {
                    $r = DB::table('metas_temp_Nomir')->select('clave', 'fila')->where('clave', $key->clave)->get();
                    foreach ($r as $a) {
                        $filas[] = $a->fila;
                    }
                }
            }
            $f = implode(", ", $filas);
            $error = array(
                "icon" => 'info',
                "title" => 'Cuidado',
                "text" => 'Existen registros repetidos en el excel: ' . $f
            );
            return $error;

        }

    }
    public static function isNULLOrEmpy($datos, $index)
    {
        for ($i = 0; $i < count($datos); $i++) {
            if (count($datos) === 35) {
                if ($datos[$i] == '' || $datos[$i] == ' ') {
                    return ["status" => true, "error" => 'El documento contiene campos vacios en la columna:' . FunFormats::abc($i) . ' fila:' . $index . ''];
                }
            } else {

                return ["status" => true, "error" => 'No se debe modificar la plantilla'];
                ;
            }
        }
        return ["status" => false, "error" => null];
    }
    public static function abc($i)
    {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
        return $columns[$i];
    }
    public static function existPP($clave, $anio, $fondo)
    {

        $arrayclave = explode('-', $clave);
        try {
            $activs = DB::table('programacion_presupuesto')
                ->select(
                    'id',
                    'upp',
                )
                ->where('deleted_at', null)
                ->where('finalidad', $arrayclave[0])
                ->where('funcion', $arrayclave[1])
                ->where('subfuncion', $arrayclave[2])
                ->where('eje', $arrayclave[3])
                ->where('linea_accion', $arrayclave[4])
                ->where('programa_sectorial', $arrayclave[5])
                ->where('tipologia_conac', $arrayclave[6])
                ->where('upp', $arrayclave[7])
                ->where('ur', $arrayclave[8])
                ->where('programa_presupuestario', $arrayclave[9])
                ->where('subprograma_presupuestario', $arrayclave[10])
                ->where('proyecto_presupuestario', $arrayclave[11])
                ->where('fondo_ramo', $fondo)
                ->where('programacion_presupuesto.ejercicio', '=', $anio)
                ->where('programacion_presupuesto.estado', '=', 1)
                ->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
                ->distinct()
                ->get();
            return $activs;


        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public static function validateMonth($clave, $m, $anio, $fondo)
    {
        $meses = json_decode($m);
        $areaAux = explode('/', $clave);
        $m = MetasController::meses($areaAux[0], $areaAux[1], $anio, $fondo);
        $arrM = [];
        $arrMV = [];
        $mesCero = [];
        $mesesV = 0;
        foreach ($m as $key => $value) {
            $e = $value;

            switch ($key) {
                case 'enero':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->enero != 0) {
                            $arrM[] = "enero";
                        }

                    } else {
                        if ($meses->enero <= 0) {
                            $mesCero[] = "ENERO";
                        }
                        $mesesV++;
                        $arrMV[] = "ENERO";
                    }
                    break;
                case 'febrero':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->febrero != 0) {
                            $arrM[] = "febrero";
                        }
                    } else {
                        if ($meses->febrero <= 0) {
                            $mesCero[] = "FEBRERO";
                        }

                        $mesesV++;
                        $arrMV[] = "FEBRERO";
                    }
                    break;
                case 'marzo':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->marzo != 0) {
                            $arrM[] = "marzo";
                        }
                    } else {
                        if ($meses->marzo <= 0) {
                            $mesCero[] = "MARZO";
                        }
                        $mesesV++;
                        $arrMV[] = "MARZO";
                    }
                    break;
                case 'abril':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->abril != 0) {
                            $arrM[] = "abril";
                        }


                    } else {
                        if ($meses->abril <= 0) {
                            $mesCero[] = "ABRIL";
                        }
                        $mesesV++;
                        $arrMV[] = "ABRIL";
                    }
                    break;
                case 'mayo':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->mayo != 0) {
                            $arrM[] = "mayo";
                        }
                    } else {
                        if ($meses->mayo <= 0) {
                            $mesCero[] = "MAYO";
                        }
                        $mesesV++;
                        $arrMV[] = "MAYO";
                    }
                    break;
                case 'junio':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->junio != 0) {
                            $arrM[] = "junio";
                        }
                    } else {
                        if ($meses->junio <= 0) {
                            $mesCero[] = "JUNIO";
                        }
                        $mesesV++;
                        $arrMV[] = "JUNIO";
                    }
                    break;
                case 'julio':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->julio != 0) {
                            $arrM[] = "julio";
                        }
                    } else {
                        if ($meses->julio <= 0) {
                            $mesCero[] = "JULIO";
                        }
                        $mesesV++;
                        $arrMV[] = "JULIO";
                    }
                    break;
                case 'agosto':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->agosto != 0) {
                            $arrM[] = "agosto";
                        }

                    } else {
                        if ($meses->agosto <= 0) {
                            $mesCero[] = "AGOSTO";
                        }
                        $mesesV++;
                        $arrMV[] = "AGOSTO";
                    }
                    break;
                case 'septiembre':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->septiembre != 0) {
                            $arrM[] = "septiembre";
                        }
                    } else {
                        if ($meses->septiembre <= 0) {
                            $mesCero[] = "SEPTIEMBRE";
                        }

                        $mesesV++;
                        $arrMV[] = "SEPTIEMBRE";
                    }
                    break;
                case 'octubre':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->octubre != 0) {
                            $arrM[] = "octubre";
                        }
                    } else {
                        if ($meses->octubre <= 0) {
                            $mesCero[] = "OCTUBRE";
                        }

                        $mesesV++;
                        $arrMV[] = "OCTUBRE";
                    }
                    break;

                case 'noviembre':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->noviembre != 0) {
                            $arrM[] = "noviembre";
                        }
                    } else {
                        if ($meses->noviembre <= 0) {
                            $mesCero[] = "NOVIEMBRE";
                        }
                        $mesesV++;
                        $arrMV[] = "NOVIEMBRE";
                    }
                    break;

                case 'diciembre':
                    if ($e == 0.0 || $e == 0) {
                        if ($meses->diciembre != 0) {
                            $arrM[] = "diciembre";
                        }
                    } else {
                        if ($meses->diciembre <= 0) {
                            $mesCero[] = "DICIEMBRE";
                        }
                        $mesesV++;
                        $arrMV[] = "DICIEMBRE";
                    }

                default:
                    break;
            }

        }
        if (count($arrM) == 0 && count($mesCero) == 0) {
            return ["status" => true, "validos" => $mesesV];
        } else {
            return ["status" => false, "errorM" => $arrM, "mV" => $arrMV, "mesCero" => $mesCero];
        }


    }
    public static function validatecalendar($upp, $act)
    {
        $tipo = MetasController::getTcalendar($upp);
        switch ($act) {
            case 0:
                if ($tipo->Acumulativa != 1) {
                    return ["status" => false, "a" => 'Acumulativa', "upp" => $upp];
                } else {
                    return ["status" => true, "a" => 'Acumulativa', "upp" => $upp];
                }
            case 1:
                if ($tipo->Continua != 1) {
                    return ["status" => false, "a" => 'Continua', "upp" => $upp];
                } else {
                    return ["status" => true, "a" => 'Continua', "upp" => $upp];
                }
            case 2:
                if ($tipo->Especial != 1) {
                    return ["status" => false, "a" => 'Especial', "upp" => $upp];
                } else {
                    return ["status" => true, "a" => 'Especial', "upp" => $upp];
                }
            default:
                # code...
                break;
        }

    }
    public static function isExist($entidad, $fondo, $mir, $noMir)
    {
        $areaAux = explode('/', $entidad);
        if ($mir != NULL) {
            $metas = DB::table('metas')
                ->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
                ->select(
                    'metas.id',
                    'mml_mir.entidad_ejecutora',
                    'mml_mir.area_funcional',
                    'mml_mir.clv_upp'
                )
                ->where('mml_mir.area_funcional', str_replace("-", '', $areaAux[0]))
                ->where('mml_mir.entidad_ejecutora', str_replace("-", '', $areaAux[1]))
                ->where('metas.clv_fondo', $fondo)
                ->where('metas.mir_id', $mir)
                ->where('mml_mir.deleted_at', null)
                ->where('metas.actividad_id', null)
                ->where('metas.deleted_at', null)->get();
        }
        if (is_numeric($noMir)) {
            $metas = DB::table('metas')
                ->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
                ->select(
                    'metas.id',
                    'mml_actividades.entidad_ejecutora',
                    'mml_actividades.area_funcional',
                    'mml_actividades.clv_upp'
                )
                ->where('mml_actividades.area_funcional', str_replace("-", '', $areaAux[0]))
                ->where('mml_actividades.entidad_ejecutora', str_replace("-", '', $areaAux[1]))
                ->where('metas.clv_fondo', $fondo)
                ->where('mml_actividades.id_catalogo', $noMir)
                ->where('metas.mir_id', null)
                ->where('mml_actividades.deleted_at', null)
                ->where('metas.deleted_at', null)->get();

        }
        if ($noMir == 'ot') {
            $metas = [];
        }
        if (count($metas) == 0) {
            return ["status" => true, "id" => null];
        } else {
            return ["status" => false, "id" => $metas[0]->id];
        }

    }

    public static function guardarMeta($key)
    {
        if (is_numeric($key['actividad_id']) && $key['mir_id'] == NULL) {

            $metaSinMir = new Metas;
            $metaSinMir->mir_id = NULL;
            $metaSinMir->clv_fondo = $key['clv_fondo'];
            $metaSinMir->actividad_id = $key['actividad_id'];
            $metaSinMir->tipo = $key['tipo'];
            $metaSinMir->beneficiario_id = $key['beneficiario_id'];
            $metaSinMir->unidad_medida_id = $key['unidad_medida_id'];
            $metaSinMir->cantidad_beneficiarios = $key['cantidad_beneficiarios'];
            $metaSinMir->enero = $key['enero'];
            $metaSinMir->febrero = $key['febrero'];
            $metaSinMir->marzo = $key['marzo'];
            $metaSinMir->abril = $key['abril'];
            $metaSinMir->mayo = $key['mayo'];
            $metaSinMir->junio = $key['junio'];
            $metaSinMir->julio = $key['julio'];
            $metaSinMir->agosto = $key['agosto'];
            $metaSinMir->septiembre = $key['septiembre'];
            $metaSinMir->octubre = $key['octubre'];
            $metaSinMir->noviembre = $key['noviembre'];
            $metaSinMir->diciembre = $key['diciembre'];
            $metaSinMir->total = $key['total'];
            $metaSinMir->estatus = 0;
            $metaSinMir->ejercicio = $key['ejercicio'];
            $metaSinMir->created_user = $key['created_user'].'-'.'CM';
            $metaSinMir->save();
            if ($metaSinMir) {
                $metaSinMir->clv_actividad = "" . $key['upp'] . '-' . $key['pp'] . '-' . $metaSinMir->id . '-' . $key['ejercicio'];
                $metaSinMir->save();
            }
        }

        if (is_numeric($key['mir_id'])) {
            $metaConMir = new Metas;
            $metaConMir->actividad_id = NULL;
            $metaConMir->mir_id = $key['mir_id'];
            $metaConMir->clv_fondo = $key['clv_fondo'];
            $metaConMir->tipo = $key['tipo'];
            $metaConMir->beneficiario_id = $key['beneficiario_id'];
            $metaConMir->unidad_medida_id = $key['unidad_medida_id'];
            $metaConMir->cantidad_beneficiarios = $key['cantidad_beneficiarios'];
            $metaConMir->enero = $key['enero'];
            $metaConMir->febrero = $key['febrero'];
            $metaConMir->marzo = $key['marzo'];
            $metaConMir->abril = $key['abril'];
            $metaConMir->mayo = $key['mayo'];
            $metaConMir->junio = $key['junio'];
            $metaConMir->julio = $key['julio'];
            $metaConMir->agosto = $key['agosto'];
            $metaConMir->septiembre = $key['septiembre'];
            $metaConMir->octubre = $key['octubre'];
            $metaConMir->noviembre = $key['noviembre'];
            $metaConMir->diciembre = $key['diciembre'];
            $metaConMir->total = $key['total'];
            $metaConMir->estatus = 0;
            $metaConMir->ejercicio = $key['ejercicio'];
            $metaConMir->created_user = $key['created_user'].'-'.'CM';
            $metaConMir->save();
            if ($metaConMir) {
                $metaConMir->clv_actividad = "" . $key['upp'] . '-' . $key['pp'] . '-' . $metaConMir->id . '-' . $key['ejercicio'];
                $metaConMir->save();
            }
        }

    }
    public static function editarMeta($key)
    {
        $meta = Metas::where('id', $key->meta_id)->firstOrFail();
        $fecha = Carbon::now()->toDateTimeString();
        if ($meta) {
            $meta->tipo = $key->tipo;
            $meta->beneficiario_id = $key->beneficiario_id;
            $meta->unidad_medida_id = $key->unidad_medida_id;
            $meta->cantidad_beneficiarios = $key->cantidad_beneficiarios;
            $meta->total = $key->total;
            $meta->enero = $key->enero;
            $meta->febrero = $key->febrero;
            $meta->marzo = $key->marzo;
            $meta->abril = $key->abril;
            $meta->mayo = $key->mayo;
            $meta->junio = $key->junio;
            $meta->julio = $key->julio;;
            $meta->agosto = $key->agosto;
            $meta->septiembre = $key->septiembre;
            $meta->octubre = $key->octubre;
            $meta->noviembre = $key->noviembre;
            $meta->diciembre = $key->diciembre;
            $meta->updated_at = $fecha;
            $meta->updated_user = auth::user()->username;
            $meta->save();

        }
    }

}