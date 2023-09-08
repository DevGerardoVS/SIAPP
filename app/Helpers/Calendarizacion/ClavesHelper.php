<?php // Code within app\Helpers\MetasHelper.php

namespace App\Helpers\Calendarizacion;

use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProgramacionPresupuesto;
use App\Http\Controllers\Calendarizacion\MetasController;
use Auth;
class ClavesHelper{
	
    public static function validaEjercicio($reqEjercicio, $clvUpp){
        try {
			$uppUsuario = Auth::user()->clv_upp;
			$ejer = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE('cierre_ejercicio_claves.estatus','=','Abierto')->where('clv_upp','=' , $clvUpp ? $clvUpp : $uppUsuario)->first();
            $ejercicio = $ejer && $ejer != null ? $ejer->ejercicio : '';
            $response = $reqEjercicio != $ejercicio ?  true :  false;
			return $response;
        } catch(\Exception $exp) {
            Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }
    public static function claveExist ($request) {
        $clave = ProgramacionPresupuesto::where([
            'clasificacion_administrativa' => $request->data[0]['clasificacionAdministrativa'],
            'entidad_federativa' => $request->data[0]['entidadFederativa'],
            'region' => $request->data[0]['region'],
            'municipio' => $request->data[0]['municipio'],
            'localidad' => $request->data[0]['localidad'],
            'upp' => $request->data[0]['upp'],
            'subsecretaria' => $request->data[0]['subsecretaria'],
            'ur' => $request->data[0]['ur'],
            'finalidad' => $request->data[0]['finalidad'],
            'funcion' => $request->data[0]['funcion'],
            'subfuncion' => $request->data[0]['subfuncion'],
            'eje' => $request->data[0]['eje'],
            'linea_accion' => $request->data[0]['lineaAccion'],
            'programa_sectorial' => $request->data[0]['programaSectorial'],
            'tipologia_conac' => $request->data[0]['conac'],
            'programa_presupuestario' => $request->data[0]['programaPre'],
            'subprograma_presupuestario' => $request->data[0]['subPrograma'],
            'proyecto_presupuestario' => $request->data[0]['proyectoPre'],
            'periodo_presupuestal' => $request->data[0]['mesAfectacion'],
            'posicion_presupuestaria' => $request->data[0]['capitulo'] . $request->data[0]['concepto'] . $request->data[0]['partidaGen'] . $request->data[0]['partidaEpecifica'],
            'tipo_gasto' => $request->data[0]['tipoGasto'],
            'anio' => $request->data[0]['anioFondo'],
            'etiquetado' => $request->data[0]['etiquetado'],
            'fuente_financiamiento' => $request->data[0]['fuenteFinanciamiento'],
            'ramo' => $request->data[0]['ramo'],
            'fondo_ramo' => $request->data[0]['fondoRamo'],
            'capital' => $request->data[0]['capital'],
            'proyecto_obra' => $request->data[0]['proyectoObra'],
            'ejercicio' =>  $request->ejercicio,
        ])->get();
        $response = count($clave)> 0 ? true : false;
        return $response;
    }
    public static function tieneMetas($request,$tipo){
        $metas = MetasController::cmetasUpp($tipo == 1 ? $request->data[0]['upp'] : $request->data[0]['clvUpp'],$tipo == 1 ? $request->ejercicio : $request->data[0]['ejercicio'] );
        if ($metas['status']) {
            if ($tipo == 1) {
    
                $clave = ProgramacionPresupuesto::where([
                    'upp' => $tipo == 1 ? $request->data[0]['upp'] : $request->data[0]['clvUpp'],
                    'finalidad' => $request->data[0]['finalidad'],
                    'funcion' => $request->data[0]['funcion'],
                    'subfuncion' => $request->data[0]['subfuncion'],
                    'eje' => $request->data[0]['eje'],
                    'linea_accion' => $request->data[0]['lineaAccion'],
                    'programa_sectorial' => $request->data[0]['programaSectorial'],
                    'tipologia_conac' => $request->data[0]['conac'],
                    'programa_presupuestario' => $request->data[0]['programaPre'],
                    'subprograma_presupuestario' => $request->data[0]['subPrograma'],
                    'proyecto_presupuestario' => $request->data[0]['proyectoPre'],
                    'ejercicio' =>  $request->ejercicio,
                    'deleted_at'=>null,
                    'fondo_ramo' => $request->data[0]['fondoRamo']
                ])->get();
                if (!count($clave)) {
                    $desconfirmado = Metascontroller::desconfirmar($tipo == 1 ? $request->data[0]['upp'] : $request->data[0]['clvUpp'],$request->ejercicio);
                }
            }else {
                $desconfirmado = Metascontroller::desconfirmar($tipo == 1 ? $request->data[0]['upp'] : $request->data[0]['clvUpp'],$tipo == 1 ? $request->ejercicio : $request->data[0]['ejercicio']);
            }
            
        }

    }

	
}