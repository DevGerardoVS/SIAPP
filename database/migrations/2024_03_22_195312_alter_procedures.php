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
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");

        DB::unprepared("CREATE PROCEDURE avance_proyectos_actividades_upp(in anio int, in corte date)
        begin
            set @corte := 'deleted_at is null';
            set @tabla := 'programacion_presupuesto';
        
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
            end if;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
        
            set @query := CONCAT('
            create temporary table aux_0
            select 
                upp clv_upp,count(area) claves,
                case
                    when estado = 1 then \"Confirmado\"
                    else \"Registrado\"
                end estatus
            from (
                select distinct
                    upp,
                    concat(
                        ur,finalidad,funcion,subfuncion,eje,linea_accion,
                        programa_sectorial,tipologia_conac,programa_presupuestario,
                        subprograma_presupuestario,proyecto_presupuestario,fondo_ramo
                    ) area,
                    pp.estado
                from ',@tabla,' pp
                where ejercicio = ',anio,' and ',@corte,'
            )t
            group by upp,estado;
            ');
        
            prepare stmt  from @query;
            execute stmt;
            deallocate prepare stmt;
        
            create temporary table aux_1
            with aux as (
                select distinct
                    clv_upp,claves mir,estatus
                from (
                    select mm.clv_upp,concat(mm.clv_ur,mm.area_funcional,m.clv_fondo) claves,m.estatus
                    from metas m
                    join mml_mir mm on m.mir_id = mm.id
                    where m.ejercicio = anio and m.deleted_at is null
                    union all 
                    select ma.clv_upp,concat(substr(ma.entidad_ejecutora,5,2),ma.area_funcional,m.clv_fondo) claves,m.estatus
                    from metas m
                    join mml_actividades ma on m.actividad_id = ma.id
                    where m.ejercicio = anio and m.deleted_at is null
                )t
            )
            select
                clv_upp,COUNT(mir) mir,
                case
                    when estatus = 0 then 'Registrado'
                    when estatus = 1 then 'Confirmado'
                    when estatus = 2 then 'Metas Seguimientos'
                end estatus
            from aux
            group by clv_upp,estatus;
        
            with aux as (
                select 
                    ve.clv_upp,ve.upp,
                    case 
                        when a0.claves is null then 0
                        else a0.claves
                    end claves,
                    case 
                        when a1.mir is null then 0 
                        else a1.mir
                    end mir,
                    case 
                        when a0.estatus is null then 'Sin Registrar'
                        else a0.estatus
                    end estatus_claves,
                    case 
                        when a1.estatus is null then 'Sin Registrar'
                        else a1.estatus
                    end estatus_mir
                from (select distinct clv_upp,upp from v_epp where ejercicio = anio and deleted_at is null) ve
                left join aux_0 a0 on ve.clv_upp = a0.clv_upp
                left join aux_1 a1 on ve.clv_upp = a1.clv_upp
            )
            select 
                clv_upp,upp,claves,mir,
                case 
                    when claves = 0 then 0
                    else round((mir/claves)*100)
                end avance,
                estatus_claves,estatus_mir
            from aux a;
        
            drop temporary table if exists aux_0;
            drop temporary table if exists aux_1;
        end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS avance_proyectos_actividades_upp;");
    }
};
