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
                ->select('clave', 'descripcion','tipo')
                ->rightJoin('catalogo','tipo_actividad_upp.clv_upp','=','catalogo.clave')
                ->where('grupo_id', 6)
                ->where($array_where)
                ->get();


            foreach ($data as $d) {
                //$d->tipo 
                $ds = array($d->clave , $d->descripcion, 
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></div>',
                '<div class="form-check"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></div>');
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
}
