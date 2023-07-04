<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ConfiguracionesController extends Controller
{
    //
    public function getIndex()
	{
		return view('administracion.configuraciones.index');
	}

    public static function GetConfiguraciones(){
        try {
            $anio_act = date('Y')-1;
            $dataSet = array();
            $data = DB::table('inicio_a')->get();

            foreach ($data as $d) {
                $ds = array(number_format($d->presupuesto_asignado, 2, '.', ',') , number_format($d->presupuesto_calendarizado, 2, '.', ','), number_format($d->disponible, 2, '.', ',') , number_format($d->avance, 2, '.', ','));
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
