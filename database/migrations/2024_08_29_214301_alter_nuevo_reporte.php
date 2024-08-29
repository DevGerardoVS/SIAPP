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
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2025;");

        DB::unprepared("CREATE PROCEDURE reporte_art_20_frac_X_a_num_1_2025(in anio int,in ver int)
BEGIN
    set @tabla := 'programacion_presupuesto';
    set @corte := 'deleted_at is null';
    if(ver is not null) then 
        set @tabla := 'programacion_presupuesto_hist';
        set @corte := concat('version = ',ver);
    end if;

    drop temporary table if exists cap_concepto;
    drop temporary table if exists totales;

    create temporary table cap_concepto
    select distinct
        c1.clave clv_capitulo,c1.descripcion capitulo,
        c2.clave clv_concepto,c2.descripcion concepto,
        concat(c1.clave,c2.clave) llave
    from clasificacion_economica ce 
    join catalogo c1 on ce.capitulo_id = c1.id
    join catalogo c2 on ce.concepto_id = c2.id
    where ce.deleted_at is null;

    set @queri := concat('
    create temporary table totales
    with aux as (
        select clv_capitulo,clv_concepto,llave,sum(total) total 
        from (
            select 
                substr(posicion_presupuestaria,1,1) clv_capitulo,
                substr(posicion_presupuestaria,2,1) clv_concepto,
                substr(posicion_presupuestaria,1,2) llave,total
            from ',@tabla,'
            where ejercicio = ',anio,' and ',@corte,'
        )t group by clv_capitulo,clv_concepto,llave
    )
    select 
        cc.llave,cc.capitulo,cc.concepto,
        case 
            when total is null then 0
            else total
        end total
    from cap_concepto cc
    left join aux a on cc.llave = a.llave;
    ');

    prepare stmt from @queri;
    execute stmt;
    deallocate prepare stmt;

    select 
        llave orden,'' capitulo,
        concepto,total importe
    from totales
    union all
    select
        concat(substr(llave,1,1),'0') orden,
        capitulo,'' concepto,
        sum(total) importe
    from totales
    group by capitulo
    order by orden;

    drop temporary table if exists cap_concepto;
    drop temporary table if exists totales;
END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS reporte_art_20_frac_X_a_num_1_2025;");
    }
};
