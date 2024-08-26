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
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");

        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int,in ver int)
BEGIN
    set @corte := 'deleted_at is null';
    set @tabla := 'programacion_presupuesto';
    set @actividades := 'mml_actividades';
    set @epp := 'v_epp';
    set @metas := 'metas';
    set @mir := 'mml_mir';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
        set @epp := 'v_epp_hist';
		  set @actividades := 'mml_actividades_hist';
		  set @metas := 'metas_hist';
		  set @mir := 'mml_mir_hist';
		  set @id := 'id_original';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @queri := concat('
    create temporary table aux_0
    select 
        upp clv_upp,count(area) claves,
        case
            when estado = 1 then \"Confirmado\"
            else \"Registrado\"
        end estatus
    from (
        select distinct
            upp,
            concat(
                ur,finalidad,funcion,subfuncion,eje,linea_accion,
                programa_sectorial,tipologia_conac,programa_presupuestario,
                subprograma_presupuestario,proyecto_presupuestario,fondo_ramo
            ) area,
            pp.estado
        from ',@tabla,' pp
        where ejercicio = ',anio,' and ',@corte,'
    )t
    group by upp,estado;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table aux_1
    with aux as (
        select distinct
            clv_upp,claves mir,estatus
        from (
            select mm.clv_upp,concat(mm.clv_ur,mm.area_funcional,m.clv_fondo) claves,m.estatus
            from \",@metas,\" m
            join \",@mir,\" mm on m.mir_id = mm.\",@id,\"
            where m.ejercicio = \",anio,\" and m.\",@corte,\"
            union all 
            select ma.clv_upp,concat(substr(ma.entidad_ejecutora,5,2),ma.area_funcional,m.clv_fondo) claves,m.estatus
            from \",@metas,\" m
            join \",@actividades,\" ma on m.actividad_id = ma.\",@id,\"
            where m.ejercicio = \",anio,\" and m.\",@corte,\"
        )t
    )
    select
        clv_upp,COUNT(mir) mir,
        case
            when estatus = 1 then 'Confirmado'
            else 'Registrado'
        end estatus
    from aux
    group by clv_upp,estatus;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @epp := concat(\"
    select distinct
        clv_upp,upp 
    from \",@epp,\" 
    where ejercicio = \",anio,\" and \",@corte,\"
    \");

    set @queri := concat(\"
    with aux as (
        select 
            ve.clv_upp,ve.upp,
            case 
                when a0.claves is null then 0
                else a0.claves
            end claves,
            case 
                when a1.mir is null then 0 
                else a1.mir
            end mir,
            case 
                when a0.estatus is null then 'Sin Registrar'
                else a0.estatus
            end estatus_claves,
            case 
                when a1.estatus is null then 'Sin Registrar'
                else a1.estatus
            end estatus_mir
        from (\",@epp,\") ve
        left join aux_0 a0 on ve.clv_upp = a0.clv_upp
        left join aux_1 a1 on ve.clv_upp = a1.clv_upp
    )
    select 
        clv_upp,upp,claves,mir,
        case 
            when claves = 0 then 0
            else round((mir/claves)*100)
        end avance,
        estatus_claves,estatus_mir
    from aux a;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
END;");

        DB::unprepared("CREATE PROCEDURE calendario_fondo_mensual(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('version = ',ver);
    end if;
                
    set @query := CONCAT('
    with aux as (
        select
            f.id,
            c1.clave clv_etiquetado,c1.descripcion etiquetado,
            c2.clave clv_fuente_financiamiento,c2.descripcion fuente_financiamiento,
            c3.clave clv_ramo,c3.descripcion ramo,
            c4.clave clv_fondo_ramo,c4.descripcion fondo_ramo,
            c5.clave clv_capital,c5.descripcion capital
        from fondo f
        join catalogo c1 on f.etiquetado_id = c1.id
        join catalogo c2 on f.fuente_financiamiento_id = c2.id
        join catalogo c3 on f.ramo_id = c3.id
        join catalogo c4 on f.fondo_ramo_id = c4.id
        join catalogo c5 on f.capital_id = c5.id
        where f.deleted_at is null
    )
    select 
        ramo,
        fondo_ramo,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,
        octubre,noviembre,diciembre,
        importe_total
    from (
        select 
            f.clv_ramo,
            f.ramo,
            f.clv_fondo_ramo,
            f.fondo_ramo,
            sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
            sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
            sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre,
            sum(total) importe_total
        from ',@tabla,' pp
        join aux f on pp.ramo = f.clv_ramo
        and pp.fondo_ramo = f.clv_fondo_ramo
        where pp.ejercicio = ',anio,' and pp.',@corte,'
        group by f.clv_ramo,f.ramo,f.clv_fondo_ramo,f.fondo_ramo
        order by clv_ramo,clv_fondo_ramo
    )t;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
END;");

        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int,in ver int,in uppC varchar(3),in tipo varchar(9))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    set @upp := '';
    set @tipo := '';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;

    if (uppC is not null) then set @upp := CONCAT('and pp.upp = \"',uppC,'\"'); end if;
    if (tipo is not null) then set @tipo := concat('and pp.tipo = \"',tipo,'\"'); end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @query := CONCAT('      
    create temporary table aux_0
    select 
        2 orden,
        ve.clv_upp,
        concat(
            ve.clv_upp,\" \",
            ve.upp
        ) upp,
        concat(
            pp.clasificacion_administrativa,
            \"-\",pp.entidad_federativa,
            \"-\",pp.region,
            \"-\",pp.municipio,
            \"-\",pp.localidad,
            \"-\",pp.upp,
            \"-\",pp.subsecretaria,
            \"-\",pp.ur,
            \"-\",pp.finalidad,
            \"-\",pp.funcion,
            \"-\",pp.subfuncion,
            \"-\",pp.eje,
            \"-\",pp.linea_accion,
            \"-\",pp.programa_sectorial,
            \"-\",pp.tipologia_conac,
            \"-\",pp.programa_presupuestario,
            \"-\",pp.subprograma_presupuestario,
            \"-\",pp.proyecto_presupuestario,
            \"-\",pp.periodo_presupuestal,
            \"-\",pp.posicion_presupuestaria,
            \"-\",pp.tipo_gasto,
            \"-\",pp.anio,
            \"-\",pp.etiquetado,
            \"-\",pp.fuente_financiamiento,
            \"-\",pp.ramo,
            \"-\",pp.fondo_ramo,
            \"-\",pp.capital,
            \"-\",pp.proyecto_obra
        ) clave,
        pp.total monto_anual,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,
        octubre,noviembre,diciembre
    from ',@tabla,' pp
    join (
		select distinct 
			clv_upp,upp
		from ',@epp,'
		where ejercicio = ',anio,' and ',@corte,'
	) ve on ve.clv_upp = pp.upp
    where pp.ejercicio = ',anio,' and pp.',@corte,' ',@upp,' ',@tipo,';
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_1
    select 
        t.orden,t.upp,t.upp clave,t.monto_anual,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,
        octubre,noviembre,diciembre,
        case 
            when cec.capturista is null then ''
            else cec.capturista
        end capturista
    from (
        select 
            1 orden,
            clv_upp,
            upp,
            sum(monto_anual) monto_anual,
            sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
            sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
            sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
        from aux_0
        group by clv_upp,upp
    )t
    left join cierre_ejercicio_claves cec on t.clv_upp = cec.clv_upp 
    and cec.deleted_at is null and cec.ejercicio = anio;

    select 
        orden,upp,clave,monto_anual,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,octubre,
        noviembre,diciembre,'' capturista
    from aux_0
    union all
    select * from aux_1
    order by upp,orden;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
END;");

        DB::unprepared("CREATE PROCEDURE seguimiento_totales(in anio int,in mes_n int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in subprograma_v varchar(3),in proyecto_v varchar(3))
begin
    drop temporary table if exists catalogo_upp_ur;
    drop temporary table if exists t_proyecto;
    drop temporary table if exists t_subprograma;
    drop temporary table if exists t_programa;
    drop temporary table if exists t_ur;
    drop temporary table if exists t_upp;
    
    create temporary table catalogo_upp_ur
    select distinct
        clv_upp,upp,clv_ur,ur
    from v_epp
    where ejercicio = 2024 and deleted_at is null;

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
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");
    }
};
