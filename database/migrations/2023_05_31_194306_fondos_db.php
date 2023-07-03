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

        Schema::create('grupos', function (Blueprint $table){
            $table->increments('id');
            $table->integer('grupo')->nullable(false);
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

/**/     Schema::create('cat_direccion',function (Blueprint $table){
            $table->increments('id');
            $table->string('cve_direccion',15)->nullable(false);
            $table->string('nombre_direccion',120)->nullable(false);
            $table->softDeletes();
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('proyectos_mir',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_upp',3)->nullable(false);
            $table->string('clv_ur',2)->nullable(false);
            $table->string('clv_programa',2)->nullable(false);
            $table->string('clv_subprograma',3)->nullable(false);
            $table->string('clv_proyecto',3)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('actividades_mir', function (Blueprint $table){
            $table->increments('id');
            $table->integer('proyecto_mir_id')->unsigned()->nullable(false);
            $table->string('clv_actividad',45)->nullable(false);
            $table->string('actividad',255)->nullable(false);
            $table->integer('estatus')->unsigned()->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('proyecto_mir_id')->references('id')->on('proyectos_mir');
        });

        

        Schema::create('metas',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_fondo',2)->nullable(false);
            $table->integer('actividad_id')->unsigned()->nullable(false);
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
            $table->foreign('actividad_id')->references('id')->on('actividades_mir');
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
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        });

        Schema::create('proyectos_obra',function (Blueprint $table){
            $table->increments('id');
            $table->string('clv_proyecto_obra',6)->nullable(false);
            $table->string('proyecto_obra',255)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->string('deleted_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->string('created_user',45)->nullable(false);
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
        Schema::dropIfExists('cat_direccion');
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
        Schema::dropIfExists('actividades_mir');
        Schema::dropIfExists('proyectos_mir');
    }
};
