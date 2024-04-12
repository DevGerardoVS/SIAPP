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
        DB::unprepared("DROP PROCEDURE if exists sp_report_etapa4_mir;");
        DB::unprepared("CREATE PROCEDURE sp_report_etapa4_mir(in upp varchar(3),in pp varchar(2),in ejercicio int(6),IN ramo_33 INT(6))
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

        DB::unprepared("DROP VIEW if exists v_epp;");
        DB::unprepared("CREATE VIEW v_epp AS
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
            ) area_funcional,
            e.ejercicio,
            e.presupuestable,
            e.con_mir,
            e.confirmado,
            e.tipo_presupuesto,
            e.deleted_at,
            e.updated_at,
            e.created_at,
            e.created_user,
            e.updated_user,
            e.deleted_user
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
        join catalogo c18 on e.proyecto_id = c18.id;");

        DB::unprepared("DROP VIEW if exists v_epp_hist;");
        DB::unprepared("CREATE VIEW v_epp_hist AS
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
            ) area_funcional,
            e.ejercicio,
            e.presupuestable,
            e.con_mir,
            e.confirmado,
            e.tipo_presupuesto,
            e.deleted_at,
            e.updated_at,
            e.created_at,
            e.created_user,
            e.updated_user,
            e.deleted_user
        from epp_hist e
        join catalogo_hist c01 on e.sector_publico_id = c01.id 
        join catalogo_hist c02 on e.sector_publico_f_id = c02.id 
        join catalogo_hist c03 on e.sector_economia_id = c03.id 
        join catalogo_hist c04 on e.subsector_economia_id = c04.id 
        join catalogo_hist c05 on e.ente_publico_id = c05.id 
        join catalogo_hist c06 on e.upp_id = c06.id 
        join catalogo_hist c07 on e.subsecretaria_id = c07.id  
        join catalogo_hist c08 on e.ur_id = c08.id 
        join catalogo_hist c09 on e.finalidad_id = c09.id 
        join catalogo_hist c10 on e.funcion_id = c10.id 
        join catalogo_hist c11 on e.subfuncion_id = c11.id 
        join catalogo_hist c12 on e.eje_id = c12.id 
        join catalogo_hist c13 on e.linea_accion_id = c13.id 
        join catalogo_hist c14 on e.programa_sectorial_id = c14.id 
        join catalogo_hist c15 on e.tipologia_conac_id = c15.id 
        join catalogo_hist c16 on e.programa_id = c16.id 
        join catalogo_hist c17 on e.subprograma_id = c17.id 
        join catalogo_hist c18 on e.proyecto_id = c18.id;");

        DB::unprepared("DROP PROCEDURE if exists mml_comprobacion;");
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
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
                set @mir := 'mml_mir_hist';
            end if;
                    
           set @queri := concat(\"
               CREATE TEMPORARY TABLE epp_t
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
                from \",@epp,\" e
                join \",@catalogo,\" c06 on e.upp_id = c06.id 
              join \",@catalogo,\" c07 on e.subsecretaria_id = c07.id  
              join \",@catalogo,\" c08 on e.ur_id = c08.id 
              join \",@catalogo,\" c09 on e.finalidad_id = c09.id 
              join \",@catalogo,\" c10 on e.funcion_id = c10.id 
              join \",@catalogo,\" c11 on e.subfuncion_id = c11.id 
              join \",@catalogo,\" c12 on e.eje_id = c12.id 
              join \",@catalogo,\" c13 on e.linea_accion_id = c13.id 
              join \",@catalogo,\" c14 on e.programa_sectorial_id = c14.id 
              join \",@catalogo,\" c15 on e.tipologia_conac_id = c15.id 
              join \",@catalogo,\" c16 on e.programa_id = c16.id 
              join \",@catalogo,\" c17 on e.subprograma_id = c17.id 
              join \",@catalogo,\" c18 on e.proyecto_id = c18.id
                WHERE e.ejercicio = \",anio,\" AND e.\",@corte,\";
           \");
        
           prepare stmt from @queri;
           execute stmt;
           deallocate prepare stmt;
           
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

        DB::unprepared("UPDATE epp SET con_mir = 0
        WHERE id IN (
            SELECT id FROM v_epp
            WHERE deleted_at IS NULL AND clv_programa IN ('5H','RM')
            UNION ALL
            SELECT id FROM v_epp
            WHERE deleted_at IS NULL AND clv_subprograma IN ('21B','UUU')
        );");

        //--------QUERIS EXTRAS----------------------------------------------------------------
        DB::unprepared("CREATE TEMPORARY TABLE if NOT EXISTS seguimiento
        SELECT
            ss.id,
            ss.clv_upp,ss.clv_ur,
            SUBSTR(m.clv_actividad,8,16) area_funcional,
            m.clv_fondo,
            ss.mes
        FROM sapp_seguimiento ss
        left JOIN metas m ON ss.meta_id = m.id
        WHERE ss.ejercicio = 2024 AND ss.deleted_at IS NULL;
         
        CREATE TEMPORARY TABLE if NOT EXISTS movimientos
        SELECT 
            clv_upp,clv_ur,area_funcional,clv_fondo,mes
        FROM (
            SELECT 
                clv_upp,clv_ur,
                area_funcional,
                SUBSTR(fondo,7,2) clv_fondo,
                mes,
                sum(ejercido_cp) ejercido_cp
            FROM sapp_movimientos sm
            WHERE ejercicio = 2024
            GROUP BY clv_upp,clv_ur,area_funcional,clv_fondo,mes
        )t
        WHERE ejercido_cp = 0;
         
        CREATE TEMPORARY TABLE if NOT EXISTS borrar
        SELECT DISTINCT 
            s.*
        FROM movimientos m
        JOIN seguimiento s ON m.clv_upp = s.clv_upp AND m.mes = s.mes and m.clv_ur = s.clv_ur and
        m.area_funcional = s.area_funcional AND m.clv_fondo = s.clv_fondo
        ORDER BY clv_upp,clv_ur,area_funcional,clv_fondo,mes;
         
        ALTER TEMPORARY TABLE borrar 
        ADD CONSTRAINT borrar_pk PRIMARY KEY (id);
         
        update sapp_seguimiento set deleted_at = now(), deleted_user = 'SISTEMA'
        where id in (SELECT id FROM borrar);
         
        DROP TEMPORARY TABLE if EXISTS seguimiento;
        DROP TEMPORARY TABLE if EXISTS movimientos;
        DROP TEMPORARY TABLE if EXISTS borrar;");

        DB::unprepared("UPDATE mml_mir SET area_funcional = '1731COBG4XFOUGVC'
        WHERE id IN (3130,3131,3132);");

        DB::unprepared("UPDATE mml_mir mm
        JOIN v_epp ve ON mm.clv_upp = ve.clv_upp AND mm.ejercicio = ve.ejercicio 
        AND mm.clv_ur = ve.clv_ur AND mm.area_funcional = ve.area_funcional AND ve.deleted_at IS NULL
        SET mm.id_epp = ve.id
        WHERE mm.id_epp != 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE sp_report_etapa4_mir;");
        DB::unprepared("DROP VIEW v_epp;");
        DB::unprepared("DROP VIEW v_epp_hist;");
        DB::unprepared("DROP PROCEDURE mml_comprobacion;");
    }
};
