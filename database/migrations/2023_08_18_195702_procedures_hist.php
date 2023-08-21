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
        DB::unprepared("CREATE PROCEDURE if not exists reporte_art_20_frac_III(in anio int, in corte date)
        begin
            select 
                va.clv_upp,va.upp,
                va.clv_ur,va.ur,
                va.clv_programa,va.programa,
                va.clv_subprograma,va.subprograma,
                concat(
                    va.clv_capitulo,
                    va.clv_concepto,
                    va.clv_partida_generica,
                    va.clv_partida_especifica
                ) as clv_partida, va.partida_especifica,
                sum(va.total) importe
            from pp_aplanado va
            where va.ejercicio = anio and va.deleted_at is null
            group by 
                va.clv_upp,va.upp,
                va.clv_ur,va.ur,
                va.clv_programa,va.programa,
                va.clv_subprograma,va.subprograma,
                va.clv_capitulo,
                va.clv_concepto,
                va.clv_partida_generica,
                va.clv_partida_especifica,
                va.clv_tipo_gasto,
                va.partida_especifica;
        END;");
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
