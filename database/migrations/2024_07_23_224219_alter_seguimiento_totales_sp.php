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
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");

        DB::unprepared("CREATE PROCEDURE seguimiento_totales(in anio int,in mes_n int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in subprograma_v varchar(3),in proyecto_v varchar(3))
begin
    drop temporary table if exists catalogo_upp_ur;
    drop temporary table if exists t_proyecto;
    drop temporary table if exists t_subprograma;
    drop temporary table if exists t_programa;
    drop temporary table if exists t_ur;
    drop temporary table if exists t_upp;
    
    create temporary table catalogo_upp_ur
    select 
        c1.clave clv_upp, c1.descripcion upp,
        c2.clave clv_ur, c2.descripcion ur
    from (
        select distinct 
            upp_id,ur_id
        from entidad_ejecutora e
        where ejercicio = anio and deleted_at is null
    )t
    left join catalogo c1 on t.upp_id = c1.id
    left join catalogo c2 on t.ur_id = c2.id;

    set @queri := concat(\"
    create temporary table t_proyecto
    with aux as (
        select 
            clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,
            sum(original_sapp) original, 
            sum(modificado) modificado,
            sum(comprometido_cp) comprometido,
            sum(devengado_cp) devengado,
            sum(ejercido_cp) ejercido,
            sum(pagado) pagado,
            date_sub(max(updated_at),interval 6 hour) updated_at
        from sapp_movimientos
        where ejercicio = \",anio,\" and clv_upp = '\",upp_v,\"' and clv_ur = '\",ur_v,\"' and 
        clv_programa = '\",programa_v,\"' and clv_subprograma = '\",subprograma_v,\"' 
        and clv_proyecto = '\",proyecto_v,\"' and mes = \",mes_n,\"
        group by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto
    )
    select 
        clv_upp,clv_ur,clv_programa,
        clv_subprograma,clv_proyecto,
        c.descripcion proyecto,
        original,modificado,comprometido,
        devengado,ejercido,pagado,a.updated_at
    from aux a
    left join catalogo c on a.clv_proyecto = c.clave and c.ejercicio = \",anio,\"
    and c.deleted_at is null and c.grupo_id = 18;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table t_subprograma
    with aux as (
        select 
            clv_upp,clv_ur,clv_programa,clv_subprograma,
            sum(original_sapp) original, 
            sum(modificado) modificado,
            sum(comprometido_cp) comprometido,
            sum(devengado_cp) devengado,
            sum(ejercido_cp) ejercido,
            sum(pagado) pagado,
            date_sub(max(updated_at),interval 6 hour) updated_at
        from sapp_movimientos
        where ejercicio = \",anio,\" and clv_upp = '\",upp_v,\"' and clv_ur = '\",ur_v,\"' and 
        clv_programa = '\",programa_v,\"' and clv_subprograma = '\",subprograma_v,\"' and mes = \",mes_n,\"
        group by clv_upp,clv_ur,clv_programa,clv_subprograma
    )
    select 
        clv_upp,clv_ur,clv_programa,
        clv_subprograma,
        c.descripcion subprograma,
        original,modificado,comprometido,
        devengado,ejercido,pagado,a.updated_at
    from aux a
    left join catalogo c on a.clv_subprograma = c.clave and c.ejercicio = \",anio,\"
    and c.deleted_at is null and c.grupo_id = 17;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table t_programa
    with aux as (
        select 
            clv_upp,clv_ur,clv_programa,
            sum(original_sapp) original, 
            sum(modificado) modificado,
            sum(comprometido_cp) comprometido,
            sum(devengado_cp) devengado,
            sum(ejercido_cp) ejercido,
            sum(pagado) pagado,
            date_sub(max(updated_at),interval 6 hour) updated_at
        from sapp_movimientos
        where ejercicio = \",anio,\" and clv_upp = '\",upp_v,\"' and clv_ur = '\",ur_v,\"' and 
        clv_programa = '\",programa_v,\"' and mes = \",mes_n,\"
        group by clv_upp,clv_ur,clv_programa
    )
    select 
        clv_upp,clv_ur,clv_programa,
        c.descripcion programa,
        original,modificado,comprometido,
        devengado,ejercido,pagado,a.updated_at
    from aux a
    left join catalogo c on a.clv_programa = c.clave and c.ejercicio = \",anio,\"
    and c.deleted_at is null and c.grupo_id = 16;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table t_ur
    with aux as (
        select 
            clv_upp,clv_ur,
            sum(original_sapp) original, 
            sum(modificado) modificado,
            sum(comprometido_cp) comprometido,
            sum(devengado_cp) devengado,
            sum(ejercido_cp) ejercido,
            sum(pagado) pagado,
            date_sub(max(updated_at),interval 6 hour) updated_at
        from sapp_movimientos
        where ejercicio = \",anio,\" and clv_upp = '\",upp_v,\"' and clv_ur = '\",ur_v,\"' and mes = \",mes_n,\"
        group by clv_upp,clv_ur
    )
    select 
        a.clv_upp,a.clv_ur,c.ur,
        original,modificado,comprometido,
        devengado,ejercido,pagado,a.updated_at
    from aux a
    left join catalogo_upp_ur c on a.clv_upp = c.clv_upp and a.clv_ur = c.clv_ur;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table t_upp
    with aux as (
        select 
            clv_upp,
            sum(original_sapp) original, 
            sum(modificado) modificado,
            sum(comprometido_cp) comprometido,
            sum(devengado_cp) devengado,
            sum(ejercido_cp) ejercido,
            sum(pagado) pagado,
            date_sub(max(updated_at),interval 6 hour) updated_at
        from sapp_movimientos
        where ejercicio = \",anio,\" and clv_upp = '\",upp_v,\"' and mes = \",mes_n,\"
        group by clv_upp
    )
    select 
        a.clv_upp,c.descripcion upp,
        original,modificado,comprometido,
        devengado,ejercido,pagado,a.updated_at
    from aux a
    left join catalogo c on a.clv_upp = c.clave and c.ejercicio = \",anio,\"
    and c.deleted_at is null and c.grupo_id = 6;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    select 
        clv_upp clave,upp descripcion,
        original,modificado,comprometido,devengado,
        ejercido,pagado,updated_at
    from t_upp
    union all
    select 
        clv_ur clave,ur descripcion,
        original,modificado,comprometido,devengado,
        ejercido,pagado,updated_at
    from t_ur
    union all
    select 
        clv_programa clave,programa descripcion,
        original,modificado,comprometido,devengado,
        ejercido,pagado,updated_at
    from t_programa
    union all
    select 
        clv_subprograma clave,subprograma descripcion,
        original,modificado,comprometido,devengado,
        ejercido,pagado,updated_at
    from t_subprograma
    union all
    select 
        clv_proyecto clave,proyecto descripcion,
        original,modificado,comprometido,devengado,
        ejercido,pagado,updated_at
    from t_proyecto;

    drop temporary table if exists catalogo_upp_ur;
    drop temporary table if exists t_proyecto;
    drop temporary table if exists t_subprograma;
    drop temporary table if exists t_programa;
    drop temporary table if exists t_ur;
    drop temporary table if exists t_upp;
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");
    }
};
