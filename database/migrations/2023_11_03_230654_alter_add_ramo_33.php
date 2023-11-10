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
        /* Schema::table('mml_avance_etapas_pp', function($table) {
            $table->tinyInteger('ramo33');
        }); */

        Schema::table('mml_definicion_problema', function($table) {
            $table->tinyInteger('ramo33');
        });

        Schema::table('mml_arbol_objetivos', function($table) {
            $table->tinyInteger('ramo33');
        });

        Schema::table('mml_arbol_problema', function($table) {
            $table->tinyInteger('ramo33');
        });

        Schema::table('mml_mir', function($table) {
            $table->tinyInteger('ramo33');
        });

        Schema::table('mml_observaciones_pp', function($table) {
            $table->tinyInteger('ramo33');
        });
    }
};
