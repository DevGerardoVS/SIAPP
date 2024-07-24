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
        DB::statement("SET foreign_key_checks=0");
        DB::unprepared("ALTER TABLE `entidad_ejecutora` CHANGE `upp_id` `upp_id` INT(10) UNSIGNED NULL DEFAULT NULL, CHANGE `subsecretaria_id` `subsecretaria_id` INT(10) UNSIGNED NULL DEFAULT NULL, CHANGE `ur_id` `ur_id` INT(10) UNSIGNED NULL DEFAULT NULL;");
        DB::unprepared("ALTER TABLE `clasificacion_funcional` CHANGE `funcion_id` `funcion_id` INT(10) UNSIGNED NULL DEFAULT NULL;");
        DB::statement("SET foreign_key_checks=1");
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
