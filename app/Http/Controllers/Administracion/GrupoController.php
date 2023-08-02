<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administracion\Grupo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    //Consulta Vista Grupos
    public function getIndex() {
        Controller::check_permission('getGrupos');
    	return view('administracion.grupos.index',['dataSet'=>'']);
    }
    //Consulta Tablero Grupos
    public function getData() {
        $data = [];
        Controller::check_permission('getGrupos');
        $query = Grupo::where('deleted_at', null)->get();

        foreach ($query as $q) {
            $rel = DB::table('adm_rel_user_grupo')
                ->where('id_grupo', '=', $q->id)
                ->get();
            $id = sizeof($rel) == 0 ? $q->id : null;
            $button1 = '<a class="btn btn-primary" href="/adm-permisos/grupo/' . $q->id . '"><span>Permisos</span></a>';
            $button2 = '<a class="btn btn-secondary" onclick="dao.editarGrupo(' . $q->id . ')" data-toggle="modal"
            data-target="#createGroup" data-backdrop="static" data-keyboard="false"><i class="fa fa-pencil" style="font-size: x-large"></i></a>';
            $button3 = '<button onclick="dao.eliminarRegistro(' .$id. ')" title="Eliminar grupo" class="btn btn-danger"><i class="fa fa-trash" style="font-size: x-large"></i></button>';
            array_push($data, [$q->nombre_grupo, $button1 . ' ' . $button2 . ' ' . $button3]);
        }

    	return response()->json(
            [
                "dataSet" => $data
            ]
        );
    }
    //Inserta Grupo
    public function getCreate(){
        Controller::check_permission('postGrupos');
    	return view('administracion.grupos.create');
    }
    //Inserta Grupo
    public function postStore(Request $request){
        Controller::check_permission('postGrupos');
        if($request->id_user !=null){
           $grupo= Grupo::where('id', $request->id_user)->firstOrFail();
            $grupo->nombre_grupo = $request->nombre;
            $grupo->save();
        }else{
            Grupo::create(['nombre_grupo'=>$request->nombre]);
        }
    	return response()->json("done", 200);
    }
    //Actualiza Grupo
    public function getUpdate($id){
        Controller::check_permission('putGrupos');
    	$grupo = Grupo::find($id);
    	return view('administracion.grupos.update', compact('grupo'));
    }
    public function getGrupo($id){
        Controller::check_permission('putGrupos');
    	$grupo = Grupo::find($id);
    	return $grupo;
    }
    //Actualiza Grupo
    public function postUpdate(Request $request){
        Controller::check_permission('putGrupos');
    	Grupo::find($request->id)->update(["nombre_grupo" => $request->nombre]);
    	return response()->json("done", 200);
    }
    //Elimina Grupo Borrado Lógico
    public function postDelete(Request $request){
        Controller::check_permission('deleteGrupos');
    	Grupo::where('id', $request->id)->delete();
    	return response()->json("done", 200);
    }
}
