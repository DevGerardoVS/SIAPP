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
         DB::statement('RENAME TABLE v_entidad_ejecutora TO v2_entidad_ejecutora');

          // Creamos la tabla
          Schema::create('v_entidad_ejecutora', function ($table) {
            $table->increments('id');
            $table->string('clv_upp');
            $table->string('upp');
            $table->string('clv_subsecretaria');
            $table->string('subsecretaria');
            $table->string('clv_ur');
            $table->string('ur');
            $table->integer('ejercicio');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('deleted_user')->nullable();
            $table->string('updated_user')->nullable();
            $table->string('created_user')->nullable();
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la tabla materializada v_epp
        Schema::dropIfExists('v_entidad_ejecutora');

        // Renombrar la vista v2_entidad_ejecutora a v_entidad_ejecutora
        DB::statement('RENAME TABLE v2_entidad_ejecutora TO v_entidad_ejecutora');
    }
};
