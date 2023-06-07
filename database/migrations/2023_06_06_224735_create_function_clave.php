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
         DB::unprepared("CREATE function clave() returns VARCHAR(255) DETERMINISTIC NO SQL return @clave;

         create view clave_pres1 as 
         select
             `tabla`.`id` as `id`
         from
             (
             select
                 `ca`.`sector_publico_id` as `id`
             from
                 `fondos_db`.`clasificacion_administrativa` `ca`
             where
                 `ca`.`id` in (
                 select
                     `pp`.`clasificacion_administrativa_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ca`.`sector_publico_fnof_id` as `id`
             from
                 `fondos_db`.`clasificacion_administrativa` `ca`
             where
                 `ca`.`id` in (
                 select
                     `pp`.`clasificacion_administrativa_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ca`.`sector_economia_id` as `id`
             from
                 `fondos_db`.`clasificacion_administrativa` `ca`
             where
                 `ca`.`id` in (
                 select
                     `pp`.`clasificacion_administrativa_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ca`.`subsector_economia_id` as `id`
             from
                 `fondos_db`.`clasificacion_administrativa` `ca`
             where
                 `ca`.`id` in (
                 select
                     `pp`.`clasificacion_administrativa_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ca`.`ente_publico_id` as `id`
             from
                 `fondos_db`.`clasificacion_administrativa` `ca`
             where
                 `ca`.`id` in (
                 select
                     `pp`.`clasificacion_administrativa_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `cg`.`entidad_federativa_id` as `id`
             from
                 `fondos_db`.`clasificacion_geografica` `cg`
             where
                 `cg`.`id` in (
                 select
                     `pp`.`clasificacion_geografica_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `cg`.`region_id` as `id`
             from
                 `fondos_db`.`clasificacion_geografica` `cg`
             where
                 `cg`.`id` in (
                 select
                     `pp`.`clasificacion_geografica_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `cg`.`municipio_id` as `id`
             from
                 `fondos_db`.`clasificacion_geografica` `cg`
             where
                 `cg`.`id` in (
                 select
                     `pp`.`clasificacion_geografica_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `cg`.`localidad_id` as `id`
             from
                 `fondos_db`.`clasificacion_geografica` `cg`
             where
                 `cg`.`id` in (
                 select
                     `pp`.`clasificacion_geografica_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ee`.`upp_id` as `id`
             from
                 `fondos_db`.`entidad_ejecutora` `ee`
             where
                 `ee`.`id` in (
                 select
                     `pp`.`entidad_ejecutora_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ee`.`subsecretaria_id` as `id`
             from
                 `fondos_db`.`entidad_ejecutora` `ee`
             where
                 `ee`.`id` in (
                 select
                     `pp`.`entidad_ejecutora_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `ee`.`ur_id` as `id`
             from
                 `fondos_db`.`entidad_ejecutora` `ee`
             where
                 `ee`.`id` in (
                 select
                     `pp`.`entidad_ejecutora_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`finalidad_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`funcion_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`subfuncion_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`eje_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`linea_accion_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`programa_sectorial_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`tipologia_conac_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`programa_presupuestario_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`subprograma_presupuestario_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `af`.`proyecto_presupuestario_id` as `id`
             from
                 `fondos_db`.`area_funcional` `af`
             where
                 `af`.`id` in (
                 select
                     `pp`.`area_funcional_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `pp`.`capitulo_id` as `id`
             from
                 `fondos_db`.`posicion_presupuestaria` `pp`
             where
                 `pp`.`id` in (
                 select
                     `pp`.`posicion_presupuestaria_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `pp`.`concepto_id` as `id`
             from
                 `fondos_db`.`posicion_presupuestaria` `pp`
             where
                 `pp`.`id` in (
                 select
                     `pp`.`posicion_presupuestaria_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `pp`.`partida_generica_id` as `id`
             from
                 `fondos_db`.`posicion_presupuestaria` `pp`
             where
                 `pp`.`id` in (
                 select
                     `pp`.`posicion_presupuestaria_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `pp`.`partida_especifica_id` as `id`
             from
                 `fondos_db`.`posicion_presupuestaria` `pp`
             where
                 `pp`.`id` in (
                 select
                     `pp`.`posicion_presupuestaria_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `c`.`id` as `id`
             from
                 `fondos_db`.`catalogo` `c`
             where
                 `c`.`id` in (
                 select
                     `pp`.`tipo_gasto_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `f`.`etiquetado_id` as `id`
             from
                 `fondos_db`.`fondo` `f`
             where
                 `f`.`id` in (
                 select
                     `pp`.`fondo_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `f`.`fuente_financiamiento_id` as `id`
             from
                 `fondos_db`.`fondo` `f`
             where
                 `f`.`id` in (
                 select
                     `pp`.`fondo_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `f`.`ramo_id` as `id`
             from
                 `fondos_db`.`fondo` `f`
             where
                 `f`.`id` in (
                 select
                     `pp`.`fondo_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `f`.`fondo_ramo_id` as `id`
             from
                 `fondos_db`.`fondo` `f`
             where
                 `f`.`id` in (
                 select
                     `pp`.`fondo_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `f`.`capital_id` as `id`
             from
                 `fondos_db`.`fondo` `f`
             where
                 `f`.`id` in (
                 select
                     `pp`.`fondo_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))
         union all
             select
                 `c`.`id` as `id`
             from
                 `fondos_db`.`catalogo` `c`
             where
                 `c`.`id` in (
                 select
                     `pp`.`proyecto_presupuestal_id`
                 from
                     `fondos_db`.`programacion_presupuesto` `pp`
                 where
                     (`pp`.`clave_presupuestal` like `clave`()))) `tabla`;"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
