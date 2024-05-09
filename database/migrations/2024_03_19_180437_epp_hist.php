<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('epp_hist')) {
            Schema::create('epp_hist', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_original')->unsigned();
                $table->tinyInteger('version');
                $table->unsignedInteger('sector_publico_id');
                $table->unsignedInteger('sector_publico_f_id');
                $table->unsignedInteger('sector_economia_id');
                $table->unsignedInteger('subsector_economia_id');
                $table->unsignedInteger('ente_publico_id');
                $table->unsignedInteger('upp_id');
                $table->unsignedInteger('subsecretaria_id');
                $table->unsignedInteger('ur_id');
                $table->unsignedInteger('finalidad_id');
                $table->unsignedInteger('funcion_id');
                $table->unsignedInteger('subfuncion_id');
                $table->unsignedInteger('eje_id');
                $table->unsignedInteger('linea_accion_id');
                $table->unsignedInteger('programa_sectorial_id');
                $table->unsignedInteger('tipologia_conac_id');
                $table->unsignedInteger('programa_id');
                $table->unsignedInteger('subprograma_id');
                $table->unsignedInteger('proyecto_id');
                $table->unsignedInteger('ejercicio');
                $table->tinyInteger('presupuestable')->default(false);
                $table->tinyInteger('con_mir');
                $table->tinyInteger('confirmado')->default(false);
                $table->tinyInteger('tipo_presupuesto')->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->softDeletes();
                $table->string('created_user', 45);
                $table->string('updated_user', 45)->nullable();
                $table->string('deleted_user', 45)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('epp_hist');
    }
};
