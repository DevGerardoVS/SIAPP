<?php

namespace App\Http\Controllers\Calendarización;

use App\Models\Catalogo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Redirect;
use Datatables;
use App\Models\calendarizacion\Metas;
use Auth;
use DB;
use Log;
use Illuminate\Database\Query\JoinClause;


class MetasController extends Controller
{
	//Consulta Vista Usuarios
	public function getIndex()
	{
		return view('calendarización.metas.index');
	}
    public function getProyecto()
	{
		return view('calendarización.metas.proyecto');
	}
	public function getNames($id){
		$cvl = DB::table('proyectos_mir')
		->select(
			'id',
			'clv_upp',
			'clv_ur',
			'clv_programa',
			'clv_subprograma',
			'clv_proyecto',
			)
		->where('proyectos_mir.id', '=', $id)
		->where('proyectos_mir.deleted_at', '=', null)->get();
		$query = DB::table('v_epp')
		->select(
			'ur',
			'programa',
			'subprograma',
			'proyecto'
			)
		->where('v_epp.clv_ur','=',$cvl[0]->clv_ur)
		->where('v_epp.clv_upp', '=',$cvl[0]->clv_upp )
		->where('v_epp.clv_programa', '=',$cvl[0]->clv_programa )
		->where( 'v_epp.clv_subprograma', '=',$cvl[0]->clv_subprograma)
		->where('v_epp.clv_proyecto', '=', $cvl[0]->clv_proyecto)->get();
	$dataSet = [];
	foreach ($query as $key) {
		$i = array(
			$key->ur,
			$key->programa,
			$key->subprograma,
			$key->proyecto,
			" ",
		);
		$dataSet[] = $i;
	}
	return $dataSet[0];
	}
public function getMetasP($ur){
		Log::debug($ur);
	$activs = DB::table("programacion_presupuesto")
	->leftJoin('v_epp', 'v_epp.clv_proyecto', '=', 'programacion_presupuesto.proyecto_presupuestario')
		->select(
		'programacion_presupuesto.id',
		'programa_presupuestario as programa',
		'subprograma_presupuestario as subprograma',
		'v_epp.proyecto as proyecto'
		)
		->where('programacion_presupuesto.ur','=',$ur)
		->distinct()->get();
		/* ->where('upp','=',auth::user()->id_ente) */
		/* $dataSet = [];
		
		foreach ($activs as $key) {
         
			$i = array(
				"programa"=>$key->programa,
				"subprograma"=>$key->subprograma,
				"proyecto"=>$key->proyecto,
				"seleccion"=>$accion
			);
			$dataSet[] = $i;
		} */
		
		return  response()->json($activs, 200);

}
    public function getMetas()
	{
		$activs = DB::table("actividades_mir")
		->select(
		'actividades_mir.id',
		'proyecto_mir_id',
		'clv_actividad',
		'actividad',
		'metas.total AS total'
		)
		->leftJoin('metas', 'metas.actividad_id', '=', 'actividades_mir.id')
		->where('metas.total', '!=', null)
		->where('actividades_mir.deleted_at', '=', null);

		$query = DB::table('proyectos_mir')
		->leftJoinSub($activs, 'act', function ($join) {
			$join->on('proyectos_mir.id', '=', 'act.proyecto_mir_id');
		})
			->select(
				'proyectos_mir.id',
				'proyectos_mir.clv_upp AS upp',
				'proyectos_mir.clv_ur AS ur',
				'proyectos_mir.clv_programa AS programa',
				'proyectos_mir.clv_subprograma AS subprograma',
				'proyectos_mir.clv_proyecto AS proyecto',
				'catalogo.descripcion',
				'programacion_presupuesto.fondo_ramo AS fondo',
				DB::raw(
				"SUM(enero
				+ febrero
				+ marzo 
				+ abril
				+ mayo
				+ junio
				+ julio
				+ agosto
				+ septiembre
				+ octubre
				+ noviembre
				+ diciembre) 
				AS presupuesto"),
				'act.actividad',
				'act.total'
				)
			->leftJoin('catalogo', 'catalogo.clave', '=', 'proyectos_mir.clv_proyecto')
			->where('catalogo.grupo_id', '=', 18)
			->leftJoin("actividades_mir",'actividades_mir.proyecto_mir_id','=','proyectos_mir.id')
			->join('programacion_presupuesto', function (JoinClause $join) {
				$join->on('proyectos_mir.clv_upp', '=', 'programacion_presupuesto.upp')
					->orOn('programacion_presupuesto.programa_presupuestario', '=', 'proyectos_mir.clv_programa')
					->orOn('programacion_presupuesto.subprograma_presupuestario', '=', 'proyectos_mir.clv_subprograma')
					->orOn('programacion_presupuesto.proyecto_presupuestario', '=', 'proyectos_mir.clv_proyecto');
			})->groupByRaw('proyectos_mir.clv_upp,proyectos_mir.clv_ur,proyectos_mir.clv_programa,proyectos_mir.clv_subprograma,proyectos_mir.clv_proyecto,catalogo.descripcion,programacion_presupuesto.fondo_ramo')
		->where('proyectos_mir.deleted_at', '=', null)->get();
		$dataSet = [];
		
		foreach ($query as $key) {
            $accion = $key->actividad != ""? '<buttton type="button" class="btn btn-success"  onclick="dao.editarMeta('.$key->id.')" class="button"><i  class="fa fa-pencil"></i></buttton>':
			'<a type="button" class="btn btn-primary"  href="/calendarizacion/proyecto/'.$key->id.'" class="button"><i class="fa-plus">Agregar</i></a>';
			$i = array(
				$key->ur,
				$key->programa,
				$key->subprograma,
				$key->descripcion,
				$key->fondo,
				$key->presupuesto,
                $key->actividad,
                $key->total,
				$accion,
			);
			$dataSet[] = $i;
		}
		return ['dataSet'=>$dataSet];
	}
    public function getProyect($id = 0)
	{
		$query = DB::table('adm_users')
			->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', DB::raw('CONCAT(adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as nombre_completo'), 'adm_users.celular', DB::raw('ifnull(adm_grupos.nombre_grupo, "Sudo") as perfil'), 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo'))
			->leftJoin('adm_rel_user_grupo', 'adm_users.id', '=', 'adm_rel_user_grupo.id_usuario')
			->leftJoin('adm_grupos', 'adm_rel_user_grupo.id_grupo', '=', 'adm_grupos.id')
			->where('adm_users.deleted_at', '=', null)
            ->orderby('adm_users.estatus');

