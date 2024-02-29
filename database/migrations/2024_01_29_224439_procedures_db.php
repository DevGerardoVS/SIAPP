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
        DB::unprepared("CREATE PROCEDURE sp_check_permission(in_usuario INT, in_funcion VARCHAR(100), in_sistema INT)
        BEGIN 
            SELECT p.id
            FROM adm_rel_funciones_grupos p
            INNER JOIN adm_funciones f ON f.id = p.id_funcion
            WHERE f.funcion = in_funcion
            AND f.id_sistema = in_sistema
            AND p.id_grupo IN (SELECT u.id_grupo FROM adm_rel_user_grupo u WHERE u.id_usuario = in_usuario);
        END");

        DB::unprepared("CREATE PROCEDURE sp_menu_sidebar(in_usuario INT, in_sistema INT, in_padre INT)
        BEGIN
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
        END");

         DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_III(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            if (corte is not null) then 
                set @id := 'id_original';
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists parte_0;
            drop temporary table if exists parte_1;
            drop temporary table if exists parte_2;
            drop temporary table if exists parte_3;
            drop temporary table if exists parte_4;
        
            set @query := CONCAT('
            create temporary table aux_0
            select 
                ve.id,ve.clv_upp,ve.upp,ve.clv_ur,
                ve.ur,ve.clv_programa,ve.programa,
                ve.clv_subprograma,ve.subprograma,
                ve.clv_proyecto,ve.proyecto
            from v_epp ve 
            where ve.ejercicio = ',anio,' and ve.deleted_at is null;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
            alter table aux_0 add primary key(id);
        
            set @query := concat('
            create temporary table aux_1
            select 
                pt.epp_id,
                pt.pos_pre_id,
                pp.posicion_presupuestaria,
                sum(pp.total) importe
            from ',@tabla,' pp
            join pp_identificadores pt on pp.',@id,' = pt.id
            where pp.ejercicio = ',anio,' and pp.',@corte,'
            group by epp_id,pos_pre_id,posicion_presupuestaria;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table parte_0
            select
                clv_upp,upp,
                clv_ur,ur,
                clv_programa,programa,
                clv_subprograma,subprograma,
                posicion_presupuestaria clv_partida,
                pp.partida_especifica partida_especifica,
                importe
            from aux_1 a1
            join aux_0 a0 on a1.epp_id = a0.id
            join posicion_presupuestaria pp on a1.pos_pre_id = pp.id
            and pp.deleted_at is null;
            
            create temporary table parte_1
            select
                clv_upp,upp,
                clv_ur,ur,
                clv_programa,programa,
                clv_subprograma,subprograma,
                sum(importe) importe
            from parte_0
            group by clv_upp,upp,clv_ur,ur,clv_programa,
            programa,clv_subprograma,subprograma;
            
            create temporary table parte_2
            select
                clv_upp,upp,
                clv_ur,ur,
                clv_programa,programa,
                sum(importe) importe
            from parte_1
            group by clv_upp,upp,clv_ur,ur,clv_programa,programa;
            
            create temporary table parte_3
            select
                clv_upp,upp,
                clv_ur,ur,
                sum(importe) importe
            from parte_2
            group by clv_upp,upp,clv_ur,ur;
            
            create temporary table parte_4
            select
                clv_upp,upp,
                sum(importe) importe
            from parte_3
            group by clv_upp,upp;
            
            select 
                case when clv_ur != '' then '' else clv_upp end clv_upp,
                case when clv_ur != '' then '' else upp end upp,
                case when clv_programa != '' then '' else clv_ur end clv_ur,
                case when clv_programa != '' then '' else ur end ur,
                case when clv_subprograma != '' then '' else clv_programa end clv_programa,
                case when clv_subprograma != '' then '' else programa end programa,
                case when clv_partida != '' then '' else clv_subprograma end clv_subprograma,
                case when clv_partida != '' then '' else subprograma end subprograma,
                clv_partida,partida_especifica,importe
            from (
                select 
                    clv_upp,upp,
                    '' clv_ur,'' ur,
                    '' clv_programa,'' programa,
                    '' clv_subprograma,'' subprograma,
                    '' clv_partida,'' partida_especifica,
                    importe
                from parte_4
                union all
                select 
                    clv_upp,upp,
                    clv_ur,ur,
                    '' clv_programa,'' programa,
                    '' clv_subprograma,'' subprograma,
                    '' clv_partida,'' partida_especifica,
                    importe
                from parte_3
                union all
                select 
                    clv_upp,upp,
                    clv_ur,ur,
                    clv_programa,programa,
                    '' clv_subprograma,'' subprograma,
                    '' clv_partida,'' partida_especifica,
                    importe
                from parte_2
                union all
                select 
                    clv_upp,upp,
                    clv_ur,ur,
                    clv_programa,programa,
                    clv_subprograma,subprograma,
                    '' clv_partida,'' partida_especifica,
                    importe
                from parte_1
                union all
                select * from parte_0
                order by clv_upp,clv_ur,clv_programa,
                clv_subprograma,clv_partida
            )t;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table parte_0;
            drop temporary table parte_1;
            drop temporary table parte_2;
            drop temporary table parte_3;
            drop temporary table parte_4;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                    where c.ejercicio = ',anio,'
                    and c.deleted_at is null and c.grupo_id = 6
                    group by c.clave,c.descripcion
                    order by clv_upp
                )t;
            ');
            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto pp';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist pp';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_3(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto pp';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist pp';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            set @query = CONCAT('
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
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            from epp e
            join ',@catalogo,' c0 on e.finalidad_id = c0.id
            join ',@catalogo,' c1 on e.funcion_id = c1.id
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
                sum(pp.total) importe
            from aux_0 a0 
            left join ',@tabla,' pp on pp.finalidad = a0.clv_finalidad 
            and pp.funcion = a0.clv_funcion
            and pp.ejercicio = ',anio,' and pp.',@corte,'
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
                case
                    when importe is null then 0
                    else importe
                end importe
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_3(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if(corte is not null) then
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_4(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_5(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
             if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            set @query := CONCAT('
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
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_6(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                from programacion_presupuesto pp 
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_1(in anio int, in corte date)
        begin
        set @tabla := 'programacion_presupuesto';
        set @corte := 'deleted_at is null';
        set @catalogo := 'catalogo';
        if (corte is not null) then 
            set @catalogo := 'catalogo_hist';
            set @tabla := 'programacion_presupuesto_hist';
            set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            from epp e
            join ',@catalogo,' c0 on e.finalidad_id = c0.id
            join ',@catalogo,' c1 on e.funcion_id = c1.id
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
                importe
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
        END;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_3(in anio int, in corte date)
        begin
        set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            from epp e
            join ',@catalogo,' c0 on e.finalidad_id = c0.id
            join ',@catalogo,' c1 on e.funcion_id = c1.id
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
                importe
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_4(in anio int, in corte date)
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_10(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_1(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                select distinct
                    clv_upp,upp,clv_subsecretaria,subsecretaria,clv_ur,ur
                from v_epp
                where ejercicio = \",anio,\" and deleted_at is null
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
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
            drop temporary table if exists aux_2;
        
            set @query := concat('
                create temporary table aux_0
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
                    pt.epp_id,
                    pp.total
                from pp_identificadores pt
                join ',@tabla,' pp on pt.id = pp.id 
                and pp.',@corte,' and pp.ejercicio = ',anio,'
                join clasificacion_geografica cg on pt.clas_geo_id = cg.id;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            set @query := concat('
                create temporary table aux_1
                select 
                    ve.id,
                    c.clave clv_upp,
                    c.descripcion upp
                from epp ve
                join ',@catalogo,' c on ve.upp_id = c.id
                and c.ejercicio = ',anio,' and c.deleted_at is null
                where ve.ejercicio = ',anio,' and ve.deleted_at is null;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table aux_2
            select 
                a0.region,
                a0.municipio,
                a0.localidad,
                a1.clv_upp,
                a1.upp,
                sum(a0.total) importe
            from aux_0 a0
            join aux_1 a1 on a0.epp_id = a1.id
            group by region,municipio,
            localidad,clv_upp,upp;
        
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
                clv_upp,
                upp,
                importe
            from (
                select 
                    region,municipio,localidad,
                    '' clv_upp, '' upp,
                    sum(total) importe
                from aux_0
                group by region,municipio,localidad
                union all
                select * from aux_2
                order by region,municipio,localidad,clv_upp,upp
            )t;
        
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table aux_2;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_3(in anio int,in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @catalogo := 'catalogo';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
            set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_4(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                    select distinct
                        clv_programa,
                        programa
                    from v_epp ve
                    where ejercicio = ',anio,' and deleted_at is null
                )t
                left join ',@tabla,' pp on t.clv_programa = pp.programa_presupuestario
                and pp.ejercicio = ',anio,' and pp.',@corte,'
                group by clv_programa,programa
                order by clv_programa;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_5(in anio int, in corte date)
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
                ve.clv_upp,                                
                ve.upp,                                    
                ve.clv_programa,                           
                ve.programa                                
            from v_epp ve                             
            where ve.ejercicio = ',anio,' and ve.deleted_at is null;
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_6(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @catalogo := 'catalogo';
            if (corte is not null) then 
                set @catalogo := 'catalogo_hist';
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
            from epp e
            join ',@catalogo,' c0 on e.finalidad_id = c0.id
            join ',@catalogo,' c1 on e.funcion_id = c1.id
            join ',@catalogo,' c2 on e.subfuncion_id = c2.id
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_7(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            if (corte is not null) then 
                set @id := 'id_original';
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists parte_0;
            drop temporary table if exists parte_1;
            drop temporary table if exists parte_2;
            drop temporary table if exists parte_3;
            drop temporary table if exists parte_4;
        
            set @query := CONCAT('
            create temporary table aux_0
            select 
                id,clv_upp,upp,clv_ur,ur,clv_finalidad,finalidad,
                clv_funcion,funcion,clv_subfuncion,subfuncion
            from v_epp ve
            where ve.ejercicio = ',anio,' and ve.deleted_at is null;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            alter table aux_0 add primary key(id);
        
            set @query := concat('
            create temporary table aux_1
            select 
                pt.epp_id,
                pt.pos_pre_id,
                pp.posicion_presupuestaria,
                sum(pp.total) importe
            from ',@tabla,' pp
            join pp_identificadores pt on pp.',@id,' = pt.id
            where pp.ejercicio = ',anio,' and pp.',@corte,'
            group by epp_id,pos_pre_id,posicion_presupuestaria;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table parte_0
            select
                concat(clv_upp,' ',upp) upp,
                concat(clv_ur,' ',ur) ur,
                finalidad,
                funcion,
                subfuncion,
                concat(posicion_presupuestaria,' ',pp.partida_especifica) partida,
                importe
            from aux_1 a1
            join aux_0 a0 on a1.epp_id = a0.id
            join posicion_presupuestaria pp on a1.pos_pre_id = pp.id
            and pp.deleted_at is null;
            
            create temporary table parte_1
            select
                upp,ur,finalidad,funcion,subfuncion,
                sum(importe) importe
            from parte_0
            group by upp,ur,finalidad,funcion,subfuncion;
            
            create temporary table parte_2
            select
                upp,ur,finalidad,funcion,
                sum(importe) importe
            from parte_1
            group by upp,ur,finalidad,funcion;
            
            create temporary table parte_3
            select
                upp,ur,finalidad,
                sum(importe) importe
            from parte_2
            group by upp,ur,finalidad;
            
            create temporary table parte_4
            select
                upp,
                sum(importe) importe
            from parte_3
            group by upp;
            
            select 
                case 
                    when ur != '' then ''
                    else upp
                end upp,
                case 
                    when funcion != '' then ''
                    else ur
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
                partida,
                importe
            from (
                select 
                    upp,'' ur,'' finalidad,'' funcion,
                    '' subfuncion,'' partida, importe
                from parte_4
                union all
                select 
                    upp,ur,finalidad,'' funcion,'' subfuncion,
                    '' partida, importe
                from parte_3
                union all
                select 
                    upp,ur,finalidad,funcion,'' subfuncion,
                    '' partida, importe
                from parte_2
                union all
                select 
                    upp,ur,finalidad,funcion,subfuncion,
                    '' partida, importe
                from parte_1
                union all
                select * from parte_0
                order by upp,ur,finalidad,funcion,subfuncion,partida
            )t;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table parte_0;
            drop temporary table parte_1;
            drop temporary table parte_2;
            drop temporary table parte_3;
            drop temporary table parte_4;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_8(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            if (corte is not null) then 
                set @id := 'id_original';
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
        
            set @query := CONCAT('
                create temporary table aux_0
                select 
                    po.clv_proyecto_obra,
                    po.proyecto_obra,
                    pp.total,
                    pp.upp,
                    pp.programa_presupuestario programa
                from pp_identificadores pt
                join ',@tabla,' pp on pt.id = pp.',@id,'
                and pp.ejercicio = ',anio,' and pp.',@corte,' 
                join proyectos_obra po on pt.obra_id = po.id;
            ');
                    
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            set @query := CONCAT('
                create temporary table aux_1
                select distinct
                    ve.clv_upp,
                    ve.upp,
                    ve.clv_programa,
                    ve.programa
                from v_epp ve
                where ejercicio = ',anio,' and deleted_at is null;;
            ');
                    
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table aux_2
            select 
                a1.clv_upp,
                a1.upp,
                a1.clv_programa,
                a1.programa,
                a0.clv_proyecto_obra,
                a0.proyecto_obra,
                sum(a0.total) importe
            from aux_0 a0
            join aux_1 a1 on a0.upp = a1.clv_upp 
            and a0.programa = a1.clv_programa
            group by a1.clv_upp,a1.upp,a1.clv_programa,a1.programa,
            a0.clv_proyecto_obra,a0.proyecto_obra;
            
            create temporary table aux_3
            select 
                clv_upp,
                upp,
                sum(importe) importe
            from aux_2
            group by clv_upp,upp;
            
            select *
            from aux_2
            union all 
            select 
                clv_upp,upp,'' clv_programa,'' programa,
                '' clv_proyecto_obra,'' proyecto_obra,
                importe
            from aux_3
            order by clv_upp,clv_programa,clv_proyecto_obra;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table aux_2;
            drop temporary table aux_3;
        END;");
        
        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int, in corte date, in uppC varchar(3),in tipo varchar(9))
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
        
        DB::unprepared("CREATE PROCEDURE calendario_fondo_mensual(in anio int, in corte date)
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
        
        DB::unprepared("CREATE PROCEDURE reporte_resumen_por_capitulo_y_partida(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @id := 'id_original';
            end if;
        
            set @query := CONCAT('
            create temporary table aux_0
            select 
                pt.pos_pre_id,
                pa.posicion_presupuestaria,
                sum(pa.total) importe
            from pp_identificadores pt 
            join ',@tabla,' pa on pt.id = pa.',@id,'
            and pa.ejercicio = ',anio,' and pa.',@corte,'
            group by pt.pos_pre_id,pa.posicion_presupuestaria;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            select 
                concat(
                    pp.clv_capitulo,'000 ',
                    pp.capitulo
                ) capitulo,
                concat(
                    a0.posicion_presupuestaria,' ',
                    pp.partida_especifica 
                ) partida,
                importe
            from aux_0 a0
            join posicion_presupuestaria pp 
            on a0.pos_pre_id = pp.id
            order by capitulo,partida;
            
            drop temporary table aux_0;
        END;");
        
        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int, in corte date)
        begin
            set @corte := 'deleted_at is null';
            set @tabla := 'programacion_presupuesto';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                    where m.ejercicio = 2024 and m.deleted_at is null
                    union all 
                    select ma.clv_upp,concat(substr(ma.entidad_ejecutora,5,2),ma.area_funcional,m.clv_fondo) claves,m.estatus
                    from metas m
                    join mml_actividades ma on m.actividad_id = ma.id
                    where m.ejercicio = 2024 and m.deleted_at is null
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
                from (select distinct clv_upp,upp from v_epp where ejercicio = 2024 and deleted_at is null) ve
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
        END;");
        
        DB::unprepared("CREATE PROCEDURE proyecto_calendario_actividades(in anio int, in upp varchar(3), in corte date,in tipo varchar(9))
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
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_5(in anio int, in corte date)
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
        
            set @from1 := concat('from tipologia_conac tc 
                left join programacion_presupuesto_hist pp on tc.clave_conac = pp.tipologia_conac and etiquetado = 1
                and pp.ejercicio = ',anio,' and pp.',@corte,'
                where tc.deleted_at is null and tc.clave_conac is not null');
            set @from2 := concat('from tipologia_conac tc 
                left join programacion_presupuesto_hist pp on tc.clave_conac = pp.tipologia_conac and etiquetado = 2
                and pp.ejercicio = ',anio,' and pp.',@corte,'
                where tc.deleted_at is null and tc.clave_conac is not null');
        
            set @query := CONCAT(\"
                create temporary table aux_0
                select 
                    1 etiquetado,
                    min(tc.id) id,
                    'Programas' abuelo,
                    tc.descripcion padre,
                    tc.descripcion_conac hijo,
                    sum(pp.total) importe
                \",@from1,\"
                group by tc.descripcion,tc.descripcion_conac
                union all
                select 
                    1 etiquetado,
                    min(tc.id) id,
                    tc.descripcion abuelo,
                    '' padre,
                    '' hijo,
                    sum(pp.total) importe
                \",@from1,\"
                group by tc.descripcion
                union all 
                select 
                    2 etiquetado,
                    min(tc.id) id,
                    'Programas' abuelo,
                    tc.descripcion padre,
                    tc.descripcion_conac hijo,
                    sum(pp.total) importe
                \",@from2,\"
                group by tc.descripcion,tc.descripcion_conac
                union all
                select 
                    2 etiquetado,
                    min(tc.id) id,
                    tc.descripcion abuelo,
                    '' padre,
                    '' hijo,
                    sum(pp.total) importe
                \",@from2,\"
                group by tc.descripcion
                order by etiquetado,id;
            \");
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table aux_1
            select 
                etiquetado,
                min(id) id,
                abuelo,
                padre,
                sum(importe) importe
            from aux_0
            where padre != ''
            group by etiquetado,abuelo,padre;
            
            create temporary table aux_2
            select 
                etiquetado,
                min(id) id,
                abuelo,
                sum(importe) importe
            from aux_1
            group by etiquetado,abuelo;
            
            create temporary table aux_3
            select 
                etiquetado,
                sum(importe) importe
            from aux_2
            group by etiquetado;
            
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
                select *
                from aux_0
                union all
                select 
                    etiquetado,id,abuelo,
                    padre,'' hijo,
                    importe
                from aux_1
                union all
                select 
                    etiquetado,id,abuelo,
                    '' padre,'' hijo,
                    importe
                from aux_2
                union all
                select 
                    etiquetado,0 id,'' abuelo,
                    '' padre,'' hijo,
                    importe
                from aux_3
                order by etiquetado,id,abuelo,
                padre,hijo,importe
            )t;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table aux_2;
            drop temporary table aux_3;
        END;");
        
        DB::unprepared("CREATE PROCEDURE proyecto_avance_general(in anio int, in corte date)
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
                 case 
                       when t.clv_capitulo = '' then ''
                     else concat(t.clv_capitulo,'000')
                 end clv_capitulo,
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

        DB::unprepared("CREATE PROCEDURE conceptos_clave(in claveT varchar(64), in anio int)
        begin
            
        set @clave := claveT COLLATE utf8mb4_unicode_ci; 
        set @epp := concat(substring(@clave,1,5),substring(@clave,16,22));
        set @clasGeo := ((substring(@clave,6,10))*1);
        set @partida := ((substring(@clave,44,6))*1);
        set @fondo := substring(@clave,52,7);
        set @obra := substring(@clave,59,6);
            
            select *
            from (
                select 'Sector Público' descripcion, vel.clv_sector_publico clave,vel.sector_publico concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Sector Público Financiero/No Financiero' descripcion, vel.clv_sector_publico_f clave,vel.sector_publico_f concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Sector Economía' descripcion, vel.clv_sector_economia clave,vel.sector_economia concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Subsector Economía' descripcion,vel.clv_subsector_economia clave,vel.subsector_economia concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Ente Público' descripcion,vel.clv_ente_publico clave,vel.ente_publico concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Entidad Federativa' descripcion,vcg.clv_entidad_federativa clave,vcg.entidad_federativa concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Región' descripcion,vcg.clv_region clave,vcg.region concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Municipio' descripcion,vcg.clv_municipio clave,vcg.municipio concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Localidad' descripcion,vcg.clv_localidad clave,vcg.localidad concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Unidad Programática Presupuestal' descripcion,vel.clv_upp clave,vel.upp concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Subsecretaría' descripcion,vel.clv_subsecretaria clave,vel.subsecretaria concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Unidad Responsable' descripcion,vel.clv_ur clave,vel.ur concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Finalidad' descripcion,vel.clv_finalidad clave,vel.finalidad concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Función' descripcion,vel.clv_funcion clave,vel.funcion concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Subfunción' descripcion,vel.clv_subfuncion clave,vel.subfuncion concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Eje' descripcion,vel.clv_eje clave,vel.eje concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Linea de Acción' descripcion,vel.clv_linea_accion clave,vel.linea_accion concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Programa Sectorial' descripcion,vel.clv_programa_sectorial clave,vel.programa_sectorial concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Tipología General' descripcion,vel.clv_tipologia_conac clave,vel.clv_tipologia_conac concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Programa Presupuestal' descripcion,vel.clv_programa clave,vel.programa concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Subprograma Presupuestal' descripcion,vel.clv_subprograma clave,vel.subprograma concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Proyecto Presupuestal' descripcion,vel.clv_proyecto clave,vel.proyecto concepto from v_epp_llaves vel where ejercicio = anio and vel.llave like @epp union all
                select 'Mes de Afectación' descripcion,substring(@clave,38,6) clave, 'Mes de Afectación' union all
                select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Año (Fondo del Ramo)' descripcion,substring(@clave,50,2) clave, 'Año' concepto union all
                select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where deleted_at is null and po.clv_proyecto_obra like @obra
            ) tabla;
        END;");

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
            END;
        ");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_II(in anio int, in corte date)
        begin
            set @corte := 'mm.deleted_at is null';
            if (corte is not null) then 
                set @corte := CONCAT('mm.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            
            set @fromj := CONCAT('from mml_mir mm
            join metas m on m.mir_id = mm.id
            left join pp_aplanado pa 
            on pa.clv_upp = mm.clv_upp  
            and pa.clv_ur = mm.clv_ur
            and pa.clv_finalidad = substring(mm.area_funcional,1,1)
            and pa.clv_funcion = substring(mm.area_funcional,2,1)
            and pa.clv_subfuncion = substring(mm.area_funcional,3,1)
            and pa.clv_eje = substring(mm.area_funcional,4,1)
            and pa.clv_linea_accion = substring(mm.area_funcional,5,2)
            and pa.clv_programa_sectorial = substring(mm.area_funcional,7,1)
            and pa.clv_tipologia_conac = substring(mm.area_funcional,8,1)
            and pa.clv_programa = substring(mm.area_funcional,9,2)
            and pa.clv_subprograma = substring(mm.area_funcional,11,3)
            and pa.clv_proyecto = substring(mm.area_funcional,14,3)
            where mm.ejercicio = ',anio,' and mm.tipo_indicador = 13 and ',@corte);
            set @query := CONCAT('
                select 
                    clv_upp,
                    upp,
                    clv_fuente_financiamiento,
                    fuente_financiamiento,
                    clv_programa,
                    programa,
                    clv_subprograma,
                    subprograma,
                    indicador,
                    objetivo,
                    actividad,
                    count(metas) metas,
                    sum(importe) importe
                from (
                    select distinct
                        pa.clv_upp,
                        pa.upp,
                        pa.clv_fuente_financiamiento,
                        pa.fuente_financiamiento,
                        pa.clv_programa,
                        pa.programa,
                        pa.clv_subprograma,
                        pa.subprograma,
                        \"\" indicador,
                        \"\" objetivo,
                        \"\" actividad,
                        0 metas,
                        0 importe
                    ',@fromj,'
                    union all
                    select distinct
                        pa.clv_upp,
                        pa.upp,
                        pa.clv_fuente_financiamiento,
                        pa.fuente_financiamiento,
                        pa.clv_programa,
                        pa.programa,
                        pa.clv_subprograma,
                        pa.subprograma,
                        mm.indicador,
                        mm.objetivo,
                        mm.definicion_indicador actividad,
                        m.id metas,
                        m.total importe
                    ',@fromj,'
                ) t
                group by clv_upp,upp,clv_fuente_financiamiento,fuente_financiamiento,
                    clv_programa,programa,clv_subprograma,subprograma,
                    indicador,objetivo,actividad;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_IX(in anio int, in corte date)
        begin
            set @corte := 'mm.deleted_at is null';
            if (corte is not null) then 
                set @corte := CONCAT('mm.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @fjw := CONCAT('from mml_mir mm
            join metas m on m.mir_id = mm.id
            join v_epp ve on mm.id_epp = ve.id
            where mm.ejercicio = ',anio,' and mm.tipo_indicador = 13 and ',@corte,'
            ');
            set @query := CONCAT('
                select 
                    case 
                        when indicador != \"\" then \"\"
                        else clv_programa
                    end clv_programa,
                    case 
                        when indicador != \"\" then \"\"
                        else programa
                    end programa,
                    indicador,
                    objetivo,
                    actividad,
                    metas,
                    importe
                from (
                    select 
                        ve.clv_programa,
                        ve.programa,
                        \"\" indicador,
                        \"\" objetivo,
                        \"\" actividad,
                        0 metas,
                        0 importe
                    ',@fjw,'
                    group by ve.clv_programa,ve.programa
                    union all
                    select 
                        ve.clv_programa,
                        ve.programa,
                        mm.indicador,
                        mm.objetivo,
                        mm.definicion_indicador actividad,
                        count(m.id) metas,
                        sum(m.total) importe
                    ',@fjw,'
                    group by ve.clv_programa,ve.programa,mm.indicador,mm.objetivo,mm.definicion_indicador
                ) t;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END");

        DB::unprepared("CREATE PROCEDURE sp_epp(in delegacion int,in uppC varchar(3),in urC varchar(2), in anio int)
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
        END");

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
                            clv_upp,
                            upp,
                            clv_programa,
                            programa
                        from v_epp
                        where ejercicio = ',anio,'
                        and con_mir = 1
                        and deleted_at is null
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
        END");

        DB::unprepared("CREATE PROCEDURE llenado_cierres_etapas()
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
        END");

        DB::unprepared("CREATE PROCEDURE llenado_nuevo_anio(in anio int,in usuario varchar(45))
        begin
            set @id := (select max(id) from catalogo);
            set @ejercicio:= (select max(ejercicio) from catalogo);
        
            #Pasar catalogo actual a historico
            insert into catalogo_hist(
                id_original,grupo_id,ejercicio,clave,descripcion,deleted_at,created_user,updated_user,deleted_user,created_at,updated_at
            )
            select 
                id,grupo_id,ejercicio,clave,descripcion,
                now() deleted_at,created_user,updated_user,deleted_user,created_at,updated_at
            from catalogo where ejercicio = @ejercicio;
        
            delete from catalogo where ejercicio = @ejercicio;
        
            #Crear el nuevo catalogo
            insert into catalogo(id,grupo_id,clave,descripcion,deleted_at,created_user,updated_user,deleted_user,created_at,updated_at,ejercicio)
            select 
                (@id:=@id+1) id,
                grupo,
                clave,
                descripcion,
                null deleted_at,
                usuario created_user,
                null updated_user,
                null deleted_user,
                now() created_at,
                now() updated_at,
                anio ejercicio
            from (
                select distinct 1 grupo,clv_sector_publico clave,sector_publico descripcion from epp_aux union all
                select distinct 2 grupo,clv_sector_publico_f clave,sector_publico_f descripcion from epp_aux union all
                select distinct 3 grupo,clv_sector_economia clave,sector_economia descripcion from epp_aux union all
                select distinct 4 grupo,clv_subsector_economia clave,subsector_economia descripcion from epp_aux union all
                select distinct 5 grupo,clv_ente_publico clave,ente_publico descripcion from epp_aux union all
                select distinct 6 grupo,clv_upp clave,upp descripcion from epp_aux union all
                select distinct 7 grupo,clv_subsecretaria clave,subsecretaria descripcion from epp_aux union all
                select distinct 8 grupo,clv_ur clave,ur descripcion from epp_aux union all
                select distinct 9 grupo,clv_finalidad clave,finalidad descripcion from epp_aux union all
                select distinct 10 grupo,clv_funcion clave,funcion descripcion from epp_aux union all
                select distinct 11 grupo,clv_subfuncion clave,subfuncion descripcion from epp_aux union all
                select distinct 12 grupo,clv_eje clave,eje descripcion from epp_aux union all
                select distinct 13 grupo,clv_linea_accion clave,linea_accion descripcion from epp_aux union all
                select distinct 14 grupo,clv_programa_sectorial clave,programa_sectorial descripcion from epp_aux union all
                select distinct 15 grupo,clv_tipologia_conac clave,tipologia_conac descripcion from epp_aux union all
                select distinct 16 grupo,clv_programa clave,programa descripcion from epp_aux union all
                select distinct 17 grupo,clv_subprograma clave,subprograma descripcion from epp_aux union all
                select distinct 18 grupo,clv_proyecto clave,proyecto descripcion from epp_aux
            )t;
        
            #Buscando ID's de catalogo para epp_aux
            update epp_aux ea join catalogo c on ea.clv_sector_publico = c.clave 
            and ea.sector_publico = c.descripcion and c.grupo_id = 1 set ea.id_sector_publico = c.id;
            update epp_aux ea join catalogo c on ea.clv_sector_publico_f = c.clave 
            and ea.sector_publico_f = c.descripcion and c.grupo_id = 2 set ea.id_sector_publico_f = c.id;
            update epp_aux ea join catalogo c on ea.clv_sector_economia = c.clave 
            and ea.sector_economia = c.descripcion and c.grupo_id = 3 set ea.id_sector_economia = c.id;
            update epp_aux ea join catalogo c on ea.clv_subsector_economia = c.clave 
            and ea.subsector_economia = c.descripcion and c.grupo_id = 4 set ea.id_subsector_economia = c.id;
            update epp_aux ea join catalogo c on ea.clv_ente_publico = c.clave 
            and ea.ente_publico = c.descripcion and c.grupo_id = 5 set ea.id_ente_publico = c.id;
            update epp_aux ea join catalogo c on ea.clv_upp = c.clave 
            and ea.upp = c.descripcion and c.grupo_id = 6 set ea.id_upp = c.id;
            update epp_aux ea join catalogo c on ea.clv_subsecretaria = c.clave 
            and ea.subsecretaria = c.descripcion and c.grupo_id = 7 set ea.id_subsecretaria = c.id;
            update epp_aux ea join catalogo c on ea.clv_ur = c.clave 
            and ea.ur = c.descripcion and c.grupo_id = 8 set ea.id_ur = c.id;
            update epp_aux ea join catalogo c on ea.clv_finalidad = c.clave 
            and ea.finalidad = c.descripcion and c.grupo_id = 9 set ea.id_finalidad = c.id;
            update epp_aux ea join catalogo c on ea.clv_funcion = c.clave 
            and ea.funcion = c.descripcion and c.grupo_id = 10 set ea.id_funcion = c.id;
            update epp_aux ea join catalogo c on ea.clv_subfuncion = c.clave 
            and ea.subfuncion = c.descripcion and c.grupo_id = 11 set ea.id_subfuncion = c.id;
            update epp_aux ea join catalogo c on ea.clv_eje = c.clave 
            and ea.eje = c.descripcion and c.grupo_id = 12 set ea.id_eje = c.id;
            update epp_aux ea join catalogo c on ea.clv_linea_accion = c.clave 
            and ea.linea_accion = c.descripcion and c.grupo_id = 13 set ea.id_linea_accion = c.id;
            update epp_aux ea join catalogo c on ea.clv_programa_sectorial = c.clave 
            and ea.programa_sectorial = c.descripcion and c.grupo_id = 14 set ea.id_programa_sectorial = c.id;
            update epp_aux ea join catalogo c on ea.clv_tipologia_conac = c.clave 
            and ea.tipologia_conac = c.descripcion and c.grupo_id = 15 set ea.id_tipologia_conac = c.id;
            update epp_aux ea join catalogo c on ea.clv_programa = c.clave 
            and ea.programa = c.descripcion and c.grupo_id = 16 set ea.id_programa = c.id;
            update epp_aux ea join catalogo c on ea.clv_subprograma = c.clave 
            and ea.subprograma = c.descripcion and c.grupo_id = 17 set ea.id_subprograma = c.id;
            update epp_aux ea join catalogo c on ea.clv_proyecto = c.clave 
            and ea.proyecto = c.descripcion and c.grupo_id = 18 set ea.id_proyecto = c.id;
        
            #Creando el nuevo epp
            insert into epp(
                sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,upp_id,subsecretaria_id,ur_id,finalidad_id,funcion_id,subfuncion_id,eje_id,linea_accion_id,programa_sectorial_id,tipologia_conac_id,programa_id,subprograma_id,proyecto_id,
                ejercicio,presupuestable,con_mir,confirmado,tipo_presupuesto,
                created_at,updated_at,deleted_at,deleted_user,updated_user,created_user,tipo_presupuesto
            )
            select 
                id_sector_publico,id_sector_publico_f,id_sector_economia,id_subsector_economia,id_ente_publico,id_upp,id_subsecretaria,id_ur,id_finalidad,id_funcion,id_subfuncion,id_eje,id_linea_accion,id_programa_sectorial,id_tipologia_conac,id_programa,id_subprograma,id_proyecto,
                anio,1,1,1,null,
                now(),now(),null,null,null,usuario,0
            from epp_aux;
        
            create temporary table subprogramas_id
            select distinct
                id_subprograma
            from epp_aux ea
            where ea.clv_subprograma in ('UUU','21B');
            
            create temporary table programas_id
            select distinct
                id_programa
            from epp_aux ea
            where ea.clv_programa in ('5H','RM');
            
            update epp set presupuestable = 0, con_mir = 0
            where programa_id in (select id_programa from programas_id);
            
            update epp e set con_mir = 0
            where e.subprograma_id in (select id_subprograma from subprogramas_id);
            
            drop temporary table subprogramas_id;
            drop temporary table programas_id;
        
            #Agregando los nuevos datos de entidad_ejecutora
            insert into entidad_ejecutora(
                upp_id,subsecretaria_id,ur_id,ejercicio,deleted_at,created_user,updated_user,deleted_user,created_at,updated_at
            )
            select distinct
                e.upp_id,
                e.subsecretaria_id,
                e.ur_id,
                anio,
                null,
                usuario,
                null,
                null,
                now(),
                now()
            from epp e
            where ejercicio = anio;
        END;");

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
        END");

     /*    DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int)
        begin
            set @upp := '';
            set @upp2 := '';
            set @programa := '';
            set @ur := '';
            set @ur2 := '';
            set @programa2 := '';
            if(upp is not null) then 
                set @upp := CONCAT('and mm.clv_upp = \"',upp,'\"'); 
                set @upp2 := CONCAT('and clv_upp = \"',upp,'\"'); 
            end if;
            if(programa is not null) then
                set @programa := CONCAT('and mm.clv_pp = \"',programa,'\"'); 
                set @programa2 := CONCAT('and clv_programa = \"',programa,'\"'); 
            end if;
            if(ur is not null) then 
                set @ur := CONCAT('and mm.clv_ur = \"',ur,'\"'); 
                set @ur2 := CONCAT('and clv_ur = \"',ur,'\"'); 
            end if;
        
            set @query := concat(\"
                SELECT
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
                FROM (
                    SELECT *
                    FROM (
                        SELECT 
                            mm.id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            ve.proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from mml_mir mm
                        join v_epp ve on ve.id = mm.id_epp and ve.clv_subprograma not in ('UUU','21B')
                        where mm.ejercicio = \",anio,\" and mm.deleted_at is null and mm.clv_pp not in ('5H','RM')
                        and nivel in (10) \",@upp,\" \",@ur,\" \",@programa,\"
                        UNION ALL 
                        SELECT 
                            mm.componente_padre id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            ve.proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from mml_mir mm
                        join v_epp ve on ve.id = mm.id_epp and ve.clv_subprograma not in ('UUU','21B')
                        where mm.ejercicio = \",anio,\" and mm.deleted_at is NULL
                        and nivel IN (11) \",@upp,\" \",@ur,\" \",@programa,\"
                        and mm.clv_pp not in ('5H','RM')
                        UNION ALL 
                        select distinct
                            0 id,
                            ve.clv_upp,
                            ve.clv_programa clv_pp,
                            ve.clv_ur,
                            '' area_funcional,
                            '' proyecto,
                            9 nivel,
                            '' objetivo,
                            '' indicador
                        from v_epp ve
                        where ejercicio = \",anio,\" and deleted_at is NULL \",@upp2,\" \",@ur2,\" \",@programa2,\"
                        and clv_programa not in ('5H','RM') and clv_subprograma not in ('UUU','21B')
                    )t 
                    GROUP BY clv_upp,clv_pp,clv_ur,id,nivel
                    ORDER BY clv_upp,clv_pp,clv_ur,id,nivel
                )t2;
            \");
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;"); */

        //------------------------Producer historico----------------------

        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_III(in anio int, in corte date)
        begin
            select 
                va.clv_upp,va.upp,
                va.clv_ur,va.ur,
                va.clv_programa,va.programa,
                va.clv_subprograma,va.subprograma,
                concat(
                    va.clv_capitulo,
                    va.clv_concepto,
                    va.clv_partida_generica,
                    va.clv_partida_especifica
                ) as clv_partida, va.partida_especifica,
                sum(va.total) importe
            from pp_aplanado va
            where va.ejercicio = anio and va.deleted_at is null
            group by 
                va.clv_upp,va.upp,
                va.clv_ur,va.ur,
                va.clv_programa,va.programa,
                va.clv_subprograma,va.subprograma,
                va.clv_capitulo,
                va.clv_concepto,
                va.clv_partida_generica,
                va.clv_partida_especifica,
                va.clv_tipo_gasto,
                va.partida_especifica;
        END;");

        //------------------------Producer validaciones----------------------

        DB::unprepared("CREATE PROCEDURE validacion_claves(in id_usuario int,in usuario varchar(45),in borrar tinyint)
        begin
            SET SESSION group_concat_max_len = 1000000;
            set @anio := (
                select c.ejercicio from cierre_ejercicio_claves c
                where c.deleted_at is null and estatus = 'Abierto'
                order by ejercicio desc 
                limit 1);
            set @carga := id_usuario;
            
            drop temporary table if exists errores;
            create temporary table errores(
                num_linea text,
                modulo varchar(100),
                error varchar(255)
            );
        
            set @tipo_usuario := (select id_grupo from adm_users where id = id_usuario);
        
            if(@anio is null and @tipo_usuario != 1) then
                insert into errores(num_linea,modulo,error) values
                ('','Ejercicios abiertos','No hay ningún ejercicio abierto');
            else
        
            set @anio := (
                select c.ejercicio from cierre_ejercicio_claves c
                where c.deleted_at is null
                order by ejercicio desc 
                limit 1
            );
            
            #CIERRE EJERCICIO CLAVES
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Cierre de Ejercicio' modulo,
                concat('El ejercicio para la clave ',clv_upp,' esta cerrado') error
            from (
            with aux as (
                select
                    group_concat(id) lista_id,
                    pp.upp clv_upp,
                    (concat('20',pp.anio))*1 ejercicio
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by upp,anio
            )
            select 
                lista_id,aux.clv_upp,estatus
            from aux
            left join cierre_ejercicio_claves ce on ce.clv_upp = aux.clv_upp
            and ce.ejercicio = @anio and ce.deleted_at is null) t
            where estatus != 'Abierto';
            
            #REVISIÓN DE EXISTENCIA DE DATOS EN CATALOGOS -------------------------------------------------------------------------
            
            #CLASIFICACION ADMINISTRATIVA
            insert into errores(num_linea,modulo,error)
            with aux as (
            select distinct
                concat(
                    ve.clv_sector_publico,
                    ve.clv_sector_publico_f,
                    ve.clv_sector_economia,
                    ve.clv_subsector_economia,
                    ve.clv_ente_publico 
                ) administrativa
            from v_epp ve
            where ejercicio = @anio and deleted_at is null)
            select 
                lista_id num_linea,
                'Clasificación Administrativa' modulo,
                concat('La clasificación administrativa ',clasificacion_administrativa,' no existe') error
            from (
                select group_concat(id) lista_id,clasificacion_administrativa 
                from programacion_presupuesto_aux
                where id_carga = @carga
                group by clasificacion_administrativa
            ) pp
            left join aux a on pp.clasificacion_administrativa = a.administrativa
            where administrativa is null;
            
            #CLASIFICACIÓN GEOGRAFICA
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Clasificación Geografica' modulo,
                concat('No existe la clasificación geografica: ',
                entidad_federativa,'-',region,'-',municipio,'-',localidad) error
            from (
            with aux as (
                select
                    group_concat(id) lista_id,
                    pp.entidad_federativa,pp.region,pp.municipio,pp.localidad
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by entidad_federativa,region,municipio,localidad
            )
            select 
                id,
                a.*
            from aux a 
            left join clasificacion_geografica cg on a.entidad_federativa = cg.clv_entidad_federativa
            and a.region = cg.clv_region and a.municipio = cg.clv_municipio 
            and a.localidad = cg.clv_localidad and cg.deleted_at is null)t
            where id is null;
            
            #ENTIDAD EJECUTORA
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Entidad Ejecutora' modulo,
                concat('No existe la entidad ejecutora: ',upp,'-',subsecretaria,'-',ur) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    upp,subsecretaria,ur
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by upp,subsecretaria,ur
            )
            select 
                ve.*,
                a.*
            from aux a
            left join (
                select distinct 
                    clv_upp,clv_subsecretaria,clv_ur
                from v_epp where deleted_at is null and ejercicio = @anio
            ) ve on a.upp = ve.clv_upp
            and a.subsecretaria = ve.clv_subsecretaria and a.ur = ve.clv_ur)t
            where clv_upp is null;
            
            #AREA FUNCIONAL
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Area Funcional' modulo,
                concat('No existe el area funcional: ',
                finalidad,'-',funcion,'-',subfuncion,'-',eje,'-',linea_Accion,'-',
                programa_sectorial,'-',tipologia_conac,'-',programa_presupuestario,'-',
                subprograma_presupuestario,'-',proyecto_presupuestario) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    finalidad,funcion,subfuncion,eje,pp.linea_accion,
                    pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,
                    pp.subprograma_presupuestario,pp.proyecto_presupuestario
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by finalidad,funcion,subfuncion,eje,pp.linea_accion,
                pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,
                pp.subprograma_presupuestario,pp.proyecto_presupuestario
            )
            select 
                e.clv_finalidad,
                a.*
            from aux a
            left join (
                select 
                    ve.clv_finalidad,ve.clv_funcion,ve.clv_subfuncion,ve.clv_eje,
                    ve.clv_linea_accion,ve.clv_programa_sectorial,ve.clv_tipologia_conac,
                    ve.clv_programa,ve.clv_subprograma,ve.clv_proyecto
                from v_epp ve
                where ve.ejercicio = @anio and ve.deleted_at is null
            ) e on a.finalidad = e.clv_finalidad and a.funcion = e.clv_funcion and a.subfuncion = e.clv_subfuncion
            and a.eje = e.clv_eje and a.linea_accion = e.clv_linea_accion 
            and a.programa_sectorial = e.clv_programa_sectorial and a.tipologia_conac = e.clv_tipologia_conac
            and a.programa_presupuestario = e.clv_programa and a.subprograma_presupuestario = clv_subprograma
            and a.proyecto_presupuestario = e.clv_proyecto)t
            where clv_finalidad is null;
            
            #POSICION PRESUPUESTARIA (PARTIDA)
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Posición Presupuestaria (Partida)' modulo,
                concat('No existe la partida: ',partida,' con tipo de gasto ',tipo_gasto) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    pp.posicion_presupuestaria partida,pp.tipo_gasto
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by posicion_presupuestaria,tipo_gasto
            )
            select 
                pp.partida clv_partida,
                a.*
            from aux a
            left join (
                select 
                    concat(
                        pt.clv_capitulo,pt.clv_concepto,
                        pt.clv_partida_generica,pt.clv_partida_especifica
                    ) partida, pt.clv_tipo_gasto
                from posicion_presupuestaria pt
                where pt.deleted_at is null
            ) pp on a.partida = pp.partida
            and a.tipo_gasto = pp.clv_tipo_gasto)t
            where clv_partida is null;
            
            #FONDOS
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Fondos' modulo,
                concat('No existe el fondo: ',etiquetado,'-',fuente_financiamiento,
                '-',ramo,'-',fondo_ramo,'-',capital) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    pp.etiquetado,pp.fuente_financiamiento,
                    pp.ramo,pp.fondo_ramo,pp.capital
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by etiquetado,fuente_financiamiento,
                ramo,fondo_ramo,capital
            )
            select 
                f.clv_etiquetado,
                a.*
            from aux a 
            left join fondo f on a.etiquetado = f.clv_etiquetado 
            and a.fuente_financiamiento = f.clv_fuente_financiamiento 
            and a.ramo = f.clv_ramo and a.fondo_ramo = f.clv_fondo_ramo)t
            where clv_etiquetado is null;
            
            #PROYECTOS OBRA
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Proyectos de Obra' modulo,
                concat('No existe la obra: ',proyecto_obra) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    pp.upp,pp.proyecto_obra
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by upp,proyecto_obra
            )
            select 
                po.id,
                a.*
            from aux a
            left join proyectos_obra po on a.proyecto_obra = po.clv_proyecto_obra
            and po.ejercicio = @anio and po.deleted_at is null)t
            where id is null;
            
            #RELACION PARTIDA - CLASIFICACIÓN ADMINISTRATIVA
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'Relación Clasificacion Administrativa - Partida' modulo,
                concat(
                    'No existe la relación entre la clasificación administrativa: ',clasificacion_administrativa,
                    ' y la posicion presupuestaria (partida): ',clasificacion_economica
                ) error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    pp.clasificacion_administrativa,
                    concat(
                        pp.posicion_presupuestaria,
                        pp.tipo_gasto
                    ) clasificacion_economica
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by clasificacion_administrativa,
                posicion_presupuestaria,tipo_gasto
            )
            select 
                re.id,
                a.*
            from aux a 
            left join rel_economica_administrativa re
            on a.clasificacion_administrativa = re.clasificacion_administrativa
            and a.clasificacion_economica = re.clasificacion_economica)t
            where id is null;
            
            #DEBAJO DE TECHOS FINANCIEROS
            drop temporary table if exists presupuesto;
            if(borrar = 0) then
                create temporary table presupuesto
                select clv_upp,clv_fondo,sum(total) presupuestado
                from (
                    select 
                        ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total
                    from programacion_presupuesto_aux ppa 
                    where id_carga = @carga
                    union all
                    select 
                        ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total presupuestado
                    from programacion_presupuesto ppa
                    where ejercicio = @anio and deleted_at is null
                )t
                group by clv_upp,clv_fondo;
            else
                create temporary table presupuesto
                select clv_upp,clv_fondo,sum(total) presupuestado
                from (
                    select 
                        ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total
                    from programacion_presupuesto_aux ppa 
                    where id_carga = @carga
                )t
                group by clv_upp,clv_fondo;
            end if;
            
            insert into errores(num_linea,modulo,error)
            select 
                 0 num_linea,
                'Revisión con Techos Financieros' modulo,
                concat('La upp: ',clv_upp,', con fondo: ',clv_fondo,', sobrepasa el techo financiero') error
            from (
                select 
                    tf.clv_upp,
                    tf.clv_fondo,
                    sum(presupuesto) asignado,
                    case
                        when pp.presupuestado is null then 0 else pp.presupuestado
                    end presupuestado
                from techos_financieros tf
                left join presupuesto pp on tf.clv_upp = pp.clv_upp and tf.clv_fondo = pp.clv_fondo
                where tf.deleted_at is null and tf.ejercicio = @anio
                group by tf.clv_upp,tf.clv_fondo,presupuestado
            )t
            where presupuestado > asignado;
            drop temporary table if exists presupuesto;
            
            #EPP PRESUPUESTABLE
            insert into errores(num_linea,modulo,error)
            select 
                lista_id num_linea,
                'EPP Presupuestable' modulo,
                concat('La clave no es presupuestable o no se encuentra en el epp') error
            from (
            with aux as (
                select 
                    group_concat(id) lista_id,
                    upp,subsecretaria,ur,
                    finalidad,funcion,subfuncion,eje,linea_accion,
                    programa_sectorial,tipologia_conac,programa_presupuestario,
                    subprograma_presupuestario,proyecto_presupuestario
                from programacion_presupuesto_aux pp
                where id_carga = @carga
                group by upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,
                programa_sectorial,tipologia_conac,programa_presupuestario,
                subprograma_presupuestario,proyecto_presupuestario
            )
            select 
                e.clv_finalidad,
                a.*
            from aux a
            left join (
                select 
                    ve.clv_upp,ve.clv_subsecretaria,ve.clv_ur,
                    ve.clv_finalidad,ve.clv_funcion,ve.clv_subfuncion,ve.clv_eje,
                    ve.clv_linea_accion,ve.clv_programa_sectorial,ve.clv_tipologia_conac,
                    ve.clv_programa,ve.clv_subprograma,ve.clv_proyecto
                from v_epp ve
                where ve.ejercicio = @anio and ve.deleted_at is null and presupuestable = 1
            ) e on a.upp = e.clv_upp and a.subsecretaria = e.clv_subsecretaria and a.ur = e.clv_ur
            and a.finalidad = e.clv_finalidad and a.funcion = e.clv_funcion and a.subfuncion = e.clv_subfuncion
            and a.eje = e.clv_eje and a.linea_accion = e.clv_linea_accion 
            and a.programa_sectorial = e.clv_programa_sectorial and a.tipologia_conac = e.clv_tipologia_conac
            and a.programa_presupuestario = e.clv_programa and a.subprograma_presupuestario = clv_subprograma
            and a.proyecto_presupuestario = e.clv_proyecto
            where clv_finalidad is null)t;
            
            # ---------------------------------------------------------------------------------------------------------------------
            
            #CLAVES REPETIDAS *****************************************************************************************************
            
            if(borrar = 0) then
                #Ambas Tablas 
                insert into errores(num_linea,modulo,error)
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        sum(cantidad) cantidad,
                        clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                        upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                    from (
                        select 
                            id,
                            1 cantidad,
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        from programacion_presupuesto_aux ppa 
                        where id_carga = @carga
                        union all
                        select 
                            0 id,
                            1 cantidad,
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        from programacion_presupuesto ppa 
                        where ejercicio = @anio and deleted_at is null
                    )t
                    group by clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
                    finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                    programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                    periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                )
                select 
                    lista_id num_linea,
                    'Claves Repetidas' modulo,
                    'Existen Claves Repetidas, las lineas que dicen 0 es porque esa clave ya registrada y no del excel' error
                from aux
                where cantidad > 1;
            else 
                #Una Sola Tabla
                insert into errores(num_linea,modulo,error)
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        sum(cantidad) cantidad,
                        clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                        upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                    from (
                        select 
                            id,
                            1 cantidad,
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        from programacion_presupuesto_aux ppa 
                        where id_carga = @carga
                    )t
                    group by clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
                    finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                    programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                    periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                )
                select 
                    lista_id num_linea,
                    'Claves Repetidas' modulo,
                    'Existen Claves Repetidas, las lineas que dicen 0 es porque esa clave ya registrada y no del excel' error
                from aux
                where cantidad > 1;
            end if;
        
            #*********************************************************************************************************************
            
            #TOTAL IGUAL A MESES
            insert into errores(num_linea,modulo,error)
            with aux as (
                select 
                    group_concat(id) num_linea
                from (
                    select 
                        id,
                        total,
                        (
                            enero+febrero+marzo+abril+mayo+
                            junio+julio+agosto+septiembre+
                            octubre+noviembre+diciembre
                        ) meses
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                )t where total != meses
            )
            select 
                num_linea,
                'Total y Meses iguales' modulo,
                'Las claves no pueden tener diferente la suma de sus meses y el total' error
            from aux
            where num_linea is not null;
            
            #PRESUPUESTO EN 0
            insert into errores(num_linea,modulo,error)
            select 
                num_linea,
                'Presupuesto en 0' modulo,
                'La clave no puede tener un total de 0' error
            from (
                select 
                    group_concat(id) num_linea,
                    'Presupuesto en 0' modulo,
                    'La clave no puede tener un total de 0' error
                from programacion_presupuesto_aux pp
                where id_carga = @carga and total < 1
            )t where num_linea is not null;
            
            #DECIMALES
            insert into errores(num_linea,modulo,error)
            with aux as (
                select group_concat(id) num_linea from (
                select 
                    id,
                    ((floor(enero)-enero)+(floor(febrero)-febrero)+(floor(marzo)-marzo)+
                    (floor(abril)-abril)+(floor(mayo)-mayo)+(floor(junio)-junio)+
                    (floor(julio)-julio)+(floor(agosto)-agosto)+(floor(septiembre)-septiembre)+
                    (floor(octubre)-octubre)+(floor(noviembre)-noviembre)+(floor(diciembre)-diciembre)) meses_d
                from programacion_presupuesto_aux pp
                where id_carga = @carga)t where meses_d < 0
            )
            select 
                num_linea,
                'Decimales' modulo,
                'No se deben ingresar decimales' error
            from aux where num_linea is not null;
            
            #TAMAÑO DE CLAVES CORRESPONDIENTE
            insert into errores(num_linea,modulo,error)
            with aux as (
                select 
                    id,
                    length(concat(
                        clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                        upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                    )) clave
                from programacion_presupuesto_aux pp
                where id_carga = @carga
            )
            select *
            from (
                select 
                    group_concat(id) num_linea,
                    'Tamaño de Clave' modulo,
                    'Existe una clave con un número menor de carácteres' error
                from aux
                where clave < 64
            )t where num_linea is not null;
                
            #REVISIÓN DE UUU, CAPITULO 1000 Y PARTIDA 39801
            insert into errores(num_linea,modulo,error)
            with aux as (
                select a.*,u.clv_upp
                from (
                    select
                        group_concat(id) lista_id,upp,
                        subprograma_presupuestario subprograma,
                        substr(posicion_presupuestaria,1,1) capitulo,
                        posicion_presupuestaria partida
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by upp,subprograma_presupuestario,posicion_presupuestaria
                ) a
                left join uppautorizadascpnomina u on a.upp = u.clv_upp 
                and u.deleted_at is null
            )
            select 
                lista_id num_linea,
                'UPPS Autorizadas' modulo,
                concat('La UPP: ',upp,', no tiene autorizado cargar el capitulo 1000') error
            from aux
            where clv_upp is null and capitulo = '1'
            union all 
            select 
                lista_id num_linea,
                'UPPS Autorizadas' modulo,
                concat('La UPP: ',upp,', no tiene autorizado cargar la partida 39801') error
            from aux
            where clv_upp is null and partida = '39801'
            union all 
            select 
                lista_id num_linea,
                'UPPS Autorizadas' modulo,
                concat('La UPP: ',upp,', no tiene autorizado cargar el subprograma UUU') error
            from aux
            where clv_upp is null and subprograma = 'UUU';
        
            set @c_errores := (select count(*) cantidad from errores);
            if(@c_errores = 0) then
                update programacion_presupuesto_aux set ejercicio = @anio where id_carga = @carga;
            
                if(@tipo_usuario = 5) then
                    update programacion_presupuesto_aux set tipo = 'RH' where id_carga = @carga;
                else
                    update programacion_presupuesto_aux set tipo = 'Operativo' where id_carga = @carga;
                end if;
        
                set @usuario := concat(usuario,'-Carga_Masiva_Claves');
            
                insert into programacion_presupuesto(
                    clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                    upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                    programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                    periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,
                    etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,
                    ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,
                    estado,tipo,deleted_at,created_user,updated_user,deleted_user,created_at,updated_at)
                select 
                    clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                    upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                    programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                    periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,
                    etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,
                    ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,
                    0,tipo,null,@usuario,null,null,now(),now()
                from programacion_presupuesto_aux a
                where id_carga = @carga;
            end if;
        
            end if;
        
            delete from programacion_presupuesto_aux where id_carga = @carga;
            
            select * from errores;
            drop temporary table if exists errores;
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

        DB::unprepared("CREATE PROCEDURE reporte_calendario(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in subprograma_v varchar(3),in proyecto_v varchar(3))
        begin
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
            drop temporary table if exists aux_4;

            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @subprograma := '';
            set @proyecto := '';

            if(upp_v is null) 
                then set @upp := concat(' where clv_upp =  ',upp_v); 
            end if;
            if(ur_v is null) 
                then set @ur := concat('and ',ur_v); 
            end if;
            if(programa_v is null) 
                then set @programa := concat('and ',programa_v); 
            end if;
            if(subprograma_v is null) 
                then set @subprograma := concat('and ',subprograma_v); 
            end if;
            if(proyecto_v is null) 
                then set @proyecto := concat('and ',proyecto_v); 
            end if;
            
            set @queri := concat('
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
                where m.ejercicio = ',anio,' and m.deleted_at is null
            )t2;
            ');

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat('
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
                        where e.ejercicio = ',anio,' and e.deleted_at is null
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
                    where ejercicio = ',anio,' and deleted_at is null
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
            ');

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;

            create temporary table aux_3
            select *
            from aux_2 
            where concat(clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto) in (
                select distinct concat(clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto) from aux_1
            );
            
            set @queri := concat('
            create temporary table aux_4
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
            ');

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
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
            from aux_4;
            
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
            drop temporary table if exists aux_3;
            drop temporary table if exists aux_4;
        END;");

        DB::unprepared("CREATE PROCEDURE sp_report_etapa4_mir(IN upp VARCHAR(4), IN pp VARCHAR(4), IN ejercicio INT(6))
        BEGIN
            SELECT mao.tipo, mao.indice, mao.descripcion, mao.seleccion_mir, ifnull(mao.tipo_indicador, '.') as tipo_indicador
            FROM mml_arbol_objetivos as mao
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio AND mao.seleccion_mir = 1 AND mao.tipo_indicador IS NULL AND mao.deleted_at IS NULL
            UNION
            SELECT 'Proposito' as tipo,  '' as indice, mdp.objetivo_central as descripcion, '0' as seleccion_mir, '.' as tipo_indicador 
            FROM mml_definicion_problema as mdp
            WHERE mdp.clv_upp = upp AND mdp.clv_pp = pp AND mdp.ejercicio = ejercicio AND mdp.deleted_at IS NULL
            UNION
            SELECT mao.tipo, @c := @c + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @c := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = 'Componente' AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1
            UNION
            SELECT mao.tipo,  @a := @a + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @a := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = 'Actividad' AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1;
        END;");

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
                set @upp := concat(' where clv_upp =  \"',upp_v,'\");
                set @upp2 := concat(' and upp =  \"',upp_v,'\");
            end if;
            if(ur_v is not null) then 
                set @ur := concat(' and clv_ur =  \"',ur_v,'\");
                set @ur2 := concat(' and ur = \"',ur_v,'\"); 
            end if;
            if(programa_v is not null) then 
                set @programa := concat(' and clv_programa = \"',programa_v,'\"); 
                set @programa2 := concat(' and programa_presupuestario = \"',programa_v,'\"); 
            end if;
            if(subprograma_v is not null) then 
                set @subprograma := concat(' and clv_subprograma = \"',subprograma_v,'\");
                set @subprograma2 := concat(' and subprograma_presupuestario = \"',subprograma_v,'\");
            end if;
            if(proyecto_v is not null) then 
                set @proyecto := concat(' and clv_proyecto = \"',proyecto_v,'\";
                set @proyecto2 := concat(' and proyecto_presupuestario = \"',proyecto_v,'\");
            end if;
            
            set @queri := concat('
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
                where m.ejercicio = ',anio,' and m.deleted_at is null
            )t2',@upp,'',@ur,'',@programa,'',@subprograma,'',@proyecto,';
            ');

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat('
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
                        where e.ejercicio = ',anio,' and e.deleted_at is null
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
                    where ejercicio = ',anio,' and deleted_at is null',@upp2,'',@ur2,'',@programa2,'',@subprograma2,'',@proyecto2,'
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
            ');

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
        END;");

        DB::unprepared("CREATE PROCEDURE sapp_reporte_presupuesto_1(in anio int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            if(upp_v is not null) then
                set @upp := concat(' and clv_upp = \"',upp_v,'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(' and clv_programa = \"',programa_v,'\");
            end if;
            
            set @queri := concat('
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
                    where sm.ejercicio = ',anio,'',@upp,'',@programa,'
                    group by clv_upp,clv_programa
                    order by clv_upp,clv_programa
                )t
                join catalogo c1 on t.clv_upp = c1.clave and c1.ejercicio = ',anio,'
                and c1.deleted_at is null and c1.grupo_id = 6
                join catalogo c2 on t.clv_programa = c2.clave and c2.ejercicio = ',anio,'
                and c2.deleted_at is null and c2.grupo_id = 16
            )
            select 
                a.*,
                case 
                    when a.modificado = 0 then 0
                    else ((a.modificado/a.devengado)*100)
                end cumplimiento,
                t.original original_t,
                t.ampliacion ampliacion_t,
                t.modificado modificado_t,
                t.comprometido comprometido_t,
                t.devengado devengado_t,
                t.ejercido ejercido_t,
                case 
                    when t.modificado = 0 then 0
                    else ((t.modificado/t.devengado)*100)
                end cumplimiento_t
            from aux a
            left join (
                select 
                    clv_upp,'' clv_programa,upp,'' programa,sum(original) original,sum(ampliacion) ampliacion,
                    sum(modificado) modificado,sum(comprometido) comprometido,
                    sum(devengado) devengado,sum(ejercido) ejercido,
                    case 
                        when sum(modificado) = 0 then 0
                        else ((sum(modificado)/sum(devengado))*100)
                    end cumplimiento
                from aux
                group by clv_upp,upp
            )t on a.clv_upp = t.clv_upp;
            ');

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        END;");

        DB::unprepared("CREATE PROCEDURE reporte_presupuesto_2(in anio int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            if(upp_v is not null) then
                set @upp := concat(' and clv_upp = \"',upp_v,'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(' and clv_programa = \"',programa_v,'\");
            end if;

            set @queri := concat(\"
            select 
                t.*,
                case 
                    when programado = 0 then (realizado*100)
                    else ((realizado/programado)*100)
                end avance,
                (realizado-programado) diferencia
            from (
                select 
                    ss.mes mes_n,ss.clv_upp,ss.clv_programa
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
                where ss.ejercicio = ',anio,' and ss.deleted_at is null',@upp,'',@programa,'
                group by ss.clv_upp,ss.clv_programa,ss.mes
                order by clv_upp,clv_programa,mes_n,programado
            )t;
            \");

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        END;");   

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
            set @upp := concat(' and clv_upp = ',upp);
        end if;
        if(programa is not null) then
            set @programa := concat(' and clv_programa = ',programa);
        end if;
        if(fondo is not null) then
            set @fondo := concat(' and substr(fondo,7,2) = ',fondo);
        end if;
        if(capitulo is not null) then
            set @capitulo := concat(' and substr(partida,1,1) = ',capitulo);
        end if;
        if(mes is not null) then
            set @mes := concat(' and mes = ',mes);
        end if;

        set @queri := concat('
        create temporary table t_capitulo
        select 
            clv_upp,c1.descripcion upp,clv_programa,c2.descripcion programa,
            clv_fondo,f2.fondo_ramo fondo,concat(f.clv_capitulo,\"000\") clv_capitulo,pp.capitulo,
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
                where ejercicio = ',anio,'',@mes,'',@upp,'',@programa,'',@fondo,'',@capitulo,'
            )t
            group by clv_upp,clv_programa,clv_fondo,clv_capitulo
        )f
        left join catalogo c1 on f.clv_upp = c1.clave and 
        c1.grupo_id = 6 and c1.ejercicio = ',anio,' and c1.deleted_at is null
        left join catalogo c2 on f.clv_programa = c2.clave and 
        c2.grupo_id = 16 and c2.ejercicio = ',anio,' and c2.deleted_at is null
        left join fondo f2 on f.clv_fondo = f2.clv_fondo_ramo and f2.deleted_at is null
        left join (
            select distinct
                clv_capitulo,capitulo
            from posicion_presupuestaria
            where deleted_at is null
        ) pp on f.clv_capitulo = pp.clv_capitulo
        order by clv_upp,clv_programa,clv_fondo,clv_capitulo;
        ');

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
                else ((devengado_u/modificado_u)*100)
            end cumplimiento_u,
            tp.clv_programa,tp.programa,
            original_p,reducciones_p,modificado_p,comprometido_p,devengado_p,
            case 
                when modificado_p = 0 then 0
                else ((devengado_p/modificado_p)*100)
            end cumplimiento_p,
            tf.clv_fondo,tf.fondo,
            original_f,reducciones_f,modificado_f,comprometido_f,devengado_f,
            case 
                when modificado_f = 0 then 0
                else ((devengado_f/modificado_f)*100)
            end cumplimiento_f,
            clv_capitulo,tc.capitulo,
            original,reducciones,modificado,comprometido,devengado,
            case 
                when modificado = 0 then 0
                else ((devengado/modificado)*100)
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
        END;"); 

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_1(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2), in fondo_v varchar(2), in capitulo_v varchar(1))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';

            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;

            if(upp_v is not null) then 
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
            if(ur_v is not null) then 
                set @ur := concat(\" and clv_ur = '\",ur_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            if(fondo_v is not null) then 
                set @fondo := concat(\" and substr(fondo,7,2) = '\",fondo_v,\"'\");
            end if;
            if(capitulo_v is not null) then 
                set @capitulo := concat(\" and substr(partida,1,1) = '\",capitulo_v,\"'\");
            end if;
            
            set @queri := concat(\"
            create temporary table aux_1
            with aux as (
                select 
                    clv_upp,clv_ur,clv_programa,clv_fondo,partida clv_partida,
                    sum(original_sapp) original,sum(ampliacion) ampliacion,
                    sum(modificado) modificado,sum(comprometido) comprometido,
                    sum(devengado) devengado,
                    case 
                        when sum(modificado) = 0 then 0
                        else ((sum(devengado)/sum(modificado))*100)
                    end cumplimiento
                from (
                    select 
                        sm.clv_upp,sm.clv_ur,sm.clv_programa,
                        substr(sm.fondo,7,2) clv_fondo,sm.partida,sm.original_sapp,
                        case 
                            when sm.ampliacion > 0 then sm.ampliacion
                            when sm.reduccion > 0 then sm.reduccion
                            else 0
                        end ampliacion,
                        sm.modificado,
                        sm.comprometido,
                        sm.devengado
                    from sapp_movimientos sm
                    where sm.ejercicio = \",anio,\"\",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"
                )t
                group by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida
                order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida
            )
            select 
                a.clv_upp,a.clv_ur,a.clv_programa,a.clv_fondo,a.clv_partida,pp.partida,
                a.original,a.ampliacion,a.modificado,a.comprometido,a.devengado,a.cumplimiento
            from aux a 
            left join (
                select 
                    concat(
                        clv_capitulo,
                        clv_concepto,
                        clv_partida_generica,
                        clv_partida_especifica,
                        clv_tipo_gasto
                    ) clv_partida,
                    partida_especifica partida
                from posicion_presupuestaria
                where deleted_at is null
            ) pp on a.clv_partida = pp.clv_partida;
            \");

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;

            create temporary table aux_2
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_ur,c2.descripcion ur
            from (
                select distinct
                    e.upp_id,e.ur_id
                from epp e
                where ejercicio = anio and deleted_at is null
            )t
            join catalogo c1 on t.upp_id = c1.id
            join catalogo c2 on t.ur_id = c2.id;

            select 
                a1.clv_upp,a1.clv_ur,a2.ur,clv_programa,clv_fondo,clv_partida,partida,
                original,ampliacion,modificado,comprometido,devengado,cumplimiento
            from aux_1 a1
            left join aux_2 a2 on a1.clv_upp = a2.clv_upp 
            and a1.clv_ur = a2.clv_ur
            order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida;

            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
        END;");   

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_2(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2), in fondo_v varchar(2))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';

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

            set @queri := concat(\"
            with aux as (
                select 
                    clv_upp,clv_ur,clv_programa,clv_fondo,mes_n,actividad,unidad_medida,beneficiarios,
                    mes,programado,realizado,(realizado-programado) diferencia,0 avance,
                    justificacion,propuesta_mejora
                from (
                    select 
                        ss.clv_upp,ss.clv_ur,ss.clv_programa,m.clv_fondo,ss.mes mes_n,
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
                    left join mml_actividades ma on m.actividad_id = ma.id
                    left join mml_mir mm on m.mir_id = mm.id
                    left join catalogo c on ma.id_catalogo = c.id
                    left join beneficiarios b on m.beneficiario_id = b.id
                    left join unidades_medida um on m.unidad_medida_id = um.id
                    where ss.ejercicio = \",anio,\" and ss.deleted_at is null
                    order by clv_upp,clv_ur,clv_programa,clv_fondo,mes
                )t \",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"
            )
            select *
            from (
                select distinct 
                    clv_upp,clv_ur,clv_programa,clv_fondo,0 mes_n,actividad,unidad_medida,beneficiarios,
                    '' mes,'' programado,'' realizado,'' diferencia,'' avance,'' justificacion,'' propuesta_mejora
                from aux
                union all
                select * from aux
                order by clv_upp,clv_ur,clv_programa,clv_fondo,mes_n
            )t;
            \");

            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
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
            select 
                c1.clave clv_upp, c1.descripcion upp,
                c2.clave clv_ur, c2.descripcion ur
            from (
                select distinct 
                    upp_id,ur_id
                from epp e
                where ejercicio = 2024 and deleted_at is null
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
        END;");

    DB::unprepared("CREATE PROCEDURE mml_presupuesto_egresos(in anio int,in upp_v varchar(3),in ur_v varchar(2),in pp_v varchar(2),in eje_v varchar(1))
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
            from metas m
            left join mml_mir mm on m.mir_id = mm.id
            left join mml_actividades ma on m.actividad_id = ma.id
            left join catalogo c on ma.id_catalogo = c.id
            left join unidades_medida um on m.unidad_medida_id = um.id
            left join beneficiarios b on m.beneficiario_id = b.id
            where m.ejercicio = \",anio,\" and m.deleted_at is null
        )t \",@upp,\"\",@ur,\"\",@pp,\"\",@eje,\";
        \");

        prepare stmt from @queri;
        execute stmt;
        deallocate prepare stmt;
        
        create temporary table catalogo_aux
        with aux as (
            select distinct
                e.upp_id,e.ur_id,e.programa_id,e.eje_id,e.linea_accion_id
            from epp e
            where e.ejercicio = anio and e.deleted_at is null
        )
        select 
            c1.clave clv_upp,c1.descripcion upp,
            c2.clave clv_ur,c2.descripcion ur,
            c3.clave clv_programa,c3.descripcion programa,
            c4.clave clv_eje,c4.descripcion eje,
            c5.clave clv_linea_accion,c5.descripcion linea_accion
        from aux a
        left join catalogo c1 on a.upp_id = c1.id
        left join catalogo c2 on a.ur_id = c2.id 
        left join catalogo c3 on a.programa_id = c3.id
        left join catalogo c4 on a.eje_id = c4.id
        left join catalogo c5 on a.linea_accion_id = c5.id;
        
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
    END;
    ");

    DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int)
    BEGIN
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
        from mml_mir mm
        left join unidades_medida um on mm.unidad_medida = um.id
        left join metas m on mm.id = m.mir_id
        left join seguimiento ss on ss.meta_id = m.id
        where mm.ejercicio = anio and mm.deleted_at is null;
        
        create temporary table catalogo_aux
        select 
            id,clave clv_programa,descripcion programa
        from catalogo 
        where ejercicio = anio and deleted_at is null and grupo_id in (16);
        
        create temporary table epp_aux
        with aux as (
            select distinct
                upp_id,
                ur_id
            from epp
            where ejercicio = anio and deleted_at is null
        )
        select 
            c1.clave clv_upp, c1.descripcion upp,
            c2.clave clv_ur, c2.descripcion ur
        from aux a
        left join catalogo c1 on c1.id = a.upp_id
        left join catalogo c2 on c2.id = a.ur_id;
        
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
                from catalogo where ejercicio = anio
                and deleted_at is null and grupo_id = 6
            ) c1 on a1.clv_upp = c1.clv_upp
        )t
        order by clv_upp,clv_programa,clv_ur,nivel;

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
    END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_check_permission");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_menu_sidebar");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS conceptos_clave;");
        DB::unprepared("DROP PROCEDURE IF EXISTS insert_pp_aplanado;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_avance_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS proyecto_calendario_actividades;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_III;");
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
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_AF_EE;");
        DB::unprepared("DROP PROCEDURE IF EXISTS lista_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_II;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_IX;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_etapas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_cierres;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_etapas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenado_epp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS crear_tabla_auxiliar;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_etapas_upp_programa;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS validaciones;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_report_etapa4_mir;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_calendario;");
        DB::unprepared("DROP PROCEDURE IF EXISTS presupuesto_sap;");
        DB::unprepared("DROP PROCEDURE IF EXISTS inicio_a;");
        DB::unprepared("DROP PROCEDURE IF EXISTS inicio_b;");
        DB::unprepared("DROP PROCEDURE IF EXISTS corte_anual_no_pp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS validacion_claves;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_reporte_calendario;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_presupuesto_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_ingresos;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS seguimiento_totales;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_presupuesto_egresos;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores");
    }
};
