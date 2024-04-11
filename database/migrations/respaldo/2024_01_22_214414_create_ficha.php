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
        Schema::table('mml_mir', function (Blueprint $table) {
            $table->Integer('desagregacion_geografica')->nullable(true)->add();
        });

        Schema::create('mml_cremaa', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_mml_mir')->unsigned()->nullable(false);
            $table->enum('claridad', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('relevancia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('economia', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('monitoreable', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('adecuado', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->enum('aportacion_marginal', ['0', '1', '2', '3'])->comment('0 = N/A; 1 = Cumple; 2 = No cumple; 3 = Cumple parcialmente');
            $table->string('justificacion',255)->nullable(false);
            $table->date('serie')->nullable(false);
            $table->string('responsable',150)->nullable(false);
            $table->string('created_user',45)->nullable(false);
            $table->string('updated_user',45)->nullable(true);
            $table->string('deleted_user',45)->nullable(true);
            $table->timestamp('created_at')->nullable(false);
            $table->timestamp('updated_at')->nullable(true);
            $table->softDeletes();

            $table->foreign('id_mml_mir')->references('id')->on('mml_mir');
        });

        Schema::create('mml_variable', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_mml_mir')->unsigned()->nullable(false);
            $table->string('nombre',150)->nullable(false);
            $table->string('descripcion',255)->nullable(false);
            $table->string('unidad_medida',50)->nullable(false);
            $table->string('medios_verificacion',200)->nullable(false);
            $table->string('frecuencia',50)->nullable(false);
            $table->string('desagregacion_geografica',50)->nullable(false);
            $table->string('metodo_recopilacion_datos',50)->nullable(false);
            $table->date('fecha_disponibilidad')->nullable(false);
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
        Schema::dropIfExists('mml_mir');
        Schema::dropIfExists('mml_cremaa');
        Schema::dropIfExists('mml_variable');
    }
};
