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
        Schema::create('mml_minutas_ficha_historico', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clv_upp', 4);
            $table->string('clv_pp', 4);
            $table->integer('id_mml_mir')->unsigned()->nullable(false);
            $table->string('ejercicio', 4);
            $table->string('ramo33', 4);
            $table->string('ruta_general', 300);
            $table->string('nombre_minuta', 200);
            $table->date('fecha_creacion');
            $table->tinyInteger('estatus')->default(0);
            $table->string('username_create', 100)->nullable();
            $table->string('username_update', 100)->nullable();
            $table->string('username_delete', 100)->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('id_mml_mir')->references('id')->on('mml_mir')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_minutas_ficha_historico');
    }
};
