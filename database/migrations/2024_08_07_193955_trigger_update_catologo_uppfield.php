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
            CREATE TRIGGER update_catalogo_uppfield
            AFTER UPDATE ON catalogo
            FOR EACH ROW
            BEGIN
                IF OLD.grupo_id = "UNIDAD PROGRAMÁTICA PRESUPUESTAL" THEN
                    -- Primer if es para verificar si es una actualización y no un soft delete
                    IF NEW.deleted_at IS NOT NULL THEN
                        -- Acciones en caso de soft delete 

                        -- Borrar cierre ejercicio claves
                        UPDATE cierre_ejercicio_claves
                        SET deleted_at = NOW(),
                            deleted_user = "SYSTEM"
                        WHERE ejercicio = OLD.ejercicio
                        AND clv_upp = OLD.clave;

                        -- Borrar cierre ejercicio metas
                        UPDATE cierre_ejercicio_metas
                        SET deleted_at = NOW(),
                            deleted_user = "SYSTEM"
                        WHERE ejercicio = OLD.ejercicio
                        AND clv_upp = OLD.clave;

                        -- Borrar cierre ejercicio mml
                        UPDATE mml_cierre_ejercicio
                        SET deleted_at = NOW(),
                            deleted_user = "SYSTEM"
                        WHERE ejercicio = OLD.ejercicio
                        AND clv_upp = OLD.clave;

                        -- Borrar cierre ejercicio sapp
                        UPDATE sapp_cierre_ejercicio
                        SET deleted_at = NOW(),
                            deleted_user = "SYSTEM"
                        WHERE ejercicio = OLD.ejercicio
                        AND clv_upp = OLD.clave;
                    END IF;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_catalogo_uppfield');
    }
};