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
        DB::unprepared("ALTER TABLE `cierre_ejercicio_metas` ADD `confirmado` TINYINT(10) NOT NULL DEFAULT '0' COMMENT 'estatus de confirmacion de metas' AFTER `estatus`;");
        DB::unprepared("INSERT INTO `catalogo`( `padre_id`, `ejercicio`, `grupo_id`, `clave`, `descripcion`, `descripcion_larga`, `descripcion_corta`, `created_user` ) VALUES( NULL, 2025, 'ACTIVIDADES ADMON', 'UUU', 'Cumplimiento de obligaciones patronales', NULL, NULL, 'ADMIN' ),( NULL, 2025, 'ACTIVIDADES ADMON', '21B', 'Cumplimiento de resoluciones emitidas por autoridad judicial y laudos', NULL, NULL, 'ADMIN' );");
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
