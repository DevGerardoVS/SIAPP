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
        DB::unprepared("DROP PROCEDURE IF EXISTS conceptos_clave;");

        DB::unprepared("CREATE PROCEDURE conceptos_clave(in claveT varchar(64),in anio int)
begin
    set @clave := claveT; 
    set @epp := concat(substring(@clave,1,5),substring(@clave,16,22));
    set @clasGeo := ((substring(@clave,6,10))*1);
    set @partida := ((substring(@clave,44,6))*1);
    set @fondo := substring(@clave,52,7);
    set @obra := substring(@clave,59,6);

	drop temporary table if exists clas_geo_llaves;
	drop temporary table if exists pos_pre_llaves;
	drop temporary table if exists fondo_llaves;

	create temporary table clas_geo_llaves
	select
		cg.id,
		concat(c1.clave,c2.clave,c3.clave,c4.clave) clasificacion_geografica_llave,
		c1.clave clv_entidad_federativa,c1.descripcion entidad_federativa,
		c2.clave clv_region,c2.descripcion region,
		c3.clave clv_municipio,c3.descripcion municipio,
		c4.clave clv_localidad,c4.descripcion localidad
	from clasificacion_geografica cg
	join catalogo c1 on cg.entidad_federativa_id = c1.id
	join catalogo c2 on cg.region_id = c2.id
	join catalogo c3 on cg.municipio_id = c3.id
	join catalogo c4 on cg.localidad_id = c4.id
	where cg.deleted_at is null;

	create temporary table pos_pre_llaves
	select
		ce.id,
		concat(c1.clave,c2.clave,c3.clave,c4.clave,c5.clave) posicion_presupuestaria_llave,
		c1.clave clv_capitulo,c1.descripcion capitulo,
		c2.clave clv_concepto,c2.descripcion concepto,
		c3.clave clv_partida_generica,c3.descripcion partida_generica,
		c4.clave clv_partida_especifica,c4.descripcion partida_especifica,
		c5.clave clv_tipo_gasto,c5.descripcion tipo_gasto
	from clasificacion_economica ce
	join catalogo c1 on ce.capitulo_id = c1.id
	join catalogo c2 on ce.concepto_id = c2.id
	join catalogo c3 on ce.partida_generica_id = c3.id
	join catalogo c4 on ce.partida_especifica_id = c4.id
	join catalogo c5 on ce.tipo_gasto_id = c5.id
	where ce.deleted_at is null;

	create temporary table fondo_llaves
	select
		f.id,
		concat(c1.clave,c2.clave,c3.clave,c4.clave,c5.clave) llave,
		c1.clave clv_etiquetado,c1.descripcion etiquetado,
		c2.clave clv_fuente_financiamiento,c2.descripcion fuente_financiamiento,
		c3.clave clv_ramo,c3.descripcion ramo,
		c4.clave clv_fondo_ramo,c4.descripcion fondo_ramo,
		c5.clave clv_capital,c5.descripcion capital
	from fondo f
	join catalogo c1 on f.etiquetado_id = c1.id
	join catalogo c2 on f.fuente_financiamiento_id = c2.id
	join catalogo c3 on f.ramo_id = c3.id
	join catalogo c4 on f.fondo_ramo_id = c4.id
	join catalogo c5 on f.capital_id = c5.id
	where f.deleted_at is null;
    
    set @query := concat(\"
    with epp_llaves as (
        select 
            ve.*,concat(clv_sector_publico,clv_sector_publico_f,clv_sector_economia,clv_subsector_economia,clv_ente_publico,clv_upp,clv_subsecretaria,clv_ur,clv_finalidad,clv_funcion,clv_subfuncion,clv_eje,clv_linea_accion,clv_programa_sectorial,clv_tipologia_conac,clv_programa,clv_subprograma,clv_proyecto) as llave
        from v_epp ve where deleted_at is null and ejercicio = \",anio,\"
    )
    select *
    from (
        select 'Sector Público' descripcion, vel.clv_sector_publico clave,vel.sector_publico concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Sector Público Financiero/No Financiero' descripcion, vel.clv_sector_publico_f clave,vel.sector_publico_f concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Sector Economía' descripcion, vel.clv_sector_economia clave,vel.sector_economia concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subsector Economía' descripcion,vel.clv_subsector_economia clave,vel.subsector_economia concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Ente Público' descripcion,vel.clv_ente_publico clave,vel.ente_publico concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Entidad Federativa' descripcion,vcg.clv_entidad_federativa clave,vcg.entidad_federativa concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Región' descripcion,vcg.clv_region clave,vcg.region concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Municipio' descripcion,vcg.clv_municipio clave,vcg.municipio concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Localidad' descripcion,vcg.clv_localidad clave,vcg.localidad concepto from clas_geo_llaves vcg where vcg.clasificacion_geografica_llave = '\",@clasGeo,\"' union all
        select 'Unidad Programática Presupuestal' descripcion,vel.clv_upp clave,vel.upp concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subsecretaría' descripcion,vel.clv_subsecretaria clave,vel.subsecretaria concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Unidad Responsable' descripcion,vel.clv_ur clave,vel.ur concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Finalidad' descripcion,vel.clv_finalidad clave,vel.finalidad concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Función' descripcion,vel.clv_funcion clave,vel.funcion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subfunción' descripcion,vel.clv_subfuncion clave,vel.subfuncion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Eje' descripcion,vel.clv_eje clave,vel.eje concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Linea de Acción' descripcion,vel.clv_linea_accion clave,vel.linea_accion concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Programa Sectorial' descripcion,vel.clv_programa_sectorial clave,vel.programa_sectorial concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Tipología General' descripcion,vel.clv_tipologia_conac clave,vel.clv_tipologia_conac concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Programa Presupuestal' descripcion,vel.clv_programa clave,vel.programa concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Subprograma Presupuestal' descripcion,vel.clv_subprograma clave,vel.subprograma concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Proyecto Presupuestal' descripcion,vel.clv_proyecto clave,vel.proyecto concepto from epp_llaves vel where vel.llave like '\",@epp,\"' union all
        select 'Mes de Afectación' descripcion,substring('\",@clave,\"',38,6) clave, 'Mes de Afectación' union all
        select 'Capítulo' descripcion,vppl.clv_capitulo clave,vppl.capitulo concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Concepto' descripcion,vppl.clv_concepto clave,vppl.concepto concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Partida Genérica' descripcion,vppl.clv_partida_generica clave,vppl.partida_generica concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Partida Específica' descripcion,vppl.clv_partida_especifica clave,vppl.partida_especifica concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Tipo de Gasto' descripcion,vppl.clv_tipo_gasto clave,vppl.tipo_gasto concepto from pos_pre_llaves vppl where vppl.posicion_presupuestaria_llave like '\",@partida,\"' union all
        select 'Año (Fondo del Ramo)' descripcion,substring('\",@clave,\"',50,2) clave, 'Año' concepto union all
        select 'Etiquetado/No Etiquetado' descripcion,vfl.clv_etiquetado clave,vfl.etiquetado concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Fuente de Financiamiento' descripcion,vfl.clv_fuente_financiamiento clave,vfl.fuente_financiamiento concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Ramo' descripcion,vfl.clv_ramo clave,vfl.ramo concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Fondo del Ramo' descripcion,vfl.clv_fondo_ramo clave,vfl.fondo_ramo concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Capital/Interes' descripcion,vfl.clv_capital clave,vfl.capital concepto from fondo_llaves vfl where vfl.llave like '\",@fondo,\"' union all
        select 'Proyecto de Obra' descripcion,po.clv_proyecto_obra clave,po.proyecto_obra from proyectos_obra po where deleted_at is null and po.clv_proyecto_obra like '\",@obra,\"'
    ) tabla;
    \");
    
    prepare stmt  from @query;
    execute stmt;
    deallocate prepare stmt;

	drop temporary table if exists clas_geo_llaves;
	drop temporary table if exists pos_pre_llaves;
	drop temporary table if exists fondo_llaves;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS conceptos_clave;");
    }
};
