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
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_1;");
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sapp_ingresos;");

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
    drop temporary table if exists completo;
    drop temporary table if exists epp_parte;

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
    order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida;
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    set @queri := concat(\"
    create temporary table aux_2
    with catal as (
        select *
        from catalogo
        where ejercicio = \",anio,\" and deleted_at is null and grupo_id in (29,30,31,32,33)
    )
    select 
        concat(
            c1.clave,c2.clave,
            c3.clave,c4.clave,c5.clave
        ) clv_partida,
        c4.descripcion partida
    from clasificacion_economica ce
    join catal c1 on ce.capitulo_id = c1.id
    join catal c2 on ce.concepto_id = c2.id
    join catal c3 on ce.partida_generica_id = c3.id
    join catal c4 on ce.partida_especifica_id = c4.id
    join catal c5 on ce.tipo_gasto_id = c5.id
    where ce.ejercicio = \",anio,\" and ce.deleted_at is null
    \");

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    create temporary table completo
    select 
        a1.clv_upp,a1.clv_ur,a1.clv_programa,a1.clv_fondo,a1.clv_partida,a2.partida,
        a1.original,a1.ampliacion,a1.modificado,a1.comprometido,a1.devengado,a1.cumplimiento
    from aux_1 a1
    left join aux_2 a2 on a1.clv_partida = a2.clv_partida;

    create temporary table epp_parte
    select distinct
        clv_upp,upp,
        clv_ur,ur
    from v_epp
    where ejercicio = anio and deleted_at is null;

    select 
        c.clv_upp,c.clv_ur,ep.ur,clv_programa,clv_fondo,clv_partida,partida,
        original,ampliacion,modificado,comprometido,devengado,cumplimiento
    from completo c
    left join epp_parte ep on c.clv_upp = ep.clv_upp and c.clv_ur = ep.clv_ur
    order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida;

    drop temporary table if exists aux_1;
    drop temporary table if exists aux_2;
    drop temporary table if exists completo;
    drop temporary table if exists epp_parte;
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
    select distinct *
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
    with aux as (
    	select *
    	from catalogo 
    	where ejercicio = \",anio,\" and deleted_at is null and grupo_id in (6,16,29,37)
    )
    select 
        clv_upp,c1.descripcion upp,clv_programa,c2.descripcion programa,
        clv_fondo,c3.descripcion fondo,concat(f.clv_capitulo,'000') clv_capitulo,c4.descripcion capitulo,
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
    left join aux c1 on f.clv_upp = c1.clave and c1.grupo_id = 6
    left join aux c2 on f.clv_programa = c2.clave and c2.grupo_id = 16
    left join aux c3 on f.clv_fondo = c3.clave and c3.grupo_id = 37
    left join aux c4 on f.clv_capitulo = c4.clave and c4.grupo_id = 29
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
