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
                        select 
                            c1.clave clv_upp,c1.descripcion upp,
                            c2.clave clv_programa,c2.descripcion programa
                        from (
                            select distinct upp_id,programa_id
                            from epp where ejercicio = ',anio,' and deleted_at is null
                        ) e
                        left join catalogo c1 on upp_id = c1.id
                        left join catalogo c2 on programa_id = c2.id
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

        DB::unprepared("CREATE PROCEDURE avance_etapas_upp_programa(in anio int)
        begin
            select 
                'UPP' tipo,
                count(verde) verde,
                count(amarillo) amarillo,
                count(rojo) rojo,
                (count(verde)+count(amarillo)+count(rojo)) total
            from (
                select 
                    case 
                        when avance = 100 then 'Verde'
                    end verde,
                    case 
                        when avance < 100 and avance >= 70 then 'Amarillo'
                    end amarillo,
                    case 
                        when avance < 70 then 'Rojo'
                    end rojo
                from (
                    select 
                        clv_upp,
                        round((sum(
                            etapa_0+etapa_1+etapa_2+etapa_3+etapa_4+etapa_5
                        )/(6*count(clv_pp)))*100) avance
                    from mml_avance_etapas_pp ma
                    where ma.deleted_at is null and ejercicio = anio
                    group by clv_upp
                )t
            )t2
            union all
            select 
                'Programa' tipo,
                count(verde) verde,
                count(amarillo) amarillo,
                count(rojo) rojo,
                (count(verde)+count(amarillo)+count(rojo)) total
            from (
                select 
                    case 
                        when avance = 100 then 'Verde'
                    end verde,
                    case 
                        when avance < 100 and avance >= 70 then 'Amarillo'
                    end amarillo,
                    case 
                        when avance < 70 then 'Rojo'
                    end rojo
                from (
                    select 
                        clv_pp,
                        round((sum(
                            etapa_0+etapa_1+etapa_2+etapa_3+etapa_4+etapa_5
                        )/(6*count(clv_pp)))*100) avance
                    from mml_avance_etapas_pp ma
                    where ma.deleted_at is null and ejercicio = anio
                    group by clv_pp
                )t
            )t2;
        end;");

        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE calendario_fondo_mensual(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int,in corte date,in uppC varchar(3),in tipo varchar(9))
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @catalogo := 'catalogo';
            set @upp := '';
               set @tipo := '';
        
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            join ',@catalogo,' c on c.clave = pp.upp
            and c.deleted_at is null and c.grupo_id = 6
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
                select *
                from (
                    select 'Sector Público' descripcion, vel.clv_sector_publico clave,vel.sector_publico concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Sector Público Financiero/No Financiero' descripcion, vel.clv_sector_publico_f clave,vel.sector_publico_f concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Sector Economía' descripcion, vel.clv_sector_economia clave,vel.sector_economia concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Subsector Economía' descripcion,vel.clv_subsector_economia clave,vel.subsector_economia concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Ente Público' descripcion,vel.clv_ente_publico clave,vel.ente_publico concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Entidad Federativa' descripcion,vcg.clv_entidad_federativa clave,vcg.entidad_federativa concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
                    select 'Región' descripcion,vcg.clv_region clave,vcg.region concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
                    select 'Municipio' descripcion,vcg.clv_municipio clave,vcg.municipio concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
                    select 'Localidad' descripcion,vcg.clv_localidad clave,vcg.localidad concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
                    select 'Unidad Programática Presupuestal' descripcion,vel.clv_upp clave,vel.upp concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Subsecretaría' descripcion,vel.clv_subsecretaria clave,vel.subsecretaria concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Unidad Responsable' descripcion,vel.clv_ur clave,vel.ur concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Finalidad' descripcion,vel.clv_finalidad clave,vel.finalidad concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Función' descripcion,vel.clv_funcion clave,vel.funcion concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Subfunción' descripcion,vel.clv_subfuncion clave,vel.subfuncion concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Eje' descripcion,vel.clv_eje clave,vel.eje concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Linea de Acción' descripcion,vel.clv_linea_accion clave,vel.linea_accion concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Programa Sectorial' descripcion,vel.clv_programa_sectorial clave,vel.programa_sectorial concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Tipología General' descripcion,vel.clv_tipologia_conac clave,vel.clv_tipologia_conac concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Programa Presupuestal' descripcion,vel.clv_programa clave,vel.programa concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Subprograma Presupuestal' descripcion,vel.clv_subprograma clave,vel.subprograma concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Proyecto Presupuestal' descripcion,vel.clv_proyecto clave,vel.proyecto concepto from v_epp_llaves vel where ejercicio = \",anio,\" and vel.llave like '\",@epp,\"' union all
                    select 'Mes de Afectación' descripcion,substring('\",@clave,\"',38,6) clave, 'Mes de Afectación' union all
                    select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
                    select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
                    select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
                    select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
                    select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
                    select 'Año (Fondo del Ramo)' descripcion,substring('\",@clave,\"',50,2) clave, 'Año' concepto union all
                    select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like '\",@fondo,\"' union all
                    select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like '\",@fondo,\"' union all
                    select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like '\",@fondo,\"' union all
                    select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like '\",@fondo,\"' union all
                    select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like '\",@fondo,\"' union all
                    select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where deleted_at is null and po.clv_proyecto_obra like '\",@obra,\"'
                ) tabla;
            \");
            
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");

        DB::unprepared("CREATE PROCEDURE corte_anual_no_pp(in anio_act int,in usuario varchar(45))
        begin
            set @anio := anio_act;
            set @deleted_at := now();
            
            set @version := (select case when max(version) is null then 1 else (max(version)+1) end
                from programacion_presupuesto_hist where ejercicio = @anio);
            
            #mml_arbol_objetivos_hist
            insert into mml_arbol_objetivos_hist(
                id_original,version,problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,calificacion_id,seleccion_mir,
                tipo_indicador,ejercicio,created_user,updated_user,created_at,updated_at,deleted_at,ramo33)
            select 
                id,@version,problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,calificacion_id,seleccion_mir,
                tipo_indicador,ejercicio,created_user,updated_user,created_at,updated_at,@deleted_at,ramo33
            from mml_arbol_objetivos ma
            where ma.ejercicio = @anio and deleted_at is null;
            
            #mml_arbol_problema_hist
            insert into mml_arbol_problema_hist(
                id_original,version,problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,ejercicio,
                created_user,updated_user,created_at,updated_at,deleted_at,ramo33)
            select 
                id,@version,problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,ejercicio,
                created_user,updated_user,created_at,updated_at,@deleted_at,ramo33
            from mml_arbol_problema ma
            where ma.ejercicio = @anio and deleted_at is null;
            
            #mml_definicion_problema_hist
            insert into mml_definicion_problema_hist(
                id_original,version,clv_upp,clv_pp,poblacion_objetivo,descripcion,magnitud,necesidad_atender,delimitacion_geografica,
                region,municipio,localidad,problema_central,objetivo_central,comentarios_upp,ejercicio,
                created_user,updated_user,created_at,updated_at,deleted_at,ramo33)
            select 
                id,@version,clv_upp,clv_pp,poblacion_objetivo,descripcion,magnitud,necesidad_atender,delimitacion_geografica,
                region,municipio,localidad,problema_central,objetivo_central,comentarios_upp,ejercicio,
                created_user,updated_user,created_at,updated_at,@deleted_at,ramo33
            from mml_definicion_problema
            where ejercicio = @anio and deleted_at is null;
            
            #mml_mir
            insert into mml_mir_hist(
                id_original,version,entidad_ejecutora,area_funcional,clv_upp,clv_ur,clv_pp,nivel,id_epp,componente_padre,objetivo,indicador,
                definicion_indicador,metodo_calculo,descripcion_metodo,tipo_indicador,unidad_medida,dimension,
                comportamiento_indicador,frecuencia_medicion,medios_verificacion,lb_valor_absoluto,lb_valor_relativo,lb_anio,
                lb_periodo_i,lb_periodo_f,mp_valor_absoluto,mp_valor_relativo,mp_anio,mp_anio_meta,mp_periodo_i,mp_periodo_f,
                supuestos,estrategias,ejercicio,created_user,updated_user,created_at,updated_at,deleted_at,ramo33)
            select 
                id,@version,entidad_ejecutora,area_funcional,clv_upp,clv_ur,clv_pp,nivel,id_epp,componente_padre,objetivo,indicador,
                definicion_indicador,metodo_calculo,descripcion_metodo,tipo_indicador,unidad_medida,dimension,
                comportamiento_indicador,frecuencia_medicion,medios_verificacion,lb_valor_absoluto,lb_valor_relativo,lb_anio,
                lb_periodo_i,lb_periodo_f,mp_valor_absoluto,mp_valor_relativo,mp_anio,mp_anio_meta,mp_periodo_i,mp_periodo_f,
                supuestos,estrategias,ejercicio,created_user,updated_user,created_at,updated_at,@deleted_at,ramo33
            from mml_mir mm
            where ejercicio = @anio and deleted_at is null;
            
            #mml_actividades
            insert into mml_actividades_hist(
                id_original,version,clv_upp,entidad_ejecutora,area_funcional,id_catalogo,nombre,ejercicio,
                created_user,updated_user,deleted_user,created_at,updated_at,deleted_at)
            select 
                id,@version,clv_upp,entidad_ejecutora,area_funcional,id_catalogo,nombre,ejercicio,
                created_user,updated_user,usuario,created_at,updated_at,@deleted_at
            from mml_actividades
            where ejercicio = @anio and deleted_at is null;
            
            #metas
            insert into metas_hist(
                id_original,version,clv_actividad,clv_fondo,mir_id,actividad_id,tipo_meta,tipo,beneficiario_id,unidad_medida_id,cantidad_beneficiarios,
                enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,estatus,ejercicio,
                created_user,created_at,updated_user,updated_at,deleted_user,deleted_at)
            select 
                id,@version,clv_actividad,clv_fondo,mir_id,actividad_id,tipo_meta,tipo,beneficiario_id,unidad_medida_id,cantidad_beneficiarios,
                enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,estatus,ejercicio,
                created_user,created_at,updated_user,updated_at,usuario,@deleted_at
            from metas
            where ejercicio = @anio and deleted_at is null;
        END;");

        DB::unprepared("CREATE PROCEDURE estatus_movimientos(in anio int,in mes_n int)
        begin
            drop temporary table if exists metas_area;
            drop temporary table if exists movimientos;
            drop temporary table if exists programacion;
            drop temporary table if exists ext_mov;
            drop temporary table if exists ext_pro;
            set @anio := anio;
            set @mes := mes_n;
        
            set @c := (select count(*) from programacion_presupuesto where ejercicio = anio);
            set @tabla := '';
            
            if(@c > 0) then
                set @tabla := 'programacion_presupuesto';
            else
                set @tabla := 'programacion_presupuesto_hist';
            end if;
            
            create temporary table metas_area
            select distinct
                case 
                    when m.mir_id is not null then mm.clv_upp
                    else ma.clv_upp
                end clv_upp,
                case 
                    when m.mir_id is not null then mm.clv_ur
                    else ma.clv_ur
                end clv_ur,
                case 
                    when m.mir_id is not null then mm.area_funcional
                    else ma.area_funcional
                end area_funcional,
                m.clv_fondo 
            from metas m
            left join mml_mir mm on m.mir_id = mm.id
            left join mml_actividades ma on m.actividad_id = ma.id
            where m.ejercicio = @anio and m.deleted_at is null
            order by clv_upp,clv_ur,area_funcional,clv_fondo;
            
            create temporary table movimientos
            select *
            from (
                select 
                    id,clv_upp,clv_ur,area_funcional,
                    substr(fondo,7,2) clv_fondo
                from sapp_movimientos sm
                where ejercicio = @anio and mes = @mes
            )t order by clv_upp,clv_ur,area_funcional,clv_fondo;
            
            set @queri := concat(\"
            create temporary table programacion
            select
                id,upp clv_upp,ur clv_ur,
                concat(
                    finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,
                    tipologia_conac,programa_presupuestario,subprograma_presupuestario,
                    proyecto_presupuestario
                )area_funcional,
                fondo_ramo clv_fondo
            from \",@tabla,\"
            where ejercicio = \",@anio,\" and deleted_at is null
            order by clv_upp,clv_ur,area_funcional,clv_fondo;
            \");
        
            create temporary table ext_mov(
                id int not null,
                clv_upp varchar(3),
                clv_ur varchar(2),
                area_funcional varchar(16),
                clv_fondo varchar(2),
                upp varchar(3),
                existe tinyint,
                primary key (id)
            );
        
            create temporary table ext_pro(
                id int not null,
                clv_upp varchar(3),
                clv_ur varchar(2),
                area_funcional varchar(16),
                clv_fondo varchar(2),
                upp varchar(3),
                existe tinyint,
                primary key (id)
            );
            
            insert into ext_mov
            select 
                m.*,ma.clv_upp upp,
                case 
                    when ma.clv_upp is null then 0
                    else 1
                end existe
            from movimientos m
            left join metas_area ma on ma.clv_upp = m.clv_upp and ma.clv_ur = m.clv_ur
            and ma.area_funcional = m.area_funcional and ma.clv_fondo = m.clv_fondo
            order by existe,clv_upp,clv_ur,area_funcional,clv_fondo;
            
            insert into ext_pro
            select 
                m.*,ma.clv_upp upp,
                case 
                    when ma.clv_upp is null then 0
                    else 1
                end existe
            from programacion m
            left join metas_area ma on ma.clv_upp = m.clv_upp and ma.clv_ur = m.clv_ur
            and ma.area_funcional = m.area_funcional and ma.clv_fondo = m.clv_fondo
            order by existe,clv_upp,clv_ur,area_funcional,clv_fondo;
            
            update sapp_movimientos set estatus = 2 where id in (select id from ext_mov where existe = 0);
            update sapp_movimientos set estatus = 1 where id in (select id from ext_mov where existe = 1);
            
            update programacion_presupuesto set estatus_sapp = 2 where id in (select id from ext_pro where existe = 0);
            update programacion_presupuesto set estatus_sapp = 1 where id in (select id from ext_pro where existe = 1);
        
            drop temporary table if exists metas_area;
            drop temporary table if exists movimientos;
            drop temporary table if exists programacion;
            drop temporary table if exists ext_mov;
            drop temporary table if exists ext_pro;
        END;");

        DB::unprepared("CREATE PROCEDURE inicio_a(in anio int)
        begin
            select
                sum(presupuesto_asignado) presupuesto_asignado,
                sum(presupuesto_calendarizado) presupuesto_calendarizado,
                sum(presupuesto_asignado) - sum(presupuesto_calendarizado) as disponible,
                truncate((sum(presupuesto_calendarizado) / sum(presupuesto_asignado)) * 100,2) as avance,
                ejercicio
            FROM (
                select 
                    sum(presupuesto) as presupuesto_asignado,
                    0 as presupuesto_calendarizado,
                    ejercicio
                from techos_financieros
                where deleted_at is null and ejercicio = anio
                group by ejercicio
                union all
                select 
                    0 as presupuesto_asignado,
                    sum(total) as presupuesto_calendarizado,
                    ejercicio
                from programacion_presupuesto
                where deleted_at is null and ejercicio = anio
                group by ejercicio
            )t 
            group by ejercicio;
        end;");

        DB::unprepared("CREATE PROCEDURE inicio_b(in anio int)
        begin
            select 
                clv_fondo clave,
                f.fondo_ramo fondo,
                asignado,
                programado,
                (programado/asignado)*100 avance,
                ejercicio
            from (
                select 
                    asig.clv_fondo,
                    case 
                        when asignado is null then 0
                        else asignado
                        end asignado,
                    case 
                        when programado is null then 0
                        else programado
                    end programado,
                    asig.ejercicio
                from (
                    select 
                        clv_fondo,
                        sum(presupuesto) asignado,
                        tf.ejercicio
                    from techos_financieros tf
                    where deleted_at is null and ejercicio = anio
                    group by clv_fondo,ejercicio
                ) asig
                left join (
                    select 
                        fondo_ramo clv_fondo,
                        sum(total) programado,
                        ejercicio
                    from programacion_presupuesto
                    where deleted_at is null and ejercicio = anio
                    group by clv_fondo,ejercicio
                )prog 
                on asig.clv_fondo = prog.clv_fondo and asig.ejercicio = prog.ejercicio
                order by ejercicio,clv_fondo
            )t2 
            left join fondo f on t2.clv_fondo = f.clv_fondo_ramo and f.deleted_at is null;
        end;");

        DB::unprepared("CREATE PROCEDURE insert_pp_aplanado(in anio int)
        begin
            delete
            from pp_identificadores
            where id not in (
                select id 
                from programacion_presupuesto pp
                union all 
                select id
                from programacion_presupuesto_hist
            );
        
            drop temporary table if exists temp_pp;
            create temporary table temp_pp(
                id_aux int not null,
                upp varchar(3),
                subsecretaria varchar(1),
                ur varchar(2),
                finalidad varchar(1),
                funcion varchar(1),
                subfuncion varchar(1),
                eje varchar(1),
                linea_accion varchar(2),
                programa_sectorial varchar(1),
                tipologia_conac varchar(1),
                programa_presupuestario varchar(2),
                subprograma_presupuestario varchar(3),
                proyecto_presupuestario varchar(3),
                id_epp int,
                primary key(id_aux)
            );
                
            drop temporary table if exists aux_epp;
            create temporary table aux_epp(
                id int not null,
                id_aux int not null,
                epp varchar(22),
                primary key(id)
            );
                
            drop temporary table if exists temp_clasgeo;
            create temporary table temp_clasgeo(
                id_aux int not null,
                region varchar(2),
                municipio varchar(3),
                localidad varchar(3),
                id_clasgeo int,
                primary key (id_aux)
            );
                
            drop temporary table if exists aux_clasgeo;
            create temporary table aux_clasgeo(
                id int not null,
                id_aux int not null,
                clasgeo varchar(8),
                primary key(id)
            );
                
            drop temporary table if exists temp_partida;
            create temporary table temp_partida(
                id_aux int not null,
                capitulo varchar(1),
                concepto varchar(1),
                partida_generica varchar(1),
                partida_especifica varchar(2),
                tipo_gasto varchar(1),
                id_partida int,
                primary key (id_aux)
            );
                
            drop temporary table if exists aux_partida;
            create temporary table aux_partida(
                id int not null,
                id_aux int not null,
                partida varchar(6),
                primary key(id)
            );
                
            drop temporary table if exists temp_fondo;
            create temporary table temp_fondo(
                id_aux int not null,
                etiquetado varchar(1),
                fuente_financiamiento varchar(1),
                ramo varchar(2),
                fondo_ramo varchar(2),
                capital varchar(1),
                id_fondo int,
                primary key(id_aux)
            );
                
            drop temporary table if exists aux_fondo;
            create temporary table aux_fondo(
                id int not null,
                id_aux int not null,
                fondo varchar(7),
                primary key(id)
            );
                
            set @id_aux := 0;
            set @p_aux := '' COLLATE utf8mb4_unicode_ci;
            set @aux := 0;
            insert into aux_epp
            select
                id,
                id_aux,
                epp
            from (
                select 
                    id,
                    case 
                        when @p_aux != epp then @id_aux := @id_aux + 1
                        else @id_aux
                    end id_aux,
                    case 
                        when @p_aux != epp then @p_aux := epp
                        else @p_aux
                    end p_aux,
                    epp
                from (
                    select 
                        id,
                        (@aux := @aux + 1) aux,
                        concat(
                            pp.upp,
                            pp.subsecretaria,
                            pp.ur,
                            pp.finalidad,
                            pp.funcion,
                            pp.subfuncion,
                            pp.eje,
                            pp.linea_accion,
                            pp.programa_sectorial,
                            pp.tipologia_conac,
                            pp.programa_presupuestario,
                            pp.subprograma_presupuestario,
                            pp.proyecto_presupuestario
                        )epp
                    from programacion_presupuesto pp
                    where pp.ejercicio = anio and pp.id not in (
                        select id from pp_identificadores
                    )
                    order by epp
                ) t
            ) tabla;
                
            insert into temp_pp
            select distinct
                id_aux,
                substring(epp,1,3) upp,
                substring(epp,4,1) subsecretaria,
                substring(epp,5,2) ur,
                substring(epp,7,1) finalidad,
                substring(epp,8,1) funcion,
                substring(epp,9,1) subfuncion,
                substring(epp,10,1) eje,
                substring(epp,11,2) linea_accion,
                substring(epp,13,1) programa_sectorial,
                substring(epp,14,1) tipologia_conac,
                substring(epp,15,2) programa,
                substring(epp,17,3) subprograma,
                substring(epp,20,3) proyecto,
                null id_epp
            from aux_epp;
                
            set @id_aux := 0;
            set @p_aux := '' COLLATE utf8mb4_unicode_ci;
            set @aux := 0;
            insert into aux_clasgeo
            select 
                id,
                id_aux,
                clasgeo
            from (
                select 
                    id,
                    case 
                        when @p_aux != clasgeo then @id_aux := @id_aux + 1
                        else @id_aux
                    end id_aux,
                    case 
                        when @p_aux != clasgeo then @p_aux := clasgeo
                        else @p_aux
                    end p_aux,
                    clasgeo
                from (
                    select 
                        id,
                        (@aux := @aux + 1) aux,
                        concat(
                            region,
                            municipio,
                            localidad
                        ) clasgeo
                    from programacion_presupuesto pp
                    where pp.ejercicio = anio and pp.id not in (
                        select id from pp_identificadores
                    )
                    order by clasgeo
                ) t
            ) tabla;
                
            insert into temp_clasgeo
            select distinct
                id_aux,
                substring(clasgeo,1,2),
                substring(clasgeo,3,3),
                substring(clasgeo,6,3),
                null id_clasgeo
            from aux_clasgeo;
                
            set @id_aux := 0;
            set @p_aux := '' COLLATE utf8mb4_unicode_ci;
            set @aux := 0;
            insert into aux_partida
            select 
                id,
                id_aux,
                partida
            from (
                select 
                    id,
                    case 
                        when @p_aux != partida then @id_aux := @id_aux + 1
                        else @id_aux
                    end id_aux,
                    case 
                        when @p_aux != partida then @p_aux := partida
                        else @p_aux
                    end p_aux,
                    partida
                from (
                    select 
                        id,
                        (@aux := @aux + 1) aux,
                        concat(
                            posicion_presupuestaria,
                            tipo_gasto
                        )partida
                    from programacion_presupuesto pp
                    where pp.ejercicio = anio and pp.id not in (
                        select id from pp_identificadores
                    )
                    order by partida,id
                ) t
            ) tabla;
                
            insert into temp_partida
            select distinct
                id_aux,
                substring(partida,1,1),
                substring(partida,2,1),
                substring(partida,3,1),
                substring(partida,4,2),
                substring(partida,6,1),
                null id_partida
            from aux_partida;
                
            set @id_aux := 0;
            set @p_aux := '' COLLATE utf8mb4_unicode_ci;
            set @aux := 0;
            insert into aux_fondo
            select 
                id,
                id_aux,
                fondo
            from (
                select 
                    id,
                    case 
                        when @p_aux != fondo then @id_aux := @id_aux + 1
                        else @id_aux
                    end id_aux,
                    case 
                        when @p_aux != fondo then @p_aux := fondo
                        else @p_aux
                    end p_aux,
                    fondo
                from (
                    select 
                        id,
                        (@aux := @aux + 1) aux,
                        concat(
                            etiquetado,
                            fuente_financiamiento,
                            ramo,
                            fondo_ramo,
                            capital
                        ) fondo
                    from programacion_presupuesto pp
                    where pp.ejercicio = anio and pp.id not in (
                        select id from pp_identificadores
                    )
                    order by fondo
                ) t
            ) tabla;
                
            insert into temp_fondo
            select distinct
                id_aux,
                substring(fondo,1,1),
                substring(fondo,2,1),
                substring(fondo,3,2),
                substring(fondo,5,2),
                substring(fondo,7,1),
                null id_fondo
            from aux_fondo;
                
            insert into pp_identificadores
            select 
                id,
                sum(id_epp),
                sum(id_clasgeo),
                sum(id_partida),
                sum(id_fondo),
                sum(id_obra)
            from (
                select 
                    ep.id,
                    t.id id_epp,
                    0 id_clasgeo,
                    0 id_partida,
                    0 id_fondo,
                    0 id_obra
                from (
                    select 
                        pp.id_aux,
                        ve.id
                    from temp_pp pp
                    join v_epp ve
                        on ve.clv_upp = pp.upp 
                        and ve.clv_subsecretaria = pp.subsecretaria 
                        and ve.clv_ur = pp.ur
                        and ve.clv_finalidad = pp.finalidad 
                        and ve.clv_funcion = pp.funcion 
                        and ve.clv_subfuncion = pp.subfuncion 
                        and ve.clv_eje = pp.eje
                        and ve.clv_linea_accion = pp.linea_accion 
                        and ve.clv_programa_sectorial = pp.programa_sectorial 
                        and ve.clv_tipologia_conac = pp.tipologia_conac 
                        and ve.clv_programa = pp.programa_presupuestario 
                        and ve.clv_subprograma = pp.subprograma_presupuestario 
                        and ve.clv_proyecto = pp.proyecto_presupuestario
                    where ve.ejercicio = anio
                ) t
                left join aux_epp ep on t.id_aux = ep.id_aux
                union all
                select
                    cg.id,
                    0 id_epp,
                    t.id id_clasgeo,
                    0 id_partida,
                    0 id_fondo,
                    0 id_obra
                from (
                    select 
                        tc.id_aux,
                        cg.id
                    from temp_clasgeo tc
                    join clasificacion_geografica cg
                        on tc.region = cg.clv_region
                        and tc.municipio = cg.clv_municipio 
                        and tc.localidad = cg.clv_localidad
                    where cg.deleted_at is null
                ) t
                left join aux_clasgeo cg on t.id_aux = cg.id_aux
                union all
                select 
                    ap.id,
                    0 id_epp,
                    0 id_clasgeo,
                    t.id id_partida,
                    0 id_fondo,
                    0 id_obra
                from (
                    select 
                        tp.id_aux,
                        pp.id
                    from temp_partida tp
                    join posicion_presupuestaria pp
                        on tp.capitulo = pp.clv_capitulo
                        and tp.concepto = pp.clv_concepto
                        and tp.partida_generica = pp.clv_partida_generica
                        and tp.partida_especifica = pp.clv_partida_especifica
                        and tp.tipo_gasto = pp.clv_tipo_gasto
                    where pp.deleted_at is null
                ) t
                left join aux_partida ap on t.id_aux = ap.id_aux
                union all
                select 
                    af.id,
                    0 id_epp,
                    0 id_clasgeo,
                    0 id_partida,
                    t.id id_fondo,
                    0 id_obra
                from (
                    select 
                        tf.id_aux,
                        f.id
                    from temp_fondo tf
                    join fondo f 
                        on tf.etiquetado = f.clv_etiquetado 
                        and tf.fuente_financiamiento = f.clv_fuente_financiamiento 
                        and tf.ramo = f.clv_ramo 
                        and tf.fondo_ramo = f.clv_fondo_ramo 
                        and tf.capital = f.clv_capital
                    where deleted_at is null
                ) t
                left join aux_fondo af on t.id_aux = af.id_aux
                union all 
                select 
                    pp.id,
                    0 id_epp,
                    0 id_clasgeo,
                    0 id_partida,
                    0 id_fondo,
                    po.id id_obra
                from programacion_presupuesto pp
                join proyectos_obra po on pp.proyecto_obra = po.clv_proyecto_obra
                where pp.ejercicio = anio and pp.id not in (
                    select id from pp_identificadores
                )
            ) tabla
            group by id;
                
            drop temporary table if exists temp_pp;
            drop temporary table if exists aux_epp;
            drop temporary table if exists temp_clasgeo;
            drop temporary table if exists aux_clasgeo;
            drop temporary table if exists temp_partida;
            drop temporary table if exists aux_partida;
            drop temporary table if exists temp_fondo;
            drop temporary table if exists aux_fondo;
        END;");

        DB::unprepared("CREATE PROCEDURE lista_upp(in tipo int)
        begin
            if tipo = 0 then
                select
                    clave clv_upp,
                    descripcion upp,
                    null fecha_baja
                from catalogo c 
                where grupo_id = 6 and deleted_at is null;
            elseif tipo = 1  then
                select #SOLO INACTIVOS
                    clave clv_upp,
                    descripcion upp, 
                    DATE_FORMAT(deleted_at, '%Y-%m-%d') fecha_baja
                from catalogo c 
                where grupo_id = 6 and deleted_at is not null;
            else 
                select 
                    clave clv_upp,
                    descripcion upp, 
                    DATE_FORMAT(deleted_at, '%Y-%m-%d') fecha_baja
                from catalogo c 
                where grupo_id = 6;
            end if;
        END;");

        DB::unprepared("CREATE PROCEDURE llenado_cierres_etapas(in tipo int)
        begin
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
        
            insert into mml_avance_etapas_pp(clv_upp,clv_pp,etapa_0,etapa_1,etapa_2,etapa_3,etapa_4,etapa_5,estatus,ejercicio,created_user,updated_user,deleted_user,created_at,updated_at,deleted_at)
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
                null deleted_at
            from v_epp ve
            where ejercicio = @anio
            and presupuestable = 1;
                    
            insert into sapp_cierre_ejercicio(
                clv_upp,enero,febrero,marzo,trimestre_uno,abril,mayo,junio,trimestre_dos,
                julio,agosto,septiembre,trimestre_tres,octubre,noviembre,diciembre,trimestre_cuatro,
                ejercicio,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user,id_usuario
            )
            select 
                clave clv_upp,
                0 enero,0 febrero,0 marzo,0 trimestre_uno,
                0 abril,0 mayo,0 junio,0 trimestre_dos,
                0 julio,0 agosto,0 septiembre,0 trimestre_tres,
                0 octubre,0 noviembre,0 diciembre,0 trimestre_cuatro,
                @anio,now(),now(),null,'SISTEMA',null,null,0
            from catalogo 
            where ejercicio = @anio and 
            deleted_at is null and grupo_id = 6
            order by clv_upp;
        END;");

        DB::unprepared("CREATE PROCEDURE llenado_nuevo_anio(in anio int)
        begin
            create temporary table rel_faltantes(
                id int not null auto_increment,
                clave varchar(30) not null,
                grupo varchar(100) not null,
                primary key (id)
            );
            
            insert into rel_faltantes(clave,grupo)
            select distinct
                concat(ea.clv_sector_publico,ea.clv_sector_publico_f,
                ea.clv_sector_economia,ea.clv_subsector_economia,ea.clv_ente_publico) clave,
                'rel_economica_administrativa' grupo
            from epp_aux ea
            where concat(ea.clv_sector_publico,ea.clv_sector_publico_f,
                ea.clv_sector_economia,ea.clv_subsector_economia,
                ea.clv_ente_publico) not in (select distinct rea.clasificacion_administrativa 
                    from rel_economica_administrativa rea
                    where rea.deleted_at is null);
                
            insert into rel_faltantes(clave,grupo)
            select distinct
                concat(clv_upp,\" \",clv_subsecretaria,\" \",clv_ur) clave,
                \"entidad_ejecutora\" grupo
            from epp_aux ea
            where concat(ea.clv_upp,ea.clv_subsecretaria,ea.clv_ur
            ) not in (select distinct 
                concat(ve.clv_upp,ve.clv_subsecretaria,ve.clv_ur) ee
                from v_entidad_ejecutora ve
            where ve.deleted_at is null);
        
            insert into rel_faltantes(clave,grupo)
            select distinct
                ea.clv_linea_accion clave,
                'sector_linea_accion' grupo
            from epp_aux ea
            where ea.clv_linea_accion not in (
                select distinct
                    c.clave 
                from sector_linea_accion sla 
                join catalogo c on sla.linea_accion_id = c.id
                where sla.deleted_at is null
            );
        
            insert into rel_faltantes(clave,grupo)
            select 
                clave,
                grupo
            from (
                select distinct
                    concat(ea.clv_tipologia_conac,\" \",ea.tipologia_conac) clave,
                    \"tipologia_conac\" grupo
                from epp_aux ea
                where concat(ea.clv_tipologia_conac,\" \",ea.tipologia_conac) not in (
                    select distinct
                        concat(tc.clave_conac,\" \",tc.descripcion_conac) tipologia
                    from tipologia_conac tc
                    where tc.deleted_at is null and tc.clave_conac is not null)
            )t where substr(clave,3,200) not in (
                select distinct
                    substr(tc.descripcion,1,200) tipologia
                from tipologia_conac tc
                where tc.deleted_at is null);
            
            insert into rel_faltantes(clave,grupo)
            select distinct
                ea.clv_linea_accion clave,
                'mml_objetivo_sectorial_estrategia' grupo
            from epp_aux ea
            where concat(
                substr(ea.linea_accion,1,7),
                replace(substr(ea.linea_accion,8,1),'.','')
            ) not in (
                select 
                    clv_cpladem_linea_accion
                from mml_objetivo_sectorial_estrategia mo
                where deleted_at is null
            );
                    
            set @filas := (select count(*) from rel_faltantes);
            if( @filas > 0) then 
                select * from rel_faltantes;
            else
                update epp_aux a
                left join catalogo c on a.clv_sector_publico = c.clave and a.sector_publico = c.descripcion and c.grupo_id = 1 
                and c.deleted_at is null
                set a.id_sector_publico = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_sector_publico_f = c.clave and a.sector_publico_f = c.descripcion and c.grupo_id = 2 
                and c.deleted_at is null
                set a.id_sector_publico_f = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_sector_economia = c.clave and a.sector_economia = c.descripcion and c.grupo_id = 3 
                and c.deleted_at is null
                set a.id_sector_economia = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_subsector_economia = c.clave and a.subsector_economia = c.descripcion and c.grupo_id = 4 
                and c.deleted_at is null
                set a.id_subsector_economia = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_ente_publico = c.clave and a.ente_publico = c.descripcion and c.grupo_id = 5 
                and c.deleted_at is null
                set a.id_ente_publico = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_upp = c.clave and a.upp = c.descripcion and c.grupo_id = 6 
                and c.deleted_at is null
                set a.id_upp = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_subsecretaria = c.clave and a.subsecretaria = c.descripcion and c.grupo_id = 7
                and c.deleted_at is null
                set a.id_subsecretaria = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_ur = c.clave and a.ur = c.descripcion and c.grupo_id = 8
                and c.deleted_at is null
                set a.id_ur = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_finalidad = c.clave and a.finalidad = c.descripcion and c.grupo_id = 9
                and c.deleted_at is null
                set a.id_finalidad = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_funcion = c.clave and a.funcion = c.descripcion and c.grupo_id = 10
                and c.deleted_at is null
                set a.id_funcion = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_subfuncion = c.clave and a.subfuncion = c.descripcion and c.grupo_id = 11
                and c.deleted_at is null
                set a.id_subfuncion = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_eje = c.clave and a.eje = c.descripcion and c.grupo_id = 12
                and c.deleted_at is null
                set a.id_eje = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_linea_accion = c.clave and a.linea_accion = c.descripcion and c.grupo_id = 13
                and c.deleted_at is null
                set a.id_linea_accion = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_programa_sectorial = c.clave and a.programa_sectorial = c.descripcion and c.grupo_id = 14
                and c.deleted_at is null
                set a.id_programa_sectorial = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_tipologia_conac = c.clave and a.tipologia_conac = c.descripcion and c.grupo_id = 15
                and c.deleted_at is null
                set a.id_tipologia_conac = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_programa = c.clave and a.programa = c.descripcion and c.grupo_id = 16
                and c.deleted_at is null
                set a.id_programa = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_subprograma = c.clave and a.subprograma = c.descripcion and c.grupo_id = 17
                and c.deleted_at is null
                set a.id_subprograma = c.id;
                
                update epp_aux a
                left join catalogo c on a.clv_proyecto = c.clave and a.proyecto = c.descripcion and c.grupo_id = 18
                and c.deleted_at is null
                set a.id_proyecto = c.id;
                
                insert into rel_faltantes(clave,grupo) select ea.clv_sector_publico clave,clv_sector_publico grupo from epp_aux ea where ea.id_sector_publico is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_sector_publico_f clave,clv_sector_publico_f grupo from epp_aux ea where ea.id_sector_publico_f is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_sector_economia clave,clv_sector_economia grupo from epp_aux ea where ea.id_sector_economia is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_subsector_economia clave,clv_subsector_economia grupo from epp_aux ea where ea.id_subsector_economia is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_ente_publico clave,clv_ente_publico grupo from epp_aux ea where ea.id_ente_publico is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_upp clave,clv_upp grupo from epp_aux ea where ea.id_upp is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_subsecretaria clave,clv_subsecretaria grupo from epp_aux ea where ea.id_subsecretaria is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_ur clave,clv_ur grupo from epp_aux ea where ea.id_ur is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_finalidad clave,clv_finalidad grupo from epp_aux ea where ea.id_finalidad is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_funcion clave,clv_funcion grupo from epp_aux ea where ea.id_funcion is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_subfuncion clave,clv_subfuncion grupo from epp_aux ea where ea.id_subfuncion is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_eje clave,clv_eje grupo from epp_aux ea where ea.id_eje is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_linea_accion clave,clv_linea_accion grupo from epp_aux ea where ea.id_linea_accion is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_programa_sectorial clave,clv_programa_sectorial grupo from epp_aux ea where ea.id_programa_sectorial is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_tipologia_conac clave,clv_tipologia_conac grupo from epp_aux ea where ea.id_tipologia_conac is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_programa  clave,clv_programa grupo from epp_aux ea where ea.id_programa is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_subprograma clave,clv_subprograma grupo from epp_aux ea where ea.id_subprograma is null;
                insert into rel_faltantes(clave,grupo) select ea.clv_proyecto clave,clv_proyecto grupo from epp_aux ea where ea.id_proyecto is null;
            
                set @filas := (select count(*) from rel_faltantes);
                if( @filas > 0) then 
                    select * from rel_faltantes;
                else
                    set @id := (select max(id) from epp);
                    if (@id is null) then set @id := 0; end if;
                    insert into epp(id,sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,
                        upp_id,subsecretaria_id,ur_id,
                        finalidad_id,funcion_id,subfuncion_id,eje_id,linea_accion_id,programa_sectorial_id,tipologia_conac_id,
                        programa_id,subprograma_id,proyecto_id,
                        ejercicio,presupuestable,confirmado,created_at,updated_at,deleted_at,deleted_user,updated_user,created_user)
                    select 
                        (@id := @id+1) id,id_sector_publico,id_sector_publico_f,id_sector_economia,id_subsector_economia,id_ente_publico,
                        id_upp,id_subsecretaria,id_ur,
                        id_finalidad,id_funcion,id_subfuncion,id_eje,id_linea_accion,id_programa_sectorial,id_tipologia_conac,
                        id_programa,id_subprograma,id_proyecto,
                        anio,1,1,now(),now(),null,null,null,'SISTEMA'
                    from epp_aux ea;
                
                update epp set presupuestable = 0 where programa_id in (
                    select 
                        id
                    from catalogo c
                    where c.clave in ('RM','5H') and deleted_at is null and c.grupo_id = 16
                );
                end if;
            end if;
            
            drop temporary table rel_faltantes;
            delete from epp_aux;
        END;");

        DB::unprepared("CREATE PROCEDURE mml_alineacion(in anio int,in trimestre_n int,in semaforo int,in corte date)
        begin
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists mir_metas;
            drop temporary table if exists estrategia_ods;
            drop temporary table if exists plan_desarrollo;
            drop temporary table if exists seguimiento;
            drop temporary table if exists t_final;
        
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            set @mir := 'mml_mir';
            set @metas := 'metas';
        
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
                set @mir := 'mml_mir_hist';
                set @metas := 'metas_hist';
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
            where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"
            group by meta_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
            set @queri := concat(\"
            create temporary table catalogo_aux
            with aux as (
                select distinct
                    upp_id,programa_id
                from \",@epp,\" e
                where ejercicio = \",anio,\" and \",@corte,\"
            )
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_programa,c2.descripcion programa
            from aux 
            left join \",@catalogo,\" c1 on aux.upp_id = c1.\",@id,\"
            left join \",@catalogo,\" c2 on aux.programa_id = c2.\",@id,\";
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
         
            set @queri := concat(\"
                create temporary table mir_metas
                select
                    t.clv_upp,ca.upp,t.clv_programa,ca.programa,t.clv_ur,c1.descripcion ur,nivel,
                    tipo_indicador,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
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
                            when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
                        end avance,
                        mm.id_epp
                    from \",@mir,\" mm
                    left join unidades_medida um on mm.unidad_medida = um.id
                    left join \",@metas,\" m on m.mir_id = mm.\",@id,\" and m.deleted_at is null
                    left join sapp_seguimiento ss on ss.meta_id = m.\",@id,\"
                    where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                    order by clv_upp,clv_programa,clv_ur,nivel
                )t
                left join catalogo_aux ca on t.clv_upp = ca.clv_upp and t.clv_programa = ca.clv_programa
                left join \",@epp,\" e on t.id_epp = e.\",@id,\"
                left join \",@catalogo,\" c1 on e.ur_id = c1.\",@id,\"
                left join \",@catalogo,\" c2 on e.eje_id = c2.\",@id,\"
                left join \",@catalogo,\" c3 on e.linea_accion_id = c3.\",@id,\"
                order by clv_upp,clv_programa,clv_ur,nivel;
            \");
        
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
                    clv_upp,upp,clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,objetivo,
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
                order by clv_upp,clv_programa,clv_ur,nivel
            )
            select 
                clv_upp,upp,clv_programa,programa,clv_ur,
                case 
                    when ur is null then ''
                    else ur
                end ur,
                nivel,tipo_indicador,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
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
            order by clv_upp,clv_programa,clv_ur,nivel
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
        end;");

        DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int,in corte date)
        begin
            set @upp := '';
            set @upp2 := '';
            set @programa := '';
            set @programa2 := '';
            set @ur := '';
            set @ur2 := '';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            set @mir := 'mml_mir';
            
            if(upp is not null) then 
                set @upp := CONCAT('and mm.clv_upp = \"',upp,'\"'); 
                set @upp2 := CONCAT('where clv_upp = \"',upp,'\"'); 
            end if;
            if(programa is not null) then
                set @programa := CONCAT('and mm.clv_pp = \"',programa,'\"'); 
                if(upp is not null) then
                    set @programa2 := CONCAT('and clv_programa = \"',programa,'\"'); 
                else
                    set @programa2 := CONCAT('where clv_programa = \"',programa,'\"'); 
                end if;
            end if;
            if(ur is not null) then 
                set @ur := CONCAT('and mm.clv_ur = \"',ur,'\"'); 
                set @ur2 := CONCAT('and clv_ur = \"',ur,'\"'); 
            end if;
            if(corte is not null) then 
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
                set @mir := 'mml_mir_hist';
            end if;
                    
            set @queri := concat(\"
                select
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
                            mm.id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            c.descripcion proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from \",@mir,\" mm
                        join \",@epp,\" ve on ve.\",@id,\" = mm.id_epp
                        left join \",@catalogo,\" c on ve.proyecto_id = c.id
                        where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                        and nivel in (10) \",@upp,\" \",@ur,\" \",@programa,\"
                        union all 
                        select
                            mm.componente_padre id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            c.descripcion proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from \",@mir,\" mm
                        join \",@epp,\" ve on ve.\",@id,\" = mm.id_epp
                        left join \",@catalogo,\" c on ve.proyecto_id = c.\",@id,\"
                        where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                        and nivel in (11) \",@upp,\" \",@ur,\" \",@programa,\"
                        union all
                        select * from (
                            select 
                                0 id,c1.clave clv_upp,c2.clave clv_pp,c3.clave clv_ur,
                                '' area_funcional,'' proyecto,9 nivel,'' objetivo,'' indicador
                            from (
                                select distinct
                                    upp_id,programa_id,ur_id
                                from \",@epp,\"
                                where ejercicio = \",anio,\" and \",@corte,\"
                            )t
                            left join \",@catalogo,\" c1 on t.upp_id = c1.\",@id,\"
                            left join \",@catalogo,\" c2 on t.programa_id = c2.\",@id,\"
                            left join \",@catalogo,\" c3 on t.ur_id = c3.\",@id,\"
                        ) ve \",@upp2,\"\",@programa2,\"\",@ur2,\"
                    )t 
                    group by clv_upp,clv_pp,clv_ur,id,nivel
                    order by clv_upp,clv_pp,clv_ur,id,nivel
                )t2;
            \");
             
            prepare stmt  from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int,in corte date)
        begin
            drop temporary table if exists aux_1;
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists epp_aux;
            drop temporary table if exists seguimiento;
            drop temporary table if exists aux_2;
        
            set @trimestre := '';
            if(trimestre_n = 1) then set @trimestre := '(1,2,3)'; end if;
            if(trimestre_n = 2) then set @trimestre := '(1,2,3,4,5,6)'; end if;
            if(trimestre_n = 3) then set @trimestre := '(1,2,3,4,5,6,7,8,9)'; end if;
            if(trimestre_n = 4) then set @trimestre := '(1,2,3,4,5,6,7,8,9,10,11,12)'; end if;
        
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            set @epp := 'epp';
            set @mir := 'mml_mir';
            set @metas := 'metas';
            if(corte is not null) then 
                set @catalogo := 'catalogo_hist';
                set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @id := 'id_original';
                set @epp := 'epp_hist';
                set @mir := 'mml_mir_hist';
                set @metas := 'metas_hist';
            end if;
            
            set @queri := concat(\"
            create temporary table seguimiento
            select 
                meta_id,
                sum(realizado) realizado
            from sapp_seguimiento ss 
            where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"
            group by meta_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
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
                    when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
                end avance
            from \",@mir,\" mm
            left join unidades_medida um on mm.unidad_medida = um.id
            left join \",@metas,\" m on mm.\",@id,\" = m.mir_id
            left join sapp_seguimiento ss on ss.meta_id = m.\",@id,\"
            where mm.ejercicio = \",anio,\" and mm.\",@corte,\";
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat(\"
            create temporary table catalogo_aux
            select 
                \",@id,\" id,clave clv_programa,descripcion programa
            from \",@catalogo,\" 
            where ejercicio = \",anio,\" and \",@corte,\" and grupo_id in (16);
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat(\"
            create temporary table epp_aux
            with aux as (
                select distinct
                    upp_id,
                    ur_id
                from \",@epp,\"
                where ejercicio = \",anio,\" and \",@corte,\"
            )
            select 
                c1.clave clv_upp, c1.descripcion upp,
                c2.clave clv_ur, c2.descripcion ur
            from aux a
            left join \",@catalogo,\" c1 on c1.\",@id,\" = a.upp_id
            left join \",@catalogo,\" c2 on c2.\",@id,\" = a.ur_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat(\"
            create temporary table aux_2
            select 
                clv_upp,upp,clv_programa,programa,clv_ur,
                case 
                    when ur is null then ''
                    else ur
                end ur,
                nivel,tipo_indicador,resumen_narrativo,
                nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,frecuencia_medicion,
                unidad_medida,dimension,medios_verificacion,meta_anual,trimestre,avance,
                case 
                    when avance <= 60 then 0
                    when avance > 60 and avance <= 94 then 1
                    when avance > 94 and avance <= 110 then 2
                    when avance > 110 then 3
                end color
            from (
                select
                    a1.clv_upp,c1.upp,a1.clv_programa,ca.programa,a1.clv_ur,ea.ur,a1.nivel,tipo_indicador,resumen_narrativo,
                    nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,frecuencia_medicion,
                    unidad_medida,dimension,medios_verificacion,meta_anual,trimestre,avance
                from aux_1 a1
                left join catalogo_aux ca on a1.clv_programa = ca.clv_programa
                left join epp_aux ea on a1.clv_upp = ea.clv_upp and a1.clv_ur = ea.clv_ur
                left join (
                    select clave clv_upp,descripcion upp
                    from \",@catalogo,\" where ejercicio = \",anio,\"
                    and \",@corte,\" and grupo_id = 6
                ) c1 on a1.clv_upp = c1.clv_upp
            )t
            order by clv_upp,clv_programa,clv_ur,nivel;
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
            drop temporary table if exists epp_aux;
            drop temporary table if exists seguimiento;
            drop temporary table if exists aux_2;
        end;");

        DB::unprepared("CREATE PROCEDURE mml_presupuesto_egresos(in anio int,in upp_v varchar(3),in ur_v varchar(2),in pp_v varchar(2),in eje_v varchar(1),in corte date)
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
            with aux as (
                select distinct
                    e.upp_id,e.ur_id,e.programa_id,e.eje_id,e.linea_accion_id
                from \",@epp,\" e
                where e.ejercicio = \",anio,\" and e.\",@corte,\"
            )
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_ur,c2.descripcion ur,
                c3.clave clv_programa,c3.descripcion programa,
                c4.clave clv_eje,c4.descripcion eje,
                c5.clave clv_linea_accion,c5.descripcion linea_accion
            from aux a
            left join \",@catalogo,\" c1 on a.upp_id = c1.\",@id,\"
            left join \",@catalogo,\" c2 on a.ur_id = c2.\",@id,\"
            left join \",@catalogo,\" c3 on a.programa_id = c3.\",@id,\"
            left join \",@catalogo,\" c4 on a.eje_id = c4.\",@id,\"
            left join \",@catalogo,\" c5 on a.linea_accion_id = c5.\",@id,\";
            \");
        
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

        DB::unprepared("CREATE PROCEDURE presupuesto_sap(in anio int)
        begin
            set @conse := 0;
        
            select 
                (@conse:=@conse+1) fila,
                t.*
            from (
                select 
                    concat(
                        entidad_federativa,
                        region,
                        pp.municipio,
                        pp.localidad,
                        pp.upp,
                        pp.subsecretaria,
                        pp.ur
                    ) centro_gestor,
                    concat(
                        pp.posicion_presupuestaria,
                        pp.tipo_gasto
                    ) posPre,
                    concat(
                        pp.anio,
                        pp.etiquetado,
                        pp.fuente_financiamiento,
                        pp.ramo,
                        pp.fondo_ramo,
                        pp.capital
                    ) fondo,
                    concat(
                        pp.finalidad,
                        pp.funcion,
                        pp.subfuncion,
                        pp.eje,
                        pp.linea_accion,
                        pp.programa_sectorial,
                        pp.tipologia_conac,
                        pp.programa_presupuestario,
                        pp.subprograma_presupuestario,
                        pp.proyecto_presupuestario
                    ) area_funcional,
                    pp.proyecto_obra,
                    enero,febrero,marzo,abril,mayo,
                    junio,julio,agosto,septiembre,
                    octubre,noviembre,diciembre
                from programacion_presupuesto pp 
                where deleted_at is null and ejercicio = anio
                order by centro_gestor,posPre,fondo
            )t
            order by fila;
        end;");

        DB::unprepared("CREATE PROCEDURE proyecto_avance_general(in anio int,in corte date)
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
                    from programacion_presupuesto pa
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
            and c.ejercicio = ',anio,' and c.deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE proyecto_calendario_actividades(in anio int,in upp varchar(3),in corte date,in tipo varchar(9))
        begin
            set @upp := '';
            set @corte := 'deleted_at is null';
            set @tabla := 'metas';
            set @catalogo := 'catalogo';
            set @tipo := '';
        
            if(upp is not null) then set @upp := concat(\"where clv_upp = '\",upp,\"'\"); end if;
            if(tipo is not null) then set @tipo := concat('and m.tipo_meta = \"',tipo,'\"'); end if;
            if(corte is not null) then
                set @tabla := 'metas_hist';
                set @catalogo := 'catalogo_hist';
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
            join mml_actividades ma on m.actividad_id = ma.id
            join unidades_medida u2 on m.unidad_medida_id = u2.id
            left join \",@catalogo,\" c on c.clave = substring(ma.area_funcional,11,3)
            and c.ejercicio = \",anio,\" and c.deleted_at is null and c.grupo_id = 20
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
            join mml_mir mm on m.mir_id = mm.id
            where m.mir_id is not null \",@tipo,\" and m.ejercicio = \",anio,\" and m.deleted_at is NULL;
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
            and c.ejercicio = ',anio,' and c.grupo_id = 6 and c.deleted_at is null;
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_II(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @catalogo := 'catalogo';
            set @epp := 'epp';
            set @deleted := 'and e.deleted_at is null';
            set @id := 'id';
            set @metas := 'metas';
            set @mir := 'mml_mir';
        
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @catalogo := 'catalogo_hist';
                set @epp := 'epp_hist';
                set @deleted := CONCAT('e.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
            join ',@mir,' mm on m.mir_id = mm.id and mm.tipo_indicador = 13
            left JOIN (
                SELECT 
                    mm.clv_upp,
                    mm.clv_pp,
                    mm.indicador
                FROM ',@mir,' mm
                WHERE ejercicio = ',anio,' AND deleted_at IS NULL AND nivel = 8
            ) mm2 ON mm.clv_upp = mm2.clv_upp AND mm.clv_pp = mm2.clv_pp
            where m.mir_id is not null and m.deleted_at is null and m.ejercicio = ',anio,'
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
            left join (
                select distinct 
                    c1.clave clv_upp,c1.descripcion upp,
                    c2.clave clv_programa,c2.descripcion programa,
                    c3.clave clv_subprograma,c3.descripcion subprograma
                from ',@epp,' e 
                left join ',@catalogo,' c1 on e.upp_id = c1.',@id,'
                left join ',@catalogo,' c2 on e.programa_id = c2.',@id,'
                left join ',@catalogo,' c3 on e.subprograma_id = c3.',@id,'
                where e.ejercicio = ',anio,' ',@deleted,'
            ) ve on a0.clv_upp = ve.clv_upp and a0.clv_programa = ve.clv_programa
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_III(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'and deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('and deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            
            set @queri := concat('
            create temporary table aux_0
            with aux as (
                select distinct
                    upp_id,ur_id,programa_id,subprograma_id,proyecto_id
                from ',@epp,'
                where ejercicio = ',anio,' ',@corte,'
            )
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_ur,c2.descripcion ur,
                c3.clave clv_programa,c3.descripcion programa,
                c4.clave clv_subprograma,c4.descripcion subprograma,
                c5.clave clv_proyecto,c5.descripcion proyecto
            from aux a
            left join ',@catalogo,' c1 on upp_id = c1.',@id,'
            left join ',@catalogo,' c2 on ur_id = c2.',@id,'
            left join ',@catalogo,' c3 on programa_id = c3.',@id,'
            left join ',@catalogo,' c4 on subprograma_id = c4.',@id,'
            left join ',@catalogo,' c5 on proyecto_id = c5.',@id,';
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_IX(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'and deleted_at is null';
            set @catalogo := 'catalogo';
            set @metas := 'metas';
            set @mir := 'mml_mir';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('and deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @catalogo := 'catalogo_hist';
                set @metas := 'metas_hist';
                set @mir := 'mml_mir_hist';
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
            join ',@mir,' mm on m.mir_id = mm.id and mm.tipo_indicador = 13
            left join (
                select 
                    mm.clv_upp,
                    mm.clv_pp,
                    mm.indicador
                from ',@mir,' mm
                where ejercicio = ',anio,' AND deleted_at IS NULL AND nivel = 8
            ) mm2 on mm.clv_upp = mm2.clv_upp AND mm.clv_pp = mm2.clv_pp
            where m.mir_id is not null and m.deleted_at is null and m.ejercicio = ',anio,'
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
            where ejercicio = ',anio,' ',@corte,'
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
                where ejercicio = ',anio,' ',@corte,'
            ) ve on a0.clv_programa = ve.clv_programa;
            ');
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            create temporary table aux_3
            select distinct 
                clv_programa,programa
            from aux_2 a2;
        
            select 
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            set @c_catalogo := 'and c.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @c_catalogo := CONCAT('c.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
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
                    where c.ejercicio = ',anio,' ',@c_catalogo,'
                    and c.deleted_at is null and c.grupo_id = 6
                    group by c.clave,c.descripcion
                    order by clv_upp
                )t;
            ');
        
            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto pp';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist pp';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
            end if;
        
            set @query := CONCAT('
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
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_3(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_2(in anio int,in corte date)
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
                    
            set @query := CONCAT('
            create temporary table aux_0
            select distinct
                c0.clave clv_finalidad,
                c0.descripcion finalidad,
                c1.clave clv_funcion,
                c1.descripcion funcion
            from ',@epp,' e
            join ',@catalogo,' c0 on e.finalidad_id = c0.',@id,'
            join ',@catalogo,' c1 on e.funcion_id = c1.',@id,'
            where e.ejercicio = ',anio,' and e.',@corte,';
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_3(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_4(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_5(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_6(in anio int,in corte date)
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
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_1(in anio int, in corte date)
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
                where c.deleted_at is null and c.ejercicio = ',anio,' and c.grupo_id = 6
                union all
                select 
                    2 etiquetado,
                    c.clave clv_upp,
                    c.descripcion upp,
                    pp.total
                from ',@catalogo,' c 
                left join ',@tabla,' pp on c.clave = pp.upp
                and pp.',@corte,' and pp.ejercicio = ',anio,' and pp.etiquetado = 2
                where c.deleted_at is null and c.ejercicio = ',anio,' and c.grupo_id = 6
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_10(in anio int, in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_1(in anio int, in corte date)
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
            from (
                with aux as (
                    select distinct
                        upp_id,subsecretaria_id,ur_id
                    from \",@epp,\" 
                    where ejercicio = \",anio,\" and \",@corte,\"
                )
                select 
                    c1.clave clv_upp,c1.descripcion upp,
                    c2.clave clv_subsecretaria,c2.descripcion subsecretaria,
                    c3.clave clv_ur,c3.descripcion ur
                from aux a
                left join \",@catalogo,\" c1 on a.upp_id = c1.\",@id,\"
                left join \",@catalogo,\" c2 on a.subsecretaria_id = c2.\",@id,\"
                left join \",@catalogo,\" c3 on a.ur_id = c3.\",@id,\"
            ) ve
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @clasificacion := 'clasificacion_geografica';
            set @anio_cg := '';
            set @catalogo := 'catalogo';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @clasificacion := 'clasificacion_geografica_hist';
                set @anio_cg := concat('and cg.ejercicio = ',anio);
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
                left join ',@clasificacion,' cg on a.clv_region = cg.clv_region
                and a.clv_municipio = cg.clv_municipio and a.clv_localidad = cg.clv_localidad
                and cg.deleted_at is null ',@anio_cg,'
                left join ',@catalogo,' c on a.clv_upp = c.clave and c.grupo_id = 6
                and c.ejercicio = ',anio,' and c.deleted_at is null
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_3(in anio int, in corte date)
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
                where c.ejercicio = ',anio,' and c.deleted_at is null and c.grupo_id = 12
                group by c.clave,c.descripcion;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_4(in anio int, in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_5(in anio int, in corte date)
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
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_programa,c2.descripcion programa
            from (
                select distinct upp_id,programa_id
                from ',@epp,' where ejercicio = ',anio,' and ',@corte,'
            )t
            left join ',@catalogo,' c1 on t.upp_id = c1.',@id,'
            left join ',@catalogo,' c2 on t.programa_id = c2.',@id,';
            ');
        
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_6(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
        
            set @query := CONCAT('
            create temporary table aux_0
            select distinct
                c0.clave clv_finalidad,
                c0.descripcion finalidad,
                c1.clave clv_funcion,
                c1.descripcion funcion,
                c2.clave clv_subfuncion,
                c2.descripcion subfuncion
            from ',@epp,' e
            join ',@catalogo,' c0 on e.finalidad_id = c0.',@id,'
            join ',@catalogo,' c1 on e.funcion_id = c1.',@id,'
            join ',@catalogo,' c2 on e.subfuncion_id = c2.',@id,'
            where e.ejercicio = ',anio,' and e.deleted_at is null;
            ');
        
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_7(in anio int, in corte date)
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
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
            drop temporary table if exists aux_4;
        
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
            left join (
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
                    from ',@epp,' where ejercicio = ',anio,' and ',@corte,'
                ) e
                left join ',@catalogo,' c1 on upp_id = c1.',@id,'
                left join ',@catalogo,' c2 on ur_id = c2.',@id,'
                left join ',@catalogo,' c3 on finalidad_id = c3.',@id,'
                left join ',@catalogo,' c4 on funcion_id = c4.',@id,'
                left join ',@catalogo,' c5 on subfuncion_id = c5.',@id,'
            ) ve on a.upp = ve.clv_upp and a.ur = ve.clv_ur
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_8(in anio int, in corte date)
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
            left join (
                select 
                    c1.clave clv_upp,c1.descripcion upp,
                    c2.clave clv_programa,c2.descripcion programa
                from (
                    select distinct upp_id,programa_id
                    from \",@epp,\" where ejercicio = \",anio,\" and \",@corte,\"
                ) e
                left join \",@catalogo,\" c1 on e.upp_id = c1.\",@id,\"
                left join \",@catalogo,\" c2 on e.programa_id = c2.\",@id,\"
            ) a1 on a0.clv_upp = a1.clv_upp and a0.clv_programa = a1.clv_programa
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
                    
            set @query := CONCAT('
            create temporary table aux_0
            select distinct
                c0.clave clv_finalidad,
                c0.descripcion finalidad,
                c1.clave clv_funcion,
                c1.descripcion funcion
            from ',@epp,' e
            join ',@catalogo,' c0 on e.finalidad_id = c0.',@id,'
            join ',@catalogo,' c1 on e.funcion_id = c1.',@id,'
            where e.ejercicio = ',anio,' and e.deleted_at is null;
            ');
        
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_3(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
                    
            set @query := CONCAT('
            create temporary table aux_0
            select distinct
                c0.clave clv_finalidad,
                c0.descripcion finalidad,
                c1.clave clv_funcion,
                c1.descripcion funcion
            from ',@epp,' e
            join ',@catalogo,' c0 on e.finalidad_id = c0.',@id,'
            join ',@catalogo,' c1 on e.funcion_id = c1.',@id,'
            where e.ejercicio = ',anio,' and e.',@corte,';
            ');
        
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_4(in anio int, in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_5(in anio int, in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_presupuesto_2(in anio int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
        
            set @queri := concat(\"
            select 
                t.*,
                case 
                    when programado = 0 then (realizado*100)
                    else TRUNCATE(((realizado/programado)*100),2)
                end avance,
                (realizado-programado) diferencia
            from (
                select 
                    ss.mes mes_n,ss.clv_upp,ss.clv_programa,
                    case 
                        when ss.mes = 1 then 'Enero'
                        when ss.mes = 2 then 'Febrero'
                        when ss.mes = 3 then 'Marzo'
                        when ss.mes = 4 then 'Abril'
                        when ss.mes = 5 then 'Mayo'
                        when ss.mes = 6 then 'Junio'
                        when ss.mes = 7 then 'Julio'
                        when ss.mes = 8 then 'Agosto'
                        when ss.mes = 9 then 'Septiembre'
                        when ss.mes = 10 then 'Octubre'
                        when ss.mes = 11 then 'Noviembre'
                        when ss.mes = 12 then 'Diciembre'
                    end mes,
                    case 
                        when ss.mes = 1 then sum(m.enero)
                        when ss.mes = 2 then sum(m.febrero)
                        when ss.mes = 3 then sum(m.marzo)
                        when ss.mes = 4 then sum(m.abril)
                        when ss.mes = 5 then sum(m.mayo)
                        when ss.mes = 6 then sum(m.junio)
                        when ss.mes = 7 then sum(m.julio)
                        when ss.mes = 8 then sum(m.agosto)
                        when ss.mes = 9 then sum(m.septiembre)
                        when ss.mes = 10 then sum(m.octubre)
                        when ss.mes = 11 then sum(m.noviembre)
                        when ss.mes = 12 then sum(m.diciembre)
                    end programado,
                    sum(ss.realizado) realizado
                from sapp_seguimiento ss 
                join metas m on ss.meta_id = m.id
                where ss.ejercicio = \",anio,\" and ss.deleted_at is null\",@upp,\"\",@programa,\"
                group by ss.clv_upp,ss.clv_programa,ss.mes
                order by clv_upp,clv_programa,mes_n,programado
            )t;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_resumen_por_capitulo_y_partida(in anio int,in corte date)
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

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_1(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(1))
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

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_2(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(6))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';
        
            if(upp_v is not null) then 
                set @upp := concat(\"where clv_upp = '\",upp_v,\"'\");
            end if;
            if(ur_v is not null) then 
                set @ur := concat(\" and clv_ur = '\",ur_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            if(fondo_v is not null) then 
                set @fondo := concat(\" and clv_fondo = '\",fondo_v,\"'\");
            end if;
            if(capitulo_v is not null) then 
                if(upp_v is not null) then
                    set @capitulo := concat(\" and clv_partida = '\",capitulo_v,\"'\");
                else
                    set @capitulo := concat(\"where clv_partida = '\",capitulo_v,\"'\");
                end if;
            end if;
        
            set @queri := concat(\"
            with aux as (
                select 
                    clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,mes_n,actividad,unidad_medida,beneficiarios,
                    mes,programado,realizado,(realizado-programado) diferencia,0 avance,
                    justificacion,propuesta_mejora
                from (
                    select 
                        ss.clv_upp,ss.clv_ur,ss.clv_programa,m.clv_fondo,rm.partida clv_partida,ss.mes mes_n,
                        case 
                            when m.mir_id is not null then mm.indicador
                            when m.actividad_id is not null then ma.nombre
                            else c.descripcion
                        end actividad,
                        um.unidad_medida,
                        concat(m.cantidad_beneficiarios,' ',b.beneficiario) beneficiarios,
                        case 
                            when ss.mes = 1 then 'Enero'
                            when ss.mes = 2 then 'Febrero'
                            when ss.mes = 3 then 'Marzo'
                            when ss.mes = 4 then 'Abril'
                            when ss.mes = 5 then 'Mayo'
                            when ss.mes = 6 then 'Junio'
                            when ss.mes = 7 then 'Julio'
                            when ss.mes = 8 then 'Agosto'
                            when ss.mes = 9 then 'Septiembre'
                            when ss.mes = 10 then 'Octubre'
                            when ss.mes = 11 then 'Noviembre'
                            when ss.mes = 12 then 'Diciembre'
                        end mes,
                        case 
                            when ss.mes = 1 then m.enero
                            when ss.mes = 2 then m.febrero
                            when ss.mes = 3 then m.marzo
                            when ss.mes = 4 then m.abril
                            when ss.mes = 5 then m.mayo
                            when ss.mes = 6 then m.junio
                            when ss.mes = 7 then m.julio
                            when ss.mes = 8 then m.agosto
                            when ss.mes = 9 then m.septiembre
                            when ss.mes = 10 then m.octubre
                            when ss.mes = 11 then m.noviembre
                            when ss.mes = 12 then m.diciembre
                        end programado,
                        ss.realizado,
                        ss.justificacion,
                        ss.propuesta_mejora
                    from sapp_seguimiento ss
                    left join metas m on ss.meta_id = m.id
                    left join sapp_rel_metas_partidas rm on ss.meta_id = rm.meta_id
                    left join mml_actividades ma on m.actividad_id = ma.id
                    left join mml_mir mm on m.mir_id = mm.id
                    left join catalogo c on ma.id_catalogo = c.id
                    left join beneficiarios b on m.beneficiario_id = b.id
                    left join unidades_medida um on m.unidad_medida_id = um.id
                    where ss.ejercicio = \",anio,\" and ss.deleted_at is null
                    order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,actividad,mes
                )t \",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"
            )
            select *
            from (
                select distinct 
                    clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,0 mes_n,actividad,unidad_medida,beneficiarios,
                    '' mes,'' programado,'' realizado,'' diferencia,'' avance,'' justificacion,'' propuesta_mejora
                from aux
                union all
                select * from aux
                order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,actividad,mes_n
            )t;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE sapp_ingresos(in anio int,in mes int,in upp varchar(3),in programa varchar(2),in fondo varchar(2),in capitulo varchar(2))
        begin
            drop temporary table if exists t_capitulo;
            drop temporary table if exists t_fondo;
            drop temporary table if exists t_programa;
            drop temporary table if exists t_upp;
        
            set @upp := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';
            set @mes := '';
            if(upp is not null) then
                set @upp := concat(\" and clv_upp = \",upp);
            end if;
            if(programa is not null) then
                set @programa := concat(\" and clv_programa = \",programa);
            end if;
            if(fondo is not null) then
                set @fondo := concat(\" and substr(fondo,7,2) = \",fondo);
            end if;
            if(capitulo is not null) then
                set @capitulo := concat(\" and substr(partida,1,1) = \",capitulo);
            end if;
            if(mes is not null) then
                set @mes := concat(\" and mes = \",mes);
            end if;
        
            set @queri := concat(\"
            create temporary table t_capitulo
            select 
                clv_upp,c1.descripcion upp,clv_programa,c2.descripcion programa,
                clv_fondo,f2.fondo_ramo fondo,concat(f.clv_capitulo,'000') clv_capitulo,pp.capitulo,
                original,reducciones,modificado,comprometido,devengado
            from (
                select 
                    clv_upp,clv_programa,clv_fondo,clv_capitulo,
                    sum(original_sapp) original,
                    sum(reduccion) reducciones,
                    sum(modificado) modificado,
                    sum(comprometido) comprometido,
                    sum(devengado) devengado
                from (
                    select 
                        clv_upp,clv_programa,
                        substr(fondo,7,2) clv_fondo,
                        substr(partida,1,1) clv_capitulo,
                        original_sapp,reduccion,modificado,comprometido,devengado
                    from sapp_movimientos 
                    where ejercicio = \",anio,\"\",@mes,\"\",@upp,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"
                )t
                group by clv_upp,clv_programa,clv_fondo,clv_capitulo
            )f
            left join catalogo c1 on f.clv_upp = c1.clave and 
            c1.grupo_id = 6 and c1.ejercicio = \",anio,\" and c1.deleted_at is null
            left join catalogo c2 on f.clv_programa = c2.clave and 
            c2.grupo_id = 16 and c2.ejercicio = \",anio,\" and c2.deleted_at is null
            left join fondo f2 on f.clv_fondo = f2.clv_fondo_ramo and f2.deleted_at is null
            left join (
                select distinct
                    clv_capitulo,capitulo
                from posicion_presupuestaria
                where deleted_at is null
            ) pp on f.clv_capitulo = pp.clv_capitulo
            order by clv_upp,clv_programa,clv_fondo,clv_capitulo;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table t_fondo
            select 
                clv_upp,tc.upp,clv_programa,tc.programa,clv_fondo,tc.fondo,
                sum(original) original_f,
                sum(reducciones) reducciones_f,
                sum(modificado) modificado_f,
                sum(comprometido) comprometido_f,
                sum(devengado) devengado_f
            from t_capitulo tc
            group by clv_upp,tc.upp,clv_programa,tc.programa,clv_fondo,tc.fondo;
            
            create temporary table t_programa
            select 
                clv_upp,tf.upp,clv_programa,tf.programa,
                sum(original_f) original_p,
                sum(reducciones_f) reducciones_p,
                sum(modificado_f) modificado_p,
                sum(comprometido_f) comprometido_p,
                sum(devengado_f) devengado_p
            from t_fondo tf
            group by clv_upp,tf.upp,clv_programa,tf.programa;
            
            create temporary table t_upp
            select 
                clv_upp,tp.upp,
                sum(original_p) original_u,
                sum(reducciones_p) reducciones_u,
                sum(modificado_p) modificado_u,
                sum(comprometido_p) comprometido_u,
                sum(devengado_P) devengado_u
            from t_programa tp
            group by clv_upp,tp.upp;
            
            select 
                tu.*,
                case 
                    when modificado = 0 then 0
                    else truncate(((devengado_u/modificado_u)*100),2)
                end cumplimiento_u,
                tp.clv_programa,tp.programa,
                original_p,reducciones_p,modificado_p,comprometido_p,devengado_p,
                case 
                    when modificado_p = 0 then 0
                    else truncate(((devengado_p/modificado_p)*100),2)
                end cumplimiento_p,
                tf.clv_fondo,tf.fondo,
                original_f,reducciones_f,modificado_f,comprometido_f,devengado_f,
                case 
                    when modificado_f = 0 then 0
                    else truncate(((devengado_f/modificado_f)*100),2)
                end cumplimiento_f,
                clv_capitulo,tc.capitulo,
                original,reducciones,modificado,comprometido,devengado,
                case 
                    when modificado = 0 then 0
                    else truncate(((devengado/modificado)*100),2)
                end cumplimiento
            from t_capitulo tc
            left join t_fondo tf on tc.clv_upp = tf.clv_upp and
            tc.clv_programa = tf.clv_programa and tc.clv_fondo = tf.clv_fondo
            left join t_programa tp on tc.clv_upp = tp.clv_upp
            and tc.clv_programa = tp.clv_programa
            left join t_upp tu on tc.clv_upp = tu.clv_upp
            order by clv_upp,clv_programa,clv_fondo,clv_capitulo;
            
            drop temporary table if exists t_capitulo;
            drop temporary table if exists t_fondo;
            drop temporary table if exists t_programa;
            drop temporary table if exists t_upp;
        end;");

        DB::unprepared("CREATE PROCEDURE sapp_reporte_calendario(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in subprograma_v varchar(3),in proyecto_v varchar(3))
        begin
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
        
            set @upp := ''; 		set @upp2 := '';
            set @ur := '';			set @ur2 := '';
            set @programa := '';	set @programa2 := '';
            set @subprograma := ''; set @subprograma2 := '';
            set @proyecto := '';	set @proyecto2 := '';
        
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
            
            set @queri := concat(\"
            create temporary table aux_1
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
                    enero,febrero,marzo,abril,mayo,
                    junio,julio,agosto,septiembre,
                    octubre,noviembre,diciembre
                from metas m
                left join mml_mir mm on m.mir_id = mm.id
                left join mml_actividades ma on m.actividad_id = ma.id
                left join catalogo c on ma.id_catalogo = c.id
                left join beneficiarios b on m.beneficiario_id = b.id
                left join unidades_medida um on m.unidad_medida_id = um.id
                where m.ejercicio = \",anio,\" and m.deleted_at is null
            )t2\",@upp,\"\",@ur,\"\",@programa,\"\",@subprograma,\"\",@proyecto,\";
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat(\"
            create temporary table aux_2
            with temp as (
                with aux as (
                    select 
                        c1.clave clv_upp,c1.descripcion upp,
                        c2.clave clv_ur,c2.descripcion ur,
                        c3.clave clv_programa,c3.descripcion programa,
                        c4.clave clv_subprograma,c4.descripcion subprograma,
                        c5.clave clv_proyecto,c5.descripcion proyecto
                    from (
                        select distinct
                            e.upp_id,e.ur_id,e.programa_id,
                            e.subprograma_id,e.proyecto_id
                        from epp e
                        where e.ejercicio = \",anio,\" and e.deleted_at is null
                    )t
                    join catalogo c1 on t.upp_id = c1.id
                    join catalogo c2 on t.ur_id = c2.id
                    join catalogo c3 on t.programa_id = c3.id
                    join catalogo c4 on t.subprograma_id = c4.id
                    join catalogo c5 on t.proyecto_id = c5.id
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
        
            create temporary table aux_3
            select 
                clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,descripcion,monto,
                '' actividad,'' beneficiarios,'' unidades_medida,
                0 enero,0 febrero,0 marzo,0 abril,0 mayo,0 junio,0 julio,
                0 agosto,0 septiembre,0 octubre,0 noviembre,0 diciembre
            from aux_2
            union all
            select 
                clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,
                '' descripcion,0 monto,actividad,beneficiarios,unidades_medida,
                enero,febrero,marzo,abril,mayo,junio,julio,
                agosto,septiembre,octubre,noviembre,diciembre
            from aux_1
            order by clv_upp,clv_ur,clv_programa,clv_subprograma,
            clv_proyecto,actividad;
            
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
                enero,febrero,marzo,abril,mayo,junio,julio,agosto,
                septiembre,octubre,noviembre,diciembre
            from aux_3;
            
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
        end;");

        DB::unprepared("CREATE PROCEDURE sapp_reporte_presupuesto_1(in anio int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            
            set @queri := concat(\"
            with aux as (
                select 
                    t.clv_upp,t.clv_programa,
                    concat(t.clv_upp,' ',c1.descripcion) upp,
                    concat(t.clv_programa,' ',c2.descripcion) programa,
                    t.original,t.ampliacion,t.modificado,t.comprometido,
                    t.devengado,t.ejercido
                from (
                    select 
                        sm.clv_upp,sm.clv_programa,sum(sm.original_sapp) original,
                        case 
                            when sum(sm.ampliacion) > 0 then sum(sm.ampliacion)
                            when sum(sm.reduccion) > 0 then sum(sm.reduccion)
                            else 0
                        end ampliacion,
                        sum(sm.modificado) modificado,sum(sm.comprometido) comprometido,
                        sum(sm.devengado) devengado,sum(sm.ejercido) ejercido
                    from sapp_movimientos sm
                    where sm.ejercicio = \",anio,\"\",@upp,\"\",@programa,\"
                    group by clv_upp,clv_programa
                    order by clv_upp,clv_programa
                )t
                join catalogo c1 on t.clv_upp = c1.clave and c1.ejercicio = \",anio,\"
                and c1.deleted_at is null and c1.grupo_id = 6
                join catalogo c2 on t.clv_programa = c2.clave and c2.ejercicio = \",anio,\"
                and c2.deleted_at is null and c2.grupo_id = 16
            )
            select 
                a.*,
                case 
                    when a.modificado = 0 then 0
                    else truncate(((a.devengado/a.modificado)*100),2)
                end cumplimiento,
                t.original original_t,
                t.ampliacion ampliacion_t,
                t.modificado modificado_t,
                t.comprometido comprometido_t,
                t.devengado devengado_t,
                t.ejercido ejercido_t,
                case 
                    when t.modificado = 0 then 0
                    else truncate(((t.devengado/t.modificado)*100),2)
                end cumplimiento_t
            from aux a
            left join (
                select 
                    clv_upp,'' clv_programa,upp,'' programa,sum(original) original,sum(ampliacion) ampliacion,
                    sum(modificado) modificado,sum(comprometido) comprometido,
                    sum(devengado) devengado,sum(ejercido) ejercido,
                    case 
                        when sum(modificado) = 0 then 0
                        else truncate(((sum(modificado)/sum(devengado))*100),2)
                    end cumplimiento
                from aux
                group by clv_upp,upp
            )t on a.clv_upp = t.clv_upp;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");

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
                from epp e
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

        DB::unprepared("CREATE PROCEDURE SP_AF_EE(in anio int)
        begin
            select
                case 
                    when clv_programa != '' then ''
                    else clv_upp
                end clv_upp,
                case 
                    when clv_programa != '' then ''
                    else upp
                end upp,
                case 
                    when clv_programa != '' then ''
                    else clv_subsecretaria
                end clv_subsecretaria,
                case 
                    when clv_programa != '' then ''
                    else subsecretaria
                end subsecretaria,
                case 
                    when clv_programa != '' then ''
                    else clv_ur
                end clv_ur,
                case 
                    when clv_programa != '' then ''
                    else ur
                end ur,
                case 
                    when clv_subprograma != '' then ''
                    else clv_programa
                end clv_programa,
                case 
                    when clv_subprograma != '' then ''
                    else programa
                end programa,
                case 
                    when clv_proyecto != '' then ''
                    else clv_subprograma
                end clv_subprograma,
                case 
                    when clv_proyecto != '' then ''
                    else subprograma
                end subprograma,
                clv_proyecto,
                proyecto
            from (
                select distinct
                    ve.clv_upp,
                    ve.upp,
                    ve.clv_subsecretaria,
                    ve.subsecretaria,
                    ve.clv_ur,
                    ve.ur,
                    '' clv_programa,
                    '' programa,
                    '' clv_subprograma,
                    '' subprograma,
                    '' clv_proyecto,
                    '' proyecto
                from v_epp ve 
                where ve.ejercicio = anio and 
                    ve.deleted_at is null
                union all
                select distinct
                    ve.clv_upp,
                    ve.upp,
                    ve.clv_subsecretaria,
                    ve.subsecretaria,
                    ve.clv_ur,
                    ve.ur,
                    ve.clv_programa,
                    ve.programa,
                    '' clv_subprograma,
                    '' subprograma,
                    '' clv_proyecto,
                    '' proyecto
                from v_epp ve 
                where ve.ejercicio = anio and 
                    ve.deleted_at is null
                union all
                select distinct
                    ve.clv_upp,
                    ve.upp,
                    ve.clv_subsecretaria,
                    ve.subsecretaria,
                    ve.clv_ur,
                    ve.ur,
                    ve.clv_programa,
                    ve.programa,
                    ve.clv_subprograma,
                    ve.subprograma,
                    '' clv_proyecto,
                    '' proyecto
                from v_epp ve 
                where ve.ejercicio = anio and 
                    ve.deleted_at is null
                union all
                select 
                    ve.clv_upp,
                    ve.upp,
                    ve.clv_subsecretaria,
                    ve.subsecretaria,
                    ve.clv_ur,
                    ve.ur,
                    ve.clv_programa,
                    ve.programa,
                    ve.clv_subprograma,
                    ve.subprograma,
                    ve.clv_proyecto,
                    ve.proyecto
                from v_epp ve 
                where ve.ejercicio = anio and 
                    ve.deleted_at is null
                order by clv_upp,clv_subsecretaria,clv_ur,clv_programa,
                    clv_subprograma,clv_proyecto
            ) tabla;
        end;");

        DB::unprepared("CREATE PROCEDURE sp_check_permission(in in_usuario int,in in_funcion varchar(100),in in_sistema int)
        begin 
            SELECT p.id
            FROM adm_rel_funciones_grupos p
            INNER JOIN adm_funciones f ON f.id = p.id_funcion
            WHERE f.funcion = in_funcion
            AND f.id_sistema = in_sistema
            AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = in_usuario);
        end;");

        DB::unprepared("CREATE PROCEDURE sp_epp(in delegacion int,in uppC varchar(3),in urC varchar(2),in anio int)
        BEGIN
            set @upp := \"\";
            set @ur := \"\";
            set @del := \"from t_epp e\";
            set @anio := anio;
            if(uppC is not null) then set @upp := CONCAT(\"and e.clv_upp = '\",uppC,\"'\"); end if;
            if(urC is not null) then set @ur := CONCAT(\"and e.clv_ur = '\",urC,\"'\"); end if;
            if(delegacion = 1) then set @del := \"from uppautorizadascpnomina u join t_epp e on u.clv_upp = e.clv_upp\"; end if;
                
            drop temporary table if exists t_epp;
            
            set @queri := concat('
                create temporary table t_epp
                select 
                    e.id,
                    c01.clave clv_sector_publico,c01.descripcion sector_publico,
                    c02.clave clv_sector_publico_f,c02.descripcion sector_publico_f,
                    c03.clave clv_sector_economia,c03.descripcion sector_economia,
                    c04.clave clv_subsector_economia,c04.descripcion subsector_economia,
                    c05.clave clv_ente_publico,c05.descripcion ente_publico,
                    c06.clave clv_upp,c06.descripcion upp,
                    c07.clave clv_subsecretaria,c07.descripcion subsecretaria,
                    c08.clave clv_ur,c08.descripcion ur,
                    c09.clave clv_finalidad,c09.descripcion finalidad,
                    c10.clave clv_funcion,c10.descripcion funcion,
                    c11.clave clv_subfuncion,c11.descripcion subfuncion,
                    c12.clave clv_eje,c12.descripcion eje,
                    c13.clave clv_linea_accion,c13.descripcion linea_accion,
                    c14.clave clv_programa_sectorial,c14.descripcion programa_sectorial,
                    c15.clave clv_tipologia_conac,c15.descripcion tipologia_conac,
                    c16.clave clv_programa,c16.descripcion programa,
                    c17.clave clv_subprograma,c17.descripcion subprograma,
                    c18.clave clv_proyecto,c18.descripcion proyecto,
                    e.presupuestable,
                    e.con_mir,
                    e.confirmado,
                    e.tipo_presupuesto,
                    e.ejercicio,
                    e.deleted_at,
                    e.updated_at,
                    e.created_at
                from epp e
                join catalogo c01 on e.sector_publico_id = c01.id 
                join catalogo c02 on e.sector_publico_f_id = c02.id 
                join catalogo c03 on e.sector_economia_id = c03.id 
                join catalogo c04 on e.subsector_economia_id = c04.id 
                join catalogo c05 on e.ente_publico_id = c05.id 
                join catalogo c06 on e.upp_id = c06.id 
                join catalogo c07 on e.subsecretaria_id = c07.id  
                join catalogo c08 on e.ur_id = c08.id 
                join catalogo c09 on e.finalidad_id = c09.id 
                join catalogo c10 on e.funcion_id = c10.id 
                join catalogo c11 on e.subfuncion_id = c11.id 
                join catalogo c12 on e.eje_id = c12.id 
                join catalogo c13 on e.linea_accion_id = c13.id 
                join catalogo c14 on e.programa_sectorial_id = c14.id 
                join catalogo c15 on e.tipologia_conac_id = c15.id 
                join catalogo c16 on e.programa_id = c16.id 
                join catalogo c17 on e.subprograma_id = c17.id 
                join catalogo c18 on e.proyecto_id = c18.id
                where e.ejercicio = ',@anio,';
            ');
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                
            set @query := CONCAT(\"
                select 
                    concat(
                        e.clv_sector_publico,
                        e.clv_sector_publico_f,
                        e.clv_sector_economia,
                        e.clv_subsector_economia,
                        e.clv_ente_publico
                    ) clasificacion_administrativa,
                    concat(
                        e.clv_upp,' ',
                        e.upp
                    ) upp,
                    concat(
                        e.clv_subsecretaria,' ',
                        e.subsecretaria
                    ) subsecretaria,
                    concat(
                        e.clv_ur,' ',
                        e.ur
                    ) unidad_responsable,
                    concat(
                        e.clv_finalidad,' ',
                        e.finalidad
                    ) finalidad,
                    concat(
                        e.clv_funcion,' ',
                        e.funcion
                    ) funcion,
                    concat(
                        e.clv_subfuncion,' ',
                        e.subfuncion
                    ) subfuncion,
                    concat(
                        e.clv_eje,' ',
                        e.eje
                    ) eje,
                    concat(
                        e.clv_linea_accion,' ',
                        e.linea_accion
                    ) linea_accion,
                    concat(
                        e.clv_programa_sectorial,' ',
                        e.programa_sectorial
                    ) programa_sectorial,
                    concat(
                        e.clv_tipologia_conac,' ',
                        e.tipologia_conac
                    ) tipologia_conac,
                    concat(
                        e.clv_programa,' ',
                        e.programa
                    ) programa,
                    concat(
                        e.clv_subprograma,' ',
                        e.subprograma
                    ) subprograma,
                    concat(
                        e.clv_proyecto,' ',
                        e.proyecto
                    ) proyecto,
                    e.ejercicio
                \",@del,\"
                where e.deleted_at is null
                \",@upp,\" \",@ur,\" order by e.clv_upp,e.clv_subsecretaria,e.clv_ur,
                e.clv_finalidad,e.clv_funcion,e.clv_subfuncion,
                e.clv_eje,e.clv_linea_accion,e.clv_programa_sectorial,e.clv_tipologia_conac,
                e.clv_programa,e.clv_subprograma,e.clv_proyecto
            \");
            
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
                
            drop temporary table if exists t_epp;
        end;");

        DB::unprepared("CREATE PROCEDURE sp_menu_sidebar(in in_usuario int,in in_sistema int,in in_padre int)
        begin
            SELECT
            m.id,
            m.nombre_menu,
            m.ruta,
            m.icono,
            m.descripcion
            FROM adm_menus m
            WHERE m.padre = COALESCE(in_padre, 0)
            AND m.id_sistema = in_sistema
            AND m.id <> 0
            AND (m.id IN (SELECT mg.id_menu FROM adm_rel_menu_grupo mg WHERE mg.id_grupo IN (SELECT ug.id_grupo FROM adm_rel_user_grupo ug WHERE ug.id_usuario = in_usuario))= 1)
            ORDER BY m.posicion ASC;
        end;");

        DB::unprepared("CREATE PROCEDURE sp_report_etapa4_mir(in upp varchar(3),in pp varchar(2),in ejercicio int(6))
        BEGIN
            SELECT mao.tipo, mao.indice, mao.descripcion, mao.seleccion_mir, ifnull(mao.tipo_indicador, \".\") as tipo_indicador
            FROM mml_arbol_objetivos as mao
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio AND mao.seleccion_mir = 1 AND mao.tipo_indicador IS NULL AND mao.deleted_at IS NULL
            UNION
            SELECT \"Proposito\" as tipo,  \"\" as indice, mdp.objetivo_central as descripcion, \"0\" as seleccion_mir, \".\" as tipo_indicador 
            FROM mml_definicion_problema as mdp
            WHERE mdp.clv_upp = upp AND mdp.clv_pp = pp AND mdp.ejercicio = ejercicio AND mdp.deleted_at IS NULL
            UNION
            SELECT mao.tipo, @c := @c + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @c := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = \"Componente\" AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1
            UNION
            SELECT mao.tipo,  @a := @a + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @a := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = \"Actividad\" AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_etapas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_etapas_upp_programa;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS conceptos_clave;");
        DB::unprepared("DROP PROCEDURE IF EXISTS corte_anual_no_pp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS estatus_movimientos;");
        DB::unprepared("DROP PROCEDURE IF EXISTS inicio_a;");
        DB::unprepared("DROP PROCEDURE IF EXISTS inicio_b;");
        DB::unprepared("DROP PROCEDURE IF EXISTS insert_pp_aplanado;");
        DB::unprepared("DROP PROCEDURE IF EXISTS lista_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_cierres_etapas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_nuevo_anio;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_alineacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_presupuesto_egresos;");
        DB::unprepared("DROP PROCEDURE IF EXISTS presupuesto_sap;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_III;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_10;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_8;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_presupuesto_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_ingresos;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_reporte_calendario;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_reporte_presupuesto_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_AF_EE;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_check_permission;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_epp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_menu_sidebar;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_report_etapa4_mir;");
    }
};
