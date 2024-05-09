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
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");

        DB::unprepared("CREATE PROCEDURE mml_comprobacion(in upp varchar(3),in programa varchar(2),in ur varchar(2),in anio int,in corte date)
        begin
            set @upp := '';
            set @upp2 := '';
            set @programa := '';
            set @programa2 := '';
            set @ur := '';
            set @ur2 := '';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            set @mir := 'mml_mir';
            
            if(upp is not null) then 
                set @upp := CONCAT('and mm.clv_upp = \"',upp,'\"'); 
                set @upp2 := CONCAT('where clv_upp = \"',upp,'\"'); 
            end if;
            if(programa is not null) then
                set @programa := CONCAT('and mm.clv_pp = \"',programa,'\"'); 
                if(upp is not null) then
                    set @programa2 := CONCAT('and clv_pp = \"',programa,'\"'); 
                else
                    set @programa2 := CONCAT('where clv_pp = \"',programa,'\"'); 
                end if;
            end if;
            if(ur is not null) then 
                set @ur := CONCAT('and mm.clv_ur = \"',ur,'\"'); 
                set @ur2 := CONCAT('and clv_ur = \"',ur,'\"'); 
            end if;
            if(corte is not null) then 
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
                set @mir := 'mml_mir_hist';
            end if;
                    
            set @queri := concat(\"
                select
                    case 
                        when nivel = 9 then clv_upp
                        else ''
                    end clv_upp,
                    case 
                        when nivel = 9 then clv_pp
                        else ''
                    end clv_pp,
                    case 
                        when nivel = 9 then clv_ur
                        else ''
                    end clv_ur,
                    case 
                        when nivel != 9 then area_funcional
                        else ''
                    end area_funcional,
                    case 
                        when nivel != 9 then proyecto
                        else ''
                    end nombre_proyecto,
                    case 
                        when nivel = 10 then 'Componente'
                        when nivel = 11 then 'Actividad'
                        else ''
                    end nivel,
                    objetivo,
                    indicador 
                from (
                    select *
                    from (
                        select
                            mm.\",@id,\" id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            c.descripcion proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from \",@mir,\" mm
                        join \",@epp,\" ve on ve.\",@id,\" = mm.id_epp
                        left join \",@catalogo,\" c on ve.proyecto_id = c.\",@id,\"
                        where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                        and nivel in (10) \",@upp,\" \",@ur,\" \",@programa,\"
                        union all 
                        select
                            mm.componente_padre id,
                            mm.clv_upp,
                            mm.clv_pp,
                            mm.clv_ur,
                            mm.area_funcional,
                            c.descripcion proyecto,
                            mm.nivel,
                            mm.objetivo,
                            mm.indicador
                        from \",@mir,\" mm
                        join \",@epp,\" ve on ve.\",@id,\" = mm.id_epp
                        left join \",@catalogo,\" c on ve.proyecto_id = c.\",@id,\"
                        where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                        and nivel in (11) \",@upp,\" \",@ur,\" \",@programa,\"
                        union all
                        select * from (
                            select 
                                0 id,c1.clave clv_upp,c2.clave clv_pp,c3.clave clv_ur,
                                '' area_funcional,'' proyecto,9 nivel,'' objetivo,'' indicador
                            from (
                                select distinct
                                    upp_id,programa_id,ur_id
                                from \",@epp,\"
                                where ejercicio = \",anio,\" and \",@corte,\"
                            )t
                            left join \",@catalogo,\" c1 on t.upp_id = c1.\",@id,\"
                            left join \",@catalogo,\" c2 on t.programa_id = c2.\",@id,\"
                            left join \",@catalogo,\" c3 on t.ur_id = c3.\",@id,\"
                        ) ve \",@upp2,\"\",@programa2,\"\",@ur2,\"
                    )t 
                    group by clv_upp,clv_pp,clv_ur,id,nivel
                    order by clv_upp,clv_pp,clv_ur,id,nivel
                )t2;
            \");
             
            prepare stmt  from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS mml_comprobacion;");
    }
};
