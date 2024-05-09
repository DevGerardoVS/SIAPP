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
        Schema::table('epp', function (Blueprint $table) {
            $table->dropUnique('llave_epp');
        });

        Schema::table('epp', function (Blueprint $table) {
            $table->unique([
                'sector_publico_id',
                'sector_publico_f_id',
                'sector_economia_id',
                'subsector_economia_id',
                'ente_publico_id',
                'upp_id',
                'subsecretaria_id',
                'ur_id',
                'finalidad_id',
                'funcion_id',
                'subfuncion_id',
                'eje_id',
                'linea_accion_id',
                'programa_sectorial_id',
                'tipologia_conac_id',
                'programa_id',
                'subprograma_id',
                'proyecto_id',
                'ejercicio',
                'deleted_at',
            ], 'llave_epp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('epp', function (Blueprint $table) {
            $table->dropUnique('llave_epp');
        });
    }
};
