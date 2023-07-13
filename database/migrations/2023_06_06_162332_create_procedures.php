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
            where va.ejercicio = anio and if  (
                    corte is null,
                    va.deleted_at is null,
                    va.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
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
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_1(in anio int, in corte date)
        begin
            select 
                vppa.upp,
                sum(total) importe
            from pp_aplanado vppa
            where ejercicio = anio and if  (
                corte is null,
                deleted_at is null,
                deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by upp;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_1_2(in anio int, in corte date)
        begin
            select
                concepto,
                case 
                    when importe is null then 0
                    else importe
                end importe
            from (
                select 
                    'Poder Ejecutivo' concepto,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where pp.clasificacion_administrativa in ('21111','21120')
                and pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                union all
                select 
                    'Poder Legislativo' concepto,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where pp.clasificacion_administrativa in ('21112')
                and pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                union all
                select 
                    'Poder Judicial' concepto,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where pp.clasificacion_administrativa in ('21113')
                and pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                union all
                select 
                    'Organos Autónomos' concepto,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where pp.clasificacion_administrativa in ('21114')
                and pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                group by concepto
            )  tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_1_3(in anio int, in corte date)
        begin
            select  
                'Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros' concepto,
                sum(total) as importe
            from programacion_presupuesto pp 
            where pp.clasificacion_administrativa like '21120'
            and pp.ejercicio = anio and pp.deleted_at is null and if  (
                corte is null,
                pp.deleted_at is null,
                pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by concepto
            union all 
            select 
                'Instituciones Públicas de la Seguridad Social' concepto,
                0 importe
            union all
            select
                'Entidades Paraestatales Empresariales No Financieras cpn Participaciones Estatal Mayoritaria' concepto,
                0 importe
            union all
            select
                'Fideicomisos Empresariales No Financieros con Participación Estatal Mayoritaria' concepto,
                0 importe
            union all
            select
                'Entidades Pararestatales Empresariales Financieras Monetarias con Participación Estatal Mayoritaria' concepto,
                0 importe
            union all
            select
                'Entidades Pararestatales Empresariales Financieras No Monetarias con Participación Estatal Mayoritaria' concepto,
                0 importe
            union all
            select
                'Fideicomisos Financieros Públicos con Participación Estatal Mayoritaria' concepto,
                0 importe;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_2(in anio int, in corte date)
        begin
            select 
                case 
                    when funcion != '' then ''
                    else finalidad
                end finalidad,
                funcion,
                importe
            from (
                select 
                    vppa.finalidad,
                    '' funcion,
                    sum(total) importe
                from pp_aplanado vppa
                where ejercicio = anio and if  (
                    corte is null,
                    vppa.deleted_at is null,
                    vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by finalidad
                union all 
                select 
                    vppa.finalidad,
                    vppa.funcion,
                    sum(total) importe
                from pp_aplanado vppa
                where ejercicio = anio and if  (
                    corte is null,
                    vppa.deleted_at is null,
                    vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by vppa.finalidad,vppa.funcion
                order by finalidad
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_3(in anio int, in corte date)
        begin
            select 
                abuelo,
                case 
                    when hijo != '' then ''
                    else padre
                end padre,
                hijo,
                importe
            from (
                select 
                    'Programas' abuelo,
                    '' padre,
                    '' hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where tc.tipo = 0 and ejercicio = anio and if  (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                union all
                select *
                from (
                    select 
                        '' abuelo,
                        tc.descripcion padre,
                        '' hijo,
                        sum(total) importe
                    from tipologia_conac tc 
                    join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                    where tc.tipo = 0 and ejercicio = anio and if  (
                        corte is null,
                        pa.deleted_at is null,
                        pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                    group by tc.descripcion
                    union all
                    select 
                        '' abuelo,
                        tc.descripcion padre,
                        tc.descripcion_conac hijo,
                        sum(total) importe
                    from tipologia_conac tc 
                    join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                    where tc.tipo = 0 and ejercicio = anio and if  (
                        corte is null,
                        pa.deleted_at is null,
                        pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                    group by tc.descripcion,tc.descripcion_conac
                    order by padre,hijo
                ) tabla
                union all
                select 
                    tc.descripcion abuelo,
                    '' padre,
                    '' hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where tc.tipo = 1 and ejercicio = anio and if (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by tc.descripcion
            ) tabla2;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_4(in anio int, in corte date)
        begin
            select 
                case 
                    when partida_generica != '' then ''
                    else capitulo
                end capitulo,
                partida_generica,
                total,
                enero,febrero,marzo,abril,mayo,
                junio,julio,agosto,septiembre,
                octubre,noviembre,diciembre
            from (
            select 
                vppa.capitulo,
                '' partida_generica,
                sum(total) total,
                sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
            from pp_aplanado vppa
            where ejercicio = anio and if (
                corte is null,
                vppa.deleted_at is null,
                vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by capitulo
            union all
            select 
                vppa.capitulo,
                vppa.partida_generica,
                sum(total) total,
                sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
            from pp_aplanado vppa
            where ejercicio = anio and if  (
                corte is null,
                vppa.deleted_at is null,
                vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by capitulo,partida_generica
            order by capitulo
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_5(in anio int, in corte date)
        begin
            select 
                vppa.capitulo,
                sum(vppa.total) importe
            from pp_aplanado vppa
            where vppa.ejercicio = anio and if (
                corte is null,
                vppa.deleted_at is null,
                vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by capitulo;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_a_num_6(in anio int, in corte date)
        begin
            select 
                conceptos,
                sum(importe) importe
            from (
                select 
                    'Gasto Corriente' conceptos,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where ((pp.posicion_presupuestaria)*1) not between 45000 and 45999
                and ((pp.posicion_presupuestaria)*1) not between 40000 and 47999
                and pp.ejercicio = anio and if (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                union all 
                select 
                    'Gasto Capital' conceptos,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where ((pp.posicion_presupuestaria)*1) between 50000 and 79999
                and pp.ejercicio = anio and if (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                union all 
                select 
                    'Amortizacion de la Deuda y Disminucion de Pasivos' conceptos,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where ((pp.posicion_presupuestaria)*1) between 90000 and 99999
                and pp.ejercicio = anio and if (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                union all 
                select 
                    'Pensiones y Jubilaciones' conceptos,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where ((pp.posicion_presupuestaria)*1) between 80000 and 89999
                and pp.ejercicio = anio and if (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                union all 
                select 
                    'Pensiones y Jubilaciones' conceptos,
                    sum(pp.total) importe
                from programacion_presupuesto pp 
                where ((pp.posicion_presupuestaria)*1) between 45000 and 45999
                and ((pp.posicion_presupuestaria)*1) between 47000 and 47999
                and pp.ejercicio = anio and if (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
            ) tabla
            group by conceptos;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_1(in anio int, in corte date)
        begin
            select 
                case 
                    when upp != '' then ''
                    else etiquetado
                end etiquetado,
                upp,
                importe
            from (
                select 
                    vppa.etiquetado,
                    '' upp,
                    sum(total) importe
                from pp_aplanado vppa
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado 
                union all
                select 
                    vppa.etiquetado,
                    vppa.upp,
                    sum(total) importe
                from pp_aplanado vppa
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by vppa.etiquetado,vppa.upp
                order by etiquetado
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_2(in anio int, in corte date)
        begin
            select 
                case 
                    when finalidad != '' then ''
                    else etiquetado
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
                    '' finalidad,
                    '' funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado
                union all
                select 
                    etiquetado,
                    finalidad,
                    '' funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,finalidad
                union all
                select 
                    etiquetado,
                    finalidad,
                    funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,finalidad,funcion
                order by etiquetado,finalidad,funcion
            ) tabla;
        END;");

        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_3(in anio int, in corte date)
        begin
            select 
                case 
                    when finalidad != '' then ''
                    else etiquetado
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
                    '' finalidad,
                    '' funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado
                union all
                select 
                    etiquetado,
                    finalidad,
                    '' funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,finalidad
                union all
                select 
                    etiquetado,
                    finalidad,
                    funcion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,finalidad,funcion
                order by etiquetado,finalidad,funcion
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_4(in anio int, in corte date)
        begin
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
                select 
                    etiquetado,
                    '' capitulo,
                    '' concepto,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado
                union all
                select 
                    etiquetado,
                    capitulo,
                    '' concepto,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,capitulo
                union all
                select 
                    etiquetado,
                    capitulo,
                    concepto,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by etiquetado,capitulo,concepto
                order by etiquetado,capitulo,concepto
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_10(in anio int, in corte date)
        begin
            select 
                concepto,
                sum(importe) importe
            from v_sector_importe vsi
            where ejercicio = anio and if  (
                corte is null,
                deleted_at is null,
                deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by concepto;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_1(in anio int, in corte date)
        begin
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
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    ''subsecretaria,
                    '' ur,
                    '' fuente_financiamiento,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    subsecretaria,
                    '' ur,
                    '' fuente_financiamiento,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,subsecretaria
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    subsecretaria,
                    ur,
                    '' fuente_financiamiento,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,subsecretaria,ur
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    subsecretaria,
                    ur,
                    fuente_financiamiento,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,subsecretaria,ur,fuente_financiamiento
                order by upp,subsecretaria,ur,fuente_financiamiento
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
        begin
            select
                case 
                    when upp != '' then ''
                    else region 
                end region,
                case 
                    when upp != '' then ''
                    else municipio
                end municipio,
                case 
                    when upp != '' then ''
                    else localidad
                end localidad,
                clv_upp,
                upp,
                importe
            from (
                select 
                    concat (
                        clv_region,' ',
                        region
                    ) as region,
                    concat (
                        clv_municipio,' ',
                        municipio
                    ) as municipio,
                    concat(
                        clv_localidad,' ',
                        localidad
                    ) as localidad,
                    '' clv_upp,
                    '' upp,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_region,vppa.region,clv_municipio,vppa.municipio,
                clv_localidad,vppa.localidad
                union all
                select 
                    concat (
                        clv_region,' ',
                        region
                    ) as region,
                    concat (
                        clv_municipio,' ',
                        municipio
                    ) as municipio,
                    concat(
                        clv_localidad,' ',
                        localidad
                    ) as localidad,
                    clv_upp,
                    upp,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_region,vppa.region,clv_municipio,vppa.municipio,
                clv_localidad,vppa.localidad,clv_upp,vppa.upp
                order by region,municipio,localidad,clv_upp
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_3(in anio int,in corte date)
        begin
            select 
                clv_eje,
                eje,
                sum(total) importe
            from pp_aplanado vppa 
            where ejercicio = anio and if  (
                corte is null,
                deleted_at is null,
                deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by clv_eje,eje;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_4(in anio int, in corte date)
        begin
            select 
            clv_programa,
            programa,
            sum(total) importe
        from pp_aplanado vppa 
        where ejercicio = anio and if  (
            corte is null,
            deleted_at is null,
            deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
        )
        group by clv_programa,programa;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_5(in anio int, in corte date)
        begin
            select
                case 
                    when capitulo != '' then ''
                    else upp
                end upp,
                case 
                    when programa_presupuestario != '' then ''
                    else capitulo
                end capitulo,
                programa_presupuestario,
                importe
            from (
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    '' capitulo,
                    '' programa_presupuestario,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp
                union all 
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_capitulo,'000 ',
                        capitulo
                    ) as capitulo,
                    '' programa_presupuestario,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_capitulo,vppa.capitulo
                union all 
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_capitulo,'000 ',
                        capitulo
                    ) as capitulo,
                    concat(
                        clv_programa,' ',
                        programa
                    ) as programa_presupuestario,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_capitulo,vppa.capitulo,clv_programa,vppa.programa
                order by upp,capitulo,programa_presupuestario
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_6(in anio int, in corte date)
        begin
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
                importe
            from (
                select 
                    finalidad,
                    '' funcion,
                    '' subfuncion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by vppa.finalidad
                union all
                select 
                    finalidad,
                    funcion,
                    '' subfuncion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by vppa.finalidad,vppa.funcion
                union all
                select 
                    finalidad,
                    funcion,
                    subfuncion,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by vppa.finalidad,vppa.funcion,vppa.subfuncion
                order by finalidad,funcion,subfuncion
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_7(in anio int, in corte date)
        begin
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
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    '' ur,
                    '' finalidad,
                    '' funcion,
                    '' subfuncion,
                    '' partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_ur,' ',
                        ur
                    ) as ur,
                    finalidad,
                    '' funcion,
                    '' subfuncion,
                    '' partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_ur,' ',
                        ur
                    ) as ur,
                    finalidad,
                    funcion,
                    '' subfuncion,
                    '' partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                funcion
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_ur,' ',
                        ur
                    ) as ur,
                    finalidad,
                    funcion,
                    subfuncion,
                    '' partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                funcion,subfuncion
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    concat(
                        clv_ur,' ',
                        ur
                    ) as ur,
                    finalidad,
                    funcion,
                    subfuncion,
                    concat(
                        clv_capitulo,
                        clv_concepto,
                        clv_partida_generica,
                        clv_partida_especifica,' ',
                        partida_especifica
                    ) as partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                funcion,subfuncion,clv_capitulo,clv_concepto,
                clv_partida_generica,clv_partida_especifica,partida_especifica
                order by upp,ur,finalidad,funcion,subfuncion,partida
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_11_8(in anio int, in corte date)
        begin
            select 
                case 
                    when programa != '' then ''
                    else clv_upp
                end clv_upp,
                case 
                    when programa != '' then ''
                    else upp
                end upp,
                clv_programa,
                programa,
                clv_proyecto_obra,
                proyecto_obra,
                importe
            from (
                select 
                    clv_upp,
                    upp,
                    '' clv_programa,
                    '' programa,
                    '' clv_proyecto_obra,
                    '' proyecto_obra,
                    sum(total) importe
                from pp_aplanado vppa 
                left join proyectos_obra po on vppa.proyecto_obra = po.clv_proyecto_obra
                where ejercicio = anio and if  (
                    corte is null,
                    vppa.deleted_at is null,
                    vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp
                union all
                select 
                    clv_upp,
                    upp,
                    clv_programa,
                    programa,
                    vppa.clv_proyecto_obra,
                    vppa.proyecto_obra,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if  (
                    corte is null,
                    vppa.deleted_at is null,
                    vppa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_upp,vppa.upp,clv_programa,vppa.programa,
                vppa.clv_proyecto_obra,vppa.proyecto_obra
                order by upp,programa,proyecto_obra
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists calendario_general(in anio int, in corte date, in uppC varchar(3))
        begin
            set @upp := uppC;
            
            select
                upp,
                clave,
                monto_anual,
                enero,febrero,marzo,abril,mayo,
                junio,julio,agosto,septiembre,
                octubre,noviembre,diciembre
            from (
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    1 orden,
                    concat(clv_upp,' ',upp) clave,
                    sum(total) monto_anual,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                    sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                    sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                from pp_aplanado vppa
                where ejercicio = anio and if (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                ) and if(
                    @upp is not null,
                    clv_upp = @upp,
                    clv_upp != ''
                )
                group by vppa.clv_upp,vppa.upp,orden,vppa.upp
                union all
                select 
                    concat(
                        clv_upp,' ',
                        upp
                    ) as upp,
                    2 orden,
                    concat(
                        pp.clv_sector_publico,
                        pp.clv_sector_publico_f,
                        pp.clv_sector_economia,
                        pp.clv_subsector_economia,
                        pp.clv_ente_publico,
                        '-',pp.clv_entidad_federativa,
                        '-',pp.clv_region,
                        '-',pp.clv_municipio,
                        '-',pp.clv_localidad,
                        '-',pp.clv_upp,
                        '-',pp.clv_subsecretaria,
                        '-',pp.clv_ur,
                        '-',pp.clv_finalidad,
                        '-',pp.clv_funcion,
                        '-',pp.clv_subfuncion,
                        '-',pp.clv_eje,
                        '-',pp.clv_linea_accion,
                        '-',pp.clv_programa_sectorial,
                        '-',pp.clv_tipologia_conac,
                        '-',pp.clv_programa,
                        '-',pp.clv_subprograma,
                        '-',pp.clv_proyecto,
                        '-',pp.periodo_presupuestal,
                        '-',pp.clv_capitulo,
                        pp.clv_concepto,
                        pp.clv_partida_generica,
                        pp.clv_partida_especifica,
                        '-',pp.clv_tipo_gasto,
                        '-',pp.anio,
                        '-',pp.clv_etiquetado,
                        '-',pp.clv_fuente_financiamiento,
                        '-',pp.clv_ramo,
                        '-',pp.clv_fondo_ramo,
                        '-',pp.clv_capital,
                        '-',pp.clv_proyecto_obra
                    ) clave,
                    total monto_anual,
                    enero,febrero,marzo,abril,mayo,
                    junio,julio,agosto,septiembre,
                    octubre,noviembre,diciembre
                from pp_aplanado pp
                where ejercicio = anio and if  (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                ) and if (
                    @upp is not null,
                    clv_upp = @upp,
                    clv_upp != ''
                )
                order by upp,orden
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists calendario_fondo_mensual(in anio int, in corte date)
        begin
            select 
                ramo,
                fondo_ramo,
                sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre,
                sum(total) importe_total
            from pp_aplanado vppa 
            where ejercicio = anio and if  (
                corte is null,
                deleted_at is null,
                deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by ramo,fondo_ramo;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_resumen_por_capitulo_y_partida(in anio int, in corte date)
        begin
            select 
                case 
                    when partida != '' then ''
                    else capitulo
                end capitulo,
                partida,
                importe
            from (
                select 
                    concat(
                        clv_capitulo,'000 ',
                        capitulo
                    ) as capitulo,
                    '' partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_capitulo,vppa.capitulo
                union all
                select 
                    concat(
                        clv_capitulo,'000 ',
                        capitulo
                    ) as capitulo,
                    concat(
                        clv_capitulo,
                        clv_concepto,
                        clv_partida_generica,
                        clv_partida_especifica,' ',
                        partida_especifica
                    ) as partida,
                    sum(total) importe
                from pp_aplanado vppa 
                where ejercicio = anio and if (
                    corte is null,
                    deleted_at is null,
                    deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by clv_capitulo,vppa.capitulo,clv_concepto,
                clv_partida_generica,clv_partida_especifica,partida_especifica
                order by capitulo,partida
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists avance_proyectos_actividades_upp(in anio int, in corte date)
        begin
            select 
            clv_upp,
            upp,
            proyectos,
            actividades,
            (actividades/proyectos)*100 avance,
            case 
                when estatus > 0 then 'Confirmado'
                else 'Registrado'
            end estatus
        from (
            select 
                ve.clv_upp,
                ve.upp,
                count(proyecto) proyectos,
                count(distinct proyecto_mir_id) actividades,
                sum(am.estatus) estatus
            from v_epp ve
            left join proyectos_mir pm on 
                pm.clv_upp = ve.clv_upp and 
                pm.clv_ur = ve.clv_ur and 
                pm.clv_programa = ve.clv_programa and 
                pm.clv_subprograma = ve.clv_subprograma and 
                pm.clv_proyecto = ve.clv_proyecto
            left join actividades_mir am on pm.id = am.proyecto_mir_id
            where pm.ejercicio = anio and if (
                corte is null,
                am.deleted_at is null,
                am.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            )
            group by clv_upp,upp
        ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists proyecto_calendario_actividades(in anio int, in upp varchar(3), in corte date)
        begin
            select 
                pm.clv_upp,
                pm.clv_ur,
                pm.clv_programa,
                pm.clv_subprograma,
                pm.clv_proyecto,
                m.clv_fondo,
                am.actividad,
                m.cantidad_beneficiarios,
                b.beneficiario,
                um.unidad_medida,
                case 
                    when m.tipo = 0 then 'Continua'
                    when m.tipo = 1 then 'Acumulativa'
                    when m.tipo = 2 then 'Especial'
                    else 'No Especificada'
                end tipo,
                case 
                    when m.tipo = 0 then enero
                    when m.tipo = 1 then total
                    when m.tipo = 2 then greatest(enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre)
                end meta_anual,
                enero,
                febrero,
                marzo,
                abril,
                mayo,
                junio,
                julio,
                agosto,
                septiembre,
                octubre,
                noviembre,
                diciembre
            from metas m
            join actividades_mir am on m.actividad_id = am.id 
            join proyectos_mir pm on am.proyecto_mir_id = pm.id
            join beneficiarios b on m.beneficiario_id = b.id 
            join unidades_medida um on m.unidad_medida_id = um.id 
            where pm.ejercicio = anio and 
                pm.deleted_at is null and if (
                    upp is null,
                    pm.clv_upp != '',
                    pm.clv_upp = upp
                ) and if (
                corte is null,
                am.deleted_at is null,
                am.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
            );
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_X_b_num_5(in anio int, in corte date)
        begin
            select 
                case 
                    when abuelo != '' then ''
                    else etiquetado
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
                importe
            from (
                select 
                    pa.etiquetado,
                    1 orden,
                    0 tipo,
                    '' abuelo,
                    '' padre,
                    '' hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where ejercicio = anio and if  (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by pa.etiquetado
                union all
                select 
                    pa.etiquetado,
                    2 orden,
                    tc.tipo,
                    'Programas' abuelo,
                    '' padre,
                    '' hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where tc.tipo = 0 and ejercicio = anio and if  (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by pa.etiquetado,tc.tipo
                union all
                select 
                    pa.etiquetado,
                    2 orden,
                    tc.tipo,
                    'Programas' abuelo,
                    tc.descripcion padre,
                    '' hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where tc.tipo = 0 and ejercicio = anio and if  (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by pa.etiquetado,tc.tipo,tc.descripcion
                union all
                select 
                    pa.etiquetado,
                    2 orden,
                    tc.tipo,
                    'Programas' abuelo,
                    tc.descripcion padre,
                    tc.descripcion_conac hijo,
                    sum(total) importe
                from tipologia_conac tc 
                join pp_aplanado pa on tc.clave_conac = pa.clv_tipologia_conac
                where ejercicio = anio and if  (
                    corte is null,
                    pa.deleted_at is null,
                    pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by pa.etiquetado,tc.tipo,tc.descripcion,tc.descripcion_conac
                order by etiquetado,orden,tipo,padre,hijo
            ) tabla;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists proyecto_avance_general(in anio int, in corte date)
        begin
            select 
                clv_upp,
                upp,
                clv_fondo clv_fondo_ramo,
                fondo fondo_ramo,
                clv_capitulo,
                capitulo,
                monto_anual,
                calendarizado,
                (monto_anual-calendarizado) disponible,
                (calendarizado/monto_anual)*100 avance,
                case 
                    when clv_upp != '' then estatus
                    else ''
                end estatus
            from (
                select 
                    case 
                        when clv_fondo != '' then ''
                        else clv_upp
                    end clv_upp,
                    case 
                        when clv_fondo != '' then ''
                        else upp
                    end upp,
                    case 
                        when clv_capitulo != '' then ''
                        else clv_fondo
                    end clv_fondo,
                    case 
                        when clv_capitulo != '' then ''
                        else fondo
                    end fondo,
                    case 
                        when capitulo = '' then ''
                        else concat(clv_capitulo,'000')
                    end clv_capitulo,
                    capitulo,
                    case 
                        when sum(monto_anual) = 0 then sum(calendarizado)
                        else sum(monto_anual)
                    end monto_anual,
                    sum(calendarizado) calendarizado,
                    case 
                        when sum(estatus) > 0 then 'Confirmado'
                        else 'Registrado'
                    end estatus
                from (
                    select *
                    from (
                        select 
                            clv_upp,
                            upp,
                            '' clv_fondo,
                            '' fondo,
                            '' clv_capitulo,
                            '' capitulo,
                            0 monto_anual,
                            sum(total) calendarizado,
                            sum(pa.estado) estatus
                        from pp_aplanado pa 
                        where pa.ejercicio = anio and if (
                            corte is null,
                            pa.deleted_at is null,
                            pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                        )
                        group by pa.clv_upp,pa.upp
                        union all
                        select 
                            clv_upp,
                            upp,
                            clv_fondo_ramo clv_fondo,
                            fondo_ramo fondo,
                            '' clv_capitulo,
                            '' capitulo,
                            0 monto_anual,
                            sum(total) calendarizado,
                            0 estatus
                        from pp_aplanado pa 
                        where pa.ejercicio = anio and if (
                            corte is null,
                            pa.deleted_at is null,
                            pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                        )
                        group by pa.clv_upp,pa.upp,pa.clv_fondo_ramo,pa.fondo_ramo
                        union all
                        select 
                            clv_upp,
                            upp,
                            clv_fondo_ramo clv_fondo,
                            fondo_ramo fondo,
                            clv_capitulo,
                            capitulo,
                            0 monto_anual,
                            sum(total) calendarizado,
                            0 estatus
                        from pp_aplanado pa 
                        where pa.ejercicio = anio and if (
                            corte is null,
                            pa.deleted_at is null,
                            pa.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                        )
                        group by pa.clv_upp,pa.upp,pa.clv_fondo_ramo,pa.fondo_ramo,pa.clv_capitulo,pa.capitulo
                        order by clv_upp,clv_fondo
                    ) c
                    union all
                    select 
                        *
                    from (
                        select 
                            clv_upp,
                            c.descripcion upp,
                            '' clv_fondo,
                            '' fondo,
                            '' clv_capitulo,
                            '' capitulo,
                            sum(presupuesto) monto_anual,
                            0 calendarizado,
                            0 estatus
                        from techos_financieros tf 
                        join catalogo c on c.clave = tf.clv_upp and c.grupo_id = 6
                        where tf.ejercicio = anio and if (
                            corte is null,
                            tf.deleted_at is null,
                            tf.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                        )
                        group by tf.clv_upp,c.descripcion
                        union all
                        select 
                            clv_upp,
                            c.descripcion upp,
                            clv_fondo,
                            f.fondo_ramo fondo,
                            '' clv_capitulo,
                            '' capitulo,
                            sum(presupuesto) monto_anual,
                            0 calendarizado,
                            0 estatus
                        from techos_financieros tf 
                        join catalogo c on c.clave = tf.clv_upp and c.grupo_id = 6
                        join fondo f on tf.clv_fondo = f.clv_fondo_ramo
                        where tf.ejercicio = anio and if (
                            corte is null,
                            tf.deleted_at is null,
                            tf.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                        )
                        group by tf.clv_upp,c.descripcion,tf.clv_fondo,f.fondo_ramo
                        order by clv_upp,clv_fondo
                    ) ma
                ) c2
                group by c2.clv_upp,c2.upp,c2.clv_fondo,c2.fondo,c2.clv_capitulo,c2.capitulo
            ) tabla;
        END;");

        DB::unprepared("CREATE PROCEDURE if not exists conceptos_clave(in claveT varchar(64), in anio int)
        begin
            
        set @clave := claveT; 
        set @epp := concat(substring(@clave,1,5),substring(@clave,16,22));
        set @clasGeo := ((substring(@clave,6,10))*1);
        set @partida := ((substring(@clave,43,6))*1);
        set @fondo := substring(@clave,51,7);
        set @obra := substring(@clave,58,6);
            
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
                select 'Mes de Afectación' descripcion,substring(@clave,37,6) clave, 'Mes de Afectación' union all
                select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from v_posicion_presupuestaria_llaves vppl where deleted_at is null and vppl.posicion_presupuestaria_llave like @partida union all
                select 'Año (Fondo del Ramo)' descripcion,substring(@clave,49,2) clave, 'Año' concepto union all
                select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from v_fondo_llaves vfl where deleted_at is null and vfl.llave like @fondo union all
                select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where deleted_at is null and po.clv_proyecto_obra like @obra
            ) tabla;
        END;");

        DB::unprepared("CREATE PROCEDURE if not exists insert_pp_aplanado(in anio int)
        begin
            create temporary table if not exists temp_epp_aplanado as
            select 
                vppl.id,
                vel.id v_epp_id
            from v_programacion_presupuesto_llaves vppl 
            join v_epp_llaves vel on vppl.epp_llave = vel.llave
            where vppl.ejercicio = anio and vppl.id not in (
                select id from pp_identificadores pa
            );

            create temporary table if not exists temp_claves_montos_aplanado as
            select 
                vppl.id,
                vcg.id clas_geo_id,
                vpp.id pos_pre_id,
                vfl.id fondo_id,
                po.id obra_id
            from v_programacion_presupuesto_llaves vppl 
            join v_clasificacion_geografica vcg on vppl.clas_geo_llave = vcg.clasificacion_geografica_llave
            join v_posicion_presupuestaria_llaves vpp on vppl.posicion_presupuestaria_llave = vpp.posicion_presupuestaria_llave 
            join v_fondo_llaves vfl on vppl.fondo_llave = vfl.llave 
            join proyectos_obra po on vppl.proyecto_obra = po.clv_proyecto_obra
            where vppl.ejercicio = anio and vppl.id not in (
                select id from pp_identificadores pa
            );

            insert into pp_identificadores
            select 
                tea.id,
                tea.v_epp_id,
                tma.clas_geo_id,
                tma.pos_pre_id,
                tma.fondo_id,
                tma.obra_id
            from temp_epp_aplanado tea
            join temp_claves_montos_aplanado tma on tea.id = tma.id
            order by id;
            
            drop temporary table temp_epp_aplanado;
            
            drop temporary table temp_claves_montos_aplanado;
        END;");

        DB::unprepared("CREATE PROCEDURE if not exists SP_AF_EE(in anio int)
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

        DB::unprepared("CREATE PROCEDURE if not exists lista_upp(in tipo int)
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
    }
};