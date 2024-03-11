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
        Schema::table('mml_mir', function (Blueprint $table) {
            $table->tinyInteger('estatus')->default(null)->nullable()->comment('null = no hay ficha; 0 = ficha guardada; 1 = ficha confirmada');
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
