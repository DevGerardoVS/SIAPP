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
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_epp;");

        DB::unprepared("CREATE PROCEDURE sp_epp(in delegacion int,in uppC varchar(3),in urC varchar(2),in anio int)
BEGIN
	set @upp := \"\";
	set @ur := \"\";
	set @del := \"from t_epp e\";
	set @anio := anio;
	if(uppC is not null) then set @upp := CONCAT(\"and e.clv_upp = '\",uppC,\"'\"); end if;
	if(urC is not null) then set @ur := CONCAT(\"and e.clv_ur = '\",urC,\"'\"); end if;
	if(delegacion = 1) then set @del := \"from uppautorizadascpnomina u join t_epp e on u.clv_upp = e.clv_upp\"; end if;
        
	drop temporary table if exists t_epp;
    
	set @queri := concat('
	create temporary table t_epp
	select 
		e.id,
		e.clv_sector_publico,e.sector_publico,
		e.clv_sector_publico_f,e.sector_publico_f,
		e.clv_sector_economia,e.sector_economia,
		e.clv_subsector_economia,e.subsector_economia,
		e.clv_ente_publico,e.ente_publico,
		e.clv_upp,e.upp,
		e.clv_subsecretaria,e.subsecretaria,
		e.clv_ur,e.ur,
		e.clv_finalidad,e.finalidad,
		e.clv_funcion,e.funcion,
		e.clv_subfuncion,e.subfuncion,
		e.clv_eje,e.eje,
		e.clv_linea_accion,e.linea_accion,
		e.clv_programa_sectorial,e.programa_sectorial,
		e.clv_tipologia_conac,e.tipologia_conac,
		e.clv_programa,e.programa,
		e.clv_subprograma,e.subprograma,
		e.clv_proyecto,e.proyecto,
		e.presupuestable,
		e.con_mir,
		e.confirmado,
		e.tipo_presupuesto,
		e.ejercicio,
		e.deleted_at,
		e.updated_at,
		e.created_at
	from v_epp e
	where e.ejercicio = ',@anio,' and e.deleted_at is null;
	');
        
	prepare stmt from @queri;
	execute stmt;
	deallocate prepare stmt;
        
	set @query := CONCAT(\"
	select 
		e.id,
		concat(
			e.clv_sector_publico,
			e.clv_sector_publico_f,
			e.clv_sector_economia,
			e.clv_subsector_economia,
			e.clv_ente_publico
		) clasificacion_administrativa,
		concat(
			e.clv_upp,' ',
			e.upp
		) upp,
		concat(
			e.clv_subsecretaria,' ',
			e.subsecretaria
 		) subsecretaria,
		concat(
			e.clv_ur,' ',
			e.ur
		) unidad_responsable,
		concat(
			e.clv_finalidad,' ',
			e.finalidad
		) finalidad,
		concat(
			e.clv_funcion,' ',
			e.funcion
		) funcion,
		concat(
			e.clv_subfuncion,' ',
			e.subfuncion
		) subfuncion,
		concat(
			e.clv_eje,' ',
			e.eje
		) eje,
		concat(
			e.clv_linea_accion,' ',
			e.linea_accion
		) linea_accion,
		concat(
			e.clv_programa_sectorial,' ',
			e.programa_sectorial
		) programa_sectorial,
		concat(
			e.clv_tipologia_conac,' ',
			e.tipologia_conac
		) tipologia_conac,
		concat(
			e.clv_programa,' ',
			e.programa
		) programa,
		concat(
			e.clv_subprograma,' ',
			e.subprograma
		) subprograma,
		concat(
			e.clv_proyecto,' ',
			e.proyecto
		) proyecto,
		e.ejercicio,
		e.confirmado
	\",@del,\"
	where e.deleted_at is null
	\",@upp,\" \",@ur,\" order by e.clv_upp,e.clv_subsecretaria,e.clv_ur,
	e.clv_finalidad,e.clv_funcion,e.clv_subfuncion,
	e.clv_eje,e.clv_linea_accion,e.clv_programa_sectorial,e.clv_tipologia_conac,
	e.clv_programa,e.clv_subprograma,e.clv_proyecto
	\");
    
	prepare stmt  from @query;
	execute stmt;
	deallocate prepare stmt;
        
	drop temporary table if exists t_epp;
end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_epp;");
    }
};
