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
        Schema::create('mml_archivo', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_mml_mir')->unsigned()->nullable(false);
            $table->string('clv_upp', 4)->nullable(false);
            $table->string('ejercicio', 4)->nullable(false);
            $table->string('nombre_archivo',100)->nullable(false);
            $table->string('ruta',200)->nullable(false);
            $table->string('extension', 4)->nullable(false);
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->nullable(false);
            $table->timestamp('updated_at')->nullable(true);
            $table->softDeletes();

            $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_archivo');
    }
};
