<?php

namespace App\Jobs;

use App\Events\ActualizarSesionUsuario;
use App\Models\carga_masiva_estatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\uppautorizadascpnomina;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Models\ProgramacionPresupuesto;
use App\Models\TechosFinancieros;
use App\Models\cierreEjercicio;
use Illuminate\Support\Facades\Log;
use Auth;
use App\Http\Controllers\Controller;
use App\Jobs\CargaMasivaClaves;
use Illuminate\Support\Facades\Session;
use DB;

class ValidacionesCargaMasivaClaves implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filearray;
    protected $user;

    public function __construct($filearray, $user)
    {
        $this->filearray = $filearray;
        $this->user = $user;

    }

    public function handle()
    {
        try {
            $uppUsuario = 0;

            if ($this->user->id_grupo == 4) {
                $uppUsuario = $this->user->clv_upp;
            }
            $usuario = $this->user;

            $arrayErrores = array();
            $arrayclaves = array();

            $arrayupps = array();
            $arraypresupuesto = array();
            //Validaciones para administrador
            if ($usuario->id_grupo == 1) {
                DB::beginTransaction();
                //carga masiva de operativas
                $ejercicio = array();
                foreach ($this->filearray as $indext => $k) {
                    $currentrow = $indext + 2;

                    //buscar en el array de upps 
                    $var = array_search($k['5'], $arrayupps);
                    switch ($k['16']) {

                        case 'UUU':

                            if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];
                            } else {
                                $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                array_push($ejercicio, '20' . $k['20']);
                            }
                            break;
                        case '':
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ' El Subprograma no puede ir vacio. Revise que no haya filas vacias con formulas. ');

                        default:
                            //buscar en el array de totales 
                            if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];

                            } else {
                                $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                array_push($ejercicio, '20' . $k['20']);
                            }

                    }
                    if (
                        array_key_exists($k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26'], $arrayclaves)
                    ) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave esta repetida en el excel Verifique de las columnas A a AA. ');

                    } else {
                        $arrayclaves[$k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26']] = $currentrow;

                    }


                    if (strlen($k['20']) !== 2 && !is_numeric($k['20'])) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El año debe ser a dos digitos y debe ser un número. ');
                    }

                    if (!is_numeric($k['27'])) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no puede ir vacio y debe ser un número. ');

                    }
                    if (
                        !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                        || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                    ) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los campos de enero a diciembre deben ser numeros. ');
                    }


                    //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                    if ($var === 0) {
                        $var = true;
                    }
                    if (strlen($k['24']) != 2) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los fondos no pueden ir vacios y deben ser a 2 caracteres ');

                    }
                    if (!$var && strlen($k['5']) == 3) {
                        array_push($arrayupps, $k['5']);
                    } else {
                        if (strlen($k['5']) != 3) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Las upp no pueden ir vacias y deben ser 3 caracteres. ');
                        }
                    }
                }



                if (count($arrayErrores) < 1) {
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
                            array_push($arrayErrores, 'No existe esa combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2] . ' ');
                        }


                        if ($valuepresupuesto != $value) {
                            array_push($arrayErrores, 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2] . ' ');

                        }

                        if ($VerifyEjercicio < 1) {
                            array_push($arrayErrores, 'El año del ejercicio  seleccionado no esta abierto para captura en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2] . ' ');

                        }
                        $helperejercicio++;

                    }

                    //validacion para eliminar registros tipo admin
                    foreach ($arrayupps as $u) {

                        //buscar en el array de totales 
                        $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $u)->count();
                        if ($uppsautorizadas) {
                            array_push($arrayErrores, 'Error:  no se puede cargar la upp: ' . $u . ' en tipo operativo porque esta autorizada para cargar RH. ');
                        }
                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->count();
                        $query = MetasHelper::actividades($u, $ejercicio[0]);
                        if (count($query) > 0) {
                            array_push($arrayErrores, 'Error: No se pueden añadir claves porque ya hay metas registradas. ');

                        }

                        $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();

                        if ($confirmadas > 0) {
                            array_push($arrayErrores, 'No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas. ');

                        }

                        if ($valupp > 0) {
                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('ejercicio', $ejercicio[0])->where('estado', 0)->forceDelete();
                        }


                    }
                    $b = array(
                        "username" => $usuario->username,
                        "accion" => 'Borrar registros carga masiva',
                        "modulo" => 'Claves presupuestales'
                    );
                    Controller::bitacora($b);
                }



            }
            //Validaciones para usuarios upps 
            else {
                $tipousuario = $usuario->id_grupo;
                $uppsautorizadas = 0;
                if ($usuario->id_grupo == 4) {
                    $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $uppUsuario)->count();
                }

                $permisoObra = DB::table('permisos_funciones')
                    ->select(
                        'id_user',
                        'id_permiso',
                    )
                    ->where('id_user', $usuario->id)
                    ->where('permisos_funciones.deleted_at', null)
                    ->where('id_permiso', 2)->get();

                $arrayupps = array();
                $arraypresupuesto = array();
                $countO = 0;
                $countR = 0;

                if ($tipousuario != 1) {
                    $ejercicio = array();
                }


                foreach ($this->filearray as $indextu => $k) {
                    //buscar en el array de upps 
                    $currentrow = $indextu + 2;

                    $var = array_search($k['5'], $arrayupps);
                    if ($usuario->id_grupo == 4) {
                        if ($uppsautorizadas && $k['18'] >= 10000 && $k['18'] < 20000) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ' No tiene permiso para cargar esa idpartida. ');

                        }
                        if ($uppsautorizadas && $k['18'] == 39801) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ' No tiene permiso para cargar esa idpartida. ');

                        }
                    }


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
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ' El Subprograma no puede ir vacio. Revise que no haya filas vacias con formulas. ');

                        default:
                            if ($tipousuario == 4) {
                                $countO++;
                                //buscar en el array de totales 
                                if (array_key_exists($k['5'] . 'COP' . $k['24'], $arraypresupuesto) && $k['27'] != '' && $k['5'] . $k['24'] != '') {

                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $arraypresupuesto[$k['5'] . 'COP' . $k['24']] + $k['27'];

                                } else {
                                    $arraypresupuesto[$k['5'] . 'COP' . $k['24']] = $k['27'];
                                    array_push($ejercicio, '20' . $k['20']);
                                }
                            }


                    }
                    if (
                        array_key_exists($k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26'], $arrayclaves)
                    ) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': La clave esta repetida en el excel Verifique de las columnas A a AA. ');

                    } else {
                        $arrayclaves[$k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26']] = $currentrow;

                    }
                    if ($usuario->id_grupo == 4) {

                        if ($k['5'] != $uppUsuario) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': No tiene permiso para registrar de  otras upps. ');


                        }
                    }
                    if ($k['26'] != '000000') {
                        if (count($permisoObra)) {
                        } else {
                            array_push($arrayErrores, 'No tiene permiso para registrar obras. ');
                        }
                    }


                    if (strlen($k['20']) !== 2 && !is_numeric($k['20'])) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El año debe ser a dos digitos y debe ser un número. ');
                    }

                    if (!is_numeric($k['27'])) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': El total no puede ir vacio y debe ser un número. ');
                    }
                    if (
                        !is_numeric($k['28']) || !is_numeric($k['29']) || !is_numeric($k['30']) || !is_numeric($k['31']) || !is_numeric($k['32']) || !is_numeric($k['33'])
                        || !is_numeric($k['34']) || !is_numeric($k['35']) || !is_numeric($k['36']) || !is_numeric($k['37']) || !is_numeric($k['38']) || !is_numeric($k['39'])
                    ) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los campos de enero a diciembre deben ser numeros. ');
                    }


                    //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                    if ($var === 0) {
                        $var = true;
                    }
                    if (strlen($k['24']) != 2) {
                        array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Los fondos no pueden ir vacios y deben ser a 2 caracteres ');

                    }
                    if (!$var && strlen($k['5']) == 3) {
                        array_push($arrayupps, $k['5']);
                    } else {
                        if (strlen($k['5']) != 3) {
                            array_push($arrayErrores, 'Error en  la fila ' . $currentrow . ': Las upp no pueden ir vacias y deben ser 3 caracteres. ');
                        }
                    }

                }

                if (count($arrayErrores) < 1) {
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
                            array_push($arrayErrores, 'No existe esa combinacion en techos financieros para la upp: ' . $arraysplit[0] . ' con fondo: ' . $arraysplit[2] . ' ');

                        }

                        if ($valuepresupuesto != $value) {

                            array_push($arrayErrores, 'El total presupuestado  no es igual al techo financiero en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2] . ' ');
                        }

                        if ($VerifyEjercicio < 1) {

                            array_push($arrayErrores, 'El año del ejercicio  seleccionado no esta abierto para captura en la upp: ' . $arraysplit[0] . ' fondo: ' . $arraysplit[2] . ' ');
                        }
                        $helperejercicio++;

                    }

                    switch ($tipousuario) {
                        case 4:

                            switch ($uppsautorizadas) {
                                case 0:
                                    //validacion para eliminar registros no confirmados 
                                    foreach ($arrayupps as $u) {
                                        $query = MetasHelper::actividades($u, $ejercicio[0]);
                                        if (count($query) > 0) {
                                            array_push($arrayErrores, 'Error:  No se pueden añadir claves porque ya hay metas registradas. ');
                                        }
                                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                        if ($valupp > 0) {
                                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                        }
                                        $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                        if ($confirmadas > 0) {
                                            array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u . ' ');


                                        }

                                    }
                                    $b = array(
                                        "username" => $usuario->username,
                                        "accion" => 'Borrar registros carga masiva',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);


                                    break;

                                case 1:
                                    if ($countR > 0) {
                                        array_push($arrayErrores, 'Hay claves de RH en el archivo de cargas masivas ');

                                    }
                                    //validacion para eliminar registros no confirmados 
                                    foreach ($arrayupps as $u) {
                                        $query = MetasHelper::actividades($u, $ejercicio[0]);
                                        if (count($query) > 0) {
                                            array_push($arrayErrores, 'Error:  No se pueden añadir claves porque ya hay metas registradas. ');
                                        }
                                        $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                        if ($valupp > 0) {
                                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '!=', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                        }
                                        $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '!=', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                        if ($confirmadas > 0) {
                                            array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u . ' ');

                                        }
                                    }
                                    $b = array(
                                        "username" => $usuario->username,
                                        "accion" => 'Borrar registros carga masiva',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);
                                    break;
                            }


                            break;

                        case 5:
                            if ($countO > 0) {
                                array_push($arrayErrores, 'Hay claves Operativas en el archivo de cargas masivas. ');

                            }
                            //validacion para eliminar registros no confirmados 
                            foreach ($arrayupps as $key => $u) {
                                $query = MetasHelper::actividadesDel($u, $ejercicio[0]);
                                if (count($query) > 0) {
                                    array_push($arrayErrores, 'Error: No se pueden añadir claves porque ya hay metas registradas. ');

                                }

                                $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $u)->count();
                                if ($uppsautorizadas == 0) {
                                    array_push($arrayErrores, 'La upp: ' . $u . ' no esta en la lista de upps autorizadas para carga masiva RH. ');

                                }


                                $valupp = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 0)->count();

                                if ($valupp > 0) {
                                    $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '==', 'UUU')->where('estado', 0)->where('ejercicio', $ejercicio[0])->forceDelete();
                                }
                                $confirmadas = ProgramacionPresupuesto::select()->where('subprograma_presupuestario', '==', 'UUU')->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                if ($confirmadas > 0) {
                                    array_push($arrayErrores, 'No se puede realizar carga masiva porque hay claves  confirmadas para la upp: ' . $u . ' ');

                                }
                            }
                            $b = array(
                                "username" => $usuario->username,
                                "accion" => 'Borrar registros carga masiva',
                                "modulo" => 'Claves presupuestales'
                            );
                            Controller::bitacora($b);
                            break;


                    }
                }







            }
            if (count($arrayErrores) > 0) {
                DB::rollBack();

                $payload = json_encode($arrayErrores);
                carga_masiva_estatus::create([
                    'id_usuario' => $usuario->id,
                    'cargapayload' => $payload,
                    'cargaMasClav' => 2,
                    'created_user' => $usuario->username
                ]);
                // event(new ActualizarSesionUsuario($usuario, $arrayErrores,2));

            } else {
                DB::commit();
                CargaMasivaClaves::dispatch($this->filearray, $usuario)->onQueue('high');

            }


        } catch (\Throwable $th) {
            DB::rollBack();
            // event(new ActualizarSesionUsuario($usuario, $th->getMessage(),2));
            \Log::debug($th);
            carga_masiva_estatus::create([
                'id_usuario' => $usuario->id,
                'cargapayload' => json_encode($th),
                'cargaMasClav' => 2,
                'created_user' => $usuario->username
            ]);

        }
    }
}
