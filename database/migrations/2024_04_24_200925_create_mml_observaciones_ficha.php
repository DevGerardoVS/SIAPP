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
        Schema::create('mml_observaciones_ficha', function (Blueprint $table) {
            $table->unique(['clv_upp', 'clv_pp', 'ejercicio', 'mir_id'], 'clave');
            $table->increments('id');
            $table->string('clv_upp',4)->nullable(true);
            $table->string('clv_pp',255)->nullable(false);
            $table->integer('mir_id')->unsigned()->nullable(false);
            $table->text('comentario')->nullable(true);
            $table->string('ruta',200)->nullable(true);
            $table->string('nombre',500)->nullable(true);
            $table->integer('ejercicio')->nullable(true);
            $table->string('created_user',45)->nullable(true);
            $table->string('updated_user',45)->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('mir_id')->references('id')->on('mml_mir');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mml_observaciones_ficha');
    }
};
