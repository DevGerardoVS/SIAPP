<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\catalogos\CatEntes;
use App\Models\administracion\Menu;
use App\Models\administracion\Sistema;
use App\Models\administracion\Funciones;
use App\Models\administracion\Grupo;
use DB;

class DatabaseSeeder extends Seeder
{
        protected $cat_entes = array(
            ['id' => 1, 'cve_upp' => '007', 'nombre_upp' => 'Secretaría de Administración y Finannzas', 'cve_ur' => '01', 'nombre_ur' => 'Dirección de Gobienrno Digital', 'cve_uo' => '001', 'nombre_uo' => 'Departamento de Proyectos Internos'],
            ['id' => 2, 'cve_upp' => '001', 'nombre_upp' => 'Secretaría de prueba', 'cve_ur' => '02', 'nombre_ur' => 'Dirección de pruebas', 'cve_uo' => '002', 'nombre_uo' => 'Departamento de Pruebas Internos']

        );

        protected $cat_users = array(
            ['id' => 1, 'id_grupo' => 1, 'nombre' => 'sudo', 'p_apellido' => 'admin', 's_apellido' => 'sedj', 'celular' => '00-00-00-00-00', 'email' => 'prueba1@gmail.com', 'username' => 'administrador', 'password' => 'valida2022', 'sudo' => 1,'clv_upp'=>NULL],
            ['id' => 2, 'id_grupo' => 1, 'nombre' => 'Francisco', 'p_apellido' => 'Méndez', 's_apellido' => 'Chávez', 'celular' => '44-32-21-90-95', 'email' => 'pacomendez2308@gmail.com', 'username' => 'depExpedientes', 'password' => 'depExpedientes.22', 'sudo' => 0,'clv_upp'=>'007'],
            ['id' => 3, 'id_grupo' => 2, 'nombre' => 'Jack', 'p_apellido' => 'Prota', 's_apellido' => 'Ponce', 'celular' => '44-32-21-90-95', 'email' => 'pruebas@gmail.com', 'username' => 'Jack', 'password' => 'valida23', 'sudo' => 0,'clv_upp'=>'007'],
            ['id' => 4, 'id_grupo' => 1, 'nombre' => 'UnidadR', 'p_apellido' => 'u', 's_apellido' => 'pp', 'celular' => '44-32-21-90-95', 'email' => 'upp_user@gmail.com', 'username' => 'upp', 'password' => 'valida23', 'sudo' => 0,'clv_upp'=>'002']
        );

        protected $sistemas = array(
            ['id' => 1, 'nombre_sistema' => 'Sistema de Calendarizacion','ruta' => 'sistemas', 'logo' => 'logo_expedientes.png', 'logo_min' => 'logo_expedientes_min.png', 'descripcion' => 'Sistema para la adminsitración de expedientes Jurídicos', 'estatus' => 1],
        );

