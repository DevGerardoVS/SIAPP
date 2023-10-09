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
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
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
                from ',@tabla,' va
                where va.ejercicio = ',anio,' and ',@corte,'
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
                    va.partida_especifica
                union all
				select 
					clv_upp
					,\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",
					sum(total) importe
				from ',@tabla,' where ejercicio = ',anio,' and ',@corte,' group by clv_upp
				order by clv_upp,clv_ur,clv_programa,clv_subprograma,clv_partida;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'vppa.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT(
                ' select 
                        upp,
                        importe
                    from (
                        select 
                            vppa.clv_upp,
                            vppa.upp,
                            sum(total) importe
                        from ',@tabla,' vppa
                        where ejercicio = ',anio,' and ',@corte,'
                        group by clv_upp,upp
                        order by clv_upp
                    ) t');

            prepare stmt  from @query;
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
                    \"Entidades Paraestatales Empresariales No Financieras cpn Participaciones Estatal Mayoritaria\" concepto,
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
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when funcion != \"\" then \"\"
                        else finalidad
                    end finalidad,
                    funcion,
                    importe
                from (
                    select 
                       	pa.clv_finalidad,
						pa.finalidad,
						\"\" clv_funcion,
						\"\" funcion,
						sum(total) importe
                    from ',@tabla,' pa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_finalidad,finalidad
                    union all 
                    select 
                        pa.clv_finalidad,
						pa.finalidad,
						pa.clv_funcion,
						pa.funcion,
						sum(total) importe
                    from ',@tabla,' pa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by pa.clv_finalidad,pa.finalidad,pa.clv_funcion,pa.funcion
					order by clv_finalidad,clv_funcion
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_3(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado pa';
            set @corte := 'pa.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist pa';
                set @corte := CONCAT('pa.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT(\"
                select 
                    abuelo,
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
                        0 id,
                        'Programas' abuelo,
                        '' padre,
                        '' hijo,
                        sum(total) importe
                    from \",@tabla,\"
                    where pa.clv_tipologia_conac in (select clave_conac from tipologia_conac tc where deleted_at is null)
                    and \",@corte,\" and pa.ejercicio = \",anio,\"
                    union all
                    select 
                        min(tc.id) id,
                        '' abuelo,
                        tc.descripcion padre,
                        '' hijo,
                        sum(pa.total) importe
                    from tipologia_conac tc
                    join \",@tabla,\" on tc.clave_conac = pa.clv_tipologia_conac
                    where pa.ejercicio = \",anio,\" and tc.deleted_at is null and \",@corte,\"
                    group by tc.descripcion
                    union all
                    select 
                        tc.id,
                        '' abuelo,
                        tc.descripcion padre,
                        tc.descripcion_conac hijo,
                        sum(pa.total) importe
                    from tipologia_conac tc 
                    join \",@tabla,\" on tc.clave_conac = pa.clv_tipologia_conac
                    where pa.ejercicio = \",anio,\" and tc.deleted_at is null and \",@corte,\"
                    group by tc.descripcion,tc.descripcion_conac,tc.id
                    union all 
                    select 
                        min(tc.id) id,
                        tc.descripcion abuelo,
                        '' padre,
                        '' hijo,
                        sum(pa.total) importe
                    from tipologia_conac tc 
                    left join \",@tabla,\" on tc.descripcion = pa.tipologia_conac and \",@corte,\" and pa.ejercicio = \",anio,\"
                    where tc.deleted_at is null and tc.tipo = 1
                    group by tc.descripcion
                    order by id
                )t;
            \");

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_4(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
                
            set @query := CONCAT('
                select 
                    case 
                        when partida_generica != \"\" then \"\"
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
                    \"\" partida_generica,
                    sum(total) total,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                    sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                    sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                from ',@tabla,' vppa
                where ejercicio = ',anio,' and ',@corte,'
                group by capitulo
                union all
                select 
                    vppa.capitulo,
                    vppa.partida_generica,
                    sum(total) total,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                    sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                    sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                from ',@tabla,' vppa
                where ejercicio = ',anio,' and ejercicio = ',anio,' and ',@corte,'
                group by capitulo,partida_generica
                order by capitulo,partida_generica
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_5(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    capitulo,importe
                from (
                    select 
                        vppa.clv_capitulo,
                        vppa.capitulo,
                        sum(vppa.total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_capitulo,capitulo
                    order by clv_capitulo
                )t;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_6(in anio int, in corte date)
        begin
            set @tabla := 'programacion_presupuesto pp';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist pp';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    conceptos,
                    importe
                from (
                    select 
						1 orden,
                        \"Gasto Corriente\" conceptos,
                        sum(pp.total) importe
                    from ',@tabla,'
                    where (substr(pp.posicion_presupuestaria,1,2)*1) between 10 and 49
                    and (substr(pp.posicion_presupuestaria,1,2)*1) not between 45 and 47
                    and pp.ejercicio = ',anio,' and ',@corte,'
                    union all 
                    select 
						2 orden,
                        \"Gasto Capital\" conceptos,
                        sum(pp.total) importe
                    from ',@tabla,'
                    where (substr(pp.posicion_presupuestaria,1,2)*1) between 50 and 79
                    and pp.ejercicio = ',anio,' and ',@corte,'
                    union all 
                    select 
						3 orden,
                        \"Amortizacion de la Deuda y Disminucion de Pasivos\" conceptos,
                        sum(pp.total) importe
                    from ',@tabla,'
                    where (substr(pp.posicion_presupuestaria,1,2)*1) between 90 and 99
                    and pp.ejercicio = ',anio,' and ',@corte,'
                    union all 
                    select 
						4 orden,
                        \"Pensiones y Jubilaciones\" conceptos,
                        sum(pp.total) importe
                    from ',@tabla,'
                    where (substr(pp.posicion_presupuestaria,1,2)*1) between 80 and 89
                    and pp.ejercicio = ',anio,' and ',@corte,'
                    union all 
					select 
						5 orden,
                        \"Participaciones\" conceptos,
                        sum(pp.total) importe
                    from ',@tabla,'
                    where (substr(pp.posicion_presupuestaria,1,2)*1) in (45,47)
                    and pp.ejercicio = ',anio,' and ',@corte,'
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_1(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    etiquetado,
                    upp,
                    importe
                from (
                    select 
                        case 
                            when upp != \"\" then \"\"
                            else etiquetado
                        end etiquetado,
                        clv_upp,
                        upp,
                        importe
                    from (
                        select 
                            vppa.etiquetado,
                            \"\" clv_upp,
                            \"\" upp,
                            sum(total) importe
                        from ',@tabla,' vppa
                        where ejercicio = ',anio,' and ',@corte,'
                        group by etiquetado 
                        union all
                        select 
                            vppa.etiquetado,
                            vppa.clv_upp,
                            vppa.upp,
                            sum(total) importe
                        from ',@tabla,' vppa
                        where ejercicio = ',anio,' and ',@corte,'
                        group by vppa.etiquetado,vppa.clv_upp,vppa.upp
                        order by etiquetado desc
                    ) tabla
                ) t;
            ');

            prepare stmt from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_2(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            
            set @query := CONCAT('
                select 
                    case 
                        when finalidad != \"\" then \"\"
                        else etiquetado
                    end etiquetado,
                    case 
                        when funcion != \"\" then \"\"
                        else finalidad
                    end finalidad,
                    funcion,
                    importe
                from (
                    select 
                        etiquetado,
						\"\" clv_finalidad,
                        \"\" finalidad,
                        \"\" funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado
                    union all
                    select 
                        etiquetado,
						clv_finalidad,
                        finalidad,
                        \"\" funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_finalidad,finalidad
                    union all
                    select 
                        etiquetado,
						clv_finalidad,
                        finalidad,
                        funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_finalidad,finalidad,funcion
    				order by etiquetado desc,clv_finalidad,finalidad,funcion
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_3(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when finalidad != \"\" then \"\"
                        else etiquetado
                    end etiquetado,
                    case 
                        when funcion != \"\" then \"\"
                        else finalidad
                    end finalidad,
                    funcion,
                    importe
                from (
                    select 
                        etiquetado,
						\"\" clv_finalidad,
                        \"\" finalidad,
                        \"\" funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado
                    union all
                    select 
                        etiquetado,
						clv_finalidad,
                        finalidad,
                        \"\" funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_finalidad,finalidad
                    union all
                    select 
                        etiquetado,
						clv_finalidad,
                        finalidad,
                        funcion,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_finalidad,finalidad,funcion
                    order by etiquetado desc,clv_finalidad,finalidad,funcion
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_4(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when capitulo != \"\" then \"\"
                        else etiquetado
                    end etiquetado,
                    case 
                        when concepto != \"\" then \"\"
                        else capitulo
                    end capitulo,
                    concepto,
                    importe
                from (
                    select 
                        etiquetado,
						\"\" clv_capitulo,
                        \"\" capitulo,
                        \"\" concepto,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado
                    union all
                    select 
                        etiquetado,
						clv_capitulo,
                        capitulo,
                        \"\" concepto,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_capitulo,capitulo
                    union all
                    select 
                        etiquetado,
						clv_capitulo,
                        capitulo,
                        concepto,
                        sum(total) importe
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,'
                    group by etiquetado,clv_capitulo,capitulo,concepto
                    order by etiquetado desc,clv_capitulo,capitulo,concepto
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
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
                    concepto,
                    sum(importe) importe
                from (
                    select 
                        sla.sector concepto,
                        pp.total importe,
                        pp.ejercicio,
                        pp.deleted_at
                    from ',@tabla,' pp 
                    join (
                        select 
                            c.clave clv_linea_accion,
                            sla.sector
                        from sector_linea_accion sla
                        join catalogo c on sla.linea_accion_id = c.id
                    ) sla on pp.linea_accion = sla.clv_linea_accion
                )t
                where ejercicio = ',anio,' and ',@corte,'
                group by concepto
            ');	

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_1(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            set @fromW := CONCAT('from ',@tabla,' where ejercicio = ',anio,' and ',@corte);
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
                set @fromW := CONCAT('from ',@tabla,' where ejercicio = ',anio,' and ',@corte);
            end if;
            
            set @query := CONCAT('
                select
                    case 
                        when subsecretaria != \"\" then \"\"
                        else upp
                    end upp,
                    case 
                        when ur != \"\" then \"\"
                        else subsecretaria
                    end subsecretaria,
                    case 
                        when fuente_financiamiento != \"\" then \"\"
                        else ur
                    end ur,
                    fuente_financiamiento,
                    importe
                from (
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        \"\"subsecretaria,
                        \"\" ur,
                        \"\" fuente_financiamiento,
                        sum(total) importe
                    ',@fromW,'
                    group by clv_upp,vppa.upp
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        subsecretaria,
                        \"\" ur,
                        \"\" fuente_financiamiento,
                        sum(total) importe
                    ',@fromW,'
                    group by clv_upp,vppa.upp,subsecretaria
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        subsecretaria,
                        ur,
                        \"\" fuente_financiamiento,
                        sum(total) importe
                    ',@fromW,'
                    group by clv_upp,vppa.upp,subsecretaria,ur
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        subsecretaria,
                        ur,
                        fuente_financiamiento,
                        sum(total) importe
                    ',@fromW,'
                    group by clv_upp,vppa.upp,subsecretaria,ur,fuente_financiamiento
                    order by upp,subsecretaria,ur,fuente_financiamiento
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            
            set @query := CONCAT('
                select
                    case 
                        when upp != \"\" then \"\"
                        else region 
                    end region,
                    case 
                        when upp != \"\" then \"\"
                        else lower(municipio)
                    end municipio,
                    case 
                        when upp != \"\" then \"\"
                        else lower(localidad)
                    end localidad,
                    clv_upp,
                    upp,
                    importe
                from (
                    select 
                        concat (
                            clv_region,\" \",
                            region
                        ) as region,
                        concat (
                            clv_municipio,\" \",
                            municipio
                        ) as municipio,
                        concat(
                            clv_localidad,\" \",
                            localidad
                        ) as localidad,
                        \"\" clv_upp,
                        \"\" upp,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_region,vppa.region,clv_municipio,vppa.municipio,
                    clv_localidad,vppa.localidad
                    union all
                    select 
                        concat (
                            clv_region,\" \",
                            region
                        ) as region,
                        concat (
                            clv_municipio,\" \",
                            municipio
                        ) as municipio,
                        concat(
                            clv_localidad,\" \",
                            localidad
                        ) as localidad,
                        clv_upp,
                        upp,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_region,vppa.region,clv_municipio,vppa.municipio,
                    clv_localidad,vppa.localidad,clv_upp,vppa.upp
                    order by region,municipio,localidad,clv_upp
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_3(in anio int,in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    clv_eje,
                    eje,
                    sum(total) importe
                from ',@tabla,'
                where ejercicio = ',anio,' and ',@corte,'
                group by clv_eje,eje;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_4(in anio int, in corte date)
        begin
        set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    clv_programa,
                    programa,
                    sum(total) importe
                from ',@tabla,'
                where ejercicio = ',anio,' and ',@corte,'
                group by clv_programa,programa;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_5(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select
                    case 
                        when capitulo != \"\" then \"\"
                        else upp
                    end upp,
                    case 
                        when programa_presupuestario != \"\" then \"\"
                        else lower(capitulo)
                    end capitulo,
                    programa_presupuestario,
                    importe
                from (
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        \"\" capitulo,
                        \"\" programa_presupuestario,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp
                    union all 
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_capitulo,\"000 \",
                            capitulo
                        ) as capitulo,
                        \"\" programa_presupuestario,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_capitulo,vppa.capitulo
                    union all 
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_capitulo,\"000 \",
                            capitulo
                        ) as capitulo,
                        concat(
                            clv_programa,\" \",
                            programa
                        ) as programa_presupuestario,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_capitulo,vppa.capitulo,clv_programa,vppa.programa
                    order by upp,capitulo,programa_presupuestario
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_6(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when funcion != \"\" then \"\"
                        else finalidad
                    end finalidad,
                    case 
                        when subfuncion != \"\" then \"\"
                        else funcion
                    end funcion,
                    subfuncion,
                    importe
                from (
                    select 
						clv_finalidad,
                        finalidad,
                        \"\" funcion,
                        \"\" subfuncion,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_finalidad,vppa.finalidad
                    union all
                    select 
						clv_finalidad,
                        finalidad,
                        funcion,
                        \"\" subfuncion,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_finalidad,vppa.finalidad,vppa.funcion
                    union all
                    select 
						clv_finalidad,
                        finalidad,
                        funcion,
                        subfuncion,
                        sum(total) importe
                    from ',@tabla,'
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_finalidad,vppa.finalidad,vppa.funcion,vppa.subfuncion
                    order by clv_finalidad,finalidad,funcion,subfuncion
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_7(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when ur != \"\" then \"\"
                        else upp
                    end upp,
                    case 
                        when funcion != \"\" then \"\"
                        else ur
                    end ur,
                    case 
                        when funcion != \"\" then \"\"
                        else finalidad
                    end finalidad,
                    case 
                        when subfuncion != \"\" then \"\"
                        else funcion
                    end funcion,
                    case 
                        when partida != \"\" then \"\"
                        else subfuncion
                    end subfuncion,
                    partida,
                    importe
                from (
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        \"\" ur,
                        \"\" finalidad,
                        \"\" funcion,
                        \"\" subfuncion,
                        \"\" partida,
                        sum(total) importe
                    from ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_ur,\" \",
                            ur
                        ) as ur,
                        finalidad,
                        \"\" funcion,
                        \"\" subfuncion,
                        \"\" partida,
                        sum(total) importe
                    from ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_ur,\" \",
                            ur
                        ) as ur,
                        finalidad,
                        funcion,
                        \"\" subfuncion,
                        \"\" partida,
                        sum(total) importe
                    from ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                    funcion
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_ur,\" \",
                            ur
                        ) as ur,
                        finalidad,
                        funcion,
                        subfuncion,
                        \"\" partida,
                        sum(total) importe
                    from ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                    funcion,subfuncion
                    union all
                    select 
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        concat(
                            clv_ur,\" \",
                            ur
                        ) as ur,
                        finalidad,
                        funcion,
                        subfuncion,
                        concat(
                            clv_capitulo,
                            clv_concepto,
                            clv_partida_generica,
                            clv_partida_especifica,\" \",
                            partida_especifica
                        ) as partida,
                        sum(total) importe
                    from ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_ur,vppa.ur,finalidad,
                    funcion,subfuncion,clv_capitulo,clv_concepto,
                    clv_partida_generica,clv_partida_especifica,partida_especifica
                    order by upp,ur,finalidad,funcion,subfuncion,partida
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_11_8(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado vppa';
            set @corte := 'vppa.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist vppa';
                set @corte := CONCAT('vppa.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when programa != \"\" then \"\"
                        else clv_upp
                    end clv_upp,
                    case 
                        when programa != \"\" then \"\"
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
                        \"\" clv_programa,
                        \"\" programa,
                        \"\" clv_proyecto_obra,
                        \"\" proyecto_obra,
                        sum(total) importe
                    from ',@tabla,' 
                    left join proyectos_obra po on vppa.proyecto_obra = po.clv_proyecto_obra
                    where ejercicio = ',anio,' and ',@corte,'
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
                    from  ',@tabla,' 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_upp,vppa.upp,clv_programa,vppa.programa,
                    vppa.clv_proyecto_obra,vppa.proyecto_obra
                    order by clv_upp,upp,programa,proyecto_obra
                ) tabla;
            ');
            
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE calendario_general(in anio int, in corte date, in uppC varchar(3))
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            set @upp := '';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            if (uppC is not null) then set @upp := CONCAT('and clv_upp = \"',uppC,'\"'); end if;

            set @query := CONCAT('         
                select
                    orden,
                    upp,
                    clave,
                    monto_anual,
                    enero,febrero,marzo,abril,mayo,
                    junio,julio,agosto,septiembre,
                    octubre,noviembre,diciembre,
                    cec.capturista
                from (
                    select 
                        clv_upp,
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        1 orden,
                        concat(clv_upp,\" \",upp) clave,
                        sum(total) monto_anual,
                        sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                        sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                        sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                    from ',@tabla,' vppa
                    where ejercicio = ',anio,' and ',@corte,' ',@upp,'
                    group by vppa.clv_upp,vppa.upp,orden,vppa.upp
                    union all
                    select 
                        clv_upp,
                        concat(
                            clv_upp,\" \",
                            upp
                        ) as upp,
                        2 orden,
                        concat(
                            pp.clv_sector_publico,
                            pp.clv_sector_publico_f,
                            pp.clv_sector_economia,
                            pp.clv_subsector_economia,
                            pp.clv_ente_publico,
                            \"-\",pp.clv_entidad_federativa,
                            \"-\",pp.clv_region,
                            \"-\",pp.clv_municipio,
                            \"-\",pp.clv_localidad,
                            \"-\",pp.clv_upp,
                            \"-\",pp.clv_subsecretaria,
                            \"-\",pp.clv_ur,
                            \"-\",pp.clv_finalidad,
                            \"-\",pp.clv_funcion,
                            \"-\",pp.clv_subfuncion,
                            \"-\",pp.clv_eje,
                            \"-\",pp.clv_linea_accion,
                            \"-\",pp.clv_programa_sectorial,
                            \"-\",pp.clv_tipologia_conac,
                            \"-\",pp.clv_programa,
                            \"-\",pp.clv_subprograma,
                            \"-\",pp.clv_proyecto,
                            \"-\",pp.periodo_presupuestal,
                            \"-\",pp.clv_capitulo,
                            pp.clv_concepto,
                            pp.clv_partida_generica,
                            pp.clv_partida_especifica,
                            \"-\",pp.clv_tipo_gasto,
                            \"-\",pp.anio,
                            \"-\",pp.clv_etiquetado,
                            \"-\",pp.clv_fuente_financiamiento,
                            \"-\",pp.clv_ramo,
                            \"-\",pp.clv_fondo_ramo,
                            \"-\",pp.clv_capital,
                            \"-\",pp.clv_proyecto_obra
                        ) clave,
                        total monto_anual,
                        enero,febrero,marzo,abril,mayo,
                        junio,julio,agosto,septiembre,
                        octubre,noviembre,diciembre
                    from ',@tabla,' pp
                    where ejercicio = ',anio,' and ',@corte,' ',@upp,'
                    order by upp,orden
                ) tabla
                left join cierre_ejercicio_claves cec on tabla.clv_upp =  cec.clv_upp and cec.deleted_at is null and cec.ejercicio = ',anio,';
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE calendario_fondo_mensual(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'vppa.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
                
            set @query := CONCAT('
                select 
                    ramo,
                    fondo_ramo,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,sum(mayo) mayo,
                    sum(junio) junio,sum(julio) julio,sum(agosto) agosto,sum(septiembre) septiembre,
                    sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre,
                    sum(total) importe_total
                from ',@tabla,' vppa 
                where ejercicio = ',anio,' and ',@corte,'
                group by ramo,fondo_ramo;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_resumen_por_capitulo_y_partida(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT('
                select 
                    case 
                        when partida != \"\" then \"\"
                        else capitulo
                    end capitulo,
                    partida,
                    importe
                from (
                    select 
                        concat(
                            clv_capitulo,\"000 \",
                            capitulo
                        ) as capitulo,
                        \"\" partida,
                        sum(total) importe
                    from ',@tabla,' vppa 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_capitulo,vppa.capitulo
                    union all
                    select 
                        concat(
                            clv_capitulo,\"000 \",
                            capitulo
                        ) as capitulo,
                        concat(
                            clv_capitulo,
                            clv_concepto,
                            clv_partida_generica,
                            clv_partida_especifica,\" \",
                            partida_especifica
                        ) as partida,
                        sum(total) importe
                    from ',@tabla,' vppa 
                    where ejercicio = ',anio,' and ',@corte,'
                    group by clv_capitulo,vppa.capitulo,clv_concepto,
                    clv_partida_generica,clv_partida_especifica,partida_especifica
                    order by capitulo,partida
                ) tabla;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
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
        set @corte := 'mm.deleted_at is null';
            set @upp := '';
            if (corte is not null) then 
                set @corte := CONCAT('mm.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            if (upp is not null) then 
                set @upp := CONCAT('and mm.clv_upp = \"',upp,'\"');
            end if;
                
            set @query := CONCAT('
				select 
					c.descripcion upp,
					t.*,
					cem.capturista
				from (
	                select 
	                    mm.clv_upp,
	                    substr(mm.entidad_ejecutora,5,2) clv_ur,
	                    substr(mm.area_funcional,9,2) clv_programa,
	                    substr(mm.area_funcional,11,3) clv_subprograma,
	                    substr(mm.area_funcional,14,3) clv_proyecto,
	                    m.clv_fondo,
	                    mm.indicador actividad,
	                    m.cantidad_beneficiarios,
	                    b.beneficiario,
	                    um.unidad_medida,
	                    m.tipo,
	                    case 
	                        when m.tipo = \"Acumulativa\" then 
	                            (m.enero+m.febrero+m.marzo+m.abril+m.mayo+m.junio+m.julio+
	                            m.agosto+m.septiembre+m.octubre+m.noviembre+m.diciembre)
	                        when m.tipo = \"Continua\" then m.enero
	                        when m.tipo = \"Especial\" then greatest
	                            (m.enero,m.febrero,m.marzo,m.abril,m.mayo,
	                            m.junio,m.julio,m.agosto,m.septiembre,
	                            m.octubre,m.noviembre,m.diciembre)
	                    end meta_anual,
	                    m.enero,m.febrero,m.marzo,m.abril,m.mayo,
	                    m.junio,m.julio,m.agosto,m.septiembre,
	                    m.octubre,m.noviembre,m.diciembre
	                from mml_mir mm 
	                join metas m on m.mir_id = mm.id
	                join unidades_medida um on m.unidad_medida_id = um.id 
	                join beneficiarios b on m.beneficiario_id = b.id
	                where mm.nivel = 11 ',@upp,' and mm.ejercicio = ',anio,' and ',@corte,'
					union all 
					select 
					    mm.clv_upp,
					    substr(mm.entidad_ejecutora,5,2) clv_ur,
					    substr(mm.area_funcional,9,2) clv_programa,
					    substr(mm.area_funcional,11,3) clv_subprograma,
					    substr(mm.area_funcional,14,3) clv_proyecto,
					    m.clv_fondo,
					    mm.nombre actividad,
					    m.cantidad_beneficiarios,
					    b.beneficiario,
					    um.unidad_medida,
					    m.tipo,
					    case 
					        when m.tipo = \"Acumulativa\" then 
					            (m.enero+m.febrero+m.marzo+m.abril+m.mayo+m.junio+m.julio+
					            m.agosto+m.septiembre+m.octubre+m.noviembre+m.diciembre)
					        when m.tipo = \"Continua\" then m.enero
					        when m.tipo = \"Especial\" then greatest
					            (m.enero,m.febrero,m.marzo,m.abril,m.mayo,
					            m.junio,m.julio,m.agosto,m.septiembre,
					            m.octubre,m.noviembre,m.diciembre)
					    end meta_anual,
					    m.enero,m.febrero,m.marzo,m.abril,m.mayo,
					    m.junio,m.julio,m.agosto,m.septiembre,
					    m.octubre,m.noviembre,m.diciembre
					from mml_actividades mm
					join metas m on m.actividad_id = mm.id
					join unidades_medida um on m.unidad_medida_id = um.id 
					join beneficiarios b on m.beneficiario_id = b.id
					where mm.ejercicio = ',anio,' ',@upp,'  and ',@corte,'
				)t
				left join catalogo c on clv_upp = c.clave and c.deleted_at is null and c.grupo_id = 6
				left join cierre_ejercicio_metas cem on t.clv_upp = cem.clv_upp and cem.ejercicio = ',anio,' and cem.deleted_at is null
				order by clv_upp;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_b_num_5(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado pa';
            set @corte := 'pa.deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist pa';
                set @corte := CONCAT('pa.deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;

            set @query := CONCAT(\"
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
                    case 
                        when importe is null then 0
                        else importe
                    end importe
                from (
                    select
                        etiquetado,
                        '' abuelo,
                        -2 orden,
                        '' padre,
                        '' hijo,
                        sum(total) importe
                    from \",@tabla,\"
                    where ejercicio = \",anio,\" and \",@corte,\"
                    group by etiquetado
                    union all
                    select 
                        pa.etiquetado,
                        'Programas' abuelo,
                        -1 orden,
                        '' padre,
                        '' hijo,
                        sum(total) importe
                    from tipologia_conac tc
                    join \",@tabla,\" on tc.clave_conac = pa.clv_tipologia_conac
                    where ejercicio = \",anio,\" and \",@corte,\" and tc.deleted_at is null
                    group by etiquetado
                    union all 
                    select 
                        pa.etiquetado,
                        'Programas' abuelo,
                        min(tc.id) orden,
                        tc.descripcion padre,
                        '' hijo,
                        sum(total) importe
                    from tipologia_conac tc
                    join \",@tabla,\" on tc.clave_conac = pa.clv_tipologia_conac
                    where ejercicio = \",anio,\" and \",@corte,\" and tc.deleted_at is null
                    group by etiquetado,padre
                    union all
                    select 
                        pa.etiquetado,
                        'Programas' abuelo,
                        min(tc.id) orden,
                        tc.descripcion padre,
                        tc.descripcion_conac hijo,
                        sum(total) importe
                    from tipologia_conac tc 
                    join \",@tabla,\" on tc.clave_conac = pa.clv_tipologia_conac
                    where  pa.ejercicio = \",anio,\" and \",@corte,\" and pa.deleted_at is null
                    group by etiquetado,padre,hijo
                    union all 
                    select 
                        'No etiquetado' etiquetado,
                        tc.descripcion abuelo,
                        min(tc.id) orden,
                        '' padre,
                        '' hijo,
                        sum(total) importe
                    from tipologia_conac tc
                    left join \",@tabla,\" on tc.descripcion = pa.tipologia_conac
                    and pa.ejercicio = \",anio,\" and \",@corte,\" and pa.clv_etiquetado = 1
                    where tc.deleted_at is null and tc.tipo = 1
                    group by abuelo
                    union all 
                    select 
                        'Etiquetado' etiquetado,
                        tc.descripcion abuelo,
                        min(tc.id) orden,
                        '' padre,
                        '' hijo,
                        sum(total) importe
                    from tipologia_conac tc
                    left join \",@tabla,\" on tc.descripcion = pa.tipologia_conac
                    and pa.ejercicio = \",anio,\" and \",@corte,\" and pa.clv_etiquetado = 2
                    where tc.deleted_at is null and tc.tipo = 1
                    group by abuelo
                    order by etiquetado desc,orden,padre,hijo
                )t;
            \");

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        END;");
        
        DB::unprepared("CREATE PROCEDURE proyecto_avance_general(in anio int, in corte date)
        begin
            set @tabla := 'pp_aplanado';
            set @corte := 'deleted_at is null';
            if (corte is not null) then 
                set @tabla := 'pp_aplanado_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 DAY)');
            end if;
            
            set @query := CONCAT('
                select
                    case
                        when clv_fondo_ramo != \"\" then \"\"
                        else clv_upp 
                    end clv_upp, 
                    case
                        when clv_fondo_ramo != \"\" then \"\"
                        else upp 
                    end upp,
                    case
                        when clv_capitulo != \"\" then \"\"
                        else clv_fondo_ramo 
                    end clv_fondo_ramo,
                    case
                        when clv_capitulo != \"\" then \"\"
                        else fondo_ramo 
                    end fondo_ramo,
                    clv_capitulo,
                    capitulo,
                    monto_anual,
                    calendarizado,
                    disponible,
                    avance,
                    estatus
                from (
                    select 
                    t.clv_upp,
                    ve.upp,
                    clv_fondo clv_fondo_ramo,
                    case 
                        when fondo = \"\" AND clv_fondo_ramo != \"\" then p.fondo_ramo
                        else fondo
                    end fondo_ramo,
                    clv_capitulo,
                    capitulo,
                    monto_anual,
                    calendarizado,
                    (monto_anual-calendarizado) disponible,
                    (calendarizado/monto_anual)*100 avance,
                    case 
                        when t.clv_upp != \"\" then estatus
                        else \"\"
                    end estatus
                    from (
                    select 
                        clv_upp,
                        GROUP_CONCAT(upp separator \"\") upp,
                        clv_fondo,
                        group_concat(fondo separator \"\") fondo,
                        group_concat(clv_capitulo separator \"\") clv_capitulo,
                        group_concat(capitulo separator \"\") capitulo,
                        case 
                            when sum(monto_anual) = 0 then sum(calendarizado)
                            else sum(monto_anual)
                        end monto_anual,
                        sum(calendarizado) calendarizado,
                        case 
                        when sum(estado) > 0 then \"Confirmado\"
                            else \"Registrado\"
                        end estatus
                        from (
                            select *
                            from (
                                select 
                                    clv_upp,
                                    upp,
                                    \"\" clv_fondo,
                                    \"\" fondo,
                                    \"\" clv_capitulo,
                                    \"\" capitulo,
                                    0 monto_anual,
                                    sum(total) calendarizado,
                                    sum(estado) estado
                                from ',@tabla,' pa
                                where pa.ejercicio = ',anio,' and pa.',@corte,'
                                group by clv_upp,upp
                                union all
                                select 
                                    clv_upp,
                                    upp,
                                    clv_fondo_ramo clv_fondo,
                                    fondo_ramo fondo,
                                    \"\" clv_capitulo,
                                    \"\" capitulo,
                                    0 monto_anual,
                                    sum(total) calendarizado,
                                    sum(estado) estado
                                from ',@tabla,' pa
                                where pa.ejercicio = ',anio,' and pa.',@corte,'
                                group by clv_upp,upp,clv_fondo_ramo,fondo_ramo
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
                                    sum(estado) estado
                                from ',@tabla,' pa
                                where pa.ejercicio = ',anio,' and pa.',@corte,'
                                group by clv_upp,upp,clv_fondo_ramo,fondo_ramo,clv_capitulo,capitulo
                                order by clv_upp,clv_fondo,clv_capitulo
                            ) c
                            union all
                            select *
                            from (
                                select
                                    tf.clv_upp,
                                    \"\" upp,
                                    \"\" clv_fondo,
                                    \"\" fondo,
                                    \"\" clv_capitulo,
                                    \"\" capitulo,
                                    sum(tf.presupuesto) monto_anual,
                                    0 calendarizado,
                                    0 estado
                                from techos_financieros tf 
                                where tf.ejercicio = ',anio,' and tf.',@corte,'
                                group by clv_upp
                                union all
                                select
                                    tf.clv_upp,
                                    \"\" upp,
                                    tf.clv_fondo,
                                    \"\" fondo,
                                    \"\" clv_capitulo,
                                    \"\" capitulo,
                                    sum(tf.presupuesto) monto_anual,
                                    0 calendarizado,
                                    0 estado
                                from techos_financieros tf 
                                where tf.ejercicio = ',anio,' and tf.',@corte,'
                                group by clv_upp,clv_fondo
                                order by clv_upp,clv_fondo
                            ) ma
                        )c2
                        group by c2.clv_upp,c2.clv_fondo,c2.clv_capitulo,c2.capitulo
                    ) t
                    left join (select distinct clv_upp,upp from v_epp where ejercicio = ',anio,') ve 
                        on t.clv_upp = ve.clv_upp
                    left join (select distinct clv_fondo_ramo,fondo_ramo from fondo where deleted_at is null) as p
                        on p.clv_fondo_ramo = t.clv_fondo
                    group by clv_upp,ve.upp,clv_fondo,t.fondo,p.fondo_ramo,clv_capitulo,
                        t.capitulo,t.monto_anual,t.calendarizado,t.estatus
                )t2;
            ');

            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
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
                
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_sector_publico clave,'sector_publico' grupo from epp_aux ea where ea.id_sector_publico is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_sector_publico_f clave,'sector_publico_f' grupo from epp_aux ea where ea.id_sector_publico_f is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_sector_economia clave,'sector_economia' grupo from epp_aux ea where ea.id_sector_economia is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_subsector_economia clave,'subsector_economia' grupo from epp_aux ea where ea.id_subsector_economia is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_ente_publico clave,'ente_publico' grupo from epp_aux ea where ea.id_ente_publico is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_upp clave,'upp' grupo from epp_aux ea where ea.id_upp is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_subsecretaria clave,'subsecretaria' grupo from epp_aux ea where ea.id_subsecretaria is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_ur clave,'ur' grupo from epp_aux ea where ea.id_ur is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_finalidad clave,'finalidad' grupo from epp_aux ea where ea.id_finalidad is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_funcion clave,'funcion' grupo from epp_aux ea where ea.id_funcion is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_subfuncion clave,'subfuncion' grupo from epp_aux ea where ea.id_subfuncion is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_eje clave,'eje' grupo from epp_aux ea where ea.id_eje is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_linea_accion clave,'linea_accion' grupo from epp_aux ea where ea.id_linea_accion is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_programa_sectorial clave,'programa_sectorial' grupo from epp_aux ea where ea.id_programa_sectorial is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_tipologia_conac clave,'tipologia_conac' grupo from epp_aux ea where ea.id_tipologia_conac is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_programa  clave,'programa' grupo from epp_aux ea where ea.id_programa is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_subprograma clave,'subprograma' grupo from epp_aux ea where ea.id_subprograma is null;
                insert into rel_faltantes(clave,grupo) select distinct ea.clv_proyecto clave,'proyecto' grupo from epp_aux ea where ea.id_proyecto is null;
            
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
        END");

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
        
            set @query := CONCAT(\"select 
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
				select 
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
				and nivel in (10,11) \",@upp,\" \",@ur,\" \",@programa,\"
				union all
				select distinct
					ve.clv_upp,
					ve.clv_programa clv_pp,
					ve.clv_ur,
					'' area_funcional,
					'' proyecto,
					9 nivel,
					'' objetivo,
					'' indicador
				from v_epp ve
				where ejercicio = \",anio,\" and deleted_at is null \",@upp2,\" \",@ur2,\" \",@programa2,\"
				group by clv_upp,clv_pp,clv_ur,nivel
				order by clv_upp,clv_pp,clv_ur,nivel
			)t;\");
        
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