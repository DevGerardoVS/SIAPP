<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Exports\ImportErrorsExport;
use App\Http\Controllers\Controller;
use App\Models\calendarizacion\clasificacion_geografica;
use App\Models\Catalogo;
use App\Models\Fondos;
use App\Models\Obra;
use App\Models\PosicionPresupuestaria;
use App\Models\RelEconomicaAdministrativa;
use App\Models\v_epp;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\TechosFinancieros;
use App\Models\cierreEjercicio;
use App\Exports\PlantillaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\uppautorizadascpnomina;
use App\Helpers\Calendarizacion\MetasHelper;

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


    public function DownloadErrors($fails)
    {

        $fails = json_decode($fails);
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
        if(Auth::user()->id_grupo == 4){
            $uppUsuario = auth::user()->clv_upp;
        }
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
        $arrayErrores = array();
        $arrayclaves = array();

        //verificar que el usuario tenga permiso
        try {
            //Validaciones para administrador
            if ( auth::user()->id_grupo==1) {

                $arrayupps = array();
                $arraypresupuesto = array();
                if ($xlsx = SimpleXLSX::parse($request->file)) {
                    $filearray = $xlsx->rows();
                    //tomamos los encabezados
                    $encabezados = array_shift($filearray);
                    //Los convertimos todos a lowecase
                    $encabezadosMin = array_map('strtolower', $encabezados);
                    //Verificamos si hay diferencia entre lo que debe ser y lo que mandaron
                    $equals = array_diff($encabezadosMin, $arrayCampos);
                    if (count($equals) > 0) {
                        array_push($arrayErrores, 'Error: No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados.');

                    }
                    if (count($filearray) <= 0) {
                        array_push($arrayErrores, 'Error: El excel esta vacio ');
                    }



                    //carga masiva de operativas
                    $ejercicio = array();
                    foreach ($filearray as  $indext=> $k) {
                        $currentrow = $indext + 2;

                        //buscar en el array de upps 
                        $var = array_search($k['5'], $arrayupps);
                           switch ($k['16']) {
                            
                            case 'UUU':
                                //buscar en el array de totales 
                                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $k['5'])->count();
                                if ($uppsautorizadas) {
                                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La upp: ' . $k['5'] . ' no se puede cargar en tipo operativo porque esta autorizada para cargar RH');
                                }

                                if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];
                                } else {
                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                    array_push($ejercicio, '20' . $k['20']);
                                }
                                break;
                            case '':
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La upp: ' . $k['5'] . ' El Subprograma no puede ir vacio. Revise que no haya filas vacias con formulas');

                            default:
                                //buscar en el array de totales 
                                if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];

                                } else {
                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                    array_push($ejercicio, '20' . $k['20']);
                                }

                        }
                        if (array_key_exists($k['0']. $k['1'].$k['2']. $k['3']. $k['4'].$k['5']. $k['6']. $k['7'].$k['8']. $k['9']
                        . $k['10'].$k['11']. $k['12']. $k['13'].$k['14']. $k['15']. $k['16'].$k['17']. $k['18']. $k['19']. $k['21']
                        . $k['22'].$k['23']. $k['24']. $k['25'].$k['26'], $arrayclaves)) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave esta repetida en el excel Verifique de las columnas A a AA');

                        }
                        else{
                            $arrayclaves[ $k['0']. $k['1'].$k['2']. $k['3']. $k['4'].$k['5']. $k['6']. $k['7'].$k['8']. $k['9']
                            . $k['10'].$k['11']. $k['12']. $k['13'].$k['14']. $k['15']. $k['16'].$k['17']. $k['18']. $k['19']. $k['21']
                            . $k['22'].$k['23']. $k['24']. $k['25'].$k['26']]=$currentrow;

                        }
                        

                        if (strlen($k['20']) !== 2 && !is_numeric($k['20'])) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El año debe ser a dos digitos y debe ser un número');
                        }

                        if (!is_numeric($k['27'])) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no puede ir vacio y debe ser un número.');

                        }
                        if (
                            !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                            || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                        ) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los campos de enero a diciembre deben ser numeros');
                        }

                        $query = MetasHelper::actividades($k['5'], '20' . $k['20']);
                        if (count($query) > 0) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': No se pueden añadir claves porque ya hay metas registradas');

                        }
                        //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                        if ($var === 0) {
                            $var = true;
                        }
                        if($var && strlen($var) == 3){
                            array_push($arrayupps, $k['5']);
                        }
                        else{
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La upp deben ser 3 caracteres');
                        }
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
                            array_push($arrayErrores, 'No existe esea combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2]);
                        }


                        if ($valuepresupuesto != $value) {
                            array_push($arrayErrores, 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]);

                        }

                        if ($VerifyEjercicio < 1) {
                            array_push($arrayErrores, 'El año del ejercicio  seleccionado no esta abierto para captura en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]);

                        }
                        $helperejercicio++;

                    }

                    //validacion para eliminar registros tipo admin
                    foreach ($arrayupps as $u) {
                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->count();


                        $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();

                        if ($confirmadas > 0) {
                            array_push($arrayErrores, 'No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas');

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
                if ($xlsx = SimpleXLSX::parse($request->file)) {
                    $filearray = $xlsx->rows();
                    //tomamos los encabezados
                    $encabezados = array_shift($filearray);
                    //Los convertimos todos a lowecase
                    $encabezadosMin = array_map('strtolower', $encabezados);
                    //Verificamos si hay diferencia entre lo que debe ser y lo que mandaron
                    $equals = array_diff($encabezadosMin, $arrayCampos);
                    if (count($equals) > 0) {
                        array_push($arrayErrores, 'Error: No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados.');
                    }
                    if (count($filearray) <= 0) {
                        array_push($arrayErrores, 'Error: El excel esta vacio ');
                    }
                    if ($tipousuario != 1) {
                        $ejercicio = array();
                    }


                    foreach ($filearray as $indextu=> $k ) {
                        //buscar en el array de upps 
                        $currentrow = $indextu + 2;

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
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La upp: ' . $k['5'] . ' El Subprograma no puede ir vacio. Revise que no haya filas vacias con formulas');

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
                        if (array_key_exists($k['0']. $k['1'].$k['2']. $k['3']. $k['4'].$k['5']. $k['6']. $k['7'].$k['8']. $k['9']
                        . $k['10'].$k['11']. $k['12']. $k['13'].$k['14']. $k['15']. $k['16'].$k['17']. $k['18']. $k['19']. $k['21']
                        . $k['22'].$k['23']. $k['24']. $k['25'].$k['26'], $arrayclaves)) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave esta repetida en el excel Verifique de las columnas A a AA');

                        }
                        else{
                            $arrayclaves[ $k['0']. $k['1'].$k['2']. $k['3']. $k['4'].$k['5']. $k['6']. $k['7'].$k['8']. $k['9']
                            . $k['10'].$k['11']. $k['12']. $k['13'].$k['14']. $k['15']. $k['16'].$k['17']. $k['18']. $k['19']. $k['21']
                            . $k['22'].$k['23']. $k['24']. $k['25'].$k['26']]=$currentrow;

                        }

                        if ($k['5'] != $uppUsuario) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': No tiene permiso para registrar de  otras upps');


                        }
                        if ($k['26'] != '000000') {
                            if (Controller::check_assignFront(2)) {

                            } else {
                                array_push($arrayErrores, 'No tiene permiso para registrar obras');
                            }                        }
                        if (strlen($k['20']) !== 2 && !is_numeric($k['20'])) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El año debe ser a dos digitos y debe ser un número');
                        }

                        if (!is_numeric($k['27'])) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no puede ir vacio y debe ser un número.');
                        }
                        if (
                            !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                            || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                        ) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los campos de enero a diciembre deben ser numeros');
                        }

                        $query = MetasHelper::actividades($k['5'], '20' . $k['20']);
                        if (count($query) > 0) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': No se pueden añadir claves porque ya hay metas registradas');

                        }
                        //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                        if ($var === 0) {
                            $var = true;
                        }
                        if($var && strlen($var) == 3){
                            array_push($arrayupps, $k['5']);
                        }
                        else{
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La upp deben ser 3 caracteres');
                        }

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
                        
                        $valuepresupuesto = TechosFinancieros::select()->where('clv_upp', $arraysplit[0])->where('tipo', $tipoFondo)->where('ejercicio', $ejercicio[$helperejercicio])->where('clv_fondo', $arraysplit[2])->value('presupuesto');

                        if ($valueExist < 1) {
                            array_push($arrayErrores, 'No existe esea combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2]);

                        }

                        if ($valuepresupuesto != $value) {

                            array_push($arrayErrores, 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]);
                        }

                        if ($VerifyEjercicio < 1) {
                            
                            array_push($arrayErrores, 'El año del ejercicio  seleccionado no esta abierto para captura en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2]);
                        }
                        $helperejercicio++;

                    }

                    switch ($tipousuario) {
                        case 4:
                            switch ($uppsautorizadas) {
                                case 0:
                                        //validacion para eliminar registros no confirmados 
                                        foreach ($arrayupps as $u) {

                                            $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                            if ($valupp > 0) {
                                                $deleted = ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                            }
                                            $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                            if ($confirmadas > 0) {
                                                array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u);


                                            }

                                        }
                                        $b = array(
                                            "username" => Auth::user()->username,
                                            "accion" => 'Borrar registros carga masiva',
                                            "modulo" => 'Claves presupuestales'
                                        );
                                        Controller::bitacora($b);
                                    

                                    break;

                                case 1:
                                    if ($countR > 0) {
                                        array_push($arrayErrores, 'Hay claves de RH en el archivo de cargas masivas');

                                    }
                                    //validacion para eliminar registros no confirmados 
                                    foreach ($arrayupps as $u) {
                                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                        if ($valupp > 0) {
                                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '!=', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                        }
                                        $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '!=', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                        if ($confirmadas > 0) {
                                            array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u);

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
                                array_push($arrayErrores, 'Hay claves Operativas en el archivo de cargas masivas');

                            }
                            //validacion para eliminar registros no confirmados 
                            foreach ($arrayupps as $key => $u) {

                                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $u)->count();
                                if ($uppsautorizadas == 0) {
                                    array_push($arrayErrores, 'La upp: ' . $u . ' no esta en la lista de upps autorizadas para carga masiva RH');

                                }


                                $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                if ($valupp > 0) {
                                    $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '==', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                }
                                $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '==', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                if ($confirmadas > 0) {
                                    array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u);

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

            DB::beginTransaction();
            foreach ($filearray as $index => $k) {
                $currentrow = $index + 2;
                $tipoclave = '';
                $añoclave = 0;
                $usuarioclave = '';

                //validacion de año 
                if (strlen($k['20']) == 2 && is_numeric($k['20'])) {
                    $year = '20' . $k['20'];
                    $añoclave = $year;
                } else {
                    if ($k['20'] != 2) {
                        $añoclave = 2024;
                    }

                }
                ///validaciones de catalogo
                $valcat = Catalogo::select()
                    ->where('grupo_id', '6')
                    ->where('clave', $k['5'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['5'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor de upp asignado no es valido');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '7')
                    ->where('clave', $k['6'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['6'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de subsecretaria introducida no es valida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '8')
                    ->where('clave', $k['7'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['7'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El campo ur no existe o la combinacion de ur upp y secretaria es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '9')
                    ->where('clave', $k['8'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['8'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de finalidad introducida no es valida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '10')
                    ->where('clave', $k['9'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['9'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de funcion introducida no es valida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '11')
                    ->where('clave', $k['10'])
                    ->count();
                $valcat >= 1 ? $k['10'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de subfuncion introducida no es valida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '12')
                    ->where('clave', $k['11'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['11'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de eje es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '13')
                    ->where('clave', $k['12'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['12'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de pt es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '14')
                    ->where('clave', $k['13'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['13'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de ps es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '15')
                    ->where('clave', $k['14'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['14'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de sprconac es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '16')
                    ->where('clave', $k['15'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['15'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de prg es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '17')
                    ->where('clave', $k['16'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['16'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de spr es invalida');

                $valcat = Catalogo::select()
                    ->where('grupo_id', '18')
                    ->where('clave', $k['17'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                $valcat >= 1 ? $k['17'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de py es invalida');




                $arraypos = str_split($k['18'], 1);

                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $k['5'])->count();

                if (count($arraypos) >= 4) {
                    if ($k['16'] == 'UUU') {
                        if ($uppsautorizadas) {
                            $tipoclave = 'RH';
                        } else {
                            $tipoclave = 'Operativo';
                        }

                        $k['26'] == '000000' ? $k['26'] : array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La obra es invalida');



                        if ($arraypos[0] == 1) {
                            $valpos = PosicionPresupuestaria::select()
                                ->where('clv_capitulo', $arraypos[0])
                                ->where('clv_concepto', $arraypos[1])
                                ->where('clv_partida_generica', $arraypos[2])
                                ->where('clv_partida_especifica', $arraypos[3] . $arraypos[4])
                                ->where('clv_tipo_gasto', $k['19'])
                                ->count();
                            if ($valpos < 1) {
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida');
                            }
                        }

                    } else {
                        $tipoclave = 'Operativo';

                        $valObra = Obra::select()
                            ->where('clv_proyecto_obra', $k['26'])
                            ->count();
                        if ($valObra > 0) {
                            $valpos = PosicionPresupuestaria::select()
                                ->where('clv_capitulo', $arraypos[0])
                                ->where('clv_concepto', $arraypos[1])
                                ->where('clv_partida_generica', $arraypos[2])
                                ->where('clv_partida_especifica', $arraypos[3] . $arraypos[4])
                                ->where('clv_tipo_gasto', $k['19'])
                                ->count();
                            if ($valpos < 1) {
                                array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida');
                            }

                        } else {
                            //la obra no existe
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La obra no existe');
                        }


                    }
                } else {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de posición presupuestaria es invalida');

                }





                //validacion nueva sobre idpartida/tipo gasto en combinacion con admonac

                $valrelEco = RelEconomicaAdministrativa::select()
                    ->where('clasificacion_administrativa', $k['0'])
                    ->where('clasificacion_economica', $k['18'] . $k['19'])
                    ->count();
                if ($valrelEco < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clasificacion economica introducida es invalida para esta clave administrativa');
                }

                //validacion de fondos
                $valfondo = Fondos::select()
                    ->where('clv_etiquetado', $k['21'])
                    ->where('clv_fuente_financiamiento', $k['22'])
                    ->where('clv_ramo', $k['23'])
                    ->where('clv_fondo_ramo', $k['24'])
                    ->where('clv_capital', $k['25'])
                    ->count();
                if ($valfondo < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de las claves de la celda V a Z es invalida');
                }


                //validacion de codigo admconac
                if (isset($k['0']) && $k['0'] !== '0') {
                    $arrayadmconac = str_split($k['0'], 1);

                    $valadm = v_epp::select()
                        ->where('clv_sector_publico', $arrayadmconac[0])
                        ->where('clv_sector_publico_f', $arrayadmconac[1])
                        ->where('clv_sector_economia', $arrayadmconac[2])
                        ->where('clv_subsector_economia', $arrayadmconac[3])
                        ->where('clv_ente_publico', $arrayadmconac[4])
                        ->where('ejercicio', $añoclave)
                        ->count();
                    if ($valadm < 1) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave de admonac es invalida');

                    }
                    //validacion de presupuestable
                    $valpresup = v_epp::select()
                        ->where('clv_sector_publico', $arrayadmconac[0])
                        ->where('clv_sector_publico_f', $arrayadmconac[1])
                        ->where('clv_sector_economia', $arrayadmconac[2])
                        ->where('clv_subsector_economia', $arrayadmconac[3])
                        ->where('clv_ente_publico', $arrayadmconac[4])
                        ->where('clv_upp', $k['5'])
                        ->where('clv_subsecretaria', $k['6'])
                        ->where('clv_ur', $k['7'])
                        ->where('clv_finalidad', $k['8'])
                        ->where('clv_funcion', $k['9'])
                        ->where('clv_subfuncion', $k['10'])
                        ->where('clv_eje', $k['11'])
                        ->where('clv_linea_accion', $k['12'])
                        ->where('clv_programa_sectorial', $k['13'])
                        ->where('clv_tipologia_conac', $k['14'])
                        ->where('clv_programa', $k['15'])
                        ->where('clv_subprograma', $k['16'])
                        ->where('clv_proyecto', $k['17'])
                        ->where('ejercicio', $añoclave)
                        ->where('presupuestable', 1)
                        ->count();
                    if ($valpresup < 1) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El programa seleccionado no es presupuestable, verifica las columnas A, F a R y el año.');

                    }
                }

                //validacion que el conjunto sea una clave valida
                $valcomb = v_epp::select()
                    ->where('clv_finalidad', $k['8'])
                    ->where('clv_funcion', $k['9'])
                    ->where('clv_subfuncion', $k['10'])
                    ->where('clv_eje', $k['11'])
                    ->where('clv_linea_accion', $k['12'])
                    ->where('clv_programa_sectorial', $k['13'])
                    ->where('clv_tipologia_conac', $k['14'])
                    ->where('clv_programa', $k['15'])
                    ->where('clv_subprograma', $k['16'])
                    ->where('clv_proyecto', $k['17'])
                    ->where('ejercicio', $añoclave)
                    ->get();
                if (count($valcomb) < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de claves de la celda I a la R es invalida');

                }

                //validacion de trio upp/ur/sub en vista
                $valv_eje = v_epp::select()
                    ->where('clv_upp', $k['5'])
                    ->where('clv_ur', $k['7'])
                    ->where('clv_subsecretaria', $k['6'])
                    ->where('ejercicio', $añoclave)
                    ->count();
                if ($valv_eje < 1) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': la combinacion de ur upp y secretaria es invalida');


                }
                //validacion de total
                $suma = $k['28'] + $k['29'] + $k['30'] + $k['31'] + $k['32'] + $k['33'] + $k['34'] + $k['35'] + $k['36'] + $k['37'] + $k['38'] + $k['39'];
                if ($k['27'] != $suma) {
                    array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no coincide con los meses');


                }

                     $k['0']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor admconac no puede ir vacio'): null;
                     $k['1']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor ef no puede ir vacio'): null;
                     $k['2']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor reg no puede ir vacio'): null;
                     $k['3']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor mpio no puede ir vacio'): null;
                     $k['4']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor loc no puede ir vacio'): null;
                     $k['5']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor upp no puede ir vacio'): null;
                     $k['6']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor subsecretaria no puede ir vacio'): null;
                     $k['7']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor ur no puede ir vacio'): null;
                     $k['8']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor finalidad no puede ir vacio'): null;
                     $k['9']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor función no puede ir vacio'): null;
                     $k['10']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor subfunción no puede ir vacio'): null;
                     $k['11']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor eje no puede ir vacio'): null;
                     $k['12']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor pt no puede ir vacio'): null;
                     $k['13']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor ps no puede ir vacio'): null;
                     $k['14']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor sprconac no puede ir vacio'): null;
                     $k['15']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor prg no puede ir vacio'): null;
                     $k['16']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor spr no puede ir vacio'): null;
                     $k['17']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor py no puede ir vacio'): null;
                     $k['18']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor idpartida no puede ir vacio'): null;
                     $k['19']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor tipo de gasto no puede ir vacio'): null;
                     $k['20']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor año no puede ir vacio'): null;
                     $k['21']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor etiquetado y no etiquetado no puede ir vacio'): null;
                     $k['22']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor fconac no puede ir vacio'): null;
                     $k['23']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor ramo no puede ir vacio'): null;
                     $k['24']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor fondo no puede ir vacio'): null;
                     $k['25']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor ci no puede ir vacio'): null;
                     $k['26']=='' ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El valor obra no puede ir vacio'): null;
                     $k['27']=='' ? $k['27'] =0: null;
                     $k['28']=='' ? $k['28'] =0: null;
                     $k['29']=='' ? $k['29'] =0: null;
                     $k['30']=='' ? $k['30'] =0: null;
                     $k['31']=='' ? $k['31'] =0: null;
                     $k['32']=='' ? $k['32'] =0: null;
                     $k['33']=='' ? $k['33'] =0: null;
                     $k['34']=='' ? $k['34'] =0: null;
                     $k['35']=='' ? $k['35'] =0: null;
                     $k['36']=='' ? $k['36'] =0: null;
                     $k['37']=='' ? $k['37'] =0: null;
                     $k['38']=='' ? $k['38'] =0: null;
                     $k['39']=='' ? $k['39'] =0: null;
                    

                //validacion de parte clave geografica
                $valgeo = clasificacion_geografica::select()
                    ->where('clv_entidad_federativa', $k['1'])
                    ->where('clv_region', $k['2'])
                    ->where('clv_municipio', $k['3'])
                    ->where('clv_localidad', $k['4'])->count();
                $valgeo < 1 ? array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La combinacion de las claves de la celda B a E es invalida') : null;



                $usuarioclave = 'CargaMasiva' . Auth::user()->username;

                $clave = ProgramacionPresupuesto::create([
                    'clasificacion_administrativa' => $k['0'],
                    'entidad_federativa' => $k['1'],
                    'region' => $k['2'],
                    'municipio' => $k['3'],
                    'localidad' => $k['4'],
                    'upp' => $k['5'],
                    'subsecretaria' => $k['6'],
                    'ur' => $k['7'],
                    'finalidad' => $k['8'],
                    'funcion' => $k['9'],
                    'subfuncion' => $k['10'],
                    'eje' => $k['11'],
                    'linea_accion' => $k['12'],
                    'programa_sectorial' => $k['13'],
                    'tipologia_conac' => $k['14'],
                    'programa_presupuestario' => $k['15'],
                    'subprograma_presupuestario' => $k['16'],
                    'proyecto_presupuestario' => $k['17'],
                    'periodo_presupuestal' => '01-ENE',
                    'posicion_presupuestaria' => $k['18'],
                    'tipo_gasto' => $k['19'],
                    'anio' => $k['20'],
                    'etiquetado' => $k['21'],
                    'fuente_financiamiento' => $k['22'],
                    'ramo' => $k['23'],
                    'fondo_ramo' => $k['24'],
                    'capital' => $k['25'],
                    'proyecto_obra' => $k['26'],
                    'ejercicio' => $añoclave,
                    'enero' => $k['28'],
                    'febrero' => $k['29'],
                    'marzo' => $k['30'],
                    'abril' => $k['31'],
                    'mayo' => $k['32'],
                    'junio' => $k['33'],
                    'julio' => $k['34'],
                    'agosto' => $k['35'],
                    'septiembre' => $k['36'],
                    'octubre' => $k['37'],
                    'noviembre' => $k['38'],
                    'diciembre' => $k['39'],
                    'total' => $k['27'],
                    'estado' => 0,
                    'tipo' => $tipoclave,
                    'updated_at' => null,
                    'created_user' => $usuarioclave
                ]);
            }

            if (count($arrayErrores) > 0) {
                DB::rollBack();
                return redirect()->back()->withErrors($arrayErrores);
            } else {
                DB::commit();

            }

            /*             (new ClavePresupuestaria)->import($request->file, 'local', \Maatwebsite\Excel\Excel::XLSX);
             */

            //mandamos llamar procedimiento de jeff
            $datos = DB::select("CALL insert_pp_aplanado(" . $ejercicio[0] . ")");
            $b = array(
                "username" => Auth::user()->username,
                "accion" => 'Carga masiva',
                "modulo" => 'Claves presupuestales'
            );
            Controller::bitacora($b);
            return redirect()->back()->withSuccess('Se cargaron correctamente los datos');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrrio un error en el sistema intentelo más tarde']);

            /*             $failures = $e->failures();

                        foreach ($failures as $key => $failure) {
                            $valuesar = $failure->values();
                            if (!$valuesar['total']) {
                                unset($failures[$key]);
                            }
                        }
                         */



        }



    }




}