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
        Schema::table('programacion_presupuesto', function (Blueprint $table) {
            $table->tinyInteger('estatus_sapp')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programacion_presupuesto', function (Blueprint $table) {
            $table->dropColumn('estatus_sapp');

        });
    }
};
