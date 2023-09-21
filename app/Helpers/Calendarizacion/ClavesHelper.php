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
    public static function getPresupuestooperativo($uppUsuario,$anio,$upp){
        $OpCalendarizado = 0;
        $disponible = 0;
        $clv_upp = $uppUsuario ? $uppUsuario : $upp;
        $presupuestoOperativo = DB::table('techos_financieros')
        ->SELECT(DB::raw('SUM(presupuesto) as totalAsignado'))
        ->where(function ($presupuestoOperativo) use ($clv_upp,$anio) {
            $presupuestoOperativo->where('tipo','=','Operativo')->where('ejercicio','=',$anio)->where('deleted_at','=',null);
            if ($clv_upp && $clv_upp != '') {
                $presupuestoOperativo->where('techos_financieros.clv_upp', '=', $clv_upp);   
            }
        })
        ->get();
        $calendarizadoOperativo = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizado'))
        ->where(function ($calendarizadoOperativo) use ($clv_upp,$anio) {
            $calendarizadoOperativo->where('tipo','=','Operativo')->where('ejercicio','=',$anio)->where('deleted_at','=',null);
            if ($clv_upp && $clv_upp != '') {
                $calendarizadoOperativo->where('programacion_presupuesto.upp', '=', $clv_upp); 
            }
        })
        ->get();
        foreach ($calendarizadoOperativo as $key => $value) {
            $OpCalendarizado = $OpCalendarizado + $value->calendarizado;
        }
        if ($OpCalendarizado != 0 ) {
            $disponible = $presupuestoOperativo[0]->totalAsignado - $OpCalendarizado;
        }else {
            $disponible = $presupuestoOperativo[0]->totalAsignado;
        }
        $resOperativo = [
            'presupuestoOperativo' => $presupuestoOperativo[0]->totalAsignado,
            'operativoCalendarizado' =>  $OpCalendarizado,
            'operativoDisponible' => $disponible,
        ];
        return $resOperativo;
        

    }
    public static function getPresupuestoRH($uppUsuario,$anio,$upp,$rol){
        $RHCalendarizado = 0;
        $disponible = 0;
        $clv_upp = $uppUsuario ? $uppUsuario : $upp;
        $presupuestoRH = DB::table('techos_financieros')
        ->SELECT(DB::raw('SUM(presupuesto) as totalAsignado'))
        ->where(function ($presupuestoRH) use ($clv_upp,$anio,$rol) {
            $presupuestoRH->where('tipo','=','RH')->where('ejercicio','=',$anio)->where('deleted_at','=',null);
            if ($clv_upp && $clv_upp != '') {
                $presupuestoRH->where('techos_financieros.clv_upp', '=', $clv_upp);   
            }
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $presupuestoRH->whereIn('techos_financieros.clv_upp',$arrayClaves);
                $presupuestoRH->where('techos_financieros.tipo', '=', 'RH');
            }
        })
        ->get();
        $calendarizadoRH = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizado'))
        ->where(function ($calendarizadoRH) use ($clv_upp,$anio,$rol) {
            $calendarizadoRH->where('tipo','=','RH')->where('ejercicio','=',$anio)->where('deleted_at','=',null);
            if ($clv_upp && $clv_upp != '') {
                $calendarizadoRH->where('programacion_presupuesto.upp', '=', $clv_upp); 
            }
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $calendarizadoRH->whereIn('programacion_presupuesto.upp',$arrayClaves);
                $calendarizadoRH->where('programacion_presupuesto.tipo', '=', 'RH');
            }
        })
        ->get();
        foreach ($calendarizadoRH as $key => $value) {
            $RHCalendarizado = $RHCalendarizado + $value->calendarizado;
        }
        if ($RHCalendarizado != 0 ) {
            $disponible = $presupuestoRH[0]->totalAsignado - $RHCalendarizado;
        }else {
            $disponible = $presupuestoRH[0]->totalAsignado;
        }
        $resOperativo = [
            'presupuestoRH' => $presupuestoRH[0]->totalAsignado,
            'RHCalendarizado' =>  $RHCalendarizado,
            'RHDisponible' => $disponible,
        ];
        return $resOperativo;
        

    }
    public static function esAutorizada($clvUpp){
        $uppAutorizados = DB::table('uppautorizadascpnomina')
        ->SELECT('clv_upp')
        ->WHERE('deleted_at','=', null)
        ->WHERE('clv_upp','=',$clvUpp)
        ->get();
        if (count($uppAutorizados)>0) {
            return true;
        }
        else {
            return false;
        }
    }

}