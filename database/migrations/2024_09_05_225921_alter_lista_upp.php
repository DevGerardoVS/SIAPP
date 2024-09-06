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
        DB::unprepared("DROP PROCEDURE IF EXISTS lista_upp;");

        DB::unprepared("CREATE PROCEDURE lista_upp(in tipo int,in anio int)
BEGIN
    if tipo = 0 then
        select
            clave clv_upp,
            descripcion upp,
            null fecha_baja
        from catalogo c 
        where grupo_id = 6 and deleted_at is null and ejercicio = anio;
    elseif tipo = 1  then
        select #SOLO INACTIVOS
            clave clv_upp,
            descripcion upp, 
            DATE_FORMAT(deleted_at, '%Y-%m-%d') fecha_baja
        from catalogo c 
        where grupo_id = 6 and deleted_at is not null and ejercicio = anio;
    else 
        select 
            clave clv_upp,
            descripcion upp, 
            DATE_FORMAT(deleted_at, '%Y-%m-%d') fecha_baja
        from catalogo c 
        where grupo_id = 6 and ejercicio = anio;
    end if;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS lista_upp;");
    }
};