		if ($id != 0) {
			$query = $query->where('adm_users.id', '=', $id);
		}

		$query = $query->get();
		$dataSet = [];
		
		foreach ($query as $key) {
            $accion = '<button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false" ><i class="fa-plus"></i>Agregar</button>';
			$i = array(
				$key->username,
				$key->email,
				$key->nombre_completo,
				$key->celular,
				$key->perfil,
				$key->estatus == 1 ? "Activo" : "Inactivo",
                " ",
                " ",
				$accion,
			);
			$dataSet[] = $i;
		}
		return $dataSet;
	}
	public function getUrs()
	{
	 	$urs = DB::table('catalogo')
			->select(
				'id',
				'clave',
				'descripcion'
			)
			->where('deleted_at', null)
			->where('grupo_id', 8)
			->get(); 
		return $urs;
	}
	public function getProgramas($ur)
	{
		log::debug($ur);
	 	$urs = DB::table('v_epp')
			->select(
				'id',
				'clv_programa',
				'programa'
			)
			->where('clv_ur', $ur)
			->get(); 
		return $urs;
	}
	public function getSelects()
	{
		$uMed = DB::table('unidades_medida')
			->select(
				'id as clave',
				'unidad_medida'
			)
			->where('deleted_at', null)
			->get();
			$fondos = DB::table('fondo')
			->select(
				'id',
				'clv_fondo_ramo',
				'fondo_ramo as ramo'
			)
			->where('deleted_at', null)
			->distinct()->get();
		/* $activ = Http::acceptJson()->get('https://pokeapi.co/api/v2/pokemon/');
		$res = json_decode($activ->body()); */
		$activ = DB::table('actividades_mir')
			->select(
				'id',
				'clv_actividad',
				'actividad'
			)
			->where('deleted_at', null)
			->get(); 
			$bene = DB::table('beneficiarios')
			->select(
				'id',
				'clave',
				'beneficiario'
			)
			->where('deleted_at', null)
			->get();
			$tAct = ["Acumulativa", "Continua", "Especial"];
		return ["unidadM"=>$uMed,"fondos"=>$fondos,"beneficiario"=>$bene,"actividades"=>$activ,"activids"=>$tAct ];
	}
	public function createMeta(Request  $request){
		log::debug($request);
		$meta = Metas::create([
			'proyecto_mir_id ' => intval($request->pMir_id),
			'actividad_id' => $request->sel_actividad,
			'clv_fondo'=> $request->sel_fondo,
			'estatus'=>0,
			'tipo' => $request->tipo_Ac,
			'beneficiario_id' => $request->tipo_Be,
			'unidad_medidad_id' => intval($request->medida),
			'cantidad_beneficiarios' => $request->beneficiario,
			'total'=>$request->sumMetas,
			'enero'=> $request[1] != NULL ?$request[1] :0,
			'febrero'=> $request[2] != NULL ?$request[2] :0,
			'marzo'=> $request[3] != NULL ?$request[3] :0,
			'abril'=> $request[4] != NULL ?$request[4] :0,
			'mayo'=> $request[5] != NULL ?$request[5] :0,
			'junio'=> $request[6] != NULL ?$request[6] :0,
			'julio'=> $request[7] != NULL ?$request[7] :0,
			'agosto'=>$request[8] != NULL ?$request[8] :0,
			'septiembre'=> $request[9] != NULL ?$request[9] :0,
			'octubre'=> $request[10]!= NULL ?$request[10] :0,
			'noviembre'=> $request[11]!= NULL ?$request[11] :0,
			'diciembre'=> $request[12]!= NULL ?$request[12] :0,
		]);
		return $meta;

	}
	public function getMetasXp()
	{
			$query = DB::table('metas')
			->leftJoin('actividades_mir', 'actividades_mir.id', '=', 'metas.actividad_id')
			->leftJoin('fondo', 'fondo.clv_fondo_ramo', '=', 'metas.clv_fondo')
			->leftJoin('beneficiarios', 'beneficiarios.id', '=', 'metas.beneficiario_id')
			->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'metas.unidad_medidad_id')
			->select(
				'metas.id',
				'actividades_mir.actividad',
				'total',
				'fondo.ramo',
				'tipo',
				'cantidad_beneficiarios',
				'beneficiarios.beneficiario',
				'unidades_medida.unidad_medida',
				
			)->where('metas.deleted_at', '=', null);
				$query = $query->get();
		$dataSet = [];
		foreach ($query as $key) {
            $accion ='<a data-toggle="modal" data-target="#addActividad" data-backdrop="static" data-keyboard="false" title="Modificar meta"
			class="btn btn-sm"onclick="dao.editarUsuario('.$key->id.')">' .
                        '<i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' .
                        '<button title="Eliminar meta" class="btn btn-sm" onclick="dao.eliminar('.$key->id. ')">' .
                        '<i class="fa fa-trash" style="color:B40000;" ></i></button>&nbsp;';
			$i = array(
				$key->actividad,
				$key->total,
				$this->actividad($key->tipo),
				$key->cantidad_beneficiarios,
                $key->beneficiario,
                $key->unidad_medida,
				$key->ramo,
				$accion,
			);
			$dataSet[] = $i;
		}
		return [ 'dataSet'=>$dataSet];
	}
	public function actividad($id)
	{
		switch ($id) {
			case 0:
				return 'Acumulativa';
			case 1:
				return 'Continua';
			case 2:
				return 'Especial';
			default:
				break;
		}

	}
	public function deleteMeta(Request $request)
	{
		//Controller::check_permission('deleteUsuarios');
		Metas::where('id', $request->id)->delete();
		return response()->json("done", 200);
	}
	public function updateMeta($id)
	{
		//Controller::check_permission('putUsuarios', false);
		Log::debug($id);
		$query =Metas::where('id', $id)->get();
		return $query;
	}
	
	//Vista Create Usuario
