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
        DB::unprepared("DROP PROCEDURE if EXISTS mml_alineacion;");
        DB::unprepared("DROP PROCEDURE if EXISTS mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE if EXISTS mml_matrices_indicadores;");
        DB::unprepared("DROP PROCEDURE if EXISTS mml_presupuesto_egresos;");

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

        DB::unprepared("CREATE PROCEDURE mml_alineacion(in anio int,in trimestre_n int,in semaforo int,in corte varchar(13),in upp_v varchar(3))
begin
    drop temporary table if exists catalogo_aux;
    drop temporary table if exists mir_metas;
    drop temporary table if exists estrategia_ods;
    drop temporary table if exists plan_desarrollo;
    drop temporary table if exists seguimiento;
    drop temporary table if exists t_final;
            
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    set @upp := '';
        
    if (corte is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
    end if;
    
    if(upp_v is not null) then
        set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
    end if;
        
    set @trimestre := '';
    if(trimestre_n = 1) then set @trimestre := '(1,2,3)'; end if;
    if(trimestre_n = 2) then set @trimestre := '(1,2,3,4,5,6)'; end if;
    if(trimestre_n = 3) then set @trimestre := '(1,2,3,4,5,6,7,8,9)'; end if;
    if(trimestre_n = 4) then set @trimestre := '(1,2,3,4,5,6,7,8,9,10,11,12)'; end if;
    
    set @queri := concat(\"
    create temporary table seguimiento
    select 
        meta_id,
        sum(realizado) realizado
    from sapp_seguimiento ss 
    where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"\",@upp,\"
    group by meta_id;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table catalogo_aux
    select distinct
        clv_upp,upp,clv_programa,programa
    from v_epp
    where ejercicio = \",anio,\" and \",@corte,\"
    \");
        
    if(corte is not null) then 
        set @queri := concat(\"
        create temporary table catalogo_aux
        with aux as (
           select *
           from catalogo_hist
           where ejercicio = \",anio,\" and \",@corte,\"
        )
        select 
           c1.clave clv_upp,c1.descripcion upp,
           c2.clave clv_programa,c2.descripcion programa
        from (
           select distinct
              upp_id,programa_id
           from epp_hist
           where ejercicio = \",anio,\" and \",@corte,\"
        ) e
        left join aux c1 on e.upp_id = c1.id_original
        left join aux c2 on e.programa_id = c2.id_original;
        \");
    end if;
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
     if(upp_v is not null) then
        set @upp := concat(\" and mm.clv_upp = '\",upp_v,\"'\");
    end if;
         
    set @queri := concat(\"
    create temporary table mir_metas
    select
        t.clv_upp,ca.upp,t.clv_programa,ca.programa,t.clv_ur,e.ur,nivel,
        tipo_indicador,padre,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
        descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,medios_verificacion,
        programado,avance,concat(e.clv_eje,'. ',e.eje) eje,e.linea_accion,
        case 
            when substr(e.linea_accion,8,1) = '.' or substr(e.linea_accion,8,1) = ' '
            then substr(e.linea_accion,1,7)
            else substr(e.linea_accion,1,8)
        end clv_cpladem
    from (
        select 
            mm.clv_upp,
            mm.clv_pp clv_programa,
            mm.clv_ur,
            nivel,
            case 
                when nivel = 8 then 'Fin'
                when nivel = 9 then 'Propósito'
                when nivel = 10 then 'Componente'
                when nivel = 11 then 'Actividad'
            end tipo_indicador,
            case 
                when mm.nivel = 10
                then mm.id
                else mm.componente_padre
            end padre,
            objetivo,
            indicador nombre_indicador,
            definicion_indicador,
            metodo_calculo,
            descripcion_metodo,
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
            um.unidad_medida,
            case 
                when dimension = 21 then 'Eficacia'
                when dimension = 22 then 'Eficiencia'
                when dimension = 23 then 'Calidad'
                when dimension = 24 then 'Economía'
            end dimension,
            medios_verificacion,
            m.total programado,
            case
                when ss.realizado is null then 0
                when ss.realizado = 0 then 0
                when m.total = 0 then 100
                when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
            end avance,
            mm.id_epp
        from mml_mir mm
        left join unidades_medida um on mm.unidad_medida = um.id
        join metas m on m.mir_id = mm.id and m.deleted_at is null
        join seguimiento ss on ss.meta_id = m.id
        where mm.ejercicio = \",anio,\" and mm.deleted_at is null\",@upp,\"
        order by clv_upp,clv_programa,clv_ur,nivel
    )t
    left join catalogo_aux ca on t.clv_upp = ca.clv_upp and t.clv_programa = ca.clv_programa
    left join v_epp e on t.id_epp = e.id and e.ejercicio = \",anio,\" and e.deleted_at is null
    order by clv_upp,clv_programa,clv_ur,padre,nivel;
    \");

    if(corte is not null) then 
    set @queri := concat(\"
        create temporary table mir_metas
        with aux as (
            select *
            from catalogo_hist
            where ejercicio = \",anio,\" and \",@corte,\" and grupo_id in (8,12,13)
        )
        select
            t.clv_upp,ca.upp,t.clv_programa,ca.programa,t.clv_ur,c1.descripcion ur,nivel,
            tipo_indicador,padre,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
            descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,medios_verificacion,
            programado,avance,concat(c2.clave,'. ',c2.descripcion) eje,c3.descripcion linea_accion,
            case 
                when substr(c3.descripcion,8,1) = '.' or substr(c3.descripcion,8,1) = ' '
                then substr(c3.descripcion,1,7)
                else substr(c3.descripcion,1,8)
            end clv_cpladem
        from (
            select 
                mm.clv_upp,
                mm.clv_pp clv_programa,
                mm.clv_ur,
                nivel,
                case 
                    when nivel = 8 then 'Fin'
                    when nivel = 9 then 'Propósito'
                    when nivel = 10 then 'Componente'
                    when nivel = 11 then 'Actividad'
                end tipo_indicador,
                case 
                    when mm.nivel = 10
                    then mm.id_original
                    else mm.componente_padre
                end padre,
                objetivo,
                indicador nombre_indicador,
                definicion_indicador,
                metodo_calculo,
                descripcion_metodo,
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
                um.unidad_medida,
                case 
                    when dimension = 21 then 'Eficacia'
                    when dimension = 22 then 'Eficiencia'
                    when dimension = 23 then 'Calidad'
                    when dimension = 24 then 'Economía'
                end dimension,
                medios_verificacion,
                m.total programado,
                case
                    when ss.realizado is null then 0
                    when ss.realizado = 0 then 0
                    when m.total = 0 then 100
                    when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
                end avance,
                mm.id_epp
            from mml_mir_hist mm
            left join unidades_medida um on mm.unidad_medida = um.id
            left join metas_hist m on m.mir_id = mm.id_original and m.deleted_at is null
            left join seguimiento ss on ss.meta_id = m.id_original
            where mm.ejercicio = \",anio,\" and mm.\",@corte,\"\",@upp,\"
            order by clv_upp,clv_programa,clv_ur,nivel
        )t
        left join catalogo_aux ca on t.clv_upp = ca.clv_upp and t.clv_programa = ca.clv_programa
        left join (
            select id_original,ur_id,eje_id,linea_accion_id
            from epp_hist
            where ejercicio = \",anio,\" and \",@corte,\"
        ) e on t.id_epp = e.id_original
        left join aux c1 on e.ur_id = c1.id_original
        left join aux c2 on e.eje_id = c2.id_original
        left join aux c3 on e.linea_accion_id = c3.id_original
        order by clv_upp,clv_programa,clv_ur,padre,nivel;
    \");
    end if;
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
            
    create temporary table estrategia_ods
    with aux as (
        select 
            clv_estrategia,plan_nacional,
            concat(group_concat(ods separator '_'),'________') ods
        from (
            select distinct
                clv_estrategia,
                concat(clv_plan_nacional,'. ',plan_nacional) plan_nacional,
                cast(clv_ods as unsigned) ods_n,
                concat(clv_ods,'. ',ods) ods
            from mml_objetivos_desarrollo_sostenible mo
            where deleted_at is null
            order by clv_estrategia,ods_n
        )t group by clv_estrategia,plan_nacional
    )
    select 
        clv_estrategia,plan_nacional,
        substring_index(ods,'_',1) ods_1,
        substring_index(substring_index(ods,'_',2),'_',-1) ods_2,
        substring_index(substring_index(ods,'_',3),'_',-1) ods_3,
        substring_index(substring_index(ods,'_',4),'_',-1) ods_4,
        substring_index(substring_index(ods,'_',5),'_',-1) ods_5,
        substring_index(substring_index(ods,'_',6),'_',-1) ods_6,
        substring_index(substring_index(ods,'_',7),'_',-1) ods_7,
        substring_index(substring_index(ods,'_',8),'_',-1) ods_8
    from aux;
    
    create temporary table plan_desarrollo
    with aux as (
        select distinct
            clv_objetivo_sectorial,objetivo_sectorial,
            clv_estrategia,estrategia,clv_cpladem_linea_accion
        from mml_objetivo_sectorial_estrategia
        where deleted_at is null
    )
    select 
        a.clv_cpladem_linea_accion clv_cpladem,
        a.clv_objetivo_sectorial,a.objetivo_sectorial,
        eo.clv_estrategia,a.estrategia,
        eo.plan_nacional,ods_1,ods_2,ods_3,
        ods_4,ods_5,ods_6,ods_7,ods_8
    from estrategia_ods eo
    left join aux a on eo.clv_estrategia = a.clv_estrategia;
            
    create temporary table t_final
    with aux as (
        select 
            clv_upp,upp,clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,padre,objetivo,
            nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,
            frecuencia_medicion,unidad_medida,dimension,medios_verificacion,programado,avance,
            case 
                when avance <= 60 then 0
                when avance > 60 and avance <= 94 then 1
                when avance > 94 and avance <= 110 then 2
                when avance > 110 then 3
            end color,
            eje,concat(pd.clv_objetivo_sectorial,'. ',pd.objetivo_sectorial) objetivo_sectorial,
            concat(pd.clv_estrategia,'. ',pd.estrategia) estrategia,
            linea_accion,plan_nacional,ods_1,ods_2,ods_3,ods_4,ods_5,ods_6,ods_7,ods_8
        from mir_metas mm
        left join plan_desarrollo pd on mm.clv_cpladem = pd.clv_cpladem
        order by clv_upp,clv_programa,clv_ur,padre,nivel
    )
    select distinct
        clv_upp,upp,clv_programa,programa,clv_ur,
        case 
            when ur is null then ''
            else ur
        end ur,
        nivel,tipo_indicador,padre,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
        descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,medios_verificacion,
        case 
            when programado is null then ''
            else programado
        end programado,
        avance,color,
        case 
            when eje is null then ''
            else eje
        end eje,
        case 
            when objetivo_sectorial is null then ''
            else objetivo_sectorial
        end objetivo_sectorial,
        case 
            when estrategia is null then ''
            else estrategia
        end estrategia,
        case 
            when linea_accion is null then ''
            else linea_accion
        end linea_accion,
        case 
            when plan_nacional is null then ''
            else plan_nacional
        end plan_nacional,
        case when ods_1 is null then '' else ods_1 end ods_1,
        case when ods_2 is null then '' else ods_2 end ods_2,
        case when ods_3 is null then '' else ods_3 end ods_3,
        case when ods_4 is null then '' else ods_4 end ods_4,
        case when ods_5 is null then '' else ods_5 end ods_5,
        case when ods_6 is null then '' else ods_6 end ods_6,
        case when ods_7 is null then '' else ods_7 end ods_7,
        case when ods_8 is null then '' else ods_8 end ods_8
    from aux;
        
    set @semaforo := \"\";
    if(semaforo is not null) then set @semaforo := concat(\"where color = \",semaforo); end if;
        
    set @queri := concat(\"
    select *
    from t_final \",@semaforo,\"
    order by clv_upp,clv_programa,clv_ur,padre,nivel
    \");
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists catalogo_aux;
    drop temporary table if exists mir_metas;
    drop temporary table if exists estrategia_ods;
    drop temporary table if exists plan_desarrollo;
    drop temporary table if exists seguimiento;
    drop temporary table if exists t_final;
END;");

        DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int,in corte varchar(13))
begin
    set @upp := '';
    set @upp2 := '';
    set @programa := '';
    set @programa2 := '';
    set @ur := '';
    set @ur2 := '';
    set @corte := 'deleted_at is null';
    set @catalogo := 'catalogo';
    set @id := 'id';
    set @mir := 'mml_mir';
    DROP TEMPORARY TABLE if EXISTS epp_t;
     
    if(upp is not null) then 
        set @upp := CONCAT('and mm.clv_upp = \"',upp,'\"'); 
        set @upp2 := CONCAT('where clv_upp = \"',upp,'\"'); 
    end if;
    if(programa is not null) then
        set @programa := CONCAT('and mm.clv_pp = \"',programa,'\"'); 
        if(upp is not null) then
            set @programa2 := CONCAT('and clv_pp = \"',programa,'\"'); 
        else
            set @programa2 := CONCAT('where clv_pp = \"',programa,'\"'); 
        end if;
    end if;
    if(ur is not null) then 
        set @ur := CONCAT('and mm.clv_ur = \"',ur,'\"'); 
        set @ur2 := CONCAT('and clv_ur = \"',ur,'\"'); 
    end if;
    if(corte is not null) then 
        set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @catalogo := 'catalogo_hist';
        set @id := 'id_original';
        set @mir := 'mml_mir_hist';
    end if;

    set @queri := concat(\"
    CREATE TEMPORARY TABLE epp_t
    select 
        clv_upp,clv_ur,clv_programa clv_pp,proyecto,
        concat(
            clv_finalidad,clv_funcion,clv_subfuncion,clv_eje,
            clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,
            clv_programa,clv_subprograma,clv_proyecto
        ) area_funcional
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null;
    \");

    if(corte is not null) then 
        set @queri := concat(\"
        CREATE TEMPORARY TABLE epp_t
        with aux as (
            select *
            from catalogo_hist
            where ejercicio = \",anio,\" and grupo_id in (6,7,8,9,10,11,12,13,14,15,16,17,18) and \",@corte,\"
        )
        SELECT DISTINCT
            c06.clave clv_upp,c08.clave clv_ur,c16.clave clv_pp,c18.descripcion proyecto,
            concat(
            c09.clave,
            c10.clave,
            c11.clave,
            c12.clave,
            c13.clave,
            c14.clave,
            c15.clave,
            c16.clave,
            c17.clave,
            c18.clave
            ) area_funcional
        from (
            select distinct 
                upp_id,subsecretaria_id,ur_id,finalidad_id,funcion_id,
                subfuncion_id,eje_id,linea_accion_id,programa_sectorial_id,
                tipologia_conac_id,programa_id,subprograma_id,proyecto_id
            from epp_hist
            where ejercicio = \",anio,\" and \",@corte,\"
        ) e
        join aux c06 on e.upp_id = c06.id_original
        join aux c07 on e.subsecretaria_id = c07.id_original  
        join aux c08 on e.ur_id = c08.id_original 
        join aux c09 on e.finalidad_id = c09.id_original 
        join aux c10 on e.funcion_id = c10.id_original 
        join aux c11 on e.subfuncion_id = c11.id_original 
        join aux c12 on e.eje_id = c12.id_original 
        join aux c13 on e.linea_accion_id = c13.id_original 
        join aux c14 on e.programa_sectorial_id = c14.id_original 
        join aux c15 on e.tipologia_conac_id = c15.id_original 
        join aux c16 on e.programa_id = c16.id_original 
        join aux c17 on e.subprograma_id = c17.id_original 
        join aux c18 on e.proyecto_id = c18.id_original;
        \");
    end if;
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @upp_n := \"\";
    if(upp is not null) then
      set @upp_n := concat(\"
         select 
             descripcion upp,'' clv_upp,'' clv_pp,'' clv_ur,'' area_funcional,
             '' nombre_proyecto,'' nivel,'' objetivo,'' indicador
         from \",@catalogo,\" 
         where grupo_id = 6 and deleted_at is null and ejercicio = \",anio,\" and clave = '\",upp,\"'
         union all\");
    end if;
            
    set @queri := concat(@upp_n,\"
    select
          '' upp,
        case 
            when nivel = 9 then clv_upp
            else ''
        end clv_upp,
        case 
            when nivel = 9 then clv_pp
            else ''
        end clv_pp,
        case 
            when nivel = 9 then clv_ur
            else ''
        end clv_ur,
        case 
            when nivel != 9 then area_funcional
            else ''
        end area_funcional,
        case 
            when nivel != 9 then proyecto
            else ''
        end nombre_proyecto,
        case 
            when nivel = 10 then 'Componente'
            when nivel = 11 then 'Actividad'
            else ''
        end nivel,
        objetivo,
        indicador 
    from (
        select *
        from (
            select
                mm.\",@id,\" id,
                mm.clv_upp,
                mm.clv_pp,
                mm.clv_ur,
                mm.area_funcional,
                ve.proyecto,
                mm.nivel,
                mm.objetivo,
                mm.indicador
            from \",@mir,\" mm
            join epp_t ve on ve.clv_upp = mm.clv_upp AND ve.clv_ur = mm.clv_ur AND 
            ve.area_funcional = mm.area_funcional
            where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
            and nivel in (10) \",@upp,\" \",@ur,\" \",@programa,\"
            union all 
            select
                mm.componente_padre id,
                mm.clv_upp,
                mm.clv_pp,
                mm.clv_ur,
                mm.area_funcional,
                ve.proyecto,
                mm.nivel,
                mm.objetivo,
                mm.indicador
            from \",@mir,\" mm
            join epp_t ve on ve.clv_upp = mm.clv_upp AND ve.clv_ur = mm.clv_ur AND 
            ve.area_funcional = mm.area_funcional
            where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
            and nivel in (11) \",@upp,\" \",@ur,\" \",@programa,\"
            union all
            select * FROM (
                 select distinct
                    0 id,clv_upp,clv_pp,clv_ur,
                    '' area_funcional,'' proyecto,9 nivel,'' objetivo,'' indicador
                 from epp_t
            ) ve \",@upp2,\"\",@programa2,\"\",@ur2,\"
        )t 
        group by clv_upp,clv_pp,clv_ur,id,nivel
        order by clv_upp,clv_pp,clv_ur,id,nivel
    )t2;
    \");
            
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    DROP TEMPORARY TABLE if EXISTS epp_t;
END;");

        DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int,in corte varchar(13),in upp_v varchar(3))
begin
    drop temporary table if exists aux_1;
    drop temporary table if exists catalogo_aux;
    drop temporary table if exists seguimiento;
    drop temporary table if exists aux_2;
        
    set @trimestre := '';
    if(trimestre_n = 1) then set @trimestre := '(1,2,3)'; end if;
    if(trimestre_n = 2) then set @trimestre := '(1,2,3,4,5,6)'; end if;
    if(trimestre_n = 3) then set @trimestre := '(1,2,3,4,5,6,7,8,9)'; end if;
    if(trimestre_n = 4) then set @trimestre := '(1,2,3,4,5,6,7,8,9,10,11,12)'; end if;
        
    set @corte := 'deleted_at is null';
    set @upp := '';
    set @id := 'id';
    set @mir := 'mml_mir';
    set @metas := 'metas';
    if(corte is not null) then 
        set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @id := 'id_original';
        set @mir := 'mml_mir_hist';
        set @metas := 'metas_hist';
    end if;
        
    if(upp_v is not null) then 
        set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
    end if;
                    
    set @queri := concat(\"
    create temporary table seguimiento
    select 
        meta_id,
        sum(realizado) realizado
    from sapp_seguimiento ss 
    where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"\",@upp,\"
    group by meta_id;
    \");
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
        
    if(upp_v is not null) then 
        set @upp := concat(\" and mm.clv_upp = '\",upp_v,\"'\");
    end if;
        
    set @queri := concat(\"
    create temporary table aux_1
    select 
        mm.clv_upp,
        mm.clv_pp clv_programa,
        mm.clv_ur,
        mm.nivel,
        case 
            when mm.nivel = 8 then 'Fin'
            when mm.nivel = 9 then 'Propósito'
            when mm.nivel = 10 then 'Componente'
            when mm.nivel = 11 then 'Actividad'
        end tipo_indicador,
        case 
           when mm.nivel = 10
           then mm.\",@id,\"
           else mm.componente_padre
        end padre,
        mm.objetivo resumen_narrativo,
        mm.indicador nombre_indicador,
        mm.definicion_indicador,
        mm.metodo_calculo,
        mm.descripcion_metodo,
        case 
            when mm.frecuencia_medicion = 29 then 'Quincenal'
            when mm.frecuencia_medicion = 30 then 'Mensual'
            when mm.frecuencia_medicion = 31 then 'Bimestral'
            when mm.frecuencia_medicion = 32 then 'Trimestral'
            when mm.frecuencia_medicion = 33 then 'Cuatrimestral'
            when mm.frecuencia_medicion = 34 then 'Semestral'
            when mm.frecuencia_medicion = 35 then 'Anual'
            when mm.frecuencia_medicion = 36 then 'Bianual'
            when mm.frecuencia_medicion = 37 then 'Quinquenal'
            when mm.frecuencia_medicion = 38 then 'Sexenal'
        end frecuencia_medicion,
        um.unidad_medida,
        case 
            when mm.dimension = 21 then 'Eficacia'
            when mm.dimension = 22 then 'Eficiencia'
            when mm.dimension = 23 then 'Calidad'
            when mm.dimension = 24 then 'Economía'
        end dimension,
        mm.medios_verificacion,
        m.total meta_anual,
        case 
            when ss.realizado is null and mm.nivel = 11 then 0
            else ss.realizado
        end trimestre,
        case
            when ss.realizado is null then 0
            when ss.realizado = 0 then 0
            when m.total = 0 then 100
            when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
        end avance
    from \",@mir,\" mm
    left join unidades_medida um on mm.unidad_medida = um.id
    join \",@metas,\" m on mm.\",@id,\" = m.mir_id
    join seguimiento ss on ss.meta_id = m.\",@id,\"
    where mm.ejercicio = \",anio,\" and mm.\",@corte,\"\",@upp,\"
    order by clv_upp,clv_programa,clv_ur,padre,nivel;
    \");
                
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table catalogo_aux
    select distinct
        clv_upp,upp,clv_programa,programa,clv_ur,ur
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null;
    \");

    if(corte is not null) then 
        set @queri := concat(\"
        create temporary table catalogo_aux
        with aux as (
           select *
           from catalogo_hist
           where ejercicio = \",anio,\" and \",@corte,\"
        )
        select 
           c1.clave clv_upp,c1.descripcion upp,
           c2.clave clv_programa,c2.descripcion programa,
           c3.clave clv_ur,c3.descripcion ur
        from (
           select distinct
              upp_id,programa_id,ur_id
           from epp_hist
           where ejercicio = \",anio,\" and \",@corte,\"
        ) e
        left join aux c1 on e.upp_id = c1.id_original
        left join aux c2 on e.programa_id = c2.id_original
        left join aux c3 on e.ur_id = c3.id_original;
        \");
    end if;

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table aux_2
    select 
        a1.clv_upp,ca.upp,a1.clv_programa,ca.programa,a1.clv_ur,
        case 
            when ca2.ur is null then ''
            else ca2.ur
        end ur,
        nivel,tipo_indicador,padre,resumen_narrativo,nombre_indicador,definicion_indicador,
        metodo_calculo,descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,
        medios_verificacion,meta_anual,trimestre,avance,
        case 
            when avance <= 60 then 0
            when avance > 60 and avance <= 94 then 1
            when avance > 94 and avance <= 110 then 2
            when avance > 110 then 3
        end color
    from aux_1 a1
    left join (
        select distinct 
            clv_upp,upp,clv_programa,programa
        from catalogo_aux
    ) ca on a1.clv_upp = ca.clv_upp and a1.clv_programa = ca.clv_programa
    left join (
        select distinct 
            clv_upp,upp,clv_ur,ur
        from catalogo_aux
    ) ca2 on a1.clv_upp = ca2.clv_upp and a1.clv_ur = ca2.clv_ur;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
        
    set @semaforo := \"\";
    if(semaforo is not null) then set @semaforo := concat(\"where color = \",semaforo); end if;

    set @queri := concat(\"
    select *
    from aux_2 \",@semaforo,\"
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    drop temporary table if exists aux_1;
    drop temporary table if exists catalogo_aux;
    drop temporary table if exists seguimiento;
    drop temporary table if exists aux_2;
END;");

        DB::unprepared("CREATE PROCEDURE mml_presupuesto_egresos(in anio int,in upp_v varchar(3),in ur_v varchar(2),in pp_v varchar(2),in eje_v varchar(1),in corte varchar(13))
begin
    drop temporary table if exists aux_1;
    drop temporary table if exists catalogo_aux;
    drop temporary table if exists completo;
    drop temporary table if exists parte_0;
    drop temporary table if exists parte_1;
    drop temporary table if exists parte_2;
    drop temporary table if exists parte_3;
    drop temporary table if exists parte_4;
    drop temporary table if exists parte_5;
    drop temporary table if exists parte_6;

    set @upp := '';
    set @ur := '';
    set @pp := '';
    set @eje := '';

    if(upp_v is not null) then set @upp := concat(\"where clv_upp = '\",upp_v,\"'\"); end if;
    if(ur_v is not null) then set @ur := concat(\" and clv_ur = '\",ur_v,\"'\"); end if;
    if(pp_v is not null) then 
        if(upp_v is not null) then
            set @pp := concat(\" and clv_programa = '\",pp_v,\"'\");
        else
            set @pp := concat(\"where clv_programa = '\",pp_v,\"'\");
        end if;
    end if;
    if(eje_v is not null) then set @eje := concat(\" and clv_eje = '\",eje_v,\"'\"); end if;

    set @metas := 'metas';
    set @corte := 'deleted_at is null';
    set @mir := 'mml_mir';
    set @id := 'id';
    set @catalogo := 'catalogo';
    set @actividades := 'mml_actividades';
    set @epp := 'epp';
    if(corte is not null) then 
        set @metas := 'metas_hist';
        set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
        set @mir := 'mml_mir_hist';
        set @id := 'id_original';
        set @catalogo := 'catalogo_hist';
        set @actividades := 'mml_actividades_hist';
        set @epp := 'epp_hist';
    end if;
    
    set @queri := concat(\"
    create temporary table aux_1
    select *
    from (
        select 
            case 
                when m.mir_id is not null then mm.clv_upp
                else ma.clv_upp
            end clv_upp,
            case 
                when m.mir_id is not null then mm.clv_ur 
                else substr(ma.entidad_ejecutora,5,2)
            end clv_ur,
            case 
                when m.mir_id is not null then substr(mm.area_funcional,9,2)
                else substr(ma.area_funcional,9,2)
            end clv_programa,
            case 
                when m.mir_id is not null then substr(mm.area_funcional,4,1)
                else substr(ma.area_funcional,4,1)
            end clv_eje,
            case 
                when m.mir_id is not null then substr(mm.area_funcional,5,2)
                else substr(ma.area_funcional,5,2)
            end clv_linea_accion,
            case 
                when m.mir_id is not null then mm.indicador
                when m.actividad_id is not null and ma.id_catalogo is null then ma.nombre
                when m.actividad_id is not null and ma.id_catalogo is not null then c.descripcion
            end actividad,
            m.total programado_anual,
            um.unidad_medida,
            m.cantidad_beneficiarios,
            b.beneficiario
        from \",@metas,\" m
        left join \",@mir,\" mm on m.mir_id = mm.\",@id,\"
        left join \",@actividades,\" ma on m.actividad_id = ma.\",@id,\"
        left join \",@catalogo,\" c on ma.id_catalogo = c.\",@id,\"
        left join unidades_medida um on m.unidad_medida_id = um.id
        left join beneficiarios b on m.beneficiario_id = b.id
        where m.ejercicio = \",anio,\" and m.\",@corte,\"
    )t \",@upp,\"\",@ur,\"\",@pp,\"\",@eje,\";
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table catalogo_aux
    select 
        clv_upp,upp,clv_ur,ur,clv_programa,programa,
        clv_eje,eje,clv_linea_accion,linea_accion
    from v_epp
    where ejercicio = \",anio,\" and deleted_at is null;
    \");
    
    if(corte is not null) then 
        set @queri := concat(\"
        create temporary table catalogo_aux
        with aux as (
            select * from \",@catalogo,\"
            where ejercicio = \",anio,\" and \",@corte,\"
        )
        select 
            c1.clave clv_upp,c1.descripcion upp,
            c2.clave clv_ur,c2.descripcion ur,
            c3.clave clv_programa,c3.descripcion programa,
            c4.clave clv_eje,c4.descripcion eje,
            c5.clave clv_linea_accion,c5.descripcion linea_accion
        from (
            select distinct
                upp_id,ur_id,programa_id,eje_id,linea_accion_id
            from \",@epp,\"
            where ejercicio = \",anio,\" and \",@corte,\"
        ) e
        left join aux c1 on e.upp_id = c1.\",@id,\" and c1.\",@corte,\"
        left join aux c2 on e.ur_id = c2.\",@id,\" and c2.\",@corte,\"
        left join aux c3 on e.programa_id = c3.\",@id,\" and c3.\",@corte,\"
        left join aux c4 on e.eje_id = c4.\",@id,\" and c4.\",@corte,\"
        left join aux c5 on e.linea_accion_id = c5.\",@id,\" and c5.\",@corte,\";
        \");
    end if;

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    create temporary table completo
    with aux as (
        select 
            a1.clv_upp,ca.upp,a1.clv_ur,ca.ur,a1.clv_programa,ca.programa,
            a1.clv_eje,ca.eje,a1.clv_linea_accion,
            case 
                when substr(ca.linea_accion,8,1) = '.' or substr(ca.linea_accion,8,1) = ' '
                then substr(ca.linea_accion,1,7)
                else substr(ca.linea_accion,1,8)
            end clv_cpladem,
            case 
                when substr(ca.linea_accion,8,1) = '.' or substr(ca.linea_accion,8,1) = ' '
                then substr(ca.linea_accion,9,60)
                else substr(ca.linea_accion,11,60)
            end linea_accion,
            actividad,programado_anual,unidad_medida,cantidad_beneficiarios,beneficiario
        from aux_1 a1
        left join catalogo_aux ca on a1.clv_upp = ca.clv_upp and 
        a1.clv_ur = ca.clv_ur and a1.clv_programa = ca.clv_programa and 
        a1.clv_eje = ca.clv_eje and a1.clv_linea_accion = ca.clv_linea_accion
        order by clv_upp,clv_ur,clv_programa,clv_eje,clv_linea_accion
    )
    select 
        clv_upp,upp,clv_ur,ur,clv_programa,programa,clv_eje,eje,
        substr(clv_cpladem,1,3) clv_objetivo_sectorial,mo.objetivo_sectorial,
        substr(clv_cpladem,1,5) clv_estrategia,mo.estrategia,
        concat(substr(clv_cpladem,1,5),'.',clv_linea_accion) clv_linea_accion,
        linea_accion,
        actividad,programado_anual,unidad_medida,cantidad_beneficiarios,beneficiario
    from aux a
    left join mml_objetivo_sectorial_estrategia mo on a.clv_cpladem = mo.clv_cpladem_linea_accion
    and mo.deleted_at is null order by clv_upp,clv_ur,clv_programa,clv_estrategia;
    
    create temporary table parte_0
    select distinct
        clv_upp,upp,clv_ur,ur,clv_programa,programa,
        clv_eje,eje,clv_objetivo_sectorial,objetivo_sectorial,
        clv_estrategia,estrategia,clv_linea_accion,linea_accion
    from completo;
    
    create temporary table parte_1
    select distinct
        clv_upp,upp,clv_ur,ur,clv_programa,programa,
        clv_eje,eje,clv_objetivo_sectorial,objetivo_sectorial,
        clv_estrategia,estrategia
    from completo;
    
    create temporary table parte_2
    select distinct
        clv_upp,upp,clv_ur,ur,clv_programa,programa,
        clv_eje,eje,clv_objetivo_sectorial,objetivo_sectorial
    from parte_1;
    
    create temporary table parte_3
    select distinct
        clv_upp,upp,clv_ur,ur,clv_programa,programa,clv_eje,eje
    from parte_2;
    
    create temporary table parte_4
    select distinct
        clv_upp,upp,clv_ur,ur,clv_programa,programa
    from parte_3;
    
    create temporary table parte_5
    select distinct
        clv_upp,upp,clv_ur,ur
    from parte_4;
    
    create temporary table parte_6
    select distinct clv_upp,upp from parte_5;
    
    with aux as (
        select 
            clv_upp,'' clv_ur,'' clv_programa,'' clv_eje,'' clv_objetivo_sectorial,'' clv_estrategia,'' clv_linea_accion,
            upp denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_6
        union all
        select 
            clv_upp,clv_ur,'' clv_programa,'' clv_eje,'' clv_objetivo_sectorial,'' clv_estrategia,'' clv_linea_accion,
            ur denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_5
        union all
        select 
            clv_upp,clv_ur,clv_programa,'' clv_eje,'' clv_objetivo_sectorial,'' clv_estrategia,'' clv_linea_accion,
            programa denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_4
        union all
        select 
            clv_upp,clv_ur,clv_programa,clv_eje,'' clv_objetivo_sectorial,'' clv_estrategia,'' clv_linea_accion,
            eje denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_3
        union all
        select 
            clv_upp,clv_ur,clv_programa,clv_eje,clv_objetivo_sectorial,'' clv_estrategia,'' clv_linea_accion,
            objetivo_sectorial denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_2
        union all
        select 
            clv_upp,clv_ur,clv_programa,clv_eje,clv_objetivo_sectorial,clv_estrategia,'' clv_linea_accion,estrategia denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_1
        union all
        select 
            clv_upp,clv_ur,clv_programa,clv_eje,clv_objetivo_sectorial,clv_estrategia,clv_linea_accion,
            case 
                when substr(linea_accion,1,1) != ' ' then linea_accion
                else substr(linea_accion,2,70)
            end denominacion,
            '' actividad,'' programado_anual,'' unidad_medida,'' cantidad_beneficiarios,'' beneficiario
        from parte_0
        union all
        select 
            clv_upp,clv_ur,clv_programa,clv_eje,clv_objetivo_sectorial,clv_estrategia,clv_linea_accion,
            linea_accion denominacion,actividad,programado_anual,unidad_medida,cantidad_beneficiarios,beneficiario
        from completo
        order by clv_upp,clv_ur,clv_programa,clv_eje,clv_objetivo_sectorial,clv_estrategia,clv_linea_accion,actividad
    )
    select 
        case 
            when clv_ur != '' then ''
            else clv_upp
        end clv_upp,
        case 
            when clv_programa != '' then ''
            else clv_ur
        end clv_ur,
        case 
            when clv_eje != '' then ''
            else clv_programa
        end clv_programa,
        case 
            when clv_objetivo_sectorial != '' then ''
            else clv_eje
        end clv_eje,
        case 
            when clv_estrategia != '' then ''
            else clv_objetivo_sectorial
        end clv_objetivo_sectorial,
        case 
            when clv_linea_accion != '' then ''
            else clv_estrategia
        end clv_estrategia,
        case 
            when actividad != '' then ''
            else clv_linea_accion
        end clv_linea_accion,
        case 
            when actividad != '' then ''
            else denominacion
        end denominacion,
        actividad,programado_anual,unidad_medida,cantidad_beneficiarios,beneficiario
    from aux;
    
    drop temporary table if exists aux_1;
    drop temporary table if exists catalogo_aux;
    drop temporary table if exists completo;
    drop temporary table if exists parte_0;
    drop temporary table if exists parte_1;
    drop temporary table if exists parte_2;
    drop temporary table if exists parte_3;
    drop temporary table if exists parte_4;
    drop temporary table if exists parte_5;
    drop temporary table if exists parte_6;
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
