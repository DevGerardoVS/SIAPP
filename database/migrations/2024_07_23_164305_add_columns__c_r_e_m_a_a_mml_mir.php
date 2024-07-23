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
            $table->tinyInteger('estatus_resumen')->default(0);
            $table->tinyInteger('cremaa_c')->default(0);
            $table->tinyInteger('cremaa_r')->default(0);
            $table->tinyInteger('cremaa_e')->default(0);
            $table->tinyInteger('cremaa_m')->default(0);
            $table->tinyInteger('cremaa_a')->default(0);
            $table->tinyInteger('cremaa_a_f')->default(0);
            $table->string('resumen_observacion_indicador', 500)->nullable();
            $table->string('resumen_observacion', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mml_mir', function (Blueprint $table) {
            $table->dropColumn('estatus_resumen');
            $table->dropColumn('cremaa_c');
            $table->dropColumn('cremaa_r');
            $table->dropColumn('cremaa_e');
            $table->dropColumn('cremaa_m');
            $table->dropColumn('cremaa_a');
            $table->dropColumn('cremaa_a_f');
            $table->dropColumn('resumen_observacion_indicador');
            $table->dropColumn('resumen_observacion');
        });
    }
};
