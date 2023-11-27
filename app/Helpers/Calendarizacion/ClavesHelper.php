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
        if (!$metas['status']) {
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
        $whereCierre = [];
        $tabla = 'programacion_presupuesto';
        if ($uppUsuario != '') {
            array_push($whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
            array_push($whereCierre, ['cierre_ejercicio_claves.estatus', '=', 'Abierto']);
        $ejercicioActual = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE($whereCierre)->first();
            if ($anio < $ejercicioActual->ejercicio) {
                $tabla = 'programacion_presupuesto_hist';
            }
        $presupuestoOperativo = DB::table('techos_financieros')
        ->SELECT(DB::raw('SUM(presupuesto) as totalAsignado'))
        ->where(function ($presupuestoOperativo) use ($clv_upp,$anio) {
            $presupuestoOperativo->where('tipo','=','Operativo')->where('ejercicio','=',$anio)->where('deleted_at','=',null);
            if ($clv_upp && $clv_upp != '') {
                $presupuestoOperativo->where('techos_financieros.clv_upp', '=', $clv_upp);   
            }
        })
        ->get();
        $calendarizadoOperativo = DB::table($tabla)
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizado'))
        ->where(function ($calendarizadoOperativo) use ($clv_upp,$anio, $tabla) {
            $deleted = [];
            if ($tabla == 'programacion_presupuesto') {
                array_push($deleted, ['deleted_at','=',null]);
            }
            array_push($deleted, ['ejercicio','=',$anio]);
            $calendarizadoOperativo->where('tipo','=','Operativo')->where($deleted);
            if ($clv_upp && $clv_upp != '') {
                $calendarizadoOperativo->where($tabla.'.upp', '=', $clv_upp); 
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
        $whereCierre = [];
        $tabla = 'programacion_presupuesto';
        if ($uppUsuario != '') {
            array_push($whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
            array_push($whereCierre, ['cierre_ejercicio_claves.estatus', '=', 'Abierto']);
        $ejercicioActual = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE($whereCierre)->first();
            if ($anio < $ejercicioActual->ejercicio) {
                $tabla = 'programacion_presupuesto_hist';
            }
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
        $calendarizadoRH = DB::table($tabla)
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizado'))
        ->where(function ($calendarizadoRH) use ($clv_upp,$anio,$rol,$tabla) {
            $deleted = [];
            if ($tabla == 'programacion_presupuesto') {
                array_push($deleted, ['deleted_at','=',null]);
            }
            array_push($deleted, ['ejercicio','=',$anio]);
            $calendarizadoRH->where('tipo','=','RH')->where($deleted);
            if ($clv_upp && $clv_upp != '') {
                $calendarizadoRH->where($tabla.'.upp', '=', $clv_upp); 
            }
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $calendarizadoRH->whereIn($tabla.'.upp',$arrayClaves);
                $calendarizadoRH->where($tabla.'.tipo', '=', 'RH');
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
    public static function esConfirmable($upp,$ejercicio){
        $Totcalendarizado = 0;
        $disponible = 0;
        $rol = '';
        $perfil = Auth::user()->id_grupo;
        switch ($perfil) {
            case 1:
                // rol administrador
                $rol = 0;
                break;
            case 4:
                // rol upp
                $rol = 1;
                break;
            case 5:
                // rol delegacion
                $rol = 2;
                break;
            default:
                // rol auditor y gobDigital
                $rol = 3;
                break;
        }
        $array_where = [];
        $array_where2 = [];
        $array_whereCierre = [];
        $autorizado = ClavesHelper::esAutorizada($upp);
            if ($autorizado) {
                array_push($array_where, ['techos_financieros.tipo', '=', 'Operativo']);
                array_push($array_where2, ['programacion_presupuesto.tipo', '=', 'Operativo']);
            }
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $ejercicio]);
            array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $ejercicio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $ejercicio]);
            if ($upp != '') {
                array_push($array_where, ['techos_financieros.clv_upp', '=', $upp]);
                array_push($array_where2, ['programacion_presupuesto.upp', '=', $upp]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $upp]);
            }
        $estatusCierre = DB::table('cierre_ejercicio_claves')
        ->SELECT('ejercicio','estatus')
        ->WHERE($array_whereCierre)
        ->first();

        $presupuestoAsignado = DB::table('techos_financieros')
        ->SELECT(DB::raw('SUM(presupuesto) as totalAsignado'))
        ->where(function ($presupuestoAsignado) use ($rol,$array_where) {
            $presupuestoAsignado->where($array_where);
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $presupuestoAsignado->whereIn('techos_financieros.clv_upp',$arrayClaves);
                $presupuestoAsignado->where('techos_financieros.tipo', '=', 'RH');
            }
        })
        ->get();
        $calendarizados = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizados'),'estado')
        ->where(function ($calendarizados) use ($rol,$array_where2) {
            $calendarizados->where($array_where2);
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $calendarizados->whereIn('programacion_presupuesto.upp',$arrayClaves);
                $calendarizados->where('programacion_presupuesto.tipo', '=', 'RH');
            }
        })
        ->get();
        foreach ($calendarizados as $key => $value) {
            $Totcalendarizado = $Totcalendarizado + $value->calendarizados;
        }
        if ($Totcalendarizado != 0 ) {
            $disponible = $presupuestoAsignado[0]->totalAsignado - $Totcalendarizado;
        }else {
            $disponible = $presupuestoAsignado[0]->totalAsignado;
        }
        $where = [];
        if ($rol == 1 || $rol == 0) {
            array_push($where, ['tipo','Operativo']);
        }
        if ($rol == 2) {
            array_push($where, ['tipo','RH']);
        }
        $estado = DB::table('programacion_presupuesto')
        ->SELECT('*')->where('upp', $upp)->where('ejercicio', $ejercicio)->where('estado', 1)->where('deleted_at','=',null)->where($where)->get();
        if ($disponible > 0 || count($estado)) {
            return false;
        }else{
            return true;
        }
    }
    public static function detallePresupuestoDelegacion($arrayTechos,$arrayProgramacion,$tabla){
        $fondos = DB::select("select 
        clv_fondo,
        f.fondo_ramo,
        sum(RH) RH,
        sum(Operativo) Operativo,
        sum(RH) techos_presupuestal,
        sum(calendarizado) calendarizado,
        sum(RH - calendarizado) disponible,
        ejercicio
        from (
            select 
                clv_fondo,
                sum(presupuesto) RH,
                0 Operativo,
                0 calendarizado,
                ejercicio
            from techos_financieros tf
            where tf.tipo = 'RH' and tf.clv_upp IN (select uppautorizadascpnomina.clv_upp from uppautorizadascpnomina where uppautorizadascpnomina.deleted_at is null) && ".$arrayTechos."
            group by clv_fondo,ejercicio
            union all 
            select 
                fondo_ramo clv_fondo,
                0 RH,
                0 Operativo,
                sum(total) calendarizado,
                ejercicio
            from ".$tabla." pp
            where pp.tipo = 'RH' and pp.upp IN (select uppautorizadascpnomina.clv_upp from uppautorizadascpnomina where uppautorizadascpnomina.deleted_at is null) && ".$arrayProgramacion."
            group by clv_fondo,ejercicio
        ) tabla
        join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
        group by clv_fondo,f.fondo_ramo,ejercicio;");

        return $fondos;
    }
    public static function detallePresupuestoAutorizadas($arrayTechos,$arrayProgramacion,$tabla){
        $fondos = DB::select("
        select 
            clv_fondo,
            f.fondo_ramo,
            0 RH,
            sum(Operativo) Operativo,
            sum(Operativo) techos_presupuestal,
            sum(calendarizado) calendarizado,
            sum(Operativo) - calendarizado disponible,
            ejercicio
        from (
            select 
                clv_fondo,
                0 RH,
                sum(presupuesto) Operativo,
                0 calendarizado,
                ejercicio
            from techos_financieros tf
            where tf.tipo = 'Operativo' && ".$arrayTechos."
            group by clv_fondo
            union all 
            select 
                fondo_ramo clv_fondo,
                0 RH,
                0 Operativo,
                sum(total) calendarizado,
                ejercicio
            from ".$tabla." pp
            where pp.tipo = 'Operativo' && ".$arrayProgramacion."
            group by clv_fondo
        ) tabla
        join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
        group by clv_fondo,f.fondo_ramo;");

        return $fondos;
    }
    public static function detallePresupuestoGeneral($arrayTechos,$arrayProgramacion,$tabla){
            $fondos = DB::select("select 
            clv_fondo,
            f.fondo_ramo,
            sum(RH) RH,
            sum(Operativo) Operativo,
            sum(RH+Operativo) techos_presupuestal,
            sum(calendarizado) calendarizado,
            sum((RH+Operativo)-calendarizado) disponible,
            ejercicio
        from (
            select 
                clv_fondo,
                sum(presupuesto) RH,
                0 Operativo,
                0 calendarizado,
                ejercicio
            from techos_financieros tf
            where tf.tipo = 'RH' &&".$arrayTechos." 
            group by clv_fondo
            union all
            select 
                clv_fondo,
                0 RH,
                sum(presupuesto) Operativo,
                0 calendarizado,
                ejercicio
            from techos_financieros tf
            where tf.tipo = 'Operativo' &&".$arrayTechos." 
            group by clv_fondo
            union all 
            select 
                fondo_ramo clv_fondo,
                0 RH,
                0 Operativo,
                sum(total) calendarizado,
                ejercicio
            from ".$tabla." pp
            where ".$arrayProgramacion."
            group by clv_fondo
        ) tabla
        join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
        group by clv_fondo,f.fondo_ramo;");

        return $fondos;
    }

}