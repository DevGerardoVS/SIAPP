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
        DB::unprepared("DROP PROCEDURE IF EXISTS refresh_entidad_ejecutora_table");
        DB::unprepared("
        CREATE PROCEDURE refresh_entidad_ejecutora_table()
        BEGIN
            TRUNCATE TABLE v_entidad_ejecutora;

            INSERT INTO v_entidad_ejecutora (id, clv_upp, upp, clv_subsecretaria, subsecretaria, clv_ur, ur, ejercicio, deleted_at, updated_at, created_at, deleted_user, updated_user, created_user)
            SELECT 
                ee.id,
                c.clave AS clv_upp,
                c.descripcion AS upp,
                c2.clave AS clv_subsecretaria,
                c2.descripcion AS subsecretaria,
                c3.clave AS clv_ur,
                c3.descripcion AS ur,
                ee.ejercicio,
                ee.deleted_at,
                ee.updated_at,
                ee.created_at,
                ee.deleted_user,
                ee.updated_user,
                ee.created_user
            FROM entidad_ejecutora ee
            JOIN catalogo c ON ee.upp_id = c.id
            JOIN catalogo c2 ON ee.subsecretaria_id = c2.id
            JOIN catalogo c3 ON ee.ur_id = c3.id;
        END
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS refresh_entidad_ejecutora_table");
 
    }
};
