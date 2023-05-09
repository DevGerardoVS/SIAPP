<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AdminPermisosHelper;
use App\Helpers\BitacoraHelper;
use App\Helpers\QueryHelper;
use Carbon\Carbon;
use App\Exports\UsersExport;
use App\Models\User;

class UsersController extends Controller{

    /**
     * Constructor de la clase controlador
     * @version 1.0
     * @author Luis Fernando Zavala
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUsuarios(Request $request){        
        $data = DB::table('users')
            ->select('users.id as id','users.nombre','apellidoP','apellidoM', 'username', 'email','telefono','users.usuario_creacion','perfiles.nombre as perfil','users.delegacion',DB::raw('IF(users.estatus=1,"Vigente","No vigente") as estatus'))
            ->join('perfiles','users.perfil_id',"=","perfiles.id")
            ->orderBy('users.nombre', 'asc')
            ->get();

        $dataSet = array();
        foreach ($data as $d) {
            $button = '';
            if(verifyPermission('usuarios.usuarios.editar')){
                $button = '<button class="btn btn-sm" title="Editar Usuario" data-bs-toggle="modal" onclick="editarRegistro('.strval($d->id).')" data-bs-target="#modal'.$d->id.'"><i class="fas fa-pencil" style="color: #267E15;"></i></button>';
            }
            if(verifyPermission('usuarios.usuarios.deshabilitar')){
                $button = $button.'/<button data-toggle="tooltip" data-placement="top" title="Deshabilitar Usuario" class="btn btn-sm" data-bs-toggle="modal" onclick="eliminarRegistro('.$d->id.')" data-bs-target="#modaldel"><i class="fas fa-trash" style="color: #ad0b00;" ></i></button>';
            }
            $ds = array($d->id, $d->nombre.' '.$d->apellidoP.' '.$d->apellidoM, $d->username, $d->email, $d->telefono, $d->usuario_creacion, $d->perfil, $d->delegacion, $d->estatus, $button);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "usuarios",
        ]);
    }

    /**
     * Función para mostrar la vista con todos los usuarios
     * @return json Usuarios
     */
    public function index(){
        
        $perfiles = DB::table('perfiles')
            ->select('id','nombre','tipo_perfil')
            ->where('estatus',1)
            ->whereNotIn('id',[2,4])
            ->orderBy('nombre')
            ->get();

        $dataSet = array();
        
        return view('users.index', ['dataSet' => $dataSet,'perfiles'=>$perfiles]);
    }

    /**
     * Función para mostrar los datos de un usuario
     * @return json Usuario
     */
    public function show(Request $request){
        $user = DB::table('users')
            ->select('users.id as id','users.nombre','apellidoP','apellidoM', 'username', 'email','telefono','users.usuario_creacion','delegacion','perfil_id',DB::raw('IF(users.estatus=1,"Vigente","No vigente") as estatus'))
            ->where('users.id','=',$request->clave)
            ->first();
        
        return response()->json(array(
            'user'=> $user,
        ));
    }

