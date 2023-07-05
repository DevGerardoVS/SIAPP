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
use App\Models\catalogos\CatEntes;

use Illuminate\Validation\ValidationException;

class ClavePreController extends Controller
{
    public function getPanel(){
        return view('calendarizacion.clavePresupuestaria.index');
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
        $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();
        $array_where = [];
        $array_whereCierre = [];
        $anio = '';
        if ($request->ejercicio && $request->ejercicio != '') {
            $anio = $request->ejercicio;
        }else {
            $anio = date('Y');
        }
        if ($uppUsuario && $uppUsuario->cve_upp != 'null') {
            array_push($array_where, ['programacion_presupuesto.upp', '=', $uppUsuario->cve_upp]);
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario->cve_upp]);
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
        if ($request->upp && $request->upp != '' || $uppUsuario && $uppUsuario->cve_upp != 'null') {
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
        // $ejercicio = date("Y");
        Log::debug(json_encode($request->ejercicio));
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
            throw ValidationException::withMessages('Esta clave ya existe');
         }else {
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
            Log::debug(json_encode($request));
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
    public function getUpp(){
        $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();
        $array_where = [];
        if ($uppUsuario && $uppUsuario->cve_upp != 'null') {
            array_push($array_where, ['catalogo.clave', '=', $uppUsuario->cve_upp]);
            array_push($array_where, ['catalogo.deleted_at', '=', null]);
            array_push($array_where, ['entidad_ejecutora.deleted_at', '=', null]);
        }else {
            array_push($array_where, ['entidad_ejecutora.deleted_at', '=', null]);
            array_push($array_where, ['catalogo.deleted_at', '=', null]);
        } 

        $upp = DB::table('entidad_ejecutora')
        ->select('entidad_ejecutora.upp_id','catalogo.clave','catalogo.descripcion')
        ->leftJoin('catalogo','entidad_ejecutora.upp_id','=','catalogo.id')
        ->where($array_where)
        ->orderBy('catalogo.clave')
        ->DISTINCT()
        ->get();
        return response()->json($upp,200);
    }
    public function getUnidadesResponsables($id){
        $unidadResponsable = DB::table('v_entidad_ejecutora')
        ->SELECT('clv_ur', 'ur')
        ->WHERE('v_entidad_ejecutora.clv_upp', '=', $id)
        ->WHERE('v_entidad_ejecutora.deleted_at', '=' , null)
        ->orderBy('v_entidad_ejecutora.clv_ur')
        ->DISTINCT()
        ->get();
        return response()->json($unidadResponsable,200);
    }
    public function getSubSecretaria($upp,$ur){
        $subSecretaria = DB::table('v_entidad_ejecutora')
        ->SELECT('clv_subsecretaria' , 'subsecretaria')
        ->WHERE('clv_upp','=',$upp)
        ->WHERE('clv_ur','=',$ur)
        ->WHERE('v_entidad_ejecutora.deleted_at','=',null)
        ->first();
        return response()->json($subSecretaria,200);
    }
    public function getProgramaPresupuestarios($uppId,$id){
        $programasPresupuestales = DB::table('v_epp')
        ->SELECT( 'clv_programa', 'programa')  
        ->WHERE('clv_upp','=',$uppId)
        ->WHERE('clv_ur','=',$id)
        ->orderBy('clv_programa')
        ->DISTINCT()
        ->get();
        return response()->json($programasPresupuestales,200);
    }
    public function getSubProgramas($ur,$id, $upp){
        $array_where = [];
        $uppAutorizados = DB::table('uppautorizadascpnomina')
        ->SELECT('clv_upp')
        ->WHERE('clv_upp','=',$upp)
        ->get();
        if ($uppAutorizados && count($uppAutorizados) > 0) {
            array_push($array_where, ['clv_subprograma', '!=', 'UUU']);
        }
        array_push($array_where, ['clv_ur','=',$ur]);
        array_push($array_where, ['clv_programa','=',$id]);
        $subProgramas = DB::table('v_epp')
        ->SELECT('clv_subprograma', 'subprograma')
        ->WHERE($array_where)
        ->orderBy('clv_subprograma')
        ->DISTINCT()
        ->get();
        return response()->json($subProgramas,200);
    }
    public function getProyectos($programa,$id){
        $proyectos = DB::table('v_epp')
        ->SELECT('clv_proyecto', 'proyecto')
        ->WHERE('clv_programa','=',$programa)
        ->WHERE('clv_subprograma','=',$id)
        ->orderBy('clv_proyecto')
        ->DISTINCT()
        ->get();
        return response()->json($proyectos,200);
    }
    public function getLineaAccion($uppId,$id){
        $linea = DB::table('v_epp')
        ->SELECT('clv_linea_accion','linea_accion')
        ->WHERE('clv_upp','=', $uppId)
        ->WHERE('clv_ur','=',$id)
        ->orderBy('clv_linea_accion')
        ->DISTINCT()
        ->get();
        return response()->json($linea,200);
    }
    public function getAreaFuncional($uppId,$id){
        $areaFuncional = DB::table('v_epp')
        ->SELECT('clv_finalidad', 'clv_funcion', 'clv_subfuncion', 'clv_eje', 'clv_programa_sectorial','clv_tipologia_conac')
        ->WHERE ('clv_upp', '=', $uppId)
        ->WHERE ('clv_ur', '=', $id)
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
       Log::debug(json_encode($ejercicio));
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
    public function getSector($clave){
        $sector = DB::table('v_sector_linea_accion')
            ->SELECT('sector')
            ->WHERE('clv_linea_accion', '=', $clave)
            ->first();
        return response()->json($sector,200);
    }
    public function getPresupuestoAsignado($ejercicio = 0){
        $Totcalendarizado = 0;
        $disponible = 0;
        $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();
        $array_where = [];
        $array_where2 = [];
        $array_whereCierre = [];
        $anio = '';
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
        if ($uppUsuario && $uppUsuario->cve_upp != 'null') {
            array_push($array_where, ['techos_financieros.clv_upp', '=', $uppUsuario->cve_upp]);
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where2, ['programacion_presupuesto.upp', '=', $uppUsuario->cve_upp]);
            array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario->cve_upp]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
        }else {
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
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
    public function getPanelPresupuestoFondo($ejercicio = 0){
        $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();
        $anio = '';
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
        $array_where = [];
        $upp = ['clave'=>'000','descripcion'=>'Detalle General'];
        if ($uppUsuario && $uppUsuario->cve_upp != 'null') {
            array_push($array_where, ['techos_financieros.clv_upp', '=', $uppUsuario->cve_upp]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
            $upp =  DB::table('catalogo')
            ->SELECT('clave','descripcion')
            ->where('grupo_id', 6)
            ->where('clave', '=', $uppUsuario->cve_upp)
            ->first();
        }else {
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
        } 
        $disponible = 0;
        $totalDisponible = 0;
        $totalAsignado = 0;
        $totalCalendarizado = 0;
        $fondos = DB::select("select
        fondo1,
        group_concat(descripcion separator '') as descripcion,
        ejercicio,
        sum(techos) as montoAsignado,
        sum(anual) as calendarizado
    from (
        select 
            clv_fondo fondo1,
            ejercicio,
            fondo.fondo_ramo descripcion,
            sum(presupuesto) techos,
            0 anual
        from techos_financieros tf
        LEFT JOIN fondo on tf.clv_fondo = fondo.clv_fondo_ramo
        WHERE tf.ejercicio = ".$anio." && tf.deleted_at IS NULL
        group by clv_fondo,descripcion
        union all
        select 
            fondo_ramo fondo1,
            ejercicio,
            '' descripcion,
            0 techos,
            sum(total) anual
        from programacion_presupuesto pp 
        WHERE pp.ejercicio = ".$anio." && PP.deleted_at IS NULL
        group by fondo_ramo,descripcion
        order by fondo1
    ) tabla
    group by fondo1;");
        foreach ($fondos as $key => $fondo) {
           if ($fondo->montoAsignado != null && $fondo->calendarizado != '') {
                $disponible = $fondo->montoAsignado - $fondo->calendarizado;
                $fondo->disponible = $disponible;
               
           }
           else {
                $disponible = $fondo->montoAsignado;
                $fondo->disponible = $disponible;
           }
           $fondo->upp = $upp;
        }
         
        return response()->json($fondos,200);
    }
    public function getConceptosClave($clave){
      $clave = DB::select("CALL conceptos_clave('$clave')");
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
        $uppUsuario = CatEntes::where('id', auth::user()->id_ente)->first();
        $array_where = [];
        if ($uppUsuario && $uppUsuario->cve_upp != 'null') {
            array_push($array_where, ['programacion_presupuesto.upp', '=', $uppUsuario->cve_upp]);
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
}