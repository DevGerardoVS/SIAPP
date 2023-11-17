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

class FunFormatsDel
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function saveImport($filearray)
    {
        if (count($filearray) >= 1) {
            $index = 2;
            $sinmir = 0;
            foreach ($filearray as $k) {
                $status = FunFormatsDel::isNULLOrEmpy($k, $index);
                if ($status['status']) {
                    $error = array(
                        "icon" => 'error',
                        "title" => 'Error',
                        "text" => $status['error']
                    );
                    return $error;
                } else {
                    
                    $anio = DB::table('cierre_ejercicio_metas')->where('clv_upp', '=', strval($k[7]))->where('deleted_at', null)->max('ejercicio');
                        if (strtoupper($k[13]) == 'N/A' && strtoupper($k[14]) == 'N/A') {
                            $error = array(
                                "icon" => 'error',
                                "title" => 'Error',
                                "text" => 'Las dos actividades Ingresadas en la fila : ' . $index . ', son "N/A" tienes que llenar una en N/A y la otra con los datos correspondientes',
                            );
                            return $error;
                        }
                            $clave = '' . strval($k[0]) . '-' . strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]) . '-' . strval($k[4]) . '-' . strval($k[5]) . '-' . strval($k[6]) . '-' . strval($k[7]) . '-' . strval($k[8]) . '-' . strval($k[9]) . '-' . strval($k[10]) . '-' . strval($k[11]) . '';
                            $entidad = '' . strval($k[0]) . '-' . strval($k[1]) . '-' . strval($k[2]) . '-' . strval($k[3]) . '-' . strval($k[4]) . '-' . strval($k[5]) . '-' . strval($k[6]) . '-' . strval($k[9]) . '-' . strval($k[10]) . '-' . strval($k[11]) . '/' . strval($k[7]) . '-' . '0' . '-' . strval($k[8]) . '';

                            $pres = FunFormatsDel::existPP($clave, $anio, $k[12]);
                            if (count($pres)) {
                               
                                        $mir = NULL;
                                        $noMir = $k[13];
                                        $e = FunFormatsDel::isExist($entidad, $k[12], $noMir);
                                        $area_funcional = '' . strval($k[0]) . strval($k[1]) . strval($k[2]) . strval($k[3]) . strval($k[4]) . strval($k[5]) . strval($k[6]) . strval($k[9]) . strval($k[10]) . strval($k[11]) . '';
                                        $entidad_ejecutora = '' . strval($k[7]) . '0' . strval($k[8]) . '';
                                        if ($e["status"]) {

                                            $unique = "";
                                            if(strtoupper($k[13]) != 'N/A' && is_numeric($k[14])){
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Datos incorrectos',
                                                    "text" => 'No modificar la plantilla para su correcto funcionamiento, para dejar un campo sin datos utiliza N/A en la fila : ' . $index
                                                );
                                                return $error;
                                            }
                                            if (strtoupper($k[14]) == 'N/A' && is_numeric($k[13])) {

                                                $unique = $area_funcional . strval($k[12]) . strval($k[13]) . strval($k[8]).'';
                                            }
                                            if(strtoupper($k[14]) != 'N/A'&& is_numeric($k[13])){
                                                $error = array(
                                                    "icon" => 'error',
                                                    "title" => 'Datos incorrectos',
                                                    "text" => 'No modificar la plantilla para su correcto funcionamiento, para dejar un campo sin datos utiliza N/A en la fila : ' . $index
                                                );
                                                return $error;
                                            }
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
                                                    if ($unique != '') {
                                                        $sinmirData = ['clave' => $unique, 'fila' => $index, 'upp' => strval($k[7]),'ur' => strval($k[8])];
                                                        DB::table('metas_temp_Nomir')->insert($sinmirData);
                                                        $sinmir++;
                                                    }
                                    
                                                        if (is_numeric($k[13])) {
                                                            $act = MmlMir::create([
                                                                'clv_upp' => strval($k[7]),
                                                                'entidad_ejecutora' => $entidad_ejecutora,
                                                                'area_funcional' => $area_funcional,
                                                                'id_catalogo' => $k[13],
                                                                'nombre' => null,
                                                                'ejercicio' => $anio,
                                                                'created_user' => auth::user()->username
                                                            ]);
                                                        }
                                                            $aux[] = [
                                                                'pp' => strval($k[11]),
                                                                'upp' => strval($k[7]),
                                                                'meta_id' => $e["id"],
                                                                'clv_fondo' => $k[12],
                                                                'actividad_id' =>$act->id,
                                                                'mir_id' => null,
                                                                'tipo' => 'Acumulativa',
                                                                'beneficiario_id' => 12,
                                                                'unidad_medida_id' => 829,
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
                                                                'tipo_meta'=>"RH",
                                                                'created_user' => auth::user()->username
                                                            ];

                                        } else {
                                            $error = array(
                                                "icon" => 'error',
                                                "title" => 'Error',
                                                "text" => 'La meta ya existe en la fila ' . $index
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
        $reps = DB::table('metas_temp_Nomir')
            ->select(
                DB::raw('COUNT(clave) AS rep'),
                'clave',
                'fila',
                'upp',
            )->groupBy('clave')
            ->get();
        if (count($reps) == $sinmir) {
            foreach ($aux as $key) {
                
                    if (($key['meta_id']) == NULL) {
                     
                        FunFormatsDel::guardarMeta($key);

                    } else {

                        FunFormatsDel::editarMeta($key);

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
                ->groupByRaw('fondo_ramo,ur,finalidad,funcion,subfuncion,eje,programacion_presupuesto.linea_accion,programacion_presupuesto.programa_sectorial,programacion_presupuesto.tipologia_conac,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario')
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

    public static function isExist($entidad, $fondo, $noMir)
    {
        $areaAux = explode('/', $entidad);
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
            $metaSinMir->created_user = $key['created_user']."-CM";
            $metaSinMir->tipo_meta =$key['tipo_meta']; 
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
            $metaSinMir->created_user = $key['created_user']."-CM";
            $metaSinMir->tipo_meta =$key['tipo_meta']; 

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
            $meta->julio = $key->julio;
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