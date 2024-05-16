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
        Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
            //
        });
          Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
                $table->string('Nombreminuta');
                $table->string('Rutaminuta');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mml_observaciones_ficha', function (Blueprint $table) {
            //
                $table->dropColumn('Nombreminuta');
                $table->dropColumn('Rutaminuta');
        });
    }
};
