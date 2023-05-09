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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('modulo',200)->nullable(false);
            $table->string('ruta',50)->nullable(true);
            $table->string('icono',50)->nullable(true);
            $table->string('tipo',10)->nullable(false);
            $table->integer('modulo_id')->nullable(true);
            $table->boolean('estatus')->nullable(false);
            $table->string('usuario_creacion',20)->nullable(false);
            $table->string('usuario_modificacion',20)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulos');
    }
};
