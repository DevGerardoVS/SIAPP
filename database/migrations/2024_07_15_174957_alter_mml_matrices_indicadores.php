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
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores;");

        DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int,in corte varchar(13),in upp_v varchar(3))
BEGIN
    drop temporary table if exists metas_seguimiento;
    drop temporary table if exists parte_uno;
    drop temporary table if exists parte_dos;
    drop temporary table if exists parte_tres;
    drop temporary table if exists parte_cuatro;
    drop temporary table if exists t_final;

    set @trimestre := \"\";
    if(trimestre_n = 1) then set @trimestre := \"and mes in (1,2,3)\"; end if;
    if(trimestre_n = 2) then set @trimestre := \"and mes in (4,5,6)\"; end if;
    if(trimestre_n = 3) then set @trimestre := \"and mes in (7,8,9)\"; end if;
    if(trimestre_n = 4) then set @trimestre := \"and mes in (10,11,12)\"; end if;

    set @metas_trimestres := \"\";
    if(trimestre_n = 1) then set @metas_trimestres := \"(enero+febrero+marzo)\"; end if;
    if(trimestre_n = 2) then set @metas_trimestres := \"(abril+mayo+junio)\"; end if;
    if(trimestre_n = 3) then set @metas_trimestres := \"(julio+agosto+septiembre)\"; end if;
    if(trimestre_n = 4) then set @metas_trimestres := \"(octubre+noviembre+diciembre)\"; end if;
    if(trimestre_n is null) then set @metas_trimestres := \"(total)\"; end if;

    set @query := concat(\"
    create temporary table metas_seguimiento
    select
        m.mir_id,
        unidad_medida,
        sum(total) total,
        case 
            when sum(realizado) is null then 0
            else sum(realizado)
        end trimestre
    from (
        select 
            m.id,mir_id,
            um.unidad_medida,
            \",@metas_trimestres,\" total
        from metas m
        join unidades_medida um on m.unidad_medida_id = um.id
        where m.ejercicio = \",anio,\" and m.deleted_at is null and m.mir_id is not null
    ) m
    left join (
        select meta_id,sum(realizado) realizado from sapp_seguimiento
        where ejercicio = \",anio,\" and deleted_at is null \",@trimestre,\"
        group by meta_id
    ) ss on ss.meta_id = m.id
    group by mir_id;
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @semaforo := \"\";
    if(semaforo = 0) then set @semaforo := concat(\"where avance <= 60\"); end if;
    if(semaforo = 1) then set @semaforo := concat(\"where avance > 60 and avance <= 94\"); end if;
    if(semaforo = 2) then set @semaforo := concat(\"where avance > 94 and avance <= 110\"); end if;
    if(semaforo = 3) then set @semaforo := concat(\"where avance > 110\"); end if;

    set @query := concat(\"
    create temporary table parte_uno
    with aux as (
        select 
            t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
            objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,total,trimestre,avance
        from (
            select 
                mm.clv_upp,
                mm.clv_pp,
                mm.clv_ur,
                substr(mm.area_funcional,5,2) clv_cpladem,
                mm.componente_padre,
                mm.nivel,
                'Actividad' tipo_indicador,
                mm.objetivo,
                mm.indicador,
                mm.definicion_indicador,
                mm.metodo_calculo,
                mm.frecuencia_medicion,
                ms.unidad_medida,
                mm.dimension,
                mm.descripcion_metodo,
                mm.medios_verificacion,
                sum(ms.total) total,
                case 
                    when sum(ms.trimestre) is null then 0
                    else sum(trimestre)
                end trimestre,
                case
                    when sum(ms.trimestre) is null then 0
                    when sum(ms.trimestre) = 0 then 0
                    when sum(ms.total) = 0 then 100
                    when sum(ms.trimestre) > 0 
                    then truncate(((sum(ms.trimestre)/sum(ms.total))*100),2)
                end avance
            from mml_mir mm 
            join metas_seguimiento ms on ms.mir_id = mm.id
            where mm.deleted_at is null and mm.ejercicio = \",anio,\" and mm.nivel = 11
            group by clv_upp,clv_pp,clv_ur,nivel,objetivo,indicador,
            definicion_indicador,metodo_calculo,descripcion_metodo,medios_verificacion
        )t
        join (
            select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from v_epp
            where ejercicio = \",anio,\" and deleted_at is null
        )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur
    )
    select *
    from aux \",@semaforo,\";
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table parte_dos
    select 
        t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
        objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,total,0 trimestre,avance
    from (
        select 
            mm.clv_upp,
            mm.clv_pp,
            mm.clv_ur,
            substr(mm.area_funcional,5,2) clv_cpladem,
            mm.id componente_padre,
            mm.nivel,
            'Componente' tipo_indicador,
            mm.objetivo,
            mm.indicador,
            mm.definicion_indicador,
            mm.metodo_calculo,
            mm.frecuencia_medicion,
            '' unidad_medida,
            mm.dimension,
            mm.descripcion_metodo,
            mm.medios_verificacion,
            0 total,
            0 trimestre,
            0 avance
        from mml_mir mm
        where id in (
            select distinct
                componente_padre
            from parte_uno
        )
    )t
    join (
        select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from v_epp
        where ejercicio = anio and deleted_at is null
    )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur;

    create temporary table parte_tres
    select 
        t.clv_upp,ve.upp,t.clv_pp,ve.programa,'' clv_ur,'' ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
        objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,total,0 trimestre,avance
    from (
        select 
            clv_upp,
            clv_pp,
            clv_ur,
            '' clv_cpladem,
            componente_padre,
            nivel,
            case 
                when nivel = 8 then 'Fin'
                when nivel = 9 then 'Proposito'
            end tipo_indicador,
            objetivo,
            indicador,
            definicion_indicador,
            metodo_calculo,
            frecuencia_medicion,
            unidad_medida,
            dimension,
            descripcion_metodo,
            medios_verificacion,
            0 total,
            0 trimestre,
            0 avance
        from mml_mir
        where ejercicio = anio and deleted_at is null and nivel in (8,9)
    )t
    join (
        select distinct clv_upp,upp,clv_programa clv_pp,programa from v_epp
        where ejercicio = anio and deleted_at is null
    )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp;

    alter table parte_uno convert to character set utf8 collate utf8_unicode_ci;
    alter table parte_dos convert to character set utf8 collate utf8_unicode_ci;
    alter table parte_tres convert to character set utf8 collate utf8_unicode_ci;

    create temporary table parte_cuatro
    with aux as (
        select * from parte_uno
        union all
        select * from parte_dos
        union all
        select * from parte_tres
    )
    select 
        clv_upp,upp,clv_pp,programa,clv_ur,ur,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,
        case 
            when frecuencia_medicion = 29 then 'Quincenal'
            when frecuencia_medicion = 30 then 'Mensual'
            when frecuencia_medicion = 31 then 'Bimestral'
            when frecuencia_medicion = 32 then 'Trimestral'
            when frecuencia_medicion = 33 then 'Cuatrimestral'
            when frecuencia_medicion = 34 then 'Semestral'
            when frecuencia_medicion = 35 then 'Anual'
            when frecuencia_medicion = 36 then 'Bianual'
            when frecuencia_medicion = 37 then 'Quinquenal'
            when frecuencia_medicion = 38 then 'Sexenal'
        end frecuencia_medicion,
        case
            when nivel = 11 then unidad_medida
            else '' 
        end unidad_medida,
        case 
            when dimension = 21 then 'Eficacia'
            when dimension = 22 then 'Eficiencia'
            when dimension = 23 then 'Calidad'
            when dimension = 24 then 'Economía'
        end dimension,
        descripcion_metodo,medios_verificacion,total meta_anual,trimestre,avance,ca.clv_cpladem
    from aux a 
    left join (
        select 
            clave,
            case 
                when substr(descripcion,8,1) = '.' then substr(descripcion,1,7)
                when substr(descripcion,8,1) = ' ' then substr(descripcion,1,7)
                else substr(descripcion,1,8)
            end clv_cpladem
        from catalogo
        where ejercicio = anio and deleted_at is null and grupo_id = 13
    ) ca on a.clv_cpladem = ca.clave
    order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

    create temporary table t_final
    select 
        clv_upp,upp,clv_pp clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,componente_padre padre,objetivo resumen_narrativo,indicador nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,
        frecuencia_medicion,
        unidad_medida,
        dimension,
        medios_verificacion,meta_anual,trimestre,avance,
        case 
            when avance <= 60 then 0
            when avance > 60 and avance <= 94 then 1
            when avance > 94 and avance <= 110 then 2
            when avance > 110 then 3
        end color
    from parte_cuatro pc
    order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

    if(upp_v is null) then 
        select * from t_final;
    else
        select * from t_final where clv_upp = upp_v;
    end if;

    drop temporary table if exists metas_seguimiento;
    drop temporary table if exists parte_uno;
    drop temporary table if exists parte_dos;
    drop temporary table if exists parte_tres;
    drop temporary table if exists parte_cuatro;
    drop temporary table if exists t_final;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores;");
    }
};
