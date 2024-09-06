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
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_cierres_etapas;");

        DB::unprepared("CREATE PROCEDURE llenado_cierres_etapas(in tipo int)
BEGIN
    set @anio := (select max(ejercicio) from v_epp);
    update cierre_ejercicio_claves set estatus = 'Cerrado' where ejercicio < @anio;
    update cierre_ejercicio_metas set estatus = 'Cerrado' where ejercicio < @anio;
    update mml_cierre_ejercicio set estatus = 'Cerrado' where ejercicio < @anio;
    set @claves := (select count(*) from cierre_ejercicio_claves where ejercicio = @anio);
    set @metas := (select count(*) from cierre_ejercicio_metas where ejercicio = @anio);
    set @mml_cierre := (select count(*) from mml_cierre_ejercicio where ejercicio = @anio);

    if(@claves = 0) then
        insert into cierre_ejercicio_claves(clv_upp,estatus,ejercicio,created_at,created_user,updated_at,updated_user,deleted_at,deleted_user,activos) 
        select distinct 
            clv_upp,
            'Cerrado' estatus,
            ejercicio,
            now() created_at,
            'SISTEMA' created_user,
            now() updated_at,
            null updated_user,
            null deleted_at,
            null deleted_user,
            1 activos
        from v_epp ve where ejercicio = @anio;
    end if;

    if(@metas = 0) then
        insert into cierre_ejercicio_metas(clv_upp,estatus,ejercicio,created_at,created_user,updated_at,updated_user,deleted_at,deleted_user,activos) 
        select distinct 
            clv_upp,
            'Cerrado' estatus,
            ejercicio,
            now() created_at,
            'SISTEMA' created_user,
            now() updated_at,
            null updated_user,
            null deleted_at,
            null deleted_user,
            1 activos
        from v_epp ve where ejercicio = @anio;
    end if;

    if(@mml_cierre = 0) then
        insert into mml_cierre_ejercicio(clv_upp,estatus,ejercicio,created_at,created_user,updated_at,updated_user,deleted_at,deleted_user)
        select distinct 
            clv_upp,
            'Cerrado' estatus,
            ejercicio,
            now() created_at,
            'SISTEMA' created_user,
            now() updated_at,
            null updated_user,
            null deleted_at,
            null deleted_user
        from v_epp ve where ejercicio = @anio;
    end if;

    insert into mml_avance_etapas_pp(clv_upp,clv_pp,etapa_0,etapa_1,etapa_2,etapa_3,etapa_4,etapa_5,estatus,ejercicio,created_user,updated_user,deleted_user,created_at,updated_at,deleted_at,ramo33)
    select distinct
        clv_upp,
        clv_programa,
        0 etapa_0,
        0 etapa_1,
        0 etapa_2,
        0 etapa_3,
        0 etapa_4,
        0 etapa_5,
        0 estatus,
        ejercicio,
        'SISTEMA' created_user,
        null updated_user,
        null deleted_user,
        now() created_at,
        now() updated_at,
        null deleted_at,
		0 ramo33
    from v_epp ve
    where ejercicio = @anio
    and presupuestable = 1;

    insert into sapp_cierre_ejercicio(
        clv_upp,enero,febrero,marzo,trimestre_uno,abril,mayo,junio,trimestre_dos,
        julio,agosto,septiembre,trimestre_tres,octubre,noviembre,diciembre,trimestre_cuatro,
        ejercicio,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    select 
        clave clv_upp,
        0 enero,0 febrero,0 marzo,0 trimestre_uno,
        0 abril,0 mayo,0 junio,0 trimestre_dos,
        0 julio,0 agosto,0 septiembre,0 trimestre_tres,
        0 octubre,0 noviembre,0 diciembre,0 trimestre_cuatro,
        @anio,now(),now(),null,'SISTEMA',null,null
    from catalogo 
    where ejercicio = @anio and deleted_at is null and grupo_id = 6
    order by clv_upp;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_cierres_etapas;");
    }
};
