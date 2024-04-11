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
        Schema::table('cierre_ejercicio_claves', function (Blueprint $table) {
            $table->tinyInteger('activos')->default(1)->add();
        });

        Schema::table('cierre_ejercicio_metas', function (Blueprint $table) {
            $table->tinyInteger('activos')->default(1)->add();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cierre_ejercicio_claves');
        Schema::dropIfExists('cierre_ejercicio_metas');
    }
};
