<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administracion\Grupo;
use DB;

class GrupoController extends Controller
{
    //Consulta Vista Grupos
    public function getIndex() {
        Controller::check_permission('getGrupos');
    	return view('administracion.grupos.index');
    }
    //Consulta Tablero Grupos
    public function getData() {
        Controller::check_permission('getGrupos', false);
        $query = Grupo::where('deleted_at', null)->get();
    	return response()->json($query, 200);
    }
    //Inserta Grupo
    public function getCreate(){
        Controller::check_permission('postGrupos', false);
    	return view('administracion.grupos.create');
    }
    //Inserta Grupo
    public function postStore(Request $request){
        Controller::check_permission('postGrupos');
    	Grupo::create($request->all());
    	return response()->json("done", 200);
    }
    //Actualiza Grupo
    public function getUpdate($id){
        Controller::check_permission('putGrupos', false);
    	$grupo = Grupo::find($id);
    	return view('administracion.grupos.update', compact('grupo'));
    }
    //Actualiza Grupo
    public function postUpdate(Request $request){
        Controller::check_permission('putGrupos');
    	Grupo::find($request->id)->update($request->all());
    	return response()->json("done", 200);
    }
    //Elimina Grupo Borrado Lógico
    public function postDelete(Request $request){
        Controller::check_permission('deleteGrupos');
    	Grupo::where('id', $request->id)->delete();
    	return response()->json("done", 200);
    }
}
