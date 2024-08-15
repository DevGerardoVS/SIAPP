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
		with aux as (
			select *
			from catalogo
			where ejercicio = ',@anio,' and deleted_at is null 
			and grupo_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18)
		)
        select 
            e.id,
            c01.clave clv_sector_publico,c01.descripcion sector_publico,
            c02.clave clv_sector_publico_f,c02.descripcion sector_publico_f,
            c03.clave clv_sector_economia,c03.descripcion sector_economia,
            c04.clave clv_subsector_economia,c04.descripcion subsector_economia,
            c05.clave clv_ente_publico,c05.descripcion ente_publico,
            c06.clave clv_upp,c06.descripcion upp,
            c07.clave clv_subsecretaria,c07.descripcion subsecretaria,
            c08.clave clv_ur,c08.descripcion ur,
            c09.clave clv_finalidad,c09.descripcion finalidad,
            c10.clave clv_funcion,c10.descripcion funcion,
            c11.clave clv_subfuncion,c11.descripcion subfuncion,
            c12.clave clv_eje,c12.descripcion eje,
            c13.clave clv_linea_accion,c13.descripcion linea_accion,
            c14.clave clv_programa_sectorial,c14.descripcion programa_sectorial,
            c15.clave clv_tipologia_conac,c15.descripcion tipologia_conac,
            c16.clave clv_programa,c16.descripcion programa,
            c17.clave clv_subprograma,c17.descripcion subprograma,
            c18.clave clv_proyecto,c18.descripcion proyecto,
            e.presupuestable,
            e.con_mir,
            e.confirmado,
            e.tipo_presupuesto,
            e.ejercicio,
            e.deleted_at,
            e.updated_at,
            e.created_at
        from epp e
		join clasificacion_administrativa ca on e.clasificacion_administrativa_id = ca.id
		join entidad_ejecutora ee on e.entidad_ejecutora_id = ee.id
		join clasificacion_funcional cf on e.clasificacion_funcional_id = cf.id 
		join pladiem p on e.pladiem_id = p.id 
		join conac c on e.conac_id = c.id
		join catalogo c01 on ca.sector_publico_id = c01.id 
		join catalogo c02 on ca.sector_publico_f_id = c02.id 
		join catalogo c03 on ca.sector_economia_id = c03.id 
		join catalogo c04 on ca.subsector_economia_id = c04.id 
		join catalogo c05 on ca.ente_publico_id = c05.id 
		join catalogo c06 on ee.upp_id = c06.id 
		join catalogo c07 on ee.subsecretaria_id = c07.id  
		join catalogo c08 on ee.ur_id = c08.id 
		join catalogo c09 on cf.finalidad_id = c09.id 
		join catalogo c10 on cf.funcion_id = c10.id 
		join catalogo c11 on cf.subfuncion_id = c11.id 
		join catalogo c12 on p.eje_id = c12.id 
		join catalogo c13 on p.linea_accion_id = c13.id 
		join catalogo c14 on p.programa_sectorial_id = c14.id 
		join catalogo c15 on c.tipologia_conac_id = c15.id 
		join catalogo c16 on e.programa_id = c16.id 
		join catalogo c17 on e.subprograma_id = c17.id 
		join catalogo c18 on e.proyecto_id = c18.id
        where e.ejercicio = ',@anio,';
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
