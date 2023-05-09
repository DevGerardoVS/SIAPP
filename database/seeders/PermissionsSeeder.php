<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios.consultar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios.agregar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios.editar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios.deshabilitar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("usuarios.usuarios.exportar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.modulos", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.modulos.consultar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.modulos.agregar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.modulos.editar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.modulos.deshabilitar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.funciones", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.funciones.consultar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.funciones.agregar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.funciones.editar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.funciones.deshabilitar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.perfiles", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.perfiles.consultar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.perfiles.agregar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.perfiles.editar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("permisos.perfiles.deshabilitar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones.consultar","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones.ver_poliza","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones.ver_detalle","web",NOW(),NOW())');
        //DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones.validar_poliza","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("concesiones.administrador_de_concesiones.exportar","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("reportes","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("reportes.reporte_de_polizas_de_seguro_por_concesion","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("reportes.reporte_de_polizas_de_seguro_por_concesion.consultar","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("reportes.reporte_de_polizas_de_seguro_por_concesion.exportar","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("bitacora_de_accesos", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("bitacora_de_accesos.bitacora", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("bitacora_de_accesos.bitacora.mostrar", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("bitacora_de_accesos.logs", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES ("bitacora_de_accesos.logs.ver_logs", "web", NOW(), NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.administrador_de_concesiones.reemplazar_poliza","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.administrador_de_concesiones.reemplazar_archivo_poliza","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones.consultar","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones.guardarpoliza","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones.getdatosconsesiones","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones.imprimirdatoss","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.consulta_de_concesiones.descargarformato","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.desbloqueo_de_concesiones","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.desbloqueo_de_concesiones.desbloqueoconcesion","web",NOW(),NOW())');
        DB::unprepared('INSERT INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES ("concesiones.desbloqueo_de_concesiones.desbloqueoconcesionupdate","web",NOW(),NOW())');

    }
}
