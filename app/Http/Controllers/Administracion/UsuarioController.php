<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use App\Models\administracion\PermisosUpp;
use App\Models\administracion\UsuarioGrupo;
use App\Models\catalogos\CatPermisos;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;
use PDF;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UsuarioController extends Controller
{
    //Consulta Vista Usuarios
    public function getIndex()
    {
        Controller::check_permission('getUsuarios');
        return view('administracion.usuarios.index');
    }
    public function getIndexUP()
    {
        Controller::check_permission('getUsuarios');
        return view('administracion.permisos.indexAdicionales');
    }
    public function getUsers()
    {
        $user = DB::table('adm_users')->select(
            'id',
            'username',
            DB::raw('CONCAT(username," ","-"," ",adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as fullname'),

        )->where('deleted_at', null)->get();

        return $user;
    }
    public function getModulos()
    {
        $modul = DB::table('adm_menus')->select('id', 'nombre_menu as nombre')->where('deleted_at', null)->where('padre', 0)->get();
        return $modul;
    }
    public function assignPermisson(Request $request)
    {

        Log::debug($request);
        if ($request->id == null) {
            $resul = PermisosUpp::create([
                'id_user' => $request->id_userP,
                'id_permiso' => $request->id_permiso,
                'descripcion' => $request->descripcion,
            ]);
        } else {
            /* 
            checar los permisos que tiene 
            verificar si tiene el permiso y si viene en el request 
            si tiene registro del permiso y no esta en el request borrar 
            si no esta en el registro y y viene en el request agregar

             */
            $resul = PermisosUpp::where('id', $request->id)->get();
            $user = PermisosUpp::where('deleted_at', null)->where('id_user', $request->id_userP)->get();
            foreach ($user as $key) {
                switch ($key->id_permiso) {
                    case 1:
                        if (isset($request->masiva)) {
                            $resul->id_permiso = $request->masiva;
                            $resul->descripcion = $request->descripcion;
                            $resul->save();
                        } else {
                            PermisosUpp::where('id', $request->id)->delete();
                        }
                        break;
                    case 2:
                        # code...
                        break;
                    case 3:
                        # code...
                        break;

                    default:
                        # code...
                        break;
                }
                if (isset($request->oficio)) {
                    $resul->id_permiso = 3;
                    $resul->descripcion = $request->descripcion;
                    $resul->save();
                }
                if (isset($request->obra)) {
                    $resul->id_permiso = 2;
                    $resul->descripcion = $request->descripcion;
                    $resul->save();
                }
                if (isset($request->masiva)) {
                    $resul->id_permiso = 1;
                    $resul->descripcion = $request->descripcion;
                    $resul->save();
                }
            }
        }
        if ($resul || $resul->wasChanged()) {
            $res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
            return response()->json($res, 200);
        } else {
            $res = ["status" => false, "mensaje" => ["icon" => 'Error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
            return response()->json($res, 200);
        }
    }
    public function createPermisson(Request $request)
    {
        $resul = CatPermisos::create($request->all());
        if ($resul) {
            $res = ["status" => true, "mensaje" => ["icon" => 'success', "text" => 'La acción se ha realizado correctamente', "title" => "Éxito!"]];
            return response()->json($res, 200);
        } else {
            $res = ["status" => false, "mensaje" => ["icon" => 'Error', "text" => 'Hubo un problema al querer realizar la acción, contacte a soporte', "title" => "Error!"]];
            return response()->json($res, 200);
        }
    }
    public function getPermisson()
    {
        $permisos = CatPermisos::where('deleted_at', null)->get();
        return response()->json($permisos, 200);
    }
    //Vista Create Usuario
    public function getCreate()
    {
        Controller::check_permission('postUsuarios', false);
        return view('administracion.usuarios.create');
    }

    //Vista Update Usuario
    public function getUpdate($id)
    {
        Controller::check_permission('putUsuarios', false);
        $query = DB::table('adm_users')
            ->select('adm_users.id', 'adm_users.username', 'adm_users.email', 'adm_users.estatus', 'adm_users.nombre', 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.celular', 'adm_users.p_apellido', 'adm_users.s_apellido', 'adm_users.nombre', DB::raw('ifnull(adm_grupos.id, "null") as id_grupo'), 'adm_grupos.nombre_grupo')
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
            ->select(
                'adm_users.id',
                'adm_users.username',
                'adm_users.email',
                'adm_users.estatus',
                DB::raw("IFNULL(adm_users.clv_upp, NULL) AS upp"),
                DB::raw('CONCAT(adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as nombre_completo'),
                'adm_users.celular',
                DB::raw('adm_grupos.nombre_grupo as grupo'),
                'adm_users.p_apellido',
                'adm_users.s_apellido',
                'adm_users.nombre',
                DB::raw('ifnull(adm_grupos.id, "null") as id_grupo')
            )
            ->leftJoin('adm_grupos', 'adm_grupos.id', '=', 'adm_users.id_grupo')
            ->where('adm_users.deleted_at', '=', null)
            ->orderby('adm_users.estatus');

        if ($id != 0) {
            $query = $query->where('adm_users.id', '=', $id);
        }

        $query = $query->get();
        $dataSet = [];

        foreach ($query as $key) {
            Auth::user()->id_grupo;
            $accion = Auth::user()->id_grupo != 3 ? '<a data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false" title="Modificar Usuario"
			class="btn btn-sm"onclick="dao.editarUsuario(' . $key->id . ')">' .
                '<i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' .
                '<a data-toggle="tooltip" title="Inhabilitar/Habilitar Usuario" class="btn btn-sm" onclick="dao.setStatus(' . $key->id . ', ' . $key->estatus . ')">' .
                '<i class="fa fa-lock"></i></a>&nbsp;' : '';
            $i = array(
                $key->username,
                $key->email,
                $key->nombre_completo,
                $key->celular,
                $key->grupo,
                $key->estatus == 1 ? "Activo" : "Inactivo",
                $accion,
            );
            $dataSet[] = $i;
        }
        return ['dataSet' => $dataSet];
    }
    public function getDataUP()
    {
        Controller::check_permission('postMetas', false);
        $query = PermisosUpp::select(
            'permisos_funciones.id',
            'permisos_funciones.id_user',
            'permisos_funciones.id_permiso',
            'adm_users.username',
            DB::raw('CONCAT(adm_users.nombre, " ", adm_users.p_apellido, " ", adm_users.s_apellido) as nombre_completo'),
            DB::raw('GROUP_CONCAT(cat_permisos.nombre SEPARATOR " / ") AS permiso'),
            'adm_grupos.nombre_grupo AS grupo'
        )
            ->leftJoin('adm_users', 'adm_users.id', '=', 'permisos_funciones.id_user')
            ->leftJoin('cat_permisos', 'cat_permisos.id', '=', 'permisos_funciones.id_permiso')
            ->leftJoin('adm_grupos', 'adm_grupos.id', '=', 'adm_users.id_grupo')
            ->groupBy('permisos_funciones.id_user')
            ->get();
        $dataSet = [];

        foreach ($query as $key) {
            Auth::user()->id_grupo;
            $accion = Auth::user()->id_grupo != 3 ? '<a  data-toggle="modal" data-target=".permisosModalE" data-backdrop="static"
			data-keyboard="false" onclick="dao.editarUp(' . $key->id . ',' . $key->id_user . ',' . $key->id_permiso . ')"><i class="fa fa-pencil" style="color:green;"></i></a>&nbsp;' : '';
            $i = array(
                $key->username,
                $key->nombre_completo,
                $key->grupo,
                $key->permiso,
                $accion,
            );
            $dataSet[] = $i;
        }
        return ['dataSet' => $dataSet];
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
        if ($request->id_user != null) {
            $this->postUpdate($request);
        } else {
            Controller::check_permission('postUsuarios');
            $validaUserName = User::where('username', $request->username)->get();
            $validaEmail = User::where('email', $request->email)->get();
            if ($validaUserName->isEmpty() == false) {
                return response()->json(["icon" => 'info', "title" => "Error!", "text" => "Username duplicado"], 200);
            }
            if ($validaEmail->isEmpty() == false) {
                return response()->json(["icon" => 'info', "title" => "Error!", "text" => "email duplicado"], 200);
            }
            $user = User::create($request->all());
            UsuarioGrupo::create([
                'id_grupo' => $request->id_grupo,
                'id_usuario' => $user->id,
            ]);

            return response()->json(["success" => 'info', "title" => "Éxito!", "text" => "Usuario guardado"], 200);
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
    public function getUpp()
    {
        $upp = DB::table('catalogo')->select(
            'id',
            'clave',
            'descripcion'
        )->where('grupo_id', '=', 6)
            ->get();
        return $upp;
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
        )->where('deleted_at', '=', null)
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
    }
    public function exportExecel()
    {
        /*Si no coloco estas lineas Falla*/
        ob_end_clean();
        ob_start();
        /*Si no coloco estas lineas Falla*/
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar Usuarios Excel',
            "modulo" => 'Usuarios'
        );
        Controller::bitacora($b);
        return Excel::download(new UsersExport(), 'Listado de Usuarios.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    public function exportPdf()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
        $data = DB::table('adm_users')
            ->select('id', 'nombre', 'p_apellido', 's_apellido', 'username', 'email', 'celular')
            ->orderBy('nombre', 'asc')
            ->get();
        view()->share('data', $data);
        $pdf = PDF::loadView('administracion.usuarios.usuariosPdf')->setPaper('a4', 'landscape');
        $b = array(
            "username" => Auth::user()->username,
            "accion" => 'Descargar Usuarios PDF',
            "modulo" => 'Usuarios'
        );
        Controller::bitacora($b);
        return $pdf->download('Lista de Usuarios.pdf');
    }

}