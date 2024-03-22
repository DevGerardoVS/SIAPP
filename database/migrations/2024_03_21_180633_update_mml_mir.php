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
        if (!Schema::hasColumn('mml_mir', 'desagregacion_geografica')) {
            Schema::table('mml_mir', function (Blueprint $table) {
                $table->integer('desagregacion_geografica')->nullable();
            });        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mml_mir', function (Blueprint $table) {
            $table->dropColumn('desagregacion_geografica');
        });
    }
};
