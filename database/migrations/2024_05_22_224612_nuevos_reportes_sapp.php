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
        DB::unprepared("DROP PROCEDURE if exists sapp_ingresos;");
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_1;");
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE if exists sapp_reporte_presupuesto_1;");
        DB::unprepared("DROP PROCEDURE if exists reporte_presupuesto_2;");
        DB::unprepared("DROP PROCEDURE if exists sapp_reporte_calendario;");

        DB::unprepared("CREATE PROCEDURE sapp_ingresos(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(2))
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
            set @trimestre := '';
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
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
            if(mes is not null) then
                set @mes := concat(\" and mes = \",mes);
            end if;
            if(trimestre is not null) then
                if(trimestre = 1) then set @trimestre := \" and mes in (1,2,3)\"; end if;
                if(trimestre = 2) then set @trimestre := \" and mes in (4,5,6)\"; end if;
                if(trimestre = 3) then set @trimestre := \" and mes in (7,8,9)\"; end if;
                   if(trimestre = 4) then set @trimestre := \" and mes in (10,11,12)\"; end if;
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
                    where ejercicio = \",anio,\"\",@mes,\"\",@trimestre,\"\",@upp,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"
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

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_1(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(1))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';
             set @mes := '';
            set @trimestre := '';
        
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
            if(mes is not null) then
                set @mes := concat(\" and mes = \",mes);
            end if;
            if(trimestre is not null) then
                if(trimestre = 1) then set @trimestre := \" and mes in (1,2,3)\"; end if;
                if(trimestre = 2) then set @trimestre := \" and mes in (4,5,6)\"; end if;
                if(trimestre = 3) then set @trimestre := \" and mes in (7,8,9)\"; end if;
               if(trimestre = 4) then set @trimestre := \" and mes in (10,11,12)\"; end if;
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
                        else truncate(((sum(devengado)/sum(modificado))*100),2)
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
                    where sm.ejercicio = \",anio,\"\",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"\",@mes,\"\",@trimestre,\"
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
            ORDER BY clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida;
        
            drop temporary table if exists aux_1;
            drop temporary table if exists aux_2;
        end;");

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_2(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(6))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';
            set @mes := '';
            set @trimestre := '';
                
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
            if(mes is not null) then 
                if(upp_v is not null||capitulo_v is not null) then set @mes := concat(\" and mes_n = \",mes);
                else set @mes := concat(\" where mes_n = \",mes);
                end if;
            end if;
            if(trimestre is not null) then
                if(upp_v is not null||capitulo_v is not null) then
                    if(trimestre = 1) then set @trimestre := \" and mes_n in (1,2,3)\"; end if;
                    if(trimestre = 2) then set @trimestre := \" and mes_n in (4,5,6)\"; end if;
                    if(trimestre = 3) then set @trimestre := \" and mes_n in (7,8,9)\"; end if;
                       if(trimestre = 4) then set @trimestre := \" and mes_n in (10,11,12)\"; end if;
               else
                       if(trimestre = 1) then set @trimestre := \" where mes_n in (1,2,3)\"; end if;
                    if(trimestre = 2) then set @trimestre := \" where mes_n in (4,5,6)\"; end if;
                    if(trimestre = 3) then set @trimestre := \" where mes_n in (7,8,9)\"; end if;
                       if(trimestre = 4) then set @trimestre := \" where mes_n in (10,11,12)\"; end if;
               end if;
            end if;
                
            set @queri := concat(\"
            with aux as (
                select 
                    clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,mes_n,actividad,unidad_medida,beneficiarios,
                    mes,programado,realizado,(realizado-programado) diferencia,
                    case
                        when programado = 0 then 100
                        else truncate(((realizado/programado)*100),2)
                    end avance,
                    justificacion,propuesta_mejora
                from (
                    select 
                        ss.clv_upp,ss.clv_ur,ss.clv_programa,m.clv_fondo,rm.partida clv_partida,ss.mes mes_n,
                        case 
                            when m.mir_id is not null then mm.indicador
                            when m.actividad_id is not null and ma.nombre is not null then ma.nombre
                            when m.mir_id is null and ma.nombre is null then c.descripcion
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
                )t \",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"\",@mes,\"\",@trimestre,\"
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

        DB::unprepared("CREATE PROCEDURE sapp_reporte_presupuesto_1(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            set @mes := '';
            set @trimestre := '';
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            if(mes is not null) then 
                set @mes := concat(\" and mes = \",mes);
            end if;
            if(trimestre is not null) then
                if(trimestre = 1) then set @trimestre := \" and mes in (1,2,3)\"; end if;
                if(trimestre = 2) then set @trimestre := \" and mes in (4,5,6)\"; end if;
                if(trimestre = 3) then set @trimestre := \" and mes in (7,8,9)\"; end if;
                   if(trimestre = 4) then set @trimestre := \" and mes in (10,11,12)\"; end if;
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
                    where sm.ejercicio = \",anio,\"\",@upp,\"\",@programa,\"\",@mes,\"\",@trimestre,\"
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
                        when sum(devengado) = 0 then 0
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

        DB::unprepared("CREATE PROCEDURE reporte_presupuesto_2(in anio int,in mes int,in trimestre int,in upp_v varchar(3),in programa_v varchar(2))
        begin
            set @upp := '';
            set @programa := '';
            set @mes := '';
            set @trimestre := '';
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            if(mes is not null) then 
                set @mes := concat(\" and ss.mes = \",mes);
            end if;
            if(trimestre is not null) then
                if(trimestre = 1) then set @trimestre := \" and ss.mes in (1,2,3)\"; end if;
                if(trimestre = 2) then set @trimestre := \" and ss.mes in (4,5,6)\"; end if;
                if(trimestre = 3) then set @trimestre := \" and ss.mes in (7,8,9)\"; end if;
                   if(trimestre = 4) then set @trimestre := \" and ss.mes in (10,11,12)\"; end if;
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
                where ss.ejercicio = \",anio,\" and ss.deleted_at is null\",@upp,\"\",@programa,\"\",@mes,\"\",@trimestre,\"
                group by ss.clv_upp,ss.clv_programa,ss.mes
                order by clv_upp,clv_programa,mes_n,programado
            )t;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
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
        DB::unprepared("DROP PROCEDURE if exists sapp_ingresos;");
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_1;");
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE if exists sapp_reporte_presupuesto_1;");
        DB::unprepared("DROP PROCEDURE if exists reporte_presupuesto_2;");
        DB::unprepared("DROP PROCEDURE if exists sapp_reporte_calendario;");
    }
};
