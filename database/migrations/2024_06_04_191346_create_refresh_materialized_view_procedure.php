<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE PROCEDURE refresh_materialized_v_epp()
            BEGIN
                TRUNCATE TABLE v_epp;
                INSERT INTO v_epp
                SELECT 
                    e.id,
                    c01.clave AS clv_sector_publico, c01.descripcion AS sector_publico,
                    c02.clave AS clv_sector_publico_f, c02.descripcion AS sector_publico_f,
                    c03.clave AS clv_sector_economia, c03.descripcion AS sector_economia,
                    c04.clave AS clv_subsector_economia, c04.descripcion AS subsector_economia,
                    c05.clave AS clv_ente_publico, c05.descripcion AS ente_publico,
                    c06.clave AS clv_upp, c06.descripcion AS upp,
                    c07.clave AS clv_subsecretaria, c07.descripcion AS subsecretaria,
                    c08.clave AS clv_ur, c08.descripcion AS ur,
                    c09.clave AS clv_finalidad, c09.descripcion AS finalidad,
                    c10.clave AS clv_funcion, c10.descripcion AS funcion,
                    c11.clave AS clv_subfuncion, c11.descripcion AS subfuncion,
                    c12.clave AS clv_eje, c12.descripcion AS eje,
                    c13.clave AS clv_linea_accion, c13.descripcion AS linea_accion,
                    c14.clave AS clv_programa_sectorial, c14.descripcion AS programa_sectorial,
                    c15.clave AS clv_tipologia_conac, c15.descripcion AS tipologia_conac,
                    c16.clave AS clv_programa, c16.descripcion AS programa,
                    c17.clave AS clv_subprograma, c17.descripcion AS subprograma,
                    c18.clave AS clv_proyecto, c18.descripcion AS proyecto,
                    e.presupuestable,
                    e.con_mir,
                    e.confirmado,
                    e.tipo_presupuesto,
                    e.ejercicio,
                    e.deleted_at,
                    e.updated_at,
                    e.created_at
                FROM epp e
                JOIN catalogo c01 ON e.sector_publico_id = c01.id
                JOIN catalogo c02 ON e.sector_publico_f_id = c02.id
                JOIN catalogo c03 ON e.sector_economia_id = c03.id
                JOIN catalogo c04 ON e.subsector_economia_id = c04.id
                JOIN catalogo c05 ON e.ente_publico_id = c05.id
                JOIN catalogo c06 ON e.upp_id = c06.id
                JOIN catalogo c07 ON e.subsecretaria_id = c07.id
                JOIN catalogo c08 ON e.ur_id = c08.id
                JOIN catalogo c09 ON e.finalidad_id = c09.id
                JOIN catalogo c10 ON e.funcion_id = c10.id
                JOIN catalogo c11 ON e.subfuncion_id = c11.id
                JOIN catalogo c12 ON e.eje_id = c12.id
                JOIN catalogo c13 ON e.linea_accion_id = c13.id
                JOIN catalogo c14 ON e.programa_sectorial_id = c14.id
                JOIN catalogo c15 ON e.tipologia_conac_id = c15.id
                JOIN catalogo c16 ON e.programa_id = c16.id
                JOIN catalogo c17 ON e.subprograma_id = c17.id
                JOIN catalogo c18 ON e.proyecto_id = c18.id;
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
        DB::unprepared("DROP PROCEDURE IF EXISTS refresh_materialized_v_epp");
  
    }
};
