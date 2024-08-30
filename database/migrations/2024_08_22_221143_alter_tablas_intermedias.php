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
    drop table if exists comp_catalogo;

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

    insert into catalogo(padre_id,ejercicio,grupo_id,clave,descripcion,descripcion_larga,descripcion_corta,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user)
    select 
        null padre_id,
        @anio_new ejercicio,
        39 grupo_id,
        case 
            when clave = '000000' then clave
            else concat(25,substr(clave,3,4))
        end clave,
        descripcion,
        descripcion_larga,descripcion_corta,
        now() created_at,now() updated_at,null deleted_at,
        usuario created_user,null updated_user,null deleted_user
    from catalogo
    where grupo_id = 39 and ejercicio = @anio_old;

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS rellenar_tablas_intermedias;");
    }
};
