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
/**/    Schema::create('grupos', function (Blueprint $table){
        $table->increments('id');
        $table->text('grupo',255)->nullable(false)->collate('utf8_bin');
        $table->integer('ejercicio')->default(null);
        $table->softDeletes();
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
    });

/**/    Schema::create('subgrupos',function (Blueprint $table){
            $table->increments('id');
            $table->integer('grupo_id')->unsigned()->nullable(false);
            $table->integer('largo_clave')->nullable(false);
            $table->text('subgrupo',255)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('grupo_id')->references('id')->on('grupos');
        });

/**/    Schema::create('catalogo', function (Blueprint $table){
            $table->increments('id');
            $table->integer('subgrupo_id')->unsigned()->nullable(false);
            $table->string('clave',6)->nullable(false);
            $table->text('descripcion')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('subgrupo_id')->references('id')->on('subgrupos');
        });

/**/    Schema::create('area_funcional', function (Blueprint $table){
            $table->increments('id')->nullable(false);
            $table->integer('finalidad_id')->unsigned()->nullable(false);
            $table->integer('funcion_id')->unsigned()->nullable(false);
            $table->integer('subfuncion_id')->unsigned()->nullable(false);
            $table->integer('eje_id')->unsigned()->nullable(false);
            $table->integer('linea_accion_id')->unsigned()->nullable(false);
            $table->integer('programa_sectorial_id')->unsigned()->nullable(false);
            $table->integer('tipologia_conac_id')->unsigned()->nullable(false);
            $table->integer('programa_presupuestario_id')->unsigned()->nullable(false);
            $table->integer('subprograma_presupuestario_id')->unsigned()->nullable(false);
            $table->integer('proyecto_presupuestario_id')->unsigned()->nullable(false);
            $table->string('llave',16)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('eje_id')->references('id')->on('catalogo');
            $table->foreign('finalidad_id')->references('id')->on('catalogo');
            $table->foreign('funcion_id')->references('id')->on('catalogo');
            $table->foreign('linea_accion_id')->references('id')->on('catalogo');
            $table->foreign('programa_presupuestario_id')->references('id')->on('catalogo');
            $table->foreign('programa_sectorial_id')->references('id')->on('catalogo');
            $table->foreign('proyecto_presupuestario_id')->references('id')->on('catalogo');
            $table->foreign('subfuncion_id')->references('id')->on('catalogo');
            $table->foreign('subprograma_presupuestario_id')->references('id')->on('catalogo');
            $table->foreign('tipologia_conac_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('entidad_ejecutora', function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->integer('subsecretaria_id')->unsigned()->nullable(false);
            $table->integer('ur_id')->unsigned()->nullable(false);
            $table->string('llave',6)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('subsecretaria_id')->references('id')->on('catalogo');
            $table->foreign('upp_id')->references('id')->on('catalogo');
            $table->foreign('ur_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('area_funcional_entidad_ejecutora', function (Blueprint $table){
            $table->increments('id');
            $table->integer('area_funcional_id')->unsigned()->nullable(false);
            $table->integer('entidad_ejecutora_id')->unsigned()->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('area_funcional_id')->references('id')->on('area_funcional');
            $table->foreign('entidad_ejecutora_id')->references('id')->on('entidad_ejecutora');
        });

/**/    Schema::create('clasificacion_administrativa', function (Blueprint $table){
            $table->increments('id');
            $table->integer('sector_publico_id')->unsigned()->nullable(false);
            $table->integer('sector_publico_fnof_id')->unsigned()->nullable(false);
            $table->integer('sector_economia_id')->unsigned()->nullable(false);
            $table->integer('subsector_economia_id')->unsigned()->nullable(false);
            $table->integer('ente_publico_id')->unsigned()->unsigned()->nullable(false);
            $table->string('llave',5)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('ente_publico_id')->references('id')->on('catalogo');
            $table->foreign('sector_economia_id')->references('id')->on('catalogo');
            $table->foreign('sector_publico_id')->references('id')->on('catalogo');
            $table->foreign('sector_publico_fnof_id')->references('id')->on('catalogo');
            $table->foreign('subsector_economia_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('clasificacion_geografica', function (Blueprint $table){
            $table->increments('id');
            $table->integer('entidad_federativa_id')->unsigned()->nullable(false);
            $table->integer('region_id')->unsigned()->nullable(false);
            $table->integer('municipio_id')->unsigned()->nullable(false);
            $table->integer('localidad_id')->unsigned()->nullable(false);
            $table->string('llave',10)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('entidad_federativa_id')->references('id')->on('catalogo');
            $table->foreign('localidad_id')->references('id')->on('catalogo');
            $table->foreign('municipio_id')->references('id')->on('catalogo');
            $table->foreign('region_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('ente_publico_upp', function (Blueprint $table){
            $table->increments('id');
            $table->integer('clasificacion_administrativa_id')->unsigned()->nullable(false);
            $table->integer('entidad_ejecutora_id')->unsigned()->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('clasificacion_administrativa_id')->references('id')->on('clasificacion_administrativa');
            $table->foreign('entidad_ejecutora_id')->references('id')->on('entidad_ejecutora');
        });

/**/    Schema::create('fondo', function (Blueprint $table){
            $table->increments('id');
            $table->integer('etiquetado_id')->unsigned()->nullable(false);
            $table->integer('fuente_financiamiento_id')->unsigned()->nullable(false);
            $table->integer('ramo_id')->unsigned()->nullable(false);
            $table->integer('fondo_ramo_id')->unsigned()->nullable(false);
            $table->integer('capital_id')->unsigned()->nullable(false);
            $table->string('llave',7)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('capital_id')->references('id')->on('catalogo');
            $table->foreign('etiquetado_id')->references('id')->on('catalogo');
            $table->foreign('fondo_ramo_id')->references('id')->on('catalogo');
            $table->foreign('fuente_financiamiento_id')->references('id')->on('catalogo');
            $table->foreign('ramo_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('posicion_presupuestaria', function (Blueprint $table){
            $table->increments('id');
            $table->integer('capitulo_id')->unsigned()->nullable(false);
            $table->integer('concepto_id')->unsigned()->nullable(false);
            $table->integer('partida_generica_id')->unsigned()->nullable(false);
            $table->integer('partida_especifica_id')->unsigned()->nullable(false);
            $table->string('llave',5)->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('capitulo_id')->references('id')->on('catalogo');
            $table->foreign('concepto_id')->references('id')->on('catalogo');
            $table->foreign('partida_especifica_id')->references('id')->on('catalogo');
            $table->foreign('partida_generica_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('partida_upp', function (Blueprint $table){
            $table->integer('posicion_presupuestaria_id')->unsigned()->nullable(false);
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->primary(['posicion_presupuestaria_id', 'upp_id']);
            $table->string('posicion_presupuestaria_llave',5)->default(null);
            $table->string('upp_clave',3)->default(null);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('posicion_presupuestaria_id')->references('id')->on('posicion_presupuestaria');
            $table->foreign('upp_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('presupuesto_upp_asignado',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->integer('fondo_id')->unsigned()->nullable(false);
            $table->bigInteger('presupuesto_asignado')->nullable(false);
            $table->bigInteger('presupuesto_rh')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('fondo_id')->references('id')->on('catalogo');
            $table->foreign('upp_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('unidades_medida',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave')->nullable(false);
            $table->text('unidad_medida',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

/**/    Schema::create('beneficiarios',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clave')->nullable(false);
            $table->text('beneficiario',255);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });

/**/    Schema::create('programacion_presupuesto',function (Blueprint $table){
            $table->increments('id');
            $table->integer('clasificacion_administrativa_id')->unsigned()->nullable(false);
            $table->integer('clasificacion_geografica_id')->unsigned()->nullable(false);
            $table->integer('entidad_ejecutora_id')->unsigned()->nullable(false);
            $table->integer('area_funcional_id')->unsigned()->nullable(false);
            $table->string('mes_afectacion',6)->nullable(false);
            $table->integer('posicion_presupuestaria_id')->unsigned()->nullable(false);
            $table->integer('tipo_gasto_id')->unsigned()->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->integer('etiquetado_id')->unsigned()->nullable(false);
            $table->integer('fondo_id')->unsigned()->nullable(false);
            $table->integer('proyecto_presupuestal_id')->unsigned()->nullable(false);
            $table->text('clave_presupuestal',64);
            $table->float('enero',23)->default(null);
            $table->float('febrero',23)->default(null);
            $table->float('marzo',23)->default(null);
            $table->float('abril',23)->default(null);
            $table->float('mayo',23)->default(null);
            $table->float('junio',23)->default(null);
            $table->float('julio',23)->default(null);
            $table->float('agosto',23)->default(null);
            $table->float('septiembre',23)->default(null);
            $table->float('octubre',23)->default(null);
            $table->float('noviembre',23)->default(null);
            $table->float('diciembre',23)->default(null);
            $table->integer('estado')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('area_funcional_id')->references('id')->on('area_funcional');
            $table->foreign('clasificacion_administrativa_id')->references('id')->on('clasificacion_administrativa');
            $table->foreign('clasificacion_geografica_id')->references('id')->on('clasificacion_geografica');
            $table->foreign('entidad_ejecutora_id')->references('id')->on('entidad_ejecutora');
            $table->foreign('etiquetado_id')->references('id')->on('catalogo');
            $table->foreign('fondo_id')->references('id')->on('catalogo');
            $table->foreign('posicion_presupuestaria_id')->references('id')->on('posicion_presupuestaria');
            $table->foreign('proyecto_presupuestal_id')->references('id')->on('catalogo');
            $table->foreign('tipo_gasto_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('tipologia_conac',function (Blueprint $table){
            $table->increments('id');
            $table->integer('padre_id')->unsigned()->nullable(false);
            $table->integer('hijo_id')->unsigned()->default(null);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('hijo_id')->references('id')->on('catalogo');
            $table->foreign('padre_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('ur_localidad',function (Blueprint $table){
            $table->integer('ur_id')->unsigned();
            $table->integer('localidad_id')->unsigned()->nullable(false);
            $table->primary(['ur_id','localidad_id']);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('localidad_id')->references('id')->on('catalogo');
            $table->foreign('ur_id')->references('id')->on('catalogo');
        });

/**/    Schema::create('upp_fondo_montos',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->integer('fondo_id')->unsigned()->nullable(false);
            $table->enum('tipo',['RH','Operativo'])->nullable(false);
            $table->integer('monto')->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('fondo_id')->references('id')->on('catalogo');
            $table->foreign('upp_id')->references('id')->on('catalogo');

        });

/**/    Schema::create('administracion_captura',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->enum('estatus',['Cerrado','Abierto'])->nullable(false);
            $table->integer('usuario_id')->unsigned()->nullable(false);
            $table->integer('ejercicio')->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('upp_id')->references('id')->on('catalogo');
            $table->foreign('usuario_id')->references('id')->on('adm_users');

        });

/**/    /*Schema::create('proyectos_ur',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->nullable(false);
            $table->integer('ur_id')->nullable(false);
            $table->integer('fondo_id')->nullable(false);
            $table->integer('proyecto_id')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });*/

/**/    Schema::create('uppAutorizadasCPNomina', function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('upp_id')->references('id')->on('catalogo');
        });

/**/    /*Schema::create('actividades',function (Blueprint $table){
            $table->increments('id');
            $table->integer('proyectos_ur_id')->unsigned()->nullable(false);
            $table->text('actividad',255)->default(null);
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
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('proyectos_ur_id')->references('id')->on('proyectos_ur');
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios');
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida');
        });*/

/**/     Schema::create('administracion_capturas',function (Blueprint $table){
            $table->increments('id');
            $table->integer('upp_id')->unsigned()->nullable(false);
            $table->enum('estatus',['Cerrado','Abierto'])->default(null);
            $table->integer('usuario_id')->nullable(false);
            $table->integer('ejercicio')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('upp_id')->references('id')->on('catalogo');

        });

/**/     Schema::create('cat_direccion',function (Blueprint $table){
            $table->increments('id');
            $table->string('cve_direccion',15)->nullable(false);
            $table->string('nombre_direccion',120)->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('proyectos_mir',function (Blueprint $table){
            $table->increments('id');
            $table->integer('programa_id')->nullable(false);
            $table->integer('subprograma_id')->nullable(false);
            $table->integer('proyecto_id')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('actividades_mir', function (Blueprint $table){
            $table->increments('id');
            $table->integer('datos_mir_id')->unsigned()->nullable(false);
            $table->string('clave',45)->nullable(false);
            $table->string('actividad',255)->nullable(false);
            $table->integer('estatus')->nullable(false);
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('datos_mir_id')->references('id')->on('proyectos_mir');
        });

        Schema::create('metas',function (Blueprint $table){
            $table->increments('id');
            $table->integer('programa_id')->unsigned()->nullable(false);
            $table->integer('subprograma_id')->unsigned()->nullable(false);
            $table->integer('proyecto_id')->unsigned()->nullable(false);
            $table->integer('actividad_id')->unsigned()->nullable(false);
            $table->enum('tipo',['Acumulativa','Continua','Especial'])->nullable(false);
            $table->integer('beneficiario_id')->nullable(false);
            $table->integer('unidad_medida_id')->nullable(false);
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
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('programa_id')->references('id')->on('catalogo');
            $table->foreign('subprograma_id')->references('id')->on('catalogo');
            $table->foreign('proyecto_id')->references('id')->on('catalogo');
            $table->foreign('actividad_id')->references('id')->on('actividades_mir');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_funcional_entidad_ejecutora');
        Schema::dropIfExists('ente_publico_upp');
        Schema::dropIfExists('fondo');
        Schema::dropIfExists('partida_upp');
        Schema::dropIfExists('presupuesto_upp_asignado');
        Schema::dropIfExists('programacion_presupuesto');
        Schema::dropIfExists('tipologia_conac');
        Schema::dropIfExists('ur_localidad');
        Schema::dropIfExists('upp_fondo_montos');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('administracion_captura');
        Schema::dropIfExists('cat_direccion');
        /*Schema::dropIfExists('actividades');*/
        Schema::dropIfExists('administracion_capturas');
        Schema::dropIfExists('uppAutorizadasCPNomina');
        Schema::dropIfExists('area_funcional');
        Schema::dropIfExists('clasificacion_administrativa');
        Schema::dropIfExists('clasificacion_geografica');
        Schema::dropIfExists('entidad_ejecutora');
        Schema::dropIfExists('posicion_presupuestaria');
        Schema::dropIfExists('catalogo');
        Schema::dropIfExists('subgrupos');
        Schema::dropIfExists('grupos');
    }
};
