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
use App\Models\cierreEjercicio;
use App\Helpers\Calendarizacion\ClavesHelper;

use Illuminate\Validation\ValidationException;

class ClavePreController extends Controller
{
    public function getPanel(){
        Controller::check_permission('getClaves');
        $array_where = [];
        $uppUsuario = Auth::user()->clv_upp ? Auth::user()->clv_upp : '';
        if ($uppUsuario != '') {
            array_push($array_where, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
            array_push($array_where, ['cierre_ejercicio_claves.estatus', '=', 'Abierto']);

        $ejer = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE($array_where)->first();
        $ultimoEjercicio = DB::table('cierre_ejercicio_claves')->max('ejercicio');
        $ejercicio = $ejer && $ejer != null ? $ejer->ejercicio : $ultimoEjercicio;
        return view('calendarizacion.clavePresupuestaria.index',compact(['uppUsuario','ejercicio']));
    }
    public function getPanelUpdate($id){
        Controller::check_permission('putClaves');
        $clave = ProgramacionPresupuesto::where('id',$id)->first();
        Controller::check_permissionEdit('putClaves',$clave->upp);
        return view('calendarizacion.clavePresupuestaria.updateCalendarzacion', compact('clave'));
    }
    public function getCreate($ejercicio){
        
        Controller::check_permission('postClaves');
        Controller::check_permission('postClavesManual');

        $descripcion = '';
        $upp =  Auth::user()->clv_upp;
        if ($upp && $upp != null && $upp != 'null') {
            $uppDescripcion =  DB::table('catalogo')
            ->SELECT('descripcion')
            ->where('grupo_id', 6)
            ->where('clave', '=', $upp)
            ->first();
            $descripcion = $uppDescripcion->descripcion;
        }
        return view('calendarizacion.clavePresupuestaria.create', compact(['ejercicio','upp','descripcion']));
    }
    public function getPanelCalendarizacion(){
        return view('calendarizacion.clavePresupuestaria.calendarizacion');
    }
    public function getClaves(Request $request){
        Controller::check_permission('getClaves');
        $uppUsuario =  Auth::user()->clv_upp;
        $rol = '';
        $perfil = Auth::user()->id_grupo;
        switch ($perfil) {
            case 1:
                $rol = 0;
                break;
            case 4:
                $rol = 1;
                break;
            case 5:
                $rol = 2;
                break;
            default:
                $rol = 3;
                break;
        }
        $array_where = [];
        $array_whereCierre = [];
        $anio = '';
        if ($request->ejercicio && $request->ejercicio != '') {
            $anio = $request->ejercicio;
        }else {
            $anio = date('Y');
        }
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
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
        (DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre AS totalByClave')),'v_entidad_ejecutora.clv_ur as claveUr','v_entidad_ejecutora.ur as descripcionUr','v_entidad_ejecutora.clv_upp as claveUpp')
        ->leftJoin('v_entidad_ejecutora', function($join)
        {
            $join->on('v_entidad_ejecutora.clv_upp', '=', 'programacion_presupuesto.upp');
            $join->on('v_entidad_ejecutora.clv_subsecretaria','=','programacion_presupuesto.subsecretaria');
            $join->on('v_entidad_ejecutora.clv_ur','=','programacion_presupuesto.ur');
        })
        ->where(function ($claves) use ($rol,$array_where) {
            $claves->where($array_where);
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $claves->whereIn('programacion_presupuesto.upp',$arrayClaves);
            }
        })
        ->orderBy('v_entidad_ejecutora.clv_upp')
        ->orderBy('v_entidad_ejecutora.clv_ur');
        if ($request->upp && $request->upp != '' || $uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
           $claves =  $claves->get();
        }else {
            if ($rol == 2) {
                $claves =  $claves->get();
            }else {
                $claves =  $claves->limit(1000)->get();
            }
        }
        $response = [
            'claves'=> $claves,
            'estatus' => $estatusCierre,
            'rol'=>$rol,
        ];
        return response()->json($response, 200);
    } 
    public function postGuardarClave(Request $request){
        Controller::check_permission('postClaves');
        Controller::check_permission('postClavesManual');

        try {
            $perfil = Auth::user()->id_grupo;
            $esEjercicioCerrado = ClavesHelper::validaEjercicio( $request->ejercicio,$request->data[0]['upp']);
            if ($esEjercicioCerrado && $perfil != 1) {
                return response()->json('invalid',200);
            }
            $claveExist = ClavesHelper::claveExist($request);
         if ($claveExist) {
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
                $hasMetas = ClavesHelper::tieneMetas($request,1);

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
                    'enero' => $request->data[0]['enero'] ? $request->data[0]['enero'] : 0,
                    'febrero' => $request->data[0]['febrero'] ? $request->data[0]['febrero'] : 0,  
                    'marzo' => $request->data[0]['marzo'] ? $request->data[0]['marzo'] : 0,   
                    'abril' => $request->data[0]['abril'] ? $request->data[0]['abril'] : 0,  
                    'mayo' => $request->data[0]['mayo'] ? $request->data[0]['mayo'] : 0,   
                    'junio' => $request->data[0]['junio'] ? $request->data[0]['junio'] : 0,    
                    'julio' => $request->data[0]['julio'] ? $request->data[0]['julio'] : 0,    
                    'agosto' => $request->data[0]['agosto'] ? $request->data[0]['agosto'] : 0,   
                    'septiembre' => $request->data[0]['septiembre'] ? $request->data[0]['septiembre'] : 0,   
                    'octubre' => $request->data[0]['octubre'] ? $request->data[0]['octubre'] : 0,   
                    'noviembre' => $request->data[0]['noviembre'] ? $request->data[0]['noviembre'] : 0,  
                    'diciembre' => $request->data[0]['diciembre'] ? $request->data[0]['diciembre'] : 0,  
                    'total' => $request->data[0]['total'],   
                    'estado' => 0,    
                    'tipo' => $request->data[0]['subPrograma'] != 'UUU' ? 'Operativo' : 'RH',    
                    'created_user' => Auth::user()->username, 
                ]);
                $aplanado = DB::select("CALL insert_pp_aplanado(".$request->ejercicio.")");
                $b = array(
                    "username"=>Auth::user()->username,
                    "accion"=>'Guardar',
                    "modulo"=>'Claves'
                 );
                 Controller::bitacora($b);
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
        Controller::check_permission('putClaves');
        try {
            $response = [];
            $perfil = Auth::user()->id_grupo;
            $esEjercicioCerrado = ClavesHelper::validaEjercicio($request->data[0]['ejercicio'],$request->data[0]['clvUpp']);
            if ($esEjercicioCerrado && $perfil != 1) {
                $response = [
                    'titulo'=> '¡Advertencia!',
                    'mensaje'=> 'No es posible realizar esta accion, el ejercicio no se encuentra abierto.',
                    'icon'=> 'warning'
                ];
                return response()->json($response,200);
            }
            ProgramacionPresupuesto::where('id', $request->data[0]['idClave'])->update([
                'enero' => $request->data[0]['enero'] ? $request->data[0]['enero'] : 0,
                'febrero' => $request->data[0]['febrero'] ? $request->data[0]['febrero'] : 0,  
                'marzo' => $request->data[0]['marzo'] ? $request->data[0]['marzo'] : 0,   
                'abril' => $request->data[0]['abril'] ? $request->data[0]['abril'] : 0,  
                'mayo' => $request->data[0]['mayo'] ? $request->data[0]['mayo'] : 0,   
                'junio' => $request->data[0]['junio'] ? $request->data[0]['junio'] : 0,    
                'julio' => $request->data[0]['julio'] ? $request->data[0]['julio'] : 0,    
                'agosto' => $request->data[0]['agosto'] ? $request->data[0]['agosto'] : 0,   
                'septiembre' => $request->data[0]['septiembre'] ? $request->data[0]['septiembre'] : 0,   
                'octubre' => $request->data[0]['octubre'] ? $request->data[0]['octubre'] : 0,   
                'noviembre' => $request->data[0]['noviembre'] ? $request->data[0]['noviembre'] : 0,  
                'diciembre' => $request->data[0]['diciembre'] ? $request->data[0]['diciembre'] : 0,  
                'total' => $request->data[0]['total'],
            ]);
            $hasMetas = ClavesHelper::tieneMetas($request,2);
            $b = array(
                "username"=>Auth::user()->username,
                "accion"=>'Editar',
                "modulo"=>'Claves'
             );
             Controller::bitacora($b);
        } catch (\Exception $exp) {
            DB::rollBack();
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
        $response = [
            'titulo'=> 'Éxito',
            'mensaje'=> 'El registro se logro con éxito.',
            'icon'=> 'success'
        ];
        return response()->json($response,200);

        
    }
    public function postEliminarClave(Request $request){
        Controller::check_permission('deleteClaves');
        $perfil = Auth::user()->id_grupo;
            $esEjercicioCerrado = ClavesHelper::validaEjercicio( $request->ejercicio,$request->upp);
            if ($esEjercicioCerrado && $perfil != 1) {
                return response()->json('invalid',200);
            }
        ProgramacionPresupuesto::where('id',$request->id)->delete();
        $b = array(
            "username"=>Auth::user()->username,
            "accion"=>'Eliminar',
            "modulo"=>'Claves'
         );
         Controller::bitacora($b);
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
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
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
        ->WHERE('v_epp.presupuestable', 1)
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
        ->WHERE('v_epp.presupuestable', 1)
        ->first();
        return response()->json($subSecretaria,200);
    }
    public function getProgramaPresupuestarios($uppId,$id, $ejercicio){
        $programasPresupuestales = DB::table('v_epp')
        ->SELECT( 'clv_programa', 'programa')  
        ->WHERE('clv_upp','=',$uppId)
        ->WHERE('clv_ur','=',$id)
        ->WHERE('ejercicio','=',$ejercicio)
        ->WHERE('v_epp.presupuestable', 1)
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
        array_push($array_where, ['presupuestable','=',1]);
        array_push($array_where, ['clv_upp','=',$upp]);
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
        ->WHERE('v_epp.presupuestable', 1)
        ->orderBy('clv_proyecto')
        ->DISTINCT()
        ->get();
        return response()->json($proyectos,200);
    }
    public function getLineaAccion($uppId,$id,$ejercicio,$programa,$subPrograma,$proyecto){
        $linea = DB::table('v_epp')
        ->SELECT('clv_linea_accion','linea_accion')
        ->WHERE('clv_upp','=', $uppId)
        ->WHERE('clv_ur','=',$id)
        ->WHERE('ejercicio','=',$ejercicio)
        ->WHERE('clv_programa','=', $programa)
        ->WHERE('clv_subprograma','=',$subPrograma)
        ->WHERE('clv_proyecto','=',$proyecto)
        ->WHERE('v_epp.presupuestable', 1)
        ->orderBy('clv_linea_accion')
        ->DISTINCT()
        ->get();
        return response()->json($linea,200);
    }
    public function getAreaFuncional($uppId,$id,$ejercicio, $subPrograma,$linea,$programa,$proyecto){
        $areaFuncional = DB::table('v_epp')
        ->SELECT('clv_finalidad', 'clv_funcion', 'clv_subfuncion', 'clv_eje', 'clv_programa_sectorial','clv_tipologia_conac')
        ->WHERE ('clv_upp', '=', $uppId)
        ->WHERE ('clv_ur', '=', $id)
        ->WHERE ('ejercicio', '=', $ejercicio)
        ->WHERE ('presupuestable', 1)
        ->WHERE ('clv_subprograma', '=',  $subPrograma)
        ->where ('clv_linea_accion', '=', $linea)
        ->where ('clv_programa', '=', $programa)
        ->where ('clv_proyecto', '=', $proyecto)
        ->DISTINCT()
        ->first();
        return response()->json($areaFuncional,200);
    }
    public function getPartidas($clasificacion){
        $partidas = DB::table('rel_economica_administrativa')
        ->SELECT(
        'v_posicion_presupuestaria_llaves.clv_capitulo',
        'v_posicion_presupuestaria_llaves.clv_concepto',
        'v_posicion_presupuestaria_llaves.clv_partida_generica',
        'v_posicion_presupuestaria_llaves.clv_partida_especifica',
        'v_posicion_presupuestaria_llaves.clv_tipo_gasto',
        'v_posicion_presupuestaria_llaves.partida_especifica')
        ->leftJoin('v_posicion_presupuestaria_llaves','rel_economica_administrativa.clasificacion_economica','=','v_posicion_presupuestaria_llaves.posicion_presupuestaria_llave')
        ->WHERE('rel_economica_administrativa.clasificacion_administrativa','=',$clasificacion)
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
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
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
        ->WHERE('presupuestable', 1)
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
        $rol = '';
        $uppUsuario = Auth::user()->clv_upp;
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
                // rolauditor y gobDigital
                $rol = 3;
                break;
        }
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
                ->WHERE('deleted_at','=', null)
                ->WHERE('clv_upp','=',$uppUsuario)
                ->get();
            $autorizado =  count($uppAutorizados) > 0 ? count($uppAutorizados) : 0;
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
                array_push($array_where, ['techos_financieros.clv_upp', '=', $uppUsuario]);
                array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
                array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
                array_push($array_where2, ['programacion_presupuesto.upp', '=', $uppUsuario]);
                array_push($array_where2, ['programacion_presupuesto.deleted_at', '=', null]);
                array_push($array_where2, ['programacion_presupuesto.ejercicio', '=', $anio]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($uppAutorizados && count($uppAutorizados) > 0  ) {
                array_push($array_where, ['techos_financieros.tipo', '=', 'Operativo']);
                array_push($array_where2, ['programacion_presupuesto.tipo', '=', 'Operativo']);
            }
           
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
        $recursosOperativos = ClavesHelper::getPresupuestooperativo($uppUsuario,$anio,$upp);
        $recursosRH = ClavesHelper::getPresupuestoRH($uppUsuario,$anio,$upp,$rol);
        $response = [
            'presupuestoAsignado'=>$presupuestoAsignado,
            'disponible'=>$disponible,
            'Totcalendarizado'=>$Totcalendarizado,
            'estatus'=>$estatusCierre,
            'rol' => $rol,
            'estado'=>count($calendarizados) ? $calendarizados[0]->estado : 0,
            'recursosOperativos'=>$recursosOperativos,
            'recursosRH'=>$recursosRH,
            'esAutorizado'=>$autorizado,
        ];
        return response()->json($response,200);
    }
    public function getPanelPresupuestoFondo($ejercicio = 0, $clvUpp = ''){
        $disponible = 0;
        $totalDisponible = 0;
        $totalAsignado = 0;
        $rol = '';
        $perfil = Auth::user()->id_grupo;
        switch ($perfil) {
            case 1:
                $rol = 0;
                break;
            case 4:
                $rol = 1;
                break;
            case 5:
                $rol = 2;
                break;
            default:
                $rol = 3;
                break;
        }
        $totalCalendarizado = 0;
        $uppUsuario = Auth::user()->clv_upp;
        $anio = '';
        $upp = ['clave'=>'000','descripcion'=>'Detalle General'];
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
        $uppAutorizados = DB::table('uppautorizadascpnomina')
            ->SELECT('clv_upp')
            ->WHERE('deleted_at','=', null)
            ->WHERE('clv_upp','=', $clvUpp != '' ? $clvUpp : $uppUsuario)
            ->get();
        $arrayTechos = "tf.deleted_at IS NULL  && tf.ejercicio = ".$anio;
        $arrayProgramacion = "pp.deleted_at IS NULL && pp.ejercicio = ".$anio;
        
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
            $arrayTechos = $arrayTechos."&& tf.clv_upp = ".$uppUsuario;
            $arrayProgramacion = $arrayProgramacion."&& pp.upp = ".$uppUsuario;
            $upp =  DB::table('catalogo')
            ->SELECT('clave','descripcion')
            ->where('grupo_id', 6)
            ->where('clave', '=', $uppUsuario)
            ->first();
        }else {
            if ($clvUpp != '') {
                $arrayTechos = $arrayTechos."&& tf.clv_upp = ".$clvUpp;
                $arrayProgramacion = $arrayProgramacion."&& pp.upp = ".$clvUpp;
            }
        } 
        if ($rol == 2) {
            $fondos = DB::select("select 
            clv_fondo,
            f.fondo_ramo,
            0 RH,
            sum(Operativo) Operativo,
            sum(Operativo) techos_presupuestal,
            sum(calendarizado) calendarizado,
            sum(Operativo - calendarizado) disponible,
            ejercicio
        from (
            select 
                clv_fondo,
                0 RH,
                sum(presupuesto) Operativo,
                0 calendarizado,
                ejercicio
            from techos_financieros tf
            where tf.tipo = 'RH' and tf.clv_upp IN (select uppautorizadascpnomina.clv_upp from uppautorizadascpnomina where uppautorizadascpnomina.deleted_at = null) && ".$arrayTechos."
            group by clv_fondo,ejercicio
            union all 
            select 
                fondo_ramo clv_fondo,
                0 RH,
                0 Operativo,
                sum(total) calendarizado,
                ejercicio
            from programacion_presupuesto pp
            where pp.tipo = 'RH' and pp.upp IN (select uppautorizadascpnomina.clv_upp from uppautorizadascpnomina where uppautorizadascpnomina.deleted_at = null) && ".$arrayProgramacion."
            group by clv_fondo,ejercicio
        ) tabla
        join fondo f on tabla.clv_fondo = f.clv_fondo_ramo
        group by clv_fondo,f.fondo_ramo,ejercicio;");
        }else {
            if ($uppAutorizados && count($uppAutorizados) > 0 ) {
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
    public function postConfirmarClaves(Request $request){ 
        $rol = 0;
        $uppUsuario = Auth::user()->clv_upp;
        $grupo =  Auth::user()->id_grupo;
        if ($grupo == 5) {
            $rol =2;
        }
        $array_where = [];
        array_push($array_where, ['programacion_presupuesto.upp', '=', $request->upp ? $request->upp : $uppUsuario]);
        array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
        array_push($array_where, ['programacion_presupuesto.ejercicio', '=', $request->ejercicio]);
        try {
            $ejer = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio')->WHERE('cierre_ejercicio_claves.estatus','=','Abierto')->where('clv_upp','=' , $request->upp ? $request->upp : $uppUsuario)->first();
            $ejercicio = $ejer && $ejer != null ? $ejer->ejercicio : '';
            $estado = DB::table('programacion_presupuesto')->SELECT('estado')->WHERE($array_where)->first();
            if ($request->ejercicio != $ejercicio || $estado && $estado->estado != 0) {
                $response = [
                    'response'=>'errorAutorizacion',
                    'rol'=>$rol
                ];
                return response()->json($response,200);
            }else {
                ProgramacionPresupuesto::where($array_where)->update([
                    'estado' => 1,
                ]);
                cierreEjercicio::where('clv_upp','=',$request->upp ? $request->upp : $uppUsuario)->where('ejercicio','=',$request->ejercicio)
                ->update([
                    'estatus'=>'Cerrado',
                    'updated_user'=>Auth::user()->username
                ]);
                $b = array(
                    "username"=>Auth::user()->username,
                    "accion"=>'Confirmar',
                    "modulo"=>'Claves'
                 );
                 Controller::bitacora($b);
            }
        } catch (\Exception $exp) {
            DB::rollBack();
			Log::debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
			return response()->json('error',200);
        }
        $response = [
            'response'=>'done',
            'rol'=>$rol
        ];
        return response()->json($response,200);
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
            ->orderBy('clv_proyecto_obra')
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
    public function getEjercicios(){
        $ejercicios = DB::table('v_epp')
        ->SELECT('ejercicio')
        ->distinct()
        ->get();
        return response()->json($ejercicios,200);
    }
}