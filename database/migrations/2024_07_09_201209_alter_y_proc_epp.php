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
        DB::unprepared("DROP PROCEDURE IF EXISTS insert_v_epp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS llenar_v_epp;");

        Schema::table('epp', function (Blueprint $table) {
            $table->integer('upp_id')->nullable(true)->default(null)->change();
        });

        DB::unprepared("CREATE PROCEDURE llenar_v_epp(in upp_v varchar(3))
BEGIN
    set @anio_old := (select (max(ejercicio)-1) from catalogo where deleted_at is null);
    set @anio_new := (select max(ejercicio) from catalogo where deleted_at is null);
    set @query1 := concat(\"set @id := (select id from catalogo where ejercicio = \",@anio_old,\" and deleted_at is null and clave = \",upp_v,\" and grupo_id = 6);\");
    set @query2 := concat(\"set @id_upp_new := (select id from catalogo where ejercicio = \",@anio_new,\" and deleted_at is null and clave = \",upp_v,\" and grupo_id = 6);\");
    set @id_ca := (select max(id) from clasificacion_administrativa where ejercicio = @anio_old and deleted_at is null);
    set @id_ee := (select max(id) from entidad_ejecutora where ejercicio = @anio_old and deleted_at is null);
    set @id_cf := (select max(id) from clasificacion_funcional where ejercicio = @anio_old and deleted_at is null);
    set @id_p := (select max(id) from pladiem where ejercicio = @anio_old and deleted_at is null);
    set @id_c := (select max(id) from conac where ejercicio = @anio_old and deleted_at is null);
    set @id_e := (select max(id) from epp);
    set @created := now();
    drop temporary table if exists catalogo_combinado;
    drop temporary table if exists administrativa_temporal;
    drop temporary table if exists ejecutora_temporal;
    drop temporary table if exists funcional_temporal;
    drop temporary table if exists pladiem_temporal;
    drop temporary table if exists conac_temporal;

    prepare stmt from @query1;
	execute stmt;
	deallocate prepare stmt;

    prepare stmt from @query2;
	execute stmt;
	deallocate prepare stmt;

    create temporary table catalogo_combinado
    with 
    catalogo_old as (
        select * from catalogo c 
        where ejercicio = @anio_old and deleted_at is null and 
        grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,26,27,28)
    ),
    catalogo_new as (
        select * from catalogo c 
        where ejercicio = @anio_new and deleted_at is null and 
        grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,26,27,28)
    )
    select
        co.id id_old,
        cn.id id_new,
        cn.ejercicio,
        cn.grupo_id,
        cn.clave,
        cn.descripcion
    from catalogo_old co
    join catalogo_new cn on co.grupo_id = cn.grupo_id and 
    co.clave = cn.clave and co.descripcion = cn.descripcion and 
    co.descripcion_larga = cn.descripcion_larga and 
    co.descripcion_corta = cn.descripcion_corta;

    create temporary table administrativa_temporal
    with aux as (
        select *
        from catalogo_combinado
        where grupo_id in (1,2,3,4,5)
    )
    select 
        ca.id id_old,
        (@id_ca:=@id_ca+1) id_new,
        c1.id_new sector_publico_id,
        c2.id_new sector_publico_f_id,
        c3.id_new sector_economia_id,
        c4.id_new subsector_economia_id,
        c5.id_new ente_publico_id
    from clasificacion_administrativa ca
    join aux c1 on ca.sector_publico_id = c1.id_old
    join aux c2 on ca.sector_publico_f_id = c2.id_old
    join aux c3 on ca.sector_economia_id = c3.id_old
    join aux c4 on ca.subsector_economia_id = c4.id_old
    join aux c5 on ca.ente_publico_id = c5.id_old
    where ca.ejercicio = @anio_old and ca.deleted_at is null;

    create temporary table ejecutora_temporal
    with aux as (
        select *
        from catalogo_combinado
        where grupo_id in (6,7,8)
    )
    select 
        ee.id id_old,
        (@id_ee:=@id_ee+1) id_new,
        c1.id_new upp_id,
        c2.id_new subsecretaria_id,
        c3.id_new ur_id
    from entidad_ejecutora ee
    join aux c1 on ee.upp_id = c1.id_old
    join aux c2 on ee.subsecretaria_id = c2.id_old
    join aux c3 on ee.ur_id = c3.id_old
    where ee.ejercicio = @anio_old and ee.deleted_at is null;

    create temporary table funcional_temporal
    with aux as (
        select *
        from catalogo_combinado
        where grupo_id in (9,10,11)
    )
    select 
        cf.id id_old,
        (@id_cf:=@id_cf+1) id_new,
        c1.id_new finalidad_id,
        c2.id_new funcion_id,
        c3.id_new subfuncion_id
    from clasificacion_funcional cf
    join aux c1 on cf.finalidad_id = c1.id_old
    join aux c2 on cf.funcion_id = c2.id_old
    join aux c3 on cf.subfuncion_id = c3.id_old
    where cf.ejercicio = @anio_old and cf.deleted_at is null;

    create temporary table pladiem_temporal
    with aux as (
        select *
        from catalogo_combinado
        where grupo_id in (12,26,27,13,14)
    )
    select 
        p.id id_old,
        (@id_p:=@id_p+1) id_new,
        c1.id_new eje_id,
        c2.id_new objetivo_sectorial_id,
        c3.id_new estrategia_id,
        c4.id_new linea_accion_id,
        c5.id_new programa_sectorial_id
    from pladiem p
    join aux c1 on p.eje_id = c1.id_old 
    join aux c2 on p.objetivo_sectorial_id = c2.id_old
    join aux c3 on p.estrategia_id = c3.id_old
    join aux c4 on p.linea_accion_id = c4.id_old
    join aux c5 on p.programa_sectorial_id = c5.id_old
    where p.ejercicio = @anio_old and p.deleted_at is null;

    create temporary table conac_temporal
    with aux as (
        select *
        from catalogo_combinado
        where grupo_id in (28,15)
    )
    select 
        c.id id_old,
        (@id_c:=@id_c+1) id_new,
        c1.id_new padre_id,
        c2.id_new tipologia_conac_id
    from conac c
    left join aux c1 on c.padre_id = c1.id_old
    left join aux c2 on c.tipologia_conac_id = c2.id_old
    where c.ejercicio = @anio_old and c.deleted_at is null;

    insert into clasificacion_administrativa(
        id,ejercicio,
        sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,
        created_at,updated_at,created_user
    )
    select 
        id_new,@anio_new ejercicio,
        sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,
        @created,@created,'ADMIN'
    from administrativa_temporal;

    insert into entidad_ejecutora(
        id,ejercicio,upp_id,subsecretaria_id,ur_id,estatus,created_at,updated_at,created_user
    )
    select 
        id_new,@anio_new ejercicio,upp_id,subsecretaria_id,ur_id,0,@created,@updated,'ADMIN'
    from ejecutora_temporal;

    insert into clasificacion_funcional(
        id,ejercicio,finalidad_id,funcion_id,subfuncion_id,estatus,created_at,updated_at,created_user
    )
    select 
        id_new,@anio_new,finalidad_id,funcion_id,subfuncion_id,0,@created,@updated,'ADMIN'
    from funcional_temporal;

    insert into pladiem(
        id,ejercicio,
        eje_id,objetivo_sectorial_id,estrategia_id,linea_accion_id,programa_sectorial_id,
        estatus,created_at,updated_at,created_user
    )
    select 
        id_new,@anio_new,
        eje_id,objetivo_sectorial_id,estrategia_id,linea_accion_id,programa_sectorial_id,
        0,@created,@updated,'ADMIN'
    from pladiem_temporal;

    insert into conac(
        id,ejercicio,padre_id,tipologia_conac_id,estatus,created_at,updated_at,created_user
    )
    select 
        id_new,@anio_new,padre_id,tipologia_conac_id,0 estatus,@created,@updated,'ADMIN'
    from conac_temporal;

    alter table administrativa_temporal add primary key (id_old,id_new);
	alter table ejecutora_temporal add primary key (id_old,id_new);
	alter table funcional_temporal add primary key (id_old,id_new);
	alter table pladiem_temporal add primary key (id_old,id_new);
	alter table conac_temporal add primary key (id_old,id_new);

    insert into epp(
        id,ejercicio,mes_i,mes_f,upp_id,
        clasificacion_administrativa_id,
        entidad_ejecutora_id,clasificacion_funcional_id,
        pladiem_id,conac_id,programa_id,subprograma_id,proyecto_id,
        estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,
        created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    with aux as (
        select * from catalogo_combinado
        where grupo_id in (16,17,18)
    )
    select 
        (@id_e:=@id_e+1) id,@anio_new ejercicio,1 mes_i,12 mes_f,@id_upp_new upp_id,
        ad.id_new at_id,
        et.id_new et_id,
        ft.id_new ft_id,
        pt.id_new p_id,
        ct.id_new c_id,
        c1.id_new c1_id,
        c2.id_new c2_id,
        c3.id_new c3_id,
        0 estatus,e.presupuestable,e.con_mir,e.confirmado,e.tipo_presupuesto,
        @created created_at,@created updated_at,null deleted_at,
        'ADMIN' created_user,null updated_user,null deleted_user
    from (
    	select * from epp
    	where ejercicio = @anio_old and deleted_at is null and upp_id = @id
    ) e
    join administrativa_temporal ad on e.clasificacion_administrativa_id = ad.id_old
    join ejecutora_temporal et on e.entidad_ejecutora_id = et.id_old
    join funcional_temporal ft on e.clasificacion_funcional_id = ft.id_old
    join pladiem_temporal pt on e.pladiem_id = pt.id_old
    join conac_temporal ct on e.conac_id = ct.id_old
    join aux c1 on e.programa_id = c1.id_old and c1.grupo_id = 16
    join aux c2 on e.subprograma_id = c2.id_old and c2.grupo_id = 17
    join aux c3 on e.proyecto_id = c3.id_old and c3.grupo_id = 18;

    update entidad_ejecutora
    set estatus = 4
    where id in (
        select distinct entidad_ejecutora_id from epp
        where ejercicio = @anio_new and deleted_at is null
    );

    update clasificacion_funcional
    set estatus = 1
    where id in (
        select distinct clasificacion_funcional_id from epp
        where ejercicio = @anio_new and deleted_at is null
    );

    update pladiem
    set estatus = 1
    where id in (
        select distinct pladiem_id from epp
        where ejercicio = @anio_new and deleted_at is null
    );

    update conac
    set estatus = 1
    where id in (
        select distinct conac_id from epp
        where ejercicio = @anio_new and deleted_at is null
    );

    insert into v_epp(
        id,
        clv_sector_publico,sector_publico,clv_sector_publico_f,sector_publico_f,clv_sector_economia,sector_economia,clv_subsector_economia,subsector_economia,clv_ente_publico,ente_publico,
        clv_upp,upp,clv_subsecretaria,subsecretaria,clv_ur,ur,
        clv_finalidad,finalidad,clv_funcion,funcion,clv_subfuncion,subfuncion,
        clv_eje,eje,clv_linea_accion,linea_accion,clv_programa_sectorial,programa_sectorial,
        clv_tipologia_conac,tipologia_conac,
        clv_programa,programa,clv_subprograma,subprograma,clv_proyecto,proyecto,presupuestable,
        con_mir,confirmado,tipo_presupuesto,ejercicio,deleted_at,updated_at,created_at,
        created_user,updated_user,deleted_user
    )
	with aux as (
		select * from catalogo
		where ejercicio = @anio_new and deleted_at is null and 
		grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,26,27,28)
	)
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
	from (
		select * from epp
		where ejercicio = @anio_new
	) e
	join clasificacion_administrativa ca on e.clasificacion_administrativa_id = ca.id
	join entidad_ejecutora ee on e.entidad_ejecutora_id = ee.id
	join clasificacion_funcional cf on e.clasificacion_funcional_id = cf.id
	join pladiem p on e.pladiem_id = p.id
	join conac c on e.conac_id = c.id
	join aux c01 on ca.sector_publico_id = c01.id 
	join aux c02 on ca.sector_publico_f_id = c02.id 
	join aux c03 on ca.sector_economia_id = c03.id 
	join aux c04 on ca.subsector_economia_id = c04.id 
	join aux c05 on ca.ente_publico_id = c05.id 
	join aux c06 on ee.upp_id = c06.id 
	join aux c07 on ee.subsecretaria_id = c07.id  
	join aux c08 on ee.ur_id = c08.id 
	join aux c09 on cf.finalidad_id = c09.id 
	join aux c10 on cf.funcion_id = c10.id 
	join aux c11 on cf.subfuncion_id = c11.id 
	join aux c12 on p.eje_id = c12.id 
	join aux c13 on p.linea_accion_id = c13.id 
	join aux c14 on p.programa_sectorial_id = c14.id 
	join aux c15 on c.tipologia_conac_id = c15.id 
	join aux c16 on e.programa_id = c16.id 
	join aux c17 on e.subprograma_id = c17.id 
	join aux c18 on e.proyecto_id = c18.id
    order by e.id;

    drop temporary table if exists catalogo_combinado;
    drop temporary table if exists administrativa_temporal;
    drop temporary table if exists ejecutora_temporal;
    drop temporary table if exists funcional_temporal;
    drop temporary table if exists pladiem_temporal;
    drop temporary table if exists conac_temporal;
END;");

        DB::unprepared("CREATE PROCEDURE insert_v_epp(in id_n_epp int,in accion tinyint)
BEGIN
    #Insert: accion = 0
    #Update o Delete: accion = 1
    drop temporary table if exists epp_descripciones;
    set @id := id_n_epp;
    set @anio := (select ejercicio from epp where id = @id);

    create temporary table epp_descripciones
    with aux as (
        select * from catalogo
        where ejercicio = @anio and deleted_at is null and
        grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18)
    )
    select 
        e.id,
        e.ejercicio,
        e.mes_i,e.mes_f,
        c01.clave clv_01, c01.descripcion desc_01,
        c02.clave clv_02, c02.descripcion desc_02,
        c03.clave clv_03, c03.descripcion desc_03,
        c04.clave clv_04, c04.descripcion desc_04,
        c05.clave clv_05, c05.descripcion desc_05,
        c06.clave clv_06, c06.descripcion desc_06,
        c07.clave clv_07, c07.descripcion desc_07,
        c08.clave clv_08, c08.descripcion desc_08,
        c09.clave clv_09, c09.descripcion desc_09,
        c10.clave clv_10, c10.descripcion desc_10,
        c11.clave clv_11, c11.descripcion desc_11,
        c12.clave clv_12, c12.descripcion desc_12,
        c13.clave clv_13, c13.descripcion desc_13,
        c14.clave clv_14, c14.descripcion desc_14,
        c15.clave clv_15, c15.descripcion desc_15,
        c16.clave clv_16, c16.descripcion desc_16,
        c17.clave clv_17, c17.descripcion desc_17,
        c18.clave clv_18, c18.descripcion desc_18,
        e.estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,
        now() created_at,now() updated_at,e.deleted_at,
        e.created_user,e.updated_user,e.deleted_user
    from (
        select * from epp
        where id = @id
    ) e
    join clasificacion_administrativa ca on e.clasificacion_administrativa_id = ca.id
    join entidad_ejecutora ee on e.entidad_ejecutora_id = ee.id
    join clasificacion_funcional cf on e.clasificacion_funcional_id = cf.id
    join pladiem p on e.pladiem_id = p.id
    join conac c on e.conac_id = c.id
    join aux c01 on ca.sector_publico_id = c01.id
    join aux c02 on ca.sector_publico_f_id = c02.id
    join aux c03 on ca.sector_economia_id = c03.id
    join aux c04 on ca.subsector_economia_id = c04.id
    join aux c05 on ca.ente_publico_id = c05.id
    join aux c06 on ee.upp_id = c06.id
    join aux c07 on ee.subsecretaria_id = c07.id
    join aux c08 on ee.ur_id = c08.id
    join aux c09 on cf.finalidad_id = c09.id
    join aux c10 on cf.funcion_id = c10.id
    join aux c11 on cf.subfuncion_id = c11.id
    join aux c12 on p.eje_id = c12.id
    join aux c13 on p.linea_accion_id = c13.id
    join aux c14 on p.programa_sectorial_id = c14.id
    join aux c15 on c.tipologia_conac_id = c15.id
    join aux c16 on e.programa_id = c16.id
    join aux c17 on e.subprograma_id = c17.id
    join aux c18 on e.proyecto_id = c18.id;

    if(accion = 0) then
        insert into v_epp(
            id,ejercicio,mes_i,mes_f,
            clv_sector_publico,sector_publico,clv_sector_publico_f,sector_publico_f,clv_sector_economia,sector_economia,
            clv_subsector_economia,subsector_economia,clv_ente_publico,ente_publico,clv_upp,upp,clv_subsecretaria,
            subsecretaria,clv_ur,ur,clv_finalidad,finalidad,clv_funcion,funcion,clv_subfuncion,subfuncion,clv_eje,eje,
            clv_linea_accion,linea_accion,clv_programa_sectorial,programa_sectorial,clv_tipologia_conac,tipologia_conac,
            clv_programa,programa,clv_subprograma,subprograma,clv_proyecto,proyecto,
            estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,
            created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
        ) 
        select 
            id,ejercicio,mes_i,mes_f,
            clv_01, desc_01,
            clv_02, desc_02,
            clv_03, desc_03,
            clv_04, desc_04,
            clv_05, desc_05,
            clv_06, desc_06,
            clv_07, desc_07,
            clv_08, desc_08,
            clv_09, desc_09,
            clv_10, desc_10,
            clv_11, desc_11,
            clv_12, desc_12,
            clv_13, desc_13,
            clv_14, desc_14,
            clv_15, desc_15,
            clv_16, desc_16,
            clv_17, desc_17,
            clv_18, desc_18,
            estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,
            created_at,updated_at,deleted_at,
            created_user,updated_user,deleted_user
        from epp_descripciones;
    end if;

    if(accion = 1) then
        update v_epp ve 
        join epp_descripciones ed on ve.id = ed.id
        set ve.ejercicio = ed.ejercicio,
        ve.mes_i = ed.mes_i,
        ve.mes_f = ed.mes_f,
        ve.clv_sector_publico = ed.clv_01,
        ve.sector_publico = ed.desc_01,
        ve.clv_sector_publico_f = ed.clv_02,
        ve.sector_publico_f = ed.desc_02,
        ve.clv_sector_economia = ed.clv_03,
        ve.sector_economia = ed.desc_03,
        ve.clv_subsector_economia = ed.clv_04,
        ve.subsector_economia = ed.desc_04,
        ve.clv_ente_publico = ed.clv_05,
        ve.ente_publico = ed.desc_05,
        ve.clv_upp = ed.clv_06,
        ve.upp = ed.desc_06,
        ve.clv_subsecretaria = ed.clv_07,
        ve.subsecretaria = ed.desc_07,
        ve.clv_ur = ed.clv_08,
        ve.ur = ed.desc_08,
        ve.clv_finalidad = ed.clv_09,
        ve.finalidad = ed.desc_09,
        ve.clv_funcion = ed.clv_10,
        ve.funcion = ed.desc_10,
        ve.clv_subfuncion = ed.clv_11,
        ve.subfuncion = ed.desc_11,
        ve.clv_eje = ed.clv_12,
        ve.eje = ed.desc_12,
        ve.clv_linea_accion = ed.clv_13,
        ve.linea_accion = ed.desc_13,
        ve.clv_programa_sectorial = ed.clv_14,
        ve.programa_sectorial = ed.desc_14,
        ve.clv_tipologia_conac = ed.clv_15,
        ve.tipologia_conac = ed.desc_15,
        ve.clv_programa = ed.clv_16,
        ve.programa = ed.desc_16,
        ve.clv_subprograma = ed.clv_17,
        ve.subprograma = ed.desc_17,
        ve.clv_proyecto = ed.clv_18,
        ve.proyecto = ed.desc_18,
        ve.estatus = ed.estatus,
        ve.presupuestable = ed.presupuestable,
        ve.con_mir = ed.con_mir,
        ve.confirmado = ed.confirmado,
        ve.tipo_presupuesto = ed.tipo_presupuesto,
        ve.updated_at = ed.updated_at,
        ve.deleted_at = ed.deleted_at,
        ve.created_user = ed.created_user,
        ve.updated_user = ed.updated_user,
        ve.deleted_user = ed.deleted_user
        where ve.id = @id;
    end if;

    drop temporary table if exists epp_descripciones;
END;");
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