        protected $menus = array(
            ['id' => 2,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Logs', 'ruta' => '/logs', 'icono' => 'fa-calendar', 'nivel' => 0, 'posicion' => 3, 'descripcion' => 'Módulo de calendario'],
            ['id' => 3,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Usuarios', 'ruta' => '/adm-usuarios', 'icono' => 'fa-user', 'nivel' => 0, 'posicion' => 3, 'descripcion' => 'Módulo para administrar los usuarios del sistema'],
            ['id' => 4,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Administración', 'ruta' => '#', 'icono' => 'fa-gears', 'nivel' => 0, 'posicion' => 7, 'descripcion' => 'Conjunto de módulos de adminsitración del sistema'],
            ['id' => 5,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Grupos', 'ruta' => '/adm-grupos', 'icono' => 'fa-users', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Módulo para administrar los grupos del sistema'],
            ['id' => 6,  'id_sistema' => 1,'padre' => 4, 'nombre_menu' => 'Bitácora', 'ruta' => '/adm-bitacora', 'icono' => 'fa-bookmark', 'nivel' => 1, 'posicion' => 2, 'descripcion' => 'Bitácora de movimientos del sistema'],
            ['id' => 7,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Calendarizacion', 'ruta' => '/calendarizacion', 'icono' => ' fa-calendar', 'nivel' => 0, 'posicion' => 4, 'descripcion' => 'Calendarizacion de presupuestos'],
            ['id' => 8,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Claves presupuestarias', 'ruta' => '/calendarizacion/claves', 'icono' => ' fa-calendar', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Registro de claves presupuestaria'],
            ['id' => 9,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Metas', 'ruta' => '/calendarizacion/metas', 'icono' => 'fa-flag-checkered', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Techos financieros'],
            ['id' => 10,  'id_sistema' => 1,'padre' => 7, 'nombre_menu' => 'Techos financieros', 'ruta' => '/calendarizacion/techos', 'icono' => 'fa-flag-checkered', 'nivel' => 2, 'posicion' => 2, 'descripcion' => 'Techos financieros'],
            ['id' => 11,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Reportes', 'ruta' => '/Reportes', 'icono' => 'fa-flag-checkered', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Reportes'],
            ['id' => 12,  'id_sistema' => 1,'padre' => 11, 'nombre_menu' => 'ley Planeacion Hacienda', 'ruta' => '/Reportes/ley-planeacion', 'icono' => 'fa-flag-checkered', 'nivel' => 1, 'posicion' => 1, 'descripcion' => 'Reportes'],
            ['id' => 13,  'id_sistema' => 1,'padre' => 11, 'nombre_menu' => 'Administrativos', 'ruta' => '/Reportes/administrativos', 'icono' => 'fa-flag-checkered', 'nivel' => 2, 'posicion' => 2, 'descripcion' => 'Reportes'],
            ['id' => 14,  'id_sistema' => 1,'padre' => 0, 'nombre_menu' => 'Administracio de captura', 'ruta' => '/admon-capturas', 'icono' => ' fa-crosshairs', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Administracion de capturas'],
            ['id' => 15,  'id_sistema' => 1,'padre' => 13, 'nombre_menu' => 'algo asi', 'ruta' => '/administrativos/algo', 'icono' => 'fa-flag-checkered', 'nivel' => 0, 'posicion' => 0, 'descripcion' => 'Reportes']

        );

        protected $funciones = array(
            ['id' => 1,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'getUsuarios', 'tipo' => 'Consulta', 'descripcion' => 'Obtener todos los usuarios de la BD'],
            ['id' => 2,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'postUsuarios', 'tipo' => 'Insercion', 'descripcion' => 'Insertar un usuario a la BD'],
            ['id' => 3,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'putUsuarios', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizar un usuario a la BD'],
            ['id' => 4,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'deleteUsuarios', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar un usuario a la BD'],
            ['id' => 5,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'viewPostUsuarios', 'tipo' => 'Vista', 'descripcion' => 'Vista create usuario'],
            ['id' => 6,  'id_sistema' => 1,'id_menu' => 3, 'modulo' => 'Usuarios', 'funcion' => 'viewPutUsuarios', 'tipo' => 'Vista', 'descripcion' => 'Vista Update usuario'],
            ['id' => 7,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'getGrupos', 'tipo' => 'Consulta', 'descripcion' => 'Insertar un grupo a la BD'],
            ['id' => 8,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'postGrupos', 'tipo' => 'Insercion', 'descripcion' => 'Insertar un grupo a la BD'],
            ['id' => 9,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Grupos', 'funcion' => 'putGrupos', 'tipo' => 'Actualizacion', 'descripcion' => 'Actualizar un grupo a la BD'],
            ['id' => 10,  'id_sistema' => 1,'id_menu' =>5, 'modulo' => 'Grupos', 'funcion' => 'deleteGrupos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar un grupo a la BD'],
            ['id' => 11,  'id_sistema' => 1,'id_menu' =>5, 'modulo' => 'Grupos', 'funcion' => 'viewPostGrupos', 'tipo' => 'Vista', 'descripcion' => 'Vista create grupo'],
            ['id' => 12,  'id_sistema' => 1,'id_menu' =>5, 'modulo' => 'Grupos', 'funcion' => 'viewPutGrupos', 'tipo' => 'Vista', 'descripcion' => 'Vista update grupo'],
            ['id' => 13,  'id_sistema' => 1,'id_menu' => 6, 'modulo' => 'Bitacora', 'funcion' => 'getBitacora', 'tipo' => 'Consulta', 'descripcion' => 'Consulta de registros de la bitácora'],
            ['id' => 14,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'getPermisos', 'tipo' => 'Consulta', 'descripcion' => 'Consulta de permisos'],
            ['id' => 15,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'postPermisos', 'tipo' => 'Insercion', 'descripcion' => 'Crear registro de permisos'],
            ['id' => 16,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'deletePermisos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar registro de permisos'],
            ['id' => 16,  'id_sistema' => 1,'id_menu' => 5, 'modulo' => 'Permisos', 'funcion' => 'deletePermisos', 'tipo' => 'Eliminacion', 'descripcion' => 'Eliminar registro de permisos'],
        );

    protected $grupos = array(

        ['id' => 1, 'nombre_grupo' => 'Administrador', 'estatus' => 0],
        ['id' => 2, 'nombre_grupo' => 'Gobdigital', 'estatus' => 0],
        ['id' => 3, 'nombre_grupo' => 'Auditor', 'estatus' => 0],
        ['id' => 4, 'nombre_grupo' => 'Upp', 'estatus' => 0],
        ['id' => 5, 'nombre_grupo' => 'Upp-CMO', 'estatus' => 0],
        ['id' => 6, 'nombre_grupo' => 'upp-CM', 'estatus' => 0],
        ['id' => 7, 'nombre_grupo' => 'upp-Obra', 'estatus' => 0],

    );



    public function run()
    {
        echo "\nInicializacion de Catalogos del Sistema";

        echo "\n    -Limpieza Anterior";
        DB::statement("SET foreign_key_checks=0");
        User::truncate();
        Sistema::truncate();

        Menu::truncate();
        Funciones::truncate();
        CatEntes::truncate();
        DB::statement("SET foreign_key_checks=1");

        DB::beginTransaction();
        try {
            echo "\n    -Carga Catálogo Entes";
            foreach ($this->cat_entes as $ente) {
                $ente_bd = CatEntes::find($ente['id']);
                if (!$ente_bd) {
                    CatEntes::create($ente);
                } else {
                    $ente_bd->update($ente);
                    $ente_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Usuarios";
            foreach ($this->cat_users as $user) {
                $user_bd = User::find($user['id']);
                if (!$user_bd) {
                    User::create($user);
                } else {
                    $user_bd->update($user);
                    $user_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Sistemas";
            foreach ($this->sistemas as $sistema) {
                $sistema_bd = Sistema::find($sistema['id']);
                if (!$sistema_bd) {
                    Sistema::create($sistema);
                } else {
                    $sistema_bd->update($sistema);
                    $sistema_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Menus";
            foreach ($this->menus as $menu) {
                $menu_bd = Menu::find($menu['id']);
                if (!$menu_bd) {
                    Menu::create($menu);
                } else {
                    $menu_bd->update($menu);
                    $menu_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Funciones";
            foreach ($this->funciones as $funcion) {
                $funcion_bd = Funciones::find($funcion['id']);
                if (!$funcion_bd) {
                    Funciones::create($funcion);
                } else {
                    $funcion_bd->update($funcion);
                    $funcion_bd->save();
                }
            }

            echo "\n    -Carga Catálogo Grupos";
            foreach ($this->grupos as $grupo) {
                $grupo_bd = Grupo::find($grupo['id']);
                if (!$grupo_bd) {
                    Grupo::create($grupo);
                } else {
                    $grupo_bd->update($grupo);
                    $grupo_bd->save();
                }
            }

      /*       $this->call([
                fondosSeeder::class
            ]); */

            DB::commit();
            echo "\n    - Se aplico con exito el Seeder - Base:\n";
        } catch (\Exception $e) {
            DB::rollback();
            echo "\n    - Ocurrio un error al ejecutar la operacion:",$e;
        }

    }
}
