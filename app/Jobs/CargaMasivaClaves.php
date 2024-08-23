<?php

namespace App\Jobs;

use App\Models\notificaciones;
use Illuminate\Bus\Queueable;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\NotificacionCreateEdit;
use DB;

class CargaMasivaClaves implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filearray;
    protected $user;
    protected $tipocarga;

    protected $id;
 
    public function __construct($filearray, $user, $tipocarga, $id )
    {
        $this->filearray = $filearray;
        $this->user = $user;
        $this->tipocarga = $tipocarga;
         $this->id = $id;
     }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //si todo sale bien procedemos al import
        try {
            $usuario = $this->user;

            $arrayErrores = array();
            $tipoclave = '';
            $añoclave = 0;
            $currentrow = 2;
            $storeP_array = array();
            DB::beginTransaction();
            //validacion de año 
            if (strlen($this->filearray['0']['20']) == 2 && is_numeric($this->filearray['0']['20'])) {
                $year = '20' . $this->filearray['0']['20'];
                $añoclave = $year;
            } else {
                if ($this->filearray['0']['20'] != 2) {
                    $añoclave = 2024;
                }

            }

            foreach ($this->filearray as $k) {

                DB::table('programacion_presupuesto_aux')->insert([
                    'id' => $currentrow,
                    'id_carga' => $usuario->id,
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
                ]);

                $currentrow++;

            }
            $arrayErrores = DB::select("CALL validacion_claves(" . $usuario->id . ", '" . $usuario->username . "'," . $this->tipocarga . ")");
            if (count($arrayErrores) > 0) {
                DB::rollBack();

                foreach ($arrayErrores as $key0 => $error) {

                    foreach ($error as $key1 => $err) {
                        switch ($key1) {

                            case 'num_linea':
                                $storeP_array[$key0] = $err;

                                break;
                            case 'modulo':
                                $storeP_array[$key0] = $storeP_array[$key0] . "$" . $err;
                                break;
                            case 'error':
                                $storeP_array[$key0] = $storeP_array[$key0] . "$" . $err;
                                break;
                        }
                    }
                }
                $payload = json_encode($storeP_array);
                $payloadsent = json_encode(
                    array(
                        "TypeButton" => 1,
                        "route" => "'/calendarizacion/download-errors-excel'",
                        "blocked" => 3,
                        "mensaje" => trans('messages.carga_masiva_error'),
                        "payload" => $payload
                    )
                );
                notificaciones::where('id', $this->id)
                    ->update([
                        'payload' => $payloadsent,
                        'status' => 2,
                        'updated_user' => $usuario->username
                    ]);
               /* event(new NotificacionCreateEdit($notification)); */
 /*                 $notification = json_encode([
                    'id' => $datos->id

                ]); */ 
    /* event(new NotificacionCreateEdit($notification)); */
            } else {
                $payloadsent = json_encode(
                    array(
                        "TypeButton" => 0,
                        "route" => "'/borrar-sesion_sesion_notificacion'",
                        "blocked" => 3,
                        "mensaje" => trans('messages.carga_masiva_exito'),
                        "payload" => ""
                    )
                );
                DB::commit();
                notificaciones::where('id', $this->id)
                    ->update([
                        'payload' => $payloadsent,
                        'status' => 1,
                        'updated_user' => $usuario->username
                    ]);
                /*

               /*  $notification = json_encode([
                    'id' => $datos->id

                ]); */
    /* event(new NotificacionCreateEdit($notification)); */
            }



            $b = array(
                "username" => $usuario->username,
                "accion" => 'Carga masiva',
                "modulo" => 'Claves presupuestales'
            );
            Controller::bitacora($b);


        } catch (\Exception $e) {
            DB::rollBack();
            $arrayfail = array();
            array_push($arrayErrores, ' $ $Ocurrio un error interno contacte a soporte.');
            $payload = json_encode($arrayfail);
            $error = $e->getMessage();
            $payloadsent = json_encode(
                array(
                    "TypeButton" => 1,
                    "route" => "'/calendarizacion/download-errors-excel'",
                    "blocked" => 3,
                    "mensaje" => trans('messages.carga_masiva_error'),
                    "payload" => $error
                )
            );
            notificaciones::where('id', $this->id)
                ->update([
                    'payload' =>  $payloadsent,
                    'status' => 2,
                    'updated_user' => $usuario->username
                ]);
           /* event(new NotificacionCreateEdit($notification)); */
/*             $notification = json_encode([
                'id' => $datos->id

            ]); */
/* event(new NotificacionCreateEdit($notification)); */

        }
    }
}
