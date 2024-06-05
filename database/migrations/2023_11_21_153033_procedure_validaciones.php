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
        DB::unprepared("DROP PROCEDURE IF EXISTS validacion_claves;");
        DB::unprepared("DROP TABLE IF EXISTS programacion_presupuesto_aux;");

        if (!Schema::hasTable('programacion_presupuesto_aux')) {
            Schema::create('programacion_presupuesto_aux', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_carga')->nullable();
                $table->string('clasificacion_administrativa', 5);
                $table->string('entidad_federativa', 2);
                $table->string('region', 2);
                $table->string('municipio', 3);
                $table->string('localidad', 3);
                $table->string('upp', 3);
                $table->string('subsecretaria', 1);
                $table->string('ur', 2);
                $table->string('finalidad', 1);
                $table->string('funcion', 1);
                $table->string('subfuncion', 1);
                $table->string('eje', 1);
                $table->string('linea_accion', 2);
                $table->string('programa_sectorial', 1);
                $table->string('tipologia_conac', 1);
                $table->string('programa_presupuestario', 2);
                $table->string('subprograma_presupuestario', 3);
                $table->string('proyecto_presupuestario', 3);
                $table->string('periodo_presupuestal', 6);
                $table->string('posicion_presupuestaria', 18);
                $table->string('tipo_gasto', 1);
                $table->string('anio', 4);
                $table->string('etiquetado', 1);
                $table->string('fuente_financiamiento', 1);
                $table->string('ramo', 2);
                $table->string('fondo_ramo', 2);
                $table->string('capital', 1);
                $table->string('proyecto_obra', 6);
                $table->integer('ejercicio')->nullable();
                $table->decimal('enero', 22, 2)->default(null);
                $table->decimal('febrero', 22, 2)->default(null);
                $table->decimal('marzo', 22, 2)->default(null);
                $table->decimal('abril', 22, 2)->default(null);
                $table->decimal('mayo', 22, 2)->default(null);
                $table->decimal('junio', 22, 2)->default(null);
                $table->decimal('julio', 22, 2)->default(null);
                $table->decimal('agosto', 22, 2)->default(null);
                $table->decimal('septiembre', 22, 2)->default(null);
                $table->decimal('octubre', 22, 2)->default(null);
                $table->decimal('noviembre', 22, 2)->default(null);
                $table->decimal('diciembre', 22, 2)->default(null);
                $table->decimal('total', 22, 2)->default(null);
                $table->integer('estado');
                $table->enum('tipo', ['Operativo', 'RH']);
            });
        }

        DB::unprepared("CREATE PROCEDURE validacion_claves(in id_usuario int,in usuario varchar(45),in borrar tinyint)
        begin
            SET SESSION group_concat_max_len = 1000000;
            set @anio := (
                select c.ejercicio from cierre_ejercicio_claves c
                where c.deleted_at is null and estatus = 'Abierto'
                order by ejercicio desc 
                limit 1);
            set @carga := id_usuario;
            
            drop temporary table if exists errores;
            create temporary table errores(
                num_linea text,
                modulo varchar(100),
                error varchar(255)
            );
        
            set @tipo_usuario := (select id_grupo from adm_users where id = id_usuario);
        
            if(@anio is null and @tipo_usuario != 1) then
                insert into errores(num_linea,modulo,error) values
                ('','Ejercicios abiertos','No hay ningún ejercicio abierto');
            else
        
                set @anio := (
                    select c.ejercicio from cierre_ejercicio_claves c
                    where c.deleted_at is null
                    order by ejercicio desc 
                    limit 1
                );
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Cierre de Ejercicio' modulo,
                    concat('El ejercicio para la clave ',clv_upp,' esta cerrado') error
                from (
                with aux as (
                    select
                        group_concat(id) lista_id,
                        pp.upp clv_upp,
                        (concat('20',pp.anio))*1 ejercicio
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by upp,anio
                )
                select 
                    lista_id,aux.clv_upp,estatus
                from aux
                left join cierre_ejercicio_claves ce on ce.clv_upp = aux.clv_upp
                and ce.ejercicio = @anio and ce.deleted_at is null) t
                where estatus != 'Abierto';

                insert into errores(num_linea,modulo,error)
                with aux as (
                select distinct
                    concat(
                        ve.clv_sector_publico,
                        ve.clv_sector_publico_f,
                        ve.clv_sector_economia,
                        ve.clv_subsector_economia,
                        ve.clv_ente_publico 
                    ) administrativa
                from v_epp ve
                where ejercicio = @anio and deleted_at is null)
                select 
                    lista_id num_linea,
                    'Clasificación Administrativa' modulo,
                    concat('La clasificación administrativa ',clasificacion_administrativa,' no existe') error
                from (
                    select group_concat(id) lista_id,clasificacion_administrativa 
                    from programacion_presupuesto_aux
                    where id_carga = @carga
                    group by clasificacion_administrativa
                ) pp
                left join aux a on pp.clasificacion_administrativa = a.administrativa
                where administrativa is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Clasificación Geografica' modulo,
                    concat('No existe la clasificación geografica: ',
                    entidad_federativa,'-',region,'-',municipio,'-',localidad) error
                from (
                with aux as (
                    select
                        group_concat(id) lista_id,
                        pp.entidad_federativa,pp.region,pp.municipio,pp.localidad
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by entidad_federativa,region,municipio,localidad
                )
                select 
                    id,
                    a.*
                from aux a 
                left join clasificacion_geografica cg on a.entidad_federativa = cg.clv_entidad_federativa
                and a.region = cg.clv_region and a.municipio = cg.clv_municipio 
                and a.localidad = cg.clv_localidad and cg.deleted_at is null)t
                where id is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Entidad Ejecutora' modulo,
                    concat('No existe la entidad ejecutora: ',upp,'-',subsecretaria,'-',ur) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        upp,subsecretaria,ur
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by upp,subsecretaria,ur
                )
                select 
                    ve.*,
                    a.*
                from aux a
                left join (
                    select distinct 
                        clv_upp,clv_subsecretaria,clv_ur
                    from v_epp where deleted_at is null and ejercicio = @anio
                ) ve on a.upp = ve.clv_upp
                and a.subsecretaria = ve.clv_subsecretaria and a.ur = ve.clv_ur)t
                where clv_upp is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Area Funcional' modulo,
                    concat('No existe el area funcional: ',
                    finalidad,'-',funcion,'-',subfuncion,'-',eje,'-',linea_Accion,'-',
                    programa_sectorial,'-',tipologia_conac,'-',programa_presupuestario,'-',
                    subprograma_presupuestario,'-',proyecto_presupuestario) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        finalidad,funcion,subfuncion,eje,pp.linea_accion,
                        pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,
                        pp.subprograma_presupuestario,pp.proyecto_presupuestario
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by finalidad,funcion,subfuncion,eje,pp.linea_accion,
                    pp.programa_sectorial,pp.tipologia_conac,pp.programa_presupuestario,
                    pp.subprograma_presupuestario,pp.proyecto_presupuestario
                )
                select 
                    e.clv_finalidad,
                    a.*
                from aux a
                left join (
                    select 
                        ve.clv_finalidad,ve.clv_funcion,ve.clv_subfuncion,ve.clv_eje,
                        ve.clv_linea_accion,ve.clv_programa_sectorial,ve.clv_tipologia_conac,
                        ve.clv_programa,ve.clv_subprograma,ve.clv_proyecto
                    from v_epp ve
                    where ve.ejercicio = @anio and ve.deleted_at is null
                ) e on a.finalidad = e.clv_finalidad and a.funcion = e.clv_funcion and a.subfuncion = e.clv_subfuncion
                and a.eje = e.clv_eje and a.linea_accion = e.clv_linea_accion 
                and a.programa_sectorial = e.clv_programa_sectorial and a.tipologia_conac = e.clv_tipologia_conac
                and a.programa_presupuestario = e.clv_programa and a.subprograma_presupuestario = clv_subprograma
                and a.proyecto_presupuestario = e.clv_proyecto)t
                where clv_finalidad is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Posición Presupuestaria (Partida)' modulo,
                    concat('No existe la partida: ',partida,' con tipo de gasto ',tipo_gasto) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        pp.posicion_presupuestaria partida,pp.tipo_gasto
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by posicion_presupuestaria,tipo_gasto
                )
                select 
                    pp.partida clv_partida,
                    a.*
                from aux a
                left join (
                    select 
                        concat(
                            pt.clv_capitulo,pt.clv_concepto,
                            pt.clv_partida_generica,pt.clv_partida_especifica
                        ) partida, pt.clv_tipo_gasto
                    from posicion_presupuestaria pt
                    where pt.deleted_at is null
                ) pp on a.partida = pp.partida
                and a.tipo_gasto = pp.clv_tipo_gasto)t
                where clv_partida is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Fondos' modulo,
                    concat('No existe el fondo: ',etiquetado,'-',fuente_financiamiento,
                    '-',ramo,'-',fondo_ramo,'-',capital) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        pp.etiquetado,pp.fuente_financiamiento,
                        pp.ramo,pp.fondo_ramo,pp.capital
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by etiquetado,fuente_financiamiento,
                    ramo,fondo_ramo,capital
                )
                select 
                    f.clv_etiquetado,
                    a.*
                from aux a 
                left join fondo f on a.etiquetado = f.clv_etiquetado 
                and a.fuente_financiamiento = f.clv_fuente_financiamiento 
                and a.ramo = f.clv_ramo and a.fondo_ramo = f.clv_fondo_ramo)t
                where clv_etiquetado is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Proyectos de Obra' modulo,
                    concat('No existe la obra: ',proyecto_obra) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        pp.upp,pp.proyecto_obra
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by upp,proyecto_obra
                )
                select 
                    po.id,
                    a.*
                from aux a
                left join proyectos_obra po on a.proyecto_obra = po.clv_proyecto_obra
                and po.ejercicio = @anio and po.deleted_at is null)t
                where id is null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'Relación Clasificacion Administrativa - Partida' modulo,
                    concat(
                        'No existe la relación entre la clasificación administrativa: ',clasificacion_administrativa,
                        ' y la posicion presupuestaria (partida): ',clasificacion_economica
                    ) error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        pp.clasificacion_administrativa,
                        concat(
                            pp.posicion_presupuestaria,
                            pp.tipo_gasto
                        ) clasificacion_economica
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by clasificacion_administrativa,
                    posicion_presupuestaria,tipo_gasto
                )
                select 
                    re.id,
                    a.*
                from aux a 
                left join rel_economica_administrativa re
                on a.clasificacion_administrativa = re.clasificacion_administrativa
                and a.clasificacion_economica = re.clasificacion_economica)t
                where id is null;
                
                drop temporary table if exists presupuesto;
                if(borrar = 0) then
                    create temporary table presupuesto
                    select clv_upp,clv_fondo,sum(total) presupuestado
                    from (
                        select 
                            ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total
                        from programacion_presupuesto_aux ppa 
                        where id_carga = @carga
                        union all
                        select 
                            ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total presupuestado
                        from programacion_presupuesto ppa
                        where ejercicio = @anio and deleted_at is null
                    )t
                    group by clv_upp,clv_fondo;
                else
                    create temporary table presupuesto
                    select clv_upp,clv_fondo,sum(total) presupuestado
                    from (
                        select 
                            ppa.upp clv_upp,ppa.fondo_ramo clv_fondo,total
                        from programacion_presupuesto_aux ppa 
                        where id_carga = @carga
                    )t
                    group by clv_upp,clv_fondo;
                end if;
                
                insert into errores(num_linea,modulo,error)
                select 
                    0 num_linea,
                    'Revisión con Techos Financieros' modulo,
                    concat('La upp: ',clv_upp,', con fondo: ',clv_fondo,', sobrepasa el techo financiero') error
                from (
                    select 
                        tf.clv_upp,
                        tf.clv_fondo,
                        sum(presupuesto) asignado,
                        case
                            when pp.presupuestado is null then 0 else pp.presupuestado
                        end presupuestado
                    from techos_financieros tf
                    left join presupuesto pp on tf.clv_upp = pp.clv_upp and tf.clv_fondo = pp.clv_fondo
                    where tf.deleted_at is null and tf.ejercicio = @anio
                    group by tf.clv_upp,tf.clv_fondo,presupuestado
                )t
                where presupuestado > asignado;
                drop temporary table if exists presupuesto;
                
                insert into errores(num_linea,modulo,error)
                select 
                    lista_id num_linea,
                    'EPP Presupuestable' modulo,
                    concat('La clave no es presupuestable o no se encuentra en el epp') error
                from (
                with aux as (
                    select 
                        group_concat(id) lista_id,
                        upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,
                        programa_sectorial,tipologia_conac,programa_presupuestario,
                        subprograma_presupuestario,proyecto_presupuestario
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                    group by upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,
                    programa_sectorial,tipologia_conac,programa_presupuestario,
                    subprograma_presupuestario,proyecto_presupuestario
                )
                select 
                    e.clv_finalidad,
                    a.*
                from aux a
                left join (
                    select 
                        ve.clv_upp,ve.clv_subsecretaria,ve.clv_ur,
                        ve.clv_finalidad,ve.clv_funcion,ve.clv_subfuncion,ve.clv_eje,
                        ve.clv_linea_accion,ve.clv_programa_sectorial,ve.clv_tipologia_conac,
                        ve.clv_programa,ve.clv_subprograma,ve.clv_proyecto
                    from v_epp ve
                    where ve.ejercicio = @anio and ve.deleted_at is null and presupuestable = 1
                ) e on a.upp = e.clv_upp and a.subsecretaria = e.clv_subsecretaria and a.ur = e.clv_ur
                and a.finalidad = e.clv_finalidad and a.funcion = e.clv_funcion and a.subfuncion = e.clv_subfuncion
                and a.eje = e.clv_eje and a.linea_accion = e.clv_linea_accion 
                and a.programa_sectorial = e.clv_programa_sectorial and a.tipologia_conac = e.clv_tipologia_conac
                and a.programa_presupuestario = e.clv_programa and a.subprograma_presupuestario = clv_subprograma
                and a.proyecto_presupuestario = e.clv_proyecto
                where clv_finalidad is null)t;
                
                if(borrar = 0) then
                    insert into errores(num_linea,modulo,error)
                    with aux as (
                        select 
                            group_concat(id) lista_id,
                            sum(cantidad) cantidad,
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        from (
                            select 
                                id,
                                1 cantidad,
                                clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                                upp,subsecretaria,ur,
                                finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                                programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                                periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                            from programacion_presupuesto_aux ppa 
                            where id_carga = @carga
                            union all
                            select 
                                0 id,
                                1 cantidad,
                                clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                                upp,subsecretaria,ur,
                                finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                                programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                                periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                            from programacion_presupuesto ppa 
                            where ejercicio = @anio and deleted_at is null
                        )t
                        group by clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                    )
                    select 
                        lista_id num_linea,
                        'Claves Repetidas' modulo,
                        'Existen Claves Repetidas, las lineas que dicen 0 es porque esa clave ya registrada y no del excel' error
                    from aux
                    where cantidad > 1;
                else 
                    insert into errores(num_linea,modulo,error)
                    with aux as (
                        select 
                            group_concat(id) lista_id,
                            sum(cantidad) cantidad,
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        from (
                            select 
                                id,
                                1 cantidad,
                                clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                                upp,subsecretaria,ur,
                                finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                                programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                                periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                            from programacion_presupuesto_aux ppa 
                            where id_carga = @carga
                        )t
                        group by clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
                        finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                    )
                    select 
                        lista_id num_linea,
                        'Claves Repetidas' modulo,
                        'Existen Claves Repetidas, las lineas que dicen 0 es porque esa clave ya registrada y no del excel' error
                    from aux
                    where cantidad > 1;
                end if;

                insert into errores(num_linea,modulo,error)
                with aux as (
                    select 
                        group_concat(id) num_linea
                    from (
                        select 
                            id,
                            total,
                            (
                                enero+febrero+marzo+abril+mayo+
                                junio+julio+agosto+septiembre+
                                octubre+noviembre+diciembre
                            ) meses
                        from programacion_presupuesto_aux pp
                        where id_carga = @carga
                    )t where total != meses
                )
                select 
                    num_linea,
                    'Total y Meses iguales' modulo,
                    'Las claves no pueden tener diferente la suma de sus meses y el total' error
                from aux
                where num_linea is not null;
                
                insert into errores(num_linea,modulo,error)
                select 
                    num_linea,
                    'Presupuesto en 0' modulo,
                    'La clave no puede tener un total de 0' error
                from (
                    select 
                        group_concat(id) num_linea,
                        'Presupuesto en 0' modulo,
                        'La clave no puede tener un total de 0' error
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga and total < 1
                )t where num_linea is not null;
                
                insert into errores(num_linea,modulo,error)
                with aux as (
                    select group_concat(id) num_linea from (
                    select 
                        id,
                        ((floor(enero)-enero)+(floor(febrero)-febrero)+(floor(marzo)-marzo)+
                        (floor(abril)-abril)+(floor(mayo)-mayo)+(floor(junio)-junio)+
                        (floor(julio)-julio)+(floor(agosto)-agosto)+(floor(septiembre)-septiembre)+
                        (floor(octubre)-octubre)+(floor(noviembre)-noviembre)+(floor(diciembre)-diciembre)) meses_d
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga)t where meses_d < 0
                )
                select 
                    num_linea,
                    'Decimales' modulo,
                    'No se deben ingresar decimales' error
                from aux where num_linea is not null;
                
                insert into errores(num_linea,modulo,error)
                with aux as (
                    select 
                        id,
                        length(concat(
                            clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                            upp,subsecretaria,ur,
                            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                            programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                            periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra
                        )) clave
                    from programacion_presupuesto_aux pp
                    where id_carga = @carga
                )
                select *
                from (
                    select 
                        group_concat(id) num_linea,
                        'Tamaño de Clave' modulo,
                        'Existe una clave con un número menor de carácteres' error
                    from aux
                    where clave < 64
                )t where num_linea is not null;
                    
                insert into errores(num_linea,modulo,error)
                with aux as (
                    select a.*,u.clv_upp
                    from (
                        select
                            group_concat(id) lista_id,upp,
                            subprograma_presupuestario subprograma,
                            substr(posicion_presupuestaria,1,1) capitulo,
                            posicion_presupuestaria partida
                        from programacion_presupuesto_aux pp
                        where id_carga = @carga
                        group by upp,subprograma_presupuestario,posicion_presupuestaria
                    ) a
                    left join uppautorizadascpnomina u on a.upp = u.clv_upp 
                    and u.deleted_at is null
                )
                select 
                    lista_id num_linea,
                    'UPPS Autorizadas' modulo,
                    concat('La UPP: ',upp,', no tiene autorizado cargar el capitulo 1000') error
                from aux
                where clv_upp is null and capitulo = '1'
                union all 
                select 
                    lista_id num_linea,
                    'UPPS Autorizadas' modulo,
                    concat('La UPP: ',upp,', no tiene autorizado cargar la partida 39801') error
                from aux
                where clv_upp is null and partida = '39801'
                union all 
                select 
                    lista_id num_linea,
                    'UPPS Autorizadas' modulo,
                    concat('La UPP: ',upp,', no tiene autorizado cargar el subprograma UUU') error
                from aux
                where clv_upp is null and subprograma = 'UUU';
            
                set @c_errores := (select count(*) cantidad from errores);
                if(@c_errores = 0) then
                    update programacion_presupuesto_aux set ejercicio = @anio where id_carga = @carga;
                
                    if(@tipo_usuario = 5) then
                        update programacion_presupuesto_aux set tipo = 'RH' where id_carga = @carga;
                    else
                        update programacion_presupuesto_aux set tipo = 'Operativo' where id_carga = @carga;
                    end if;
            
                    set @usuario := concat(usuario,'-Carga_Masiva_Claves');
                
                    insert into programacion_presupuesto(
                        clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                        upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,
                        etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,
                        ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,
                        estado,tipo,deleted_at,created_user,updated_user,deleted_user,created_at,updated_at)
                    select 
                        clasificacion_administrativa,entidad_federativa,region,municipio,localidad,
                        upp,subsecretaria,ur,finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,
                        programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,
                        periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,
                        etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,
                        ejercicio,enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,
                        0,tipo,null,@usuario,null,null,now(),now()
                    from programacion_presupuesto_aux a
                    where id_carga = @carga;
                end if;
        
            end if;
        
            delete from programacion_presupuesto_aux where id_carga = @carga;
            
            select * from errores;
            drop temporary table if exists errores;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS validaciones;");
        DB::unprepared("DROP TABLE IF EXISTS programacion_presupuesto_aux;");
    }
};