/* 	public function getCreate()
	{
		return view('calendarización.metas.index');
	} */

	//Vista Update Usuario
/* 	public function getUpdate($id)
	{
		Controller::check_permission('putUsuarios', false);
		$query = DB::table('adm_users')
			->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', 'adm_users.nombre', 'adm_users.p_apellido', 'adm_users.s_apellido',  'adm_users.celular', DB::raw('ifnull(adm_grupos.nombre_grupo, "Sudo") as perfil'), 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo') ,'adm_grupos.nombre_grupo')
			->leftJoin('adm_rel_user_grupo', 'adm_users.id', '=', 'adm_rel_user_grupo.id_usuario')
			->leftJoin('adm_grupos', 'adm_rel_user_grupo.id_grupo', '=', 'adm_grupos.id')
			->where('adm_users.deleted_at', '=', null)
			->where('adm_users.id', '=', $id)->get();


		return $query[0];
	}
	//Consulta Tablero Usuarios
	public function getData($id = 0)
	{
		Controller::check_permission('getUsuarios', false);
		$query = DB::table('adm_users')
			->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', DB::raw('CONCAT(adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as nombre_completo'), 'adm_users.celular', DB::raw('ifnull(adm_grupos.nombre_grupo, "Sudo") as perfil'), 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo'))
			->leftJoin('adm_rel_user_grupo', 'adm_users.id', '=', 'adm_rel_user_grupo.id_usuario')
			->leftJoin('adm_grupos', 'adm_rel_user_grupo.id_grupo', '=', 'adm_grupos.id')
			->where('adm_users.deleted_at', '=', null)
            ->orderby('adm_users.estatus');

		if ($id != 0) {
			$query = $query->where('adm_users.id', '=', $id);
		}

		$query = $query->get();
		$dataSet = [];
		
		foreach ($query as $key) {
			$accion ='<a data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false" title="Modificar Usuario"
			class="btn btn-sm"onclick="dao.editarUsuario('.$key->id.')">' .
                        '<i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' .
                        '<a data-toggle="tooltip" title="Inhabilitar/Habilitar Usuario" class="btn btn-sm" onclick="dao.setStatus(' .$key->id. ', ' .$key->estatus.')">' .
                        '<i class="fa fa-lock"></i></a>&nbsp;'
                        /*'<a data-toggle="tooltip" title="Eliminar Usuario" class="btn btn-sm" onclick="dao.eliminarUsuario('.$key->id. ')">' .
                        '<i class="fa fa-trash" style="color:B40000;" ></i></a>&nbsp;';
			$i = array(
				$key->username,
				$key->email,
				$key->nombre_completo,
				$key->celular,
				$key->perfil,
				$key->estatus == 1 ? "Activo" : "Inactivo",
				$accion,
			);
			$dataSet[] = $i;
		}
		return [ 'dataSet'=>$dataSet];
	}
	//Confirmar Email
	public function getCheckemail(Request $request)
	{
		Controller::check_permission('getUsuarios', false);
		$result = \DB::table('adm_users')->where('email', $request->email)->where('id', '<>', $request->id_usuario)->first();
		return response()->json($result, 200);
	}


	//Inserta Usuario
	public function postStore(Request $request)
	{
		if ($request->id_user != 0) {
			$this->postUpdate($request);
		} else {
			Controller::check_permission('postUsuarios');
			$validaUserName = User::where('username', $request->username)->get();
			$validaEmail = User::where('email', $request->email)->get();
			if ($validaUserName->isEmpty() == false) {
				return response()->json("userDuplicate", 200);
			}
			if ($validaEmail->isEmpty() == false) {
				return response()->json("emailDuplicate", 200);
			}
			$user = User::create($request->all());
			UsuarioGrupo::create([
				'id_grupo' => $request->id_grupo,
				'id_usuario' => $user->id
			]);

			return response()->json("done", 200);
		}
	}
	//Reset Password
	public function postResetpwd(Request $request)
	{
		$user = User::find($request->id);
		$user->password = $request->password;
		$user->save();
		return response(200);
	}
	//Actualiza Usuario
	public function postUpdate(Request $request)
	{
		Controller::check_permission('putUsuarios');
 		User::find($request->id_user)->update($request->all());
		return response()->json("done", 200);
	}
	//Elimina Usuario
	public function postDelete(Request $request)
	{
		Controller::check_permission('deleteUsuarios');
		User::where('id', $request->id)->delete();
		return response()->json("done", 200);
	}
	//Consulta Grupos para Usuarios
	public function getGrupos($id)
	{
		Controller::check_permission('postGrupos', false);
		$usuario = User::find($id);
		$grupos_disponibles = DB::select('SELECT id, nombre_grupo FROM adm_grupos WHERE id NOT IN(SELECT id_grupo FROM adm_rel_user_grupo WHERE id_usuario = ?) AND deleted_at IS NULL', [$id]);
		$grupos_asignados = DB::select('SELECT id, nombre_grupo FROM adm_grupos WHERE id IN (SELECT id_grupo FROM adm_rel_user_grupo WHERE id_usuario = ?) AND deleted_at IS NULL', [$id]);
		$grupos = ["disponibles" => $grupos_disponibles, "asignados" => $grupos_asignados];
		return view('administracion.usuarios.grupos', compact('usuario'), compact('grupos'));
	}

	//Inserta Roles
	public function postGrupos(Request $request)
	{
		//Controller::check_permission('addRole');
		if ($request->grupos):
			UsuarioGrupo::where('id_usuario', '=', $request->id)->delete();
			foreach ($request->grupos as $grupo) {
				$user_role = new UsuarioGrupo();
				$user_role->id_usuario = $request->id;
				$user_role->id_grupo = $grupo;
				$user_role->save();
			}
			return response()->json("done", 200);
		endif;
		return response()->json("nada", 200);


	}
	public function grupos()
	{
		$perfil = DB::table('adm_grupos')->select(
			'id',
			'nombre_grupo',
			'estatus'
		)->where('deleted_at','=',null)
		->get();
		return response()->json($perfil, 200);
	}
	//Actualiza Estatus de Usuario
	public function postStatus(Request $request)
	{
		$user = User::find($request->id);
		if ($request->estatus == 1) {
			$user->estatus = 2;
		} else {
			$user->estatus = 1;
		}
		$user->save();
		return response()->json("done", 200);
	}
	//Actualiza Contraseña
	public function postUpdatepwd(Request $request)
	{
		$usuario = User::find(Auth::user()->id);
		if (\Hash::check($request->old_password, $usuario->password)) {
			$usuario->password = $request->new_password;
			$usuario->save();
			return "success";
		}
		return "error";
	}
	public function postRpassword(Request $request)
	{
		$user = User::find($request->id);
		$user->password = $request->password;
		$user->save();
		return response(200);
	} */

}