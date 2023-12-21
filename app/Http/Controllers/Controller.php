<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\administracion\Bitacora;
use App\Models\administracion\PermisosUpp;
use Request;
use Auth;
use DB;
use Session;
use Response;
use Log;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //Validacion de Permisos de Usuario
    //Variable global
    protected $bodyQuery;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->bodyQuery = '{
                "upp": "c06.clave",
                "pp": "c16.clave",
                "ur": "c08.clave",
                "subprograma": "c17.clave",
                "ejercicio": "e.ejercicio",
                "proyecto": "c18.clave",
                "linea_accion": "c13.clave",
                "id_epp": "e.id" ,
                "subsecretaria":"c07.clave",
                "programaPre": "c16.clave"

            }';

            $this->bodyQuery = json_decode($this->bodyQuery, true);
            return $next($request);
        });
    }
    public static function check_permission($funcion,$bt = true) {
        $permiso = DB::select('SELECT p.id
        FROM adm_rel_funciones_grupos p
        INNER JOIN adm_funciones f ON f.id = p.id_funcion
        WHERE f.funcion = ?
        AND f.id_sistema = ?
        AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = ?);', [$funcion, Session::get('sistema'), Auth::user()->id]);
    	if($permiso) {
                $estructura = DB::select('SELECT modulo, tipo FROM adm_funciones WHERE funcion=? AND id_sistema = ?', [$funcion, Session::get('sistema')]);
                if(count($estructura) > 0){
                     return true;
                }else{
                    abort('401');
                }
    		
        }
    	else
    		abort('401');
    }
    public static function check_permissionEdit($funcion,$upp) {
        $bool = false;
        if (Auth::user()->id_grupo == 1 ) {
            return true;
        }else{
            if($upp ===  Auth::user()->clv_upp){
                $bool = true;
            }else{
                abort('401');
            }
        }
        if($bool){
            $permiso = DB::select('SELECT p.id
            FROM adm_rel_funciones_grupos p
            INNER JOIN adm_funciones f ON f.id = p.id_funcion
            WHERE f.funcion = ?
            AND f.id_sistema = ?
            AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = ?);', [$funcion, Session::get('sistema'), Auth::user()->id]);
                if ($permiso) {
                    $estructura = DB::select('SELECT modulo, tipo FROM adm_funciones WHERE funcion=? AND id_sistema = ?', [$funcion, Session::get('sistema')]);
                    if (count($estructura) > 0) {
                        return true;
                    } else {
                        abort('401');
                    }
    
                } else
                    abort('401');

        }
        
    }
    public static function check_assign($name) {
            $permiso = DB::table('permisos_funciones')
                ->select(
                    'id_user',
                    'id_permiso',
                    )
            ->where('id_user', auth::user()->id)
            ->where('permisos_funciones.deleted_at',null)
            ->where('id_permiso', $name)->get();
    	if(count($permiso)) {
                    return true;
        }
    	else
    		abort('401');
    }
    public static function check_assignFront($name) {
        $permiso = DB::table('permisos_funciones')
            ->select(
                'id_user',
                'id_permiso',
                )
        ->where('id_user', auth::user()->id)
        ->where('permisos_funciones.deleted_at',null)
        ->where('id_permiso', $name)->get();
    	if(count($permiso)) {
    		return true;
        }
    	else
        return false;
    }
    public static function bitacora($bitArray) {
 
         $bitacora = new Bitacora();
         $bitacora->username = $bitArray['username'];
         $bitacora->accion =$bitArray['accion']; /* editar,crear,eliminar,consultar, descargar */;
         $bitacora->modulo = $bitArray['modulo'];
         $bitacora->ip_origen = Request::getClientIp();
         $bitacora->fecha_movimiento = Carbon::now()->isoFormat('YYYY-MM-DD');
         $bitacora->save();
    }
    public function getVEpp($request) {
        $query = DB::table('epp as e')
                ->select('e.presupuestable', 'e.con_mir', 'e.confirmado', 'e.ejercicio',
                         'e.deleted_at', 'e.created_at', 'e.updated_at');

       if ($request->part1 == 1) {
           $query = $query->addSelect('c01.clave as clv_sector_publico', 'c01.descripcion as sector_publico',
                                   'c02.clave as clv_sector_publico_f', 'c02.descripcion as sector_publico_f',
                                   'c03.clave as clv_sector_economia', 'c03.descripcion as sector_economia',
                                   'c04.clave as clv_subsector_economia', 'c04.descripcion as subsector_economia',
                                   'c05.clave as clv_ente_publico', 'c05.descripcion as ente_publico')
                           ->leftJoin('catalogo as c01', 'e.sector_publico_id', '=', 'c01.id')
                          ->leftJoin('catalogo as c02', 'e.sector_publico_f_id', '=', 'c02.id')
                          ->leftJoin('catalogo as c03', 'e.sector_economia_id', '=', 'c03.id')
                          ->leftJoin('catalogo as c04', 'e.subsector_economia_id', '=', 'c04.id')
                          ->leftJoin('catalogo as c05', 'e.ente_publico_id', '=', 'c05.id');
       }

       if ($request->part2 == 1) {
           $query = $query->addSelect('c09.clave as clv_finalidad', 'c09.descripcion as finalidad',
                                   'c10.clave as clv_funcion', 'c10.descripcion as funcion')
                          ->leftJoin('catalogo as c09', 'e.finalidad_id', '=', 'c09.id')
                          ->leftJoin('catalogo as c10', 'e.funcion_id', '=', 'c10.id');
       }

       if ($request->part3 == 1) {
           $query = $query->addSelect('c11.clave as clv_subfuncion', 'c11.descripcion as subfuncion',
                                   'c12.clave as clv_eje', 'c12.descripcion as eje',
                                   'c13.clave as clv_linea_accion', 'c13.descripcion as linea_accion',
                                   'c14.clave as clv_programa_sectorial', 'c14.descripcion as programa_sectorial',
                                   'c15.clave as clv_tipologia_conac', 'c15.descripcion as tipologia_conac')
                          ->leftJoin('catalogo as c11', 'e.subfuncion_id', '=', 'c11.id')
                          ->leftJoin('catalogo as c12', 'e.eje_id', '=', 'c12.id')
                          ->leftJoin('catalogo as c13', 'e.linea_accion_id', '=', 'c13.id')
                          ->leftJoin('catalogo as c14', 'e.programa_sectorial_id', '=', 'c14.id')
                          ->leftJoin('catalogo as c15', 'e.tipologia_conac_id', '=', 'c15.id');
       }

       if ($request->part4 == 1) {
           $query = $query->addSelect('c16.clave as clv_programa', 'c16.descripcion as programa',
                                   'c17.clave as clv_subprograma', 'c17.descripcion as subprograma',
                                   'c18.clave as clv_proyecto', 'c18.descripcion as proyecto')
                          ->leftJoin('catalogo as c16', 'e.programa_id', '=', 'c16.id')
                          ->leftJoin('catalogo as c17', 'e.subprograma_id', '=', 'c17.id')
                          ->leftJoin('catalogo as c18', 'e.proyecto_id', '=', 'c18.id');
       }
       if ($request->part5 == 1) {
            $query = $query->addSelect('c06.clave as clv_upp', 'c06.descripcion as upp',
                    'c08.clave as clv_ur', 'c08.descripcion as ur',
                    'c07.clave as clv_subsecretaria', 'c07.descripcion as subsecretaria')
                ->leftJoin('catalogo as c06', 'e.upp_id', '=', 'c06.id')
                ->leftJoin('catalogo as c07', 'e.subsecretaria_id', '=', 'c07.id')
                ->leftJoin('catalogo as c08', 'e.ur_id', '=', 'c08.id');
       }

       if ($request->distinct == 1) {
           $query = $query->distinct();
       }else {
           $query = $query->addSelect('e.id');
       }

       //Where Dinamico
    //    Log::info('where', [$request->where]);
    //    Log::info('where values', [$request->whereValues]);
       if ($request->where) {
           for($x = 0; $x < sizeof($request->where); $x ++) {
               if ($request->where[$x] == 'upp' && $request->part5 == 0) {
                   return response("Necesitas mandar como 1 la parte5 para poder usar la condición de la upp");
               }
               if ($request->where[$x] == 'pp' && $request->part4 == 0) {
                   return response("Necesitas mandar como 1 la parte4 para poder usar la condición de la pp");
               }
               if ($request->where[$x] == 'ur' && $request->part5 == 0) {
                return response("Necesitas mandar como 1 la parte5 para poder usar la condición de la ur");
                }
               $query = $query->where($this->bodyQuery[$request->where[$x]], $request->whereValues[$x]);
           }
       }
       

       //GroupBy dinamico
       if ($request->groupBy) {
           for($x = 0; $x < sizeof($request->groupBy); $x ++) {
               $query = $query->GroupBy($this->bodyQuery[$request->groupBy[$x]]);
           }
       }

       //OrderBy dinamico
       if ($request->orderBy) {
           for($x = 0; $x < sizeof($request->orderBy); $x ++) {
               $query = $query->orderBy($this->bodyQuery[$request->orderBy[$x]], $request->orderDirection[$x]);
           }
       }

       
       $query = $query->where('e.deleted_at',null)->where('presupuestable',1)->get();
       return $query;
    }

}
