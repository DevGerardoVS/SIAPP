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
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_III;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2025;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_10;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_8;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_II(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    set @id := 'id';
    set @metas := 'metas';
    set @mir := 'mml_mir';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @epp := 'v_epp_hist';
        set @id := 'id_original';
        set @metas := 'metas_hist';
        set @mir := 'mml_mir_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;

    set @queri:= concat('
    create temporary table aux_0 
    select 
        mm.clv_upp,
        m.clv_fondo,
        mm.clv_pp clv_programa,
        substr(mm.area_funcional,11,3) clv_subprograma,
        mm2.indicador,
        mm.objetivo,
        mm.indicador actividad,
        count(*) metas
    from ',@metas,' m
    join ',@mir,' mm on m.mir_id = mm.',@id,' and mm.tipo_indicador = 13
    left join (
        select 
            mm.clv_upp,
            mm.clv_pp,
            mm.indicador
        from ',@mir,' mm
        where ejercicio = ',anio,' and ',@corte,' and nivel = 8
    ) mm2 on mm.clv_upp = mm2.clv_upp and mm.clv_pp = mm2.clv_pp
    where m.mir_id is not null and m.',@corte,' and m.ejercicio = ',anio,'
    group by mm.clv_upp,m.clv_fondo,mm.clv_pp,mm.area_funcional,mm2.indicador,mm.objetivo,mm.indicador;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri:= concat('
    create temporary table aux_1
    select 
        upp clv_upp,
        fondo_ramo clv_fondo,
        programa_presupuestario clv_programa,
        subprograma_presupuestario clv_subprograma,
        sum(total) importe
    from ',@tabla,' pp
    where pp.ejercicio = ',anio,' and pp.',@corte,'
    group by upp,fondo_ramo,programa_presupuestario,subprograma_presupuestario;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @catalogo := concat('
    select distinct
	    clv_upp,upp,clv_programa,
	    programa,clv_subprograma,subprograma
    from ',@epp,'
    where ejercicio = ',anio,' and ',@corte,'
    ');

    set @queri := concat('
    create temporary table aux_2
    select 
        a0.clv_upp,
        ve.upp,
        c.clave clv_fuente_financiamiento,
        c.descripcion fuente_financiamiento,
        a0.clv_programa,
        ve.programa,
        a0.clv_subprograma,
        ve.subprograma,
        a0.indicador,
        a0.objetivo,
        a0.actividad,
        a0.metas,
        case 
            when a1.importe is null then 0
            else a1.importe
        end importe
    from aux_0 a0 
    left join aux_1 a1 on a0.clv_upp = a1.clv_upp
    and a0.clv_fondo = a1.clv_fondo and a0.clv_programa = a1.clv_programa
    and a0.clv_subprograma = a1.clv_subprograma
    left join catalogo c on a0.clv_fondo = c.clave and c.grupo_id = 37 and c.deleted_at is null and c.ejercicio = ',anio,'
    left join (',@catalogo,') ve on a0.clv_upp = ve.clv_upp and a0.clv_programa = ve.clv_programa
    and a0.clv_subprograma = ve.clv_subprograma;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    create temporary table aux_3
    select distinct 
        clv_upp,upp,
        clv_fuente_financiamiento,fuente_financiamiento,
        clv_programa,programa,
        clv_subprograma,subprograma
    from aux_2 a2;

    select 
        case 
            when objetivo != '' then ''
            else clv_upp
        end clv_upp,
        case 
            when objetivo != '' then ''
            else upp
        end upp,
        case 
            when objetivo != '' then ''
            else clv_fuente_financiamiento
        end clv_fuente_financiamiento,
        case
            when objetivo != '' then ''
            else fuente_financiamiento
        end fuente_financiamiento,
        case 
            when objetivo != '' then ''
            else clv_programa
        end clv_programa,
        case 
            when objetivo != '' then ''
            else programa
        end programa,
        case 
            when objetivo != '' then ''
            else clv_subprograma
        end clv_subprograma,
        case 
            when objetivo != '' then ''
            else subprograma
        end subprograma,
        case
            when indicador IS NULL then ''
            ELSE indicador 
        end indicador,
        objetivo,
        actividad,
        metas,
        importe
    from (
        select * 
        from aux_2 a2
        union all 
        select a3.*,
            '' indicador,
            '' objetivo,
            '' actividad,
            0 metas,
            0 importe
        from aux_3 a3
        order by clv_upp,clv_fuente_financiamiento,
        clv_programa,clv_subprograma,metas
    )t;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_III(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists pp_temporal;

    set @queri := concat('
    create temporary table aux_0
    select distinct
        clv_upp,upp,clv_ur,ur,
        clv_programa,programa,
        clv_subprograma,subprograma,
        clv_proyecto,proyecto
    from ',@epp,'
    where ejercicio = ',anio,';
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @query := concat('
    create temporary table aux_1
    select 
        upp clv_upp,ur clv_ur,programa_presupuestario clv_programa,
        subprograma_presupuestario clv_subprograma,
        proyecto_presupuestario clv_proyecto,
        posicion_presupuestaria pos_pre,
        sum(total) importe
    from ',@tabla,' pp
    where ejercicio = ',anio,' and ',@corte,'
    group by upp,ur,programa_presupuestario,
    subprograma_presupuestario,
    proyecto_presupuestario,posicion_presupuestaria;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table pp_temporal
    select 
        ce.id,
        ce.ejercicio,
        c1.clave clv_capitulo,c1.descripcion capitulo,
        c2.clave clv_concepto,c2.descripcion concepto,
        c3.clave clv_partida_generica,c3.descripcion partida_generica,
        c4.clave clv_partida_especifica,c4.descripcion partida_especifica,
        c5.clave clv_tipo_gasto,c5.descripcion tipo_gasto,
        ce.estatus,
        ce.created_at,ce.updated_at,ce.deleted_at,
        ce.created_user,ce.updated_user,ce.deleted_user
    from clasificacion_economica ce
    join catalogo c1 on ce.capitulo_id = c1.id
    join catalogo c2 on ce.concepto_id = c2.id
    join catalogo c3 on ce.partida_generica_id = c3.id
    join catalogo c4 on ce.partida_especifica_id = c4.id
    join catalogo c5 on ce.tipo_gasto_id = c5.id
    where ce.deleted_at is null;
    
    select 
        a1.clv_upp,a0.upp,
        a1.clv_ur,a0.ur,
        a1.clv_programa,a0.programa,
        a1.clv_subprograma,a0.subprograma,
        a1.clv_proyecto,a0.proyecto,
        a1.pos_pre clv_partida,
        pp.partida_especifica,
        sum(a1.importe) importe
    from aux_1 a1
    left join aux_0 a0 on a1.clv_upp = a0.clv_upp and
    a1.clv_ur = a0.clv_ur and a1.clv_programa = a0.clv_programa
    and a1.clv_subprograma = a0.clv_subprograma
    and a1.clv_proyecto = a0.clv_proyecto
    left join pp_temporal pp on
    concat(pp.clv_capitulo,pp.clv_concepto,
    pp.clv_partida_generica,pp.clv_partida_especifica) = a1.pos_pre
    group by a1.clv_upp,a0.upp,a1.clv_ur,a0.ur,
    a1.clv_programa,a0.programa,a1.clv_subprograma,
    a0.subprograma,a1.clv_proyecto,a0.proyecto,a1.pos_pre,
    pp.partida_especifica
    order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists pp_temporal;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_IX(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    set @metas := 'metas';
    set @mir := 'mml_mir';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @catalogo := 'catalogo_hist';
        set @metas := 'metas_hist';
        set @mir := 'mml_mir_hist';
        set @id := 'id_original';
        set @corte := concat('version = ',ver);
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
    
    set @queri := concat('
    create temporary table aux_0 
    select 
        mm.clv_pp clv_programa,
        mm2.indicador,
        mm.objetivo,
        mm.indicador actividad,
        count(*) metas
    from ',@metas,' m
    join ',@mir,' mm on m.mir_id = mm.',@id,' and mm.tipo_indicador = 13
    left join (
        select 
            mm.clv_upp,
            mm.clv_pp,
            mm.indicador
        from ',@mir,' mm
        where ejercicio = ',anio,' and nivel = 8 and ',@corte,'
    ) mm2 on mm.clv_upp = mm2.clv_upp and mm.clv_pp = mm2.clv_pp
    where m.mir_id is not null and m.ejercicio = ',anio,' and m.',@corte,'
    group by mm.clv_pp,mm2.indicador,mm.objetivo,mm.indicador;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @queri := concat('
    create temporary table aux_1
    select 
        programa_presupuestario clv_programa,
        sum(total) importe
    from ',@tabla,' pp
    where ejercicio = ',anio,' and ',@corte,'
    group by programa_presupuestario;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat('
    create temporary table aux_2
    select 
        a0.clv_programa,
        ve.programa,
        a0.indicador,
        a0.objetivo,
        a0.actividad,
        a0.metas,
        case 
            when a1.importe is null then 0
            else a1.importe
        end importe
    from aux_0 a0 
    left join aux_1 a1 on a0.clv_programa = a1.clv_programa
    left join (
        select distinct
            clave clv_programa,descripcion programa
        from ',@catalogo,'
        where ejercicio = ',anio,' and deleted_at is null and grupo_id = 16
    ) ve on a0.clv_programa = ve.clv_programa;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    create temporary table aux_3
    select distinct 
        clv_programa,programa
    from aux_2 a2;

    select distinct
        case 
            when objetivo != '' then ''
            else clv_programa
        end clv_programa,
        case 
            when objetivo != '' then ''
            else programa
        end programa,
        case
            when indicador is null then ''
            else indicador 
        end indicador,
        objetivo,
        actividad,
        metas,
        importe
    from (
        select * 
        from aux_2 a2
        union all 
        select a3.*,
            '' indicador,
            '' objetivo,
            '' actividad,
            0 metas,
            0 importe
        from aux_3 a3
        order by clv_programa,metas
    )t;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @epp := 'v_epp';
    set @corte := 'deleted_at is null';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @epp := 'v_epp_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @query := CONCAT('
        select
            upp,
            case
                when importe is null then 0 
                else importe 
            end importe
        from (
            select 
                c.clv_upp,
                c.upp,
                sum(total) importe
            from (
                select distinct clv_upp,upp
                from ',@epp,' where ejercicio = ',anio,' and ',@corte,'
            ) c
            left join ',@tabla,' pp on c.clv_upp = pp.upp 
            and pp.ejercicio = ',anio,' and pp.',@corte,'
            group by c.clv_upp,c.upp
            order by clv_upp
        )t;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto pp';
    set @corte := 'deleted_at is null';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist pp';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @queri := CONCAT('
        select
            concepto,
            case 
                when importe is null then 0
                else importe
            end importe
        from (
            select 
                \"Poder Ejecutivo\" concepto,
                sum(pp.total) importe
            from ',@tabla,'
            where pp.clasificacion_administrativa in (\"21111\",\"21120\")
                and pp.ejercicio = ',anio,' and ',@corte,'
            union all
            select 
                \"Poder Legislativo\" concepto,
                sum(pp.total) importe
            from ',@tabla,'
            where pp.clasificacion_administrativa in (\"21112\")
                and pp.ejercicio = ',anio,' and ',@corte,'
            union all
            select 
                \"Poder Judicial\" concepto,
                sum(pp.total) importe
            from ',@tabla,'
            where pp.clasificacion_administrativa in (\"21113\")
                and pp.ejercicio = ',anio,' and ',@corte,'
            union all
            select 
                \"Organos Autónomos\" concepto,
                sum(pp.total) importe
            from ',@tabla,'
            where pp.clasificacion_administrativa in (\"21114\")
                and pp.ejercicio = ',anio,' and ',@corte,'
            group by concepto
        )  tabla;
    ');

    prepare stmt  from @queri;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_3(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto pp';
    set @corte := 'deleted_at is null';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist pp';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @queri = CONCAT('
        select  
            \"Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros\" concepto,
            sum(total) as importe
        from ',@tabla,'
        where pp.clasificacion_administrativa like \"21120\"
        and pp.ejercicio = ',anio,' and ',@corte,'
        group by concepto
        union all 
        select 
            \"Instituciones Públicas de la Seguridad Social\" concepto,
            0 importe
        union all
        select
            \"Entidades Paraestatales Empresariales No Financieras con Participaciones Estatal Mayoritaria\" concepto,
            0 importe
        union all
        select
            \"Fideicomisos Empresariales No Financieros con Participación Estatal Mayoritaria\" concepto,
            0 importe
        union all
        select
            \"Entidades Pararestatales Empresariales Financieras Monetarias con Participación Estatal Mayoritaria\" concepto,
           0 importe
        union all
        select
            \"Entidades Pararestatales Empresariales Financieras No Monetarias con Participación Estatal Mayoritaria\" concepto,
            0 importe
        union all
        select
            \"Fideicomisos Financieros Públicos con Participación Estatal Mayoritaria\" concepto,
            0 importe;
    ');

    prepare stmt  from @queri;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2025(in anio int,in ver int)
BEGIN
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if(ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
    end if;

    drop temporary table if exists cap_concepto;

    create temporary table cap_concepto
    select distinct
        c1.clave clv_capitulo,c1.descripcion capitulo,
        c2.clave clv_concepto,c2.descripcion concepto,
        concat(c1.clave,c2.clave) llave
    from clasificacion_economica ce 
    join catalogo c1 on ce.capitulo_id = c1.id
    join catalogo c2 on ce.concepto_id = c2.id
    where ce.deleted_at is null;

    set @queri := concat(\"
    with aux as (
        select 
            ce.clv_capitulo,ce.capitulo,
            ce.clv_concepto,ce.concepto,
            case 
                when sum(pp.total) is null then 0
                else sum(pp.total)
            end importe
        from cap_concepto ce
        left join \",@tabla,\" pp on ce.llave = substr(pp.posicion_presupuestaria,1,2)
        and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        group by ce.clv_capitulo,ce.capitulo,ce.clv_concepto,ce.concepto
        union all 
        select distinct 
            clv_capitulo,capitulo,
            '' clv_concepto,'' concepto,
            0 importe
        from cap_concepto
        order by clv_capitulo,clv_concepto
    )
    select 
        case 
            when clv_concepto = '' then concat(clv_capitulo,0)
            else concat(clv_capitulo,clv_concepto)
        end orden,
        case 
            when clv_concepto != '' then ''
            else capitulo
        end capitulo,
        concepto,
        importe
    from aux
    order by orden;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists cap_concepto;
END;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_2(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
        set @epp := 'v_epp_hist';
        set @id := 'id_original';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;

    set @query := concat('
    create temporary table aux_0
    select distinct
        clv_finalidad,finalidad,clv_funcion,funcion
    from ',@epp,'
    where ejercicio = ',anio,' and ',@corte,';
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    create temporary table aux_1
    select 
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        sum(pp.total) importe
    from ',@tabla,' pp
    join aux_0 a0 on pp.finalidad = a0.clv_finalidad 
    and pp.funcion = a0.clv_funcion
    where pp.ejercicio = ',anio,' and pp.',@corte,'
    group by a0.clv_finalidad,a0.finalidad,a0.clv_funcion,a0.funcion;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        clv_finalidad,
        finalidad,
        sum(importe) importe
    from aux_1
    group by clv_finalidad,finalidad;
    
    select 
        case 
            when funcion != '' then ''
            else finalidad
        end finalidad,
        funcion,
        importe
    from (
        select 
            clv_finalidad,finalidad,
            '' clv_funcion,'' funcion,
            importe
        from aux_2
        union all
        select * from aux_1
        order by clv_finalidad,clv_funcion
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_3(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    if(ver is not null) then
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists conac_temporal;

    set @queri := concat(\"
    create temporary table conac_temporal
    select 
    	c.id,c.ejercicio,
    	c1.clave clv_padre,c1.descripcion padre,
    	c2.clave clv_tipologia_conac,c2.descripcion tipologia_conac
    from conac c
    join catalogo c1 on c.padre_id = c1.id
    left join catalogo c2 on c.tipologia_conac_id = c2.id
    where c.deleted_at is null and c.ejercicio = \",anio,\";
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @tablaWith := concat(\"
    with aux as (
        select 
            min(tc.id) id,
            'Programas' abuelo,
            tc.padre,
            tc.tipologia_conac hijo,
            sum(pp.total) importe
        from conac_temporal tc 
        left join \",@tabla,\" pp on tc.clv_tipologia_conac = pp.tipologia_conac
        and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        group by tc.padre,tc.tipologia_conac
        union all
        select 
            min(tc.id) id,
            tc.padre abuelo,
            '' padre,
            '' hijo,
            sum(pp.total) importe
        from conac_temporal tc 
        left join \",@tabla,\" pp on tc.clv_tipologia_conac = pp.tipologia_conac
        and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        group by tc.padre
        order by id
    )\");
    
    set @selectWith := \"select 
        case 
            when padre != '' then ''
            else abuelo
        end abuelo,
        case 
            when hijo != '' then ''
            else padre
        end padre,
        hijo,
        case
            when importe is null then 0
            else importe
        end importe
    from (
        select 
            min(id) id,
            abuelo,
            '' padre,
            '' hijo,
            sum(importe) importe
        from aux
        where abuelo = 'Programas'
        group by abuelo
        union all
        select 
            min(id) id,
            abuelo,
            padre,
            '' hijo,
            sum(importe) importe
        from aux
        where padre != ''
        group by abuelo,padre
        union all
        select * from aux
        order by id,abuelo,padre,hijo
    )t;\";

    set @query := concat(@tablaWith,@selectWith);
    
    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists conac_temporal;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_4(in anio int,in ver int)
begin
	set @tabla := 'programacion_presupuesto';
	set @corte := 'deleted_at is null';
	if (ver is not null) then 
		set @tabla := 'programacion_presupuesto_hist';
		set @corte := CONCAT('version = ',ver);
	end if;

	drop temporary table if exists aux_0;
	drop temporary table if exists aux_1;
                
	set @query := CONCAT('
	create temporary table aux_0
	select 
		po.clv_capitulo,
		po.capitulo,
		po.clv_concepto,
		po.concepto,
		sum(pp.total) total,
		sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
		sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
		sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre 
	from (
		select distinct
			concat(c1.clave,c2.clave) llave,
			c1.clave clv_capitulo,c1.descripcion capitulo,
			c2.clave clv_concepto,c2.descripcion concepto
		from clasificacion_economica ce
		join catalogo c1 on ce.capitulo_id = c1.id
		join catalogo c2 on ce.concepto_id = c2.id
		where ce.deleted_at is null
	) po
	left join ',@tabla,' pp on substring(pp.posicion_presupuestaria,1,2) = po.llave
	and pp.ejercicio = ',anio,' and pp.',@corte,'
	group by po.clv_capitulo,po.capitulo,po.clv_concepto,po.concepto
	order by clv_capitulo,clv_concepto;
	');

	prepare stmt  from @query;
	execute stmt;
	deallocate prepare stmt;

	create temporary table aux_1
	select
		clv_capitulo,
		capitulo,
		sum(total) total,
		sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
		sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
		sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
	from aux_0
	group by clv_capitulo,capitulo;
    
	select 
		case 
			when concepto != '' then ''
			else capitulo
		end capitulo,
		concepto partida_generica,
		case when total is null then 0 else total end total,
		case when enero is null then 0 else enero end enero,
		case when febrero is null then 0 else febrero end febrero,
		case when marzo is null then 0 else marzo end marzo,
		case when abril is null then 0 else abril end abril,
		case when mayo is null then 0 else mayo end mayo,
		case when junio is null then 0 else junio end junio,
		case when julio is null then 0 else julio end julio,
		case when agosto is null then 0 else agosto end agosto,
		case when septiembre is null then 0 else septiembre end septiembre,
		case when octubre is null then 0 else octubre end octubre,
		case when noviembre is null then 0 else noviembre end noviembre,
		case when diciembre is null then 0 else diciembre end diciembre
	from (
		select *
		from aux_0
		union all
		select 
			clv_capitulo,capitulo,
			'' clv_concepto,'' concepto,
			total,
			enero,febrero,marzo,abril,mayo,
			junio,julio,agosto,septiembre,
			octubre,noviembre,diciembre
		from aux_1
		order by clv_capitulo,clv_concepto
	)t;
    
	drop temporary table aux_0;
	drop temporary table aux_1;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_5(in anio int,in ver int)
begin
	set @tabla := 'programacion_presupuesto';
	set @corte := 'deleted_at is null';
	if (ver is not null) then 
		set @tabla := 'programacion_presupuesto_hist';
		set @corte := CONCAT('version = ',ver);
	end if;

	set @queri := CONCAT('
	select 
		capitulo,
		case
			when importe is null then 0 
			else importe 
		end importe
	from (
		select 
			t.clv_capitulo,
			t.capitulo,
			sum(pa.total) importe
		from (
			select distinct
				c1.clave clv_capitulo,
				c1.descripcion capitulo
			from clasificacion_economica ce
			join catalogo c1 on ce.capitulo_id = c1.id
			where ce.deleted_at is null
		)t
		left join ',@tabla,' pa
		on t.clv_capitulo = substring(pa.posicion_presupuestaria,1,1)
		and pa.',@corte,' and pa.ejercicio = ',anio,'
		group by t.clv_capitulo,t.capitulo
		order by clv_capitulo
	)t2;
	');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_6(in anio int,in ver int)
begin
	set @tabla := 'programacion_presupuesto';
	set @corte := 'deleted_at is null';
    
	if (ver is not null) then 
		set @tabla := 'programacion_presupuesto_hist';
		set @corte := CONCAT('version = ',ver);
	end if;

	set @query := concat('
	select 
		conceptos,
		importe
	from (
		select 
			pp.tipo_gasto clv_tipo_gasto,
			p.tipo_gasto conceptos,
			case 
				when sum(total) is null then 0
				else sum(total)
			end importe
		from ',@tabla,' pp 
		join (
			select distinct
				c1.clave clv_tipo_gasto,
				c1.descripcion tipo_gasto
			from clasificacion_economica ce
			join catalogo c1 on ce.tipo_gasto_id = c1.id
			where ce.deleted_at is null
		) p on pp.tipo_gasto = p.clv_tipo_gasto
		where pp.ejercicio = ',anio,' and pp.',@corte,'
		group by pp.tipo_gasto,p.tipo_gasto
		order by clv_tipo_gasto
	)t;
	');

	prepare stmt from @query;
	execute stmt;
	deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_1(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists epp_temporal;

    set @queri := concat('
    create temporary table epp_temporal
    select distinct 
        clv_upp,upp
    from ',@epp,' 
    where ejercicio = ',anio,' and ',@corte,'
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := CONCAT('
    with aux as (
        select 
            1 etiquetado,
            c.clv_upp,
            c.upp,
            pp.total
        from epp_temporal c 
        left join ',@tabla,' pp on c.clv_upp = pp.upp
        and pp.',@corte,' and pp.ejercicio = ',anio,' and pp.etiquetado = 1
        union all
        select 
            2 etiquetado,
            c.clv_upp,
            c.upp,
            pp.total
        from epp_temporal c 
        left join ',@tabla,' pp on c.clv_upp = pp.upp
        and pp.',@corte,' and pp.ejercicio = ',anio,' and pp.etiquetado = 2
    )
    select 
        case 
            when upp != \"\" then \"\"
            when etiquetado = 1 then \"Gasto No Etiquetado\"
            when etiquetado = 2 then \"Gasto Etiquetado\"
        end etiquetado,
        upp,
        case
            when importe is null then 0
            else importe
        end importe
    from (
        select 
            etiquetado,
            \"\" clv_upp,
            \"\" upp,
            sum(total) importe
        from aux
        group by etiquetado
        union all
        select 
            etiquetado,
            clv_upp,
            upp,
            sum(total) importe
        from aux
        group by etiquetado,clv_upp,upp
        order by etiquetado,clv_upp,upp
    )t;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists epp_temporal;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_2(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;

    set @query := concat(\"
    create temporary table aux_0
    select distinct
        clv_finalidad,finalidad,
        clv_funcion,funcion
    from \",@epp,\"
    where ejercicio = \",anio,\" and \",@corte,\"
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    create temporary table aux_1
    select 
        1 etiquetado,
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        sum(pp.total) importe
    from aux_0 a0 
    left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad 
    and pp.funcion = a0.clv_funcion and pp.etiquetado = 1
    and pp.ejercicio = ',anio,' and pp.',@corte,'
    group by etiquetado,a0.clv_finalidad,a0.finalidad,a0.clv_funcion,a0.funcion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    insert into aux_1
    select 
        2 etiquetado,
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        sum(pp.total) importe
    from aux_0 a0 
    left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad 
    and pp.funcion = a0.clv_funcion and pp.etiquetado = 2
    and pp.ejercicio = ',anio,' and pp.',@corte,'
    group by etiquetado,a0.clv_finalidad,a0.finalidad,a0.clv_funcion,a0.funcion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        etiquetado,
        clv_finalidad,
        finalidad,
        sum(importe) importe
    from aux_1
    group by etiquetado,clv_finalidad,finalidad;
    
    create temporary table aux_3
    select 
        etiquetado,
        sum(importe) importe
    from aux_2
    group by etiquetado;
    
    select 
        case 
            when finalidad != '' then ''
            when etiquetado = 1 then 'Gasto No Etiquetado'
            when etiquetado = 2 then 'Gasto Etiquetado'
        end etiquetado,
        case 
            when funcion != '' then ''
            else finalidad
        end finalidad,
        funcion,
        case
            when importe is null then 0
            else importe
        end importe
    from (
        select 
            etiquetado,
            '' clv_finalidad,'' finalidad,
            '' clv_funcion,'' funcion,
            importe
        from aux_3
        union all
        select 
            etiquetado,
            clv_finalidad,finalidad,
            '' clv_funcion,'' funcion,
            importe
        from aux_2
        union all
        select * from aux_1
        order by etiquetado,clv_finalidad,clv_funcion
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
    drop temporary table aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_3(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
            
    set @queri := concat(\"
    create temporary table aux_0
    select distinct
        clv_finalidad,finalidad,
        clv_funcion,funcion
    from \",@epp,\"
    where ejercicio = \",anio,\" and \",@corte,\"
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    create temporary table aux_1
    select 
        1 etiquetado,
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        sum(pp.total) importe
    from aux_0 a0 
    left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad 
    and pp.funcion = a0.clv_funcion and pp.etiquetado = 1
    and pp.ejercicio = ',anio,' and pp.',@corte,'
    group by etiquetado,a0.clv_finalidad,a0.finalidad,a0.clv_funcion,a0.funcion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    insert into aux_1
    select 
        2 etiquetado,
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        sum(pp.total) importe
    from aux_0 a0 
    left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad 
    and pp.funcion = a0.clv_funcion and pp.etiquetado = 2
    and pp.ejercicio = ',anio,' and pp.',@corte,'
    group by etiquetado,a0.clv_finalidad,a0.finalidad,a0.clv_funcion,a0.funcion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        etiquetado,
        clv_finalidad,
        finalidad,
        sum(importe) importe
    from aux_1
    group by etiquetado,clv_finalidad,finalidad;
    
    create temporary table aux_3
    select 
        etiquetado,
        sum(importe) importe
    from aux_2
    group by etiquetado;
    
    select 
        case 
            when finalidad != '' then ''
            when etiquetado = 1 then 'Gasto No Etiquetado'
            when etiquetado = 2 then 'Gasto Etiquetado'
        end etiquetado,
        case 
            when funcion != '' then ''
            else finalidad
        end finalidad,
        funcion,
        case
            when importe is null then 0
            else importe
        end importe
    from (
        select 
            etiquetado,
            '' clv_finalidad,'' finalidad,
            '' clv_funcion,'' funcion,
            importe
        from aux_3
        union all
        select 
            etiquetado,
            clv_finalidad,finalidad,
            '' clv_funcion,'' funcion,
            importe
        from aux_2
        union all
        select * from aux_1
        order by etiquetado,clv_finalidad,clv_funcion
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
    drop temporary table aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_4(in anio int,in ver int)
begin
	set @tabla := 'programacion_presupuesto';
	set @corte := 'deleted_at is null';
	if (ver is not null) then 
		set @tabla := 'programacion_presupuesto_hist';
		set @corte := CONCAT('version = ',ver);
	end if;

	drop temporary table if exists aux_0;
	drop temporary table if exists aux_1;
	drop temporary table if exists aux_2;

	set @query := CONCAT(\"
	create temporary table aux_0
	select 
		case 
			when pa.etiquetado = 1 then 'Gasto No Etiquetado'
			else 'Gasto Etiquetado'
		end etiquetado,
		pp.clv_capitulo,
		pp.capitulo,
		pp.clv_concepto,
		pp.concepto,
		sum(pa.total) importe
	from (
		select distinct
			c1.clave clv_capitulo,c1.descripcion capitulo,
			c2.clave clv_concepto,c2.descripcion concepto
		from clasificacion_economica ce
		join catalogo c1 on ce.capitulo_id = c1.id
		join catalogo c2 on ce.concepto_id = c2.id
		where ce.deleted_at is null
	) pp
	join \",@tabla,\" pa on pp.clv_capitulo = substring(pa.posicion_presupuestaria,1,1)
	and pp.clv_concepto = substring(pa.posicion_presupuestaria,2,1)
	and pa.ejercicio = \",anio,\" and pa.\",@corte,\"
	group by pa.etiquetado,pp.clv_capitulo,pp.capitulo,pp.clv_concepto,pp.concepto;
	\");

	prepare stmt  from @query;
	execute stmt;
	deallocate prepare stmt;

	create temporary table aux_1
	select 
		etiquetado,
		clv_capitulo,
		capitulo,
		sum(importe) importe
	from aux_0
	group by etiquetado,clv_capitulo,capitulo;
    
	create temporary table aux_2
	select 
		etiquetado,
		sum(importe) importe
	from aux_1
	group by etiquetado;
    
	select 
		case 
			when capitulo != '' then ''
			else etiquetado
		end etiquetado,
		case 
			when concepto != '' then ''
			else capitulo
		end capitulo,
		concepto,
		importe
	from (
		select *
		from aux_0 a0
		union all 
		select 
			etiquetado,
			clv_capitulo,capitulo,
			'' clv_concepto,'' concepto,
			importe 
		from aux_1 a1
		union all
		select 
			etiquetado,
			'' clv_capitulo,'' capitulo,
			'' clv_concepto,'' concepto,
			importe
		from aux_2
		order by etiquetado,clv_capitulo,clv_concepto
	)t;
    
	drop temporary table aux_0;
	drop temporary table aux_1;
	drop temporary table aux_2;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_5(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    drop temporary table if exists conac_temporal;

    set @queri := concat(\"
    create temporary table conac_temporal
    select 
        c.id,
        c1.clave,c1.descripcion,
        c2.clave clave_conac,c2.descripcion descripcion_conac
    from conac c 
    join catalogo c1 on c.padre_id = c1.id 
    left join catalogo c2 on c.tipologia_conac_id = c2.id
    where c.ejercicio = \",anio,\" and c.deleted_at is null;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @query := CONCAT(\"
        select 
            case 
                when abuelo != '' then ''
                when etiquetado = 1 then 'Gasto No Etiquetado'
                when etiquetado = 2 then 'Gasto Etiquetado'
            end etiquetado,
            case 
                when padre != '' then ''
                else abuelo
            end abuelo,
            case 
                when hijo != '' then ''
                else padre
            end padre,
            hijo,
            case 
                when importe is null then 0
                else importe
            end importe
        from (
            select 
                etiquetado,
                -1 id,
                '' abuelo,
                '' padre,
                '' clave,
                '' hijo,
                sum(total) importe
            from \",@tabla,\" pp
            where pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            group by etiquetado
            union all
            select 
                1 etiquetado,
                min(tc.id) id,
                tc.descripcion abuelo,
                '' padre,
                '' clave,
                '' hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave = pp.tipologia_conac
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 1
            where tc.clave is not null
            group by descripcion
            union all 
            select 
                1 etiquetado,
                min(tc.id) id,
                'Programas' abuelo,
                tc.descripcion padre,
                '' clave,
                '' hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac 
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 1
            where tc.clave_conac is not null
            group by tc.descripcion
            union all 
            select 
                2 etiquetado,
                min(tc.id) id,
                'Programas' abuelo,
                tc.descripcion padre,
                '' clave,
                '' hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac 
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 2
            where tc.clave_conac is not null
            group by tc.descripcion
            union all
            select 
                2 etiquetado,
                min(tc.id) id,
                tc.descripcion abuelo,
                '' padre,
                '' clave,
                '' hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave = pp.tipologia_conac
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 2
            where tc.clave is not null
            group by descripcion
            union all
            select 
                pp.etiquetado,
                0 id,
                'Programas' abuelo,
                '' padre,
                '' clave,
                '' hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            where tc.clave_conac is not null
            group by pp.etiquetado
            union all
            select 
                1 etiquetado,
                min(tc.id) id,
                'Programas' abuelo,
                tc.descripcion padre,
                tc.clave_conac clave,
                tc.descripcion_conac hijo,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac 
            and pp.etiquetado = 1 and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            where tc.clave_conac is not null
            group by descripcion,clave_conac,descripcion_conac
            union all 
            select 
                2 etiquetado,
                min(tc.id) id,
                'Programas' abuelo,
                tc.descripcion,
                tc.clave_conac,
                tc.descripcion_conac,
                sum(pp.total) importe
            from conac_temporal tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac
            and pp.etiquetado = 2 and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            where tc.clave_conac is not null
            group by descripcion,clave_conac,descripcion_conac
            order by etiquetado,id,abuelo,padre,clave,hijo
        )t;
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists conac_temporal;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_10(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @query := CONCAT('
        select 
            sector concepto,
            case
                when importe is null then 0 
                else importe 
            end importe
        from (
        select 
            sl.clv_sector,
            sl.sector,
            sum(pp.total) importe
        from sector_linea_accion sl
        left join ',@tabla,' pp 
        on sl.clv_linea_accion = pp.linea_accion
        and pp.ejercicio = ',anio,' and pp.',@corte,'
        where sl.deleted_at is null
        group by sl.clv_sector,sl.sector
        order by clv_sector)t;
    ');	

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_1(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'epp';
    set @catalogo := 'catalogo';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'epp_hist';
        set @catalogo := 'catalogo_hist';
        set @id := 'id_original';
    end if;

    DROP TEMPORARY table if exists aux_0;
    DROP TEMPORARY TABLE if exists aux_1;
    DROP TEMPORARY TABLE if exists aux_2;
    DROP TEMPORARY TABLE if exists aux_3;

    set @from := concat(\"
    select distinct
        clv_upp,upp,
        clv_subsecretaria,subsecretaria,
        clv_ur,ur
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null
    \");

    if(ver is not null) then
    set @from := concat(\"
    select distinct
        clv_upp,upp,
        clv_subsecretaria,subsecretaria,
        clv_ur,ur
    from v_epp_hist
    where ejercicio = \",anio,\" and version = \",ver,\"
    \");
    end if;
                    
    set @tablas := CONCAT(\"
    create temporary table aux_0
    with aux as (
        select 
            upp clv_upp,subsecretaria clv_subsecretaria,
            ur clv_ur,fondo_ramo clv_fondo,sum(total) importe
        from \",@tabla,\"
        where ejercicio = \",anio,\" and \",@corte,\"
        group by upp,subsecretaria,ur,fondo_ramo
    )
    select 
        concat(ve.clv_upp,' ',ve.upp) upp,
        ve.subsecretaria,
        ve.ur,
        case 
            when f.fuente_financiamiento is null then ''
            else f.fuente_financiamiento
        end fuente_financiamiento,
        case 
            when a.importe is null then 0
            else a.importe
        end importe
    from (\",@from,\") ve
    left join aux a on ve.clv_upp = a.clv_upp 
    and ve.clv_subsecretaria = a.clv_subsecretaria and ve.clv_ur = a.clv_ur
    left join (
		select distinct
			c1.descripcion fuente_financiamiento,
			c2.clave clv_fondo
		from fondo f 
		join catalogo c1 on f.fuente_financiamiento_id = c1.id
		join catalogo c2 on f.fondo_ramo_id = c2.id
		where f.deleted_at is null
    ) f on a.clv_fondo = f.clv_fondo;
    \");

    prepare stmt from @tablas;
    execute stmt;
    deallocate prepare stmt;

    CREATE TEMPORARY TABLE aux_1 AS 
    (SELECT upp,subsecretaria,ur,sum(importe) importe FROM aux_0 GROUP BY upp,subsecretaria,ur);

    CREATE TEMPORARY TABLE aux_2 AS 
    (SELECT upp,subsecretaria,sum(importe) importe FROM aux_1 GROUP BY upp,subsecretaria);

    CREATE TEMPORARY TABLE aux_3 AS 
    (SELECT upp,SUM(importe) importe FROM aux_2 GROUP BY upp);

    select 
        case 
            when subsecretaria != '' then ''
            else upp
        end upp,
        case 
            when ur != '' then ''
            else subsecretaria
        end subsecretaria,
        case 
            when fuente_financiamiento != '' then ''
            else ur
        end ur,
        fuente_financiamiento,
        importe
    from (
        select 
            upp,'' subsecretaria,'' ur,
            '' fuente_financiamiento,
            importe
        from aux_3
        union all
        select 
            upp,subsecretaria,'' ur,
            '' fuente_financiamiento,
            importe
        from aux_2
        union all
        select 
            upp,subsecretaria,ur,
            '' fuente_financiamiento,
            importe
        from aux_1
        union all
        select 
            upp,subsecretaria,ur,
            fuente_financiamiento,
            importe
        from aux_0
        where fuente_financiamiento != ''
        order by upp,subsecretaria,ur,
        fuente_financiamiento
    )t;

    DROP TEMPORARY TABLE aux_0;
    DROP TEMPORARY TABLE aux_1;
    DROP TEMPORARY TABLE aux_2;
    DROP TEMPORARY TABLE aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @cortePP := 'deleted_at is null';
    set @corte := 'deleted_at is null';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @cortePP := CONCAT('version = ',ver);
        set @corte := CONCAT('version = ',ver);
    end if;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists v_clasificacion_geografica;
   
    create temporary table v_clasificacion_geografica
    select 
		 cg.id,
		 c1.clave clv_entidad_federativa,c1.descripcion entidad_federativa,
		 c2.clave clv_region,c2.descripcion region,
		 c3.clave clv_municipio,c3.descripcion municipio,
		 c4.clave clv_localidad,c4.descripcion localidad
	 from clasificacion_geografica cg 
	 join catalogo c1 on cg.entidad_federativa_id = c1.id 
	 join catalogo c2 on cg.region_id = c2.id
	 join catalogo c3 on cg.municipio_id = c3.id
	 join catalogo c4 on cg.localidad_id = c4.id
	 where cg.deleted_at is null;

    set @queri := concat('
        create temporary table aux_0
        with aux as (
            select 
                region clv_region,
                municipio clv_municipio,
                localidad clv_localidad,
                upp clv_upp,
                sum(total) importe
            from ',@tabla,' pp
            where ejercicio = ',anio,' and ',@cortePP,'
            group by region,municipio,localidad,upp
        )
        select 
            concat(
                cg.clv_region,\" \",
                cg.region
            ) region,
            concat(
                cg.clv_municipio,\" \",
                cg.municipio
            ) municipio,
            concat(
                cg.clv_localidad,\" \",
                cg.localidad
            ) localidad,
            a.clv_upp,
            c.descripcion upp,
            a.importe
        from aux a
        left join v_clasificacion_geografica cg on a.clv_region = cg.clv_region
        and a.clv_municipio = cg.clv_municipio and a.clv_localidad = cg.clv_localidad
        left join catalogo c on a.clv_upp = c.clave and c.grupo_id = 6
        and c.ejercicio = ',anio,' and c.deleted_at is null
        order by cg.clv_region,cg.clv_municipio,cg.clv_localidad,a.clv_upp;
    ');
    
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_1
    select 
        region,municipio,localidad,
        sum(importe) importe
    from aux_0
    group by region,municipio,localidad;
    
    select 
        case 
            when clv_upp != '' then ''
            else region
        end region,
        case 
            when clv_upp != '' then ''
            else municipio
        end municipio,
        case 
            when clv_upp != '' then ''
            else localidad
        end localidad,
        clv_upp,upp,
        importe
    from (
        select *
        from aux_0
        union all 
        select 
            region,municipio,localidad,
            '' clv_upp,'' upp,
            importe
        from aux_1
        order by region,municipio,localidad,clv_upp,upp
    )t;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists v_clasificacion_geografica;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_3(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @queri := CONCAT('
        select 
            c.clave clv_eje,
            c.descripcion eje,
            case
                when sum(pp.total) is null then 0
                else sum(pp.total)
            end importe
        from catalogo c
        left join ',@tabla,' pp on c.clave = pp.eje 
        and pp.ejercicio = ',anio,' and pp.',@corte,'
        where c.ejercicio = ',anio,' and c.grupo_id = 12 and c.deleted_at is null
        group by c.clave,c.descripcion;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_4(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @epp := 'v_epp_hist';
        set @corte := CONCAT('version = ',ver);
    end if;

    set @query := CONCAT('
        with aux as (
        	select distinct clv_programa,programa from ',@epp,'
	        where ejercicio = ',anio,' and ',@corte,'
        )
        select 
            t.clv_programa,
            t.programa,
            case 
                when sum(pp.total) is null then 0
                else sum(pp.total)
            end importe
        from aux t
        left join ',@tabla,' pp on t.clv_programa = pp.programa_presupuestario
        and pp.ejercicio = ',anio,' and pp.',@corte,'
        group by clv_programa,programa
        order by clv_programa;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_6(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;

    set @queri := concat('
    create temporary table aux_0
    select distinct
        clv_finalidad,finalidad,
        clv_funcion,funcion,
        clv_subfuncion,subfuncion
    from ',@epp,'
    where ejercicio = ',anio,' and ',@corte,';
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    create temporary table aux_1
    select 
        a0.clv_finalidad,
        a0.finalidad,
        a0.clv_funcion,
        a0.funcion,
        a0.clv_subfuncion,
        a0.subfuncion,
        sum(pp.total) importe
    from aux_0 a0  
    left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad
    and pp.funcion = a0.clv_funcion and pp.subfuncion = a0.clv_subfuncion
    and pp.ejercicio = ',anio,' and pp.',@corte,'
    group by a0.clv_finalidad,a0.finalidad,a0.clv_funcion,
    a0.funcion,a0.clv_subfuncion,a0.subfuncion;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        clv_finalidad,
        finalidad,
        clv_funcion,
        funcion,
        sum(importe) importe
    from aux_1
    group by clv_finalidad,finalidad,
    clv_funcion,funcion;
    
    create temporary table aux_3
    select 
        clv_finalidad,
        finalidad,
        sum(importe) importe
    from aux_1
    group by clv_finalidad,finalidad;
    
    select 
        case 
            when funcion != '' then ''
            else finalidad
        end finalidad,
        case 
            when subfuncion != '' then ''
            else funcion
        end funcion,
        subfuncion,
        case
            when importe is null then 0
            else importe
        end importe
    from (
        select 
            clv_finalidad,finalidad,
            '' clv_funcion,'' funcion,
            '' clv_subfuncion,'' subfuncion,
            importe
        from aux_3
        union all
        select 
            clv_finalidad,finalidad,
            clv_funcion,funcion,
            '' clv_subfuncion,'' subfuncion,
            importe
        from aux_2
        union all
        select * from aux_1
        order by clv_finalidad,clv_funcion,clv_subfuncion
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
    drop temporary table aux_3;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_7(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @cortePP := 'deleted_at is null';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @cortePP := CONCAT('version = ',ver);
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
    drop temporary table if exists aux_4;
    drop temporary table if exists posicion_presupuestaria_t;

    set @from := concat('
    select distinct
        clv_upp,upp,clv_ur,ur,
        clv_finalidad,finalidad,
        clv_funcion,funcion,
        clv_subfuncion,subfuncion
    from v_epp
    where ejercicio = ',anio,' and deleted_at is null
    ');

    if(ver is not null) then
    set @from := concat('
    select distinct
        clv_upp,upp,clv_ur,ur,
        clv_finalidad,finalidad,
        clv_funcion,funcion,
        clv_subfuncion,subfuncion
    from v_epp_hist
    where ejercicio = ',anio,' and version = ',ver,'
    ');
    end if;

    set @query := concat('
    create temporary table aux_0
    select 
        ve.clv_upp,ve.upp,
        ve.clv_ur,ve.ur,
        ve.clv_finalidad,ve.finalidad,
        ve.clv_funcion,ve.funcion,
        ve.clv_subfuncion,ve.subfuncion,
        a.importe
    from (
        select
            upp,
            ur,
            finalidad,
            funcion,
            subfuncion,
            sum(pp.total) importe
        from ',@tabla,' pp
        where ejercicio = ',anio,' and pp.',@cortePP,'
        group by upp,ur,finalidad,funcion,subfuncion
    ) a
    left join (',@from,') ve on a.upp = ve.clv_upp and a.ur = ve.clv_ur
    and a.finalidad = ve.clv_finalidad and a.funcion = ve.clv_funcion
    and a.subfuncion = ve.clv_subfuncion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
   
	create temporary table posicion_presupuestaria_t
    select distinct
		c1.clave clv_capitulo,c1.descripcion capitulo,
		c2.clave clv_concepto,c2.descripcion concepto,
		c3.clave clv_partida_generica,c3.descripcion partida_generica,
		c4.clave clv_partida_especifica,c4.descripcion partida_especifica,
		c5.clave clv_tipo_gasto,c5.descripcion tipo_gasto
	 from clasificacion_economica ce
	 join catalogo c1 on ce.capitulo_id = c1.id
	 join catalogo c2 on ce.concepto_id = c2.id
	 join catalogo c3 on ce.partida_generica_id = c3.id
	 join catalogo c4 on ce.partida_especifica_id = c4.id
	 join catalogo c5 on ce.tipo_gasto_id = c5.id
	 where ce.deleted_at is null;

    set @query := concat('
    create temporary table aux_1
    select 
        pa.upp clv_upp,pa.ur clv_ur,
        pa.finalidad clv_finalidad,
        pa.funcion clv_funcion,
        pa.subfuncion clv_subfuncion,
        pa.posicion_presupuestaria clv_partida,
        pp.partida_especifica partida,
        sum(pa.total) importe
    from ',@tabla,' pa
    join posicion_presupuestaria_t pp on pa.posicion_presupuestaria =
    concat(pp.clv_capitulo,pp.clv_concepto,pp.clv_partida_generica,
    pp.clv_partida_especifica)
    where pa.ejercicio = ',anio,' and pa.',@cortePP,'
    group by pa.upp,pa.ur,pa.finalidad,pa.funcion,
    pa.subfuncion,pa.posicion_presupuestaria,pp.partida_especifica;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,
        clv_funcion,funcion,sum(importe) importe
    from aux_0
    group by clv_upp,upp,clv_ur,ur,clv_finalidad,
    finalidad,clv_funcion,funcion;
    
    create temporary table aux_3
    select 
        clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,
        sum(importe) importe
    from aux_2
    group by clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad;
    
    create temporary table aux_4
    select 
        clv_upp,upp,sum(importe) importe
    from aux_3
    group by clv_upp,upp;
    
    select 
        case 
            when clv_ur != '' then ''
            else concat(clv_upp,' ',upp)
        end upp,
        case 
            when clv_funcion != '' then ''
            else concat(clv_ur,' ',ur)
        end ur,
        case 
            when funcion != '' then ''
            else finalidad
        end finalidad,
        case 
            when subfuncion != '' then ''
            else funcion
        end funcion,
        case 
            when partida != '' then ''
            else subfuncion
        end subfuncion,
        concat(clv_partida,' ',partida) partida,
        importe
    from (
        select 
            clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,clv_funcion,funcion,
            clv_subfuncion,subfuncion,''clv_partida,'' partida,importe
        from aux_0
        union all 
        select 
            clv_upp,'' upp,clv_ur,'' ,clv_finalidad,'' finalidad,clv_funcion,
            '' funcion,clv_subfuncion,'' subfuncion,clv_partida,partida,importe
        from aux_1
        union all 
        select 
            clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,clv_funcion,funcion,
            '' clv_subfuncion,'' subfuncion,'' clv_partida,'' partida,importe
        from aux_2
        union all 
        select 
            clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,'' clv_funcion,'' funcion,
            '' clv_subfuncion,'' subfuncion,'' clv_partida,'' partida,importe
        from aux_3
        union all 
        select 
            clv_upp,upp,'' clv_ur,'' ur,'' clv_finalidad,'' finalidad,'' clv_funcion,
            '' funcion,'' clv_subfuncion,'' subfuncion,'' clv_partida,'' partida,importe
        from aux_4
        order by clv_upp,clv_ur,clv_finalidad,clv_funcion,clv_subfuncion,clv_partida
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
    drop temporary table aux_3;
    drop temporary table aux_4;
    drop temporary table if exists posicion_presupuestaria_t;
end;");
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_8(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    set @from := concat(\"
    select distinct
        clv_upp,upp,clv_programa,programa
    from \",@epp,\"
    where ejercicio = \",anio,\" and \",@corte,\"
    \");

    set @query := concat(\"
    with aux as (
        select 
            a1.clv_upp,a1.upp,
            a1.clv_programa,a1.programa,
            a0.clv_proyecto_obra,
            po.descripcion proyecto_obra,
            a0.importe
        from (
            select 
                pp.upp clv_upp,
                pp.programa_presupuestario clv_programa,
                pp.proyecto_obra clv_proyecto_obra,
                sum(pp.total) importe
            from \",@tabla,\" pp
            where pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            group by upp,programa_presupuestario,proyecto_obra
        ) a0
        left join (\",@from,\") a1 on a0.clv_upp = a1.clv_upp and a0.clv_programa = a1.clv_programa
        left join catalogo po on a0.clv_proyecto_obra = po.clave and po.grupo_id = 39
        and po.deleted_at is null and po.ejercicio = \",anio,\"
        order by clv_upp,clv_programa,clv_proyecto_obra
    )
    select 
        case 
            when clv_programa != '' then ''
            else clv_upp
        end clv_upp,
        case 
            when clv_programa != '' then ''
            else upp
        end upp,
        clv_programa,programa,
        clv_proyecto_obra,proyecto_obra,
        importe
    from (
        select * from aux
        union all
        select 
            clv_upp,upp,'' clv_programa,'' programa,
            '' clv_proyecto_obra,'' proyecto_obra,importe
        from (
            select clv_upp,upp,sum(importe) importe 
            from aux group by clv_upp,upp
        )t
        order by clv_upp,clv_programa,clv_proyecto_obra
    )tabla;
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_III;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2025;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_10;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_8;");
    }
};
