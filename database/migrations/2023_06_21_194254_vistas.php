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
        //
        DB::unprepared("CREATE VIEW IF NOT EXISTS inicio_a AS
            SELECT presupuesto_asignado,
                presupuesto_calendarizado,
                presupuesto_asignado - presupuesto_calendarizado as disponible,
                (presupuesto_calendarizado / SUM(presupuesto_asignado) ) * 100 as avance
            FROM
                (select sum(presupuesto) as presupuesto_asignado, 1 as def from techos_financieros where ejercicio=YEAR(CURDATE())) as t1
                inner join 
                (select sum(total) as presupuesto_calendarizado, 1 as def from programacion_presupuesto where ejercicio=YEAR(CURDATE())) as t2 
                on t1.def = t2.def;");


        DB::unprepared("CREATE VIEW IF NOT EXISTS inicio_b AS
            SELECT t2.clave, fondo, asignado, programado, programado / asignado *100 AS avance FROM
                (SELECT 
                programacion_presupuesto.fondo_ramo AS clave, fondo.fondo_ramo AS fondo, sum(total) AS programado
                FROM
                    programacion_presupuesto
                INNER JOIN 
                    fondo ON fondo.clv_fondo_ramo=programacion_presupuesto.fondo_ramo
                WHERE
                    programacion_presupuesto.ejercicio=YEAR(CURDATE())
                GROUP BY fondo.fondo_ramo) AS t1
                RIGHT JOIN (SELECT 
                clv_fondo_ramo AS clave,
                sum(presupuesto) AS asignado
                FROM
                    fondo
                    INNER JOIN techos_financieros ON clv_fondo_ramo=techos_financieros.clv_fondo
                WHERE techos_financieros.ejercicio = YEAR(CURDATE())
                group by clave) AS t2 ON t1.clave=t2.clave;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared("DROP VIEW IF EXISTS inicio_a");
        DB::unprepared("DROP VIEW IF EXISTS inicio_b");
    }
};
