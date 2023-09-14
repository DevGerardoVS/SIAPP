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
        DB::unprepared("CREATE VIEW v_epp AS
        select 
            e.id,
            e.con_mir AS con_mir,
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
            e.ejercicio,
            e.deleted_at,
            e.updated_at,
            e.created_at
        from epp e
        join catalogo c01 on e.sector_publico_id = c01.id 
        join catalogo c02 on e.sector_publico_f_id = c02.id 
        join catalogo c03 on e.sector_economia_id = c03.id 
        join catalogo c04 on e.subsector_economia_id = c04.id 
        join catalogo c05 on e.ente_publico_id = c05.id 
        join catalogo c06 on e.upp_id = c06.id 
        join catalogo c07 on e.subsecretaria_id = c07.id  
        join catalogo c08 on e.ur_id = c08.id 
        join catalogo c09 on e.finalidad_id = c09.id 
        join catalogo c10 on e.funcion_id = c10.id 
        join catalogo c11 on e.subfuncion_id = c11.id 
        join catalogo c12 on e.eje_id = c12.id 
        join catalogo c13 on e.linea_accion_id = c13.id 
        join catalogo c14 on e.programa_sectorial_id = c14.id 
        join catalogo c15 on e.tipologia_conac_id = c15.id 
        join catalogo c16 on e.programa_id = c16.id 
        join catalogo c17 on e.subprograma_id = c17.id 
        join catalogo c18 on e.proyecto_id = c18.id");

        DB::unprepared("CREATE VIEW v_entidad_ejecutora as
        select 
            ee.id,
            c.clave clv_upp,
            c.descripcion upp,
            c2.clave clv_subsecretaria,
            c2.descripcion subsecretaria,
            c3.clave clv_ur,
            c3.descripcion ur,
            ee.deleted_at,
            ee.updated_at,
            ee.created_at,
            ee.deleted_user,
            ee.updated_user,
            ee.created_user
        from entidad_ejecutora ee
        join catalogo c on ee.upp_id = c.id
        join catalogo c2 on ee.subsecretaria_id = c2.id
        join catalogo c3 on ee.ur_id = c3.id;");

        DB::unprepared("CREATE VIEW v_epp_llaves as
        select 
            ve.*,
            concat(
                ve.clv_sector_publico,
                ve.clv_sector_publico_f,
                ve.clv_sector_economia,
                ve.clv_subsector_economia,
                ve.clv_ente_publico,
                ve.clv_upp,
                ve.clv_subsecretaria,
                ve.clv_ur,
                ve.clv_finalidad,
                ve.clv_funcion,
                ve.clv_subfuncion,
                ve.clv_eje,
                ve.clv_linea_accion,
                ve.clv_programa_sectorial,
                ve.clv_tipologia_conac,
                ve.clv_programa,
                ve.clv_subprograma,
                ve.clv_proyecto
            ) as llave
        from v_epp ve;");

        DB::unprepared("CREATE VIEW v_programacion_presupuesto_llaves as
        select 
            pp.id,
            concat(
                pp.clasificacion_administrativa,
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
            ) as epp_llave,
            ((concat(
                pp.entidad_federativa,
                pp.region,
                pp.municipio,
                pp.localidad
            ))*1) as clas_geo_llave,
            pp.periodo_presupuestal,
            ((concat(
                pp.posicion_presupuestaria,
                pp.tipo_gasto
            ))*1) as posicion_presupuestaria_llave,
            pp.anio,
            concat(
                pp.etiquetado,
                pp.fuente_financiamiento,
                pp.ramo,
                pp.fondo_ramo,
                pp.capital
            ) as fondo_llave,
            pp.proyecto_obra,
            pp.ejercicio,
            enero,febrero,marzo,abril,mayo,
            junio,julio,agosto,septiembre,
            octubre,noviembre,diciembre,
            pp.total,
            pp.estado,
            pp.tipo,
            pp.deleted_at,
            pp.updated_at,
            pp.created_at
        from programacion_presupuesto pp;");

        DB::unprepared("CREATE VIEW v_clasificacion_geografica as
        select
            cg.*,
            ((concat(
                cg.clv_entidad_federativa,
                cg.clv_region,
                cg.clv_municipio,
                cg.clv_localidad
            ))*1) as clasificacion_geografica_llave
        from clasificacion_geografica cg;");

        DB::unprepared("CREATE VIEW v_posicion_presupuestaria_llaves as
        select 
            pp.*,
            ((concat(
                pp.clv_capitulo,
                pp.clv_concepto,
                pp.clv_partida_generica,
                pp.clv_partida_especifica,
                pp.clv_tipo_gasto
            ))*1) as posicion_presupuestaria_llave
        from posicion_presupuestaria pp;");

        DB::unprepared("CREATE VIEW v_fondo_llaves as
        select 
            f.*,
            concat(
                f.clv_etiquetado,
                f.clv_fuente_financiamiento,
                f.clv_ramo,
                f.clv_fondo_ramo,
                f.clv_capital
            ) as llave
        from fondo f;");

        DB::unprepared("CREATE VIEW pp_aplanado as
        select 
            tp.id,
            ve.clv_sector_publico,
            ve.sector_publico,
            ve.clv_sector_publico_f,
            ve.sector_publico_f,
            ve.clv_sector_economia,
            ve.sector_economia,
            ve.clv_subsector_economia,
            ve.subsector_economia,
            ve.clv_ente_publico,
            ve.ente_publico,
            cg.clv_entidad_federativa,
            cg.entidad_federativa,
            cg.clv_region,
            cg.region,
            cg.clv_municipio,
            cg.municipio,
            cg.clv_localidad,
            cg.localidad,
            ve.clv_upp,
            ve.upp,
            ve.clv_subsecretaria,
            ve.subsecretaria,
            ve.clv_ur,
            ve.ur,
            ve.clv_finalidad,
            ve.finalidad,
            ve.clv_funcion,
            ve.funcion,
            ve.clv_subfuncion,
            ve.subfuncion,
            ve.clv_eje,
            ve.eje,
            ve.clv_linea_accion,
            ve.linea_accion,
            ve.clv_programa_sectorial,
            ve.programa_sectorial,
            ve.clv_tipologia_conac,
            ve.tipologia_conac,
            ve.clv_programa,
            ve.programa,
            ve.clv_subprograma,
            ve.subprograma,
            ve.clv_proyecto,
            ve.proyecto,
            pp.periodo_presupuestal,
            pp2.clv_capitulo,
            pp2.capitulo,
            pp2.clv_concepto,
            pp2.concepto,
            pp2.clv_partida_generica,
            pp2.partida_generica,
            pp2.clv_partida_especifica,
            pp2.partida_especifica,
            pp2.clv_tipo_gasto,
            pp2.tipo_gasto,
            pp.anio,
            f.clv_etiquetado,
            f.etiquetado,
            f.clv_fuente_financiamiento,
            f.fuente_financiamiento,
            f.clv_ramo,
            f.ramo,
            f.clv_fondo_ramo,
            f.fondo_ramo,
            f.clv_capital,
            f.capital,
            po.clv_proyecto_obra,
            po.proyecto_obra,
            pp.ejercicio,
            enero,
            febrero,
            marzo,
            abril,
            mayo,
            junio,
            julio,
            agosto,
            septiembre,
            octubre,
            noviembre,
            diciembre,
            pp.total,
            pp.estado,
            pp.tipo,
            pp.deleted_at,
            pp.updated_at,
            pp.created_at,
            pp.deleted_user,
            pp.updated_user,
            pp.created_user
        from pp_identificadores tp
        join programacion_presupuesto pp on tp.id = pp.id
        join v_epp ve on tp.epp_id = ve.id
        join clasificacion_geografica cg on tp.clas_geo_id = cg.id
        join posicion_presupuestaria pp2 on tp.pos_pre_id = pp2.id 
        join fondo f on tp.fondo_id = f.id 
        join proyectos_obra po on tp.obra_id = po.id;");

        DB::unprepared("CREATE VIEW pp_aplanado_hist as
        select 
            pp.id,
            pp.id_original,
            pp.version,
            ve.clv_sector_publico,
            ve.sector_publico,
            ve.clv_sector_publico_f,
            ve.sector_publico_f,
            ve.clv_sector_economia,
            ve.sector_economia,
            ve.clv_subsector_economia,
            ve.subsector_economia,
            ve.clv_ente_publico,
            ve.ente_publico,
            cg.clv_entidad_federativa,
            cg.entidad_federativa,
            cg.clv_region,
            cg.region,
            cg.clv_municipio,
            cg.municipio,
            cg.clv_localidad,
            cg.localidad,
            ve.clv_upp,
            ve.upp,
            ve.clv_subsecretaria,
            ve.subsecretaria,
            ve.clv_ur,
            ve.ur,
            ve.clv_finalidad,
            ve.finalidad,
            ve.clv_funcion,
            ve.funcion,
            ve.clv_subfuncion,
            ve.subfuncion,
            ve.clv_eje,
            ve.eje,
            ve.clv_linea_accion,
            ve.linea_accion,
            ve.clv_programa_sectorial,
            ve.programa_sectorial,
            ve.clv_tipologia_conac,
            ve.tipologia_conac,
            ve.clv_programa,
            ve.programa,
            ve.clv_subprograma,
            ve.subprograma,
            ve.clv_proyecto,
            ve.proyecto,
            pp.periodo_presupuestal,
            pp2.clv_capitulo,
            pp2.capitulo,
            pp2.clv_concepto,
            pp2.concepto,
            pp2.clv_partida_generica,
            pp2.partida_generica,
            pp2.clv_partida_especifica,
            pp2.partida_especifica,
            pp2.clv_tipo_gasto,
            pp2.tipo_gasto,
            pp.anio,
            f.clv_etiquetado,
            f.etiquetado,
            f.clv_fuente_financiamiento,
            f.fuente_financiamiento,
            f.clv_ramo,
            f.ramo,
            f.clv_fondo_ramo,
            f.fondo_ramo,
            f.clv_capital,
            f.capital,
            po.clv_proyecto_obra,
            po.proyecto_obra,
            pp.ejercicio,
            enero,
            febrero,
            marzo,
            abril,
            mayo,
            junio,
            julio,
            agosto,
            septiembre,
            octubre,
            noviembre,
            diciembre,
            pp.total,
            pp.estado,
            pp.tipo,
            pp.deleted_at,
            pp.updated_at,
            pp.created_at,
            pp.deleted_user,
            pp.updated_user,
            pp.created_user
        from pp_identificadores tp
        join programacion_presupuesto_hist pp on tp.id = pp.id_original
        join v_epp ve on tp.epp_id = ve.id
        join clasificacion_geografica cg on tp.clas_geo_id = cg.id
        join posicion_presupuestaria pp2 on tp.pos_pre_id = pp2.id 
        join fondo f on tp.fondo_id = f.id 
        join proyectos_obra po on tp.obra_id = po.id;");

        DB::unprepared("CREATE VIEW inicio_a AS
        select
            sum(presupuesto_asignado) presupuesto_asignado,
            sum(presupuesto_calendarizado) presupuesto_calendarizado,
            sum(presupuesto_asignado) - sum(presupuesto_calendarizado) as disponible,
            (sum(presupuesto_calendarizado) / sum(presupuesto_asignado)) * 100 as avance,
            ejercicio
        FROM (
            select 
                sum(presupuesto) as presupuesto_asignado,
                0 as presupuesto_calendarizado,
                ejercicio
            from techos_financieros
            where deleted_at is null
            group by ejercicio
            union all
            select 
                0 as presupuesto_asignado,
                sum(total) as presupuesto_calendarizado,
                ejercicio
            from programacion_presupuesto
            where deleted_at is null
            group by ejercicio
        )t 
        group by ejercicio;");


        DB::unprepared("CREATE VIEW inicio_b AS
        select 
            clv_fondo_ramo clave,
            fondo_ramo fondo,
            sum(asignado) asignado,
            sum(programado) programado,
            (sum(programado)/sum(asignado))*100 avance,
            ejercicio
        from (
            select 
                pa.clv_fondo_ramo,
                fondo_ramo,
                0 asignado,
                sum(pa.total) programado,
                pa.ejercicio
            from pp_aplanado pa
            where deleted_at is null
            group by clv_fondo_ramo,fondo_ramo,ejercicio
            union all
            select 
                tf.clv_fondo,
                f.fondo_ramo,
                sum(tf.presupuesto) asignado,
                0 programado,
                ejercicio
            from techos_financieros tf
            left join fondo f on tf.clv_fondo = f.clv_fondo_ramo
            where tf.deleted_at is null 
            group by clv_fondo,fondo_ramo,ejercicio
        )t group by clv_fondo_ramo,fondo_ramo,ejercicio;");

        DB::unprepared("CREATE VIEW v_sector_linea_accion AS 
        select 
            sla.id,
            c.clave clv_linea_accion,
            c.descripcion linea_accion,
              sla.clv_sector,
            sla.sector
        from sector_linea_accion sla
        join catalogo c on sla.linea_accion_id = c.id;");

        DB::unprepared("CREATE VIEW v_sector_importe AS 
        select 
            sla.sector concepto,
            pp.total importe,
            pp.ejercicio,
            pp.deleted_at
        from programacion_presupuesto pp 
        join v_sector_linea_accion sla on pp.linea_accion = sla.clv_linea_accion;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared("DROP VIEW IF EXISTS inicio_a");
        DB::unprepared("DROP VIEW IF EXISTS inicio_b");
        DB::unprepared("DROP VIEW IF EXISTS pp_aplanado");
        DB::unprepared("DROP VIEW IF EXISTS v_clasificacion_geografica");
        DB::unprepared("DROP VIEW IF EXISTS v_entidad_ejecutora");
        DB::unprepared("DROP VIEW IF EXISTS v_epp");
        DB::unprepared("DROP VIEW IF EXISTS v_epp_llaves");
        DB::unprepared("DROP VIEW IF EXISTS v_fondo_llaves");
        DB::unprepared("DROP VIEW IF EXISTS v_posicion_presupuestaria_llaves");
        DB::unprepared("DROP VIEW IF EXISTS v_programacion_presupuesto_llaves");
        DB::unprepared("DROP VIEW IF EXISTS v_sector_linea_accion");
        DB::unprepared("DROP VIEW IF EXISTS v_sector_importe");
    }
};