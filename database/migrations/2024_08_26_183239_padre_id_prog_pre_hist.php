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
        DB::unprepared("INSERT INTO programacion_presupuesto_hist(
            id_original,version,clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,
            subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,
            anio,etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,
            enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,
            estado,tipo,created_user,updated_user,deleted_user,created_at,updated_at,deleted_at,estatus_sapp
        )
        select
            id,0 version,clasificacion_administrativa,entidad_federativa,region,municipio,localidad,upp,subsecretaria,ur,
            finalidad,funcion,subfuncion,eje,linea_accion,programa_sectorial,tipologia_conac,programa_presupuestario,
            subprograma_presupuestario,proyecto_presupuestario,periodo_presupuestal,posicion_presupuestaria,tipo_gasto,anio,
            etiquetado,fuente_financiamiento,ramo,fondo_ramo,capital,proyecto_obra,ejercicio,
            enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre,total,estado,tipo,
            created_user,updated_user,'SISTEMA' deleted_user,created_at,updated_at,NOW() deleted_at,estatus_sapp
        from programacion_presupuesto
        where ejercicio = 2024 and deleted_at is null;");

        DB::unprepared("DELETE FROM programacion_presupuesto WHERE ejercicio = 2024;");

        DB::unprepared("UPDATE catalogo c
        JOIN pladiem p ON p.objetivo_sectorial_id = c.id
        SET c.padre_id = p.eje_id;");

        DB::unprepared("UPDATE catalogo c
        JOIN pladiem p ON p.estrategia_id = c.id
        SET c.padre_id = p.objetivo_sectorial_id;");

        DB::unprepared("UPDATE catalogo c
        JOIN pladiem p ON p.linea_accion_id = c.id
        SET c.padre_id = p.estrategia_id;");
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
