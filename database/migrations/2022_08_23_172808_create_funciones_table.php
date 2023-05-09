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
        Schema::create('funciones', function (Blueprint $table) {
            $table->id();
            $table->string('funcion',200)->nullable(false);
            $table->string('ruta',50)->nullable(true);
            $table->string('icono',50)->nullable(true);
            $table->text('acciones')->nullable(true);
            $table->integer('orden')->nullable(false);
            $table->unsignedBigInteger('modulo_id')->nullable(false);
            $table->boolean('estatus')->nullable(false);
            $table->string('usuario_creacion',20)->nullable(false);
            $table->string('usuario_modificacion',20)->nullable(true);
            $table->timestamps();
            $table->foreign('modulo_id')
                ->references('id')
                ->on('modulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funciones');
    }
};
