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
        //
        Schema::table('mml_minutas_ficha_historico', function (Blueprint $table) {
            //
            $table->text('ruta_general')->nullable(false)->change();
            $table->string('nombre_minuta', 500)->nullable(false)->change();
        });

        Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
            $table->text('ruta')->nullable(true)->change();
            $table->string('nombre',500)->nullable(true)->change();
            $table->string('Nombreminuta',500)->change();
            $table->text('Rutaminuta')->change();
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
        Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
            //
            $table->dropColumn('ruta_general');
            $table->dropColumn('nombre_minuta');
        });

        Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
            //
                $table->dropColumn('ruta');
                $table->dropColumn('nombre');
                $table->dropColumn('Nombreminuta');
                $table->dropColumn('Rutaminuta');
        });

    }
};
