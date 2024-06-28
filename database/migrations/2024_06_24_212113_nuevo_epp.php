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
        Schema::dropIfExists('v2_entidad_ejecutora');
        Schema::dropIfExists('v2_epp');
        Schema::dropIfExists('v_clasificacion_geografica');
        Schema::dropIfExists('v_epp_llaves');
        Schema::dropIfExists('v_fondo_llaves');
        Schema::dropIfExists('pp_identificadores');
        Schema::dropIfExists('pp_ident_aux');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('posicion_presupuestaria');
        Schema::dropIfExists('epp_observaciones');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('ramo_33');
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('epp');
        Schema::dropIfExists('v_epp');
        Schema::dropIfExists('catalogo');

        if (!Schema::hasTable('catalogo')) {
            Schema::create('catalogo', function(Blueprint $table){
                $table->increments('id');
                $table->integer('ejercicio')->nullable();
                $table->enum('grupo_id', [
                    'SECTOR PÚBLICO','SECTOR PÚBLICO FINANCIERO/NO FINANCIERO','SECTOR DE ECONOMÍA','SUBSECTOR DE ECONOMÍA','ENTE PÚBLICO',
                    'UNIDAD PROGRAMÁTICA PRESUPUESTAL','SUBSECRETARÍA','UNIDAD RESPONSABLE','FINALIDAD','FUNCIÓN','SUBFUNCIÓN',
                    'EJE','LÍNEA DE ACCIÓN','PROGRAMA SECTORIAL','TIPOLOGÍA CONAC','PROGRAMA PRESUPUESTARIO','SUBPROGRAMA PRESUPUESTARIO',
                    'PROYECTO PRESUPUESTARIO','METAS_TIPO','ACTIVIDADES ADMON','FONDO FEDERAL','ENTIDAD FEDERATIVA','REGIÓN','MUNICIPIO',
                    'LOCALIDAD','OBJETIVO SECTORIAL','ESTRATEGIA','PADRE - TIPOLOGÍA CONAC','CAPÍTULO','CONCEPTO','PARTIDA GENÉRICA',
                    'PARTIDA ESPECÍFICA','TIPO DE GASTO','ETIQUETADO/NO ETIQUETADO','FUENTE DE FINANCIAMIENTO','RAMO',
                    'FONDO DEL RAMO','CAPITAL/INTERES','PROYECTO DE OBRA'
                ]);
                $table->string('clave',6)->nullable();
                $table->text('descripcion')->nullable();
                $table->string('descripcion_larga',43)->nullable();
                $table->string('descripcion_corta',22)->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }

        Schema::create('clasificacion_administrativa', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('sector_publico_id')->unsigned();
            $table->integer('sector_publico_f_id')->unsigned();
            $table->integer('sector_economia_id')->unsigned();
            $table->integer('subsector_economia_id')->unsigned();
            $table->integer('ente_publico_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('sector_publico_id')->references('id')->on('catalogo');
            $table->foreign('sector_publico_f_id')->references('id')->on('catalogo');
            $table->foreign('sector_economia_id')->references('id')->on('catalogo');
            $table->foreign('subsector_economia_id')->references('id')->on('catalogo');
            $table->foreign('ente_publico_id')->references('id')->on('catalogo');
        });

        Schema::create('clasificacion_geografica', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('entidad_federativa_id')->unsigned();
            $table->integer('region_id')->unsigned();
            $table->integer('municipio_id')->unsigned();
            $table->integer('localidad_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('entidad_federativa_id')->references('id')->on('catalogo');
            $table->foreign('region_id')->references('id')->on('catalogo');
            $table->foreign('municipio_id')->references('id')->on('catalogo');
            $table->foreign('localidad_id')->references('id')->on('catalogo');
        });

        Schema::create('entidad_ejecutora', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('upp_id')->unsigned();
            $table->integer('subsecretaria_id')->unsigned();
            $table->integer('ur_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('upp_id')->references('id')->on('catalogo');
            $table->foreign('subsecretaria_id')->references('id')->on('catalogo');
            $table->foreign('ur_id')->references('id')->on('catalogo');
        });

        Schema::create('clasificacion_funcional', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('finalidad_id')->unsigned();
            $table->integer('funcion_id')->unsigned();
            $table->integer('subfuncion_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('finalidad_id')->references('id')->on('catalogo');
            $table->foreign('funcion_id')->references('id')->on('catalogo');
            $table->foreign('subfuncion_id')->references('id')->on('catalogo');
        });

        Schema::create('pladiem', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('eje_id')->unsigned();
            $table->integer('objetivo_sectorial_id')->unsigned();
            $table->integer('estrategia_id')->unsigned();
            $table->integer('linea_accion_id')->unsigned();
            $table->integer('programa_sectorial_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('eje_id')->references('id')->on('catalogo');
            $table->foreign('objetivo_sectorial_id')->references('id')->on('catalogo');
            $table->foreign('estrategia_id')->references('id')->on('catalogo');
            $table->foreign('linea_accion_id')->references('id')->on('catalogo');
            $table->foreign('programa_sectorial_id')->references('id')->on('catalogo');
        });

        Schema::create('conac', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('padre_id')->unsigned();
            $table->integer('tipologia_conac_id')->nullable()->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('padre_id')->references('id')->on('catalogo');
            $table->foreign('tipologia_conac_id')->references('id')->on('catalogo');
        });

        Schema::create('fondo', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('etiquetado_id')->unsigned();
            $table->integer('fuente_financiamiento_id')->unsigned();
            $table->integer('ramo_id')->unsigned();
            $table->integer('fondo_ramo_id')->unsigned();
            $table->integer('capital_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('etiquetado_id')->references('id')->on('catalogo');
            $table->foreign('fuente_financiamiento_id')->references('id')->on('catalogo');
            $table->foreign('ramo_id')->references('id')->on('catalogo');
            $table->foreign('fondo_ramo_id')->references('id')->on('catalogo');
            $table->foreign('capital_id')->references('id')->on('catalogo');
        });

        Schema::create('clasificacion_economica', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio')->nullable();
            $table->integer('capitulo_id')->unsigned();
            $table->integer('concepto_id')->unsigned();
            $table->integer('partida_generica_id')->unsigned();
            $table->integer('partida_especifica_id')->unsigned();
            $table->integer('tipo_gasto_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('capitulo_id')->references('id')->on('catalogo');
            $table->foreign('concepto_id')->references('id')->on('catalogo');
            $table->foreign('partida_generica_id')->references('id')->on('catalogo');
            $table->foreign('partida_especifica_id')->references('id')->on('catalogo');
            $table->foreign('tipo_gasto_id')->references('id')->on('catalogo');
        });

        Schema::create('epp', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio');
            $table->tinyInteger('mes_i')->nullable();
            $table->tinyInteger('mes_f')->nullable();
            $table->integer('clasificacion_administrativa_id')->unsigned();
            $table->integer('entidad_ejecutora_id')->unsigned();
            $table->integer('clasificacion_funcional_id')->unsigned();
            $table->integer('pladiem_id')->unsigned();
            $table->integer('conac_id')->unsigned();
            $table->integer('programa_id')->unsigned();
            $table->integer('subprograma_id')->unsigned();
            $table->integer('proyecto_id')->unsigned();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('clasificacion_administrativa_id')->references('id')->on('clasificacion_administrativa');
            $table->foreign('entidad_ejecutora_id')->references('id')->on('entidad_ejecutora');
            $table->foreign('clasificacion_funcional_id')->references('id')->on('clasificacion_funcional');
            $table->foreign('pladiem_id')->references('id')->on('pladiem');
            $table->foreign('conac_id')->references('id')->on('conac');
            $table->foreign('programa_id')->references('id')->on('catalogo');
            $table->foreign('subprograma_id')->references('id')->on('catalogo');
            $table->foreign('proyecto_id')->references('id')->on('catalogo');
        });

        Schema::create('ramo_33', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ramo_fondo_id')->unsigned();
            $table->integer('fondo_federal_id')->unsigned();
            $table->integer('programa_id')->unsigned();
            $table->integer('ejercicio');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('ramo_fondo_id')->references('id')->on('fondo');
            $table->foreign('fondo_federal_id')->references('id')->on('catalogo');
            $table->foreign('programa_id')->references('id')->on('catalogo');
        });

        Schema::create('epp_observaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('epp_id')->unsigned();
            $table->integer('etapa')->default(0);
            $table->string('observacion',500);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('epp_id')->references('id')->on('epp');
        });

        Schema::create('upp_extras', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ejercicio');
            $table->integer('upp_id')->unsigned();
            $table->integer('clasificacion_administrativa_id')->unsigned();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('upp_id')->references('id')->on('catalogo');
            $table->foreign('clasificacion_administrativa_id')->references('id')->on('clasificacion_administrativa');
        });

        Schema::create('v_epp', function(Blueprint $table){
            $table->increments('id');
            $table->integer('ejercicio');
            $table->tinyInteger('mes_i')->nullable();
            $table->tinyInteger('mes_f')->nullable();
            $table->string('clv_sector_publico',1);
            $table->text('sector_publico');
            $table->string('clv_sector_publico_f',1);
            $table->text('sector_publico_f');
            $table->string('clv_sector_economia',1);
            $table->text('sector_economia');
            $table->string('clv_subsector_economia',1);
            $table->text('subsector_economia');
            $table->string('clv_ente_publico',1);
            $table->text('ente_publico');
            $table->string('clv_upp',3);
            $table->text('upp');
            $table->string('clv_subsecretaria',1);
            $table->text('subsecretaria');
            $table->string('clv_ur',2);
            $table->text('ur');
            $table->string('clv_finalidad',1);
            $table->text('finalidad');
            $table->string('clv_funcion',1);
            $table->text('funcion');
            $table->string('clv_subfuncion',1);
            $table->text('subfuncion');
            $table->string('clv_eje',1);
            $table->text('eje');
            $table->string('clv_linea_accion',2);
            $table->text('linea_accion');
            $table->string('clv_programa_sectorial',1);
            $table->text('programa_sectorial');
            $table->string('clv_tipologia_conac',1);
            $table->text('tipologia_conac');
            $table->string('clv_programa',2);
            $table->text('programa');
            $table->string('clv_subprograma',3);
            $table->text('subprograma');
            $table->string('clv_proyecto',3);
            $table->text('proyecto');
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
        });

        Schema::create('archivos_epp', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('upp_id')->unsigned();
            $table->integer('ejercicio')->nullable(false);
            $table->string('nombre',100);
            $table->string('ruta',100);
            $table->text('acciones');
            $table->tinyInteger('estatus')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user', 45);
            $table->string('updated_user', 45)->nullable();
            $table->string('deleted_user', 45)->nullable();
            $table->foreign('upp_id')->references('id')->on('catalogo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v2_entidad_ejecutora');
        Schema::dropIfExists('v2_epp');
        Schema::dropIfExists('v_clasificacion_geografica');
        Schema::dropIfExists('v_epp_llaves');
        Schema::dropIfExists('v_fondo_llaves');
        Schema::dropIfExists('pp_identificadores');
        Schema::dropIfExists('pp_ident_aux');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('epp');
        Schema::dropIfExists('catalogo');
    }
};
