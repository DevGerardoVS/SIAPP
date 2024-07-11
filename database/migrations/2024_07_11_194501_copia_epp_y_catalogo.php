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
        DB::unprepared("DROP PROCEDURE IF EXISTS llenar_v_epp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS rellenar_tablas_intermedias;");

        DB::unprepared("CREATE PROCEDURE rellenar_tablas_intermedias(in usuario varchar(45))
BEGIN
    drop temporary table if exists administrativa_temp;
    drop temporary table if exists conac_temp;
    set @id_cat := (select max(id) from catalogo);
    set @anio_old := (select max(ejercicio) from catalogo where deleted_at is null);
    set @anio_new := @anio_old + 1;
    set @created := now();
    set @id_ca := (select max(id) from clasificacion_administrativa);
    set @id_ee := (select max(id) from entidad_ejecutora);
    set @id_cf := (select max(id) from clasificacion_funcional);
    set @id_p := (select max(id) from pladiem);
    set @id_c := (select max(id) from conac);
    set @id_ue := (select max(id) from upp_extras);
    truncate comp_catalogo;

    create table comp_catalogo
    select
        id id_old,(@id_cat:=@id_cat+1) id_new,@anio_new ejercicio,grupo_id,clave,descripcion,
        descripcion_larga,descripcion_corta,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from catalogo
    where ejercicio = @anio_old and deleted_at is null and 
    grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,26,27,28);

    alter table comp_catalogo add primary key (id_old,id_new);

    insert into catalogo(
        id,ejercicio,grupo_id,clave,descripcion,descripcion_larga,descripcion_corta,
        created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    select 
        id_new,ejercicio,grupo_id,clave,descripcion,descripcion_larga,descripcion_corta,
        created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    from comp_catalogo;

    create temporary table administrativa_temp
    with aux as (
        select * from comp_catalogo
        where grupo_id in (1,2,3,4,5)
    )
    select 
        ca.id id_old,(@id_ca:=@id_ca+1) id_new,@anio_new ejercicio,
        c1.id_new sector_publico_id,
        c2.id_new sector_publico_f_id,
        c3.id_new sector_economia_id,
        c4.id_new subsector_economia_id,
        c5.id_new ente_publico_id,
        0 estatus,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from clasificacion_administrativa ca
    join aux c1 on ca.sector_publico_id = c1.id_old
    join aux c2 on ca.sector_publico_f_id = c2.id_old
    join aux c3 on ca.sector_economia_id = c3.id_old
    join aux c4 on ca.subsector_economia_id = c4.id_old
    join aux c5 on ca.ente_publico_id = c5.id_old
    where ca.ejercicio = @anio_old and ca.deleted_at is null;

    insert into clasificacion_administrativa (
        id,ejercicio,sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,
        estatus,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    select 
        id_new,ejercicio,sector_publico_id,sector_publico_f_id,sector_economia_id,subsector_economia_id,ente_publico_id,
        estatus,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    from administrativa_temp;

    insert into entidad_ejecutora(
        id,ejercicio,upp_id,subsecretaria_id,ur_id,estatus,
        created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    with aux as (
        select * from comp_catalogo
        where grupo_id in (6,7,8)
    )
    select
        (@id_ee:=@id_ee+1) id,@anio_new ejercicio,
        c1.id_new upp_id,
        c2.id_new subsecretaria_id,
        c3.id_new ur_id,
        0 estatus,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from entidad_ejecutora ee
    join aux c1 on ee.upp_id = c1.id_old
    join aux c2 on ee.subsecretaria_id = c2.id_old
    join aux c3 on ee.ur_id = c3.id_old
    where ee.ejercicio = @anio_old and ee.deleted_at is null;

    insert into clasificacion_funcional(
        id,ejercicio,finalidad_id,funcion_id,subfuncion_id,estatus,
        created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    with aux as (
        select * from comp_catalogo
        where grupo_id in (9,10,11)
    )
    select
        (@id_cf:=@id_cf+1) id,@anio_new ejercicio,
        c1.id_new finalidad_id,
        c2.id_new funcion_id,
        c3.id_new subfuncion_id,
        0 estatus,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from clasificacion_funcional cf
    join aux c1 on cf.finalidad_id = c1.id_old
    join aux c2 on cf.funcion_id = c2.id_old
    join aux c3 on cf.subfuncion_id = c3.id_old
    where cf.ejercicio = @anio_old and cf.deleted_at is null;

    insert into pladiem(
	    id,ejercicio,eje_id,objetivo_sectorial_id,estrategia_id,linea_accion_id,programa_sectorial_id,
	    estatus,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    with aux as (
        select * from comp_catalogo
        where grupo_id in (12,26,27,13,14)
    )
    select 
        (@id_p:=@id_p+1) id,@anio_new ejercicio,
        c1.id_new eje_id,
        c2.id_new objetivo_sectorial_id,
        c3.id_new estrategia_id,
        c4.id_new linea_accion_id,
        c5.id_new programa_sectorial_id,
        0 estatus,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from pladiem p
    join aux c1 on p.eje_id = c1.id_old
    join aux c2 on p.objetivo_sectorial_id = c2.id_old
    join aux c3 on p.estrategia_id = c3.id_old
    join aux c4 on p.linea_accion_id = c4.id_old
    join aux c5 on p.programa_sectorial_id = c5.id_old
    where p.ejercicio = @anio_old and p.deleted_at is null;

    create temporary table conac_temp
    with aux as (
        select * from comp_catalogo
        where grupo_id in (15,28)
    )
    select 
        (@id_c:=@id_c+1) id,@anio_new ejercicio,
        c1.id_new padre_id,
        c2.id_new tipologia_conac_id,
        0 estatus,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from conac c
    left join aux c1 on c.padre_id = c1.id_old
    left join aux c2 on c.tipologia_conac_id = c2.id_old
    where c.ejercicio = @anio_old and c.deleted_at is null;

    insert into conac(
        id,ejercicio,padre_id,tipologia_conac_id,estatus,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )select * from conac_temp;

    insert into upp_extras(
        id,ejercicio,upp_id,clasificacion_administrativa_id,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user
    )
    select 
        (@id_ue:=@id_ue+1) id,@anio_new ejercicio,
        ca.id_new upp_id,
        ad.id_new clasificacion_administrativa_id,
        @created created_at,@created updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from upp_extras ue
    join (
        select * from comp_catalogo
        where grupo_id = 6
    ) ca on ue.upp_id = ca.id_old
    join administrativa_temp ad on ue.clasificacion_administrativa_id = ad.id_old
    where ue.ejercicio = @anio_old and ue.deleted_at is null;

    drop temporary table if exists administrativa_temp;
    drop temporary table if exists conac_temp;
END;");

        DB::unprepared("CREATE PROCEDURE llenar_v_epp(in upp_v varchar(3),in usuario varchar(45))
BEGIN
    set @anio_old := (select (max(ejercicio)-1) from catalogo where deleted_at is null);
    set @anio_new := (select max(ejercicio) from catalogo where deleted_at is null);
    set @query1 := concat(\"set @id := (select id from catalogo where ejercicio = \",@anio_old,\" and deleted_at is null and clave = '\",upp_v,\"' and grupo_id = 6);\");
    set @query2 := concat(\"set @id_upp_new := (select id from catalogo where ejercicio = \",@anio_new,\" and deleted_at is null and clave = '\",upp_v,\"' and grupo_id = 6);\");
    set @id_e := (select max(id) from epp);
    set @created := now();
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

    create temporary table administrativa_temporal
    with new_admin as (
        with aux as (
            select * from comp_catalogo
            where grupo_id in (1,2,3,4,5)
        )
        select 
            ca.id id_old,
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
        where ca.ejercicio = @anio_old
    )
    select na.id_old,ca.id id_new
    from new_admin na
    join clasificacion_administrativa ca on
    ca.sector_publico_id = na.sector_publico_id and 
    ca.sector_publico_f_id = na.sector_publico_f_id and 
    ca.sector_economia_id = na.sector_economia_id and 
    ca.subsector_economia_id = na.subsector_economia_id and 
    ca.ente_publico_id = na.ente_publico_id and 
    ca.ejercicio = @anio_new;

    create temporary table ejecutora_temporal
    with new_entidad as (
        with aux as (
            select * from comp_catalogo
            where grupo_id in (6,7,8)
        )
        select 
            ee.id id_old,
            c1.id_new upp_id,
            c2.id_new subsecretaria_id,
            c3.id_new ur_id
        from entidad_ejecutora ee
        join aux c1 on ee.upp_id = c1.id_old
        join aux c2 on ee.subsecretaria_id = c2.id_old
        join aux c3 on ee.ur_id = c3.id_old
        where ee.ejercicio = @anio_old
    )
    select ne.id_old,ee.id id_new
    from new_entidad ne
    join entidad_ejecutora ee on
    ee.upp_id = ne.upp_id and
    ee.subsecretaria_id = ne.subsecretaria_id and
    ee.ur_id = ne.ur_id and
    ee.ejercicio = @anio_new;

    create temporary table funcional_temporal
    with new_funcional as (
        with aux as (
            select * from comp_catalogo
            where grupo_id in (9,10,11)
        )
        select 
            cf.id id_old,
            c1.id_new finalidad_id,
            c2.id_new funcion_id,
            c3.id_new subfuncion_id
        from clasificacion_funcional cf
        join aux c1 on cf.finalidad_id = c1.id_old
        join aux c2 on cf.funcion_id = c2.id_old
        join aux c3 on cf.subfuncion_id = c3.id_old
        where cf.ejercicio = @anio_old
    )
    select nf.id_old,cf.id id_new
    from new_funcional nf
    join clasificacion_funcional cf on
    cf.finalidad_id = nf.finalidad_id and
    cf.funcion_id = nf.funcion_id and
    cf.subfuncion_id = nf.subfuncion_id and
    cf.ejercicio = @anio_new;

    create temporary table pladiem_temporal
    with new_pladiem as (
        with aux as (
            select * from comp_catalogo
            where grupo_id in (12,26,27,13,14)
        )
        select 
            p.id id_old,
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
        where p.ejercicio = @anio_old
    )
    select np.id_old,p.id id_new
    from new_pladiem np
    join pladiem p on 
    p.eje_id = np.eje_id and
    p.objetivo_sectorial_id = np.objetivo_sectorial_id and
    p.estrategia_id = np.estrategia_id and
    p.linea_accion_id = np.linea_accion_id and
    p.programa_sectorial_id = np.programa_sectorial_id and 
    p.ejercicio = @anio_new;

    create temporary table conac_temporal
    with new_conac as (
        with aux as (
            select *
            from comp_catalogo
            where grupo_id in (28,15)
        )
        select 
            c.id id_old,
            c1.id_new padre_id,
            c2.id_new tipologia_conac_id
        from conac c
        left join aux c1 on c.padre_id = c1.id_old
        left join aux c2 on c.tipologia_conac_id = c2.id_old
        where c.ejercicio = @anio_old
    )
    select nc.id_old,c.id id_new
    from new_conac nc
    join conac c on 
    c.padre_id = nc.padre_id and 
    c.tipologia_conac_id = nc.tipologia_conac_id and
    c.ejercicio = @anio_new;

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
        select * from comp_catalogo
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
        usuario created_user,null updated_user,null deleted_user
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
        e.created_at,
        e.created_user,
        e.updated_user,
        e.deleted_user
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

    drop temporary table if exists administrativa_temporal;
    drop temporary table if exists ejecutora_temporal;
    drop temporary table if exists funcional_temporal;
    drop temporary table if exists pladiem_temporal;
    drop temporary table if exists conac_temporal;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS insert_v_epp;");
        DB::unprepared("DROP PROCEDURE IF EXISTS rellenar_tablas_intermedias;");
    }
};
