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
        /*Schema::create('area_funcional', function (Blueprint $table){
            $table->increments('id');
        });*/
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_funcional');
    }
};
