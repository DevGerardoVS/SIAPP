<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use Datatables;
use App\Models\User;
use Auth;
use DB;
use Log;
use App\Models\administracion\UsuarioGrupo;

class UsuarioController extends Controller
{
	//Consulta Vista Usuarios
	public function getIndex()
	{
		Controller::check_permission('getUsuarios');
		return view('usuarios.index');
	}
	//Vista Create Usuario
	public function getCreate()
	{
		Controller::check_permission('postUsuarios', false);
		return view('usuarios.create');
	}

	//Vista Update Usuario
	public function getUpdate($id)
	{
		Controller::check_permission('putUsuarios', false);
		$query = DB::table('adm_users')
			->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', 'adm_users.nombre', 'adm_users.p_apellido', 'adm_users.s_apellido',  'adm_users.celular', DB::raw('ifnull(adm_grupos.nombre_grupo, "Sudo") as perfil'), 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo'))
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
			->where('adm_users.deleted_at', '=', null);
		if ($id != 0) {
			$query = $query->where('adm_users.id', '=', $id);
		}

		$query = $query->get();
		$dataSet = [];
		
		foreach ($query as $key) {
			$accion ='<a data-toggle="tooltip" title="Modificar Usuario"  class="btn btn-sm"onclick="dao.editarUsuario('.$key->id.')" >' .
                        '<i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' .
                        '<a data-toggle="tooltip" title="Inhabilitar/Habilitar Usuario" class="btn btn-sm" onclick="dao.setStatus(' .$key->id. ', ' .$key->estatus.')">' .
                        '<i class="fa fa-lock"></i></a>&nbsp;' .
                        '<a data-toggle="tooltip" title="Eliminar Usuario" class="btn btn-sm" onclick="dao.eliminarUsuario('.$key->id. ')">' .
                        '<i class="fa fa-trash" style="color:B40000;" ></i></a>&nbsp;';
			$i = array(
				$key->username,
				$key->email,
				$key->nombre_completo,
				$key->celular,
				$key->perfil,
				$key->estatus,
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
		return view('usuarios.grupos', compact('usuario'), compact('grupos'));
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
		)->get();
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
	}

}