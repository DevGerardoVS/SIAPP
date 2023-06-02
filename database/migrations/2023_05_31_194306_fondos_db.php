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
        Schema::create('area_funcional', function (Blueprint $table){
            $table->increments('id')->nullable(false);
            $table->integer('finalidad_id')->nullable(false);
            $table->integer('funcion_id')->nullable(false);
            $table->integer('subfuncion_id')->nullable(false);
            $table->integer('eje_id')->nullable(false);
            $table->integer('linea_accion_id')->nullable(false);
            $table->integer('programa_sectorial_id')->nullable(false);
            $table->integer('subprograma_sectorial_id')->nullable(false);
            $table->integer('proyecto_presupuestario_id')->nullable(false);
            $table->string('llave',16)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            /*$table->foreign('eje_id')->references('id')->on('catalogo');
            $table->foreign('finalidad_id')->references('id')->on('catalogo');*/
        });

        Schema::create('area_funcional_entidad_ejecutora', function (Blueprint $table){
            $table->increments('id');
            $table->integer('area_funcional_id')->nullable(false);
            $table->integer('entidad_ejecutora_id')->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            /*$table->foreign('area_funcional_id')->references('id')->on('area_funcional');
            $table->foreign('entidad_ejecutora_id')->references('id')->on('entidad_ejecutora');*/
        });

        Schema::create('catalogo', function (Blueprint $table){
            $table->increments('id');
            $table->integer('subgrupo_id')->nullable(false);
            $table->string('clave',6)->nullable(false);
            $table->text('descripcion')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            /*$table->foreign('subgrupo_id')->references('id')->on('subgrupos');*/
        });

        Schema::create('clasificacion_administrativa', function (Blueprint $table){
            $table->increments('id');
            $table->integer('sector_publico_id')->nullable(false);
            $table->integer('sector_publico_fnof_id')->nullable(false);
            $table->integer('sector_economia_id')->nullable(false);
            $table->integer('subsector_economia_id')->nullable(false);
            $table->integer('ente_publico_id')->nullable(false);
            $table->string('llave',5)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            /**/
        });

        Schema::create('clasificacion_geografica', function (Blueprint $table){
            $table->increments('id');
            $table->integer('entidad_federativa_id')->nullable(false);
            $table->integer('region_id')->nullable(false);
            $table->integer('municipio_id')->nullable(false);
            $table->integer('localidad_id')->nullable(false);
            $table->string('llave',10)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            /**/
        });

        Schema::create('ente_publico_upp', function (Blueprint $table){
            $table->increments('id');
            $table->integer('clasificacion_administrativa_id')->nullable(false);
            $table->integer('entidad_ejecutora_id')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('entidad_ejecutora', function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->nullable(false);
            $table->integer('subsecretaria_id')->nullable(false);
            $table->integer('ur_id')->nullable(false);
            $table->string('llave',6)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('fondo', function (Blueprint $table){
            $table->increments('id');
            $table->integer('etiquetado_id')->nullable(false);
            $table->integer('ramo_id')->nullable(false);
            $table->integer('fondo_ramo_id')->nullable(false);
            $table->integer('capital_id')->nullable(false);
            $table->string('llave',7)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('grupos', function (Blueprint $table){
            $table->increments('id');
            $table->text('grupo',255)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('partida_upp', function (Blueprint $table){
            $table->integer('posicion_presupuestaria_id')->nullable(false);
            $table->string('posicion_presupuestaria_llave',5)->default(null);
            $table->integer('upp_id')->nullable(false);
            $table->string('upp_clave',3)->default(null);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('posicion_presupuestaria', function (Blueprint $table){
            $table->increments('id');
            $table->increments('capitulo_id')->nullable(false);
            $table->increments('concepto_id')->nullable(false);
            $table->increments('partida_generica_id')->nullable(false);
            $table->increments('partida_especifica_id')->nullable(false);
            $table->string('llave',5)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('presupuesto_upp_asignado',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->nullable(false);
            $table->integer('fondo_id')->nullable(false);
            $table->integer('presupuesto_id')->nullable(false);
            $table->text('tipo',25)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('programacion_presupuesto',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clasificacion_administrativa_id')->nullable(false);
            $table->integer('clasificacion_geografica_id')->nullable(false);
            $table->integer('entidad_ejecutora_id')->nullable(false);
            $table->integer('area_funcional_id')->nullable(false);
            $table->string('mes_afectacion',6)->nullable(false);
            $table->integer('posicion_presupuestaria_id')->nullable(false);
            $table->integer('tipo_gasto_id')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->integer('etiquetado_id')->nullable(false);
            $table->integer('fondo_id')->nullable(false);
            $table->integer('proyecto_presupuestal_id')->nullable(false);
            $table->float('enero')->default(null);
            $table->float('febrero')->default(null);
            $table->float('marzo')->default(null);
            $table->float('abril')->default(null);
            $table->float('mayo')->default(null);
            $table->float('junio')->default(null);
            $table->float('julio')->default(null);
            $table->float('agosto')->default(null);
            $table->float('septiembre')->default(null);
            $table->float('octubre')->default(null);
            $table->float('noviembre')->default(null);
            $table->float('diciembre')->default(null);
            $table->integer('unidad_medida_id')->nullable(false);
            $table->integer('beneficiarios_id')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('subgrupos',function (Blueprint $table){
            $table->increments('id');
            $table->integer('grupo_id')->nullable(false);
            $table->integer('largo_id')->nullable(false);
            $table->text('subgrupo',255)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('tipologia_conac',function (Blueprint $table){
            $table->increments('id');
            $table->integer('padre_id')->nullable(false);
            $table->integer('hijo_id')->default(null);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('ur_localidad',function (Blueprint $table){
            $table->increments('ur_id');
            $table->integer('localidad_id')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

        Schema::create('upp_fondo_montos',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->nullable(false);
            $table->integer('fondo_id')->nullable(false);
            $table->enum('tipo')->nullable(false);
            $table->integer('monto')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

        Schema::create('unidades_medida',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave')->nullable(false);
            $table->text('unidad_medida',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

        Schema::create('beneficiarios',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave')->nullable(false);
            $table->text('beneficiario',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

        Schema::create('administracion_captura',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->nullable(false);
            $table->enum('estatus')->nullable(false);
            $table->integer('usuario')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_funcional');
        Schema::dropIfExists('area_funcional_entidad_ejecutora');
        Schema::dropIfExists('catalogo');
        Schema::dropIfExists('clasificacion_administrativa');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('ente_publico_upp');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('partida_upp');
        Schema::dropIfExists('posicion_presupuestaria');
        Schema::dropIfExists('presupuesto_upp_asignado');
        Schema::dropIfExists('programacion_presupuesto');
        Schema::dropIfExists('subgrupos');
        Schema::dropIfExists('tipologia_conac');
        Schema::dropIfExists('ur_localidad');
        Schema::dropIfExists('upp_fondo_montos');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('administracion_captura');

    }
};
