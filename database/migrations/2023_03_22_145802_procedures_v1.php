<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE sp_check_permission(in_usuario INT, in_funcion VARCHAR(100), in_sistema INT)
        BEGIN 
            SELECT p.id
            FROM adm_rel_funciones_grupos p
            INNER JOIN adm_funciones f ON f.id = p.id_funcion
            WHERE f.funcion = in_funcion
            AND f.id_sistema = in_sistema
            AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = in_usuario);
        END");

        DB::unprepared("CREATE PROCEDURE sp_menu_sidebar(in_usuario INT, in_sistema INT, in_padre INT)
        BEGIN
            SELECT
            m.id,
            m.nombre_menu,
            m.ruta,
            m.icono,
            m.descripcion
            FROM adm_menus m
            WHERE m.padre = COALESCE(in_padre, 0)
            AND m.id_sistema = in_sistema
            AND m.id <> 0
            AND (m.id IN (SELECT mg.id_menu FROM adm_rel_menu_grupo mg WHERE mg.id_grupo IN (SELECT ug.id_grupo FROM adm_rel_user_grupo ug WHERE ug.id_usuario = in_usuario))
            OR (SELECT u.sudo FROM adm_users u WHERE u.id = in_usuario) = 1)
            ORDER BY m.posicion ASC;
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_check_permission");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_menu_sidebar");
    }
};
