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
        if (!Schema::hasColumn('mml_mir_hist', 'estatus')) {
            Schema::table('mml_mir_hist', function (Blueprint $table) {
                $table->tinyInteger('estatus')->default(null)->nullable()->comment('null = no hay ficha; 0 = ficha guardada; 1 = ficha confirmada');
            });
        }

        if (!Schema::hasColumn('mml_mir_hist', 'desagregacion_geografica')) {
            Schema::table('mml_mir_hist', function (Blueprint $table) {
                $table->integer('desagregacion_geografica')->nullable();
            });        
        }
        
        if (!Schema::hasColumn('mml_observaciones_ficha', 'desagregacion_geografica')) {
            Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
                $table->string('ramo33', 4);
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
        Schema::table('mml_mir_hist', function (Blueprint $table) {
            $table->dropColumn('estatus');
            $table->dropColumn('desagregacion_geografica');
        });

        Schema::table('mml_mir_hist', function (Blueprint $table) {
            $table->dropColumn('ramo33');
        });
    }
};
