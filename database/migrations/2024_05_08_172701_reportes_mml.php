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
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE if exists mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE if exists mml_alineacion;");
        DB::unprepared("DROP PROCEDURE if exists mml_matrices_indicadores;");

        DB::unprepared("CREATE PROCEDURE reporte_seguimiento_2(in anio int,in upp_v varchar(3),in ur_v varchar(2),in programa_v varchar(2),in fondo_v varchar(2),in capitulo_v varchar(6))
        begin
            set @upp := '';
            set @ur := '';
            set @programa := '';
            set @fondo := '';
            set @capitulo := '';
                
            if(upp_v is not null) then 
                set @upp := concat(\"where clv_upp = '\",upp_v,\"'\");
            end if;
            if(ur_v is not null) then 
                set @ur := concat(\" and clv_ur = '\",ur_v,\"'\");
            end if;
            if(programa_v is not null) then 
                set @programa := concat(\" and clv_programa = '\",programa_v,\"'\");
            end if;
            if(fondo_v is not null) then 
                set @fondo := concat(\" and clv_fondo = '\",fondo_v,\"'\");
            end if;
            if(capitulo_v is not null) then 
                if(upp_v is not null) then
                    set @capitulo := concat(\" and clv_partida = '\",capitulo_v,\"'\");
                else
                    set @capitulo := concat(\"where clv_partida = '\",capitulo_v,\"'\");
                end if;
            end if;
                
            set @queri := concat(\"
            with aux as (
                select 
                    clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,mes_n,actividad,unidad_medida,beneficiarios,
                    mes,programado,realizado,(realizado-programado) diferencia,
                    case
                        when programado = 0 then 100
                        else truncate(((realizado/programado)*100),2)
                    end avance,
                    justificacion,propuesta_mejora
                from (
                    select 
                        ss.clv_upp,ss.clv_ur,ss.clv_programa,m.clv_fondo,rm.partida clv_partida,ss.mes mes_n,
                        case 
                            when m.mir_id is not null then mm.indicador
                            when m.actividad_id is not null and ma.nombre is not null then ma.nombre
                            when m.mir_id is null and ma.nombre is null then c.descripcion
                        end actividad,
                        um.unidad_medida,
                        concat(m.cantidad_beneficiarios,' ',b.beneficiario) beneficiarios,
                        case 
                            when ss.mes = 1 then 'Enero'
                            when ss.mes = 2 then 'Febrero'
                            when ss.mes = 3 then 'Marzo'
                            when ss.mes = 4 then 'Abril'
                            when ss.mes = 5 then 'Mayo'
                            when ss.mes = 6 then 'Junio'
                            when ss.mes = 7 then 'Julio'
                            when ss.mes = 8 then 'Agosto'
                            when ss.mes = 9 then 'Septiembre'
                            when ss.mes = 10 then 'Octubre'
                            when ss.mes = 11 then 'Noviembre'
                            when ss.mes = 12 then 'Diciembre'
                        end mes,
                        case 
                            when ss.mes = 1 then m.enero
                            when ss.mes = 2 then m.febrero
                            when ss.mes = 3 then m.marzo
                            when ss.mes = 4 then m.abril
                            when ss.mes = 5 then m.mayo
                            when ss.mes = 6 then m.junio
                            when ss.mes = 7 then m.julio
                            when ss.mes = 8 then m.agosto
                            when ss.mes = 9 then m.septiembre
                            when ss.mes = 10 then m.octubre
                            when ss.mes = 11 then m.noviembre
                            when ss.mes = 12 then m.diciembre
                        end programado,
                        ss.realizado,
                        ss.justificacion,
                        ss.propuesta_mejora
                    from sapp_seguimiento ss
                    left join metas m on ss.meta_id = m.id
                    left join sapp_rel_metas_partidas rm on ss.meta_id = rm.meta_id
                    left join mml_actividades ma on m.actividad_id = ma.id
                    left join mml_mir mm on m.mir_id = mm.id
                    left join catalogo c on ma.id_catalogo = c.id
                    left join beneficiarios b on m.beneficiario_id = b.id
                    left join unidades_medida um on m.unidad_medida_id = um.id
                    where ss.ejercicio = \",anio,\" and ss.deleted_at is null
                    order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,actividad,mes
                )t \",@upp,\"\",@ur,\"\",@programa,\"\",@fondo,\"\",@capitulo,\"
            )
            select *
            from (
                select distinct 
                    clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,0 mes_n,actividad,unidad_medida,beneficiarios,
                    '' mes,'' programado,'' realizado,'' diferencia,'' avance,'' justificacion,'' propuesta_mejora
                from aux
                union all
                select * from aux
                order by clv_upp,clv_ur,clv_programa,clv_fondo,clv_partida,actividad,mes_n
            )t;
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        end;");

        DB::unprepared("CREATE PROCEDURE mml_matrices_indicadores(in anio int,in trimestre_n int,in semaforo int,in corte date,in upp_v varchar(3))
        begin
            drop temporary table if exists aux_1;
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists epp_aux;
            drop temporary table if exists seguimiento;
            drop temporary table if exists aux_2;
        
            set @trimestre := '';
            if(trimestre_n = 1) then set @trimestre := '(1,2,3)'; end if;
            if(trimestre_n = 2) then set @trimestre := '(1,2,3,4,5,6)'; end if;
            if(trimestre_n = 3) then set @trimestre := '(1,2,3,4,5,6,7,8,9)'; end if;
            if(trimestre_n = 4) then set @trimestre := '(1,2,3,4,5,6,7,8,9,10,11,12)'; end if;
        
            set @catalogo := 'catalogo';
            set @corte := 'deleted_at is null';
            set @id := 'id';
            set @epp := 'epp';
            set @mir := 'mml_mir';
            set @metas := 'metas';
            set @upp := '';
            if(corte is not null) then 
                set @catalogo := 'catalogo_hist';
                set @corte := concat('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @id := 'id_original';
                set @epp := 'epp_hist';
                set @mir := 'mml_mir_hist';
                set @metas := 'metas_hist';
            end if;
        
            if(upp_v is not null) then 
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
                    
            set @queri := concat(\"
            create temporary table seguimiento
            select 
                meta_id,
                sum(realizado) realizado
            from sapp_seguimiento ss 
            where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"\",@upp,\"
            group by meta_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
            if(upp_v is not null) then 
                set @upp := concat(\" and mm.clv_upp = '\",upp_v,\"'\");
            end if;
        
            set @queri := concat(\"
            create temporary table aux_1
            select 
                mm.clv_upp,
                mm.clv_pp clv_programa,
                mm.clv_ur,
                mm.nivel,
                case 
                    when mm.nivel = 8 then 'Fin'
                    when mm.nivel = 9 then 'Propósito'
                    when mm.nivel = 10 then 'Componente'
                    when mm.nivel = 11 then 'Actividad'
                end tipo_indicador,
                case 
                   when mm.nivel = 10
                   then mm.id
                   else mm.componente_padre
                end padre,
                mm.objetivo resumen_narrativo,
                mm.indicador nombre_indicador,
                mm.definicion_indicador,
                mm.metodo_calculo,
                mm.descripcion_metodo,
                case 
                    when mm.frecuencia_medicion = 29 then 'Quincenal'
                    when mm.frecuencia_medicion = 30 then 'Mensual'
                    when mm.frecuencia_medicion = 31 then 'Bimestral'
                    when mm.frecuencia_medicion = 32 then 'Trimestral'
                    when mm.frecuencia_medicion = 33 then 'Cuatrimestral'
                    when mm.frecuencia_medicion = 34 then 'Semestral'
                    when mm.frecuencia_medicion = 35 then 'Anual'
                    when mm.frecuencia_medicion = 36 then 'Bianual'
                    when mm.frecuencia_medicion = 37 then 'Quinquenal'
                    when mm.frecuencia_medicion = 38 then 'Sexenal'
                end frecuencia_medicion,
                um.unidad_medida,
                case 
                    when mm.dimension = 21 then 'Eficacia'
                    when mm.dimension = 22 then 'Eficiencia'
                    when mm.dimension = 23 then 'Calidad'
                    when mm.dimension = 24 then 'Economía'
                end dimension,
                mm.medios_verificacion,
                m.total meta_anual,
                case 
                    when ss.realizado is null and mm.nivel = 11 then 0
                    else ss.realizado
                end trimestre,
                case
                    when ss.realizado is null then 0
                    when ss.realizado = 0 then 0
                    when m.total = 0 then 100
                    when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
                end avance
            from \",@mir,\" mm
            left join unidades_medida um on mm.unidad_medida = um.id
            left join \",@metas,\" m on mm.\",@id,\" = m.mir_id
            left join seguimiento ss on ss.meta_id = m.\",@id,\"
            where mm.ejercicio = \",anio,\" and mm.\",@corte,\"\",@upp,\"
            order by clv_upp,clv_programa,clv_ur,padre,nivel;
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                    
            set @queri := concat(\"
            create temporary table catalogo_aux
            select 
                \",@id,\" id,clave clv_programa,descripcion programa
            from \",@catalogo,\" 
            where ejercicio = \",anio,\" and \",@corte,\" and grupo_id in (16);
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                    
            set @queri := concat(\"
            create temporary table epp_aux
            with aux as (
                select distinct
                    upp_id,
                    ur_id
                from \",@epp,\"
                where ejercicio = \",anio,\" and \",@corte,\"
            )
            select 
                c1.clave clv_upp, c1.descripcion upp,
                c2.clave clv_ur, c2.descripcion ur
            from aux a
            left join \",@catalogo,\" c1 on c1.\",@id,\" = a.upp_id
            left join \",@catalogo,\" c2 on c2.\",@id,\" = a.ur_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
            
            set @queri := concat(\"
            create temporary table aux_2
            select 
                clv_upp,upp,clv_programa,programa,clv_ur,
                case 
                    when ur is null then ''
                    else ur
                end ur,
                nivel,tipo_indicador,padre,resumen_narrativo,
                nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,frecuencia_medicion,
                unidad_medida,dimension,medios_verificacion,meta_anual,trimestre,avance,
                case 
                    when avance <= 60 then 0
                    when avance > 60 and avance <= 94 then 1
                    when avance > 94 and avance <= 110 then 2
                    when avance > 110 then 3
                end color
            from (
                select
                    a1.clv_upp,c1.upp,a1.clv_programa,ca.programa,a1.clv_ur,ea.ur,a1.nivel,tipo_indicador,padre,resumen_narrativo,
                    nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,frecuencia_medicion,
                    unidad_medida,dimension,medios_verificacion,meta_anual,trimestre,avance
                from aux_1 a1
                left join catalogo_aux ca on a1.clv_programa = ca.clv_programa
                left join epp_aux ea on a1.clv_upp = ea.clv_upp and a1.clv_ur = ea.clv_ur
                left join (
                    select clave clv_upp,descripcion upp
                    from \",@catalogo,\" where ejercicio = \",anio,\"
                    and \",@corte,\" and grupo_id = 6
                ) c1 on a1.clv_upp = c1.clv_upp
            )t
            order by clv_upp,clv_programa,clv_ur,padre,nivel;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                
            set @semaforo := \"\";
            if(semaforo is not null) then set @semaforo := concat(\"where color = \",semaforo); end if;
        
            set @queri := concat(\"
            select *
            from aux_2 \",@semaforo,\"
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
            drop temporary table if exists aux_1;
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists epp_aux;
            drop temporary table if exists seguimiento;
            drop temporary table if exists aux_2;
        end;");

        DB::unprepared("CREATE PROCEDURE mml_alineacion(in anio int,in trimestre_n int,in semaforo int,in corte date,in upp_v varchar(3))
        begin
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists mir_metas;
            drop temporary table if exists estrategia_ods;
            drop temporary table if exists plan_desarrollo;
            drop temporary table if exists seguimiento;
            drop temporary table if exists t_final;
                    
            set @tabla := 'programacion_presupuesto';
            set @corte := 'deleted_at is null';
            set @epp := 'epp';
            set @catalogo := 'catalogo';
            set @id := 'id';
            set @mir := 'mml_mir';
            set @metas := 'metas';
            set @upp := '';
                
            if (corte is not null) then 
                set @tabla := 'programacion_presupuesto_hist';
                set @corte := CONCAT('deleted_at between \"',corte,'\" and DATE_ADD(\"',corte,'\", INTERVAL 1 HOUR)');
                set @epp := 'epp_hist';
                set @catalogo := 'catalogo_hist';
                set @id := 'id_original';
                set @mir := 'mml_mir_hist';
                set @metas := 'metas_hist';
            end if;
           
            if(upp_v is not null) then
                set @upp := concat(\" and clv_upp = '\",upp_v,\"'\");
            end if;
                
            set @trimestre := '';
            if(trimestre_n = 1) then set @trimestre := '(1,2,3)'; end if;
            if(trimestre_n = 2) then set @trimestre := '(1,2,3,4,5,6)'; end if;
            if(trimestre_n = 3) then set @trimestre := '(1,2,3,4,5,6,7,8,9)'; end if;
            if(trimestre_n = 4) then set @trimestre := '(1,2,3,4,5,6,7,8,9,10,11,12)'; end if;
            
            set @queri := concat(\"
            create temporary table seguimiento
            select 
                meta_id,
                sum(realizado) realizado
            from sapp_seguimiento ss 
            where ejercicio = \",anio,\" and deleted_at is null and mes in \",@trimestre,\"\",@upp,\"
            group by meta_id;
            \");
        
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                
            set @queri := concat(\"
            create temporary table catalogo_aux
            with aux as (
                select distinct
                    upp_id,programa_id
                from \",@epp,\" e
                where ejercicio = \",anio,\" and \",@corte,\"
            )
            select 
                c1.clave clv_upp,c1.descripcion upp,
                c2.clave clv_programa,c2.descripcion programa
            from aux 
            left join \",@catalogo,\" c1 on aux.upp_id = c1.\",@id,\"
            left join \",@catalogo,\" c2 on aux.programa_id = c2.\",@id,\";
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
           
             if(upp_v is not null) then
                set @upp := concat(\" and mm.clv_upp = '\",upp_v,\"'\");
            end if;
                 
            set @queri := concat(\"
                create temporary table mir_metas
                select
                    t.clv_upp,ca.upp,t.clv_programa,ca.programa,t.clv_ur,c1.descripcion ur,nivel,
                    tipo_indicador,padre,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
                    descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,medios_verificacion,
                    programado,avance,concat(c2.clave,'. ',c2.descripcion) eje,c3.descripcion linea_accion,
                    case 
                        when substr(c3.descripcion,8,1) = '.' or substr(c3.descripcion,8,1) = ' '
                        then substr(c3.descripcion,1,7)
                        else substr(c3.descripcion,1,8)
                    end clv_cpladem
                from (
                    select 
                        mm.clv_upp,
                        mm.clv_pp clv_programa,
                        mm.clv_ur,
                        nivel,
                        case 
                            when nivel = 8 then 'Fin'
                            when nivel = 9 then 'Propósito'
                            when nivel = 10 then 'Componente'
                            when nivel = 11 then 'Actividad'
                        end tipo_indicador,
                        case 
                            when mm.nivel = 10
                            then mm.id
                            else mm.componente_padre
                        end padre,
                        objetivo,
                        indicador nombre_indicador,
                        definicion_indicador,
                        metodo_calculo,
                        descripcion_metodo,
                        case 
                            when frecuencia_medicion = 29 then 'Quincenal'
                            when frecuencia_medicion = 30 then 'Mensual'
                            when frecuencia_medicion = 31 then 'Bimestral'
                            when frecuencia_medicion = 32 then 'Trimestral'
                            when frecuencia_medicion = 33 then 'Cuatrimestral'
                            when frecuencia_medicion = 34 then 'Semestral'
                            when frecuencia_medicion = 35 then 'Anual'
                            when frecuencia_medicion = 36 then 'Bianual'
                            when frecuencia_medicion = 37 then 'Quinquenal'
                            when frecuencia_medicion = 38 then 'Sexenal'
                        end frecuencia_medicion,
                        um.unidad_medida,
                        case 
                            when dimension = 21 then 'Eficacia'
                            when dimension = 22 then 'Eficiencia'
                            when dimension = 23 then 'Calidad'
                            when dimension = 24 then 'Economía'
                        end dimension,
                        medios_verificacion,
                        m.total programado,
                        case
                            when ss.realizado is null then 0
                            when ss.realizado = 0 then 0
                            when m.total = 0 then 100
                            when ss.realizado > 0 then truncate(((ss.realizado/m.total)*100),2)
                        end avance,
                        mm.id_epp
                    from \",@mir,\" mm
                    left join unidades_medida um on mm.unidad_medida = um.id
                    left join \",@metas,\" m on m.mir_id = mm.\",@id,\" and m.deleted_at is null
                    left join seguimiento ss on ss.meta_id = m.\",@id,\"
                    where mm.ejercicio = \",anio,\" and mm.\",@corte,\"\",@upp,\"
                    order by clv_upp,clv_programa,clv_ur,nivel
                )t
                left join catalogo_aux ca on t.clv_upp = ca.clv_upp and t.clv_programa = ca.clv_programa
                left join \",@epp,\" e on t.id_epp = e.\",@id,\"
                left join \",@catalogo,\" c1 on e.ur_id = c1.\",@id,\"
                left join \",@catalogo,\" c2 on e.eje_id = c2.\",@id,\"
                left join \",@catalogo,\" c3 on e.linea_accion_id = c3.\",@id,\"
                order by clv_upp,clv_programa,clv_ur,padre,nivel;
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
                    
            create temporary table estrategia_ods
            with aux as (
                select 
                    clv_estrategia,plan_nacional,
                    concat(group_concat(ods separator '_'),'________') ods
                from (
                    select distinct
                        clv_estrategia,
                        concat(clv_plan_nacional,'. ',plan_nacional) plan_nacional,
                        cast(clv_ods as unsigned) ods_n,
                        concat(clv_ods,'. ',ods) ods
                    from mml_objetivos_desarrollo_sostenible mo
                    where deleted_at is null
                    order by clv_estrategia,ods_n
                )t group by clv_estrategia,plan_nacional
            )
            select 
                clv_estrategia,plan_nacional,
                substring_index(ods,'_',1) ods_1,
                substring_index(substring_index(ods,'_',2),'_',-1) ods_2,
                substring_index(substring_index(ods,'_',3),'_',-1) ods_3,
                substring_index(substring_index(ods,'_',4),'_',-1) ods_4,
                substring_index(substring_index(ods,'_',5),'_',-1) ods_5,
                substring_index(substring_index(ods,'_',6),'_',-1) ods_6,
                substring_index(substring_index(ods,'_',7),'_',-1) ods_7,
                substring_index(substring_index(ods,'_',8),'_',-1) ods_8
            from aux;
            
            create temporary table plan_desarrollo
            with aux as (
                select distinct
                    clv_objetivo_sectorial,objetivo_sectorial,
                    clv_estrategia,estrategia,clv_cpladem_linea_accion
                from mml_objetivo_sectorial_estrategia
                where deleted_at is null
            )
            select 
                a.clv_cpladem_linea_accion clv_cpladem,
                a.clv_objetivo_sectorial,a.objetivo_sectorial,
                eo.clv_estrategia,a.estrategia,
                eo.plan_nacional,ods_1,ods_2,ods_3,
                ods_4,ods_5,ods_6,ods_7,ods_8
            from estrategia_ods eo
            left join aux a on eo.clv_estrategia = a.clv_estrategia;
                    
            create temporary table t_final
            with aux as (
                select 
                    clv_upp,upp,clv_programa,programa,clv_ur,ur,nivel,tipo_indicador,padre,objetivo,
                    nombre_indicador,definicion_indicador,metodo_calculo,descripcion_metodo,
                    frecuencia_medicion,unidad_medida,dimension,medios_verificacion,programado,avance,
                    case 
                        when avance <= 60 then 0
                        when avance > 60 and avance <= 94 then 1
                        when avance > 94 and avance <= 110 then 2
                        when avance > 110 then 3
                    end color,
                    eje,concat(pd.clv_objetivo_sectorial,'. ',pd.objetivo_sectorial) objetivo_sectorial,
                    concat(pd.clv_estrategia,'. ',pd.estrategia) estrategia,
                    linea_accion,plan_nacional,ods_1,ods_2,ods_3,ods_4,ods_5,ods_6,ods_7,ods_8
                from mir_metas mm
                left join plan_desarrollo pd on mm.clv_cpladem = pd.clv_cpladem
                order by clv_upp,clv_programa,clv_ur,padre,nivel
            )
            select 
                clv_upp,upp,clv_programa,programa,clv_ur,
                case 
                    when ur is null then ''
                    else ur
                end ur,
                nivel,tipo_indicador,padre,objetivo,nombre_indicador,definicion_indicador,metodo_calculo,
                descripcion_metodo,frecuencia_medicion,unidad_medida,dimension,medios_verificacion,
                case 
                    when programado is null then ''
                    else programado
                end programado,
                avance,color,
                case 
                    when eje is null then ''
                    else eje
                end eje,
                case 
                    when objetivo_sectorial is null then ''
                    else objetivo_sectorial
                end objetivo_sectorial,
                case 
                    when estrategia is null then ''
                    else estrategia
                end estrategia,
                case 
                    when linea_accion is null then ''
                    else linea_accion
                end linea_accion,
                case 
                    when plan_nacional is null then ''
                    else plan_nacional
                end plan_nacional,
                case when ods_1 is null then '' else ods_1 end ods_1,
                case when ods_2 is null then '' else ods_2 end ods_2,
                case when ods_3 is null then '' else ods_3 end ods_3,
                case when ods_4 is null then '' else ods_4 end ods_4,
                case when ods_5 is null then '' else ods_5 end ods_5,
                case when ods_6 is null then '' else ods_6 end ods_6,
                case when ods_7 is null then '' else ods_7 end ods_7,
                case when ods_8 is null then '' else ods_8 end ods_8
            from aux;
                
            set @semaforo := \"\";
            if(semaforo is not null) then set @semaforo := concat(\"where color = \",semaforo); end if;
                
            set @queri := concat(\"
            select *
            from t_final \",@semaforo,\"
            order by clv_upp,clv_programa,clv_ur,padre,nivel
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
        
            drop temporary table if exists catalogo_aux;
            drop temporary table if exists mir_metas;
            drop temporary table if exists estrategia_ods;
            drop temporary table if exists plan_desarrollo;
            drop temporary table if exists seguimiento;
            drop temporary table if exists t_final;
        END;");

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
           DROP TEMPORARY TABLE if EXISTS epp_t;
            
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
            CREATE TEMPORARY TABLE epp_t
            SELECT DISTINCT
                c06.clave clv_upp,c08.clave clv_ur,c16.clave clv_pp,c18.descripcion proyecto,
                concat(
                c09.clave,
                c10.clave,
                c11.clave,
                c12.clave,
                c13.clave,
                c14.clave,
                c15.clave,
                c16.clave,
                c17.clave,
                c18.clave
                ) area_funcional
            from \",@epp,\" e
            join \",@catalogo,\" c06 on e.upp_id = c06.id 
            join \",@catalogo,\" c07 on e.subsecretaria_id = c07.id  
            join \",@catalogo,\" c08 on e.ur_id = c08.id 
            join \",@catalogo,\" c09 on e.finalidad_id = c09.id 
            join \",@catalogo,\" c10 on e.funcion_id = c10.id 
            join \",@catalogo,\" c11 on e.subfuncion_id = c11.id 
            join \",@catalogo,\" c12 on e.eje_id = c12.id 
            join \",@catalogo,\" c13 on e.linea_accion_id = c13.id 
            join \",@catalogo,\" c14 on e.programa_sectorial_id = c14.id 
            join \",@catalogo,\" c15 on e.tipologia_conac_id = c15.id 
            join \",@catalogo,\" c16 on e.programa_id = c16.id 
            join \",@catalogo,\" c17 on e.subprograma_id = c17.id 
            join \",@catalogo,\" c18 on e.proyecto_id = c18.id
            WHERE e.ejercicio = \",anio,\" AND e.\",@corte,\";
            \");
                
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
           
           set @upp_n := \"\";
           if(upp is not null) then
             set @upp_n := concat(\"
                select 
                    descripcion upp,'' clv_upp,'' clv_pp,'' clv_ur,'' area_funcional,
                    '' nombre_proyecto,'' nivel,'' objetivo,'' indicador
                from \",@catalogo,\" 
                where grupo_id = 6 and deleted_at is null and ejercicio = \",anio,\" and clave = '\",upp,\"'
                union all\");
           end if;
                   
            set @queri := concat(@upp_n,\"
            select
                  '' upp,
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
                        ve.proyecto,
                        mm.nivel,
                        mm.objetivo,
                        mm.indicador
                    from \",@mir,\" mm
                    join epp_t ve on ve.clv_upp = mm.clv_upp AND ve.clv_ur = mm.clv_ur AND 
                    ve.area_funcional = mm.area_funcional
                    where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                    and nivel in (10) \",@upp,\" \",@ur,\" \",@programa,\"
                    union all 
                    select
                        mm.componente_padre id,
                        mm.clv_upp,
                        mm.clv_pp,
                        mm.clv_ur,
                        mm.area_funcional,
                        ve.proyecto,
                        mm.nivel,
                        mm.objetivo,
                        mm.indicador
                    from \",@mir,\" mm
                    join epp_t ve on ve.clv_upp = mm.clv_upp AND ve.clv_ur = mm.clv_ur AND 
                    ve.area_funcional = mm.area_funcional
                    where mm.ejercicio = \",anio,\" and mm.\",@corte,\"
                    and nivel in (11) \",@upp,\" \",@ur,\" \",@programa,\"
                    union all
                    select * FROM (
                         select distinct
                            0 id,clv_upp,clv_pp,clv_ur,
                            '' area_funcional,'' proyecto,9 nivel,'' objetivo,'' indicador
                         from epp_t
                    ) ve \",@upp2,\"\",@programa2,\"\",@ur2,\"
                )t 
                group by clv_upp,clv_pp,clv_ur,id,nivel
                order by clv_upp,clv_pp,clv_ur,id,nivel
            )t2;
            \");
                   
            prepare stmt from @queri;
            execute stmt;
            deallocate prepare stmt;
           
            DROP TEMPORARY TABLE if EXISTS epp_t;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE if exists reporte_seguimiento_2;");
        DB::unprepared("DROP PROCEDURE if exists mml_comprobacion;");
        DB::unprepared("DROP PROCEDURE if exists mml_alineacion;");
        DB::unprepared("DROP PROCEDURE if exists mml_matrices_indicadores;");
    }
};
