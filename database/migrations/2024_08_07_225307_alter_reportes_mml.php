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
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_alineacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_presupuesto_egresos;");

        DB::unprepared("CREATE PROCEDURE mml_alineacion(in anio int,in trimestre_n int,in semaforo int,in ver int,in upp_v int)
BEGIN
	drop temporary table if exists plan_desarrollo;
	drop temporary table if exists metas_seguimiento;
	drop temporary table if exists parte_uno;
	drop temporary table if exists parte_dos;
	drop temporary table if exists parte_tres;
	drop temporary table if exists parte_cuatro;
	drop temporary table if exists t_final;

	set @metas := 'metas';
	set @mir := 'mml_mir';
	set @corte := 'deleted_at is null';
	set @v_epp := 'v_epp';
	set @id := 'id';
	if(ver is not null) then 
		set @metas := 'metas_hist';
		set @mir := 'mml_mir_hist';
		set @v_epp := 'v_epp_hist';
		set @id := 'id_original';
		set @corte := concat('version = ',ver);
	end if;

	create temporary table plan_desarrollo
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
		ose.clv_cpladem,
		concat(ose.clv_eje,' ',ose.eje) eje,
		concat(ose.clv_objetivo_sectorial,' ',ose.objetivo_sectorial) objetivo_sectorial,
		concat(eo.clv_estrategia,' ',ose.estrategia) estrategia,
		ose.linea_accion,
		eo.plan_nacional,ods_1,ods_2,ods_3,
		ods_4,ods_5,ods_6,ods_7,ods_8
	from (
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
		from aux
	) eo
	left join (
		select 
			c1.clave clv_eje,c1.descripcion eje,
			c2.clave clv_objetivo_sectorial,c2.descripcion objetivo_sectorial,
			c3.clave clv_estrategia,c3.descripcion estrategia,
			case 
				when substr(c4.descripcion,8,1) = '.' then substr(c4.descripcion,1,7)
				when substr(c4.descripcion,8,1) = ' ' then substr(c4.descripcion,1,7)
				else substr(c4.descripcion,1,8)
			end clv_cpladem,
			c4.clave clv_linea_accion,c4.descripcion linea_accion
		from pladiem p
		join catalogo c1 on p.eje_id = c1.id
		join catalogo c2 on p.objetivo_sectorial_id = c2.id
		join catalogo c3 on p.estrategia_id = c3.id
		join catalogo c4 on p.linea_accion_id = c4.id
		where p.ejercicio = anio
	) ose on eo.clv_estrategia = ose.clv_estrategia;

	set @trimestre := \"\";
	if(trimestre_n = 1) then set @trimestre := \"and mes in (1,2,3)\"; end if;
	if(trimestre_n = 2) then set @trimestre := \"and mes in (4,5,6)\"; end if;
	if(trimestre_n = 3) then set @trimestre := \"and mes in (7,8,9)\"; end if;
	if(trimestre_n = 4) then set @trimestre := \"and mes in (10,11,12)\"; end if;

	set @metas_trimestres := \"\";
	if(trimestre_n = 1) then set @metas_trimestres := \"(enero+febrero+marzo)\"; end if;
	if(trimestre_n = 2) then set @metas_trimestres := \"(abril+mayo+junio)\"; end if;
	if(trimestre_n = 3) then set @metas_trimestres := \"(julio+agosto+septiembre)\"; end if;
	if(trimestre_n = 4) then set @metas_trimestres := \"(octubre+noviembre+diciembre)\"; end if;
	if(trimestre_n is null) then set @metas_trimestres := \"(total)\"; end if;

	set @query := concat(\"
	create temporary table metas_seguimiento
	select
		m.mir_id,
		sum(total) total,
		case 
			when sum(realizado) is null then 0
			else sum(realizado)
		end realizado
	from (
		select 
			\",@id,\" id,mir_id,
			\",@metas_trimestres,\" total
		from \",@metas,\"
		where  ejercicio = \",anio,\" and \",@corte,\" and mir_id is not null
	) m
	left join (
		select meta_id,sum(realizado) realizado from sapp_seguimiento
		where ejercicio = \",anio,\" and deleted_at is null \",@trimestre,\"
		group by meta_id
	) ss on ss.meta_id = m.id
	group by mir_id;
	\");

	prepare stmt from @query;
	execute stmt;
	deallocate prepare stmt;

	set @semaforo := \"\";
	if(semaforo = 0) then set @semaforo := concat(\"where avance <= 60\"); end if;
	if(semaforo = 1) then set @semaforo := concat(\"where avance > 60 and avance <= 94\"); end if;
	if(semaforo = 2) then set @semaforo := concat(\"where avance > 94 and avance <= 110\"); end if;
	if(semaforo = 3) then set @semaforo := concat(\"where avance > 110\"); end if;

	set @query := concat(\"
	create temporary table parte_uno
	with aux as (
		select 
			t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,dimension,descripcion_metodo,medios_verificacion,supuestos,total,avance
		from (
			select 
				mm.clv_upp,
				mm.clv_pp,
				mm.clv_ur,
				substr(mm.area_funcional,5,2) clv_cpladem,
				mm.componente_padre,
				mm.nivel,
				'Actividad' tipo_indicador,
				mm.objetivo,
				mm.indicador,
				mm.definicion_indicador,
				mm.metodo_calculo,
				mm.frecuencia_medicion,
				mm.dimension,
				mm.descripcion_metodo,
				mm.medios_verificacion,
				mm.supuestos,
				sum(ms.total) total,
				case
					when sum(ms.realizado) is null then 0
					when sum(ms.realizado) = 0 then 0
					when sum(ms.total) = 0 then 100
					when sum(ms.realizado) > 0 
					then truncate(((sum(ms.realizado)/sum(ms.total))*100),2)
				end avance
			from \",@mir,\" mm 
			join metas_seguimiento ms on ms.mir_id = mm.\",@id,\"
			where mm.\",@corte,\" and mm.ejercicio = \",anio,\" and mm.nivel = 11 and mm.\",@corte,\"
			group by clv_upp,clv_pp,clv_ur,nivel,objetivo,indicador,
			definicion_indicador,metodo_calculo,descripcion_metodo,medios_verificacion
		)t
		join (
			select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from \",@v_epp,\"
			where ejercicio = \",anio,\" and \",@corte,\"
		)ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur
	)
	select *
	from aux \",@semaforo,\";
	\");

	prepare stmt from @query;
	execute stmt;
	deallocate prepare stmt;

	set @query := concat(\"
	create temporary table parte_dos
	select 
		t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,dimension,descripcion_metodo,medios_verificacion,supuestos,total,avance
	from (
		select 
			mm.clv_upp,
			mm.clv_pp,
			mm.clv_ur,
			substr(mm.area_funcional,5,2) clv_cpladem,
			mm.\",@id,\" componente_padre,
			mm.nivel,
			'Componente' tipo_indicador,
			mm.objetivo,
			mm.indicador,
			mm.definicion_indicador,
			mm.metodo_calculo,
			mm.frecuencia_medicion,
			mm.dimension,
			mm.descripcion_metodo,
			mm.medios_verificacion,
			mm.supuestos,
			0 total,
			0 avance
		from \",@mir,\" mm
		where \",@id,\" in (
			select distinct
				componente_padre
			from parte_uno
		)
	)t
	join (
		select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from \",@v_epp,\"
		where ejercicio = \",anio,\" and \",@corte,\"
	)ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur;
	\");

	prepare stmt from @query;
	execute stmt;
	deallocate prepare stmt;

	set @query := concat(\"
	create temporary table parte_tres
	select 
		t.clv_upp,ve.upp,t.clv_pp,ve.programa,'' clv_ur,'' ur,clv_cpladem,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,dimension,descripcion_metodo,medios_verificacion,supuestos,total,avance
	from (
		select 
			clv_upp,
			clv_pp,
			clv_ur,
			'' clv_cpladem,
			componente_padre,
			nivel,
			case 
				when nivel = 8 then 'Fin'
				when nivel = 9 then 'Proposito'
			end tipo_indicador,
			objetivo,
			indicador,
			definicion_indicador,
			metodo_calculo,
			frecuencia_medicion,
			dimension,
			descripcion_metodo,
			medios_verificacion,
			supuestos,
			0 total,
			0 avance
		from \",@mir,\"
		where ejercicio = \",anio,\" and \",@corte,\" and nivel in (8,9)
	)t
	join (
		select distinct clv_upp,upp,clv_programa clv_pp,programa from \",@v_epp,\"
		where ejercicio = \",anio,\" and \",@corte,\"
	)ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp;
	\");

	prepare stmt from @query;
	execute stmt;
	deallocate prepare stmt;

	alter table parte_uno convert to character set utf8 collate utf8_unicode_ci;
	alter table parte_dos convert to character set utf8 collate utf8_unicode_ci;
	alter table parte_tres convert to character set utf8 collate utf8_unicode_ci;

	create temporary table parte_cuatro
	with aux as (
		select * from parte_uno
		union all
		select * from parte_dos
		union all
		select * from parte_tres
	)
	select 
		clv_upp,upp,clv_pp,programa,clv_ur,ur,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,
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
		case 
			when dimension = 21 then 'Eficacia'
			when dimension = 22 then 'Eficiencia'
			when dimension = 23 then 'Calidad'
			when dimension = 24 then 'Economía'
		end dimension,
		descripcion_metodo,medios_verificacion,supuestos,total,avance,ca.clv_cpladem
	from aux a 
	left join (
		select 
			clave,
			case 
				when substr(descripcion,8,1) = '.' then substr(descripcion,1,7)
				when substr(descripcion,8,1) = ' ' then substr(descripcion,1,7)
				else substr(descripcion,1,8)
			end clv_cpladem
		from catalogo
		where ejercicio = anio and deleted_at is null and grupo_id = 13
	) ca on a.clv_cpladem = ca.clave
	order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

	create temporary table t_final
	select 
		clv_upp,upp,clv_pp clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,componente_padre padre,objetivo,indicador nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,
		frecuencia_medicion,
		'' unidad_medida,
		dimension,
		medios_verificacion,supuestos,total programado,avance,
		case 
			when avance <= 60 then 0
			when avance > 60 and avance <= 94 then 1
			when avance > 94 and avance <= 110 then 2
			when avance > 110 then 3
		end color,
		eje,objetivo_sectorial,estrategia,linea_accion,plan_nacional,ods_1,ods_2,ods_3,ods_4,ods_5,ods_6,ods_7,ods_8
	from parte_cuatro pc
	left join plan_desarrollo pd on pc.clv_cpladem = pd.clv_cpladem
	order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

	if(upp_v is null) then 
		select * from t_final;
	else
		select * from t_final where clv_upp = upp_v;
	end if;

	drop temporary table if exists plan_desarrollo;
	drop temporary table if exists metas_seguimiento;
	drop temporary table if exists parte_uno;
	drop temporary table if exists parte_dos;
	drop temporary table if exists parte_tres;
	drop temporary table if exists parte_cuatro;
	drop temporary table if exists t_final;
END;");

        DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int,in ver int,in upp_v varchar(3))
BEGIN
	drop temporary table if exists metas_seguimiento;
	drop temporary table if exists parte_uno;
	drop temporary table if exists parte_dos;
 	drop temporary table if exists parte_tres;
	drop temporary table if exists parte_cuatro;
	drop temporary table if exists t_final;

    set @trimestre := \"\";
    if(trimestre_n = 1) then set @trimestre := \"and mes in (1,2,3)\"; end if;
    if(trimestre_n = 2) then set @trimestre := \"and mes in (4,5,6)\"; end if;
    if(trimestre_n = 3) then set @trimestre := \"and mes in (7,8,9)\"; end if;
    if(trimestre_n = 4) then set @trimestre := \"and mes in (10,11,12)\"; end if;

    set @metas_trimestres := \"\";
    if(trimestre_n = 1) then set @metas_trimestres := \"(enero+febrero+marzo)\"; end if;
    if(trimestre_n = 2) then set @metas_trimestres := \"(abril+mayo+junio)\"; end if;
    if(trimestre_n = 3) then set @metas_trimestres := \"(julio+agosto+septiembre)\"; end if;
    if(trimestre_n = 4) then set @metas_trimestres := \"(octubre+noviembre+diciembre)\"; end if;
    if(trimestre_n is null) then set @metas_trimestres := \"(total)\"; end if;
   
    set @metas := 'metas';
	set @mir := 'mml_mir';
	set @corte := 'deleted_at is null';
	set @v_epp := 'v_epp';
	set @id := 'id';
	if(ver is not null) then 
		set @metas := 'metas_hist';
		set @mir := 'mml_mir_hist';
		set @v_epp := 'v_epp_hist';
		set @id := 'id_original';
		set @corte := concat('version = ',ver);
	end if;

    set @query := concat(\"
    create temporary table metas_seguimiento
    select
        m.mir_id,
        unidad_medida,
        sum(total) total,
        case 
            when sum(realizado) is null then 0
            else sum(realizado)
        end trimestre
    from (
        select 
            m.\",@id,\" id,mir_id,
            um.unidad_medida,
            \",@metas_trimestres,\" total
        from \",@metas,\" m
        join unidades_medida um on m.unidad_medida_id = um.id
        where m.ejercicio = \",anio,\" and m.\",@corte,\" and m.mir_id is not null
    ) m
    left join (
        select meta_id,sum(realizado) realizado from sapp_seguimiento
        where ejercicio = \",anio,\" and deleted_at is null \",@trimestre,\"
        group by meta_id
    ) ss on ss.meta_id = m.id
    group by mir_id;
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @semaforo := \"\";
    if(semaforo = 0) then set @semaforo := concat(\"where avance <= 60\"); end if;
    if(semaforo = 1) then set @semaforo := concat(\"where avance > 60 and avance <= 94\"); end if;
    if(semaforo = 2) then set @semaforo := concat(\"where avance > 94 and avance <= 110\"); end if;
    if(semaforo = 3) then set @semaforo := concat(\"where avance > 110\"); end if;

    set @query := concat(\"
    create temporary table parte_uno
    with aux as (
        select 
            t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
            objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,supuestos,total,trimestre,avance
        from (
            select 
                mm.clv_upp,
                mm.clv_pp,
                mm.clv_ur,
                substr(mm.area_funcional,5,2) clv_cpladem,
                mm.componente_padre,
                mm.nivel,
                'Actividad' tipo_indicador,
                mm.objetivo,
                mm.indicador,
                mm.definicion_indicador,
                mm.metodo_calculo,
                mm.frecuencia_medicion,
                ms.unidad_medida,
                mm.dimension,
                mm.descripcion_metodo,
                mm.medios_verificacion,
                mm.supuestos,
                sum(ms.total) total,
                case 
                    when sum(ms.trimestre) is null then 0
                    else sum(trimestre)
                end trimestre,
                case
                    when sum(ms.trimestre) is null then 0
                    when sum(ms.trimestre) = 0 then 0
                    when sum(ms.total) = 0 then 100
                    when sum(ms.trimestre) > 0 
                    then truncate(((sum(ms.trimestre)/sum(ms.total))*100),2)
                end avance
            from \",@mir,\" mm 
            join metas_seguimiento ms on ms.mir_id = mm.\",@id,\"
            where mm.\",@corte,\" and mm.ejercicio = \",anio,\" and mm.nivel = 11
            group by clv_upp,clv_pp,clv_ur,nivel,objetivo,indicador,
            definicion_indicador,metodo_calculo,descripcion_metodo,medios_verificacion
        )t
        join (
            select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from \",@v_epp,\"
            where ejercicio = \",anio,\" and \",@corte,\"
        )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur
    )
    select *
    from aux \",@semaforo,\";
    \");

    prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat(\"
    create temporary table parte_dos
    select 
        t.clv_upp,ve.upp,t.clv_pp,ve.programa,t.clv_ur,ve.ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
        objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,supuestos,total,0 trimestre,avance
    from (
        select 
            mm.clv_upp,
            mm.clv_pp,
            mm.clv_ur,
            substr(mm.area_funcional,5,2) clv_cpladem,
            mm.\",@id,\" componente_padre,
            mm.nivel,
            'Componente' tipo_indicador,
            mm.objetivo,
            mm.indicador,
            mm.definicion_indicador,
            mm.metodo_calculo,
            mm.frecuencia_medicion,
            '' unidad_medida,
            mm.dimension,
            mm.descripcion_metodo,
            mm.medios_verificacion,
            mm.supuestos,
            0 total,
            0 trimestre,
            0 avance
        from \",@mir,\" mm
        where \",@id,\" in (
            select distinct
                componente_padre
            from parte_uno
        )
    )t
    join (
        select distinct clv_upp,upp,clv_programa clv_pp,programa,clv_ur,ur from \",@v_epp,\"
        where ejercicio = \",anio,\" and \",@corte,\"
    )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp and t.clv_ur = ve.clv_ur;
	 \");
   
	 prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    set @query := concat(\"
    create temporary table parte_tres
    select 
        t.clv_upp,ve.upp,t.clv_pp,ve.programa,'' clv_ur,'' ur,clv_cpladem,componente_padre,nivel,tipo_indicador,
        objetivo,indicador,definicion_indicador,metodo_calculo,frecuencia_medicion,unidad_medida,dimension,descripcion_metodo,medios_verificacion,supuestos,total,0 trimestre,avance
    from (
        select 
            clv_upp,
            clv_pp,
            clv_ur,
            '' clv_cpladem,
            componente_padre,
            nivel,
            case 
                when nivel = 8 then 'Fin'
                when nivel = 9 then 'Proposito'
            end tipo_indicador,
            objetivo,
            indicador,
            definicion_indicador,
            metodo_calculo,
            frecuencia_medicion,
            unidad_medida,
            dimension,
            descripcion_metodo,
            medios_verificacion,
            supuestos,
            0 total,
            0 trimestre,
            0 avance
        from \",@mir,\"
        where ejercicio = \",anio,\" and \",@corte,\" and nivel in (8,9)
    )t
    join (
        select distinct clv_upp,upp,clv_programa clv_pp,programa from \",@v_epp,\"
        where ejercicio = \",anio,\" and \",@corte,\"
    )ve on t.clv_upp = ve.clv_upp and t.clv_pp = ve.clv_pp;
	 \");
   
	 prepare stmt from @query;
    execute stmt;
    deallocate prepare stmt;

    alter table parte_uno convert to character set utf8 collate utf8_unicode_ci;
    alter table parte_dos convert to character set utf8 collate utf8_unicode_ci;
    alter table parte_tres convert to character set utf8 collate utf8_unicode_ci;

    create temporary table parte_cuatro
    with aux as (
        select * from parte_uno
        union all
        select * from parte_dos
        union all
        select * from parte_tres
    )
    select 
        clv_upp,upp,clv_pp,programa,clv_ur,ur,componente_padre,nivel,tipo_indicador,objetivo,indicador,definicion_indicador,metodo_calculo,
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
        case
            when nivel = 11 then unidad_medida
            else '' 
        end unidad_medida,
        case 
            when dimension = 21 then 'Eficacia'
            when dimension = 22 then 'Eficiencia'
            when dimension = 23 then 'Calidad'
            when dimension = 24 then 'Economía'
        end dimension,
        descripcion_metodo,medios_verificacion,supuestos,total meta_anual,trimestre,avance,ca.clv_cpladem
    from aux a 
    left join (
        select 
            clave,
            case 
                when substr(descripcion,8,1) = '.' then substr(descripcion,1,7)
                when substr(descripcion,8,1) = ' ' then substr(descripcion,1,7)
                else substr(descripcion,1,8)
            end clv_cpladem
        from catalogo
        where ejercicio = anio and deleted_at is null and grupo_id = 13
    ) ca on a.clv_cpladem = ca.clave
    order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

    create temporary table t_final
    select 
        clv_upp,upp,clv_pp clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,componente_padre padre,objetivo resumen_narrativo,indicador nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,
        frecuencia_medicion,
        unidad_medida,
        dimension,
        medios_verificacion,supuestos,meta_anual,trimestre,avance,
        case 
            when avance <= 60 then 0
            when avance > 60 and avance <= 94 then 1
            when avance > 94 and avance <= 110 then 2
            when avance > 110 then 3
        end color
    from parte_cuatro pc
    order by clv_upp,clv_pp,clv_ur,componente_padre,nivel;

    if(upp_v is null) then 
        select * from t_final;
    else
        select * from t_final where clv_upp = upp_v;
    end if;

    drop temporary table if exists metas_seguimiento;
    drop temporary table if exists parte_uno;
    drop temporary table if exists parte_dos;
    drop temporary table if exists parte_tres;
    drop temporary table if exists parte_cuatro;
    drop temporary table if exists t_final;
END;");

        DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int,in ver int)
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
    set @v_epp := 'v_epp';
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
    if(ver is not null) then 
        set @corte := CONCAT('version = ',ver);
        set @v_epp := 'v_epp_hist';
        set @catalogo := 'catalogo_hist';
        set @id := 'id_original';
        set @mir := 'mml_mir_hist';
    end if;

    set @queri := concat(\"
    CREATE TEMPORARY TABLE epp_t
    select 
        id,clv_upp,clv_ur,clv_programa clv_pp,proyecto,
        concat(
            clv_finalidad,clv_funcion,clv_subfuncion,clv_eje,
            clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,
            clv_programa,clv_subprograma,clv_proyecto
        ) area_funcional
    from \",@v_epp,\"
    where ejercicio = \",anio,\" and \",@corte,\";
    \");
        
    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;
    
    set @upp_n := \"\";
    if(upp is not null) then
      set @upp_n := concat(\"
         select distinct
             upp,'' clv_upp,'' clv_pp,'' clv_ur,'' area_funcional,
             '' nombre_proyecto,'' nivel,'' objetivo,'' indicador
         from \",@v_epp,\" 
         where ejercicio = \",anio,\" and clv_upp = '\",upp,\"' and \",@corte,\"
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
            join epp_t ve on ve.id = mm.id_epp
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
            join epp_t ve on ve.id = mm.id_epp
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

        DB::unprepared("CREATE PROCEDURE mml_presupuesto_egresos(in anio int,in upp_v varchar(3),in ur_v varchar(2),in pp_v varchar(2),in eje_v varchar(1),in ver int)
BEGIN
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

    set @corte := 'deleted_at is null';
    set @id := 'id';
    set @epp := 'v_epp';
    set @nombre := '                when m.actividad_id is not null and ma.id_catalogo is null then ma.nombre
                when m.actividad_id is not null and ma.id_catalogo is not null then c.descripcion';
    if(ver is not null) then 
        set @corte := concat('version = ',ver);
        set @id := 'id_original';
        set @epp := 'v_epp_hist';
        set @nombre := 'else ma.nombre';
    end if;

    set @from := concat(\"
        from metas m
        left join mml_mir mm on m.mir_id = mm.id
        left join mml_actividades ma on m.actividad_id = ma.id
        left join catalogo c on ma.id_catalogo = c.id
        left join unidades_medida um on m.unidad_medida_id = um.id
        left join beneficiarios b on m.beneficiario_id = b.id
        where m.ejercicio = \",anio,\" and m.\",@corte,\"
    \");

    if(ver is not null) then
    set @from := concat(\"
        from metas_hist m
        left join mml_mir_hist mm on m.mir_id = mm.id_original
        left join mml_actividades_hist ma on m.actividad_id = ma.id_original
        where m.ejercicio = \",anio,\" and m.\",@corte,\"
    \");
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
                \",@nombre,\"
            end actividad,
            m.total programado_anual,
            \",if(ver is null,\"um.unidad_medida,\",\"m.unidad_medida,\"),\"
            m.cantidad_beneficiarios,
            beneficiario
        \",@from,\"
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
    from \",@epp,\"
    where ejercicio = \",anio,\" and \",@corte,\"
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
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_alineacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_matrices_indicadores;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_presupuesto_egresos;");
    }
};
