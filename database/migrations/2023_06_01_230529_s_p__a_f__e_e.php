<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "begin
        if softdelete != 1 then

            select
                ct1.deleted_at upp_baja,

                ct1.clave upp_clave,

                ct1.descripcion upp,
                ct2.deleted_at subsecretaria_baja,

                ct2.clave subsecretaria_clave,

                ct2.descripcion subsecretaria,
                ct3.deleted_at ur_baja,

                ct3.clave ur_clave,

                ct3.descripcion ur,
                ct4.deleted_at programa_baja,

                ct4.clave programa_clave,

                ct4.descripcion programa,
                ct4.deleted_at subprograma_baja,

                ct5.clave subprograma_clave,

                ct5.descripcion subprograma,
                ct6.deleted_at proyecto_baja,

                ct6.clave proyecto_clave,

                ct6.descripcion proyecto
            from area_funcional_entidad_ejecutora afee
            join area_funcional aft on afee.area_funcional_id = aft.id
            join entidad_ejecutora eet on afee.entidad_ejecutora_id = eet.id
            join catalogo ct1 on eet.upp_id = ct1.id
            join catalogo ct2 on eet.subsecretaria_id = ct2.id
            join catalogo ct3 on eet.ur_id = ct3.id
            join catalogo ct4 on aft.programa_presupuestario_id = ct4.id
            join catalogo ct5 on aft.subprograma_presupuestario_id = ct5.id
            join catalogo ct6 on aft.proyecto_presupuestario_id = ct6.id
            where find_in_set(ct1.ejercicio, anio) and (case
                when softdelete = 0
                    then
                        ct1.deleted_at is not null or
                        ct2.deleted_at is not null or
                        ct3.deleted_at is not null or
                        ct4.deleted_at is not null or
                        ct5.deleted_at is not null or
                        ct6.deleted_at is not null
                else afee.id >= 1
                end);
        else
            select
                ct1.clave upp_clave,
                ct1.descripcion upp,
                ct2.clave subsecretaria_clave,
                ct2.descripcion subsecretaria,
                ct3.clave ur_clave,
                ct3.descripcion ur,
                ct4.clave programa_clave,
                ct4.descripcion programa,
                ct5.clave subprograma_clave,
                ct5.descripcion subprograma,
                ct6.clave proyecto_clave,
                ct6.descripcion proyecto
            from area_funcional_entidad_ejecutora afee
            join area_funcional aft on afee.area_funcional_id = aft.id
            join entidad_ejecutora eet on afee.entidad_ejecutora_id = eet.id
            join catalogo ct1 on eet.upp_id = ct1.id
            join catalogo ct2 on eet.subsecretaria_id = ct2.id
            join catalogo ct3 on eet.ur_id = ct3.id
            join catalogo ct4 on aft.programa_presupuestario_id = ct4.id
            join catalogo ct5 on aft.subprograma_presupuestario_id = ct5.id
            join catalogo ct6 on aft.proyecto_presupuestario_id = ct6.id
            where find_in_set(ct1.ejercicio, anio) and
                ct1.deleted_at is null and
                ct2.deleted_at is null and
                ct3.deleted_at is null;
        end if;

    END";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};