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
use App\Models\Mir;
use App\Http\Controllers\Calendarizacion\MetasController;
use App\Helpers\Calendarizacion\MetasCmHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

                            $pres = FunFormats::existPP($clave);
                            if (count($pres)) {
                                $tipoCalendario = strval($k[16]);

                                switch ($tipoCalendario) {
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
                                        return $error;
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
                                   
                                    $m = FunFormats::validateMonth($entidad, json_encode($meses), $anio);
                                    if ($m["status"]) {

                                        $mir = is_numeric($k[14]) ? $k[14] : NULL;
                                        $noMir = $k[13];
                                        $e = FunFormats::isExist($entidad, $k[12]);
                                        $area_funcional = '' . strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[9]) . strval($k[10]) . strval($k[11]) . '';
                                        $entidad_ejecutora = '' . strval($k[7]) . '0' . strval($k[8]) . '';
                                        if ($e["status"]) {

                                            $unique = "";
                                            $uniqueMir = "";
                                            if (strtoupper($k[13]) == 'N/A' || is_string($k[13]) && is_numeric($k[14])) {

                                                $uniqueMir = $area_funcional . strval($k[12]) . strval($k[14]) . strval($k[8]) . '';
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

                                                $unique = $area_funcional . strval($k[12]) . strval($k[13]) . strval($k[8]) . '';
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
                                            if (!count($medidas)) {
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Error',
                                                    "text" => 'La unidad de medida no existe en la fila ' . $index
                                                );
                                                return $error;

                                            }
                                                $bene = DB::table('beneficiarios')->select('id', 'clave')->where('deleted_at', null)->where('clave', $k[30])->get();
                                            if (count($bene)) {
                                                if (!is_numeric($k[32])) {
                                                    $error = array(
                                                        "icon" => 'error',
                                                        "title" => 'Datos incorrectos',
                                                        "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                                                    );
                                                    return $error;

                                                } else {
                                                    if ($k[32] <= 0) {
                                                        $error = array(
                                                            "icon" => 'error',
                                                            "title" => 'Datos incorrectos',
                                                            "text" => 'El numero de beneficiarios debe ser un NUMERO mayor a 0 en la fila: ' . $index
                                                        );
                                                        return $error;

                                                    }
                                                }
                                            } else {
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Error',
                                                    "text" => 'La clave de beneficiario no existe en la fila ' . $index
                                                );
                                                return $error;

                                            }
                                                    if ($uniqueMir != '') {
                                                        $conmirData = ['clave' => $uniqueMir, 'fila' => $index, 'upp' => strval($k[7]),"ur"=> strval($k[8])];
                                                        DB::table('metas_temp')->insert($conmirData);
                                                        $conmir++;
                                                    }
                                                    if ($unique != '') {
                                                        $sinmirData = ['clave' => $unique, 'fila' => $index, 'upp' => strval($k[7]),'ur'=> strval($k[8])];
                                                        DB::table('metas_temp_Nomir')->insert($sinmirData);
                                                        $sinmir++;
                                                    }
                                                  
                                                    $type = FunFormats::typeTotal($k, $m["validos"]);
                                                    if ($type != false) {
                                                        if (is_numeric($k[13])) {
                                                            $id_catalogo=$k[13];
                                                            $nombre=null;
                                                            $act = FunFormats::createMml_Ac($k);
                                                        }
                                                        if (strtolower($k[13]) == 'ot') {
                                                            $id_catalogo=null;
                                                            $nombre=$k[15];
                                                            $act = FunFormats::createMml_Ac($k);
                                                        }
                                                        if(strval($k[10])!='UUU'){
                                                            $aux[] = [
                                                                'pp' => strval($k[11]),
                                                                'upp' => strval($k[7]),
                                                                'meta_id' => $e["id"],
                                                                'clv_fondo' => $k[12],
                                                                'actividad_id' => is_numeric($k[14]) ? NULL : $act,
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
                                                                'actividad_id' => is_numeric($k[14]) ? NULL : $act,
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
        $columnas = [];
        $aux = 0;
        if (count($datos) != 35) {
                return ["status" => true, "error" => 'No se debe modificar la plantilla, columnas irregulares en la fila: '.$index];
            }
        for ($i = 0; $i <= 34; $i++) {
                if ($datos[$i] == '' || $datos[$i] == ' ') {
                    $columnas[] = FunFormats::abc($i);
                }
        }
        if(count($columnas)>=1){
            return ["status" => true, "error" => 'El documento contiene campos vacios en la columna:' .implode(",", $columnas) . ' fila:' . $index . ''];
        }else{
            return ["status" => false, "error" => null];
        }
        
    }
    public static function abc($i)
    {
        $columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'];
        return$columns[$i];
    }
    public static function existPP($obj)
    {
        Log::debug(json_encode($obj));
        try {
            $activs = DB::table('programacion_presupuesto')
                ->select(
                    'id',
                    'upp',
                )
                ->where('deleted_at', null)
                ->where('finalidad', $obj->finalidad)
                ->where('funcion', $obj->funcion)
                ->where('subfuncion', $obj->subfuncion)
                ->where('eje', $obj->eje)
                ->where('linea_accion', $obj->linea_accion)
                ->where('programa_sectorial', $obj->pro7grama_sectorial)
                ->where('tipologia_conac', $obj->tipologia_conac)
                ->where('upp', $obj->clv_upp)
                ->where('ur', $obj->clv_ur)
                ->where('programa_presupuestario', $obj->programa_presupuestario)
                ->where('subprograma_presupuestario', $obj->subprograma_presupuestario)
                ->where('proyecto_presupuestario', $obj->proyecto_presupuestario)
                ->where('fondo_ramo',  $obj->clv_fondo)
                ->where('programacion_presupuesto.ejercicio', '=', $obj->ejercicio)
                ->where('programacion_presupuesto.estado', '=', 1)
                ->groupByRaw('finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
                ->distinct()
                ->get();
                Log::debug(json_encode($$activs));
            return $activs;


        } catch (\Throwable $th) {
            throw $th;
        }

    }
    public static function existSapp($obj)
    {
        try {
            
            Log::debug($obj->clv_upp."-". $obj->clv_ur."-".$obj->area_funcional."-".$obj->clv_fondo);
            $activs = DB::table('sapp_movimientos')
                ->select(
                    'id',
                    DB::raw('SUBSTRING(centro_gestor, 11, 6) AS entidad')
                )
                ->where([
                'clv_upp'=> $obj->clv_upp,
                'clv_ur'=> $obj->clv_ur,
                'area_funcional'=> $obj->area_funcional
                ])
               ->where(DB::raw('substr(fondo, 7, 2)'), '=', strval($obj->clv_fondo) )
                ->where('ejercido_cp', '>=',0.01)
                ->get();
            return $activs;
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug( $th);
        }
       
    }
    public static function existSapp_Seguimiento($obj)
    {
        try {
            
            $activs = DB::table('sapp_seguimiento')
                ->select('*')
                ->where([
                'clv_upp'=> $obj->clv_upp,
                'clv_ur'=> $obj->clv_ur,
                'clv_programa'=> $obj->clv_pp,
                'clv_subprograma'=> $obj->subprograma_presupuestario,
                'clv_proyecto'=>$obj->proyecto_presupuestario,
                'estatus'=>1,
                'deleted_at'=>NULL
                ])
                ->get();
            return $activs;
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug( $th);
        }
       
    }

    public static function validateMonth($obj,$m, $anio)
    {         
        Log::debug('validateMonth');
        $meses = json_decode($m);
        $perfil=$obj->id_grupo == 4 ? false : true;
        $m = MetasCmHelper::movimientoMeses($obj->area_funcional,$obj->clv_upp,$obj->clv_ur,$obj->clv_fondo,$anio,$perfil);
        $arrM = [];
        $arrMV = [];
        $mesCero = [];
        $mesesV = 0;
        foreach ($m as $key => $value) {
            $e = intval( $value );
            Log::debug($e);
            switch ($key) {
                case 'enero':
                    if ($e === 0.0 || $e === 0) {
                        Log::debug("enero: ".$meses->enero);
                        if ($meses->enero !== 0) {
                            $arrM[] = "enero";
                        }

                    } else {
                        $mesesV++;
                        $arrMV[] = "ENERO";
                    }
                    break;
                case 'febrero':
                    if ($e === 0.0 || $e === 0) {
                        Log::debug("febrero: ".$meses->febrero);
                        if ($meses->febrero !== 0) {
                            $arrM[] = "febrero";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "FEBRERO";
                    }
                    break;
                case 'marzo':
                    if ($e === 0.0 || $e === 0) {
                        Log::debug("marzo: ".$meses->marzo);
                        if ($meses->marzo !== 0) {
                            $arrM[] = "marzo";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "MARZO";
                    }
                    break;
                case 'abril':
                    if ($e === 0.0 || $e === 0) {
                        Log::debug("abril: ".$meses->abril);
                        if ($meses->abril !== 0) {
                            $arrM[] = "abril";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "ABRIL";
                    }
                    break;
                case 'mayo':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->mayo !== 0) {
                            $arrM[] = "mayo";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "MAYO";
                    }
                    break;
                case 'junio':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->junio !== 0) {
                            $arrM[] = "junio";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "JUNIO";
                    }
                    break;
                case 'julio':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->julio !== 0) {
                            $arrM[] = "julio";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "JULIO";
                    }
                    break;
                case 'agosto':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->agosto !== 0) {
                            $arrM[] = "agosto";
                        }

                    } else {
                        $mesesV++;
                        $arrMV[] = "AGOSTO";
                    }
                    break;
                case 'septiembre':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->septiembre !== 0) {
                            $arrM[] = "septiembre";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "SEPTIEMBRE";
                    }
                    break;
                case 'octubre':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->octubre !== 0) {
                            $arrM[] = "octubre";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "OCTUBRE";
                    }
                    break;

                case 'noviembre':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->noviembre !== 0) {
                            $arrM[] = "noviembre";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "NOVIEMBRE";
                    }
                    break;

                case 'diciembre':
                    if ($e === 0.0 || $e === 0) {
                        if ($meses->diciembre !== 0) {
                            $arrM[] = "diciembre";
                        }
                    } else {
                        $mesesV++;
                        $arrMV[] = "DICIEMBRE";
                    }

                default:
                    break;
            }

        }
        if (count($arrM) == 0 && count($mesCero) == 0) {
            Log::debug('true');
            return ["status" => true, "validos" => $mesesV];
        } else {
            Log::debug('false');
            return ["status" => false, "errorM" => $arrM, "mV" => $arrMV, "mesCero" => $mesCero,"validos" => $mesesV ];
        }


    }
    public static function validatecalendar($upp, $act)
    {
        $tipo = MetasController::getTcalendar($upp);
        Log::debug(json_encode($tipo));
        if(!$tipo){
            return ["status" => false, "a" => 'Acumulativa', "upp" => $upp, "mensaje"=>'La upp:'.$upp.' no se encuentra en los registros: tipo actividad upp'];
        }
        switch (intval($act)) {
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
    public static function isExist($obj,$anio)
    {
        Log::debug($obj->tipoMeta);
        Log::debug($obj->area_funcional.$obj->clv_upp.$obj->clv_ur.$obj->clv_fondo);

        switch ($obj->tipoMeta) {
            case 'M':
                $metas = DB::table('metas')
                ->leftJoin('mml_mir', 'mml_mir.id', 'metas.mir_id')
                ->select(
                    'metas.id',
                    'mml_mir.area_funcional',
                    'mml_mir.clv_upp'
                )
                ->where('mml_mir.area_funcional', $obj->area_funcional)
                ->where('mml_mir.clv_upp', $obj->clv_upp)
                ->where('mml_mir.clv_ur', $obj->clv_ur)
                ->where('metas.clv_fondo', $obj->clv_fondo)
                ->where('metas.mir_id', $obj->mir_id)
                ->where('metas.ejercicio', $obj->ejercicio)
                ->where('mml_mir.deleted_at', null)
                ->where('metas.actividad_id', null)
                ->where('metas.deleted_at', null)->get();
                Log::debug( $metas);
                break;
            case 'O':
                $metas = DB::table('metas')
                ->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
                ->select(
                    'metas.id',
                    'mml_actividades.entidad_ejecutora',
                    'mml_actividades.area_funcional',
                    'mml_actividades.clv_upp',
                    'mml_actividades.id'
                    
                )
                ->where('mml_actividades.area_funcional', $obj->area_funcional)
                ->where('mml_actividades.clv_upp', $obj->clv_upp)
                ->where('mml_actividades.clv_ur', $obj->clv_ur)
                ->where('metas.clv_fondo', $obj->clv_fondo)
                ->where('metas.ejercicio', $obj->ejercicio)
                ->where('mml_actividades.id_catalogo', null)
                ->where('metas.mir_id', null)
                ->where('mml_actividades.deleted_at', null)
                ->where('metas.deleted_at', null)->get();
                Log::debug( $metas);


                break;
            case 'C':
                $metas = DB::table('metas')
                ->leftJoin('mml_actividades', 'mml_actividades.id', 'metas.actividad_id')
                ->select(
                    'metas.id',
                    'mml_actividades.entidad_ejecutora',
                    'mml_actividades.area_funcional',
                    'mml_actividades.clv_upp'
                )
                ->where('mml_actividades.area_funcional', $obj->area_funcional)
                ->where('mml_actividades.clv_upp', $obj->clv_upp)
                ->where('mml_actividades.clv_ur', $obj->clv_ur)
                ->where('metas.clv_fondo', $obj->clv_fondo)
                ->where('mml_actividades.id_catalogo', $obj->actividad_id)
                ->where('metas.ejercicio', $obj->ejercicio)
                ->where('metas.mir_id', null)
                ->where('mml_actividades.deleted_at', null)
                ->where('metas.deleted_at', null)->get();
                Log::debug( $metas);
                break;
        }
        if (count($metas)) {
            return ["status" => true, "id" => $metas[0]->id];
           
        } else {
            return ["status" => false, "id" => null];
        }

    }

    public static function guardarMeta($key)
    {
        Log::debug('ENTRANDO A FUNCION guardarMeta');
        $meta = isset($key->id_met)? Metas::find($key->id_met):new Metas;   
        $meta->clv_fondo = $key->clv_fondo;
        $meta->tipo = $key->tipo;
        $meta->beneficiario_id = $key->beneficiario_id;
        $meta->unidad_medida_id = $key->unidad_medida_id;
        $meta->cantidad_beneficiarios = $key->cantidad_beneficiarios;
        $meta->enero = $key->enero;
        $meta->febrero = $key->febrero;
        $meta->marzo = $key->marzo;
        $meta->abril = $key->abril;
        $meta->mayo = $key->mayo;
        $meta->junio = $key->junio;
        $meta->julio = $key->julio;
        $meta->agosto = $key->agosto;
        $meta->septiembre = $key->septiembre;
        $meta->octubre = $key->octubre;
        $meta->noviembre = $key->noviembre;
        $meta->diciembre = $key->diciembre;
        $meta->total = $key->total;
        $meta->estatus = 0;
        $meta->ejercicio = $key->ejercicio;
        $meta->created_user = $key->created_user.'-'.'CM';
        $meta->tipo_meta = "Operativo";
        $meta->save();

        if ($meta) {
            $meta->clv_actividad = strval($key->clv_upp . '-' . $key->clv_ur . '-' .$key->area_funcional .'-'.$key->clv_fondo.'-'.$meta->id . '-' . $key->ejercicio);
            switch ($key->tipoMeta) {
                case 'M':
                    $meta->mir_id = $key->mir_id;
                    $meta->actividad_id = NULL;
                    break;
                case 'O':
                    $meta->mir_id = NULL;
                    $meta->actividad_id = $key->actividad_id;
                    
                    break;
                case 'C':
                    $meta->mir_id = NULL;
                    $meta->actividad_id = $key->actividad_id;
    
                    break;
            }
           
        }
        
        $meta->save();

    }
    public static function createMml_Ac($obj)
	{
		$mml_act = new MmlActividades();
		$mml_act->clv_upp = $obj->clv_upp;
		$mml_act->clv_ur = $obj->clv_ur;
		$mml_act->clv_pp = $obj->clv_pp;
		$mml_act->entidad_ejecutora =$obj->entidad_ejecutora;
		$mml_act->area_funcional = $obj->area_funcional;
		$mml_act->id_catalogo = $obj->actividad_id;
        $mml_act->nombre =$obj->nombre_actividad;
		$mml_act->ejercicio = $obj->ejercicio;
        $mml_act->created_user = $obj->created_user. '- CM';
        Log::debug($mml_act );
		$mml_act->save();
	
		return $mml_act->id;
	}
    public static function validateExcel($obj)
	{
        $index = 2;
        $errors = [];
        Log::debug('dentro de validateExcel');
        Log::debug(count($obj));
		if (count($obj) <= 0) {
            $error = array(
                "status" => false,
                "icon" => 'error',
                "title" => 'Error',
                "text" => 'El documento esta vacio'
            );
            Log::debug('El documento esta vacio');
            return $error;
            
        } else {
            foreach ($obj as $k) {
                $status = FunFormats::isNULLOrEmpy($k, $index);
                if ($status['status']) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => $status['error']
                    );
                    $errors[] = $error;
                }
            }
        }
            if (count($errors) >= 1) {
                $usr = Auth::user()->username;
                $tableName = 'erroresExcelTemp' . $usr;
                Schema::create($tableName, function (Blueprint $table) {
                    $table->temporary();
                    $table->increments('id');
                    $table->string('error', 255)->nullable(false);
                });
                $obj = (object) $errors;
                Log::debug('foreach');
                foreach ($obj as $key) {
                    DB::table($tableName)->insert(["error" =>$key['text']]);
                }
                Log::debug('return');
                $errorRes = [
                    "status" => false,
                    "icon" => "error",
                    "title" => "Error",
                    "text" => "Revisar las instrucciones de la Carga Masiva",
                    "footer" => "<a href='/CargaMasiva/Errores/'" . $tableName . "'>Descargar errores</a>"
                ];
                return $errorRes;

            }else{
                $errorRes = [
                    "status" => true,
                ];
            }
	}




}