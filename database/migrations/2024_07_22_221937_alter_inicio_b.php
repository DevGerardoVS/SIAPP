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
        DB::unprepapred("DROP PROCEDURE IF EXISTS inicio_b;");

        DB::unprepared("CREATE PROCEDURE inicio_b(in anio int)
begin
	select 
		clv_fondo clave,
		f.descripcion fondo,
		asignado,
		programado,
		(programado/asignado)*100 avance,
		ejercicio
	from (
		select 
			asig.clv_fondo,
			case 
				when asignado is null then 0
				else asignado
			end asignado,
			case 
				when programado is null then 0
				else programado
			end programado,
			asig.ejercicio
		from (
			select 
				clv_fondo,
				sum(presupuesto) asignado,
				tf.ejercicio
			from techos_financieros tf
			where deleted_at is null and ejercicio = anio
			group by clv_fondo,ejercicio
		) asig
		left join (
			select 
				fondo_ramo clv_fondo,
				sum(total) programado,
				ejercicio
			from programacion_presupuesto
			where deleted_at is null and ejercicio = anio
			group by clv_fondo,ejercicio
		)prog 
		on asig.clv_fondo = prog.clv_fondo and asig.ejercicio = prog.ejercicio
		order by ejercicio,clv_fondo
	)t2 
	left join (
		select clave,descripcion from catalogo
		where deleted_at is null and grupo_id = 37 and ejercicio = anio
	) f on t2.clv_fondo = f.clave;
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepapred("DROP PROCEDURE IF EXISTS inicio_b;");
    }
};
