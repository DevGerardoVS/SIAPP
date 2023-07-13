<?php

namespace App\Http\Controllers\Calendarizacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;
use Auth;
use DateTime;
use DataTables;
use App\Models\ProgramacionPresupuesto;

use Illuminate\Validation\ValidationException;

class ClavePreController extends Controller
{
    public function getPanel(){
        $uppUsuario = Auth::user()->clv_upp;
        $ejer = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE('cierre_ejercicio_claves.estatus','=','Abierto')->first();
        $ejercicio = $ejer->ejercicio;
        return view('calendarizacion.clavePresupuestaria.index',compact(['uppUsuario','ejercicio']));
    }
    public function getPanelUpdate($id){
        $clave = ProgramacionPresupuesto::where('id',$id)->first();
        return view('calendarizacion.clavePresupuestaria.updateCalendarzacion', compact('clave'));
    }
    public function getCreate($ejercicio){
        return view('calendarizacion.clavePresupuestaria.create', compact('ejercicio'));
    }
    public function getPanelCalendarizacion(){
        return view('calendarizacion.clavePresupuestaria.calendarizacion');
    }
    public function getClaves(Request $request){
        $uppUsuario =  Auth::user()->clv_upp;
        $array_where = [];
        $array_whereCierre = [];
        $anio = '';
        if ($request->ejercicio && $request->ejercicio != '') {
            $anio = $request->ejercicio;
        }else {
            $anio = date('Y');
        }
        if ($uppUsuario && $uppUsuario != null) {
            array_push($array_where, ['programacion_presupuesto.upp', '=', $uppUsuario]);
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($request->ur && $request->ur != '') {
                array_push($array_where, ['programacion_presupuesto.ur', '=', $request->ur]);
            }
        }else {
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($request->upp && $request->upp != '') {
                array_push($array_where, ['programacion_presupuesto.upp', '=', $request->upp]);
                if ($request->ur && $request->ur != '') {
                    array_push($array_where, ['programacion_presupuesto.ur', '=', $request->ur]);
                }
            }
        }
        $estatusCierre = DB::table('cierre_ejercicio_claves')
        ->SELECT('ejercicio','estatus')
        ->WHERE($array_whereCierre)
        ->first(); 

        $claves = DB::table('programacion_presupuesto')
        ->SELECT('programacion_presupuesto.id','programacion_presupuesto.clasificacion_administrativa','programacion_presupuesto.entidad_federativa','programacion_presupuesto.region','programacion_presupuesto.municipio',
                'programacion_presupuesto.localidad','programacion_presupuesto.upp','programacion_presupuesto.subsecretaria','programacion_presupuesto.ur','programacion_presupuesto.finalidad','programacion_presupuesto.funcion',
                'programacion_presupuesto.subfuncion','programacion_presupuesto.eje','programacion_presupuesto.linea_accion','programacion_presupuesto.programa_sectorial','programacion_presupuesto.tipologia_conac','programacion_presupuesto.programa_presupuestario',
                'programacion_presupuesto.subprograma_presupuestario','proyecto_presupuestario','programacion_presupuesto.periodo_presupuestal','programacion_presupuesto.posicion_presupuestaria',
                'programacion_presupuesto.tipo_gasto','programacion_presupuesto.anio','programacion_presupuesto.etiquetado','programacion_presupuesto.fuente_financiamiento','programacion_presupuesto.ramo','programacion_presupuesto.fondo_ramo',
                'programacion_presupuesto.capital','programacion_presupuesto.proyecto_obra','programacion_presupuesto.ejercicio','programacion_presupuesto.estado',
        (DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre AS totalByClave')),'v_entidad_ejecutora.clv_ur as claveUr','v_entidad_ejecutora.ur as descripcionUr')
        ->leftJoin('v_entidad_ejecutora', function($join)
        {
            $join->on('v_entidad_ejecutora.clv_upp', '=', 'programacion_presupuesto.upp');
            $join->on('v_entidad_ejecutora.clv_subsecretaria','=','programacion_presupuesto.subsecretaria');
            $join->on('v_entidad_ejecutora.clv_ur','=','programacion_presupuesto.ur');
        })
        ->where($array_where)
        ->orderBy('v_entidad_ejecutora.clv_ur');
        if ($request->upp && $request->upp != '' || $uppUsuario && $uppUsuario != null) {
           $claves =  $claves->get();
        }else {
            $claves =  $claves->limit(1000)->get();
        }
        $response = [
            'claves'=> $claves,
            'estatus' => $estatusCierre,
        ];
        return response()->json($response, 200);
    } 
    public function postGuardarClave(Request $request){
        try {
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
         if (count($clave)> 0) {
            return response()->json('duplicado',200);
            throw ValidationException::withMessages(['duplicado'=>'Esta clave ya existe']);
           
         }else {
            $disponible = 0;
            $presupuestoUpp = DB::table('techos_financieros')
            ->SELECT('presupuesto','tipo')
            ->WHERE('clv_upp', '=', $request->data[0]['upp'])
            ->WHERE('clv_fondo', '=', $request->data[0]['fondoRamo'])
            ->WHERE('ejercicio', '=', $request->ejercicio)
            ->WHERE('tipo', '=', $request->data[0]['subPrograma'] != 'UUU' ? 'Operativo' : 'RH' )
            ->WHERE('deleted_at', '=', null)
            ->first();
            $presupuestoAsignado = DB::table('programacion_presupuesto')
            ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
            ->WHERE ('upp', '=', $request->data[0]['upp'])
            ->WHERE('fondo_ramo', '=', $request->data[0]['fondoRamo'])
            ->WHERE('ejercicio', '=', $request->ejercicio)
            ->WHERE('tipo', '=', $request->data[0]['subPrograma'] != 'UUU' ? 'Operativo' : 'RH' )
            ->WHERE('deleted_at', '=', null)
            ->first();
            if ($presupuestoUpp && $presupuestoUpp != '') {
                if ($presupuestoAsignado && $presupuestoAsignado != '' ) {
                    $disponible = $presupuestoUpp->presupuesto - $presupuestoAsignado->TotalAsignado;
                }else {
                    $disponible = $presupuestoUpp->presupuesto ? $presupuestoUpp->presupuesto : 0;
                }
            }
            if ( $request->data[0]['total'] <= $disponible ) {
                $nuevaClave = ProgramacionPresupuesto::create([
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
                    'enero' => $request->data[0]['enero'],
                    'febrero' => $request->data[0]['febrero'],  
                    'marzo' => $request->data[0]['marzo'],   
                    'abril' => $request->data[0]['abril'],  
                    'mayo' => $request->data[0]['mayo'],   
                    'junio' => $request->data[0]['junio'],    
                    'julio' => $request->data[0]['julio'],    
                    'agosto' => $request->data[0]['agosto'],   
                    'septiembre' => $request->data[0]['septiembre'],   
                    'octubre' => $request->data[0]['octubre'],   
                    'noviembre' => $request->data[0]['noviembre'],  
                    'diciembre' => $request->data[0]['diciembre'],  
                    'total' => $request->data[0]['total'],   
                    'estado' => 0,    
                    'tipo' => $request->data[0]['subPrograma'] != 'UUU' ? 'Operativo' : 'RH',    
                    'created_user' => Auth::user()->username, 
                ]);
            }else {
                return response()->json('cantidadNoDisponible',200);
                throw ValidationException::withMessages(['error de cantidades'=>'Las cantidades no coinciden...']);
            }
         }
           
        } catch (\Exception $exp) {
            DB::rollBack();
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
        return response()->json('done',200);
    }
    public function postEditarClave(Request $request){
        try {
            ProgramacionPresupuesto::where('id', $request->data[0]['idClave'])->update([
                'enero' => $request->data[0]['enero'],
                'febrero' => $request->data[0]['febrero'],  
                'marzo' => $request->data[0]['marzo'],   
                'abril' => $request->data[0]['abril'],  
                'mayo' => $request->data[0]['mayo'],   
                'junio' => $request->data[0]['junio'],    
                'julio' => $request->data[0]['julio'],    
                'agosto' => $request->data[0]['agosto'],   
                'septiembre' => $request->data[0]['septiembre'],   
                'octubre' => $request->data[0]['octubre'],   
                'noviembre' => $request->data[0]['noviembre'],  
                'diciembre' => $request->data[0]['diciembre'],  
                'total' => $request->data[0]['total'],
            ]);
        } catch (\Exception $exp) {
            DB::rollBack();
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
        return response()->json('done',200);

        
    }
    public function postEliminarClave(Request $request){
        ProgramacionPresupuesto::where('id',$request->id)->delete();
        return response()->json('done',200);
    }
    public function getRegiones(){
        $regiones = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.clv_region','clasificacion_geografica.region')
        ->where('clasificacion_geografica.deleted_at', '=', null)
        ->orderBy('clasificacion_geografica.clv_region')
        ->distinct()
        ->get();
        return response()->json($regiones, 200);
    }
    public function getMunicipios($id){
        $municipios = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.clv_municipio','clasificacion_geografica.municipio')
        ->where('clasificacion_geografica.deleted_at', '=', null)
        ->where('clasificacion_geografica.clv_region', '=', $id)
        ->orderBy('clasificacion_geografica.clv_municipio')
        ->distinct()
        ->get();
        return response()->json($municipios, 200);
    }
    public function getLocalidades($id){
        $localidades = DB::table('clasificacion_geografica')
        ->SELECT('clasificacion_geografica.clv_localidad', 'clasificacion_geografica.localidad')
        ->WHERE('clasificacion_geografica.clv_municipio','=' ,$id)
        ->WHERE('clasificacion_geografica.deleted_at', '=', null)
        ->orderBy('clasificacion_geografica.clv_localidad')
        ->DISTINCT()
        ->get();
        return response()->json($localidades,200);
    }
    public function getUpp($ejercicio){
        $uppUsuario = Auth::user()->clv_upp;
        $array_where = [];
        if ($uppUsuario && $uppUsuario != null) {
            array_push($array_where, ['v_epp.clv_upp', '=', $uppUsuario]);  
        }
        array_push($array_where, ['v_epp.ejercicio', '=', $ejercicio]);
        array_push($array_where, ['v_epp.deleted_at', '=', null]);
        $upp = DB::table('v_epp')
        ->select('v_epp.clv_upp','v_epp.upp')
        ->where($array_where)
        ->orderBy('v_epp.clv_upp')
        ->DISTINCT()
        ->get();
        return response()->json($upp,200);
    }
    public function getUnidadesResponsables($id = '',$ejercicio){
        $unidadResponsable = DB::table('v_epp')
        ->SELECT('clv_ur', 'ur')
        ->WHERE('v_epp.clv_upp', '=', $id)
        ->WHERE('v_epp.ejercicio', '=', $ejercicio)
        ->WHERE('v_epp.deleted_at', '=' , null)
        ->orderBy('v_epp.clv_ur')
        ->DISTINCT()
        ->get();
        return response()->json($unidadResponsable,200);
    }
    public function getSubSecretaria($upp,$ur,$ejercicio){
        $subSecretaria = DB::table('v_epp')
        ->SELECT('clv_subsecretaria' , 'subsecretaria')
        ->WHERE('clv_upp','=',$upp)
        ->WHERE('clv_ur','=',$ur)
        ->WHERE('ejercicio','=',$ejercicio)
        ->WHERE('v_epp.deleted_at','=',null)
        ->first();
        return response()->json($subSecretaria,200);
    }
    public function getProgramaPresupuestarios($uppId,$id, $ejercicio){
        $programasPresupuestales = DB::table('v_epp')
        ->SELECT( 'clv_programa', 'programa')  
        ->WHERE('clv_upp','=',$uppId)
        ->WHERE('clv_ur','=',$id)
        ->WHERE('ejercicio','=',$ejercicio)
        ->orderBy('clv_programa')
        ->DISTINCT()
        ->get();
        return response()->json($programasPresupuestales,200);
    }
    public function getSubProgramas($ur, $id, $upp, $ejercicio){
        $array_where = [];
        $uppAutorizados = DB::table('uppautorizadascpnomina')
        ->SELECT('clv_upp')
        ->WHERE('deleted_at','=', null)
        ->WHERE('clv_upp','=',$upp)
        ->get();
        if ($uppAutorizados && count($uppAutorizados) > 0) {
            array_push($array_where, ['clv_subprograma', '!=', 'UUU']);
        }
        array_push($array_where, ['clv_ur','=',$ur]);
        array_push($array_where, ['clv_programa','=',$id]);
        array_push($array_where, ['ejercicio','=',$ejercicio]);
        $subProgramas = DB::table('v_epp')
        ->SELECT('clv_subprograma', 'subprograma')
        ->WHERE($array_where)
        ->orderBy('clv_subprograma')
        ->DISTINCT()
        ->get();
        return response()->json($subProgramas,200);
    }
    public function getProyectos($programa,$id, $upp,$ur ,$ejercicio){
        $proyectos = DB::table('v_epp')
        ->SELECT('clv_proyecto', 'proyecto')
        ->WHERE('clv_programa','=',$programa)
        ->WHERE('clv_subprograma','=',$id)
        ->WHERE('clv_upp','=',$upp)
        ->WHERE('clv_ur','=',$ur)
        ->WHERE('ejercicio','=',$ejercicio)
        ->orderBy('clv_proyecto')
        ->DISTINCT()
        ->get();
        return response()->json($proyectos,200);
    }
    public function getLineaAccion($uppId,$id,$ejercicio){
        $linea = DB::table('v_epp')
        ->SELECT('clv_linea_accion','linea_accion')
        ->WHERE('clv_upp','=', $uppId)
        ->WHERE('clv_ur','=',$id)
        ->WHERE('ejercicio','=',$ejercicio)
        ->orderBy('clv_linea_accion')
        ->DISTINCT()
        ->get();
        return response()->json($linea,200);
    }
    public function getAreaFuncional($uppId,$id,$ejercicio){
        $areaFuncional = DB::table('v_epp')
        ->SELECT('clv_finalidad', 'clv_funcion', 'clv_subfuncion', 'clv_eje', 'clv_programa_sectorial','clv_tipologia_conac')
        ->WHERE ('clv_upp', '=', $uppId)
        ->WHERE ('clv_ur', '=', $id)
        ->WHERE ('ejercicio', '=', $ejercicio)
        ->DISTINCT()
        ->first();
        return response()->json($areaFuncional,200);
    }
    public function getPartidas(){
        $partidas = DB::table('posicion_presupuestaria')
        ->SELECT('partida_especifica', 'clv_capitulo','clv_concepto','clv_partida_generica','clv_partida_especifica','clv_tipo_gasto')
        ->WHERE('posicion_presupuestaria.deleted_at','=',null)
        ->DISTINCT()
        ->get();
        return response()->json($partidas,200);
    }
    public function getFondos($id,$subP,$ejercicio = 0){
        $array_where = [];
        $anio = '';
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
            array_push($array_where, ['tipo', '=', $subP != 'UUU' ? 'Operativo' : 'RH']);
            array_push($array_where, ['techos_financieros.clv_upp', '=', $id]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where, ['fondo.deleted_at', '=', null]);
        $fondos = DB::table('techos_financieros')
        ->SELECT('techos_financieros.ejercicio' , 'techos_financieros.clv_fondo', 'fondo.fondo_ramo', 'fondo.clv_etiquetado', 
        'fondo.clv_fuente_financiamiento', 'fondo.clv_ramo', 'fondo.clv_capital')
        ->leftJoin('fondo', 'techos_financieros.clv_fondo' ,'=', 'fondo.clv_fondo_ramo') 
        ->WHERE($array_where)
        ->DISTINCT()
        ->get();
        return response()->json($fondos,200);
    }
    public function getClasificacionAdmin($upp,$ur){
        $clasificacion = DB::table('v_epp')
        ->SELECT('clv_sector_publico', 'clv_sector_publico_f', 'clv_sector_economia', 'clv_subsector_economia', 'clv_ente_publico')
        ->WHERE('clv_upp', '=', $upp)
        ->WHERE('clv_ur', '=', $ur)
        ->DISTINCT()
        ->first();
        return response()->json($clasificacion,200);
    }
    public function getPresupuestoPorUpp($upp,$fondo,$subPrograma,$ejercicio){
        $disponible = 0;
        $presupuestoUpp = DB::table('techos_financieros')
        ->SELECT('presupuesto','tipo')
        ->WHERE('clv_upp', '=', $upp)
        ->WHERE('clv_fondo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        $presupuestoAsignado = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        if ($presupuestoUpp && $presupuestoUpp != '') {
            if ($presupuestoAsignado && $presupuestoAsignado != '' ) {
                $disponible = $presupuestoUpp->presupuesto - $presupuestoAsignado->TotalAsignado;
            }else {
                $disponible = $presupuestoUpp->presupuesto ? $presupuestoUpp->presupuesto : 0;
            }
        }
           
        $response = [
            'presupuesto'=>$presupuestoUpp ? $presupuestoUpp->presupuesto : 0,
            'disponible'=>$disponible,
            'tipo'=> $presupuestoUpp ?  $presupuestoUpp->tipo : '',
        ];
        return response()->json($response,200);
    }
    public function getPresupuestoPorUppEdit($upp,$fondo,$subPrograma,$ejercicio,$id){
        $disponible = 0;
        $presupuestoUpp = DB::table('techos_financieros')
        ->SELECT('presupuesto','tipo')
        ->WHERE('clv_upp', '=', $upp)
        ->WHERE('clv_fondo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        $presupuestoAsignado = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        $asignado = DB::table('programacion_presupuesto')
        ->SELECT('total')
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->WHERE('id',$id)
        ->first();
        if ($presupuestoUpp && $presupuestoUpp != '') {
            if ($presupuestoAsignado && $presupuestoAsignado != '' ) {
                $disponible = $presupuestoUpp->presupuesto - $presupuestoAsignado->TotalAsignado;
            }else {
                $disponible = $presupuestoUpp->presupuesto ? $presupuestoUpp->presupuesto : 0;
            }
        }
           
        $response = [
            'presupuesto'=>$presupuestoUpp ? $presupuestoUpp->presupuesto : 0,
            'disponible'=>$disponible,
            'calendarizado'=>$asignado->total ? $asignado->total : '',
            'tipo'=> $presupuestoUpp ?  $presupuestoUpp->tipo : '',
        ];
        return response()->json($response,200);
    }
    public function getSector($clave){
        $sector = DB::table('v_sector_linea_accion')
            ->SELECT('sector')
            ->WHERE('clv_linea_accion', '=', $clave)
            ->first();
        return response()->json($sector,200);
    }
    public function getPresupuestoAsignado($ejercicio = 0, $upp = ''){
        $Totcalendarizado = 0;
        $disponible = 0;
        $uppUsuario = Auth::user()->clv_upp;
        $array_where = [];
        $array_where2 = [];
        $array_whereCierre = [];
        $anio = '';
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
            $uppAutorizados = DB::table('uppautorizadascpnomina')
            ->SELECT('clv_upp')
            ->WHERE('clv_upp','=',$upp)
            ->get();
            if ($uppAutorizados && count($uppAutorizados) > 0) {
                array_push($array_where, ['techos_financieros.tipo', '=', 'Operativo']);
                array_push($array_where2, ['programacion_presupuesto.tipo', '=', 'Operativo']);
            }
        if ($uppUsuario && $uppUsuario != null) {
            array_push($array_where, ['techos_financieros.clv_upp', '=', $uppUsuario]);
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where2, ['programacion_presupuesto.upp', '=', $uppUsuario]);
            array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
           
        }else {
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($upp != '') {
                array_push($array_where, ['techos_financieros.clv_upp', '=', $upp]);
                array_push($array_where2, ['programacion_presupuesto.upp', '=', $upp]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $upp]);
            }
        } 
        $estatusCierre = DB::table('cierre_ejercicio_claves')
        ->SELECT('ejercicio','estatus')
        ->WHERE($array_whereCierre)
        ->first();

        $presupuestoAsignado = DB::table('techos_financieros')
        ->SELECT(DB::raw('SUM(presupuesto) as totalAsignado'))
        ->where($array_where)
        ->get();
        $calendarizados = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizados'))
        ->where($array_where2)
        ->get();
        foreach ($calendarizados as $key => $value) {
            $Totcalendarizado = $Totcalendarizado + $value->calendarizados;
        }
        if ($Totcalendarizado != 0 ) {
            $disponible = $presupuestoAsignado[0]->totalAsignado - $Totcalendarizado;
        }else {
            $disponible = $presupuestoAsignado[0]->totalAsignado;
        }
        $response = [
            'presupuestoAsignado'=>$presupuestoAsignado,
            'disponible'=>$disponible,
            'Totcalendarizado'=>$Totcalendarizado,
            'estatus'=>$estatusCierre
        ];
        return response()->json($response,200);
    }
    public function getPanelPresupuestoFondo($ejercicio = 0, $clvUpp = ''){
        $disponible = 0;
        $totalDisponible = 0;
        $totalAsignado = 0;
        $totalCalendarizado = 0;
        $uppUsuario = Auth::user()->clv_upp;
        $anio = '';
        $upp = ['clave'=>'000','descripcion'=>'Detalle General'];
        $array_where = [];
        $array_where2 = [];
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
        $uppAutorizados = DB::table('uppautorizadascpnomina')
            ->SELECT('clv_upp')
            ->WHERE('clv_upp','=', $clvUpp != '' ? $clvUpp : $uppUsuario)
            ->get();
        $arrayTechos = "tf.deleted_at IS NULL  && tf.ejercicio = ".$anio;
        $arrayProgramacion = "PP.deleted_at IS NULL && PP.ejercicio = ".$anio;
        
        if ($uppUsuario && $uppUsuario != null) {
            $arrayTechos = $arrayTechos."&& tf.clv_upp = ".$uppUsuario;
            $arrayProgramacion = $arrayProgramacion."&& PP.upp = ".$uppUsuario;
            $upp =  DB::table('catalogo')
            ->SELECT('clave','descripcion')
            ->where('grupo_id', 6)
            ->where('clave', '=', $uppUsuario)
            ->first();
        }else {
            if ($clvUpp != '') {
                $arrayTechos = $arrayTechos."&& tf.clv_upp = ".$clvUpp;
                $arrayProgramacion = $arrayProgramacion."&& PP.upp = ".$clvUpp;
            }
        } 
        if ($uppAutorizados && count($uppAutorizados) > 0) {
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
                from programacion_presupuesto pp
                where pp.tipo = 'Operativo' && ".$arrayProgramacion."
                group by clv_fondo
            ) tabla
            join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
            group by clv_fondo,f.fondo_ramo;");
        }else {
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
            from programacion_presupuesto pp
            where ".$arrayProgramacion."
            group by clv_fondo
        ) tabla
        join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
        group by clv_fondo,f.fondo_ramo;");
        }
        $response = [
            'fondos' => $fondos,
            'upp' => $upp
        ];
        return response()->json($response,200);
    }
    public function getConceptosClave($clave, $anioFondo){
      $clave = DB::select("CALL conceptos_clave('$clave', 20$anioFondo)");
         $dataset=[];
        $i=0;
        foreach($clave as $key){
            $a = array($key->descripcion,$key->clave,$key->concepto);
            $dataset[] =$a;
            $i++;
        }  
        return $dataset;
    }
    public function postConfirmarClaves(){
        $uppUsuario = Auth::user()->clv_upp;
        $array_where = [];
        if ($uppUsuario && $uppUsuario != null) {
            array_push($array_where, ['programacion_presupuesto.upp', '=', $uppUsuario]);
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
        }else {
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
        } 
        try {
            ProgramacionPresupuesto::where($array_where)->update([
                'estado' => 1,
            ]);
        } catch (\Exception $exp) {
            DB::rollBack();
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
        return response()->json('done',200);
    }
    public function getObras($val){
        $response = [];
        $permisos = DB::table('permisos_funciones')
        ->SELECT('permisos_funciones.id_user', 'permisos_funciones.id_permiso', 'adm_users.clv_upp')
        ->leftJoin('adm_users','permisos_funciones.id_user','=' ,'adm_users.id')
        ->WHERE('adm_users.clv_upp', '=', $val)
        ->WHERE('permisos_funciones.id_permiso' ,'=' ,2) 
        ->get();
         if (count($permisos)) {
            $obras = DB::table('proyectos_obra')
            ->SELECT('clv_proyecto_obra','proyecto_obra')
            ->where('deleted_at','=',null)
            ->get();
            $response = [
                'permisoObra' => 200,
                'obras' => $obras
            ];
         }else {
            $response = [
                'permisoObra' => 400,
                'obras' => ''
            ];
         }
        
        return response()->json($response,200);
    }
}