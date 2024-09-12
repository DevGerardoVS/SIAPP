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
use App\Helpers\Calendarizacion\MetasHelper;

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
            ->where('ejercicio',$ejercicio)
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
        // Revisa los permisos para poder ver las claves presupuestales...
        Controller::check_permission('getClaves');
        $uppUsuario =  Auth::user()->clv_upp;
        $rol = '';
        $clvUpp = $request->filUpp ? $request->filUpp : $uppUsuario;
        $perfil = Auth::user()->id_grupo;
        // asigna el valor del rol al cual pertenece el usuario actual...
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
        $whereCierre = [];
        $anio = '';
        $tabla = 'programacion_presupuesto';
        // page Lenngth
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip = ($pageNumber - 1) * $pageLength;
        // Page order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';
        
        if ($uppUsuario != '') { // Si el usuario es perfil upp agrega el cierre de ejercicio a un array where para agregarlo a la consulta
            array_push($whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
        // obtenemos el ejercicio actual en base a la tabla cierre ejercicio claves...
        $ejercicioActual = DB::table('cierre_ejercicio_claves')->SELECT(DB::raw('MAX( ejercicio )AS ejercicio'))->WHERE($whereCierre)->first();
        if ($request->filtro_anio && $request->filtro_anio != '') {//verifica que exista un ejercicio en los filtros seleccionados...
            $anio = $request->filtro_anio;
            if ($anio < $ejercicioActual->ejercicio) {// verifica que el ejercicio actual sea el mismo que se pasa en el filtro para decidir a que tabla dirigir la consulata...
                $tabla = 'programacion_presupuesto_hist';
                 // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
                array_push($array_where, [$tabla.'.version', '=', 0]);
            }
        }else {
            $anio = date('Y');
            $tabla = 'programacion_presupuesto_hist';
            // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
            array_push($array_where, [$tabla.'.version', '=', 0]);
        }
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
            array_push($array_where, [$tabla.'.upp', '=', $uppUsuario]);
            if ($tabla == 'programacion_presupuesto') {
                array_push($array_where, [$tabla.'.deleted_at', '=', null]);
            }
            array_push($array_where, [$tabla.'.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($request->filtro_ur && $request->filtro_ur != '') {
                array_push($array_where, [$tabla.'.ur', '=', $request->filtro_ur]);
            }
        }else {
            if ($tabla == 'programacion_presupuesto') {
                array_push($array_where, [$tabla.'.deleted_at', '=', null]);    
            }
            array_push($array_where, [$tabla.'.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($request->filUpp  && $request->filUpp  != '') {
                array_push($array_where, [$tabla.'.upp', '=', $request->filUpp ]);
                if ($request->filtro_ur && $request->filtro_ur != '') {
                    array_push($array_where, [$tabla.'.ur', '=', $request->filtro_ur]);
                }
            }
        }
        // agregamos la variable urr para usarla en la sub consulta y poder obtener las descripciones de cada ur... 
        $urr = $request->filtro_ur;
        // sub consulta para retorna el estatus de cierre de ejercicio de las claves...
        $estatusCierre = DB::table('cierre_ejercicio_claves')
        ->SELECT('ejercicio','estatus')
        ->WHERE($array_whereCierre)
        ->first(); 
        // generamos una sub consulta para obtener las descripciones de las urs sin consultar la v_epp...
        $desc = DB::table('v_epp')
            ->select('clv_upp','upp','clv_ur','ur','ejercicio')
            ->where(function ($desc) use ($clvUpp,$anio,$urr) {
                $desc->where('ejercicio','=',$anio)->where('deleted_at','=',null);
                if ($clvUpp && $clvUpp != '') {
                    $desc->where('clv_upp', '=', $clvUpp);   
                }
                if ($urr && $urr != '') {
                    $desc->where('clv_ur',$urr);
                }
            })
            ->distinct();
        // agregamos la variable de busqueda...
        $search = $request->search['value'];
        $claves = DB::table($tabla)
        ->SELECT($tabla.'.id',$tabla.'.clasificacion_administrativa',
        (DB::raw("CONCAT(".strval($tabla).'.entidad_federativa'.','.$tabla.'.region'.','.$tabla.'.municipio'.','.$tabla.'.localidad'.','.$tabla.'.upp'.','.$tabla.'.subsecretaria'.','.$tabla.'.ur'.") AS centroGestor")),
        (DB::raw("CONCAT(".strval($tabla).'.finalidad'.','.$tabla.'.funcion'.','.$tabla.'.subfuncion'.','.$tabla.'.eje'.','.$tabla.'.linea_accion'.','.$tabla.'.programa_sectorial'.','.$tabla.'.tipologia_conac'.','.$tabla.'.programa_presupuestario'.','.$tabla.'.subprograma_presupuestario'.','.$tabla.'.proyecto_presupuestario'.") AS areaFuncional")),
        $tabla.'.periodo_presupuestal',
        (DB::raw("CONCAT(".strval($tabla).'.posicion_presupuestaria'.','.$tabla.'.tipo_gasto'.") AS posicionPre")),
        (DB::raw("CONCAT(".strval($tabla).'.anio'.','.$tabla.'.etiquetado'.','.$tabla.'.fuente_financiamiento'.','.$tabla.'.ramo'.','.$tabla.'.fondo_ramo'.','.$tabla.'.capital'.") AS fondo")),
        $tabla.'.proyecto_obra',
        $tabla.'.ejercicio',
        $tabla.'.estado',
        (DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre AS totalByClave')),
        (DB::raw("case
		when estado = 0 then CONCAT(desc.clv_upp, '-' ,desc.clv_ur, '-' ,desc.ur,' - Registradas')
		when estado = 1 then CONCAT(desc.clv_upp, '-' ,desc.clv_ur, '-' ,desc.ur,' - Confirmadas')
	    END AS row")),
        'desc.clv_ur as claveUr','desc.ur as descripcionUr','desc.clv_upp as claveUpp'
        
        )->leftJoinSub($desc, 'desc', function ($join) use($tabla){
            $join->on('desc.clv_upp', '=', $tabla.'.upp');
            $join->on('desc.clv_ur','=',$tabla.'.ur');
            $join->on('desc.ejercicio','=',$tabla.'.ejercicio');
        })
        ->where(function ($claves) use ($rol,$array_where, $tabla) {
            $claves->where($array_where);
            if ($rol == 1) {
                $claves->where($tabla.'.tipo','Operativo');
            }
            if ($rol == 2) {
                $claves->where($tabla.'.tipo','RH');
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $claves->where($tabla.'.tipo','RH');
                $claves->whereIn($tabla.'.upp',$arrayClaves);
            }
        })
        ->where(function($claves) use ($search, $tabla){
            if ($search && $search != '') {
                $claves->orWhere(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre'),'like',"%".$search."%");
                $claves->orWhere('proyecto_obra','like',"%".$search."%");
                $claves->orWhere(DB::raw("CONCAT(".strval($tabla).'.posicion_presupuestaria'.','.$tabla.'.tipo_gasto'.")"),'like',"%".$search."%");
                $claves->orWhere(DB::raw("CONCAT(".strval($tabla).'.finalidad'.','.$tabla.'.funcion'.','.$tabla.'.subfuncion'.','.$tabla.'.eje'.','.$tabla.'.linea_accion'.','.$tabla.'.programa_sectorial'.','.$tabla.'.tipologia_conac'.','.$tabla.'.programa_presupuestario'.','.$tabla.'.subprograma_presupuestario'.','.$tabla.'.proyecto_presupuestario'.")"),'like',"%".$search."%");
                $claves->orWhere(DB::raw("CONCAT(".strval($tabla).'.entidad_federativa'.','.$tabla.'.region'.','.$tabla.'.municipio'.','.$tabla.'.localidad'.','.$tabla.'.upp'.','.$tabla.'.subsecretaria'.','.$tabla.'.ur'.")"),'like',"%".$search."%");
                $claves->orWhere($tabla.'.clasificacion_administrativa','like',"%".$search."%");
                $claves->orWhere($tabla.'.periodo_presupuestal','like',"%".$search."%");
            }
        })
        ->orderBy('desc.clv_upp')
        ->orderBy('desc.clv_ur');
        if ($request->filUpp  && $request->filUpp  != '' || $uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
            $recordsFiltered = $recordsTotal = $claves->count();
            $claves = $claves->skip($skip)->take($pageLength)->get();
        //    $claves =  $claves->get();
        }else {
            if ($rol == 2) {
                $recordsFiltered = $recordsTotal = $claves->count();
                $claves = $claves->skip($skip)->take($pageLength)->get();
                // $claves =  $claves->get();
            }else {
                $recordsFiltered = $recordsTotal = $claves->count();
                $claves = $claves->skip($skip)->take($pageLength)->get();
                // $claves =  $claves->limit(1000)->get();
            }
        }
        $esAutorizada = ClavesHelper::esAutorizada($clvUpp);
        foreach ($claves as $key => $value) {
            $value->estatus = $estatusCierre;
            $value->esAutorizada = $esAutorizada;
            $value->rol = $rol;
        }
        $response = [
            'data'=> $claves,
            'estatus' => $estatusCierre,
            'rol'=>$rol,
            'esAutorizada'=>$esAutorizada,
            "draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered,
        ];
        return response()->json($response, 200);
    } 
    public function postGuardarClave(Request $request){
        Controller::check_permission('postClaves');
        Controller::check_permission('postClavesManual');
        DB::beginTransaction();
        try {
            $perfil = Auth::user()->id_grupo;
            $tipo = '';
            $esEjercicioCerrado = ClavesHelper::validaEjercicio( $request->ejercicio,$request->data[0]['upp']);
            if ($esEjercicioCerrado && $perfil != 1) {
                return response()->json('invalid',200);
            }
            $claveExist = ClavesHelper::claveExist($request);
         if ($claveExist) {
            return response()->json('duplicado',200);
            throw ValidationException::withMessages(['duplicado'=>'Esta clave ya existe']);
           
         }else {
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
            $disponible = 0;
            $presupuestoUpp = DB::table('techos_financieros')
            ->SELECT('presupuesto','tipo')
            ->WHERE('clv_upp', '=', $request->data[0]['upp'])
            ->WHERE('clv_fondo', '=', $request->data[0]['fondoRamo'])
            ->WHERE('ejercicio', '=', $request->ejercicio)
            ->where(function ($presupuestoUpp) use ($rol) {
                //para que solo tome los recursos operativos en perfil upp
                if ($rol == 1) {
                    $presupuestoUpp->where('tipo', '=', 'Operativo' );
                }
                //para que solo tome los recursos RH en perfil delegacion
                if ($rol == 2) {
                    $presupuestoUpp->where('tipo', '=', 'RH' );
                }
            })
            ->WHERE('deleted_at', '=', null)
            ->first();
            $presupuestoAsignado = DB::table('programacion_presupuesto')
            ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
            ->WHERE ('upp', '=', $request->data[0]['upp'])
            ->WHERE('fondo_ramo', '=', $request->data[0]['fondoRamo'])
            ->WHERE('ejercicio', '=', $request->ejercicio)
            ->where(function ($presupuestoAsignado) use ($rol) {
                //para que solo tome los recursos operativos en perfil upp
                if ($rol == 1 || $rol == 0) {
                    $presupuestoAsignado->where('tipo', '=', 'Operativo' );
                }
                //para que solo tome los recursos RH en perfil delegacion
                if ($rol == 2) {
                    $presupuestoAsignado->where('tipo', '=', 'RH' );
                }
            })
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
                $esAutorizada = ClavesHelper::esAutorizada($request->data[0]['upp']);
                if (!$esAutorizada) {
                    $tipo = 'Operativo';
                }else {
                    $tipo = $request->data[0]['subPrograma'] != 'UUU'  ? 'Operativo' : 'RH';
                }

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
                    'tipo' => $tipo,   
                    'created_user' => Auth::user()->username, 
                ]);
                $b = [];
                if(isset($nuevaClave->id)){
                        $flag = true;
                }else{
                    $flag = false;
                }
                    if ($flag) {
                        try {
                            $b = array(
                                "username"=>Auth::user()->username,
                                "accion"=>'Guardar',
                                "modulo"=>'Claves'
                            );
                            Controller::bitacora($b);
                            DB::commit();
                        } catch (\Throwable $th) {
                            throw new \Exception($th->getMessage());
                        }
                       
                    }

            }else {
                DB::rollBack();
                return response()->json('cantidadNoDisponible',200);
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
                'updated_user' => Auth::user()->username,
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
        $tieneMetas = MetasHelper::actividades($request->upp, 0,$request->ejercicio);
        if (count($tieneMetas)) {
            return response()->json('invalid',200);
        }
        ProgramacionPresupuesto::where('id', $request->id)->update([
            'deleted_user' => Auth::user()->username,
            'estado'=> 0,
        ]);   
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
        $regiones = DB::table('catalogo')
        ->SELECT('clave as clv_region','descripcion as region')
        ->where('grupo_id','REGIÓN')
        ->whereNull('deleted_at')
        ->orderBy('clave')
        ->distinct()
        ->get();
        return response()->json($regiones, 200);
    }
    public function getMunicipios($id){
        
        $municipios = DB::table('clasificacion_geografica as cg')
        ->join('catalogo as c1','c1.id','=','cg.region_id')
        ->join('catalogo as c2','c2.id','=','cg.municipio_id')
        ->SELECT('c2.clave as clv_municipio','c2.descripcion as municipio')
        ->whereNull('cg.deleted_at')
        ->where('c1.clave', '=', $id)
        ->orderBy('c2.clave')
        ->distinct()
        ->get();
        return response()->json($municipios, 200);
    }
    public function getLocalidades($id){
        $localidades = DB::table('clasificacion_geografica as cg')
        ->join('catalogo as c1','c1.id','=','cg.region_id')
        ->join('catalogo as c2','c2.id','=','cg.municipio_id')
        ->join('catalogo as c3','c3.id','=','cg.localidad_id')
        ->select('c3.clave as clv_localidad', 'c3.descripcion as localidad')
        ->where('c2.clave','=' ,$id)
        ->whereNull('cg.deleted_at')
        ->orderBy('c3.clave')
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
        $uppAutorizados = ClavesHelper::esAutorizada($upp);
        if ($uppAutorizados) {
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
    public function getPartidas($clasificacion,$upp){
        $vPosicionPre = DB::table('clasificacion_economica as ce')
        ->SELECT('ce.id','ce.deleted_at',
        'c1.clave as clv_capitulo','c1.descripcion as capitulo',
        'c2.clave as clv_concepto','c2.descripcion as concepto',
        'c3.clave as clv_partida_generica','c3.descripcion as partida_generica',
        'c4.clave as clv_partida_especifica','c4.descripcion as partida_especifica',
        'c5.clave as clv_tipo_gasto','c5.descripcion as tipo_gasto') 
        
        ->JOIN('catalogo as c1','ce.capitulo_id','=','c1.id') 
        ->JOIN('catalogo as c2', 'ce.concepto_id','=','c2.id')   
        ->JOIN('catalogo as c3','ce.partida_generica_id','=','c3.id') 
        ->JOIN('catalogo as c4','ce.partida_especifica_id','=','c4.id') 
        ->JOIN('catalogo as c5','ce.tipo_gasto_id','=','c5.id') 
        ->whereNull('ce.deleted_at');
        $array_where = [];
        $esAutorizada = ClavesHelper::esAutorizada($upp);
        array_push($array_where, ['rel_economica_administrativa.clasificacion_administrativa','=',$clasificacion]);
        array_push($array_where, ['vPosicionPre.deleted_at','=', null]);
        if ($esAutorizada) {
            array_push($array_where, ['vPosicionPre.clv_capitulo','!=',1]);
            array_push($array_where, [DB::raw("CONCAT(vPosicionPre.clv_capitulo,vPosicionPre.clv_concepto,vPosicionPre.clv_partida_generica,vPosicionPre.clv_partida_especifica,vPosicionPre.clv_tipo_gasto)"),'!=',398011]);
        }
        $partidas = DB::table('rel_economica_administrativa')
        ->leftJoinSub($vPosicionPre, 'vPosicionPre', function ($join){
            $join->on('rel_economica_administrativa.clasificacion_economica', '=', DB::raw("CONCAT(vPosicionPre.clv_capitulo,vPosicionPre.clv_concepto,vPosicionPre.clv_partida_generica,vPosicionPre.clv_partida_especifica,vPosicionPre.clv_tipo_gasto)"));
        })
        ->SELECT(
        'vPosicionPre.clv_capitulo',
        'vPosicionPre.clv_concepto',
        'vPosicionPre.clv_partida_generica',
        'vPosicionPre.clv_partida_especifica',
        'vPosicionPre.clv_tipo_gasto',
        'vPosicionPre.partida_especifica')
        // ->leftJoin('v_posicion_presupuestaria_llaves','rel_economica_administrativa.clasificacion_economica','=','v_posicion_presupuestaria_llaves.posicion_presupuestaria_llave')
        ->WHERE($array_where)
        ->DISTINCT()
        ->get();
        return response()->json($partidas,200);
    }
    public function getFondos($id,$subP,$ejercicio = 0){
        $array_where = [];
        $anio = '';
        $tipo = '';
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
        }else {
            $anio = date('Y');
        }
        $esAutorizada = ClavesHelper::esAutorizada($id);
                if (!$esAutorizada) {
                    $tipo = 'Operativo';
                }else {
                    $tipo = $subP != 'UUU'  ? 'Operativo' : 'RH';
                }
            array_push($array_where, ['tf.tipo', '=', $tipo]);
            array_push($array_where, ['tf.clv_upp', '=', $id]);
            array_push($array_where, ['tf.ejercicio', '=', $anio]);
            array_push($array_where, ['tf.deleted_at', '=', null]);
            // array_push($array_where, ['fondo.deleted_at', '=', null]);


        $fondos = DB::table('fondo as f')
        ->SELECT( 
        'tf.ejercicio','tf.clv_fondo',
        'c1.clave as clv_etiquetado','c1.descripcion as etiquetado',
        'c2.clave as clv_fuente_financiamiento','c2.descripcion as fuente_financiamiento',
        'c3.clave as clv_ramo','c3.descripcion as ramo',
        'c4.clave as clv_fondo_ramo','c4.descripcion as fondo_ramo',
        'c5.clave as clv_capital','c5.descripcion as capital',
        
        )
        ->JOIN('catalogo as c1', 'f.etiquetado_id', '=', 'c1.id') 
        ->JOIN('catalogo as c2', 'f.fuente_financiamiento_id', '=', 'c2.id') 
        ->JOIN('catalogo as c3', 'f.ramo_id', '=', 'c3.id') 
        ->JOIN('catalogo as c4', 'f.fondo_ramo_id', '=', 'c4.id') 
        ->JOIN('catalogo as c5', 'f.capital_id', '=', 'c5.id') 
        ->JOIN('techos_financieros as tf', 'c4.clave', '=', 'tf.clv_fondo') 
        
        // ->SELECT('techos_financieros.ejercicio' , 'techos_financieros.clv_fondo', 'fondo.fondo_ramo', 'fondo.clv_etiquetado', 
        // 'fondo.clv_fuente_financiamiento', 'fondo.clv_ramo', 'fondo.clv_capital')
        // ->leftJoin('fondo', 'techos_financieros.clv_fondo' ,'=', 'fondo.clv_fondo_ramo') 
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
        $disponible = 0;
        $presupuestoUpp = DB::table('techos_financieros')
        ->SELECT('presupuesto','tipo')
        ->WHERE('clv_upp', '=', $upp)
        ->WHERE('clv_fondo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->where(function ($presupuestoUpp) use ($rol) {
            //para que solo tome los recursos operativos en perfil upp
            if ($rol == 1) {
                $presupuestoUpp->where('tipo', '=', 'Operativo' );
            }
            //para que solo tome los recursos RH en perfil delegacion
            if ($rol == 2) {
                $presupuestoUpp->where('tipo', '=', 'RH' );
            }
        })
        ->WHERE('deleted_at', '=', null)
        ->first();
        $presupuestoAsignado = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->where(function ($presupuestoAsignado) use ($rol) {
            //para que solo tome los recursos operativos en perfil upp
            if ($rol == 1 || $rol == 0) {
                $presupuestoAsignado->where('tipo', '=', 'Operativo' );
            }
            //para que solo tome los recursos RH en perfil delegacion
            if ($rol == 2) {
                $presupuestoAsignado->where('tipo', '=', 'RH' );
            }
        })
        // ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
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
        $disponible = 0;
        $presupuestoUpp = DB::table('techos_financieros')
        ->SELECT('presupuesto','tipo')
        ->WHERE('clv_upp', '=', $upp)
        ->WHERE('clv_fondo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->where(function ($presupuestoUpp) use ($rol) {
            //para que solo tome los recursos operativos en perfil upp
            if ($rol == 1) {
                $presupuestoUpp->where('tipo', '=', 'Operativo' );
            }
            //para que solo tome los recursos RH en perfil delegacion
            if ($rol == 2) {
                $presupuestoUpp->where('tipo', '=', 'RH' );
            }
        })
        // ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        $presupuestoAsignado = DB::table('programacion_presupuesto')
        ->SELECT(DB::raw('SUM( total )AS TotalAsignado'))
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->where(function ($presupuestoAsignado) use ($rol) {
            //para que solo tome los recursos operativos en perfil upp
            if ($rol == 1 || $rol == 0) {
                $presupuestoAsignado->where('tipo', '=', 'Operativo' );
            }
            //para que solo tome los recursos RH en perfil delegacion
            if ($rol == 2) {
                $presupuestoAsignado->where('tipo', '=', 'RH' );
            }
        })
        // ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
        ->WHERE('deleted_at', '=', null)
        ->first();
        $asignado = DB::table('programacion_presupuesto')
        ->SELECT('total')
        ->WHERE ('upp', '=', $upp)
        ->WHERE('fondo_ramo', '=', $fondo)
        ->WHERE('ejercicio', '=', $ejercicio)
        ->where(function ($presupuestoAsignado) use ($rol) {
            //para que solo tome los recursos operativos en perfil upp
            if ($rol == 1 || $rol == 0) {
                $presupuestoAsignado->where('tipo', '=', 'Operativo' );
            }
            //para que solo tome los recursos RH en perfil delegacion
            if ($rol == 2) {
                $presupuestoAsignado->where('tipo', '=', 'RH' );
            }
        })
        // ->WHERE('tipo', '=', $subPrograma != 'UUU' ? 'Operativo' : 'RH' )
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
        $sector = DB::table('sector_linea_accion')
            ->SELECT('sector')
            ->WHERE('clv_linea_accion', '=', $clave)
            ->first();
        return response()->json($sector,200);
    }
    public function getPresupuestoAsignado(Request $request){
        $ejercicio = $request->ejercicio > 0 ? $request->ejercicio : 0;
        $upp = $request->upp != '' ? $request->upp : '';
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
                // rol auditor y gobDigital
                $rol = 3;
                break;
        }
        $array_where = [];
        $array_where2 = [];
        $array_whereCierre = [];
        $whereCierre = [];
        $anio = '';
        $tabla = 'programacion_presupuesto';
        if ($uppUsuario != '') {
            array_push($whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
            
        $ejercicioActual = DB::table('cierre_ejercicio_claves')->SELECT(DB::raw('MAX( ejercicio )AS ejercicio'))->WHERE($whereCierre)->first();
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
            if ($anio < $ejercicioActual->ejercicio) {
                $tabla = 'programacion_presupuesto_hist';
                // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
                array_push($array_where2, [$tabla.'.version', '=', 0]);
                array_push($array_where2, ['tipo', '=', 'Operativo']);
            }
        }else {
            $anio = date('Y');
            $tabla = 'programacion_presupuesto_hist';
            // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
            array_push($array_where2, [$tabla.'.version', '=', 0]);
            array_push($array_where2, ['tipo', '=', 'Operativo']);
        }
        $autorizado = ClavesHelper::esAutorizada($uppUsuario ? $uppUsuario : $upp);
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
                array_push($array_where, ['techos_financieros.clv_upp', '=', $uppUsuario]);
                array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
                array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
                array_push($array_where2, [$tabla.'.upp', '=', $uppUsuario]);
                if ($tabla == 'programacion_presupuesto') {
                    array_push($array_where2, [$tabla.'.deleted_at', '=', null]);
                }
               
                array_push($array_where2, [$tabla.'.ejercicio', '=', $anio]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
                array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($autorizado) {
                array_push($array_where, ['techos_financieros.tipo', '=', 'Operativo']);
                array_push($array_where2, [$tabla.'.tipo', '=', 'Operativo']);
            }
           
        }else {
            array_push($array_where, ['techos_financieros.deleted_at', '=', null]);
            array_push($array_where, ['techos_financieros.ejercicio', '=', $anio]);
            if ($tabla == 'programacion_presupuesto') {
                array_push($array_where2, [$tabla.'.deleted_at', '=', null]);
            }
            
            array_push($array_where2, [$tabla.'.ejercicio', '=', $anio]);
            array_push($array_whereCierre, ['cierre_ejercicio_claves.ejercicio', '=', $anio]);
            if ($upp != '') {
                array_push($array_where, ['techos_financieros.clv_upp', '=', $upp]);
                array_push($array_where2, [$tabla.'.upp', '=', $upp]);
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
        $calendarizados = DB::table($tabla)
        ->SELECT(DB::raw('enero + febrero + marzo + abril + mayo + junio + julio + agosto + septiembre + octubre + noviembre + diciembre as calendarizados'),'estado')
        ->where(function ($calendarizados) use ($rol,$array_where2,$tabla) {
            $calendarizados->where($array_where2);
            if ($rol == 2) {
                $arrayClaves = [];
                $uppAutorizados = DB::table('uppautorizadascpnomina')->select('clv_upp')->where('deleted_at','=',null)->get()->toArray();
                foreach ($uppAutorizados as $key => $value) {
                    array_push($arrayClaves, $value->clv_upp);
                }
                $calendarizados->whereIn($tabla.'.upp',$arrayClaves);
                $calendarizados->where($tabla.'.tipo', '=', 'RH');
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
            'upp'=>$upp,
        ];
        return response()->json(['response'=>$response],200);
    }
    public function getPanelPresupuestoFondo(Request $request){
        $ejercicio =  $request->ejercicio != '' ? $request->ejercicio : 0; 
        $clvUpp = $request->clvUpp != '' ? $request->clvUpp : '';
        $disponible = 0;
        $totalDisponible = 0;
        $totalAsignado = 0;
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
        $totalCalendarizado = 0;
        $uppUsuario = Auth::user()->clv_upp;
        $anio = '';
        $whereCierre = [];
        $arrayProgramacion = '';
        $tabla = 'programacion_presupuesto';
        if ($uppUsuario != '') {
            array_push($whereCierre, ['cierre_ejercicio_claves.clv_upp', '=', $uppUsuario]);
        }
        $ejercicioActual = DB::table('cierre_ejercicio_claves')->SELECT(DB::raw('MAX( ejercicio )AS ejercicio'))->WHERE($whereCierre)->first();

        $upp = ['clave'=>'000','descripcion'=>'Detalle General'];
        if ($ejercicio && $ejercicio > 0) {
            $anio = $ejercicio;
            $arrayProgramacion = "pp.ejercicio = ".$anio;
            if ($anio < $ejercicioActual->ejercicio) {
                $tabla = 'programacion_presupuesto_hist';
                // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
                $arrayProgramacion = "".$arrayProgramacion." && pp.version = 0";
            }
        }else {
            $anio = date('Y');
            $arrayProgramacion = "pp.ejercicio = ".$anio;
            $tabla = 'programacion_presupuesto_hist';
            // agregar que sea version cero cuando la tabla sea programcacion presupuesto historico
            $arrayProgramacion = "".$arrayProgramacion." && pp.version = 0";
        }
        $uppAutorizados = ClavesHelper::esAutorizada($clvUpp != '' ? $clvUpp : $uppUsuario);
        $arrayTechos = "tf.deleted_at IS NULL  && tf.ejercicio = ".$anio;
        if ($tabla == 'programacion_presupuesto') {
            $arrayProgramacion = "".$arrayProgramacion." && pp.deleted_at IS NULL";
        }
        
        if ($uppUsuario && $uppUsuario != null && $uppUsuario != 'null') {
            $arrayTechos = "".$arrayTechos." && tf.clv_upp = '".strval($uppUsuario)."'";
            $arrayProgramacion = "".$arrayProgramacion."&& pp.upp = '".strval($uppUsuario)."'";
            $upp =  DB::table('catalogo')
            ->SELECT('clave','descripcion')
            ->where('ejercicio',$anio)
            ->where('grupo_id', 6)
            
            ->where('clave', '=', $uppUsuario)
            ->first();
        }else {
            if ($clvUpp != '') {
                $arrayTechos = "".$arrayTechos." && tf.clv_upp = '".strval($clvUpp)."'";
                $arrayProgramacion = "".$arrayProgramacion."&& pp.upp = '".strval($clvUpp)."'";
            }
        } 
        if ($rol == 2) {
            $fondos = ClavesHelper::detallePresupuestoDelegacion($arrayTechos,$arrayProgramacion, $tabla);
        }else {
            if ($uppAutorizados) {
                if ($rol == 0) {
                    $fondos = ClavesHelper::detallePresupuestoGeneral($arrayTechos,$arrayProgramacion, $tabla);
                }else {
                    $fondos = ClavesHelper::detallePresupuestoAutorizadas($arrayTechos,$arrayProgramacion, $tabla);
                }
               
            }else {
                    $fondos = ClavesHelper::detallePresupuestoGeneral($arrayTechos,$arrayProgramacion, $tabla);
                }
        }
        
        $response = [
            'fondos' => $fondos,
            'upp' => $upp,
            'rol' => $rol
        ];
        return response()->json(['response'=>$response],200);
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
        switch ($grupo) {
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
        array_push($array_where, ['programacion_presupuesto.upp', '=', $request->upp ? $request->upp : $uppUsuario]);
        array_push($array_where, ['programacion_presupuesto.deleted_at', '=', null]);
        array_push($array_where, ['programacion_presupuesto.ejercicio', '=', $request->ejercicio]);
        try {
            $upp = $request->upp ? $request->upp : $uppUsuario;
            $ejer = DB::table('cierre_ejercicio_claves')->SELECT('ejercicio','estatus')->where('clv_upp','=' ,$upp)->where('ejercicio','=' ,$request->ejercicio)->first();
            $ejercicio = $ejer && $ejer != null ? $ejer->estatus : '';
            $estado = DB::table('programacion_presupuesto')->SELECT('estado')->WHERE($array_where)->first();
            if ($ejercicio !='Abierto' && $rol != 0 || $estado && $estado->estado != 0 && $rol != 0) {
                $response = [
                    'response'=>'errorAutorizacion',
                    'rol'=>$rol
                ];
                return response()->json($response,200);
            }else {
                $esConfirmable = ClavesHelper::esConfirmable($upp,$request->ejercicio);
                if ($esConfirmable) {
                    ProgramacionPresupuesto::where($array_where)->update([
                        'estado' => 1,
                    ]);
                    $b = array(
                        "username"=>Auth::user()->username,
                        "accion"=>'Confirmar',
                        "modulo"=>'Claves'
                     );
                     Controller::bitacora($b);
                }else {
                    $response = [
                        'response'=>'errorAutorizacion',
                        'rol'=>$rol
                    ];
                    return response()->json($response,200);
                }
                
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
    public function alertaAvtividades($upp,$ejercicio){
        $estatus = 0;
        $tieneMetas = MetasHelper::actividades($upp, 0,$ejercicio);
        //revisar el estatus en uno si estan confirmadas si no no mostrar mensaje;
        if (count($tieneMetas)) {
            $estatus = $tieneMetas[0]->estatus;
        }
        $response = [
            'estatus'=> $estatus,
            'metas'=>count($tieneMetas),
        ];
        return response()->json($response,200);
    }

    public function getManualCMC(){
        $file = "";
        $name = "";
        
        if(Auth::user()->id_grupo==4){
            $name = "CAP_Manual_de_Usuario_UPP-CargaMasivaClaves.pdf";
            $file= public_path()."/manuales/". $name;
        } 
        
        $headers = array('Content-Type: application/pdf',);

        return response()->download($file,$name,$headers);
    }

    
}