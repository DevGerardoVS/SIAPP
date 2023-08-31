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
            $table->string('clv_upp',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->string('poblacion_objetivo',255)->nullable(false);
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
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
           
             /* $table->foreign('upp_id')->references('id')->on('catalogo'); */ 
        });
        Schema::create('mml_arbol_problema', function (Blueprint $table){
            $table->increments('id');
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->string('clv_upp',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(true);
            $table->enum('tipo',['Efecto','Causa'])->nullable(true);
            $table->integer('padre_id')->nullable(true);
            $table->string('indice',10)->nullable(true);
            $table->enum('tipo_objeto',['Superior','Directo','Indirecto'])->nullable(true);
            $table->string('descripcion',255)->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */

        });

        Schema::create('mml_arbol_objetivos', function (Blueprint $table){
            $table->increments('id');
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->string('clv_upp',4)->nullable(true);
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
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */

        });

        Schema::create('mml_observaciones_pp', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->integer('problema_id')->unsigned()->nullable(false);
            $table->tinyInteger('etapa')->unsigned()->nullable(false);
            $table->string('comentario',255)->nullable(true);
            $table->string('ruta',200)->nullable(true);
            $table->string('nombre',70)->nullable(true);
            $table->integer('ejercicio')->nullable(true);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('problema_id')->references('id')->on('mml_definicion_problema');
/*             $table->foreign('clv_upp')->references('clv_upp')->on('catalogo');
 */        });

        Schema::create('mml_objetivo_sectorial_estrategia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_objetivo_sectorial', 6)->nullable(false);
            $table->text('objetivo_sectorial')->nullable(false);
            $table->string('clv_estrategia', 9)->nullable(false);
            $table->text('estrategia')->nullable(false);
            $table->string('clv_cpladem_linea_accion', 12)->nullable(false);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::create('mml_mir',function (Blueprint $table){
            $table->increments('id');
            $table->string('entidad_ejecutora',6)->nullable(true);
            $table->string('area_funcional',16)->nullable(true);
            $table->string('clv_upp',4)->nullable(true);
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
            $table->enum('lb_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(true);
            $table->enum('lb_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(true);
            $table->string('mp_valor_absoluto',255)->nullable(false);
            $table->string('mp_valor_relativo',255)->nullable(false);
            $table->integer('mp_anio')->nullable(false);
            $table->integer('mp_anio_meta')->nullable(true);
            $table->enum('mp_periodo_i',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(true);
            $table->enum('mp_periodo_f',['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Novimebre','Diciembre'])->nullable(true);
            $table->text('supuestos')->nullable(false);
            $table->text('estrategias')->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            /* $table->foreign('upp_id')->references('id')->on('catalogo'); */
        });



        Schema::create('mml_avance_etapas_pp', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',4)->nullable(true);
            $table->string('clv_pp',5)->nullable(false);
            $table->tinyInteger('etapa_0')->nullable(false);
            $table->tinyInteger('etapa_1')->nullable(false);
            $table->tinyInteger('etapa_2')->nullable(false);
            $table->tinyInteger('etapa_3')->nullable(false);
            $table->tinyInteger('etapa_4')->nullable(false);
            $table->tinyInteger('etapa_5')->nullable(false);
            $table->integer('estatus')->unsigned()->nullable(false);
            $table->integer('ejercicio')->nullable(false)->default(0);
            $table->string('nombre_minuta',15)->nullable(true);
            $table->string('ruta',50)->nullable(true);
            $table->string('extension',4)->nullable(true);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
           
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
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
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
        Schema::create('mml_cierre_ejercicio', function (Blueprint $table){
            $table->increments('id')->nullable(false);
            $table->string('clv_upp',30)->nullable(false);
            $table->enum('estatus', ['Cerrado', 'Abierto'])->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            
        });

        Schema::create('grupos', function (Blueprint $table){
            $table->increments('id');
            $table->string('grupo',100)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('catalogo', function (Blueprint $table){
            $table->increments('id');
            $table->integer('grupo_id')->unsigned()->nullable(false);
            $table->string('clave',6)->nullable(false);
            $table->text('descripcion')->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('grupo_id')->references('id')->on('grupos');
        });

        Schema::create('entidad_ejecutora', function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->integer('subsecretaria_id')->unsigned()->nullable(false);
            $table->integer('ur_id')->unsigned()->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('clasificacion_geografica', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_entidad_federativa',2)->nullable(false);
            $table->string('entidad_federativa',255)->nullable(false);
            $table->string('clv_region',2)->nullable(false);
            $table->string('region',255)->nullable(false);
            $table->string('clv_municipio',3)->nullable(false);
            $table->string('municipio',255)->nullable(false);
            $table->string('clv_localidad',3)->nullable(false);
            $table->string('localidad',255)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('fondo', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_etiquetado',1)->nullable(false);
            $table->string('etiquetado', 255)->nullable(false);
            $table->string('clv_fuente_financiamiento',1)->nullable(false);
            $table->string('fuente_financiamiento', 255)->nullable(false);
            $table->string('clv_ramo',2)->nullable(false);
            $table->string('ramo', 255)->nullable(false);
            $table->string('clv_fondo_ramo',2)->nullable(false);
            $table->string('fondo_ramo', 255)->nullable(false);
            $table->string('clv_capital',1)->nullable(false);
            $table->string('capital', 255)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('posicion_presupuestaria', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_capitulo',1)->nullable(false);
            $table->string('capitulo',255)->nullable(false);
            $table->string('clv_concepto',1)->nullable(false);
            $table->string('concepto',255)->nullable(false);
            $table->string('clv_partida_generica',1)->nullable(false);
            $table->string('partida_generica',255)->nullable(false);
            $table->string('clv_partida_especifica',2)->nullable(false);
            $table->string('partida_especifica',255)->nullable(false);
            $table->string('clv_tipo_gasto',2)->nullable(true);
            $table->string('tipo_gasto',255)->nullable(true);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('techos_financieros',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->string('clv_fondo',2)->nullable(false);
            $table->bigInteger('presupuesto')->nullable(false);
            $table->enum('tipo', ['Operativo', 'RH']);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('unidades_medida',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave')->nullable(false);
            $table->text('unidad_medida',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

        Schema::create('beneficiarios',function (Blueprint $table){
            $table->increments('id');
            $table->string('clave',2)->nullable(false);
            $table->text('beneficiario',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));    
        });

        Schema::create('programacion_presupuesto',function (Blueprint $table){
            $table->increments('id');
            $table->string('clasificacion_administrativa',5)->nullable(false);
            $table->string('entidad_federativa',2)->nullable(false);
            $table->string('region',2)->nullable(false);
            $table->string('municipio',3)->nullable(false);
            $table->string('localidad',3)->nullable(false);
            $table->string('upp',3)->nullable(false);
            $table->string('subsecretaria',1)->nullable(false);
            $table->string('ur',2)->nullable(false);
            $table->string('finalidad',1)->nullable(false);
            $table->string('funcion',1)->nullable(false);
            $table->string('subfuncion',1)->nullable(false);
            $table->string('eje',1)->nullable(false);
            $table->string('linea_accion',2)->nullable(false);
            $table->string('programa_sectorial',1)->nullable(false);
            $table->string('tipologia_conac',1)->nullable(false);
            $table->string('programa_presupuestario',2)->nullable(false);
            $table->string('subprograma_presupuestario',3)->nullable(false);
            $table->string('proyecto_presupuestario',3)->nullable(false);
            $table->string('periodo_presupuestal',6)->nullable(false);
            $table->string('posicion_presupuestaria',5)->nullable(false);
            $table->string('tipo_gasto',1)->nullable(false);
            $table->string('anio',2)->nullable(false);
            $table->string('etiquetado',1)->nullable(false);
            $table->string('fuente_financiamiento',1)->nullable(false);
            $table->string('ramo',2)->nullable(false);
            $table->string('fondo_ramo',2)->nullable(false);
            $table->string('capital',1)->nullable(false);
            $table->string('proyecto_obra',6)->nullable(false);
            $table->integer('ejercicio')->nullable(true);
            $table->decimal('enero',22,2)->default(null);
            $table->decimal('febrero',22,2)->default(null);
            $table->decimal('marzo',22,2)->default(null);
            $table->decimal('abril',22,2)->default(null);
            $table->decimal('mayo',22,2)->default(null);
            $table->decimal('junio',22,2)->default(null);
            $table->decimal('julio',22,2)->default(null);
            $table->decimal('agosto',22,2)->default(null);
            $table->decimal('septiembre',22,2)->default(null);
            $table->decimal('octubre',22,2)->default(null);
            $table->decimal('noviembre',22,2)->default(null);
            $table->decimal('diciembre',22,2)->default(null);
            $table->decimal('total',22,2)->default(null);
            $table->integer('estado')->nullable(false);
            $table->enum('tipo', ['Operativo', 'RH']);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

/**/    Schema::create('tipologia_conac',function (Blueprint $table){
            $table->increments('id');
            $table->integer('tipo')->unsigned()->nullable(false);
            $table->string('descripcion',255)->nullable(false);
            $table->string('clave_conac',1)->nullable(true);
            $table->string('descripcion_conac',255)->nullable(true);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('cierre_ejercicio_metas',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->enum('estatus',['Cerrado','Abierto'])->default(null);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(false);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable(true);
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
        });

        Schema::create('uppautorizadascpnomina', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('cierre_ejercicio_claves',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->enum('estatus',['Cerrado','Abierto'])->default(null);
            $table->integer('ejercicio')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(false);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable(true);
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
        });



        Schema::create('metas',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_fondo',2)->nullable(false);
            $table->integer('mir_id')->unsigned()->nullable(false);
            $table->enum('tipo',['Acumulativa','Continua','Especial'])->nullable(false);
            $table->integer('beneficiario_id')->unsigned()->nullable(false);
            $table->integer('unidad_medida_id')->unsigned()->nullable(false);
            $table->integer('cantidad_beneficiarios');
            $table->integer('enero')->default(null);
            $table->integer('febrero')->default(null);
            $table->integer('marzo')->default(null);
            $table->integer('abril')->default(null);
            $table->integer('mayo')->default(null);
            $table->integer('junio')->default(null);
            $table->integer('julio')->default(null);
            $table->integer('agosto')->default(null);
            $table->integer('septiembre')->default(null);
            $table->integer('octubre')->default(null);
            $table->integer('noviembre')->default(null);
            $table->integer('diciembre')->default(null);
            $table->integer('total')->default(null);
            $table->integer('estatus')->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios');
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida');
            $table->foreign('mir_id')->references('id')->on('mml_mir');
        });

        Schema::create('sector_linea_accion',function (Blueprint $table){
            $table->increments('id');
            $table->integer('linea_accion_id')->unsigned()->nullable(false);
            $table->string('clv_sector',1)->nullable(false);
            $table->string('sector',255)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('linea_accion_id')->references('id')->on('catalogo');
        });

        Schema::create('epp',function (Blueprint $table){
            $table->increments('id');
            $table->integer('sector_publico_id')->unsigned()->nullable(false);
            $table->integer('sector_publico_f_id')->unsigned()->nullable(false);
            $table->integer('sector_economia_id')->unsigned()->nullable(false);
            $table->integer('subsector_economia_id')->unsigned()->nullable(false);
            $table->integer('ente_publico_id')->unsigned()->nullable(false);
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->integer('subsecretaria_id')->unsigned()->nullable(false);
            $table->integer('ur_id')->unsigned()->nullable(false);
            $table->integer('finalidad_id')->unsigned()->nullable(false);
            $table->integer('funcion_id')->unsigned()->nullable(false);
            $table->integer('subfuncion_id')->unsigned()->nullable(false);
            $table->integer('eje_id')->unsigned()->nullable(false);
            $table->integer('linea_accion_id')->unsigned()->nullable(false);
            $table->integer('programa_sectorial_id')->unsigned()->nullable(false);
            $table->integer('tipologia_conac_id')->unsigned()->nullable(false);
            $table->integer('programa_id')->unsigned()->nullable(false);
            $table->integer('subprograma_id')->unsigned()->nullable(false);
            $table->integer('proyecto_id')->unsigned()->nullable(false);
            $table->integer('ejercicio')->unsigned()->nullable(false);
            $table->boolean('presupuestable')->default(false);
            $table->boolean('confirmado')->default(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('created_user',45)->nullable(false);
        });

        Schema::create('pp_identificadores',function (Blueprint $table){
            $table->increments('id');
            $table->integer('epp_id')->unsigned()->nullable(false);
            $table->integer('clas_geo_id')->unsigned()->nullable(false);
            $table->integer('pos_pre_id')->unsigned()->nullable(false);
            $table->integer('fondo_id')->unsigned()->nullable(false);
            $table->integer('obra_id')->unsigned()->nullable(false);

            $table->foreign('epp_id')->references('id')->on('epp');
            $table->foreign('clas_geo_id')->references('id')->on('clasificacion_geografica');
            $table->foreign('pos_pre_id')->references('id')->on('posicion_presupuestaria');
            $table->foreign('fondo_id')->references('id')->on('fondo');
        });

        Schema::create('proyectos_obra',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_proyecto_obra',6)->nullable(false);
            $table->string('proyecto_obra',255)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('created_user',45)->nullable(false);
        });

        Schema::create('tipo_actividad_upp', function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->boolean('Continua')->default(false);
            $table->boolean('Acumulativa')->default(false);
            $table->boolean('Especial')->default(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_user',45)->nullable(false);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('updated_user',45)->nullable(true);
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
        });

        Schema::create('rel_economica_administrativa', function (Blueprint $table){
            $table->increments('id');
            $table->string('clasificacion_administrativa',5)->nullable(false);
            $table->string('clasificacion_economica',6)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });

        Schema::create('programacion_presupuesto_hist', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_original')->nullable(false);
            $table->integer('version')->nullable(false);
            $table->string('clasificacion_administrativa',5)->nullable(false);
            $table->string('entidad_federativa',2)->nullable(false);
            $table->string('region',2)->nullable(false);
            $table->string('municipio',3)->nullable(false);
            $table->string('localidad',3)->nullable(false);
            $table->string('upp',3)->nullable(false);
            $table->string('subsecretaria',1)->nullable(false);
            $table->string('ur',2)->nullable(false);
            $table->string('finalidad',1)->nullable(false);
            $table->string('funcion',1)->nullable(false);
            $table->string('subfuncion',1)->nullable(false);
            $table->string('eje',1)->nullable(false);
            $table->string('linea_accion',2)->nullable(false);
            $table->string('programa_sectorial',1)->nullable(false);
            $table->string('tipologia_conac',1)->nullable(false);
            $table->string('programa_presupuestario',2)->nullable(false);
            $table->string('subprograma_presupuestario',3)->nullable(false);
            $table->string('proyecto_presupuestario',3)->nullable(false);
            $table->string('periodo_presupuestal',6)->nullable(false);
            $table->string('posicion_presupuestaria',5)->nullable(false);
            $table->string('tipo_gasto',1)->nullable(false);
            $table->string('anio',2)->nullable(false);
            $table->string('etiquetado',1)->nullable(false);
            $table->string('fuente_financiamiento',1)->nullable(false);
            $table->string('ramo',2)->nullable(false);
            $table->string('fondo_ramo',2)->nullable(false);
            $table->string('capital',1)->nullable(false);
            $table->string('proyecto_obra',6)->nullable(false);
            $table->integer('ejercicio')->nullable(true);
            $table->decimal('enero',22,2)->default(null);
            $table->decimal('febrero',22,2)->default(null);
            $table->decimal('marzo',22,2)->default(null);
            $table->decimal('abril',22,2)->default(null);
            $table->decimal('mayo',22,2)->default(null);
            $table->decimal('junio',22,2)->default(null);
            $table->decimal('julio',22,2)->default(null);
            $table->decimal('agosto',22,2)->default(null);
            $table->decimal('septiembre',22,2)->default(null);
            $table->decimal('octubre',22,2)->default(null);
            $table->decimal('noviembre',22,2)->default(null);
            $table->decimal('diciembre',22,2)->default(null);
            $table->decimal('total',22,2)->default(null);
            $table->integer('estado')->nullable(false);
            $table->enum('tipo', ['Operativo', 'RH']);
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->nullable(false);
            $table->timestamp('updated_at')->nullable(true);
            $table->softDeletes();
        });

        Schema::create('epp_aux', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_sector_publico')->default(null);
            $table->string('clv_sector_publico',6)->nullable(false);
            $table->text('sector_publico')->nullable(false);
            $table->integer('id_sector_publico_f')->default(null);
            $table->string('clv_sector_publico_f',6)->nullable(false);
            $table->text('sector_publico_f')->nullable(false);
            $table->integer('id_sector_economia')->default(null);
            $table->string('clv_sector_economia',6)->nullable(false);
            $table->text('sector_economia')->nullable(false);
            $table->integer('id_subsector_economia')->default(null);
            $table->string('clv_subsector_economia',6)->nullable(false);
            $table->text('subsector_economia')->nullable(false);
            $table->integer('id_ente_publico')->default(null);
            $table->string('clv_ente_publico',6)->nullable(false);
            $table->text('ente_publico')->nullable(false);
            $table->integer('id_upp')->default(null);
            $table->string('clv_upp',6)->nullable(false);
            $table->text('upp')->nullable(false);
            $table->integer('id_subsecretaria')->default(null);
            $table->string('clv_subsecretaria',6)->nullable(false);
            $table->text('subsecretaria')->nullable(false);
            $table->integer('id_ur')->default(null);
            $table->string('clv_ur',6)->nullable(false);
            $table->text('ur')->nullable(false);
            $table->integer('id_finalidad')->default(null);
            $table->string('clv_finalidad',6)->nullable(false);
            $table->text('finalidad')->nullable(false);
            $table->integer('id_funcion')->default(null);
            $table->string('clv_funcion',6)->nullable(false);
            $table->text('funcion')->nullable(false);
            $table->integer('id_subfuncion')->default(null);
            $table->string('clv_subfuncion',6)->nullable(false);
            $table->text('subfuncion')->nullable(false);
            $table->integer('id_eje')->default(null);
            $table->string('clv_eje',6)->nullable(false);
            $table->text('eje')->nullable(false);
            $table->integer('id_linea_accion')->default(null);
            $table->string('clv_linea_accion',6)->nullable(false);
            $table->text('linea_accion')->nullable(false);
            $table->integer('id_programa_sectorial')->default(null);
            $table->string('clv_programa_sectorial',6)->nullable(false);
            $table->text('programa_sectorial')->nullable(false);
            $table->integer('id_tipologia_conac')->default(null);
            $table->string('clv_tipologia_conac',6)->nullable(false);
            $table->text('tipologia_conac')->nullable(false);
            $table->integer('id_programa')->default(null);
            $table->string('clv_programa',6)->nullable(false);
            $table->text('programa')->nullable(false);
            $table->integer('id_subprograma')->default(null);
            $table->string('clv_subprograma',6)->nullable(false);
            $table->text('subprograma')->nullable(false);
            $table->integer('id_proyecto')->default(null);
            $table->string('clv_proyecto',6)->nullable(false);
            $table->text('proyecto')->nullable(false);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('techos_financieros');
        Schema::dropIfExists('programacion_presupuesto');
        Schema::dropIfExists('tipologia_conac');
        Schema::dropIfExists('metas');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('cierre_ejercicio_metas');
        Schema::dropIfExists('cierre_ejercicio_claves');
        Schema::dropIfExists('uppAutorizadasCPNomina');
        Schema::dropIfExists('clasificacion_administrativa');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('posicion_presupuestaria');
        Schema::dropIfExists('epp');
        Schema::dropIfExists('sector_linea_accion');
        Schema::dropIfExists('catalogo');
        Schema::dropIfExists('subgrupos');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('pp_identificadores');
        Schema::dropIfExists('proyectos_obra');
        Schema::dropIfExists('tipo_actividad_upp');
        Schema::dropIfExists('rel_economica_administrativa');
        Schema::dropIfExists('programacion_presupuesto_hist');
        Schema::dropIfExists('mml_avance_etapas_pp');
        Schema::dropIfExists('mml_catalogos');
        Schema::dropIfExists('mml_objetivos_desarrollo_sostenible');
        Schema::dropIfExists('mml_definicion_problema');
        Schema::dropIfExists('mml_arbol_problema');
        Schema::dropIfExists('mml_arbol_objetivos');
        Schema::dropIfExists('mml_observaciones_pp');
        Schema::dropIfExists('mml_objetivo_sectorial_estrategia');
        Schema::dropIfExists('mml_mir');
        Schema::dropIfExists('epp_aux');
    }
};
