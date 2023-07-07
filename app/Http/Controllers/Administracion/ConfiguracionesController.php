<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\administracion\TipoActividadUpp;

class ConfiguracionesController extends Controller
{
    //
    public function getIndex()
	{
		return view('administracion.configuraciones.index');
	}

    public function GetUpps(){
        try {
            $dataSet = array();

            $dataSet = DB::table('catalogo')
                ->select('clave', 'descripcion')
                ->where('grupo_id', 6)
                ->get();

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function GetConfiguraciones(Request $request){
        try {
            $dataSet = array();
            $array_where = [];
            $filter = $request->filter;

            if($filter!=null || $filter!='') array_push($array_where, ['clave','=',$filter]);

            $data = DB::table('tipo_actividad_upp')
                ->select('clave', 'descripcion','Acumulativa','Continua','Especial')
                ->join('catalogo','tipo_actividad_upp.clv_upp','=','catalogo.clave')
                ->where('grupo_id', 6)
                ->where($array_where)
                ->get();

            $i=0;
            foreach ($data as $d) {
                //$d->tipo 
                $acumulativa = $d->Acumulativa==1? ' checked' : '';
                $continua = $d->Continua==1? ' checked' : '';
                $especial = $d->Especial==1? ' checked' : '';

                $ds = array($d->clave , $d->descripcion, 
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData('.$d->clave.',\'acumulative\')" id="'.$d->clave.'"'.$acumulativa.'></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData('.$d->clave.',\'continue\')" id="'.$d->clave.'"'.$continua.'></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" onclick="updateData('.$d->clave.',\'especial\')" id="'.$d->clave.'"'.$especial.'></div>');
                $dataSet[] = $ds;
            }

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function updateUpps(Request $request){
        try {
            $dataSet = array();
            $array_where = [];

           $tipo_actividad = TipoActividadUpp::where()->first();
           

            return response()->json([
                "dataSet" => $dataSet,
                "catalogo" => "Configuraciones",
            ]);

        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
}
