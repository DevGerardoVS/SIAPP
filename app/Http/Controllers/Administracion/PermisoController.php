<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administracion\Grupo;
use App\Models\administracion\Menu;
use App\Models\administracion\Funciones;
use App\Models\administracion\Sistema;
use App\Models\administracion\Permisos;
use App\Models\administracion\MenuGrupo;
use App\Models\administracion\SistemaGrupo;
use DB;

class PermisoController extends Controller
{
    //Consulta Grupo Correspondiente
    public function getGrupo($id) {
        Controller::check_permission('getPermisos');
    	$grupo = Grupo::find($id);
		$permisos = Permisos::where('id_grupo', '=', $grupo->id)->get();
		$menus = MenuGrupo::where('id_grupo', '=', $grupo->id)->get();
		$asignados = [];
		foreach($permisos as $permiso)
			$asignados[] = $permiso->id_funcion;
		$menus_asignados = [];
		foreach($menus as $menu)
			$menus_asignados[] = $menu->id_menu;

        $funciones_sistema = DB::select('SELECT COUNT(id) AS total FROM adm_rel_funciones_grupos WHERE id_grupo = ? AND id_funcion IN (SELECT id FROM adm_funciones)', [$grupo->id])[0];
        $menus_sistema = DB::select('SELECT COUNT(id) AS total FROM adm_rel_menu_grupo WHERE id_grupo = ? AND id_menu IN (SELECT id FROM adm_menus)', [$grupo->id])[0];
        $funciones = Funciones::get();
        $menus = Menu::get();
		$data = ["grupo" => $grupo, "asignados" => $asignados, "menus" => $menus_asignados];
        //dd($data);
		return view('administracion.permisos.index', compact('data'));
    }
    //Asigna Grupo a Usuario
    public function postAsigna(Request $request) {
        Controller::check_permission('postPermisos', false);
    	$permiso = new Permisos();
		$permiso->id_grupo = $request->role;
		$permiso->id_funcion = $request->modulo;
		$permiso->save();
		return "true";
    }
    //Elimina Permisos de Usuario
    public function postRemueve(Request $request) {
        Controller::check_permission('deletePermisos', false);
    	Permisos::where('id_grupo', '=', $request->role)->where('id_funcion', '=', $request->modulo)->delete();
    }
    //Asigna Menu a Grupo
    public function postMasigna(Request $request) {
        Controller::check_permission('postPermisos', false);
    	$menu = new MenuGrupo();
		$menu->id_grupo = $request->role;
		$menu->id_menu = $request->menu;
		$menu->save();
		return "true";
    }
    //Desasigna Menu a Grupo
    public function postMremueve(Request $request) {
        Controller::check_permission('deletePermisos', false);
    	MenuGrupo::where('id_grupo', '=', $request->role)->where('id_menu', '=', $request->menu)->delete();
    }
    //Asigna Rol a Sistema
    public function postSasigna(Request $request) {
        Controller::check_permission('postPermisos', false);
    	$sistema = new SistemaGrupo();
    	$sistema->id_grupo = $request->role;
    	$sistema->save();
    	return "true";
    }
    //DesAsigna Rol a Sistema
    public function postSremueve(Request $request) {
        Controller::check_permission('deletePermisos', false);
    	SistemaGrupo::where('id_grupo', '=', $request->role)->delete();
    }
    //Asigna Todos los Permisos a Grupo
    public function postAllPermission(Request $request) {
        if($request->type == "add") {
            $menus = Menu::get();
            foreach($menus as $menu) {
                $permiso = new MenuGrupo();
                $permiso->id_grupo = $request->role;
                $permiso->id_menu = $menu->id;
                $permiso->save();
            }
            $funciones = Funciones::get();
            foreach($funciones as $funcion) {
                $permiso = new Permisos();
                $permiso->id_grupo = $request->role;
                $permiso->id_funcion = $funcion->id;
                $permiso->save();
            }
            $permiso = new SistemaGrupo();
            $permiso->id_grupo = $request->role;
            $permiso->save();
        } else {
            SistemaGrupo::where('id_grupo', $request->role)->delete();
            $menus = Menu::get();
            foreach($menus as $menu) {
                MenuGrupo::where('id_grupo', $request->role)->where('id_menu', $menu->id)->delete();
            }
            $funciones = Funciones::get();
            foreach($funciones as $funcion) {
                Permisos::where('id_grupo', $request->role)->where('id_funcion', $funcion->id)->delete();
            }
        }
        return 200;
    }
}
