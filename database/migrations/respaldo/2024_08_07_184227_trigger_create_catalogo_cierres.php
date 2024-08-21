<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        DB::unprepared('
            CREATE TRIGGER insert_cierres
            AFTER INSERT ON catalogo
            FOR EACH ROW
            BEGIN
                IF NEW.grupo_id = "UNIDAD PROGRAMÁTICA PRESUPUESTAL" THEN
                    -- Insert en cierre_ejercicio_claves
                    INSERT INTO cierre_ejercicio_claves (
                        clv_upp,
                        estatus,
                        ejercicio,
                        activos,
                        created_at,
                        created_user
                    ) VALUES (
                        NEW.clave,
                        "Abierto",
                        NEW.ejercicio,
                        1,
                        NOW(),
                        "SYSTEM"
                    );
                    -- Insert en cierre_ejercicio_metas
                    INSERT INTO cierre_ejercicio_metas (
                        clv_upp,
                        estatus,
                        ejercicio,
                        activos,
                        created_at,
                        created_user
                    ) VALUES (
                        NEW.clave,
                        "Abierto",
                        NEW.ejercicio,
                        1,
                        NOW(),
                        "SYSTEM"
                    );
                    -- Insert en mml_cierre_ejercicio
                    INSERT INTO mml_cierre_ejercicio (
                        clv_upp,
                        estatus,
                        statusm,
                        ejercicio,
                        created_at,
                        created_user
                    ) VALUES (
                        NEW.clave,
                        "Abierto",
                        0,
                        NEW.ejercicio,
                        NOW(),
                        "SYSTEM"
                    );
                    -- Insert en sapp_cierre_ejercicio
                    INSERT INTO sapp_cierre_ejercicio (
                        clv_upp,
                        ejercicio,
                        created_user,
                        created_at
                    ) VALUES (
                        NEW.clave,
                        NEW.ejercicio,
                        "SYSTEM",
                        NOW()
                    );
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_cierres');
    }
};