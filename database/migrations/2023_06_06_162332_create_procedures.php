<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_III(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
            
            create temporary table aux_0
            select distinct
                clv_upp,upp,
                clv_ur,ur,
                clv_programa,programa,
                clv_subprograma,subprograma,
                clv_proyecto,proyecto
            from v_epp 
            where ejercicio = anio and deleted_at is null;
            
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
            
            select 
                a1.clv_upp,a0.upp,
                a1.clv_ur,a0.ur,
                a1.clv_programa,a0.programa,
                a1.clv_subprograma,a0.subprograma,
                a1.clv_proyecto,a0.proyecto,
                a1.pos_pre,
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
            pp.partida_especifica;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
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
        
            set @consulta := concat('
            select 
                pos,
                sum(total) importe
            from (
                select 
                    (substring(pp.posicion_presupuestaria,1,2)*1) pos, 
                    pp.total
                from ',@tabla,' pp
                where ejercicio = ',anio,' and pp.',@corte,'
            )t
            group by pos order by pos
            ');
        
            set @query := concat(\"
            with aux as (\",@consulta,\")
            select 
                conceptos,
                case 
                    when importe is null then 0
                    else importe
                end importe
            from (
            select 
                'Gasto Corriente' conceptos,
                sum(importe) importe
            from aux
            where pos between 10 and 49
            and pos not in (45)
            union all
            select 
                'Gasto Capital' conceptos,
                sum(importe) importe
            from aux
            where pos between 50 and 79
            union all
            select 
                'Amortizaciones' conceptos,
                sum(importe) importe
            from aux
            where pos between 90 and 99
            union all
            select 
                'Participaciones' conceptos,
                sum(importe) importe
            from aux
            where pos between 80 and 89
            union all
            select 
                'Pensiones y Jubilaciones' conceptos,
                sum(importe) importe
            from aux
            where pos = 45)t;
            \");
        
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
                    
            set @tablas := CONCAT('
            create temporary table aux_0
            with aux as (
                select distinct 
                    ve.clv_upp,ve.upp,
                    ve.clv_subsecretaria,ve.subsecretaria,
                    ve.clv_ur,ve.ur
                from v_epp ve
                where ejercicio = ',anio,'
            )
            select 
                concat(
                    a.clv_upp,\" \",
                    a.upp
                ) upp,
                a.subsecretaria,
                a.ur,
                f.fuente_financiamiento,
                case 
                    when sum(pp.total) is null then 0
                    else sum(pp.total)
                end importe
            from aux a
            left join ',@tabla,' pp on pp.ejercicio = ',anio,' and pp.',@corte,' 
            and pp.upp = a.clv_upp and pp.subsecretaria = a.clv_subsecretaria and pp.ur = a.clv_ur
            left join techos_financieros tf on a.clv_upp = tf.clv_upp and tf.ejercicio = ',anio,' and tf.deleted_at is null
            left join fondo f on tf.clv_fondo = f.clv_fondo_ramo and f.deleted_at is null
            group by a.clv_upp,a.upp,a.clv_subsecretaria,a.subsecretaria,
            a.clv_ur,a.ur,f.fuente_financiamiento;
            ');

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
                order by upp,subsecretaria,ur,
                fuente_financiamiento
            )t;

            DROP TEMPORARY TABLE aux_0;
            DROP TEMPORARY TABLE aux_1;
            DROP TEMPORARY TABLE aux_2;
            DROP TEMPORARY TABLE aux_3;
        END;
        ");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto';
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            if(corte is not null) then
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            
            set @subquery := concat('select * from (
                with aux as (
                select 
                    pp.region,
                    pp.municipio,
                    pp.localidad,
                    sum(pp.total) importe
                from ',@tabla,' pp
                where pp.ejercicio = ',anio,' and pp.',@corte,'
                group by region,municipio,localidad
                )
                select
                    cg.clv_region,
                    cg.region,
                    cg.clv_municipio,
                    cg.municipio,
                    cg.clv_localidad,
                    cg.localidad,
                    \"\" clv_upp,\"\" upp,
                    a.importe
                from aux a
                join clasificacion_geografica cg on a.region = cg.clv_region
                and a.municipio = cg.clv_municipio and a.localidad = cg.clv_localidad
                and cg.deleted_at is null)t
                UNION ALL 
                select * from (
                with aux as (
                select 
                    pp.region,
                    pp.municipio,
                    pp.localidad,
                    pp.upp,
                    sum(pp.total) importe
                from ',@tabla,' pp
                where pp.ejercicio = ',anio,' and pp.',@corte,'
                group by region,municipio,localidad,upp
                )
                select 
                    a.region clv_region,\"\" region,
                    a.municipio clv_municipio,\"\" municipio,
                    a.localidad clv_localidad,\"\" localidad,
                    a.upp clv_upp,
                    ch.descripcion upp,
                    importe
                from aux a
                join ',@catalogo,' ch on a.upp = ch.clave and ch.ejercicio = ',anio,'
                and ch.grupo_id = 6 and ch.deleted_at is null)t
                order by clv_region,clv_municipio,
                clv_localidad,clv_upp');
            
            set @query := concat(\"
            select 
                case 
                    when clv_upp != '' then ''
                    else clv_region
                end clv_region,
                case 
                    when clv_upp != '' then ''
                    else region
                end region,
                case 
                    when clv_upp != '' then ''
                    else clv_municipio
                end clv_municipio,
                case 
                    when clv_upp != '' then ''
                    else municipio
                end municipio,
                case 
                    when clv_upp != '' then ''
                    else clv_localidad
                end clv_localidad,
                case 
                    when clv_upp != '' then ''
                    else localidad
                end localidad,
                clv_upp,upp,
                importe
            from (\",@subquery,\")tabla;
            \");
        
            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
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
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
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
                select distinct
                    ve.clv_upp,ve.upp,
                    ve.clv_ur,ve.ur,
                    ve.clv_finalidad,ve.finalidad,
                    ve.clv_funcion,ve.funcion,
                    ve.clv_subfuncion,ve.subfuncion
                from v_epp ve
                where ejercicio = ',anio,' and deleted_at is null
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
        
        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int, in corte date, in uppC varchar(3))
        begin
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @catalogo := 'catalogo';
            set @upp := '';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @catalogo := 'catalogo_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            if (uppC is not null) then set @upp := CONCAT('and pp.upp = \"',uppC,'\"'); end if;
        
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
            where pp.ejercicio = ',anio,' and pp.',@corte,' ',@upp,';
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
            if (corte is not null) then 
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
                        
            set @query := CONCAT('
                select
                    clv_upp,
                    group_concat(upp) upp,
                    sum(proyectos) proyectos,
                    sum(proyectos_actividades) actividades,
                    round((sum(proyectos_actividades)/sum(proyectos))*100) avance,
                    case
                        when sum(proyectos) = sum(proyectos_actividades) then \"Confirmado\"
                        else \"Registrado\"
                    end estatus
                from (
                    select
                        clv_upp,
                        upp,
                        count(*) proyectos,
                        0 proyectos_actividades
                    from v_epp ve
                    where ejercicio = ',anio,' and presupuestable = 1 and ',@corte,'
                    group by clv_upp,upp
                    union all 
                    select 
                        mm.clv_upp,
                        \"\" upp,
                        0 proyectos,
                        count(distinct mm.area_funcional) proyectos_actividades
                    from metas m 
                    left join mml_mir mm on m.mir_id = mm.id
                    where mm.',@corte,' and mm.ejercicio = ',anio,'
                    group by clv_upp
					union all
					select 
						mm.clv_upp,
						\"\" upp,
						0 proyectos,
						count(distinct mm.area_funcional) proyectos_actividades
					from metas m 
					left join mml_actividades mm on m.actividad_id = mm.id 
					where mm.',@corte,' and mm.ejercicio = ',anio,'
					group by clv_upp
                )t
                group by clv_upp;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE proyecto_calendario_actividades(in anio int, in upp varchar(3), in corte date)
        begin
            set @upp := '';
            set @corte := 'deleted_at is null';
            set @tabla := 'metas';
            set @catalogo := 'catalogo';
            if(upp is not null) then set @upp := concat(\"where clv_upp = '\",upp,\"'\"); end if;
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
            where mir_id is null and m.ejercicio = \",anio,\" and m.deleted_at is null
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
            where m.mir_id is not null and m.ejercicio = \",anio,\" and m.deleted_at is NULL;
            \");
        
            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
            
            create temporary table aux_1
            select distinct
                clv_upp,clv_ur,clv_programa,clv_subprograma,
                clv_proyecto,clv_fondo
            from aux_0;
            
            select 
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
                agosto,septiembre,octubre,noviembre,diciembre
            from (
                select * from aux_0
                union all
                select 
                    clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,clv_fondo,
                    '' actividad,0 cantidad_beneficiarios,'' beneficiario,'' unidad_medida,'' tipo,0 meta_anual,
                    0 enero,0 febrero,0 marzo,0 abril,0 mayo,0 junio,0 julio,0 agosto,0 septiembre,0 octubre,0 noviembre,0 diciembre
                from aux_1
                order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_proyecto,clv_fondo,actividad
            )t;
        
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
            drop temporary table if exists aux_3;
            drop temporary table if exists aux_4;
                    
            set @query := CONCAT('
            create temporary table aux_0
            select 
                tu.clv_upp,
                tu.upp,
                fo.clv_fondo_ramo,
                fo.fondo_ramo,
                concat(po.clv_capitulo,\"000\") clv_capitulo,
                po.capitulo,
                0 monto_anual,
                sum(pa.total) calendarizado,
                sum(pa.estado) status
            from ',@tabla,' pa
            join (
                select distinct
                    clave clv_upp,descripcion upp
                from ',@catalogo,' ch 
                where ch.deleted_at is null and ch.ejercicio = ',anio,' and ch.grupo_id = 6
            ) tu on pa.upp = tu.clv_upp
            join (
                select distinct
                    f.clv_fondo_ramo,f.fondo_ramo
                from fondo f
                where f.deleted_at is null
            ) fo on pa.fondo_ramo = fo.clv_fondo_ramo 
            join (
                select distinct 
                    pp.clv_capitulo,pp.capitulo 
                from posicion_presupuestaria pp
                where pp.deleted_at is null
            ) po on substring(pa.posicion_presupuestaria,1,1) = po.clv_capitulo
            where pa.ejercicio = ',anio,' and pa.',@corte,'
            group by tu.clv_upp,tu.upp,fo.clv_fondo_ramo,
            fo.fondo_ramo,po.clv_capitulo,po.capitulo;
            ');
        
            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table aux_1
            select 
                clv_upp,upp,
                clv_fondo_ramo,fondo_ramo,
                0 monto_anual,
                sum(calendarizado) calendarizado,
                sum(status) status
            from aux_0
            group by clv_upp,upp,
            clv_fondo_ramo,fondo_ramo;
        
            create temporary table aux_2
            select 
                clv_upp,
                c.descripcion upp,
                clv_fondo clv_fondo_ramo,
                f.fondo_ramo,
                sum(presupuesto) monto_anual,
                0 calendarizado
            from techos_financieros tf
            join catalogo_hist c on tf.clv_upp = c.clave 
            and c.deleted_at is null and c.ejercicio = anio and c.grupo_id = 6
            join fondo f on tf.clv_fondo = f.clv_fondo_ramo 
            and f.deleted_at is null
            where tf.ejercicio = anio and tf.deleted_at is null
            group by clv_upp,descripcion,clv_fondo,f.fondo_ramo;
        
            create temporary table aux_3
            select 
                clv_upp,upp,
                clv_fondo_ramo,fondo_ramo,
                sum(monto_anual) monto_anual,
                sum(calendarizado) calendarizado,
                sum(status) status
            from (
                select 
                    *
                from aux_1
                union all
                select 
                    clv_upp,upp,
                    clv_fondo_ramo,fondo_ramo,
                    monto_anual,
                    calendarizado,
                    0 status
                from aux_2
            )t
            group by clv_upp,upp,clv_fondo_ramo,fondo_ramo;
        
            create temporary table aux_4
            select 
                clv_upp,upp,
                sum(monto_anual) monto_anual,
                sum(calendarizado) calendarizado,
                sum(status) status
            from aux_3
            group by clv_upp,upp;
        
            with aux as (
            select 
                clv_upp,upp,
                clv_fondo_ramo,fondo_ramo,
                clv_capitulo,capitulo,
                case 
                    when monto_anual = 0 then calendarizado 
                    else monto_anual
                end monto_anual,
                calendarizado,
                status
            from (
                select 
                    clv_upp,upp,
                    '' clv_fondo_ramo,'' fondo_ramo,
                    '' clv_capitulo,'' capitulo,
                    monto_anual,calendarizado,status
                from aux_4
                union all
                select 
                    clv_upp,upp,
                    clv_fondo_ramo,fondo_ramo,
                    '' clv_capitulo,'' capitulo,
                    monto_anual,calendarizado,status
                from aux_3
                union all
                select * from aux_0
                order by clv_upp,clv_fondo_ramo,clv_capitulo
            )t)
            select 
                clv_upp,upp,clv_fondo_ramo,fondo_ramo,clv_capitulo,capitulo,
                monto_anual,calendarizado,disponible,avance,
                case 
                    when status is null then 'Guardado'
                    else status
                end status
            from(
            select 
                case 
                    when fondo_ramo != '' then ''
                    else clv_upp
                end clv_upp,
                case 
                    when fondo_ramo != '' then ''
                    else upp
                end upp,
                case 
                    when capitulo != '' then ''
                    else clv_fondo_ramo
                end clv_fondo_ramo,
                case 
                    when capitulo != '' then ''
                    else fondo_ramo
                end fondo_ramo,
                clv_capitulo,
                capitulo,
                monto_anual,
                calendarizado,
                (monto_anual-calendarizado) disponible,
                (calendarizado/monto_anual)*100 avance,
                case 
                    when fondo_ramo != '' then ''
                    when status is null then 'Guardado'
                    when status = 0 then 'Guardado'
                    when status = 1 then 'Confirmado'
                end status
            from aux)t;
            
            drop temporary table aux_0;
            drop temporary table aux_1;
            drop temporary table aux_2;
            drop temporary table aux_3;
            drop temporary table aux_4;
        END;");

        DB::unprepared("CREATE PROCEDURE conceptos_clave(in claveT varchar(64), in anio int)
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
	        set @del := \"from v_epp e\";
	        if(uppC is not null) then set @upp := CONCAT(\"and e.clv_upp = '\",uppC,\"'\"); end if;
	        if(urC is not null) then set @upr := CONCAT(\"and clv_ur = '\",urC,\"'\"); end if;
	        if(delegacion = 1) then set @del := \"from uppautorizadascpnomina u join v_epp e on u.clv_upp = e.clv_upp\"; end if;
           
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
                where ejercicio = \",anio,\"
				and e.deleted_at is null
				\",@upp,\"
				\",@ur,\"
			\");
               
			prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
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

        DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int)
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
                        join v_epp ve on ve.id = mm.id_epp
                        where mm.ejercicio = \",anio,\" and mm.deleted_at is null
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
                        join v_epp ve on ve.id = mm.id_epp
                        where mm.ejercicio = \",anio,\" and mm.deleted_at is null
                        and nivel IN (11) \",@upp,\" \",@ur,\" \",@programa,\"
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
                    )t 
                    GROUP BY clv_upp,clv_pp,clv_ur,id,nivel
                    ORDER BY clv_upp,clv_pp,clv_ur,id,nivel
                )t2;
            \");
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
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
    }
};