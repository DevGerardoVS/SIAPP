<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Exports\ImportErrorsExport;
use App\Http\Controllers\Controller;
use App\Imports\ClavePresupuestaria;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\TechosFinancieros;
use App\Models\cierreEjercicio;
use App\Exports\PlantillaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\uppautorizadascpnomina;


use Auth;
use Shuchkin\SimpleXLSX;
use App\Models\ProgramacionPresupuesto;

use DB;

class CalendarizacionCargaMasivaController extends Controller
{
    //Obtener plantilla para descargar
    public function getExcel(Request $request)
    {
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descarga',
            "modulo" => 'Plantilla carga masiva'
        );
        Controller::bitacora($b);
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();


        return Excel::download(new PlantillaExport, 'Plantilla.xlsx');


    }


    public function DownloadErrors($fails){

      $fails=  json_decode($fails);
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descarga',
            "modulo" => 'Errores carga masiva'
        );
        Controller::bitacora($b);
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();


        return Excel::download(new ImportErrorsExport($fails), 'Errores.xlsx');
    }

    //Obtener datos del excel
    public function loadDataPlantilla(Request $request)
    {
        $request->tipo ? $tipoAdm = $request->tipo : $tipoAdm = NULL;
        $uppUsuario = auth::user()->clv_upp;
        $message = [
            'file' => 'El archivo debe ser tipo xlsx'
        ];
        $arrayCampos = array(
            'admconac',
            'ef',
            'reg',
            'mpio',
            'loc',
            'upp',
            'subsecretaria',
            'ur',
            'finalidad',
            'funcion',
            'subfuncion',
            'eg',
            'pt',
            'ps',
            'sprconac',
            'prg',
            'spr',
            'py',
            'idpartida',
            'tipogasto',
            'año',
            'no etiquetado y etiquetado',
            'fconac',
            'ramo',
            'fondo',
            'ci',
            'obra',
            'total',
            'enero',
            'febrero',
            'marzo',
            'abril',
            'mayo',
            'junio',
            'julio',
            'agosto',
            'septiembre',
            'octubre',
            'noviembre',
            'diciembre'
        );
         $request->validate([
            'file' => 'required|mimes:xlsx'
        ], $message); 

        ini_set('max_execution_time', 1200);

        //verificar que el usuario tenga permiso
        try {
            //Validaciones para administrador
            if ($tipoAdm != NULL) {

                $arrayupps = array();
                $arraypresupuesto = array();
                $countO = 0;
                $countR = 0;
                if ($xlsx = SimpleXLSX::parse($request->file)) {
                    $filearray = $xlsx->rows();
                    //tomamos los encabezados
                    $encabezados = array_shift($filearray);
                    //Los convertimos todos a lowecase
                    $encabezadosMin = array_map('strtolower', $encabezados);
                    //Verificamos si hay diferencia entre lo que debe ser y lo que mandaron
                    $equals = array_diff($encabezadosMin, $arrayCampos);
                    if (count($equals) > 0) {
                        return redirect()->back()->withErrors(['error' => 'No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados']);
                    }
                    if (count($filearray) <= 0) {
                        return redirect()->back()->withErrors(['error' => 'El excel esta vacio']);
                    }



                            //carga masiva de operativas
                            $ejercicio = array();
                            foreach ($filearray as $k) {

                                //buscar en el array de upps 
                                $var = array_search($k['5'], $arrayupps);

                                switch ($k['16']) {
                                    case 'UUU':
                                        //buscar en el array de totales 
                                        $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $k['5'])->count();
                                        if ($uppsautorizadas) {
                                            return redirect()->back()->withErrors(['error' => 'La upp: ' . $k['5'] . ' no se puede cargar en tipo operativo porque esta autorizada para cargar RH']);
                                        }

                                        if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                            $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];
                                        } else {
                                            $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                            array_push($ejercicio, '20' . $k['20']);
                                        }
                                        break;
                                    case '':
                                        return redirect()->back()->withErrors(['error' => 'El Subprograma no puede ir vacio. Revise que no haya filas vacias con formulas']);

                                    default:
                                        //buscar en el array de totales 
                                        if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                            $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];

                                        } else {
                                            $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                            array_push($ejercicio, '20' . $k['20']);
                                        }

                                }

                                if (strlen($k['20']) !== 2) {
                                    return redirect()->back()->withErrors(['error' => 'El año debe ser a dos digitos']);
                                }

                                if (!is_numeric($k['27'])) {
                                    return redirect()->back()->withErrors(['error' => 'El total no puede ir vacio y debe ser un número.']);
                                }
                                if (
                                    !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                                    || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                                ) {
                                    return redirect()->back()->withErrors(['error' => 'los campos de enero a diciembre deben ser numeros']);
                                }

                                //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                                if ($var === 0) {
                                    $var = true;
                                }
                                $var == false ? array_push($arrayupps, $k['5']) : NULL;
                            }


                            //validacion de totales
                            $helperejercicio = 0;
                            foreach ($arraypresupuesto as $key => $value) {
                                $arraysplit = str_split($key, 3);
                                $tipoFondo = '';

                                $tipoFondo = 'Operativo';


                                $VerifyEjercicio = cierreEjercicio::select()->where('clv_upp', $arraysplit[0])->where('estatus', 'Abierto')->where('ejercicio', $ejercicio[$helperejercicio])->count();


                                $valuepresupuesto = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('ejercicio', $ejercicio[$helperejercicio])->where('tipo', $tipoFondo)->where('clv_fondo', $arraysplit[2])->value('presupuesto');

                                $valueExist = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('ejercicio', $ejercicio[$helperejercicio])->where('tipo', $tipoFondo)->where('clv_fondo', $arraysplit[2])->count();

                                
                                if ($valueExist < 1) {
                                    return redirect()->back()->withErrors(['error' => 'No existe esea combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2]]);

                                }


                                if ($valuepresupuesto != $value) {
                                    return redirect()->back()->withErrors(['error' => 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]]);
                                }

                                if ($VerifyEjercicio < 1) {
                                    return redirect()->back()->withErrors(['error' => 'El año del ejercicio  seleccionado no es valido en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]]);
                                }
                                $helperejercicio++;

                            }

                            //validacion para eliminar registros tipo admin
                            foreach ($arrayupps as $u) {
                                $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->count();


                                $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();

                                if ($confirmadas>0) {
                                    return redirect()->back()->withErrors(['error' => 'No se pueden añadir más claves por carga masiva a la upp: '.$u.' porque ya tiene claves confirmadas']);

                                }

                                if ($valupp > 0) {
                                    $deleted = ProgramacionPresupuesto::where('upp', $u)->where('ejercicio', $ejercicio[0])->where('estado', 0)->forceDelete();
                                }


                            }
                            $b = array(
                                "username" => Auth::user()->username,
                                "accion" => 'Borrar registros carga masiva',
                                "modulo" => 'Claves presupuestales'
                            );
                            Controller::bitacora($b);




                    


                }
            }
            //Validaciones para usuarios upps 
            else {
                $tipousuario = auth::user()->id_grupo;

                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $uppUsuario)->count();
                // Checar permiso
                if (Controller::check_assignFront(1)) {
                } else {
                    return redirect()->back()->withErrors(['error' => 'No tiene permiso para subir carga masiva']);


                }



                $arrayupps = array();
                $arraypresupuesto = array();
                $countO = 0;
                $countR = 0;
                $ObraCount = 0;
                $DiferenteUpp = 0;
                if ($xlsx = SimpleXLSX::parse($request->file)) {
                    $filearray = $xlsx->rows();
                    if (count($filearray) <= 1) {
                        return redirect()->back()->withErrors(['error' => 'El excel esta vacio']);
                    }
                    //tomamos los encabezados
                    $encabezados = array_shift($filearray);
                    //Los convertimos todos a lowecase
                    $encabezadosMin = array_map('strtolower', $encabezados);
                    //Verificamos si hay diferencia entre lo que debe ser y lo que mandaron
                    $equals = array_diff($encabezadosMin, $arrayCampos);
                    if (count($equals) > 0) {
                        return redirect()->back()->withErrors(['error' => 'No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados']);
                    }
                    if (count($filearray) <= 0) {
                        return redirect()->back()->withErrors(['error' => 'El excel esta vacio']);
                    }
                    if ($tipousuario != 1) {
                        $ejercicio = array();
                    }


                    foreach ($filearray as $k) {
                        //buscar en el array de upps 

                        $var = array_search($k['5'], $arrayupps);

                        switch ($k['16']) {
                            case 'UUU':
                                if ($tipousuario == 5 || $uppsautorizadas) {
                                    $countR++;
                                    //buscar en el array de totales 
                                    if (array_key_exists($k['5'] . 'CRH' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                        $arraypresupuesto[$k['5'] . 'CRH' . $k['24']] = $arraypresupuesto[$k['5'] . 'CRH' . $k['24']] + $k['27'];
                                    } else {
                                        $arraypresupuesto[$k['5'] . 'CRH' . $k['24']] = $k['27'];
                                        array_push($ejercicio, '20' . $k['20']);
                                    }
                                } else {
                                    //buscar en el array de totales 
                                    if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                        $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];
                                    } else {
                                        $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                        array_push($ejercicio, '20' . $k['20']);
                                    }
                                }

                                break;
                            case '':
                                return redirect()->back()->withErrors(['error' => 'El Subprograma no puede ir vacio']);

                            default:
                                $countO++;
                                //buscar en el array de totales 
                                if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];

                                } else {
                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                    array_push($ejercicio, '20' . $k['20']);
                                }

                        }
                        if ($k['5'] != $uppUsuario) {
                            $DiferenteUpp++;
                        }
                        if ($k['26'] != '000000') {

                            $ObraCount++;
                        }
                        if (strlen($k['20']) !== 2) {
                            return redirect()->back()->withErrors(['error' => 'El año debe ser a dos digitos']);
                        }

                        if (!is_numeric($k['27'])) {
                            return redirect()->back()->withErrors(['error' => 'El total no puede ir vacio y debe ser un número.']);
                        }
                        if (
                            !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                            || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                        ) {
                            return redirect()->back()->withErrors(['error' => 'los campos de enero a diciembre deben ser numeros']);
                        }

                        //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                        if ($var === 0) {
                            $var = true;
                        }
                        $var == false ? array_push($arrayupps, $k['5']) : NULL;

                    }
                    //validacion de totales
                    $helperejercicio = 0;
                    foreach ($arraypresupuesto as $key => $value) {
                        $arraysplit = str_split($key, 3);
                        $tipoFondo = '';
                        if ($arraysplit[1] == 'CRH') {
                            $tipoFondo = 'RH';
                        } else {
                            $tipoFondo = 'Operativo';
                        }


                        $VerifyEjercicio = cierreEjercicio::select()->where('clv_upp', $arraysplit[0])->where('estatus', 'Abierto')->where('ejercicio', $ejercicio[$helperejercicio])->count();
                    
                        $valueExist = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('ejercicio', $ejercicio[$helperejercicio])->where('tipo', $tipoFondo)->where('clv_fondo', $arraysplit[2])->count();

                        if ($valueExist < 1) {
                            return redirect()->back()->withErrors(['error' => 'No existe esea combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2]]);

                        }


                        $valuepresupuesto = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('tipo', $tipoFondo)->where('ejercicio', $ejercicio[$helperejercicio])->where('clv_fondo', $arraysplit[2])->value('presupuesto');
                        if ($valuepresupuesto != $value) {

                            return redirect()->back()->withErrors(['error' => 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]]);
                        }

                        if ($VerifyEjercicio < 1) {
                            return redirect()->back()->withErrors(['error' => 'El año del ejercicio  seleccionado no es valido en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]]);
                        }
                        $helperejercicio++;

                    }

                    switch ($tipousuario) {
                        case 4:

                            if ($DiferenteUpp > 0) {
                                return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar de  otras upps']);
                            }
                            if ($ObraCount > 0) {
                                if (Controller::check_assignFront(2)) {

                                } else {
                                    return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar obras']);

                                }


                            }
                            switch ($uppsautorizadas) {
                                case 0:
                                    if ($DiferenteUpp > 0) {
                                        return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar de  otras upps']);

                                    } else {
                                        
                                        //validacion para eliminar registros no confirmados 
                                        foreach ($arrayupps as $u) {

                                            $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                            if ($valupp > 0) {
                                                $deleted = ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                            }
                                            $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                            if ($confirmadas >0) {
                                                return redirect()->back()->withErrors(['error' => 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: '.$u]);
            
                                            }
                                            
                                        }
                                        $b = array(
                                            "username" => Auth::user()->username,
                                            "accion" => 'Borrar registros carga masiva',
                                            "modulo" => 'Claves presupuestales'
                                        );
                                        Controller::bitacora($b);
                                    }

                                    break;

                                case 1:
                                    if ($countR > 0) {
                                        return redirect()->back()->withErrors(['error' => 'Hay claves de RH en el archivo de cargas masivas']);
                                    }
                                    if ($DiferenteUpp > 0) {
                                        return redirect()->back()->withErrors(['error' => 'No tiene permiso para registrar de  otras upps']);

                                    }
                                    //validacion para eliminar registros no confirmados 
                                    foreach ($arrayupps as $u) {
                                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                        if ($valupp > 0) {
                                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '!=', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                        }
                                        $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '!=', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                        if ($confirmadas > 0) {
                                            return redirect()->back()->withErrors(['error' => 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: '.$u]);
        
                                        }
                                    }
                                    $b = array(
                                        "username" => Auth::user()->username,
                                        "accion" => 'Borrar registros carga masiva',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);
                                    break;
                            }


                            break;

                        case 5:
                            if ($countO > 0) {
                                return redirect()->back()->withErrors(['error' => 'Hay claves Operativas en el archivo de cargas masivas']);

                            }
                            //validacion para eliminar registros no confirmados 
                            foreach ($arrayupps as $key => $u) {

                                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $u)->count();
                                if ($uppsautorizadas == 0) {
                                    return redirect()->back()->withErrors(['error' => 'La upp: '.$u.' no esta en la lista de upps autorizadas para carga masiva RH']);

                                }


                                $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                if ($valupp > 0) {
                                    $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '==', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                }
                                $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '==', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                if ($confirmadas >0) {
                                    return redirect()->back()->withErrors(['error' => 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: '.$u]);

                                }
                            }
                            $b = array(
                                "username" => Auth::user()->username,
                                "accion" => 'Borrar registros carga masiva',
                                "modulo" => 'Claves presupuestales'
                            );
                            Controller::bitacora($b);
                            break;


                    }


                }
            }
        } catch (\Throwable $th) {
            Log::debug($th);
            return redirect()->back()->withErrors(['error' => 'Ocurrio un error intentelo más tarde']);

        }
        //si todo sale bien procedemos al import
        try {
            (new ClavePresupuestaria)->import($request->file, 'local', \Maatwebsite\Excel\Excel::XLSX);


            //mandamos llamar procedimiento de jeff
            $datos = DB::select("CALL insert_pp_aplanado(" . $ejercicio[0] . ")");
            $b = array(
                "username" => Auth::user()->username,
                "accion" => 'Carga masiva',
                "modulo" => 'Claves presupuestales'
            );
            Controller::bitacora($b);
            return redirect()->back()->withSuccess('Se cargaron correctamente los datos');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $key => $failure) {
                $valuesar = $failure->values();
                if (!$valuesar['total']) {
                    unset($failures[$key]);
                }
            }
            return redirect()->back()->withErrors($failures);



        }



    }




}