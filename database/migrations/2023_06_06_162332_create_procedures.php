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
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_III(in anio int)
        BEGIN
            select 
                ct5.clave upp_clave,
                ct5.descripcion upp,
                ct6.clave ur_clave,
                ct6.descripcion ur,
                ct4.clave programa_presupuestario_clave,
                ct4.descripcion programa_presupuestario,
                ct.clave subprograma_presupuestario_clave,
                ct.descripcion subprograma_presupuestario,
                concat(
                    ppt.llave,
                    ct3.clave
                ) partida_presupuestal_llave,
                ct2.descripcion partida_presupuestal,
                sum((enero+febrero+marzo+abril+mayo+junio+
                julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
            from programacion_presupuesto pp
            join entidad_ejecutora eet on pp.entidad_ejecutora_id = eet.id
            join area_funcional aft on pp.area_funcional_id = aft.id
            join posicion_presupuestaria ppt on pp.posicion_presupuestaria_id = ppt.id
            join catalogo ct on aft.subprograma_presupuestario_id = ct.id
            join catalogo ct2 on ppt.partida_especifica_id = ct2.id
            join catalogo ct3 on pp.tipo_gasto_id = ct3.id
            join catalogo ct4 on aft.programa_presupuestario_id = ct4.id
            join catalogo ct5 on eet.upp_id = ct5.id
            join catalogo ct6 on eet.ur_id = ct6.id
            where pp.ejercicio = anio and pp.deleted_at is NULL
            group by ct5.clave,ct5.descripcion,ct6.clave,ct6.descripcion,ct4.clave,
            ct4.descripcion,ct.clave,ct.descripcion,partida_presupuestal_llave,ct2.descripcion;
        END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_a_num_1(in anio int)
        BEGIN
            select 
                ct.descripcion concepto,
                sum((enero+febrero+marzo+abril+mayo+junio+
                julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
            from programacion_presupuesto pp
            join posicion_presupuestaria ppt on pp.posicion_presupuestaria_id = ppt.id
            join catalogo ct on ppt.concepto_id = ct.id
            where pp.ejercicio = 23 and pp.deleted_at is NULL
            group by ct.descripcion;
        END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_a_num_2(in anio int)
        BEGIN
            select 
        		ct.descripcion,
        		ct2.descripcion,
        		sum((enero+febrero+marzo+abril+mayo+junio+
        		junio+julio+agosto+septiembre+octubre+noviembre+diciembre)) as total
        	from programacion_presupuesto pp 
        	join area_funcional aft on pp.area_funcional_id = aft.id 
        	join catalogo ct on aft.finalidad_id = ct.id 
        	join catalogo ct2 on aft.funcion_id = ct2.id 
        	where pp.ejercicio = anio and pp.deleted_at is null 
        	group by ct.descripcion, ct2.descripcion;
        END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_10_frac_X_a_num_5(in anio int)
        begin
            select 
                ct.clave capitulo_clave,
                ct.descripcion capitulo,
                sum((enero+febrero+marzo+abril+mayo+junio+
                julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
            from programacion_presupuesto pp
            join posicion_presupuestaria ppt on pp.posicion_presupuestaria_id = ppt.id 
            join catalogo ct on ppt.capitulo_id = ct.id
            where pp.ejercicio = anio and pp.deleted_at is NULL
            group by ct.clave, ct.descripcion;
        END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_a_num_6(in anio int)
        begin
            select 
                ct.descripcion concepto,
                sum((enero+febrero+marzo+abril+mayo+junio+
                julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
            from programacion_presupuesto pp
            join catalogo ct on pp.tipo_gasto_id = ct.id
            where pp.ejercicio = anio and pp.deleted_at is NULL
            group by ct.clave, ct.descripcion;
        END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_1(in anio int, in corte date)
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
                    ct2.descripcion etiquetado,
                    '' upp,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                from programacion_presupuesto pp
                join catalogo ct2 on pp.etiquetado_id = ct2.id
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by ct2.descripcion
                union all
                select 
                    ct2.descripcion etiquetado,
                    ct.descripcion upp,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                from programacion_presupuesto pp
                join entidad_ejecutora eet on pp.entidad_ejecutora_id = eet.id 
                join catalogo ct on eet.upp_id = ct.id 
                join catalogo ct2 on pp.etiquetado_id = ct2.id
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by ct2.descripcion, ct.descripcion
                order by etiquetado,upp
            ) tabla;
        END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS art_20_frac_X_b_num_1(in anio int)
          begin
              select 
                  c.descripcion etiquetado,
                  c2.descripcion upp,
                  sum((enero+febrero+marzo+abril+mayo+junio+
                      julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
              from programacion_presupuesto pp 
              join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
              join catalogo c on pp.etiquetado_id = c.id 
              join catalogo c2 on ee.upp_id = c2.id 
              where pp.ejercicio = anio and pp.deleted_at is null
              group by c.descripcion,c2.descripcion
              order by c.descripcion;
          END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS art_20_frac_X_b_num_2(in anio int)
           begin
               select 
                   c.descripcion etiquetado,
                   c2.descripcion finalidad,
                   c3.descripcion funcion,
                   sum((enero+febrero+marzo+abril+mayo+junio+
                       julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
               from programacion_presupuesto pp 
               join area_funcional af on pp.area_funcional_id = af.id 
               join catalogo c on pp.etiquetado_id = c.id 
               join catalogo c2 on af.finalidad_id = c2.id 
               join catalogo c3 on af.funcion_id = c3.id 
               where pp.ejercicio = anio and pp.deleted_at is null
               group by c.descripcion,c2.descripcion,c3.descripcion
               order by c.descripcion,c2.descripcion;
           END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS art_20_frac_X_b_num_4(in anio int)
           begin
               select 
                   c.descripcion etiquetado,
                   c2.descripcion capitulo,
                   c3.descripcion concepto,
                   sum((enero+febrero+marzo+abril+mayo+junio+
                       julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
               from programacion_presupuesto pp 
               join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
               join catalogo c on pp.etiquetado_id = c.id 
               join catalogo c2 on pp2.capitulo_id = c2.id 
               join catalogo c3 on pp2.concepto_id = c3.id  
               where pp.ejercicio = anio and pp.deleted_at is null
               group by c.descripcion,c2.descripcion,c3.descripcion
               order by c.descripcion,c2.descripcion;
           END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_1(in anio int)
           begin
               select 
                   c.clave upp_clave,
                   c.descripcion upp_descripcion,
                   c2.descripcion subsecretaria_descripcion,
                   c3.descripcion ur_descripcion,
                   c4.descripcion fuente_descripcion,
                   sum((enero+febrero+marzo+abril+mayo+junio+
                   julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
               from programacion_presupuesto pp
               join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
               join fondo f on pp.fondo_id = f.id 
               join catalogo c on ee.upp_id = c.id 
               join catalogo c2 on ee.subsecretaria_id = c2.id 
               join catalogo c3 on ee.ur_id = c3.id 
               join catalogo c4 on f.fuente_financiamiento_id = c4.id 
               where pp.ejercicio = anio and pp.deleted_at is null
               group by c.clave,c.descripcion,c2.descripcion,
               c3.descripcion,c4.descripcion;
           END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_2(in anio int, in corte date)
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
                   upp,
                   importe
               from (
                   select
                       concat(
                           c.clave,' as ',
                           c.descripcion
                       ) region,
                       concat(
                           c2.clave,' as ',
                           c2.descripcion
                       ) municipio,
                       concat(
                           c3.clave,' as ',
                           c3.descripcion
                       ) localidad,
                       '' upp,
                       sum((enero+febrero+marzo+abril+mayo+junio+
                       julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                   from programacion_presupuesto pp
                   join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                   join clasificacion_geografica cg on pp.clasificacion_geografica_id = cg.id 
                   join catalogo c on cg.region_id = c.id 
                   join catalogo c2 on cg.municipio_id = c2.id 
                   join catalogo c3 on cg.localidad_id = c3.id 
                   where pp.ejercicio = anio and pp.deleted_at is null and if  (
                   corte is null,
                   pp.deleted_at is null,
                   pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
               )
                   group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                   c3.clave,c3.descripcion
                   union all
                   select
                       concat(
                           c.clave,' as ',
                           c.descripcion
                       ) region,
                       concat(
                           c2.clave,' as ',
                           c2.descripcion
                       ) municipio,
                       concat(
                           c3.clave,' as ',
                           c3.descripcion
                       ) localidad,
                       concat(
                       c4.clave,' as ',
                       c4.descripcion
                       ) upp,
                       sum((enero+febrero+marzo+abril+mayo+junio+
                       julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                   from programacion_presupuesto pp
                   join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                   join clasificacion_geografica cg on pp.clasificacion_geografica_id = cg.id 
                   join catalogo c on cg.region_id = c.id 
                   join catalogo c2 on cg.municipio_id = c2.id 
                   join catalogo c3 on cg.localidad_id = c3.id 
                   join catalogo c4 on ee.upp_id = c4.id 
                   where pp.ejercicio = anio and pp.deleted_at is null and if  (
                   corte is null,
                   pp.deleted_at is null,
                   pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
               )
                   group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                   c3.clave,c3.descripcion,c4.clave,c4.descripcion
                   order by region,municipio,localidad,upp
               ) tabla;
           END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_3(in anio int, in corte date)
            begin
                select
                    c.clave eje_clave,
                    c.descripcion eje,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                from programacion_presupuesto pp
                join area_funcional af on pp.area_funcional_id = af.id 
                join catalogo c on af.eje_id = c.id 
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by c.clave,c.descripcion;
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_4(in anio int, in corte date)
            begin
                select
                    c.clave programa_presupuestario_clave,
                    c.descripcion programa_presupuestario,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                from programacion_presupuesto pp
                join area_funcional af on pp.area_funcional_id = af.id 
                join catalogo c on af.programa_presupuestario_id = c.id 
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by c.clave,c.descripcion;
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_5(in anio int, in corte date)
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
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        '' capitulo,
                        '' programa_presupuestario,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                    join catalogo c on ee.upp_id = c.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c3.clave,'000 ',
                            c3.descripcion
                        ) capitulo,
                        '' programa_presupuestario,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                    join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c3 on pp2.capitulo_id = c3.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c3.clave,c3.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c3.clave,'000 ',
                            c3.descripcion
                        ) capitulo,
                        concat(
                            c2.clave,' as ',
                            c2.descripcion
                        ) programa_presupuestario,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on af.programa_presupuestario_id = c2.id 
                    join catalogo c3 on pp2.capitulo_id = c3.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c3.clave,c3.descripcion,c2.clave,c2.descripcion
                    order by upp,capitulo,programa_presupuestario
                ) tabla;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_6(in anio int, in corte date)
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
                        c.descripcion finalidad,
                        '' funcion,
                        '' subfuncion,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on af.finalidad_id = c.id  
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion
                    union all
                    select
                        c.descripcion finalidad,
                        c2.descripcion funcion,
                        '' subfuncion,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on af.finalidad_id = c.id 
                    join catalogo c2 on af.funcion_id = c2.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion,c2.descripcion
                    union all
                    select
                        c.descripcion finalidad,
                        c2.descripcion funcion,
                        c3.descripcion subfuncion,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on af.finalidad_id = c.id 
                    join catalogo c2 on af.funcion_id = c2.id 
                    join catalogo c3 on af.subfuncion_id = c3.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion,c2.descripcion,c3.descripcion
                    order by finalidad,funcion,subfuncion
                ) tabla;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_7(in anio int, in corte date)
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
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        '' ur,
                        '' finalidad,
                        '' funcion,
                        '' subfuncion,
                        '' partida,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join catalogo c on ee.upp_id = c.id  
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c2.clave,' as ',
                            c2.descripcion
                        ) ur,
                        c3.descripcion finalidad,
                        '' funcion,
                        '' subfuncion,
                        '' partida,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on ee.ur_id = c2.id 
                    join catalogo c3 on af.finalidad_id = c3.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                    c3.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c2.clave,' as ',
                            c2.descripcion
                        ) ur,
                        c3.descripcion finalidad,
                        c4.descripcion funcion,
                        '' subfuncion,
                        '' partida,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on ee.ur_id = c2.id 
                    join catalogo c3 on af.finalidad_id = c3.id 
                    join catalogo c4 on af.funcion_id = c4.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                    c3.descripcion,c4.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c2.clave,' as ',
                            c2.descripcion
                        ) ur,
                        c3.descripcion finalidad,
                        c4.descripcion funcion,
                        c5.descripcion subfuncion,
                        '' partida,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on ee.ur_id = c2.id 
                    join catalogo c3 on af.finalidad_id = c3.id 
                    join catalogo c4 on af.funcion_id = c4.id 
                    join catalogo c5 on af.subfuncion_id = c5.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                    c3.descripcion,c4.descripcion,c5.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        concat(
                            c2.clave,' as ',
                            c2.descripcion
                        ) ur,
                        c3.descripcion finalidad,
                        c4.descripcion funcion,
                        c5.descripcion subfuncion,
                        concat(
                            pp2.llave,' as ',
                            c6.descripcion
                        ) partida,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on ee.ur_id = c2.id 
                    join catalogo c3 on af.finalidad_id = c3.id 
                    join catalogo c4 on af.funcion_id = c4.id 
                    join catalogo c5 on af.subfuncion_id = c5.id 
                    join catalogo c6 on pp2.partida_especifica_id = c6.id
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                    c3.descripcion,c4.descripcion,c5.descripcion,pp2.llave,c6.descripcion
                    order by upp,ur,finalidad,funcion,subfuncion,partida
                ) tabla;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_11_8(in anio int, in corte date)
            begin
                select
                    case 
                        when programa != '' then ''
                        else upp
                    end upp,
                    programa_clave,
                    programa,
                    obra_clave,
                    obra_accion,
                    importe
                from (
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        '' programa_clave,
                        '' programa,
                        '' obra_clave,
                        '' obra_accion,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join catalogo c on ee.upp_id = c.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion
                    union all
                    select
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) upp,
                        c2.clave programa_clave,
                        c2.descripcion programa,
                        c3.clave obra_clave,
                        c3.descripcion obra_accion,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on ee.upp_id = c.id 
                    join catalogo c2 on af.programa_presupuestario_id = c2.id 
                    join catalogo c3 on pp.proyecto_presupuestal_id = c3.id
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion,c2.clave,c2.descripcion,
                    c3.clave,c3.descripcion
                    order by upp,programa,obra_clave
                ) tabla;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS fondos_db.calendario_fondo_mensual(in anio int, in corte date)
            begin
                select
                    c.descripcion ramo,
                    c2.descripcion fondo,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,
                    sum(mayo) mayo,sum(junio) junio,sum(julio) julio,sum(agosto) agosto,
                    sum(septiembre) septiembre,sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                from programacion_presupuesto pp
                join fondo f on pp.fondo_id = f.id 
                join catalogo c on f.ramo_id = c.id 
                join catalogo c2 on f.fondo_ramo_id = c2.id 
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by c.descripcion,c2.descripcion;
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS calendario_general(in anio int, in corte date)
            begin
                select
                    clave,
                    monto_anual,
                    enero,febrero,marzo,abril,
                    mayo,junio,julio,agosto,
                    septiembre,octubre,noviembre,diciembre
                from (
                    select 
                        c.clave upp_clave,
                        concat(
                            c.clave,' as ',
                            c.descripcion
                        ) clave,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as monto_anual,
                        sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,
                        sum(mayo) mayo,sum(junio) junio,sum(julio) julio,sum(agosto) agosto,
                        sum(septiembre) septiembre,sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                    from programacion_presupuesto pp 
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                    join catalogo c on ee.upp_id = c.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.clave,c.descripcion
                    union all
                    select
                        c4.clave upp_clave,
                        concat(
                            ca.llave,'_',
                            substring(cg.llave,1,2),'_',substring(cg.llave,3,2),'_',
                            substring(cg.llave,5,3),'_',substring(cg.llave,8,3),
                            '_',
                            substring(ee.llave,1,3),'_',substring(ee.llave,3,1),'_',
                            substring(ee.llave,4,2),
                            '_',
                            substring(af.llave,1,1),'_',substring(af.llave,2,1),'_',
                            substring(af.llave,3,1),'_',substring(af.llave,4,1),'_',
                            substring(af.llave,5,2),'_',substring(af.llave,7,1),'_',
                            substring(af.llave,8,1),'_',substring(af.llave,9,2),'_',
                            substring(af.llave,10,3),'_',substring(af.llave,13,3),
                            '_',
                            pp.mes_afectacion,'_',
                            substring(pp2.llave,1,1),'_',substring(pp2.llave,2,1),'_',
                            substring(pp2.llave,3,1),'_',substring(pp2.llave,4,2),
                            '_',
                            c.clave,'_',
                            pp.ejercicio,'_',
                            c2.clave,'_',
                            substring(f.llave,1,1),'_',substring(f.llave,2,2),'_',
                            substring(f.llave,4,2),'_',substring(f.llave,6,1),
                            '_',
                            c3.clave
                        ) clave,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as monto_anual,
                        sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,
                        sum(mayo) mayo,sum(junio) junio,sum(julio) julio,sum(agosto) agosto,
                        sum(septiembre) septiembre,sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                    from programacion_presupuesto pp
                    join clasificacion_administrativa ca on pp.clasificacion_administrativa_id = ca.id 
                    join clasificacion_geografica cg on pp.clasificacion_geografica_id = cg.id 
                    join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                    join catalogo c on pp.tipo_gasto_id = c.id 
                    join catalogo c2 on pp.etiquetado_id = c2.id 
                    join fondo f on pp.fondo_id = f.id 
                    join catalogo c3 on pp.proyecto_presupuestal_id = c3.id
                    join catalogo c4 on ee.upp_id = c4.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c4.clave,ca.llave,cg.llave,ee.llave,af.llave,pp.mes_afectacion,
                    pp2.llave,c.clave,pp.ejercicio,c2.clave,f.llave,c3.clave
                    order by upp_clave
                ) tabla;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS calendario_general_upp(in anio int, in corte date, in upp varchar(3))
            begin
                select
                    concat(
                        ca.llave,'_',
                        substring(cg.llave,1,2),'_',substring(cg.llave,3,2),'_',
                        substring(cg.llave,5,3),'_',substring(cg.llave,8,3),
                        '_',
                        substring(ee.llave,1,3),'_',substring(ee.llave,4,1),'_',
                        substring(ee.llave,5,2),
                        '_',
                        substring(af.llave,1,1),'_',substring(af.llave,2,1),'_',
                        substring(af.llave,3,1),'_',substring(af.llave,4,1),'_',
                        substring(af.llave,5,2),'_',substring(af.llave,7,1),'_',
                        substring(af.llave,8,1),'_',substring(af.llave,9,2),'_',
                        substring(af.llave,10,3),'_',substring(af.llave,13,3),
                        '_',
                        pp.mes_afectacion,'_',
                        substring(pp2.llave,1,1),'_',substring(pp2.llave,2,1),'_',
                        substring(pp2.llave,3,1),'_',substring(pp2.llave,4,2),
                        '_',
                        c.clave,'_',
                        pp.ejercicio,'_',
                        c2.clave,'_',
                        substring(f.llave,1,1),'_',substring(f.llave,2,2),'_',
                        substring(f.llave,4,2),'_',substring(f.llave,6,1),
                        '_',
                        c3.clave
                    ) clave,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as monto_anual,
                    sum(enero) enero,sum(febrero) febrero,sum(marzo) marzo,sum(abril) abril,
                    sum(mayo) mayo,sum(junio) junio,sum(julio) julio,sum(agosto) agosto,
                    sum(septiembre) septiembre,sum(octubre) octubre,sum(noviembre) noviembre,sum(diciembre) diciembre
                from programacion_presupuesto pp
                join clasificacion_administrativa ca on pp.clasificacion_administrativa_id = ca.id 
                join clasificacion_geografica cg on pp.clasificacion_geografica_id = cg.id 
                join entidad_ejecutora ee on pp.entidad_ejecutora_id = ee.id 
                join area_funcional af on pp.area_funcional_id = af.id 
                join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                join catalogo c on pp.tipo_gasto_id = c.id 
                join catalogo c2 on pp.etiquetado_id = c2.id 
                join fondo f on pp.fondo_id = f.id 
                join catalogo c3 on pp.proyecto_presupuestal_id = c3.id
                join catalogo c4 on ee.upp_id = c4.id 
                where pp.ejercicio = 23 and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                ) and c4.clave = '001'
                group by ca.llave,cg.llave,ee.llave,af.llave,pp.mes_afectacion,
                pp2.llave,c.clave,pp.ejercicio,c2.clave,f.llave,c3.clave;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS resumen_capitulo_partida(in anio int, in corte date)
            begin
                select
                    concat(
                        c.clave,'000 ',c.descripcion
                    ) capitulo,
                    pp2.llave partida_llave,
                    c2.descripcion partida,
                    sum((enero+febrero+marzo+abril+mayo+junio+
                    julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                from programacion_presupuesto pp
                join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                join catalogo c on pp2.capitulo_id = c.id 
                join catalogo c2 on pp2.partida_especifica_id = c2.id 
                where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                group by c.clave,c.descripcion,pp2.llave,c2.descripcion;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_a_num_3(in anio int, in corte date)
            begin
                select 
                    case
                        when hijo != '' then ''
                        else padre
                    end padre,
                    hijo,
                    importe
                from (
                    select 
                        case 
                            when padre is null
                            then hijo
                        else padre
                        end padre,
                        case 
                            when padre is null then null
                            else hijo
                        end hijo,
                        importe
                    from (
                        select 
                            cP.descripcion padre,
                            cH.descripcion hijo,
                            sum((enero+febrero+marzo+abril+mayo+junio+
                            julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                        from programacion_presupuesto pp 
                        join area_funcional af on pp.area_funcional_id = af.id 
                        join tipologia_conac tc on af.tipologia_conac_id = tc.hijo_id
                        join catalogo cP on tc.padre_id = cP.id
                        join catalogo cH on tc.hijo_id = cH.id
                        where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                        group by cP.descripcion,cH.descripcion
                        union all
                        select 
                            cP.descripcion padre,
                            ' as ' hijo,
                            sum((enero+febrero+marzo+abril+mayo+junio+
                            julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                        from programacion_presupuesto pp
                        join area_funcional af on pp.area_funcional_id = af.id 
                        join tipologia_conac tc on af.tipologia_conac_id = tc.hijo_id
                        join catalogo cP on tc.padre_id = cP.id
                        where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                        group by cP.descripcion
                    ) tabla
                    order by padre, hijo
                ) Tfinal;
            END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_art_20_frac_X_b_num_5(in anio int, in corte date)
            begin
                select 
                    case 
                        when padre != '' then ''
                        else etiquetado
                    end etiquetado,
                    case 
                        when padre is null then hijo
                        when hijo != '' then ''
                        else padre
                    end padre,
                    case 
                        when padre is null then null
                        else hijo
                    end hijo,
                    importe
                from (
                    select 
                        c.descripcion etiquetado,
                        '' padre,
                        '' hijo,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp 
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join catalogo c on pp.etiquetado_id = c.id 
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion
                    union all
                    select 
                        c.descripcion etiquetado,
                        cP.descripcion padre,
                        '' hijo,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp 
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join tipologia_conac tc on af.tipologia_conac_id = tc.hijo_id
                    join catalogo c on pp.etiquetado_id = c.id 
                    join catalogo cP on tc.padre_id = cP.id
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion,cP.descripcion
                    union all
                    select 
                        c.descripcion etiquetado,
                        cP.descripcion padre,
                        cH.descripcion hijo,
                        sum((enero+febrero+marzo+abril+mayo+junio+
                        julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                    from programacion_presupuesto pp 
                    join area_funcional af on pp.area_funcional_id = af.id 
                    join tipologia_conac tc on af.tipologia_conac_id = tc.hijo_id
                    join catalogo c on pp.etiquetado_id = c.id 
                    join catalogo cP on tc.padre_id = cP.id
                    join catalogo cH on tc.hijo_id = cH.id
                    where pp.ejercicio = anio and pp.deleted_at is null and if  (
                    corte is null,
                    pp.deleted_at is null,
                    pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                )
                    group by c.descripcion,cP.descripcion,cH.descripcion
                    order by etiquetado,padre,hijo
                ) tabla;
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS reporte_resumen_por_capitulo_y_partida(in anio int, in corte date)
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
                                c.clave,'000 ',
                                c.descripcion
                            ) capitulo,
                            '' partida,
                            sum((enero+febrero+marzo+abril+mayo+junio+
                            julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                        from programacion_presupuesto pp 
                        join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                        join catalogo c on pp2.capitulo_id = c.id 
                        where pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                        group by c.clave,c.descripcion
                        union all
                        select 
                            concat(
                                c.clave,'000 ',
                                c.descripcion
                            ) capitulo,
                            concat(
                                pp2.llave,' as ',
                                c2.descripcion
                            ) partida,
                            sum((enero+febrero+marzo+abril+mayo+junio+
                            julio+agosto+septiembre+octubre+noviembre+diciembre)) as importe
                        from programacion_presupuesto pp 
                        join posicion_presupuestaria pp2 on pp.posicion_presupuestaria_id = pp2.id 
                        join catalogo c on pp2.capitulo_id = c.id 
                        join catalogo c2 on pp2.partida_especifica_id = c2.id 
                        where pp.ejercicio = anio and pp.deleted_at is null and if  (
                        corte is null,
                        pp.deleted_at is null,
                        pp.deleted_at between corte and DATE_ADD(corte, INTERVAL 1 DAY)
                    )
                        group by c.clave,c.descripcion,pp2.llave,c2.descripcion
                        order by capitulo,partida
                    ) tabla;
                END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS avance_general(in anio int, in corte date)
                begin
                        select
                        case 
                            when fondo != '' then ''
                            else upp_clave
                        end upp_clave,
                        case 
                            when fondo != '' then ''
                            else upp
                        end upp,
                        fondo_clave,
                        fondo,
                        capitulo_clave,
                        capitulo,
                        monto_anual,
                        calendarizado,
                        (monto_anual-calendarizado) disponible,
                        truncate((calendarizado/monto_anual),2) avance
                    from (
                        select 
                            c.clave upp_clave,
                            c.descripcion upp,
                            '' fondo_clave,
                            '' fondo,
                            '' capitulo_clave,
                            '' capitulo,
                            sum(monto_anual) monto_anual,
                            sum(calendarizado) calendarizado
                        from v_presupuesto_upp_fondo vpf
                        join catalogo c on vpf.upp_id = c.id 
                        where vpf.ejercicio = anio
                        group by c.clave,c.descripcion
                        union all
                        select 
                            c.clave upp_clave,
                            c.descripcion upp,
                            c2.clave fondo_clave,
                            c2.descripcion fondo,
                            '' capitulo_clave,
                            '' capitulo,
                            monto_anual,
                            calendarizado
                        from v_presupuesto_upp_fondo vpf
                        join catalogo c on vpf.upp_id = c.id 
                        join catalogo c2 on vpf.fondo_id = c2.id 
                        where vpf.ejercicio = anio
                        order by upp_clave,fondo_clave
                    ) tabla;
                END;");

        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS SP_AF_EE(in anio varchar(255), in softdelete int)
            #Primer valor deben ser los años, ejemplo: SP_AF_EE('2023,2024',1) traera los valores activos del 2023 y 2024
            #Segundo valor debe ser un entero:
            #0 = valores en soft-delete 
            #1 = valores activos
            #otro = todos
            #Ejemplo: SP_AF_EE('2022',0) traera los valores inactivos del 2022
            begin
                select 
                    ct1.deleted_at upp_fecha_baja,
                    ct1.clave upp_clave,
                    ct1.descripcion upp,
                    ct2.clave subsecretaria_clave,
                    ct2.descripcion subsecretaria,
                    ct3.clave ur_clave,
                    ct3.descripcion ur,
                    ct4.clave programa_clave,
                    ct4.descripcion programa,
                    ct5.clave subprograma_clave,
                    ct5.descripcion subprograma,
                    ct6.clave proyecto_clave,
                    ct6.descripcion proyecto
                from area_funcional_entidad_ejecutora afee 
                join area_funcional aft on afee.area_funcional_id = aft.id 
                join entidad_ejecutora eet on afee.entidad_ejecutora_id = eet.id 
                join catalogo ct1 on eet.upp_id = ct1.id 
                join catalogo ct2 on eet.subsecretaria_id = ct2.id 
                join catalogo ct3 on eet.ur_id = ct3.id 
                join catalogo ct4 on aft.programa_presupuestario_id = ct4.id 
                join catalogo ct5 on aft.subprograma_presupuestario_id = ct5.id 
                join catalogo ct6 on aft.proyecto_presupuestario_id = ct6.id
                where find_in_set(ct1.ejercicio, anio) and (case 
                    when softdelete = 0 then ct1.deleted_at is not null
                    when softdelete = 1 then ct1.deleted_at is null
                    else ct1.id >= 1
                    end);
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS SP_claves_descripciones(
                in clasAdmin varchar(5),in clasGeo varchar(10),in entiEjec varchar(6),in areaFunc varchar(16),
                in posPre varchar(5),in tipoGasto varchar(2),in fondoLlave varchar(7),in proyectoObra varchar(6)
            )
            begin
                select 
                    s.subgrupo,
                    c.clave,
                    c.descripcion
                from catalogo c 
                join subgrupos s on c.subgrupo_id = s.id
                where c.id in (
                    select * from (
                        select ca.sector_publico_id from clasificacion_administrativa ca where llave like clasAdmin union all
                        select ca.sector_publico_fnof_id from clasificacion_administrativa ca where llave like clasAdmin union all
                        select ca.sector_economia_id from clasificacion_administrativa ca where llave like clasAdmin union all
                        select ca.subsector_economia_id from clasificacion_administrativa ca where llave like clasAdmin union all
                        select ca.ente_publico_id from clasificacion_administrativa ca where llave like clasAdmin union all
                        select cg.entidad_federativa_id from clasificacion_geografica cg where llave like clasGeo union all
                        select cg.region_id from clasificacion_geografica cg where llave like clasGeo union all
                        select cg.municipio_id from clasificacion_geografica cg where llave like clasGeo union all
                        select cg.localidad_id from clasificacion_geografica cg where llave like clasGeo union all
                        select ee.upp_id from entidad_ejecutora ee where llave like entiEjec union all
                        select ee.subsecretaria_id from entidad_ejecutora ee where llave like entiEjec union all
                        select ee.ur_id from entidad_ejecutora ee where llave like entiEjec union all
                        select af.finalidad_id from area_funcional af where llave like areaFunc union all
                        select af.funcion_id from area_funcional af where llave like areaFunc union all
                        select af.subfuncion_id from area_funcional af where llave like areaFunc union all
                        select af.eje_id from area_funcional af where llave like areaFunc union all
                        select af.linea_accion_id from area_funcional af where llave like areaFunc union all
                        select af.programa_sectorial_id from area_funcional af where llave like areaFunc union all
                        select af.tipologia_conac_id from area_funcional af where llave like areaFunc union all
                        select af.programa_presupuestario_id from area_funcional af where llave like areaFunc union all
                        select af.subprograma_presupuestario_id from area_funcional af where llave like areaFunc union all
                        select af.proyecto_presupuestario_id from area_funcional af where llave like areaFunc union all
                        select pp.capitulo_id from posicion_presupuestaria pp where llave like posPre union all
                        select pp.concepto_id from posicion_presupuestaria pp where llave like posPre union all
                        select pp.partida_generica_id from posicion_presupuestaria pp where llave like posPre union all
                        select pp.partida_especifica_id from posicion_presupuestaria pp where llave like posPre union all
                        select c.id from catalogo c where c.subgrupo_id = 27 and c.clave = tipoGasto union all
                        select f.etiquetado_id from fondo f where llave like fondoLlave union all
                        select f.fuente_financiamiento_id from fondo f where llave like fondoLlave union all
                        select f.ramo_id from fondo f where llave like fondoLlave union all
                        select f.fondo_ramo_id from fondo f where llave like fondoLlave union all
                        select f.capital_id from fondo f where llave like fondoLlave union all
                        select c.id from catalogo c where subgrupo_id = 33 and clave like proyectoObra
                    ) as claves
                );
            END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS SP_entidad_ejecutora(in softdelete int)
        begin
            if softdelete = 3 then
                select 
                    c.clave upp_clave,
                    c.descripcion upp
                from catalogo c
                where c.subgrupo_id = 10;
            else
                select 
                    c.clave upp_clave,
                    c.descripcion upp,
                    c2.clave subsecretaria_clave,
                    c2.descripcion subsecretaria,
                    c3.clave ur_clave,
                    c3.descripcion ur
                from entidad_ejecutora ee 
                join catalogo c on ee.upp_id = c.id 
                join catalogo c2 on ee.subsecretaria_id = c2.id 
                join catalogo c3 on ee.ur_id = c3.id 
                WHERE ( case
                    when softdelete = 1 then c.deleted_at IS NULL
                    when softdelete = 0 then c.deleted_at IS NOT NULL 
                    ELSE ee.id > 0
                end);
            end if;
        END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS clave_presupuestal(in cl varchar(65))
        begin
            select 
                clave,
                descripcion
            from (
                select 
                    c.subgrupo_id,
                    c.clave,
                    c.descripcion
                from catalogo c 
                where c.id in
                (select id from
                (select @clave:=cl) param,clave_pres1) 
                union all
                select 
                    22.5 subgrupo_id,
                    mes_afectacion clave,
                    'Mes de Afectación' descripcion
                from programacion_presupuesto pp
                where pp.clave_presupuestal like cl
                union all
                select 
                    27.5 subgrupo_id,
                    ejercicio clave,
                    'Año' descripcion
                from programacion_presupuesto pp
                where pp.clave_presupuestal like cl
            ) tabla
            order by subgrupo_id;
        END;");
        DB::unprepared("CREATE PROCEDURE IF NOT EXISTS borrado_logico_presupuestos(in anio int)
        begin
            update programacion_presupuesto 
            set deleted_at = now()
            where ejercicio = anio and deleted_at is null;
        END;");
        
        DB::unprepared("CREATE PROCEDURE if not exists conceptos_clave(in claveT varchar(64))
        begin
            
        set @clave := claveT; 
            
        set @epp := concat(substring(@clave,1,5),substring(@clave,16,22)); 
        
        set @clasGeo := ((substring(@clave,6,10))*1); 
        
        set @partida := ((substring(@clave,44,6))*1); 
        
        set @fondo := substring(@clave,52,7); 
        
        set @obra := substring(@clave,59,6); 
            
            select *
            from (
                select 'Sector Público' descripcion, vel.clv_sector_publico clave,vel.sector_publico concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Sector Público Financiero/No Financiero' descripcion, vel.clv_sector_publico_f clave,vel.sector_publico_f concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Sector Economía' descripcion, vel.clv_sector_economia clave,vel.sector_economia concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Subsector Economía' descripcion,vel.clv_subsector_economia clave,vel.subsector_economia concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Ente Público' descripcion,vel.clv_ente_publico clave,vel.ente_publico concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Entidad Federativa' descripcion,vcg.clv_entidad_federativa clave,vcg.entidad_federativa concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Región' descripcion,vcg.clv_region clave,vcg.region concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Municipio' descripcion,vcg.clv_municipio clave,vcg.municipio concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Localidad' descripcion,vcg.clv_localidad clave,vcg.localidad concepto from v_clasificacion_geografica vcg where vcg.clasificacion_geografica_llave = @clasGeo union all
                select 'Unidad Programática Presupuestal' descripcion,vel.clv_upp clave,vel.upp concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Subsecretaría' descripcion,vel.clv_subsecretaria clave,vel.subsecretaria concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Unidad Responsable' descripcion,vel.clv_ur clave,vel.ur concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Finalidad' descripcion,vel.clv_finalidad clave,vel.finalidad concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Función' descripcion,vel.clv_funcion clave,vel.funcion concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Subfunción' descripcion,vel.clv_subfuncion clave,vel.subfuncion concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Eje' descripcion,vel.clv_eje clave,vel.eje concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Linea de Acción' descripcion,vel.clv_linea_accion clave,vel.linea_accion concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Programa Sectorial' descripcion,vel.clv_programa_sectorial clave,vel.programa_sectorial concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Tipología General' descripcion,vel.clv_tipologia_conac clave,vel.clv_tipologia_conac concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Programa Presupuestal' descripcion,vel.clv_programa clave,vel.programa concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Subprograma Presupuestal' descripcion,vel.clv_subprograma clave,vel.subprograma concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Proyecto Presupuestal' descripcion,vel.clv_proyecto clave,vel.proyecto concepto from v_epp_llaves vel where vel.llave like @epp union all
                select 'Mes de Afectación' descripcion,substring(@clave,38,6) clave, 'Mes de Afectación' union all
                select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from v_posicion_presupuestaria_llaves vppl where vppl.posicion_presupuestaria_llave like @partida union all
                select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from v_posicion_presupuestaria_llaves vppl where vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from v_posicion_presupuestaria_llaves vppl where vppl.posicion_presupuestaria_llave like @partida union all
                select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from v_posicion_presupuestaria_llaves vppl where vppl.posicion_presupuestaria_llave like @partida union all
                select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from v_posicion_presupuestaria_llaves vppl where vppl.posicion_presupuestaria_llave like @partida union all
                select 'Año (Fondo del Ramo)' descripcion,substring(@clave,50,2) clave, 'Año' concepto union all
                select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from v_fondo_llaves vfl where vfl.llave like @fondo union all
                select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from v_fondo_llaves vfl where vfl.llave like @fondo union all
                select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from v_fondo_llaves vfl where vfl.llave like @fondo union all
                select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from v_fondo_llaves vfl where vfl.llave like @fondo union all
                select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from v_fondo_llaves vfl where vfl.llave like @fondo union all
                select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where po.clv_proyecto_obra like @obra
            ) tabla;
        END;
        
        CREATE PROCEDURE if not exists insert_pp_aplanado(in anio int)
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
        END");

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
        END");

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
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS v_presupuesto_upp_fondo");
        DB::unprepared("DROP PROCEDURE IF EXISTS v_presupuesto_calendarizado");
        DB::unprepared("DROP PROCEDURE IF EXISTS borrado_logico_presupuestos");
        DB::unprepared("DROP PROCEDURE IF EXISTS clave_presupuestal");
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_entidad_ejecutora");
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_AF_EE");
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_claves_descripciones");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_4;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_6;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_7;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_8;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_fondo_mensual;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS calendario_general_upp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS resumen_capitulo_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_3;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_5;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_resumen_por_capitulo_y_partida;");
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_general;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_11_1");
        DB::unprepared("DROP PROCEDURE IF EXISTS art_20_frac_X_b_num_4");
        DB::unprepared("DROP PROCEDURE IF EXISTS art_20_frac_X_b_num_2");
        DB::unprepared("DROP PROCEDURE IF EXISTS art_20_frac_X_b_num_1");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_b_num_1");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_6");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_10_frac_X_a_num_5");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_2");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_III");
    }
};