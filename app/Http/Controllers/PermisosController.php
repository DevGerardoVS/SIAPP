<?php

namespace App\Http\Controllers;

use App\Helpers\AdminPermisosHelper;
use App\Models\Funciones;
use App\Models\Modulos;
use App\Models\Perfiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermisosController extends Controller
{
    /**
     * Constructor de la clase controlador
     * @version 1.0
     * @author Luis Fernando Zavala
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getModulosPadre(Request $request)
    {
        $query = AdminPermisosHelper::getModulosSistema($request, 'modulos_padre');

        return response()->json([
            "modulos_padre" => $query,
        ]);
    }

    public function getModulosSistema(Request $request)
    {
        $query = AdminPermisosHelper::getModulosSistema($request, 'modulos_sistema');

        return response()->json([
            "modulos_sistema" => $query,
        ]);
    }

    public function getPermisosSistema(Request $request)
    {
        $query = AdminPermisosHelper::getJsonPermisosForm(array());

        return response()->json([
            "items" => $query,
        ]);
    }

    public function getModulos(Request $request)
    {
        $data = Modulos::select(
            'modulos.id',
            'modulos.modulo',
            'modulos.ruta',
            'modulos.icono',
            DB::raw('if(modulos.tipo="mod","Módulo","Submódulo") as tipo'),
            'mp.modulo as modulo_padre'
        )
            ->leftjoin('modulos as mp', 'modulos.modulo_id', '=', 'mp.id')
            ->get();

        $dataSet = array();
        foreach ($data as $d) {
            $module_name = $d->modulo_padre != "" ? $d->modulo_padre . ' - ' . $d->modulo : $d->modulo;
            $button = '<button class="btn btn-sm btn_editar" data-route="' . route('acciones_modulos', ['action' => 'edit', 'id' => $d->id]) . '" title="' . __("messages.editar") . '" data-bs-toggle="modal"><i class="fas fa-pencil" style="color: #267E15;"></i></button>';
            $mod_libre = AdminPermisosHelper::verifyIfModulosHasFunciones($d->id, $d->tipo);
            if ($mod_libre) {
                $button = $button . '/<button class="btn btn-sm btn_delete" data-route="' . route('acciones_modulos', ['action' => 'delete', 'id' => $d->id]) . '" title="' . __("messages.eliminar") . '" data-bs-toggle="modal"><i class="fas fa-trash" style="color: #ad0b00;"></i></button>';
            }
            $ds = array($module_name, $d->ruta, $d->icono, $d->tipo, $button);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "modulos",
        ]);
    }

    public function modulos(Request $request)
    {
        $dataSet = array();

        return view("permisos.modulos", [
            'dataSet' => json_encode($dataSet),
        ]);
    }

    public function agregarModulos(Request $request)
    {
        $customMessages = [
            'required' => "El campo no puede ir vacío",
        ];
        // se validan los campos
        $request->validate([
            'modulo' => 'required',
            'tipo' => 'required',
            'modulo_id' => $request->input('tipo') == 'sub' ? 'required' : '',
        ], $customMessages);

        $verify_if_exist = Modulos::where('ruta', $request->input('ruta'))
            ->where('tipo', $request->input('tipo'))
            ->where('modulo_id', $request->input('modulo_id'))
            ->get();
        if (($verify_if_exist->count() == 0 && !$request->input('id')) || $request->input('id')) {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::saveAdminModulos($request);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo registrar el módulo',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Módulo guardado con éxito',
            );

            return response()->json($returnData);
        } else {
            $returnData = array(
                'status' => 'info',
                'title' => 'Registro existente',
                'message' => 'Este módulo contiene una ruta que ya existe en el sistema.',
            );

            return response()->json($returnData);
        }
    }

    public function actionsModulos(Request $request, $action, $id)
    {
        if ($action == 'delete') {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::deleteModulos($id);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo borrar el módulo',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Módulo eliminado con éxito',
            );

            return response()->json($returnData);
        } else if ($action == 'edit') {
            //queries
            $objEditar = Modulos::where('id', '=', $id)->firstOrFail();
            $modulos_padre = Modulos::select('id', 'modulo')->where('estatus', 1)->where('tipo', 'mod')->where('id', '!=', $id)->get();

            $returnData = array(
                'objEditar' => $objEditar,
                'modulos_padre' => $modulos_padre,
            );
            return response()->json($returnData);
        }
    }

    public function getFunciones(Request $request)
    {
        $data = Funciones::select(
            'funciones.id',
            'funciones.funcion',
            'funciones.ruta',
            'funciones.icono',
            'modulos.modulo',
            'm_padre.modulo as mod_padre'
        )
            ->join('modulos', 'funciones.modulo_id', "=", "modulos.id")
            ->join('modulos as m_padre', 'modulos.modulo_id', 'm_padre.id')
            ->get();

        $dataSet = array();
        foreach ($data as $d) {
            $button = '<button class="btn btn-sm btn_editar" data-route="' . route('acciones_funciones', ['action' => 'edit', 'id' => $d->id]) . '" title="' . __("messages.editar") . '" data-bs-toggle="modal"><i class="fas fa-pencil" style="color: #267E15;"></i></button>';
            $button = $button . '/<button class="btn btn-sm btn_delete" data-route="' . route('acciones_funciones', ['action' => 'delete', 'id' => $d->id]) . '" title="' . __("messages.eliminar") . '" data-bs-toggle="modal"><i class="fas fa-trash" style="color: #ad0b00;"></i></button>';
            $ds = array($d->funcion, $d->ruta, $d->icono, $d->mod_padre . ' - ' . $d->modulo, $button);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "funciones",
        ]);
    }

    public function funciones(Request $request)
    {
        $dataSet = array();

        return view("permisos.funciones", [
            'dataSet' => json_encode($dataSet),
        ]);
    }

    public function agregarFunciones(Request $request)
    {
        $customMessages = [
            'required' => "El campo no puede ir vacío",
        ];
        // se validan los campos
        $request->validate([
            'funcion' => 'required',
            'modulo_id' => 'required',
        ], $customMessages);

        $verify_if_exist = Funciones::where('ruta', $request->input('ruta'))->where('modulo_id', $request->input('modulo_id'))->get();
        if (($verify_if_exist->count() == 0 && !$request->input('id')) || $request->input('id')) {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::saveAdminFunciones($request);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo registrar la función',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Función guardado con éxito',
            );

            return response()->json($returnData);
        } else {
            $returnData = array(
                'status' => 'info',
                'title' => 'Registro existente',
                'message' => 'Esta función ya existe en el sistema.',
            );

            return response()->json($returnData);
        }
    }

    public function actionsFunciones(Request $request, $action, $id)
    {
        if ($action == 'delete') {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::deletePermissions($id);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo borrar la función',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Función eliminada con éxito',
            );

            return response()->json($returnData);
        } else if ($action == 'edit') {
            //queries
            $objEditar = Funciones::where('id', '=', $id)->firstOrFail();
            $modulos_sistema = Modulos::select(
                'modulos.id',
                'modulos.modulo',
                DB::raw('md_p.modulo as modulo_padre')
            )
                ->join(DB::raw('modulos as md_p'), 'modulos.modulo_id', "=", "md_p.id")
                ->where('modulos.estatus', 1)
                ->where('modulos.tipo', 'sub')
                ->get();

            $returnData = array(
                'objEditar' => $objEditar,
                'modulos_sistema' => $modulos_sistema,
            );
            return response()->json($returnData);
        }
    }

    public function getPerfiles(Request $request)
    {
        $data = Perfiles::select(
            'id', 'nombre', 'estatus',
            DB::raw('if(tipo_perfil = "g","General",if(tipo_perfil = "a","Agua",if(tipo_perfil = "p","Predial","Sin elegir"))) as tipo_perfil')
        )->get();

        $dataSet = array();
        foreach ($data as $d) {
            $button = '<button class="btn btn-sm btn_editar" data-route="' . route('acciones_perfiles', ['action' => 'edit', 'id' => $d->id]) . '" title="' . __("messages.editar") . '" data-bs-toggle="modal"><i class="fas fa-pencil" style="color: #267E15;"></i></button>';
            $perfil_libre = AdminPermisosHelper::verifyIfRoleHasAsigned($d->id);
            if ($perfil_libre && $d->estatus == 1) {
                $button = $button . '/<button class="btn btn-sm btn_delete" data-route="' . route('acciones_perfiles', ['action' => 'disabled', 'id' => $d->id]) . '" title="' . __("messages.deshabilitar_perfil") . '" data-bs-toggle="modal"><i class="fas fa-trash" style="color: #ad0b00;"></i></button>';
            } else if ($perfil_libre && $d->estatus == 0) {
                $button = $button . '/<button class="btn btn-sm btn_enabled" data-route="' . route('acciones_perfiles', ['action' => 'enabled', 'id' => $d->id]) . '" title="' . __("messages.habilitar_perfil") . '" data-bs-toggle="modal"><i class="fas fa-undo" style="color: #267E15;"></i></button>';
            }
            $estatus = $d->estatus == 1 ? __("messages.vigente") : __("messages.no_vigente");
            $ds = array($d->nombre, $d->tipo_perfil, $estatus, $button);
            $dataSet[] = $ds;
        }

        return response()->json([
            "dataSet" => $dataSet,
            "catalogo" => "perfiles",
        ]);
    }

    public function perfiles(Request $request)
    {
        $dataSet = array();

        return view("permisos.perfiles", [
            'dataSet' => json_encode($dataSet),
        ]);
    }

    public function agregarPerfiles(Request $request)
    {
        $customMessages = [
            'required' => "El campo no puede ir vacío",
        ];
        // se validan los campos
        $request->validate([
            'nombre' => 'required',
            //'permisos' => 'required',
        ], $customMessages);

        $verify_if_exist = Perfiles::where('nombre', $request->input('nombre'))->where('tipo_perfil', $request->input('tipo_perfil'))->get();
        if (($verify_if_exist->count() == 0 && !$request->input('id')) || $request->input('id')) {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::saveAdminPerfiles($request);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo registrar el perfil',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Perfil guardado con éxito',
            );

            return response()->json($returnData);
        } else {
            $returnData = array(
                'status' => 'info',
                'title' => 'Registro existente',
                'message' => 'Este perfil ya existe en el sistema.',
            );

            return response()->json($returnData);
        }
    }

    public function actionsPerfiles(Request $request, $action, $id)
    {
        if ($action == 'disabled') {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::deletePerfiles($id, $action);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo deshabilitar el perfil',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Perfil deshabilitado con éxito',
            );

            return response()->json($returnData);
        } else if ($action == 'enabled') {
            try {
                DB::beginTransaction();
                $query = AdminPermisosHelper::deletePerfiles($id, $action);
                DB::commit();
            } catch (\Exception$exp) {
                DB::rollBack();
                Log::channel('daily')->debug('Excepcion ' . $exp->getMessage());
                $returnData = array(
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'Hubo un error, no se pudo deshabilitar el perfil',
                );
                return response()->json($returnData);
            }

            $returnData = array(
                'status' => 'success',
                'title' => 'Éxito',
                'message' => 'Perfil deshabilitado con éxito',
            );

            return response()->json($returnData);
        } else if ($action == 'edit') {
            //queries
            $objEditar = Perfiles::where('id', '=', $id)->firstOrFail();
            $permisos_json = $objEditar->permisos ?? "[]";

            $query = AdminPermisosHelper::getJsonPermisosForm(json_decode($permisos_json));

            $returnData = array(
                'objEditar' => $objEditar,
                'query' => $query,
            );
            return response()->json($returnData);
        }
    }
}
