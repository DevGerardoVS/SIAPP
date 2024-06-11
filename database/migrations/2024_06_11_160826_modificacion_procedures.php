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
        DB::unprepared("DROP PROCEDURE if EXISTS avance_etapas;");
        DB::unprepared("DROP PROCEDURE if EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE if EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE if EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE if EXISTS conceptos_clave;");
        DB::unprepared("DROP PROCEDURE if EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE if EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_III;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_6;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_10;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_8;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE if EXISTS sapp_reporte_calendario;");

        DB::unprepared("CREATE PROCEDURE avance_etapas(in anio int, in upp varchar(3), in programa varchar(2), in lim_i int, in lim_s int)
begin
    set @programa := '';
    set @upp := '';
    set @lim_i := 0;
    set @lim_s := 100;
    if (programa is not null) then set @programa := CONCAT('and clv_pp = \"',programa,'\"'); end if;
    if (upp is not null) then set @upp := CONCAT(' and clv_upp = \"',upp,'\"'); end if;
    if (lim_i is not null) then set @lim_i := lim_i; end if;
    if (lim_s is not null) then set @lim_s := lim_s; end if;

    set @lim_upp := CONCAT('(select clv_upp
    from (
        select 
            clv_upp,
            round((sum(etapa_0+etapa_1+etapa_2+etapa_3
            +etapa_4+etapa_5)/(count(clv_pp)*6))*100) avance
        from mml_avance_etapas_pp maep
        where ejercicio = ',anio,' and deleted_at is null
        group by clv_upp
    )t where avance between ',@lim_i,' and ',@lim_s,')');

    set @froms := CONCAT('left join (
                select distinct
                    clv_upp,upp,clv_programa,programa
                from v_epp
                where ejercicio = ',anio,' and deleted_at is null
            ) up on 
                ma.clv_upp = up.clv_upp and
                ma.clv_pp = up.clv_programa
            where ma.deleted_at is null and ma.ejercicio = ',anio);

    set @query := CONCAT('
        select 
        clv_upp,
        upp,
        num_pp,
        clv_pp,
        programa,
        etapa,
        avance,
        revisado,
        m_enviada,
        m_atendida
    from (
        select 
            clv_upp,
            upp,
            num_pp,
            \"\" clv_pp,
            \"\" programa,
            0 etapa,
            avance,
            0 revisado,
            0 m_enviada,
            0 m_atendida
        from (
            select 
                sum(aux) aux,
                clv_upp,
                upp,
                max(num_pp) num_pp,
                sum(avance) avance
            from (
                select 
                    0 aux,
                    ma.clv_upp,
                    up.upp,
                    count(clv_pp) num_pp,
                    round((sum(etapa_0+etapa_1+etapa_2+etapa_3
                    +etapa_4+etapa_5)/(count(clv_pp)*6))*100) avance
                from mml_avance_etapas_pp ma
                ',@froms,'
                group by clv_upp,upp
                union all 
                select 
                    count(ma.clv_upp) aux,
                    ma.clv_upp,
                    up.upp,
                    count(clv_pp) num_pp,
                    0 avance
                from mml_avance_etapas_pp ma
                ',@froms,' ',@programa,'
                group by clv_upp,upp
            )t
            group by clv_upp,upp
        )t2
        where aux > 0
        union all
        select 
            ma.clv_upp,
            up.upp,
            0 num_pp,
            ma.clv_pp,
            up.programa,
            case 
                when etapa_0 = 0 then -1
                else (etapa_1+etapa_2+etapa_3+etapa_4+etapa_5)
            end etapa,
            round(((etapa_0+etapa_1+etapa_2+etapa_3
            +etapa_4+etapa_5)/6)*100) avance,
            case 
                when estatus = 2 then 1
                else 0
            end revisado,
            case 
                when estatus = 2 then 1
                else 0
            end m_enviada,
            case 
                when estatus = 3 then 1
                else 0
            end m_atendida
        from mml_avance_etapas_pp ma
        ',@froms,' ',@programa,'
        order by clv_upp,clv_pp
    )t where clv_upp in ',@lim_upp,@upp,' order by clv_upp,clv_pp');

    set @queryF := CONCAT('
        select 
            case 
                when clv_pp != \"\" then \"\"
                else clv_upp
            end clv_upp,
            case 
                when clv_pp != \"\" then \"\"
                else upp
            end upp,
            num_pp,
            clv_pp,
            programa,
            etapa,
            avance,
            revisado,
            m_enviada,
            m_atendida
        from (',@query,') f;
    ');

    prepare stmt  from @queryF;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int,in corte varchar(13))
begin
    set @corte := 'deleted_at is null';
    set @tabla := 'programacion_presupuesto';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @query := CONCAT('
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

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_1
    with aux as (
        select distinct
            clv_upp,claves mir,estatus
        from (
            select mm.clv_upp,concat(mm.clv_ur,mm.area_funcional,m.clv_fondo) claves,m.estatus
            from metas m
            join mml_mir mm on m.mir_id = mm.id
            where m.ejercicio = anio and m.deleted_at is null
            union all 
            select ma.clv_upp,concat(substr(ma.entidad_ejecutora,5,2),ma.area_funcional,m.clv_fondo) claves,m.estatus
            from metas m
            join mml_actividades ma on m.actividad_id = ma.id
            where m.ejercicio = anio and m.deleted_at is null
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
        from (select distinct clv_upp,upp from v_epp where ejercicio = anio and deleted_at is null) ve
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
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
end;");

        DB::unprepared("CREATE PROCEDURE calendario_fondo_mensual(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
    end if;
                
    set @query := CONCAT('
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
        join fondo f on pp.ramo = f.clv_ramo
        and pp.fondo_ramo = f.clv_fondo_ramo
        where pp.ejercicio = ',anio,' and pp.',@corte,'
        group by f.clv_ramo,f.ramo,f.clv_fondo_ramo,f.fondo_ramo
        order by clv_ramo,clv_fondo_ramo
    )t;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
END;");

        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int,in corte varchar(13),in uppC varchar(3),in tipo varchar(9))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @corteCat := ' and c.deleted_at is null';
    set @catalogo := 'catalogo';
    set @upp := '';
    set @tipo := '';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @catalogo := 'catalogo_hist';
        set @corteCat := concat(' and c.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
        set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
    end if;

    if (uppC is not null) then set @upp := CONCAT('and pp.upp = \"',uppC,'\"'); end if;
    if (tipo is not null) then set @tipo := concat('and pp.tipo = \"',tipo,'\"'); end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @query := CONCAT('      
    create temporary table aux_0
    select 
        2 orden,
        c.clave clv_upp,
        concat(
            c.clave,\" \",
            c.descripcion
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
    join ',@catalogo,' c on c.clave = pp.upp and c.grupo_id = 6',@corteCat,'
    where pp.ejercicio = ',anio,' and pp.',@corte,' ',@upp,' ',@tipo,';
    ');

    prepare stmt  from @query;
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

        DB::unprepared("CREATE PROCEDURE conceptos_clave(in claveT varchar(64),in anio int)
begin
    set @clave := claveT; 
    set @epp := concat(substring(@clave,1,5),substring(@clave,16,22));
    set @clasGeo := ((substring(@clave,6,10))*1);
    set @partida := ((substring(@clave,44,6))*1);
    set @fondo := substring(@clave,52,7);
    set @obra := substring(@clave,59,6);
    
    set @query := concat(\"
    with 
        epp_llaves as (
            select 
                ve.*,concat(clv_sector_publico,clv_sector_publico_f,clv_sector_economia,clv_subsector_economia,clv_ente_publico,clv_upp,clv_subsecretaria,clv_ur,clv_finalidad,clv_funcion,clv_subfuncion,clv_eje,clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,clv_programa,clv_subprograma,clv_proyecto) as llave
            from v_epp ve where deleted_at is null and ejercicio = \",anio,\"
        ),
        clas_geo_llaves as (
            select cg.*,concat(clv_entidad_federativa,clv_region,clv_municipio,clv_localidad) clasificacion_geografica_llave
            from clasificacion_geografica cg where deleted_at is null
        ),
        pos_pre_llaves as (
            select pp.*,concat(clv_capitulo,clv_concepto,clv_partida_generica,clv_partida_especifica,clv_tipo_gasto) posicion_presupuestaria_llave
            from posicion_presupuestaria pp where deleted_at is null
        ),
        fondo_llaves as (
            select f.*,concat(clv_etiquetado,clv_fuente_financiamiento,clv_ramo,clv_fondo_ramo,clv_capital) llave
            from fondo f where deleted_at is null
        )
    select *
    from (
        select 'Sector Público' descripcion, vel.clv_sector_publico clave,vel.sector_publico concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Sector Público Financiero/No Financiero' descripcion, vel.clv_sector_publico_f clave,vel.sector_publico_f concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Sector Economía' descripcion, vel.clv_sector_economia clave,vel.sector_economia concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subsector Economía' descripcion,vel.clv_subsector_economia clave,vel.subsector_economia concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Ente Público' descripcion,vel.clv_ente_publico clave,vel.ente_publico concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Entidad Federativa' descripcion,vcg.clv_entidad_federativa clave,vcg.entidad_federativa concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Región' descripcion,vcg.clv_region clave,vcg.region concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Municipio' descripcion,vcg.clv_municipio clave,vcg.municipio concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Localidad' descripcion,vcg.clv_localidad clave,vcg.localidad concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Unidad Programática Presupuestal' descripcion,vel.clv_upp clave,vel.upp concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subsecretaría' descripcion,vel.clv_subsecretaria clave,vel.subsecretaria concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Unidad Responsable' descripcion,vel.clv_ur clave,vel.ur concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Finalidad' descripcion,vel.clv_finalidad clave,vel.finalidad concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Función' descripcion,vel.clv_funcion clave,vel.funcion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subfunción' descripcion,vel.clv_subfuncion clave,vel.subfuncion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Eje' descripcion,vel.clv_eje clave,vel.eje concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Linea de Acción' descripcion,vel.clv_linea_accion clave,vel.linea_accion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Programa Sectorial' descripcion,vel.clv_programa_sectorial clave,vel.programa_sectorial concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Tipología General' descripcion,vel.clv_tipologia_conac clave,vel.clv_tipologia_conac concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Programa Presupuestal' descripcion,vel.clv_programa clave,vel.programa concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subprograma Presupuestal' descripcion,vel.clv_subprograma clave,vel.subprograma concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Proyecto Presupuestal' descripcion,vel.clv_proyecto clave,vel.proyecto concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Mes de Afectación' descripcion,substring('\",@clave,\"',38,6) clave, 'Mes de Afectación' union all
        select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Año (Fondo del Ramo)' descripcion,substring('\",@clave,\"',50,2) clave, 'Año' concepto union all
        select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where deleted_at is null and po.clv_proyecto_obra like '\",@obra,\"'
    ) tabla;
    \");
    
    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
END;");

        DB::unprepared("CREATE PROCEDURE proyecto_avance_general(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @catalogo := 'catalogo_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            where pa.ejercicio = ',anio,' and pa.',@corte,'
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
        clv_upp,
        c.descripcion upp,
        clv_fondo,
        f.fondo_ramo fondo,
        min(monto_anual) monto_anual,
        sum(calendarizado) calendarizado
    from aux_0 a0
    left join ',@catalogo,' c on a0.clv_upp = c.clave and c.grupo_id = 6
    and c.ejercicio = ',anio,' and c.',@corte,'
    left join fondo f on a0.clv_fondo = f.clv_fondo_ramo
    and f.deleted_at is null
    group by clv_upp,descripcion,clv_fondo,fondo_ramo;
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
                clv_capitulo,capitulo
            from posicion_presupuestaria
            where deleted_at is null
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
END;");

        DB::unprepared("CREATE PROCEDURE proyecto_calendario_actividades(in anio int,in upp varchar(3),in corte varchar(13),in tipo varchar(9))
begin
    set @upp := '';
    set @corte := 'deleted_at is null';
    set @tabla := 'metas';
    set @actividades := 'mml_actividades';
    set @mir := 'mml_mir';
    set @catalogo := 'catalogo';
    set @id := 'id';
    set @tipo := '';

    if(upp is not null) then set @upp := concat(\"where clv_upp = '\",upp,\"'\"); end if;
    if(tipo is not null) then set @tipo := concat('and m.tipo_meta = \"',tipo,'\"'); end if;
    if(corte is not null) then
        set @mir := 'mml_mir_hist';
        set @id := 'id_original';
        set @tabla := 'metas_hist';
        set @catalogo := 'catalogo_hist';
        set @actividades := 'mml_actividades_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        b.beneficiario,
        u2.unidad_medida,
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
    from \",@tabla,\" m
    join beneficiarios b on m.beneficiario_id = b.id 
    join unidades_medida um on m.unidad_medida_id = um.id
    join \",@actividades,\" ma on m.actividad_id = ma.\",@id,\"
    join unidades_medida u2 on m.unidad_medida_id = u2.id
    left join \",@catalogo,\" c on c.clave = substring(ma.area_funcional,11,3)
    and c.ejercicio = \",anio,\" and c.\",@corte,\" and c.grupo_id = 20
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
        b.beneficiario,
        um.unidad_medida,
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
    from \",@tabla,\" m 
    join beneficiarios b on m.beneficiario_id = b.id
    join unidades_medida um on m.unidad_medida_id = um.id
    join \",@mir,\" mm on m.mir_id = mm.\",@id,\"
    where m.mir_id is not null \",@tipo,\" and m.ejercicio = \",anio,\" and m.\",@corte,\";
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
    set @query := concat('
    create temporary table aux_1
    select distinct
        c.descripcion upp,clv_upp,clv_ur,clv_programa,clv_subprograma,
        clv_proyecto,clv_fondo
    from aux_0 a0
    left join ',@catalogo,' c on a0.clv_upp = c.clave
    and c.ejercicio = ',anio,' and c.grupo_id = 6 and c.',@corte,';
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
END;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_II(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @id := 'id';
    set @metas := 'metas';
    set @mir := 'mml_mir';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @id := 'id_original';
        set @metas := 'metas_hist';
        set @mir := 'mml_mir_hist';
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
    left JOIN (
        SELECT 
            mm.clv_upp,
            mm.clv_pp,
            mm.indicador
        FROM ',@mir,' mm
        WHERE ejercicio = ',anio,' AND ',@corte,' AND nivel = 8
    ) mm2 ON mm.clv_upp = mm2.clv_upp AND mm.clv_pp = mm2.clv_pp
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
    from v_epp
    where ejercicio = ',anio,' and deleted_at is null
    ');

    if(corte is not null) then
    set @catalogo := concat('
    select distinct 
        c1.clave clv_upp,c1.descripcion upp,
        c2.clave clv_programa,c2.descripcion programa,
        c3.clave clv_subprograma,c3.descripcion subprograma
    from epp_hist e 
    left join catalogo_hist c1 on e.upp_id = c1.id_original
    left join catalogo_hist c2 on e.programa_id = c2.id_original
    left join catalogo_hist c3 on e.subprograma_id = c3.id_original
    where e.ejercicio = ',anio,' and e.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)
    ');
    end if;

    set @queri := concat('
    create temporary table aux_2
    select 
        a0.clv_upp,
        ve.upp,
        f.clv_fuente_financiamiento,
        f.fuente_financiamiento,
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
    left join fondo f on a0.clv_fondo = f.clv_fondo_ramo
    left join (',@catalogo,') ve on a0.clv_upp = ve.clv_upp and a0.clv_programa = ve.clv_programa
    and a0.clv_subprograma = ve.clv_subprograma
    and f.deleted_at is null;
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_III(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'and deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('and deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @queri := concat('
    create temporary table aux_0
    select distinct
        clv_upp,upp,clv_ur,ur,
        clv_programa,programa,
        clv_subprograma,subprograma,
        clv_proyecto,proyecto
    from v_epp
    where ejercicio = ',anio,';
    ');
    
    if(corte is not null) then
    set @queri := concat('
    create temporary table aux_0
    with aux as (
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)
    )
    select 
        c1.clave clv_upp,c1.descripcion upp,
        c2.clave clv_ur,c2.descripcion ur,
        c3.clave clv_programa,c3.descripcion programa,
        c4.clave clv_subprograma,c4.descripcion subprograma,
        c5.clave clv_proyecto,c5.descripcion proyecto
    from (
        select distinct
            upp_id,ur_id,programa_id,subprograma_id,proyecto_id
        from epp_hist
        where ejercicio = ',anio,' and deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)
    ) e
    left join aux c1 on e.upp_id = c1.id_original
    left join aux c2 on e.ur_id = c2.id_original
    left join aux c3 on e.programa_id = c3.id_original
    left join aux c4 on e.subprograma_id = c4.id_original
    left join aux c5 on e.proyecto_id = c5.id_original;
    ');
    end if;

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
    where ejercicio = ',anio,' ',@corte,'
    group by upp,ur,programa_presupuestario,
    subprograma_presupuestario,
    proyecto_presupuestario,posicion_presupuestaria;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
    
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
    left join posicion_presupuestaria pp on
    concat(pp.clv_capitulo,pp.clv_concepto,
    pp.clv_partida_generica,pp.clv_partida_especifica) = a1.pos_pre
    and pp.deleted_at is null
    group by a1.clv_upp,a0.upp,a1.clv_ur,a0.ur,
    a1.clv_programa,a0.programa,a1.clv_subprograma,
    a0.subprograma,a1.clv_proyecto,a0.proyecto,a1.pos_pre,
    pp.partida_especifica
    order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_IX(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    set @metas := 'metas';
    set @mir := 'mml_mir';
    set @id := 'id';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @catalogo := 'catalogo_hist';
        set @metas := 'metas_hist';
        set @mir := 'mml_mir_hist';
        set @id := 'id_original';
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
        where ejercicio = ',anio,' and ',@corte,'
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto pp';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist pp';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_3(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto pp';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist pp';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @catalogo := 'catalogo';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @catalogo := 'catalogo_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
                c.clave clv_upp,
                c.descripcion upp,
                sum(total) importe
            from ',@catalogo,' c
            left join ',@tabla,' pp on c.clave = pp.upp 
            and pp.',@corte,' and pp.ejercicio = ',anio,'
            where c.ejercicio = ',anio,' and c.',@corte,' and c.grupo_id = 6
            group by c.clave,c.descripcion
            order by clv_upp
        )t;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_2(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @catalogo := 'catalogo';
    set @corte := 'deleted_at is null';
    set @epp := 'epp';
    set @id := 'id';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @catalogo := 'catalogo_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @epp := 'epp_hist';
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
    from v_epp
    where ejercicio = ',anio,' and ',@corte,';
    ');
    
    if(corte is not null) then 
    set @query := CONCAT('
    create temporary table aux_0
    with aux as (
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and ',@corte,'
    )
    select distinct
        c0.clave clv_finalidad,
        c0.descripcion finalidad,
        c1.clave clv_funcion,
        c1.descripcion funcion
    from (
        select distinct
            finalidad_id,funcion_id
        from epp_hist
        where ejercicio = ',anio,' and ',@corte,'
    ) e
    join aux c0 on e.finalidad_id = c0.id_original
    join aux c1 on e.funcion_id = c1.id_original;
    ');
    end if;

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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_3(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if(corte is not null) then
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;
    
    set @tablaWith := concat(\"
    with aux as (
        select 
            min(tc.id) id,
            'Programas' abuelo,
            tc.descripcion padre,
            tc.descripcion_conac hijo,
            sum(pp.total) importe
        from tipologia_conac tc 
        left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac
        and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        where tc.deleted_at is null and tc.clave_conac is not null
        group by tc.descripcion,tc.descripcion_conac
        union all
        select 
            min(tc.id) id,
            tc.descripcion abuelo,
            '' padre,
            '' hijo,
            sum(pp.total) importe
        from tipologia_conac tc 
        left join \",@tabla,\" pp on tc.clave = pp.tipologia_conac
        and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
        where tc.deleted_at is null and tc.clave is not null
        group by tc.descripcion
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
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_4(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
            concat(
                clv_capitulo,clv_concepto
            ) llave,
            po.clv_capitulo,
            po.capitulo,
            po.clv_concepto,
            po.concepto 
        from posicion_presupuestaria po
        where deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_5(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
     if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
                pp.clv_capitulo,
                pp.capitulo
            from posicion_presupuestaria pp
            where pp.deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_6(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
                clv_tipo_gasto,tipo_gasto
            from posicion_presupuestaria pp
            where deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_1(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
        set @catalogo := 'catalogo_hist';
    end if;

    set @query := CONCAT('
    with aux as (
        select 
            1 etiquetado,
            c.clave clv_upp,
            c.descripcion upp,
            pp.total
        from ',@catalogo,' c 
        left join ',@tabla,' pp on c.clave = pp.upp
        and pp.',@corte,' and pp.ejercicio = ',anio,' and pp.etiquetado = 1
        where c.',@corte,' and c.ejercicio = ',anio,' and c.grupo_id = 6
        union all
        select 
            2 etiquetado,
            c.clave clv_upp,
            c.descripcion upp,
            pp.total
        from ',@catalogo,' c 
        left join ',@tabla,' pp on c.clave = pp.upp
        and pp.',@corte,' and pp.ejercicio = ',anio,' and pp.etiquetado = 2
        where c.',@corte,' and c.ejercicio = ',anio,' and c.grupo_id = 6
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

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_2(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null
    \");
    
    if(corte is not null) then
    set @query := CONCAT('
    create temporary table aux_0
    with aux as(
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and ',@corte,'
    )
    select
        c0.clave clv_finalidad,
        c0.descripcion finalidad,
        c1.clave clv_funcion,
        c1.descripcion funcion
    from (
        select distinct
            finalidad_id,funcion_id
        from epp_hist
        where ejercicio = ',anio,' and ',@corte,'
    ) e
    join aux c0 on e.finalidad_id = c0.id_original
    join aux c1 on e.funcion_id = c1.id_original;
    ');
    end if;

    prepare stmt  from @query;
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

    prepare stmt  from @query;
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

    prepare stmt  from @query;
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_3(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null
    \");
    
    if(corte is not null) then
    set @query := CONCAT('
    create temporary table aux_0
    with aux as(
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and ',@corte,'
    )
    select
        c0.clave clv_finalidad,
        c0.descripcion finalidad,
        c1.clave clv_funcion,
        c1.descripcion funcion
    from (
        select distinct
            finalidad_id,funcion_id
        from epp_hist
        where ejercicio = ',anio,' and ',@corte,'
    ) e
    join aux c0 on e.finalidad_id = c0.id_original
    join aux c1 on e.funcion_id = c1.id_original;
    ');
    end if;

    prepare stmt  from @query;
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

    prepare stmt  from @query;
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

    prepare stmt  from @query;
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_4(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
                pp.clv_capitulo,
                pp.capitulo,
                pp.clv_concepto,
                pp.concepto
            from posicion_presupuestaria pp
            where pp.deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_5(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

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
            from tipologia_conac tc 
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
            from tipologia_conac tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac 
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 1
            where tc.tipo = 0
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
            from tipologia_conac tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac 
            and pp.ejercicio = \",anio,\" and pp.\",@corte,\" and pp.etiquetado = 2
            where tc.tipo = 0
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
            from tipologia_conac tc 
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
            from tipologia_conac tc 
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
            from tipologia_conac tc 
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
            from tipologia_conac tc 
            left join \",@tabla,\" pp on tc.clave_conac = pp.tipologia_conac
            and pp.etiquetado = 2 and pp.ejercicio = \",anio,\" and pp.\",@corte,\"
            where tc.clave_conac is not null
            group by descripcion,clave_conac,descripcion_conac
            order by etiquetado,id,abuelo,padre,clave,hijo
        )t;
    \");

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_10(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_1(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @epp := 'epp';
    set @catalogo := 'catalogo';
    set @id := 'id';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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

    if(corte is not null) then
    set @from := concat(\"
        with aux as (
            select *
            from catalogo_hist
            where ejercicio = \",anio,\" and \",@corte,\"
        )
        select 
            c1.clave clv_upp,c1.descripcion upp,
            c2.clave clv_subsecretaria,c2.descripcion subsecretaria,
            c3.clave clv_ur,c3.descripcion ur
        from (
            select distinct
                upp_id,subsecretaria_id,ur_id
            from epp_hist
            where ejercicio = \",anio,\" and \",@corte,\"
        ) e
        left join aux c1 on e.upp_id = c1.id_original
        left join aux c2 on e.subsecretaria_id = c2.id_original
        left join aux c3 on e.ur_id = c3.id_original
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
            fuente_financiamiento,
            clv_fondo_ramo clv_fondo
        from fondo f
        where deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
        set @catalogo := 'catalogo_hist';
    end if;
    
    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;

    set @query := concat('
        create temporary table aux_0
        with aux as (
            select 
                region clv_region,
                municipio clv_municipio,
                localidad clv_localidad,
                upp clv_upp,
                sum(total) importe
            from ',@tabla,' pp
            where ejercicio = ',anio,' and ',@corte,'
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
        left join clasificacion_geografica cg on a.clv_region = cg.clv_region
        and a.clv_municipio = cg.clv_municipio and a.clv_localidad = cg.clv_localidad and cg.deleted_at is null
        left join ',@catalogo,' c on a.clv_upp = c.clave and c.grupo_id = 6
        and c.ejercicio = ',anio,' and c.',@corte,'
        order by cg.clv_region,cg.clv_municipio,cg.clv_localidad,a.clv_upp;
    ');
    
    prepare stmt  from @query;
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
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_3(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
        set @catalogo := 'catalogo_hist';
    end if;

    set @query := CONCAT('
        select 
            c.clave clv_eje,
            c.descripcion eje,
            case
                when sum(pp.total) is null then 0
                else sum(pp.total)
            end importe
        from ',@catalogo,' c
        left join ',@tabla,' pp on c.clave = pp.eje 
        and pp.ejercicio = ',anio,' and pp.',@corte,'
        where c.ejercicio = ',anio,' and c.grupo_id = 12 and c.',@corte,'
        group by c.clave,c.descripcion;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_4(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @catalogo := 'catalogo_hist';
    end if;

    set @query := CONCAT('
        select 
            t.clv_programa,
            t.programa,
            case 
                when sum(pp.total) is null then 0
                else sum(pp.total)
            end importe
        from (
            select 
                clave clv_programa,descripcion programa
            from ',@catalogo,'
            where ejercicio = ',anio,' and ',@corte,' and grupo_id = 16
        )t
        left join ',@tabla,' pp on t.clv_programa = pp.programa_presupuestario
        and pp.ejercicio = ',anio,' and pp.',@corte,'
        group by clv_programa,programa
        order by clv_programa;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_5(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
    drop temporary table if exists aux_4;

    set @query := CONCAT('
    create temporary table aux_0
    select 
        pp.upp,
        po.clv_capitulo,
        po.capitulo,
        pp.programa_presupuestario programa,
        sum(total) importe
    from ',@tabla,' pp
    join (
        select distinct
            po.clv_capitulo,
            po.capitulo
        from posicion_presupuestaria po
        where po.deleted_at is null
    ) po on po.clv_capitulo = substring(pp.posicion_presupuestaria,1,1)
    where pp.ejercicio = ',anio,' and pp.',@corte,'
    group by pp.upp,po.clv_capitulo,po.capitulo,pp.programa_presupuestario;
    ');

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat('
    create temporary table aux_1
    select distinct 
        clv_upp,upp,clv_programa,programa
    from v_epp
    where ejercicio = ',anio,' and deleted_at is null;
    ');

    if(corte is not null) then
    set @query := concat('
    create temporary table aux_1
    with aux as (
        select*
        from catalogo_hist 
        where ejercicio = ',anio,' and ',@corte,'
    )
    select 
        c1.clave clv_upp,c1.descripcion upp,
        c2.clave clv_programa,c2.descripcion programa
    from (
        select distinct upp_id,programa_id
        from epp_hist where ejercicio = ',anio,' and ',@corte,'
    )t
    left join aux c1 on t.upp_id = c1.id_original
    left join aux c2 on t.programa_id = c2.id_original;
    ');
    end if;

    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

    create temporary table aux_2
    select 
        concat(
            a1.clv_upp,\" \",
            a1.upp
        ) upp,
        concat(
            a0.clv_capitulo,\"000 \",
            a0.capitulo
        ) capitulo,
        concat(
            a1.clv_programa,\" \",
            a1.programa
        ) programa,
        importe
    from aux_0 a0
    join aux_1 a1 on a0.upp = a1.clv_upp 
    and a0.programa = a1.clv_programa;
    
    create temporary table aux_3
    select 
        upp,
        capitulo,
        sum(importe) importe
    from aux_2
    group by upp,capitulo;
    
    create temporary table aux_4
    select 
        upp,
        sum(importe) importe
    from aux_3
    group by upp;
    
    select 
        case 
            when capitulo != '' then ''
            else upp
        end upp,
        case 
            when programa != '' then ''
            else capitulo
        end capitulo,
        programa programa_presupuestario,
        importe
    from (
        select  
            upp,
            '' capitulo,
            '' programa,
            importe
        from aux_4
        union all
        select
            upp,
            capitulo,
            '' programa,
            importe
        from aux_3
        union all
        select * from aux_2
        order by upp,capitulo,programa
    )t;
    
    drop temporary table aux_0;
    drop temporary table aux_1;
    drop temporary table aux_2;
    drop temporary table aux_3;
    drop temporary table aux_4;
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_6(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;

    set @query := concat('
    create temporary table aux_0
    select distinct
        clv_finalidad,finalidad,
        clv_funcion,funcion,
        clv_subfuncion,subfuncion
    from v_epp
    where ejercicio = ',anio,' and deleted_at is null;
    ');

    if(corte is not null) then
    set @query := CONCAT('
    create temporary table aux_0
    with aux as(
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and grupo_id in (9,10,11) and ',@corte,'
    )
    select distinct
        c0.clave clv_finalidad,
        c0.descripcion finalidad,
        c1.clave clv_funcion,
        c1.descripcion funcion,
        c2.clave clv_subfuncion,
        c2.descripcion subfuncion
    from (
        select distinct 
            finalidad_id,funcion_id,subfuncion_id
        from epp_hist
        where ejercicio = ',anio,' and ',@corte,'
    ) e
    join aux c0 on e.finalidad_id = c0.id_original
    join aux c1 on e.funcion_id = c1.id_original
    join aux c2 on e.subfuncion_id = c2.id_original;
    ');
    end if;

    prepare stmt  from @query;
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_7(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
    drop temporary table if exists aux_4;

    set @from := concat('
    select distinct
        clv_upp,upp,clv_ur,ur,
        clv_finalidad,finalidad,
        clv_funcion,funcion,
        clv_subfuncion,subfuncion
    from v_epp
    where ejercicio = ',anio,' and ',@corte,'
    ');

    if(corte is not null) then
    set @from := concat('
    with aux as (
        select *
        from catalogo_hist
        where ejercicio = ',anio,' and grupo_id in (6,8,9,10,11,12) and ',@corte,'
    )
    select 
        c1.clave clv_upp,c1.descripcion upp,
        c2.clave clv_ur,c2.descripcion ur,
        c3.clave clv_finalidad,c3.descripcion finalidad,
        c4.clave clv_funcion,c4.descripcion funcion,
        c5.clave clv_subfuncion,c5.descripcion subfuncion
    from (
        select distinct
            upp_id,ur_id,finalidad_id,
            funcion_id,subfuncion_id
        from epp_hist where ejercicio = ',anio,' and ',@corte,'
    ) e
    left join aux c1 on upp_id = c1.id_original
    left join aux c2 on ur_id = c2.id_original
    left join aux c3 on finalidad_id = c3.id_original
    left join aux c4 on funcion_id = c4.id_original
    left join aux c5 on subfuncion_id = c5.id_original
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
        where ejercicio = ',anio,' and pp.',@corte,'
        group by upp,ur,finalidad,funcion,subfuncion
    ) a
    left join (',@from,') ve on a.upp = ve.clv_upp and a.ur = ve.clv_ur
    and a.finalidad = ve.clv_finalidad and a.funcion = ve.clv_funcion
    and a.subfuncion = ve.clv_subfuncion;
    ');

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

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
    join posicion_presupuestaria pp on pa.posicion_presupuestaria =
    concat(pp.clv_capitulo,pp.clv_concepto,pp.clv_partida_generica,
    pp.clv_partida_especifica) and pp.deleted_at is null
    where pa.ejercicio = ',anio,' and pa.',@corte,'
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
end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_8(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;

    set @from := concat(\"
    select distinct
        clv_upp,upp,clv_programa,programa
    from v_epp
    where ejercicio = \",anio,\" and \",@corte,\"
    \");

    if(corte is not null) then
    set @from := concat(\"
    with aux as (
        select *
        from catalogo_hist
        where ejercicio = \",anio,\" and grupo_id in (6,16) and \",@corte,\"
    )
    select 
        c1.clave clv_upp,c1.descripcion upp,
        c2.clave clv_programa,c2.descripcion programa
    from (
        select distinct upp_id,programa_id
        from epp_hist 
        where ejercicio = \",anio,\" and \",@corte,\"
    ) e
    left join aux c1 on e.upp_id = c1.id_original
    left join aux c2 on e.programa_id = c2.id_original
    \");
    end if;

    set @query := concat(\"
    with aux as (
        select 
            a1.clv_upp,a1.upp,
            a1.clv_programa,a1.programa,
            a0.clv_proyecto_obra,
            po.proyecto_obra,
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
        left join proyectos_obra po on a0.clv_proyecto_obra = po.clv_proyecto_obra
        and po.deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_resumen_por_capitulo_y_partida(in anio int,in corte varchar(13))
begin
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @id := 'id';

    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
        set @id := 'id_original';
    end if;

    drop temporary table if exists aux_0;
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;

    create temporary table aux_0
    select distinct
        clv_capitulo,
        capitulo,
        concat(
            pp.clv_capitulo,
            pp.clv_concepto,
            pp.clv_partida_generica,
            pp.clv_partida_especifica
        ) clv_partida,
        pp.partida_especifica partida
    from posicion_presupuestaria pp
    where pp.deleted_at is null;
    
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

        DB::unprepared("CREATE PROCEDURE sapp_reporte_calendario(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in subprograma_v varchar(3),in proyecto_v varchar(3))
begin
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
    
    set @upp := ''; 		set @upp2 := '';
    set @ur := '';			set @ur2 := '';
    set @programa := '';	set @programa2 := '';
    set @subprograma := ''; set @subprograma2 := '';
    set @proyecto := '';	set @proyecto2 := '';
    set @mes := '';         set @trimestre := '';
    set @todos := '';

    if(upp_v is not null) then 
        set @upp := concat(\" where clv_upp =  '\",upp_v,\"'\");
        set @upp2 := concat(\" and upp =  '\",upp_v,\"'\");
    end if;
    if(ur_v is not null) then 
        set @ur := concat(\" and clv_ur = '\",ur_v,\"'\");
        set @ur2 := concat(\" and ur = '\",ur_v,\"'\"); 
    end if;
    if(programa_v is not null) then 
        set @programa := concat(\" and clv_programa = '\",programa_v,\"'\"); 
        set @programa2 := concat(\" and programa_presupuestario = '\",programa_v,\"'\"); 
    end if;
    if(subprograma_v is not null) then 
        set @subprograma := concat(\" and clv_subprograma = '\",subprograma_v,\"'\");
        set @subprograma2 := concat(\" and subprograma_presupuestario = '\",subprograma_v,\"'\");
    end if;
    if(proyecto_v is not null) then 
        set @proyecto := concat(\" and clv_proyecto = '\",proyecto_v,\"'\");
        set @proyecto2 := concat(\" and proyecto_presupuestario = '\",proyecto_v,\"'\");
    end if;
    if(mes is not null) then
        if(mes = 1) then set @mes := 'enero'; end if;
        if(mes = 2) then set @mes := 'febrero'; end if;
        if(mes = 3) then set @mes := 'marzo'; end if;
        if(mes = 4) then set @mes := 'abril'; end if;
        if(mes = 5) then set @mes := 'mayo'; end if;
        if(mes = 6) then set @mes := 'junio'; end if;
        if(mes = 7) then set @mes := 'julio'; end if;
        if(mes = 8) then set @mes := 'agosto'; end if;
        if(mes = 9) then set @mes := 'septiembre'; end if;
        if(mes = 10) then set @mes := 'octubre'; end if;
        if(mes = 11) then set @mes := 'noviembre'; end if;
        if(mes = 12) then set @mes := 'diciembre'; end if;
    end if;
    if(trimestre is not null) then
        if(trimestre = 1) then set @trimestre := 'enero, febrero, marzo'; end if;
        if(trimestre = 2) then set @trimestre := 'abril, mayo, junio'; end if;
        if(trimestre = 3) then set @trimestre := 'julio, agosto, septiembre'; end if;
        if(trimestre = 4) then set @trimestre := 'octubre, noviembre, diciembre'; end if;
    end if;
    if(mes is null and trimestre is null) then 
        set @todos := 'enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre';
    end if;
    
    set @queri := concat(\"
    create temporary table aux_2
    with temp as (
        with aux as (
            select distinct
                clv_upp,upp,clv_ur,ur,
                clv_programa,programa,
                clv_subprograma,subprograma,
                clv_proyecto,proyecto
            from v_epp
            where ejercicio = \",anio,\" and deleted_at is null
        )
        select 
            a.*,t.monto
        from (
            select 
                upp clv_upp,ur clv_ur,
                programa_presupuestario clv_programa,
                subprograma_presupuestario clv_subprograma,
                proyecto_presupuestario clv_proyecto,
                sum(total) monto
            from programacion_presupuesto
            where ejercicio = \",anio,\" and deleted_at is null\",@upp2,\"\",@ur2,\"\",@programa2,\"\",@subprograma2,\"\",@proyecto2,\"
            group by upp,ur,programa_presupuestario,
            subprograma_presupuestario,proyecto_presupuestario
        )t
        left join aux a on t.clv_upp = a.clv_upp and t.clv_ur = a.clv_ur
        and t.clv_programa = a.clv_programa and t.clv_subprograma = a.clv_subprograma
        and t.clv_proyecto = a.clv_proyecto
        order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto
    )
    select 
        clv_upp,'' clv_ur,'' clv_programa,'' clv_subprograma,'' clv_proyecto,upp descripcion,sum(monto) monto
    from temp
    group by clv_upp,upp
    union all
    select 
        clv_upp,clv_ur,'' clv_programa,'' clv_subprograma,'' clv_proyecto,ur descripcion,sum(monto) monto
    from temp
    group by clv_upp,clv_ur,ur
    union all
    select 
        clv_upp,clv_ur,clv_programa,'' clv_subprograma,'' clv_proyecto,programa descripcion,sum(monto) monto
    from temp
    group by clv_upp,upp,clv_ur,ur,clv_programa,programa
    union all
    select 
        clv_upp,clv_ur,clv_programa,clv_subprograma,'' clv_proyecto,subprograma descripcion,sum(monto) monto
    from temp
    group by clv_upp,clv_ur,clv_programa,clv_subprograma,subprograma
    union all
    select 
        clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,proyecto descripcion,monto
    from temp
    order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto;
    \");
    
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @mes2 := '';
    set @trimestre2 := '';
    set @todos2 := '';
    if(mes is not null) then set @mes2 := concat('0 ',@mes); end if;
    if(trimestre is not null) then set @trimestre2 := concat('0 ',replace(@trimestre,',',',0')); end if;
    if(mes is null and trimestre is null) then set @todos2 := concat('0 ',replace(@todos,',',',0 ')); end if;

    set @queri := concat(\"
    create temporary table aux_3
    select 
        clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,descripcion,monto,
        '' actividad,'' beneficiarios,'' unidades_medida,
        \",@mes2,\"\",@trimestre2,\"\",@todos2,\"
    from aux_2
    union all
    select 
        clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,
        '' descripcion,0 monto,actividad,beneficiarios,unidades_medida,
        \",@mes,\"\",@trimestre,\"\",@todos,\"
    from (
        select *
        from (
            select 
                case 
                    when m.mir_id is not null then mm.clv_upp 
                    when m.actividad_id is not null then ma.clv_upp
                end clv_upp,
                case 
                    when m.mir_id is not null then mm.clv_ur
                    when m.actividad_id is not null then substr(ma.entidad_ejecutora,5,2)
                end clv_ur,
                case 
                    when m.mir_id is not null then mm.clv_pp
                    when m.actividad_id is not null then substr(ma.area_funcional,9,2)
                end clv_programa,
                case 
                    when m.mir_id is not null then substr(mm.area_funcional,11,3)
                    when m.actividad_id is not null then substr(ma.area_funcional,11,3)
                end clv_subprograma,
                case 
                    when m.mir_id is not null then substr(mm.area_funcional,14,3)
                    when m.actividad_id is not null then substr(ma.area_funcional,14,3)
                end clv_proyecto,
                case 
                    when m.mir_id is not null then concat(mm.id,' ',mm.indicador)
                    when m.actividad_id is not null and ma.id_catalogo is null
                        then concat(ma.id,' ',ma.nombre)
                    when m.actividad_id is not null and ma.id_catalogo is not null 
                        then concat(ma.id_catalogo,' ',c.descripcion)
                end actividad,
                concat(m.cantidad_beneficiarios,' ',b.beneficiario) beneficiarios,
                concat(m.total,' ',um.unidad_medida) unidades_medida,
                \",@mes,\"\",@trimestre,\"\",@todos,\"
            from metas m
            left join mml_mir mm on m.mir_id = mm.id
            left join mml_actividades ma on m.actividad_id = ma.id
            left join catalogo c on ma.id_catalogo = c.id
            left join beneficiarios b on m.beneficiario_id = b.id
            left join unidades_medida um on m.unidad_medida_id = um.id
            where m.ejercicio = \",anio,\" and m.deleted_at is null
        )t2\",@upp,\"\",@ur,\"\",@programa,\"\",@subprograma,\"\",@proyecto,\"
    ) aux_1
    order by clv_upp,clv_ur,clv_programa,clv_subprograma,
    clv_proyecto,actividad;
    \");

    if(mes is not null) then set @mes := concat(@mes,' as mes'); end if;
    if(trimestre is not null) then 
        if(trimestre = 1) then set @trimestre := 'enero mes1,febrero mes2,marzo mes3'; end if;
        if(trimestre = 2) then set @trimestre := 'abril mes1,mayo mes2,junio mes3'; end if;
        if(trimestre = 3) then set @trimestre := 'julio mes1,agosto mes2,septiembre mes3'; end if;
        if(trimestre = 4) then set @trimestre := 'octubre mes1,noviembre mes2,diciembre mes3'; end if;
    end if;

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @queri := concat(\"
    select 
        case 
            when actividad != '' then 2
            else 1
        end claves,
        case 
            when clv_ur != '' then ''
            else clv_upp
        end clv_upp,
        case 
            when clv_programa != '' then ''
            else clv_ur
        end clv_ur,
        case 
            when clv_subprograma != '' then ''
            else clv_programa
        end clv_programa,
        case 
            when clv_proyecto != '' then ''
            else clv_subprograma
        end clv_subprograma,
        case 
            when actividad != '' then ''
            else clv_proyecto
        end clv_proyecto,
        descripcion,monto,actividad,beneficiarios,unidades_medida,
        \",@mes,\"\",@trimestre,\"\",@todos,\"
    from aux_3;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists aux_3;
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE if EXISTS avance_etapas;");
        DB::unprepared("DROP PROCEDURE if EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE if EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE if EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE if EXISTS conceptos_clave;");
        DB::unprepared("DROP PROCEDURE if EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE if EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_III;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_1_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_a_num_6;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_10;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_5;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_art_20_frac_X_b_num_11_8;");
        DB::unprepared("DROP PROCEDURE if EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE if EXISTS sapp_reporte_calendario;");
    }
};
