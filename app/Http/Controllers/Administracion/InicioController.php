<?php

namespace App\Http\Controllers\Administracion;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class InicioController extends Controller
{
    //
    public static function GetInicioA(){
        try {
            $anio_act = date('Y')-1;
            $dataSet = array();
            $data = DB::table('inicio_a')->get();

            foreach ($data as $d) {
                $ds = array($d->presupuesto_asignado, $d->presupuesto_calendarizado, $d->disponible,$d->avance);
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "inicio",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function GetInicioB(){
        try {
            $anio_act = date('Y')-1;
            $dataSet = array();
            $data = DB::table('inicio_b')->get();

            foreach ($data as $d) {
                $ds = array($d->clave, $d->fondo, $d->asignado, $d->programado, $d->avance);
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "inicio",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

}
