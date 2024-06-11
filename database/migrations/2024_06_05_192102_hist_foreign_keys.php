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
        if (Schema::hasColumn('epp_hist', 'id')) {
            Schema::table('epp_hist', function (Blueprint $table) {
                $table->dropColumn('id');
                $table->primary(['id_original', 'version']);
            });
        }

        DB::statement('ALTER TABLE epp_hist MODIFY COLUMN id_original INT UNSIGNED NOT NULL AUTO_INCREMENT');

        if (Schema::hasColumn('catalogo_hist', 'id')) {
            Schema::table('catalogo_hist', function (Blueprint $table) {
                $table->dropColumn('id');
                $table->primary(['id_original', 'version']);
            });
        }

        DB::statement('ALTER TABLE catalogo_hist MODIFY COLUMN id_original INT UNSIGNED NOT NULL AUTO_INCREMENT');

        Schema::table('epp_hist', function (Blueprint $table) {
            $table->foreign('sector_publico_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('sector_publico_f_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('sector_economia_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('subsector_economia_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('ente_publico_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('upp_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('subsecretaria_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('ur_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('finalidad_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('funcion_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('subfuncion_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('eje_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('linea_accion_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('programa_sectorial_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('tipologia_conac_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('programa_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('subprograma_id')->references('id_original')->on('catalogo_hist');
            $table->foreign('proyecto_id')->references('id_original')->on('catalogo_hist');
        });

        DB::statement('ALTER TABLE metas_hist MODIFY COLUMN mir_id INT');

        Schema::table('metas_hist', function (Blueprint $table) {
            $table->foreign('mir_id')->references('id_original')->on('mml_mir_hist');
            $table->foreign('actividad_id')->references('id_original')->on('mml_actividades_hist');
        });

        DB::statement('ALTER TABLE mml_actividades_hist MODIFY COLUMN id_catalogo INT(10) UNSIGNED');

        Schema::table('mml_actividades_hist', function (Blueprint $table) {
            $table->foreign('id_catalogo')->references('id_original')->on('catalogo_hist');
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