    /**
     * Función para almacenar un nuevo usuario
     * @return json Usuarios
     */
    public function store(Request $request){
        $validations = [
            'nombre'=>'required|min:3|max:50',
            'paterno'=>'max:50',
            'materno'=>'max:50',
            'username'=>'required|max:50|unique:users',
            'password'=>['required','string','regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/','min:8','confirmed'],
            'email'=>'required|email|regex:/^[a-z0-9!#$%&*+_-]+(?:\\.[a-z0-9!#$%&*+_-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/|unique:users,email',
            'telefono'=>'digits:10',
            'delegacion'=>in_array($request->perfil,[3,5]) ? 'required|min:3|max:50' : 'max:50',
            'perfil'=>'required',
            'estatus'=>'required'
        ];

        $request->validate($validations);
        
        try{
            DB::beginTransaction();
            $user = Auth::user()->username;      
            $request['password'] = Hash::make($request['password']);
            DB::table('users')->insert([
                'nombre'=>$request->nombre,
                'apellidoP'=>$request->paterno,
                'apellidoM'=>$request->materno,
                'username'=>$request->username,
                'password'=>$request->password,
                'email'=>$request->email,
                'telefono'=>$request->telefono,
                'delegacion'=>$request->delegacion,
                'perfil_id'=>$request->perfil,
                'estatus'=>$request->estatus,
                'created_at'=>Carbon::now()->toDateTimeString(),
                'usuario_creacion'=>$user
            ]);

            AdminPermisosHelper::asignRoleToUser($request->perfil,$request->username);
            
            $json=[];
            $json1 = ['tabla'=>"users"];
            $json += ['nombre'=>$request->nombre];
            $json += ['paterno'=>$request->paterno];
            $json += ['materno'=>$request->materno];
            $json += ['username'=>$request->username];
            $json += ['password'=>$request->password];
            $json += ['email'=>$request->email];
            $json += ['telefono'=>$request->telefono];
            $json += ['delegacion'=>$request->delegacion];
            $json += ['perfil_id'=>$request->perfil];
            $json += ['estatus'=>$request->estatus];
            $json += ['usuario_creacion'=>$user];
            $json = ["nuevo"=>$json];
            $json1 += $json;
            $json = json_encode($json1);
            $ip = BitacoraHelper::getIp();
            $modulo = "Users";
            $accion = "Alta";
            BitacoraHelper::saveBitacora($ip,$modulo,$accion,$json);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack(); 
            \Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Error al crear el usuario'
            );
            return response()->json($returnData);
        }
        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Usuario creado correctamente.'
        );
        return response()->json($returnData);
    }

    /**
     * Funcion que actualiza los datos de una oficina
     * @param json|integer $request,$id - Carga con los datos de la oficina y clave de la oficina a modificar
     * @return string Oficina actualizada
     */
    public function update(Request $request){
        try{
            $perfil = DB::table('perfiles')->select('id','nombre','tipo_perfil')->where('id',$request->perfil)->first();
            $validations = [
                'clave'=>'required',
                'nombre'=>'required|min:3|max:50',
                'paterno'=>'max:50',
                'materno'=>'max:50',
                'username'=>'required|max:50',
                'email'=>"required|email|regex:/^[a-z0-9!#$%&*+_-]+(?:\\.[a-z0-9!#$%&*+_-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/",
                'telefono'=>'digits:10',
                'delegacion'=>in_array($request->perfil,[3,5]) ? 'required|min:3|max:50' : 'max:50',
                'perfil'=>'required',
                'estatus'=>'required'
            ];

            $request->validate($validations);

            DB::beginTransaction();
            $usuario = DB::table('users')->where('id', $request->clave)->first();

            $fecha = Carbon::now()->toDateTimeString();
            $user = Auth::user()->username;
            $resp=DB::table('users')
            ->where('id', $request->clave)
            ->update([
                'nombre'=>$request->nombre,
                'apellidoP'=>$request->paterno,
                'apellidoM'=>$request->materno,
                'username'=>$request->username,
                'email'=>$request->email,
                'telefono'=>$request->telefono,
                'delegacion'=>$request->delegacion,
                'perfil_id'=>$request->perfil,
                'estatus'=>$request->estatus,
                'updated_at'=>$fecha,
                'usuario_modificacion'=>$user
            ]);

            AdminPermisosHelper::asignRoleToUser($request->perfil,$request->username);
            
            $json=[];
            $json1=[];
            $json2 = ['tabla'=>"Users"];
            $json+=['id'=>$request->clave];
            $json1+=['id'=>$request->clave];
            $json += ['nombre'=>$request->nombre];
            $json1 += ['nombre'=>$usuario->nombre];
            $json += ['apellido paterno'=>$request->paterno];
            $json1 += ['apellido paterno'=>$usuario->apellidoP];
            $json += ['apellido materno'=>$request->materno];
            $json1 += ['apellido materno'=>$usuario->apellidoM];
            $json += ['email'=>$request->email];
            $json1 += ['email'=>$usuario->email];
            $json += ['telefono'=>$request->telefono];
            $json1 += ['telefono'=>$usuario->telefono];
            $json += ['delegacion'=>$request->delegacion];
            $json1 += ['delegacion'=>$usuario->delegacion];

            $json += ['perfil'=>$request->perfil];
            $json1 += ['perfil'=>$usuario->perfil_id];

            $json += ['estatus'=>$request->estatus];
            $json1 += ['estatus'=>$usuario->estatus];
            
            $json += ['usuario_modificacion'=>$user];
            $json = ["nuevo"=>$json];
            $json1 = ["anterior"=>$json1];
            $json2 += $json1;
            $json2 += $json;
            $json = json_encode($json2);
            $ip = BitacoraHelper::getIp();
            $modulo = "Users";
            $accion = "Modificacion";
            BitacoraHelper::saveBitacora($ip,$modulo,$accion,$json);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack(); 
            \Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Error al actualizar el usuario'
            );
            return response()->json($returnData);
        }
        
        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Usuario actualizado correctamente.'
        );
        return response()->json($returnData);
        
    }
    /**
     * Funcion para dar de baja (lógica) a un usuario
     * @param string $id - Clave del usuario
     * @return string Usuario eliminado
     */
    public function destroy(Request $request){
        try{
            DB::beginTransaction();
            $fecha = Carbon::now()->toDateTimeString();
            $user = Auth::user()->username;
            $json=[];
            $json1=[];

            $usuario = DB::table('users')
            ->select('users.id as id','users.nombre','apellidoP','apellidoM', 'username', 'email','telefono','users.usuario_creacion','perfil_id',DB::raw('IF(users.estatus=1,"Vigente","No vigente") as estatus') ,'updated_at')
            ->where('users.id','=',$request->id)
            ->first();

            DB::table('users')
                ->where('id', $request->id)
                ->update([
                    'users.estatus' => 0,
                    'users.updated_at' => $fecha,
                    'users.usuario_modificacion' => $user,
                ]);
            $json2 = ['tabla'=>"Users"];
            $json+=['id'=>$request->id];
            $json1+=['id'=>$request->id];
            $json+=['fecha_actualizacion'=>$fecha];
            $json1+=['fecha_actualizacion'=>$usuario->updated_at];
            $json += ['estatus'=>0];
            $json1 += ['estatus'=>$usuario->estatus];
            $json += ['usuario_modificacion'=>$user];
            $json = ["nuevo"=>$json];
            $json1 = ["anterior"=>$json1];
            $json2 += $json1;
            $json2 += $json;
            $json = json_encode($json2);
            
            $ip = BitacoraHelper::getIp();
            $modulo = "Usuarios";
            $accion = "Baja";
            BitacoraHelper::saveBitacora($ip,$modulo,$accion,$json);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack(); 
            \Log::channel('daily')->debug('Excepcion '.$exp->getMessage());
            
            $returnData = array(
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Error al deshabilitar el usuario'
            );
            return response()->json($returnData);
        }
        
        $returnData = array(
            'status' => 'success',
            'title' => 'Éxito',
            'message' => 'Usuario deshabilitado'
        );
        return response()->json($returnData);
    }
    
    /**
     * Funcion para exportar el catalogo de oficinas a excel
    * @return \Illuminate\Support\Collection
    */
    public function export() 
    {
        return Excel::download(new UsersExport, 'Usuarios.xlsx');
    }


}
