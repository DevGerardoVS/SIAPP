<?php // Code within app\Helpers\BitacoraHelper.php

namespace App\Helpers;

use App\Models\Funciones;
use App\Models\Modulos;
use App\Models\Perfiles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermisosHelper
{

    public static function verifyRole($role)
    {
        $validation = Auth::user()->hasRole($role);

        return $validation;
    }

    public static function verifyPermission($permission)
    {
        $validation = Auth::user()->can($permission);

        return $validation;
    }

    public static function getModulosSistema($request, $tipo)
    {
        $array_where = [];
        $id = $request->input('id');
        $query;
        if ($tipo == 'modulos_padre') {
            if ($id) {
                array_push($array_where, ['id', '!=', $id]);
            }
            array_push($array_where, ['tipo', '=', 'mod']);
            $query = Modulos::select('id', 'modulo')->where('estatus', 1)->where($array_where)->get();
        } else if ($tipo == 'modulos_sistema') {
            array_push($array_where, ['modulos.tipo', '=', 'sub']);
            $query = Modulos::select(
                'modulos.id',
                'modulos.modulo',
                DB::raw('md_p.modulo as modulo_padre')
            )
                ->join(DB::raw('modulos as md_p'), 'modulos.modulo_id', "=", "md_p.id")
                ->where('modulos.estatus', 1)
                ->where($array_where)
                ->get();
        }

        return $query;
    }

    public static function getPermisosSistema($funcion_id)
    {
        $query = Funciones::select(
            'funciones.id',
            DB::raw('md.id as modulo_id'),
            DB::raw('md.modulo as modulo'),
            DB::raw('sub.id as submodulo_id'),
            DB::raw('sub.modulo as submodulo'),
            'funciones.funcion',
            'funciones.acciones'
        )
            ->join(DB::raw('modulos as sub'), 'funciones.modulo_id', "=", "sub.id")
            ->join(DB::raw('modulos as md'), 'sub.modulo_id', "=", "md.id")
            ->where('funciones.id', $funcion_id)
            ->get();

        return $query;
    }

    public static function funcionesAccionesMenuJson($modulo_id, $funciones_id)
    {
        $funciones = array();
        $query_dos = Funciones::select(
            'funciones.id',
            'funciones.funcion',
            'funciones.ruta',
            'funciones.icono',
            'funciones.orden',
            DB::raw('funciones.modulo_id as submodulo_id'),
            'funciones.acciones'
        )
            ->where('funciones.modulo_id', $modulo_id)
            ->whereIn('funciones.id', $funciones_id)
            ->get();

        foreach ($query_dos as $q2) {
            //asignamos los permisos a la funcion
            $funciones[] = array(
                'id' => $q2->id,
                'nombre' => $q2->funcion,
                'ruta' => $q2->ruta,
                'icono' => $q2->icono,
                'orden' => $q2->orden,
            );
        }
        return $funciones;
    }

    public static function getJsonPermisosMenu($submodulos_id, $funciones_id)
    {
        //consultamos los modulos y submodulos para iterar los permisos
        $query_uno = Modulos::select(
            DB::raw('modulos.id as submodulo_id'),
            DB::raw('modulos.modulo as submodulo'),
            DB::raw('modulos.ruta as subruta'),
            DB::raw('modulos.icono as subicono'),
            'modulos.modulo_id',
            'md.modulo',
            DB::raw('md.ruta as modruta'),
            DB::raw('md.icono as modicono'),
        )
            ->join(DB::raw('modulos as md'), 'modulos.modulo_id', "=", "md.id")
            ->where('modulos.tipo', 'sub')
            ->whereIn('modulos.id', $submodulos_id)
            ->get();

        $modulos = array();

        //iteramos los modulos
        foreach ($query_uno as $q1) {
            if (count($modulos) === 0) { //si el arreglo esta vacio
                //obtenemos las funciones con sus respectivos permisos en base al submodulo
                $funciones = AdminPermisosHelper::funcionesAccionesMenuJson($q1->submodulo_id, $funciones_id);
                //agregamos el primer elemento de modulo, submodulo, funciones y permisos
                $modulos[] = array(
                    'modulo' => $q1->modulo_id,
                    'namemodulo' => $q1->modulo,
                    'modruta' => $q1->modruta,
                    'modicono' => $q1->modicono,
                    'submodulos' => array(
                        array(
                            'submodulo' => $q1->submodulo_id,
                            'namesubmodulo' => $q1->submodulo,
                            'subruta' => $q1->subruta,
                            'subicono' => $q1->subicono,
                            'funciones' => $funciones,
                        ),
                    ),
                );
            } else { //si el arreglo ya contiene datos
                //buscamos en el arreglo si el módulo existe en el arreglo
                $element_exist = array_search($q1->modulo_id, array_column($modulos, 'modulo'));

                if ($element_exist === false) { //si no existe el modulo en el arreglo
                    //obtenemos sus funciones y permisos
                    $funciones = AdminPermisosHelper::funcionesAccionesMenuJson($q1->submodulo_id, $funciones_id);
                    //agregamos el elemento con su modulo, submodulo, funciones y permisos
                    $modulos[] = array(
                        'modulo' => $q1->modulo_id,
                        'namemodulo' => $q1->modulo,
                        'modruta' => $q1->modruta,
                        'modicono' => $q1->modicono,
                        'submodulos' => array(
                            array(
                                'submodulo' => $q1->submodulo_id,
                                'namesubmodulo' => $q1->submodulo,
                                'subruta' => $q1->subruta,
                                'subicono' => $q1->subicono,
                                'funciones' => $funciones,
                            ),
                        ),
                    );
                } else if ($element_exist !== false) { //si ya existe el modulo en el arreglo
                    //buscamos sus funciones y permisos
                    $funciones = AdminPermisosHelper::funcionesAccionesMenuJson($q1->submodulo_id, $funciones_id);
                    //agregamos el submodulo al modulo ya existente en el arreglo junt con sus respectivas funciones y permisos
                    $modulos[$element_exist]['submodulos'][] = array(
                        'submodulo' => $q1->submodulo_id,
                        'namesubmodulo' => $q1->submodulo,
                        'subruta' => $q1->subruta,
                        'subicono' => $q1->subicono,
                        'funciones' => $funciones,
                    );
                }
            }
        }

        return $modulos;
    }

    public static function funcionesAccionesJson($modulo_id, $array_values)
    {
        $funciones = array();
        $query_dos = Funciones::select(
            'funciones.id',
            'funciones.funcion',
            DB::raw('funciones.modulo_id as submodulo_id'),
            DB::raw('sub.modulo as submodulo'),
            'funciones.acciones'
        )
            ->join(DB::raw('modulos as sub'), 'funciones.modulo_id', "=", "sub.id")
            ->where('funciones.modulo_id', $modulo_id)
            ->get();

        foreach ($query_dos as $q2) {
            $permisos = array();
            $json_p = json_decode($q2->acciones, true);

            //se iteran los permisos de la funcion para generar el arreglo de datos
            foreach ($json_p['acciones'] as $p) {
                $id_permiso = $q2->submodulo_id . '_' . $q2->id . '_' . $p['accion']; //'submodulo_id'_'funcion_id'_'accion'

                if (in_array($id_permiso, $array_values)) {
                    $permisos[] = array(
                        'value' => $id_permiso,
                        'label' => $p['descripcion'],
                        //'selected'=>true
                    );
                } else {
                    $permisos[] = array(
                        'value' => $id_permiso,
                        'label' => $p['descripcion'],
                    );
                }
            }
            //asignamos los permisos a la funcion
            $funciones[] = array(
                //'value'=>$q2->id,
                'label' => $q2->submodulo . ' - ' . $q2->funcion,
                'children' => $permisos,
            );
        }

        return $funciones;
    }

    public static function getJsonPermisosForm($array_values)
    {
        //consultamos los modulos y submodulos para iterar los permisos
        $query_uno = Modulos::select(
            'modulos.id',
            DB::raw('modulos.modulo as submodulo'),
            'modulos.modulo_id',
            'md.modulo'
        )
            ->join(DB::raw('modulos as md'), 'modulos.modulo_id', "=", "md.id")
            ->where('modulos.tipo', 'sub')
            ->get();

        $modulos = array();

        //iteramos los modulos
        foreach ($query_uno as $q1) {
            $list_func = AdminPermisosHelper::funcionesAccionesJson($q1->id, $array_values);
            if (count($list_func) > 0) {
                foreach ($list_func as $l_f) {
                    $modulos[] = $l_f;
                }
            }
        }

        return $modulos;
    }

    public static function verifyIfModulosHasFunciones($modulo_id, $tipo)
    {
        $mod_libre = false;
        $funciones = Funciones::where('modulo_id', '=', $modulo_id)->get();

        if ($funciones->count() == 0) {
            if ($tipo == 'Módulo') {
                $modulos = Modulos::where('modulo_id', '=', $modulo_id)->get();

                if ($modulos->count() == 0) {
                    $mod_libre = true;
                }
            } else {
                $mod_libre = true;
            }
        }

        return $mod_libre;
    }

    public static function verifyIfRoleHasAsigned($id)
    {
        $users = User::where('perfil_id', '=', $id)->get();
        $role_libre = $users->count() == 0 ? true : false;
        return $role_libre;
    }

    public static function deleteModulos($id)
    {
        try {
            $modulo_obj = Modulos::where('id', '=', $id)->firstOrFail();
            $module_name = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($modulo_obj->modulo)));
            $data_old = array(
                'id' => $modulo_obj->id,
                'modulo' => $modulo_obj->modulo,
                'ruta' => $modulo_obj->ruta,
                'icono' => $modulo_obj->icono,
                'tipo' => $modulo_obj->icono,
                'modulo_id' => $modulo_obj->modulo_id,
                'estatus' => $modulo_obj->estatus,
                'usuario_creacion' => $modulo_obj->usuario_creacion,
                'usuario_modificacion' => $modulo_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($modulo_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($modulo_obj->updated_at)),
            );

            //verificamos que no haya ningún permiso a nombre del rol en la tabla de permissions
            $permission_func_exist = Permission::where('name', $module_name)->get();
            if ($permission_func_exist->count() > 0) {
                //si lo hay, lo eliminamos
                AdminPermisosHelper::removePermissionsFromRole($module_name);
                $permission_func_exist[0]->delete();
                //eliminamos cache de permisos
                app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }
            //ahora solo borramos el modulo
            $modulo_obj->delete();

            $data_new = array();
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'modulos',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Modulos", "Baja", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function saveAdminModulos($request)
    {
        try {
            $id = $request->input('id');
            $array_data = [];
            $data_old = [];

            $modulos_obj = $id ? Modulos::where('id', $id)->firstOrFail() : new Modulos();

            if ($id) {
                $data_old = array(
                    'modulo' => $modulos_obj->modulo,
                    'ruta' => $modulos_obj->ruta,
                    'icono' => $modulos_obj->icono,
                    'tipo' => $modulos_obj->tipo,
                    'modulo_id' => $modulos_obj->modulo_id,
                    'estatus' => $modulos_obj->estatus,
                    'usuario_creacion' => $modulos_obj->usuario_creacion,
                    'usuario_modificacion' => $modulos_obj->usuario_modificacion,
                    'created_at' => date("d/m/Y H:i:s", strtotime($modulos_obj->created_at)),
                    'updated_at' => date("d/m/Y H:i:s", strtotime($modulos_obj->updated_at)),
                );
                $modulos_obj->usuario_modificacion = Auth::user()->username;
            } else {
                $modulos_obj->estatus = 1;
                $modulos_obj->usuario_creacion = Auth::user()->username;
            }

            $modulos_obj->modulo = $request->input('modulo');
            $modulos_obj->ruta = $request->input('ruta');
            $modulos_obj->icono = $request->input('icono');
            $modulos_obj->tipo = $request->input('tipo');
            $modulos_obj->modulo_id = $request->input('modulo_id');
            $modulos_obj->save();

            $data_new = array(
                'modulo' => $request->input('modulo'),
                'ruta' => $request->input('ruta'),
                'icono' => $request->input('icono'),
                'tipo' => $request->input('tipo'),
                'modulo_id' => $request->input('modulo_id'),
                'estatus' => $modulos_obj->estatus,
                'usuario_creacion' => $modulos_obj->usuario_creacion,
                'usuario_modificacion' => $modulos_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($modulos_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($modulos_obj->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'modulos',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Modulos", $id ? "Edicion" : "Registro", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function createPermissions($permisos_array, $permisos_delete, $id, $modulo_id, $funcion)
    {
        try {
            $permisos = json_decode($permisos_array);
            $delete = json_decode($permisos_delete);

            $modulo = Modulos::where('id', '=', $modulo_id)->firstOrFail()->modulo;
            $module_name = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($modulo)));
            $funcion_name = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($funcion)));

            if ($id) {
                if ($delete != null) {
                    foreach ($delete->acciones as $permiso_delete) {
                        $permission_exist = Permission::where('name', $module_name . '.' . $funcion_name . '.' . $permiso_delete->accion)->get();
                        if ($permission_exist->count() > 0) {
                            $permission_exist[0]->delete();
                        }
                        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }
                }
                foreach ($permisos->acciones as $permiso) {
                    $permission_exist = Permission::where('name', $module_name . '.' . $funcion_name . '.' . $permiso->accion)->get();
                    if ($permission_exist->count() == 0) {
                        Permission::create(['name' => $module_name . '.' . $funcion_name . '.' . $permiso->accion]);
                    }
                    //app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                }
            } else {
                if ($permission_exist = Permission::where('name', $module_name)->get()->count() == 0) {
                    Permission::create(['name' => $module_name]);
                }
                if ($permission_exist = Permission::where('name', $module_name . '.' . $funcion_name)->get()->count() == 0) {
                    Permission::create(['name' => $module_name . '.' . $funcion_name]);
                }

                foreach ($permisos->acciones as $permisos) {
                    $permission_exist = Permission::where('name', $module_name . '.' . $funcion_name . '.' . $permisos->accion)->get();
                    if ($permission_exist->count() == 0) {
                        Permission::create(['name' => $module_name . '.' . $funcion_name . '.' . $permisos->accion]);
                    }
                }
            }
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function removePermissionsFromRole($permission_name)
    {
        try {
            $roles = Role::all();
            foreach ($roles as $role) {
                $role->revokePermissionTo($permission_name);
            }
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function deletePermissions($id)
    {
        try {
            $funcion_obj = Funciones::where('id', '=', $id)->firstOrFail();
            $modulo_obj = Modulos::where('id', '=', $funcion_obj->modulo_id)->firstOrFail();
            $module_name = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($modulo_obj->modulo)));
            $funcion_name = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($funcion_obj->funcion)));
            $delete = json_decode($funcion_obj->acciones);
            $data_old = array(
                'id' => $funcion_obj->id,
                'funcion' => $funcion_obj->funcion,
                'ruta' => $funcion_obj->ruta,
                'icono' => $funcion_obj->icono,
                'acciones' => $delete,
                'modulo_id' => $funcion_obj->modulo_id,
                'estatus' => $funcion_obj->estatus,
                'usuario_creacion' => $funcion_obj->usuario_creacion,
                'usuario_modificacion' => $funcion_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($funcion_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($funcion_obj->updated_at)),
            );

            if ($delete != null) {
                foreach ($delete->acciones as $permiso_delete) {
                    $permission_exist = Permission::where('name', $module_name . '.' . $funcion_name . '.' . $permiso_delete->accion)->get();
                    if ($permission_exist->count() > 0) {
                        AdminPermisosHelper::removePermissionsFromRole($module_name . '.' . $funcion_name . '.' . $permiso_delete->accion);
                        $permission_exist[0]->delete();
                    }
                    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                }

                $permission_func_exist = Permission::where('name', $module_name . '.' . $funcion_name)->get();
                if ($permission_func_exist->count() > 0) {
                    AdminPermisosHelper::removePermissionsFromRole($module_name . '.' . $funcion_name);
                    $permission_func_exist[0]->delete();
                }
                app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }
            $funcion_obj->delete();

            $data_new = array();
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'funciones',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Funciones", "Baja", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function saveAdminFunciones($request)
    {
        try {
            $id = $request->input('id');
            $array_data = [];
            $data_old = [];

            $funciones_obj = $id ? Funciones::where('id', $id)->firstOrFail() : new Funciones();
            AdminPermisosHelper::createPermissions(
                $request->input('acciones'),
                $request->input('acciones_delete'),
                $id,
                $request->input('modulo_id'),
                $request->input('funcion')
            );

            if ($id) {
                $data_old = array(
                    'funcion' => $funciones_obj->funcion,
                    'ruta' => $funciones_obj->ruta,
                    'icono' => $funciones_obj->icono,
                    'acciones' => $funciones_obj->acciones,
                    'modulo_id' => $funciones_obj->modulo_id,
                    'orden' => $funciones_obj->orden,
                    'estatus' => $funciones_obj->estatus,
                    'usuario_creacion' => $funciones_obj->usuario_creacion,
                    'usuario_modificacion' => $funciones_obj->usuario_modificacion,
                    'created_at' => date("d/m/Y H:i:s", strtotime($funciones_obj->created_at)),
                    'updated_at' => date("d/m/Y H:i:s", strtotime($funciones_obj->updated_at)),
                );
                $funciones_obj->usuario_modificacion = Auth::user()->username;
            } else {
                $funciones_obj->estatus = 1;
                $funciones_obj->usuario_creacion = Auth::user()->username;

            }

            $funciones_obj->funcion = $request->input('funcion');
            $funciones_obj->ruta = $request->input('ruta');
            $funciones_obj->icono = $request->input('icono');
            $funciones_obj->acciones = $request->input('acciones');
            $funciones_obj->modulo_id = $request->input('modulo_id');
            $funciones_obj->orden = $request->input('orden');
            $funciones_obj->save();

            $data_new = array(
                'funcion' => $request->input('funcion'),
                'ruta' => $request->input('ruta'),
                'icono' => $request->input('icono'),
                'acciones' => $request->input('acciones'),
                'modulo_id' => $request->input('modulo_id'),
                'orden' => $request->input('orden'),
                'estatus' => $funciones_obj->estatus,
                'usuario_creacion' => $funciones_obj->usuario_creacion,
                'usuario_modificacion' => $funciones_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($funciones_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($funciones_obj->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'funciones',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Funciones", $id ? "Edicion" : "Registro", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function iteratePermisosList($array_permisos)
    {
        $array_ids_subm = array();
        $array_ids_func = array();

        foreach ($array_permisos as $p) {
            $submodulo_id = explode('_', $p)[0];
            if (!in_array($submodulo_id, $array_ids_subm)) {
                $array_ids_subm[] = explode('_', $p)[0];
            }
            $funcion_id = explode('_', $p)[1];
            if (!in_array($funcion_id, $array_ids_func)) {
                $array_ids_func[] = explode('_', $p)[1];
            }
        }

        return array('submodulos' => $array_ids_subm, 'funciones' => $array_ids_func);
    }

    public static function iterateAccionesList($array_acciones)
    {
        $funcion_ids = array();
        $permisos_ids = array();

        foreach ($array_acciones as $p) {
            $submodulo_id = explode('_', $p)[1];
            if (!in_array($submodulo_id, $funcion_ids)) {
                $funcion_ids[] = explode('_', $p)[1];
            }
        }

        $q_funciones = Funciones::select(
            'funciones.id',
            DB::raw('funciones.modulo_id as submodulo_id'),
            DB::raw('sub.modulo as submodulo'),
            'funciones.funcion',
            'funciones.acciones'
        )
            ->join(DB::raw('modulos as sub'), 'funciones.modulo_id', "=", "sub.id")
            ->whereIn('funciones.id', $funcion_ids)
            ->get();

        foreach ($q_funciones as $q_f) {
            $n_sub = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($q_f->submodulo)));
            $n_func = str_replace(' ', '_', strtolower(AdminPermisosHelper::eliminar_acentos($q_f->funcion)));
            $json_p = json_decode($q_f->acciones, true);
            $array_action = array();
            foreach ($json_p['acciones'] as $p) {
                if (in_array($q_f->submodulo_id . '_' . $q_f->id . '_' . $p['accion'], $array_acciones)) {
                    $permisos_ids[] = $n_sub . '.' . $n_func . '.' . $p['accion'];
                }
            }
        }

        return $permisos_ids;
    }

    public static function createRolesWithPermissions($array_permisos, $new_name_rol, $id, $old_name_rol)
    {
        try {
            $array_permisos_ids = AdminPermisosHelper::iterateAccionesList($array_permisos);

            if ($id) {
                if ($old_name_rol != $new_name_rol) {
                    $role = Role::firstOrCreate(['name' => $old_name_rol]);
                    $role->name = $new_name_rol;
                    $role->save();
                } else {
                    $role = Role::firstOrCreate(['name' => $new_name_rol]);
                }

                $role->syncPermissions($array_permisos_ids);
            } else {
                $role = Role::create(['name' => $new_name_rol]);
                $role->syncPermissions($array_permisos_ids);
            }
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function saveAdminPerfiles($request)
    {
        try {
            $id = $request->input('id');
            $array_data = [];
            $data_old = [];

            $perfiles_obj = $id ? Perfiles::where('id', $id)->firstOrFail() : new Perfiles();

            if ($id) {
                $data_old = array(
                    'nombre' => $perfiles_obj->nombre,
                    'permisos' => $perfiles_obj->permisos,
                    'menu' => $perfiles_obj->menu,
                    'tipo_perfil' => $perfiles_obj->tipo_perfil,
                    'estatus' => $perfiles_obj->estatus,
                    'usuario_creacion' => $perfiles_obj->usuario_creacion,
                    'usuario_modificacion' => $perfiles_obj->usuario_modificacion,
                    'created_at' => date("d/m/Y H:i:s", strtotime($perfiles_obj->created_at)),
                    'updated_at' => date("d/m/Y H:i:s", strtotime($perfiles_obj->updated_at)),
                );
                $perfiles_obj->usuario_modificacion = Auth::user()->username;
            } else {
                $perfiles_obj->estatus = 1;
                $perfiles_obj->usuario_creacion = Auth::user()->username;
            }

            $array_submodulos_id = AdminPermisosHelper::iteratePermisosList($request->input('permisos'));
            $json_menu = AdminPermisosHelper::getJsonPermisosMenu($array_submodulos_id['submodulos'], $array_submodulos_id['funciones']);
            //creamos/actualizamos el rol con los permisos seleccionados
            AdminPermisosHelper::createRolesWithPermissions($request->input('permisos'), $request->input('nombre'), $id, $perfiles_obj->nombre);

            $perfiles_obj->nombre = $request->input('nombre');
            $perfiles_obj->permisos = json_encode($request->input('permisos'));
            $perfiles_obj->menu = json_encode($json_menu);
            $perfiles_obj->tipo_perfil = $request->input('tipo_perfil');
            $perfiles_obj->save();
            //al actualizar el perfil, verificamos si hay usuarios con el perfil asignado para actualizar sus roles
            if ($id) {
                AdminPermisosHelper::verifyIfUserHasRolesAsigned($id);
            }

            $data_new = array(
                'nombre' => $request->input('nombre'),
                'permisos' => json_encode($request->input('permisos')),
                'menu' => json_encode($json_menu),
                'tipo_perfil' => $request->input('tipo_perfil'),
                'estatus' => $perfiles_obj->estatus,
                'usuario_creacion' => $perfiles_obj->usuario_creacion,
                'usuario_modificacion' => $perfiles_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($perfiles_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($perfiles_obj->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'funciones',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Perfiles", $id ? "Edicion" : "Registro", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function asignRoleToUser($perfil_id, $username)
    {
        try {
            $obj_perfil = Perfiles::where('id', '=', $perfil_id)->firstOrFail();
            $obj_user = User::where('username', $username)->firstOrFail();

            if (!$obj_user->hasRole($obj_perfil->nombre)) {
                $obj_user->syncRoles([$obj_perfil->nombre]);
            }
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function verifyIfUserHasRolesAsigned($perfil_id)
    {
        try {
            $users_with_role = User::where('perfil_id', $perfil_id)->get();
            foreach ($users_with_role as $user_obj) {
                AdminPermisosHelper::asignRoleToUser($user_obj->perfil_id, $user_obj->username);
            }
        } catch (\Exception$exp) {
            //Log::channel('daily')->debug('exp '.$exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function deletePerfiles($id, $action)
    {
        try {
            $perfil_obj = Perfiles::where('id', '=', $id)->firstOrFail();
            $data_old = array(
                'nombre' => $perfil_obj->nombre,
                'permisos' => $perfil_obj->permisos,
                'menu' => $perfil_obj->menu,
                'tipo_perfil' => $perfil_obj->tipo_perfil,
                'estatus' => $perfil_obj->estatus,
                'usuario_creacion' => $perfil_obj->usuario_creacion,
                'usuario_modificacion' => $perfil_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($perfil_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($perfil_obj->updated_at)),
            );

            //ahora solo deshabilitamos el perfil
            $perfil_obj->estatus = $action == 'enabled' ? 1 : 0;
            $perfil_obj->save();

            $data_new = array(
                'nombre' => $perfil_obj->nombre,
                'permisos' => $perfil_obj->permisos,
                'menu' => $perfil_obj->menu,
                'tipo_perfil' => $perfil_obj->tipo_perfil,
                'estatus' => $perfil_obj->estatus,
                'usuario_creacion' => $perfil_obj->usuario_creacion,
                'usuario_modificacion' => $perfil_obj->usuario_modificacion,
                'created_at' => date("d/m/Y H:i:s", strtotime($perfil_obj->created_at)),
                'updated_at' => date("d/m/Y H:i:s", strtotime($perfil_obj->updated_at)),
            );
            // generamos arreglo con el dato anterior y el nuevo
            $array_data = array(
                'tabla' => 'perfiles',
                'anterior' => $data_old,
                'nuevo' => $data_new,
            );
            BitacoraHelper::saveBitacora(BitacoraHelper::getIp(), "Perfiles", $action == 'enabled' ? "Habilitar" : "Baja", json_encode($array_data));
        } catch (\Exception$exp) {
            Log::channel('daily')->debug('exp ' . $exp->getMessage());
            throw new \Exception($exp->getMessage());
        }
    }

    public static function eliminar_acentos($cadena)
    {

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena);

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena);

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena);

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena);

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }
}
