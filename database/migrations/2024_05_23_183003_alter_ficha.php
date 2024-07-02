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
        if (Schema::hasTable('mml_cremaa')) {
            Schema::table('mml_cremaa', function (Blueprint $table) {
                $table->string('justificacion', 500)->change();
            });   
        } 

        if (Schema::hasTable('mml_variable')) {
            Schema::table('mml_variable', function (Blueprint $table) {
                $table->string('medios_verificacion', 500)->change();
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
