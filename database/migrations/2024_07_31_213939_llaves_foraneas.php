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
            DB::statement('ALTER TABLE mml_mir MODIFY id_epp INT(10) UNSIGNED;');
            DB::statement('ALTER TABLE mml_mir MODIFY componente_padre INT(10) UNSIGNED;');
            $table->foreign('id_epp')->references('id')->on('v_epp')->onUpdate('cascade');
            $table->foreign('componente_padre')->references('id')->on('mml_mir')->onUpdate('cascade');
        });

        Schema::table('mml_actividades', function (Blueprint $table) {
            DB::statement('ALTER TABLE mml_actividades MODIFY id_catalogo INT(10) UNSIGNED;');
            $table->foreign('id_catalogo')->references('id')->on('catalogo')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
