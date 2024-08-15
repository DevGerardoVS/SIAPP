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
        if (Schema::hasTable('upp_extras')) {
            Schema::table('upp_extras', function (Blueprint $table) {
                $table->tinyInteger('estatus_epp')->nullable(true)->default(0)->after('clasificacion_administrativa_id');
            });
        }

        if (Schema::hasTable('catalogo')){
            Schema::table('catalogo', function (Blueprint $table) {
                $table->string('descripcion_larga',43)->nullable(true)->default(true)->change();
                $table->string('descripcion_corta',22)->nullable(true)->default(true)->change();
            });
        }

        if (Schema::hasTable('entidad_ejecutora')){
            Schema::table('entidad_ejecutora', function (Blueprint $table) {
                $table->integer('upp_id')->unsigned()->default(null)->change();
                $table->integer('subsecretaria_id')->unsigned()->default(null)->change();
                $table->integer('ur_id')->unsigned()->default(null)->change();
            });
        }

        if (Schema::hasTable('epp')) {
            Schema::table('epp', function (Blueprint $table) {
                $table->tinyInteger('upp_id')->nullable(true)->default(null)->after('mes_f');
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
        //
    }
};
