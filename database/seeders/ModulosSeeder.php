<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (1,"Usuarios","users",NULL,"admin","admin",NOW(),NULL,"mod",NULL,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (2,"Usuarios","users",NULL,"admin",NULL,NOW(),NULL,"sub",1,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (3,"Configuraciones","configuraciones",NULL,"admin",NULL,NOW(),NULL,"mod",NULL,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (4,"Permisos",NULL,NULL,"admin","admin",NOW(),NULL,"sub",3,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (5,"Bitacora de accesos",NULL,NULL,"admin","admin",NOW(),NULL,"sub",3,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (6,"Configuración inicial",NULL,NULL,"admin","admin",NOW(),NULL,"sub",3,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (7,"Concesiones","consultaradeudoconsescion",NULL,"admin","admin",NOW(),NULL,"mod",NULL,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (8,"Concesiones",NULL,NULL,"admin","admin",NOW(),NULL,"sub",7,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (9,"Reportes",NULL,NULL,"admin","admin",NOW(),NULL,"mod",NULL,1)');   
        DB::unprepared('INSERT INTO modulos (`id`,`modulo`,`ruta`,`icono`,`usuario_creacion`,`usuario_modificacion`,`created_at`,`updated_at`,`tipo`,`modulo_id`,`estatus`) VALUES (10,"Reportes",NULL,NULL,"admin","admin",NOW(),NULL,"sub",9,1)');   
    }
}
