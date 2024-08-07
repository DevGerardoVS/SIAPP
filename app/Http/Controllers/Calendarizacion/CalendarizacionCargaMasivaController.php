<?php

namespace App\Http\Controllers\Calendarizacion;

use App\Events\NotificacionCreateEdit;
use App\Exports\ImportErrorsExport;
use App\Http\Controllers\Controller;
use App\Models\notificaciones;
use App\Jobs\ValidacionesCargaMasivaClaves;
use Illuminate\Http\Request;
use App\Exports\PlantillaExport;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Auth;
use Shuchkin\SimpleXLSX;


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


    public function DownloadErrors()
    {

        $fails = 0;

        if (session()->has('payload')) {
            $fails = json_decode(session::get('payload'));

        }
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descarga',
            "modulo" => 'Errores carga masiva'
        );
        session()->forget(['payload', 'mensaje', 'route']);

        Session::put('status', 3);
        Session::put("blocked", 3);


        Controller::bitacora($b);
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();

        $deleted = notificaciones::where('id_usuario', '=', Auth::user()->id)
            ->where('id_sistema', '=', 1)
            ->forceDelete();

        return Excel::download(new ImportErrorsExport($fails), 'Errores.xlsx');
    }




    //Obtener datos del excel
    public function loadDataPlantilla(Request $request)
    {

        $tipocarga = 0;

        if ($request->tipo) {
            $tipocarga = $request->tipo;
        } elseif ($request->tipo_adm) {
            $tipocarga = $request->tipo_adm;
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

        $user = Auth::user();

        if ($xlsx = SimpleXLSX::parse($request->file)) {
            $filearray = $xlsx->rows();
            //tomamos los encabezados
            $encabezados = array_shift($filearray);
            //Los convertimos todos a lowecase
            $encabezadosMin = array_map('strtolower', $encabezados);
            $encabezadosMin = array_filter($encabezadosMin, 'strlen');
            //Verificamos si hay diferencia entre lo que debe ser y lo que mandaron
            $equals = array_diff($encabezadosMin, $arrayCampos);
            if (count($equals) > 0) {
                return redirect()->back()->withErrors('Error: No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados. ');
                /*        $payloadsent = json_encode(
                            array(
                                "TypeButton" => 0,
                                "route" => "",
                                "blocked" => 3,
                                "mensaje" => ' Error: No es la plantilla o fue editada. Favor de solo usar la plantilla sin modificar los encabezados.',
                                "payload" => ""
                            )
                        );
                        $datos = notificaciones::create([
                            'id_usuario' => $user->id,
                            'id_sistema' => 1,
                            'payload' => $payloadsent,
                            'status' => 2,
                            'created_user' => $user->username
                        ]);
                        
                         $notification = json_encode([
                            'id' => $datos->id

                        ]); */
                /* event(new NotificacionCreateEdit($notification)); */
            }
            if (count($filearray) <= 0) {
                return redirect()->back()->withErrors('Error: El excel esta vacio. ');
                /*           $payloadsent = json_encode(
                             array(
                                 "TypeButton" => 0,
                                 "route" => "",
                                 "blocked" => 3,
                                 "mensaje" => ' El excel esta vacio.',
                                 "payload" => ""
                             )
                         );
                         $datos = notificaciones::create([
                             'id_usuario' => $user->id,
                             'id_sistema' => 1,
                             'payload' => $payloadsent,
                             'status' => 2,
                             'created_user' => $user->username
                         ]);
                        $notification = json_encode([
                             'id' => $datos->id

                         ]); */
                /* event(new NotificacionCreateEdit($notification)); */
            }
        }





        $filearray = array_map('self::nestedtrim', $filearray);
        $tienecargapen = notificaciones::where('id_usuario', $user->id)->first();


        if ($tienecargapen) {
            return redirect()->back()->withErrors('Ya tienes una carga masiva en proceso ');
            /*           $payloadsent = json_encode(
                           array(
                               "TypeButton" => 0,
                               "route" => "",
                               "blocked" => 3,
                               "mensaje" => trans('messages.carga_masiva_proceso'),
                               "payload" => ""
                           )
                       );
                       $datos = notificaciones::create([
                           'id_usuario' => $user->id,
                           'id_sistema' => 1,
                           'payload' => $payloadsent,
                           'status' => 0,
                           'created_user' => $user->username
                       ]);
                        $notification = json_encode([
                           'id' => $datos->id

                       ]); */
            /* event(new NotificacionCreateEdit($notification)); */

        } else {

            $payloadsent = json_encode(
                array(
                    "TypeButton" => 0,// 0 es mensaje, 1 es que si es botton, 2 ahref 
                    "route" => "",
                    "blocked" => 0, // 0 es Carga masiva Calendarizacion, 1 es Reportes SAPP,3 Carga Masiva SAPP
                    "mensaje" => trans('messages.carga_masiva_cargando'),
                    "payload" => ""
                )
            );

            $datos = notificaciones::create([
                'id_usuario' => $user->id,
                'id_sistema' => 1,
                'payload' => $payloadsent,
                'status' => 0,
                'created_user' => $user->username
            ]);
            /*             $notification = json_encode([
                            'id' => $datos->id

                        ]); */
            /* event(new NotificacionCreateEdit($notification)); */

            Session::put('status', 0);
            session::put('blocked', 3);
            ValidacionesCargaMasivaClaves::dispatch($filearray, $user, $tipocarga, $datos->id)->onQueue('high');
            return redirect()->back();

        }



    }


    public static function nestedtrim($value)
    {
        if (is_array($value)) {
            return array_map('self::nestedtrim', $value);
        }
        return trim($value);
    }

}