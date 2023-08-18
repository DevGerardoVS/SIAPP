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
        Schema::dropIfExists('mml_arbol_problema');
        Schema::dropIfExists('mml_avance_etapas_pp');
        Schema::dropIfExists('mml_catalogos');
        Schema::dropIfExists('mml_objetivos_desarrollo_sostenible');
        Schema::dropIfExists('mml_arbol_objetivos');
        Schema::dropIfExists('mml_observaciones_pp');
        Schema::dropIfExists('mml_objetivo_sectorial_estrategia');
        Schema::dropIfExists('mml_mir');
        Schema::dropIfExists('mml_definicion_problema');
 

        Schema::create('mml_definicion_problema', function (Blueprint $table){
            $table->increments('id');
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->string('poblacion_Objetivo',255)->nullable(false);
            $table->string('descripcion',255)->nullable(false);
            $table->string('magnitud',255)->nullable(false);
            $table->string('necesidad_atender',255)->nullable(false);
            $table->integer('delimitacion_geografica')->nullable(false);
            $table->string('region',3)->nullable(false);
            $table->string('municipio',3)->nullable(false);
            $table->string('localidad',3)->nullable(false);
            $table->string('problema_central',255)->nullable(false);
            $table->string('objetivo_central',255)->nullable(false);
            $table->string('comentarios_upp',255)->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
           
             /* $table->foreign('upp_id')->references('id')->on('catalogo'); */ 
        });
        Schema::create('mml_arbol_problema', function (Blueprint $table){
            $table->increments('id');
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(true);
            $table->enum('tipo',['Efecto','Causa'])->nullable(true);
            $table->integer('padre_id')->nullable(true);
            $table->string('indice',10)->nullable(true);
            $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable(true);
            $table->string('descripcion',255)->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */

        });

        Schema::create('mml_arbol_objetivos', function (Blueprint $table){
            $table->increments('id');
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(true);
            $table->enum('tipo',['Fin','Medio'])->nullable(true);
            $table->integer('padre_id')->nullable(true);
            $table->string('indice',10)->nullable(true);
            $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable(true);
            $table->string('descripcion',255)->nullable(false);
            $table->integer('calificacion_id')->nullable(false);
            $table->tinyInteger('seleccion_mir')->nullable(false);
            $table->enum('tipo_indicador',['Componente','Actividad'])->nullable(true);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */

        });

        Schema::create('mml_observaciones_pp', function (Blueprint $table){
            $table->increments('id');
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->tinyInteger('etapa')->unsigned()->nullable(false);
            $table->string('comentario',255)->nullable(true);
            $table->string('ruta',200)->nullable(true);
            $table->string('nombre',70)->nullable(true);
            $table->integer('ejercicio')->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->softDeletes();

            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */
        });

        Schema::create('mml_objetivo_sectorial_estrategia', function (Blueprint $table){
            $table->increments('id');
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_objetivo_sectorial',6)->nullable(false);
            $table->text('objetivo_sectorial')->nullable(false);
            $table->string('clv_estrategia',9)->nullable(false);
            $table->text('estrategia')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */
        });

        Schema::create('mml_mir',function (Blueprint $table){
            $table->increments('id');
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_ur',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->integer('nivel')->nullable(false);
            $table->bigInteger('id_epp')->nullable(true);
            $table->Integer('componente_padre')->nullable(true);
            $table->text('objetivo')->nullable(false);
            $table->string('indicador',255)->nullable(false);
            $table->string('definicion_indicador',255)->nullable(false);
            $table->string('metodo_calculo',255)->nullable(false);
            $table->text('descripcion_metodo')->nullable(false);
            $table->integer('tipo_indicador')->nullable(false);
            $table->integer('unidad_medida')->nullable(false);
            $table->integer('dimension')->nullable(false);
            $table->integer('comportamiento_indicador')->nullable(false);
            $table->integer('frecuencia_medicion')->nullable(false);
            $table->text('medios_verificacion')->nullable(false);
            $table->string('lb_valor_absoluto',255)->nullable(false);
            $table->string('lb_valor_relativo',255)->nullable(false);
            $table->integer('lb_anio')->nullable(false);
            $table->enum('lb_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(false);
            $table->enum('lb_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(false);
            $table->string('mp_valor_absoluto',255)->nullable(false);
            $table->string('mp_valor_relativo',255)->nullable(false);
            $table->integer('mp_anio')->nullable(false);
            $table->integer('mp_anio_meta')->nullable(false);
            $table->integer('mp_periodo_i')->nullable(false);
            $table->integer('mp_periodo_f')->nullable(false);
            $table->text('supuestos')->nullable(false);
            $table->text('estrategias')->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */
        });



        Schema::create('mml_avance_etapas_pp', function (Blueprint $table){
            $table->increments('id');
            $table->string('upp_id',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->tinyInteger('etapa_0')->nullable(false);
            $table->tinyInteger('etapa_1')->nullable(false);
            $table->tinyInteger('etapa_2')->nullable(false);
            $table->tinyInteger('etapa_3')->nullable(false);
            $table->tinyInteger('etapa_4')->nullable(false);
            $table->tinyInteger('etapa_5')->nullable(false);
            $table->integer('estatus')->unsigned()->nullable(false);
            $table->integer('ejercicio')->nullable(false)->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->softDeletes();
    
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */
        });


        Schema::create('mml_objetivos_desarrollo_sostenible', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_estrategia',9)->nullable(false);
            $table->string('clv_plan_nacional',1)->nullable(false);
            $table->string('plan_nacional',255)->nullable(false);
            $table->string('clv_ods',2)->nullable(false);
            $table->text('ods')->nullable(false);
            $table->string('clv_objetivos_y_metas_ods',5)->nullable(false);
            $table->text('objetivos_y_metas_ods')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });


        Schema::create('mml_catalogos', function (Blueprint $table){
            $table->increments('id');
            $table->string('grupo',30)->nullable(false);
            $table->string('valor',50)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->softDeletes();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_avance_etapas_pp');
        Schema::dropIfExists('mml_catalogos');
        Schema::dropIfExists('mml_objetivos_desarrollo_sostenible');
        Schema::dropIfExists('mml_definicion_problema');
        Schema::dropIfExists('mml_arbol_problema');
        Schema::dropIfExists('mml_arbol_objetivos');
        Schema::dropIfExists('mml_observaciones_pp');
        Schema::dropIfExists('mml_objetivo_sectorial_estrategia');
        Schema::dropIfExists('mml_mir');

        
    }
};
