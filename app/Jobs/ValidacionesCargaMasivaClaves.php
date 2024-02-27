<?php

namespace App\Jobs;

use App\Models\notificaciones;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\uppautorizadascpnomina;
use App\Helpers\Calendarizacion\MetasHelper;
use App\Models\ProgramacionPresupuesto;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Calendarizacion\MetasDelController;
use App\Jobs\CargaMasivaClaves;
use DB;
use App\Events\NotificacionCreateEdit;

class ValidacionesCargaMasivaClaves implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filearray;
    protected $user;
    protected $tipocarga;

    public function __construct($filearray, $user, $tipocarga)
    {
        $this->filearray = $filearray;
        $this->user = $user;
        $this->tipocarga = $tipocarga;

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
                    if (
                        array_key_exists($k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26'], $arrayclaves)
                    ) {

                    } else {
                        $arrayclaves[$k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26']] = $currentrow;

                    }

                    array_push($ejercicio, '20' . $k['20']);

                    //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                    if ($var === 0) {
                        $var = true;
                    }

                    if (!$var && strlen($k['5']) == 3) {
                        array_push($arrayupps, $k['5']);
                    }
                }



                if (count($arrayErrores) < 1) {


                    //validacion para eliminar registros tipo admin
                    foreach ($arrayupps as $u) {

                        //buscar en el array de totales 
                        $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $u)->count();
                        if ($uppsautorizadas) {
                            array_push($arrayErrores, ' $ $No se puede cargar la upp: ' . $u . ' en tipo operativo porque esta autorizada para cargar RH. ');
                        }
                        $query = MetasHelper::actividades($u, $ejercicio[0]);
                        //MetasDelController::checkConfirmadas
                        if (count($query) > 0) {
                            array_push($arrayErrores, ' $ $No se pueden añadir claves porque ya hay metas registradas. ');

                        }

                        $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();

                        if ($confirmadas > 0) {
                            array_push($arrayErrores, ' $ $No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas. ');

                        }


                        if ($this->tipocarga == 1) {
                            $deleted = ProgramacionPresupuesto::where('upp', $u)->where('tipo', 'Operativo')->where('ejercicio', $ejercicio[0])->where('estado', 0)->delete();
                        }
                    }
                    if ($this->tipocarga == 1) {
                        $b = array(
                            "username" => $usuario->username,
                            "accion" => 'Borrar registros carga masiva',
                            "modulo" => 'Claves presupuestales'
                        );
                        Controller::bitacora($b);
                    }

                }



            }
            //Validaciones para usuarios upps 
            else {
                $tipousuario = $usuario->id_grupo;
                $uppsautorizadas = 0;
                if ($usuario->id_grupo == 4) {
                    $uppsautorizadas = uppautorizadascpnomina::where('clv_upp', $uppUsuario)->count();
                }

                $arrayupps = array();
                $arraypresupuesto = array();


                if ($tipousuario != 1) {
                    $ejercicio = array();
                }
                $countO = 0;
                $countR = 0;
                foreach ($this->filearray as $indextu => $k) {
                    //buscar en el array de upps 
                    $currentrow = $indextu + 2;

                    $var = array_search($k['5'], $arrayupps);


                    switch ($k['16']) {

                        case 'UUU':
                            $countR++;
                            if ($tipousuario == 5 || $uppsautorizadas) {
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
                            if ($tipousuario == 5) {
                                $countO++;
                                array_push($ejercicio, '20' . $k['20']);

                            }

                    }
                    if (
                        array_key_exists($k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26'], $arrayclaves)
                    ) {

                    } else {
                        $arrayclaves[$k['0'] . $k['1'] . $k['2'] . $k['3'] . $k['4'] . $k['5'] . $k['6'] . $k['7'] . $k['8'] . $k['9']
                            . $k['10'] . $k['11'] . $k['12'] . $k['13'] . $k['14'] . $k['15'] . $k['16'] . $k['17'] . $k['18'] . $k['19'] . $k['21']
                            . $k['22'] . $k['23'] . $k['24'] . $k['25'] . $k['26']] = $currentrow;

                    }






                    //Se revisa el valor de var si es 0 significa que existe el key 0 en el array se usa el if para cambiar el valor para evitar que la condicion falle
                    if ($var === 0) {
                        $var = true;
                    }
                    if (strlen($k['24']) != 2) {
                    }
                    if (!$var && strlen($k['5']) == 3) {
                        array_push($arrayupps, $k['5']);
                    }

                }




                switch ($tipousuario) {
                    case 4:

                        switch ($uppsautorizadas) {
                            case 0:
                                $confirmadasC = 0;

                                //validacion para eliminar registros no confirmados 
                                foreach ($arrayupps as $u) {

                                    if ($u != $uppUsuario) {
                                        array_push($arrayErrores, ' $ $No tiene permiso para registrar de  la upp: ' . $u);
                                    }

                                    $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('tipo', 'Operativo')->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                    if ($confirmadas > 0) {
                                        $confirmadasC++;
                                    }

                                    $query = MetasHelper::actividades($u, $ejercicio[0]);
                                    if (count($query) > 0) {
                                        array_push($arrayErrores, ' $ $No se pueden añadir claves porque ya hay metas registradas. en la upp: ' . $u);
                                    }

                                    if ($this->tipocarga == 1) {
                                        $deleted = ProgramacionPresupuesto::where('upp', $u)->where('estado', 0)->where('tipo', 'Operativo')->where('ejercicio', $ejercicio[0])->delete();
                                    }
                                }
                                if ($confirmadasC > 0) {
                                    array_push($arrayErrores, ' $ $No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas. ');

                                }
                                if ($this->tipocarga == 1) {
                                    $b = array(
                                        "username" => $usuario->username,
                                        "accion" => 'Borrar registros carga masiva',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);
                                }



                                break;

                            case 1:
                                if ($countR > 0) {
                                    array_push($arrayErrores, '$ $Hay claves de RH en el archivo de cargas masivas ');
                                }
                                $confirmadasC = 0;
                                //validacion para eliminar registros no confirmados 
                                foreach ($arrayupps as $u) {
                                    if ($u != $uppUsuario) {
                                        array_push($arrayErrores, ' $ $: No tiene permiso para registrar de  la upp: ' . $u);
                                    }
                                    $query = MetasHelper::actividades($u, $ejercicio[0]);
                                    if (count($query) > 0) {
                                        array_push($arrayErrores, ' $ $No se pueden añadir claves porque ya hay metas registradas. ');
                                    }

                                    $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('subprograma_presupuestario', '!=', 'UUU')->where('tipo', 'Operativo')->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                    if ($confirmadas > 0) {
                                        $confirmadasC++;
                                    }

                                    if ($this->tipocarga == 1) {
                                        $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', '!=', 'UUU')->where('tipo', 'Operativo')->where('estado', 0)->where('ejercicio', $ejercicio[0])->delete();
                                    }
                                }
                                if ($confirmadasC > 0) {
                                    array_push($arrayErrores, ' $ $No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas. ');

                                }

                                if ($this->tipocarga == 1) {
                                    $b = array(
                                        "username" => $usuario->username,
                                        "accion" => 'Borrar registros carga masiva',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);
                                }
                                break;
                        }


                        break;

                    case 5:
                        //validacion para eliminar registros no confirmados 
                        foreach ($arrayupps as $key => $u) {
                            //nueva funcion aca
                            if ($countO == 0) {
                                $confirmadas = ProgramacionPresupuesto::select()->where('upp', $u)->where('subprograma_presupuestario', 'UUU')->where('tipo', 'RH')->where('estado', 1)->where('ejercicio', $ejercicio[0])->count();
                                if ($confirmadas > 0) {
                                    array_push($arrayErrores, ' $ $No se pueden añadir más claves por carga masiva a la upp: ' . $u . ' porque ya tiene claves confirmadas. ');
                                }

                                if ($this->tipocarga == 1) {
                                    $deleted = ProgramacionPresupuesto::where('upp', $u)->where('subprograma_presupuestario', 'UUU')->where('tipo', 'RH')->where('estado', 0)->where('ejercicio', $ejercicio[0])->delete();
                                }

                                $query = MetasDelController::actividadesCargaMasDel($u, $usuario->username, $ejercicio[0]);
                                if (count($query) > 0) {

                                    foreach ($query as $key => $value) {
                                        DB::table('metas')->where('id', '=', $value->idm)->delete();
                                    }

                                    $b = array(
                                        "username" => $usuario->username,
                                        "accion" => 'Borrar registros de metas de usuario delegacion en carga masiva.',
                                        "modulo" => 'Claves presupuestales'
                                    );
                                    Controller::bitacora($b);
                                }
                            } else {
                                array_push($arrayErrores, ' $ $Hay programas que no son UUU en el archivo.');

                            }

                        }
                        if ($this->tipocarga == 1) {
                            $bcarga = array(
                                "username" => $usuario->username,
                                "accion" => 'Borrar registros carga masiva',
                                "modulo" => 'Claves presupuestales'
                            );
                            Controller::bitacora($bcarga);
                        }


                        break;


                }








            }
            if (count($arrayErrores) > 0) {
                DB::rollBack();


                $payload = json_encode($arrayErrores);
                $payloadsent = json_encode(
                    array(
                        "TypeButton" => 1,
                        "route" => "'/calendarizacion/download-errors-excel'",
                        "mensaje" => trans('messages.carga_masiva_error'),
                        "payload" => $payload
                    )
                );
                notificaciones::where('id_usuario', $usuario->id)
                    ->update([
                        'payload' => $payloadsent,
                        'status' => 2,
                        'updated_user' => $usuario->username
                    ]);
                    $datos = notificaciones::where('id_usuario', $usuario->id)->first();

                    event(new NotificacionCreateEdit($datos));


            } else {
                DB::commit();
                CargaMasivaClaves::dispatch($this->filearray, $usuario, $this->tipocarga)->onQueue('high');

            }


        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::debug($th);
            $error = array();
            array_push($error, ' $ $Ocurrio un error interno contacte a soporte.');
            $error = json_encode($error);
            $payloadsent = json_encode(
                array(
                    "TypeButton" => 1,
                    "route" => "'/calendarizacion/download-errors-excel'",
                    "mensaje" => trans('messages.carga_masiva_error'),
                    "payload" => $error
                )
            );
            notificaciones::where('id_usuario', $usuario->id)
                ->update([
                    'payload' => $payloadsent,
                    'status' => 2,
                    'updated_user' => $usuario->username
                ]);
                $datos = notificaciones::where('id_usuario', $usuario->id)->first();
                event(new NotificacionCreateEdit($datos));

        }
    }
}
