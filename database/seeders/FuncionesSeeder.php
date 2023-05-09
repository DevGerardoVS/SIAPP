<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FuncionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`, `orden`) VALUES ("Usuarios", "users", NULL, "{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar usuarios\"},{\"accion\":\"agregar\",\"descripcion\":\"Agregar usuarios \"},{\"accion\":\"editar\",\"descripcion\":\"Editar usuarios\"},{\"accion\":\"deshabilitar\",\"descripcion\":\"Deshabilitar usuarios\"},{\"accion\":\"exportar\",\"descripcion\":\"Exportar datos\"}]}", 2, 1, "admin", NULL, NOW(), NULL, 1)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`, `orden`) VALUES ("Modulos", "modulos", NULL, "{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar módulos\"},{\"accion\":\"agregar\",\"descripcion\":\"Agregar módulos\"},{\"accion\":\"editar\",\"descripcion\":\"Editar módulos\"},{\"accion\":\"deshabilitar\",\"descripcion\":\"Deshabilitar módulos\"}]}", 4, 1, "admin", NULL, NOW(), NULL, 1)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`, `orden`) VALUES ("Funciones", "funciones", NULL, "{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar funciones\"},{\"accion\":\"agregar\",\"descripcion\":\"Agregar funciones\"},{\"accion\":\"editar\",\"descripcion\":\"Editar funciones\"},{\"accion\":\"deshabilitar\",\"descripcion\":\"Deshabilitar funciones\"}]}", 4, 1, "admin", NULL, NOW(), NULL, 2)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`, `orden`) VALUES ("Perfiles", "perfiles", NULL, "{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar perfiles\"},{\"accion\":\"agregar\",\"descripcion\":\"Agregar perfiles\"},{\"accion\":\"editar\",\"descripcion\":\"Editar perfiles\"},{\"accion\":\"deshabilitar\",\"descripcion\":\"Deshabilitar perfiles\"}]}", 4, 1, "admin", NULL, NOW(), NULL, 3)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`,`ruta`,`icono`,`acciones`,`orden`,`modulo_id`,`estatus`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`) VALUES ("Administrador de concesiones","admin_concesiones",NULL,"{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar datos\"},{\"accion\":\"ver_poliza\",\"descripcion\":\"Ver archivo póliza\"},{\"accion\":\"ver_detalle\",\"descripcion\":\"Ver detalle de concesión\"},{\"accion\":\"exportar\",\"descripcion\":\"Exportar excel\"},{\"accion\":\"reemplazar_poliza\",\"descripcion\":\"Reemplazar póliza de seguro\"},{\"accion\":\"reemplazar_archivo_poliza\",\"descripcion\":\"Reemplazar documento de póliza de seguro\"}]}",0,8,1,"admin",NULL,NOW(),NULL)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`,`ruta`,`icono`,`acciones`,`orden`,`modulo_id`,`estatus`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`) VALUES ("Reporte de pólizas de seguro por concesión","reporte_polizas_x_concesion",NULL,"{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar datos\"},{\"accion\":\"exportar\",\"descripcion\":\"Exportar datos en excel\"}]}",0,10,1,"admin",NULL,NOW(),NULL)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `orden`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`) VALUES ("Bitácora", "bitacoras", NULL, "{\"acciones\":[{\"accion\":\"mostrar\",\"descripcion\":\"Mostrar listado\"}]}", 0, 5, 1, "admin", NULL, NOW(), NULL)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`, `ruta`, `icono`, `acciones`, `orden`, `modulo_id`, `estatus`, `usuario_creacion`, `usuario_modificacion`, `created_at`, `updated_at`) VALUES ("Logs", "viewLogs", NULL, "{\"acciones\":[{\"accion\":\"ver_logs\",\"descripcion\":\"Ver logs\"}]}", 0, 5, 1, "admin", NULL, NOW(), NULL)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`,`ruta`,`icono`,`acciones`,`orden`,`modulo_id`,`estatus`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`) VALUES ("Consulta de concesiones","consultaradeudoconsescion",NULL,"{\"acciones\":[{\"accion\":\"consultar\",\"descripcion\":\"Consultar concesiones\"},{\"accion\":\"guardarpoliza\",\"descripcion\":\"guardarpoliza\"},{\"accion\":\"getdatosconsesiones\",\"descripcion\":\"getdatosconsesiones\"},{\"accion\":\"imprimirdatoss\",\"descripcion\":\"imprimirdatoss\"},{\"accion\":\"descargarformato\",\"descripcion\":\"descargarformato\"}]}",1,8,1,"admin",NULL,NOW(),NULL)');
        DB::unprepared('INSERT INTO `funciones` (`funcion`,`ruta`,`icono`,`acciones`,`orden`,`modulo_id`,`estatus`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`) VALUES ("Desbloqueo de concesiones","desbloqueoconcesion",NULL,"{\"acciones\":[{\"accion\":\"desbloqueoconcesion\",\"descripcion\":\"desbloqueoconcesion\"},{\"accion\":\"desbloqueoconcesionupdate\",\"descripcion\":\"desbloqueoconcesionupdate\"}]}",2,8,1,"admin",NULL,NOW(),NULL)');

    }
}