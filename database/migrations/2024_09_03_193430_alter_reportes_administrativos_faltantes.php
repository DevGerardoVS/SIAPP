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
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");

        DB::unprepared("CREATE PROCEDURE reporte_resumen_por_capitulo_y_partida(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
        set @id := 'id_original';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;

    create temporary table aux_0
    select 
        c1.clave clv_capitulo,
        c1.descripcion capitulo,
        concat(
            c1.clave,c2.clave,c3.clave,c4.clave
        ) clv_partida,
        c3.descripcion partida
    from clasificacion_economica ce
    join catalogo c1 on ce.capitulo_id = c1.id
    join catalogo c2 on ce.concepto_id = c2.id
    join catalogo c3 on ce.partida_generica_id = c3.id
    join catalogo c4 on ce.partida_especifica_id = c4.id
    where ce.deleted_at is null;
    
    set @query := concat(\"
    create temporary table aux_1
    with aux as(
        select 
            substring(pp.posicion_presupuestaria,1,1) clv_capitulo, 
            pp.posicion_presupuestaria clv_partida,
            sum(pp.total) importe
        from \",@tabla,\" pp
        where pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        group by posicion_presupuestaria
    )
    select 
        concat(
            a0.clv_capitulo,'000 ',
            a0.capitulo
        ) capitulo,
        concat(
            a0.clv_partida,' ',
            a0.partida
        ) partida,
        a.importe
    from aux a
    left join aux_0 a0 on a.clv_capitulo = a0.clv_capitulo
    and a.clv_partida = a0.clv_partida;
    \");
    
    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        capitulo,sum(importe) importe
    from aux_1
    group by capitulo;
    
    select 
        case 
            when partida != '' then ''
            else capitulo
        end capitulo,
        partida,
        importe
    from (
        select * from aux_1
        union all
        select capitulo,'' partida,importe from aux_2
        order by capitulo,partida
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
end;");

        DB::unprepared("CREATE PROCEDURE proyecto_avance_general(in anio int,in ver int)
begin
    set @tabla := 'programacion_presupuesto';
	set @cortePP := 'deleted_at is null';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @cortePP := concat('version = ',ver);
		if(ver > 0) then
        	set @epp := 'v_epp_hist';
			set @corte := concat('version = ',ver);
		end if;
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    
    set @query := concat('
    create temporary table aux_0
    select 
        t1.clv_upp,
        t1.clv_fondo,
        t2.clv_capitulo,
        t1.presupuesto monto_anual,
        t2.importe calendarizado
    from (
        select 
            tf.clv_upp,
            tf.clv_fondo,
            sum(tf.presupuesto) presupuesto
        from techos_financieros tf
        where tf.ejercicio = ',anio,' and tf.deleted_at is null
        group by clv_upp,clv_fondo
    )t1
    left join (
        select 
            clv_upp,clv_fondo,clv_capitulo,
            sum(total) importe
        from (
            select 
                upp clv_upp,
                fondo_ramo clv_fondo,
                substr(posicion_presupuestaria,1,1) clv_capitulo,
                total
            from ',@tabla,' pa
            where pa.ejercicio = ',anio,' and pa.',@cortePP,'
        )t
        group by clv_upp,clv_fondo,clv_capitulo
    )t2 on t1.clv_upp = t2.clv_upp and t1.clv_fondo = t2.clv_fondo;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
    set @query := concat('
    create temporary table aux_1
    select 
        ve.clv_upp,ve.upp,
        c.clave clv_fondo,c.descripcion fondo,
        min(monto_anual) monto_anual,
        sum(calendarizado) calendarizado
    from aux_0 a0
    left join (
        select distinct clv_upp,upp from ',@epp,'
        where ejercicio = ',anio,' and ',@corte,'
    ) ve on a0.clv_upp = ve.clv_upp
    left join catalogo c on a0.clv_fondo = c.clave and 
    c.ejercicio = ',anio,' and c.deleted_at is null and c.grupo_id = 37
    group by ve.clv_upp,ve.upp,c.clave,c.descripcion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
    create temporary table aux_2
    select 
        clv_upp,upp,
        sum(monto_anual) monto_anual,
        sum(calendarizado) calendarizado
    from aux_1
    group by clv_upp,upp;
    
    with aux as (
        select 
            case 
                when clv_fondo != '' then ''
                else clv_upp
            end clv_upp,
            case 
                when clv_fondo != '' then ''
                else t.upp
            end upp,
            case 
                when t.clv_capitulo != '' then ''
                else clv_fondo
            end clv_fondo_ramo,
            case 
                when t.clv_capitulo != '' then ''
                else fondo
            end fondo_ramo,
            t.clv_capitulo,
            case 
                when pp.capitulo is null then ''
                else pp.capitulo
            end capitulo,
            monto_anual,
            calendarizado,
            es.status
        from (
            select 
                clv_upp,upp,
                '' clv_fondo,'' fondo,
                '' clv_capitulo,
                monto_anual,calendarizado
            from aux_2
            union all
            select 
                clv_upp,upp,clv_fondo,fondo,
                '' clv_capitulo,
                monto_anual,calendarizado
            from aux_1 a1
            union all
            select 
                clv_upp,'' upp,
                clv_fondo,'' fondo,
                clv_capitulo,
                calendarizado monto_anual,
                calendarizado
            from aux_0 a0
            order by clv_upp,clv_fondo,clv_capitulo
        )t
        left join (
            select distinct 
                clave clv_capitulo,descripcion capitulo
            from catalogo
            where deleted_at is null and grupo_id = 29
        ) pp on t.clv_capitulo = pp.clv_capitulo
        left join (
            select 
                upp,
                case
                    when max(estado) = 0 then 'guardado'
                    when max(estado) = 1 then 'confirmado'
                    when max(estado) is null then 'sin registrar'
                end status
            from programacion_presupuesto pp
            where pp.deleted_at is null and pp.ejercicio = 2024
            and pp.tipo = 'operativo'
            group by upp
        ) es on t.clv_upp = es.upp
    )
    select 
        clv_upp,upp,clv_fondo_ramo,fondo_ramo,
        clv_capitulo,capitulo,monto_anual,calendarizado,
        (monto_anual-calendarizado) disponible,
        (calendarizado/monto_anual)*100 avance,
        case
            when clv_upp = '' then ''
            else status
        end estatus
    from aux;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
end;");

        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int,in ver int,in uppC varchar(3),in tipo varchar(9))
begin
    set @tabla := 'programacion_presupuesto';
	set @cortePP := 'deleted_at is null';
    set @corte := 'deleted_at is null';
    set @epp := 'v_epp';
    set @upp := '';
    set @tipo := '';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @cortePP := concat('version = ',ver);
		if(ver > 0) then
        	set @epp := 'v_epp_hist';
			set @corte := concat('version = ',ver);
		end if;
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
    where pp.ejercicio = ',anio,' and pp.',@cortePP,' ',@upp,' ',@tipo,';
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
end;");

        DB::unprepared("CREATE PROCEDURE proyecto_calendario_actividades(in anio int,in upp varchar(3),in ver int,in tipo varchar(9))
begin
    set @upp := '';
    set @corte := 'deleted_at is null';
    set @tabla := 'metas';
    set @actividades := 'mml_actividades';
    set @mir := 'mml_mir';
    set @id := 'id';
    set @tipo := '';
    set @epp := 'v_epp';

    if(upp is not null) then set @upp := concat(\"where clv_upp = '\",upp,\"'\"); end if;
    if(tipo is not null) then set @tipo := concat('and m.tipo_meta = \"',tipo,'\"'); end if;
    if(ver > 0) then
        set @mir := 'mml_mir_hist';
        set @id := 'id_original';
        set @tabla := 'metas_hist';
        set @actividades := 'mml_actividades_hist';
        set @corte := concat('version = ',ver);
        set @epp := 'v_epp_hist';
    end if;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @query := concat(\"
    create temporary table aux_0
    select 
        ma.clv_upp,
        substring(ma.entidad_ejecutora,5,2) clv_ur,
        substring(ma.area_funcional,9,2) clv_programa,
        substring(ma.area_funcional,11,3) clv_subprograma,
        substring(ma.area_funcional,14,3) clv_proyecto,
        m.clv_fondo,
        case
            when c.descripcion is not null then c.descripcion
            else ma.nombre 
        end actividad,
        m.cantidad_beneficiarios,
        \",if(ver > 0,\"m.beneficiario,\",\"b.beneficiario,\"),\"
		\",if(ver > 0,\"m.unidad_medida,\",\"um.unidad_medida,\"),\"
        m.tipo,
        case 
            when m.tipo = 'Acumulativa' 
                then (enero+febrero+marzo+abril+mayo+junio+julio+agosto+septiembre+octubre+noviembre+diciembre)
            when m.tipo = 'Continua' then enero 
            when m.tipo = 'Especial' 
                then greatest(enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre)
        end meta_anual,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,
        octubre,noviembre,diciembre
    from \",@tabla,\" m \",if(ver > 0,\"\",\"join beneficiarios b on m.beneficiario_id = b.id\"),\" \",if(ver > 0,\"\",\"join unidades_medida um on m.unidad_medida_id = um.id\"),\"
    join \",@actividades,\" ma on m.actividad_id = ma.\",@id,\" \",if(ver > 0,\"\",\"join unidades_medida u2 on m.unidad_medida_id = u2.id\"),\"
    left join catalogo c on c.clave = substring(ma.area_funcional,11,3)
    and c.deleted_at is null and c.grupo_id = 20
    where mir_id is null and m.ejercicio = \",anio,\" and m.deleted_at is null \",@tipo,\"
    union all 
    select 
        mm.clv_upp,
        mm.clv_ur,
        mm.clv_pp clv_programa,
        substring(mm.area_funcional,11,3) clv_subprograma,
        substring(mm.area_funcional,14,3) clv_proyecto,
        m.clv_fondo,
        mm.objetivo actividad,
        m.cantidad_beneficiarios,
        \",if(ver > 0,\"m.beneficiario,\",\"b.beneficiario,\"),\"
		\",if(ver > 0,\"m.unidad_medida,\",\"um.unidad_medida,\"),\"
        m.tipo,
        case 
            when m.tipo = 'Acumulativa' 
                then (enero+febrero+marzo+abril+mayo+junio+julio+agosto+septiembre+octubre+noviembre+diciembre)
            when m.tipo = 'Continua' then enero 
            when m.tipo = 'Especial' 
                then greatest(enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre)
        end meta_anual,
        enero,febrero,marzo,abril,mayo,
        junio,julio,agosto,septiembre,
        octubre,noviembre,diciembre
    from \",@tabla,\" m \",if(ver > 0,\"\",\"join beneficiarios b on m.beneficiario_id = b.id\"),\" \",if(ver > 0,\"\",\"join unidades_medida um on m.unidad_medida_id = um.id\"),\"
    join \",@mir,\" mm on m.mir_id = mm.\",@id,\"
    where m.mir_id is not null \",@tipo,\" and m.ejercicio = \",anio,\" and m.\",@corte,\";
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
    set @query := concat('
    create temporary table aux_1
    select distinct
        c.upp,c.clv_upp,clv_ur,clv_programa,clv_subprograma,
        clv_proyecto,clv_fondo
    from aux_0 a0
    left join (
        select distinct clv_upp,upp from ',@epp,'
        where ejercicio = ',anio,' and ',@corte,'
    ) c on a0.clv_upp = c.clv_upp;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
    set @query := concat(\"
    select 
        upp,
        case 
            when actividad != '' then ''
            else clv_upp
        end clv_upp,
        case 
            when actividad != '' then ''
            else clv_ur
        end clv_ur,
        case 
            when actividad != '' then ''
            else clv_programa
        end clv_programa,
        case 
            when actividad != '' then ''
            else clv_subprograma
        end clv_subprograma,
        case 
            when actividad != '' then ''
            else clv_proyecto
        end clv_proyecto,
        case 
            when actividad != '' then ''
            else clv_fondo
        end clv_fondo,
        actividad,cantidad_beneficiarios,beneficiario,unidad_medida,
        tipo,meta_anual,enero,febrero,marzo,abril,mayo,junio,julio,
        agosto,septiembre,octubre,noviembre,diciembre,'' capturista
    from (
        select '' upp,a0.* from aux_0 a0
        union all
        select 
            upp,clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,clv_fondo,
            '' actividad,0 cantidad_beneficiarios,'' beneficiario,'' unidad_medida,'' tipo,0 meta_anual,
            0 enero,0 febrero,0 marzo,0 abril,0 mayo,0 junio,0 julio,0 agosto,0 septiembre,0 octubre,0 noviembre,0 diciembre
        from aux_1
        order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,clv_fondo,actividad
    )t \",@upp,\";
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table aux_0;
    drop temporary table aux_1;
end;");

        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int,in ver int)
BEGIN
    set @corte := 'deleted_at is null';
    set @cortePP := 'deleted_at is null';
    set @tabla := 'programacion_presupuesto';
    set @actividades := 'mml_actividades';
    set @epp := 'v_epp';
    set @metas := 'metas';
    set @mir := 'mml_mir';
    set @id := 'id';

    if (ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @cortePP := concat('version = ',ver);
        if(ver > 0) then
            set @epp := 'v_epp_hist';
		    set @actividades := 'mml_actividades_hist';
            set @corte := concat('version = ',ver);
		    set @metas := 'metas_hist';
		    set @mir := 'mml_mir_hist';
		    set @id := 'id_original';
        end if;
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
        where ejercicio = ',anio,' and ',@cortePP,'
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
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
    }
};
