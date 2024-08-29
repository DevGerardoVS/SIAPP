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
        DB::unprepared("DELETE FROM cierre_ejercicio_claves WHERE ejercicio = 2025;");
        DB::unprepared("DELETE FROM cierre_ejercicio_metas WHERE ejercicio = 2025;");

        DB::unprepared("INSERT INTO cierre_ejercicio_claves(clv_upp,estatus,ejercicio,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user)
        SELECT DISTINCT
            clv_upp,'Abierto' estatus,2025 ejercicio,
            NOW() created_at,NOW() updated_at,NULL deleted_at,
            'SISTEMA' created_user,NULL updated_user,NULL deleted_user
        FROM v_epp
        WHERE ejercicio = 2025 AND deleted_at IS NULL;");

        DB::unprepared("INSERT INTO cierre_ejercicio_metas(clv_upp,estatus,ejercicio,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user)
        SELECT DISTINCT
            clv_upp,'Abierto' estatus,2025 ejercicio,
            NOW() created_at,NOW() updated_at,NULL deleted_at,
            'SISTEMA' created_user,NULL updated_user,NULL deleted_user
        FROM v_epp
        WHERE ejercicio = 2025 AND deleted_at IS NULL;");

        DB::unprepared("UPDATE adm_users SET deleted_at = NOW(), deleted_user = 'SISTEMA'
        WHERE id IN (2,3,5,91,92,95,150,152,157,158,159,160,162,163,164,165,166);");

        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+37@sfa.michoacan.gob.mx' where id = 9;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+38@sfa.michoacan.gob.mx' where id = 10;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+39@sfa.michoacan.gob.mx' where id = 11;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+40@sfa.michoacan.gob.mx' where id = 12;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+41@sfa.michoacan.gob.mx' where id = 13;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+42@sfa.michoacan.gob.mx' where id = 14;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+43@sfa.michoacan.gob.mx' where id = 15;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+44@sfa.michoacan.gob.mx' where id = 16;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+45@sfa.michoacan.gob.mx' where id = 17;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+46@sfa.michoacan.gob.mx' where id = 18;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+47@sfa.michoacan.gob.mx' where id = 19;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+48@sfa.michoacan.gob.mx' where id = 20;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+49@sfa.michoacan.gob.mx' where id = 21;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+50@sfa.michoacan.gob.mx' where id = 22;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+51@sfa.michoacan.gob.mx' where id = 23;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+52@sfa.michoacan.gob.mx' where id = 24;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+53@sfa.michoacan.gob.mx' where id = 25;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+54@sfa.michoacan.gob.mx' where id = 26;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+55@sfa.michoacan.gob.mx' where id = 27;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+56@sfa.michoacan.gob.mx' where id = 28;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+57@sfa.michoacan.gob.mx' where id = 29;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+58@sfa.michoacan.gob.mx' where id = 30;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+59@sfa.michoacan.gob.mx' where id = 31;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+60@sfa.michoacan.gob.mx' where id = 32;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+61@sfa.michoacan.gob.mx' where id = 33;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+62@sfa.michoacan.gob.mx' where id = 34;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+63@sfa.michoacan.gob.mx' where id = 35;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+64@sfa.michoacan.gob.mx' where id = 36;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+65@sfa.michoacan.gob.mx' where id = 37;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+66@sfa.michoacan.gob.mx' where id = 38;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+67@sfa.michoacan.gob.mx' where id = 39;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+68@sfa.michoacan.gob.mx' where id = 40;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+69@sfa.michoacan.gob.mx' where id = 41;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+70@sfa.michoacan.gob.mx' where id = 42;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+71@sfa.michoacan.gob.mx' where id = 43;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+72@sfa.michoacan.gob.mx' where id = 44;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+73@sfa.michoacan.gob.mx' where id = 45;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+74@sfa.michoacan.gob.mx' where id = 46;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+75@sfa.michoacan.gob.mx' where id = 47;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+76@sfa.michoacan.gob.mx' where id = 48;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+77@sfa.michoacan.gob.mx' where id = 49;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+78@sfa.michoacan.gob.mx' where id = 50;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+79@sfa.michoacan.gob.mx' where id = 51;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+80@sfa.michoacan.gob.mx' where id = 52;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+81@sfa.michoacan.gob.mx' where id = 53;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+82@sfa.michoacan.gob.mx' where id = 54;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+83@sfa.michoacan.gob.mx' where id = 55;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+84@sfa.michoacan.gob.mx' where id = 56;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+85@sfa.michoacan.gob.mx' where id = 57;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+86@sfa.michoacan.gob.mx' where id = 58;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+87@sfa.michoacan.gob.mx' where id = 59;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+88@sfa.michoacan.gob.mx' where id = 60;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+89@sfa.michoacan.gob.mx' where id = 61;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+90@sfa.michoacan.gob.mx' where id = 62;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+91@sfa.michoacan.gob.mx' where id = 63;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+92@sfa.michoacan.gob.mx' where id = 64;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+93@sfa.michoacan.gob.mx' where id = 65;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+94@sfa.michoacan.gob.mx' where id = 66;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+95@sfa.michoacan.gob.mx' where id = 67;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+96@sfa.michoacan.gob.mx' where id = 68;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+97@sfa.michoacan.gob.mx' where id = 69;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+98@sfa.michoacan.gob.mx' where id = 70;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+99@sfa.michoacan.gob.mx' where id = 71;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+100@sfa.michoacan.gob.mx' where id = 72;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+101@sfa.michoacan.gob.mx' where id = 73;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+102@sfa.michoacan.gob.mx' where id = 74;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+103@sfa.michoacan.gob.mx' where id = 75;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+104@sfa.michoacan.gob.mx' where id = 76;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+105@sfa.michoacan.gob.mx' where id = 77;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+106@sfa.michoacan.gob.mx' where id = 78;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+107@sfa.michoacan.gob.mx' where id = 79;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+108@sfa.michoacan.gob.mx' where id = 80;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+109@sfa.michoacan.gob.mx' where id = 81;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+110@sfa.michoacan.gob.mx' where id = 82;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+111@sfa.michoacan.gob.mx' where id = 83;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+112@sfa.michoacan.gob.mx' where id = 84;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+113@sfa.michoacan.gob.mx' where id = 85;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+114@sfa.michoacan.gob.mx' where id = 86;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+115@sfa.michoacan.gob.mx' where id = 87;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+116@sfa.michoacan.gob.mx' where id = 88;");
        DB::unprepared("UPDATE adm_users SET email = 'hugo.diaza+117@sfa.michoacan.gob.mx' where id = 89;");

        DB::unprepared("DROP PROCEDURE if exists sp_report_etapa4_mir;");
        DB::unprepared("CREATE PROCEDURE sp_report_etapa4_mir(in upp varchar(3),in pp varchar(2),in ejercicio int(6))
        BEGIN
            SELECT mao.tipo, mao.indice, mao.descripcion, mao.seleccion_mir, ifnull(mao.tipo_indicador, \".\") as tipo_indicador
            FROM mml_arbol_objetivos as mao
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio AND mao.seleccion_mir = 1 AND mao.tipo_indicador IS NULL AND mao.deleted_at IS NULL
            UNION
            SELECT \"Proposito\" as tipo,  \"\" as indice, mdp.objetivo_central as descripcion, \"0\" as seleccion_mir, \".\" as tipo_indicador 
            FROM mml_definicion_problema as mdp
            WHERE mdp.clv_upp = upp AND mdp.clv_pp = pp AND mdp.ejercicio = ejercicio AND mdp.deleted_at IS NULL
            UNION
            SELECT mao.tipo, @c := @c + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @c := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = \"Componente\" AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1
            UNION
            SELECT mao.tipo,  @a := @a + 1 as indice, mao.descripcion, mao.seleccion_mir, mao.tipo_indicador
            FROM mml_arbol_objetivos as mao
            cross join (select @a := 0) r
            WHERE mao.clv_upp = upp AND mao.clv_pp = pp and mao.ejercicio = ejercicio  AND mao.tipo_indicador = \"Actividad\" AND mao.deleted_at IS NULL AND mao.seleccion_mir = 1;
        END;");

        DB::unprepared("UPDATE mml_avance_etapas_pp SET etapa_4 = 1, etapa_5 = 1 
        WHERE clv_upp = '089' AND clv_pp = 'YQ' AND etapa_5 = 0 AND ejercicio = 2025 AND ramo33 = 0;");

        DB::unprepared("UPDATE mml_avance_etapas_pp SET etapa_4 = 1, etapa_5 = 1 
        WHERE clv_upp = '035' AND clv_pp = 'ZK' AND etapa_5 = 0 AND ejercicio = 2025 AND ramo33 = 0;");

        DB::unprepared("UPDATE adm_users SET nombre = 'DIP. LAURA IVONNE', p_apellido = 'PANTOJA', s_apellido = 'ABASCAL' WHERE username = 'UPP001';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. JORGE', p_apellido = 'RESÉNDIZ', s_apellido = 'GARCÍA' WHERE username = 'UPP002';");
        DB::unprepared("UPDATE adm_users SET nombre = 'RAÚL', p_apellido = 'ZEPEDA', s_apellido = 'VILLASEÑOR' WHERE username = 'UPP003';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. ELÍAS', p_apellido = 'IBARRA', s_apellido = 'TORRES' WHERE username = 'UPP006';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. LUIS', p_apellido = 'NAVARRO', s_apellido = 'GARCÍA' WHERE username = 'UPP007';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. ROGELIO', p_apellido = 'ZARAZÚA', s_apellido = 'SÁNCHEZ' WHERE username = 'UPP008';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. CUAUHTÉMOC', p_apellido = 'RAMÍREZ', s_apellido = 'ROMERO' WHERE username = 'UPP009';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. CLAUDIO', p_apellido = 'MÉNDEZ', s_apellido = 'FERNÁNDEZ' WHERE username = 'UPP010';");
        DB::unprepared("UPDATE adm_users SET nombre = 'C. ROBERTO E.', p_apellido = 'MONROY', s_apellido = 'GARCÍA' WHERE username = 'UPP011';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. GABRIELA DESIREÉ', p_apellido = 'MOLINA', s_apellido = 'AGUILAR' WHERE username = 'UPP012';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. ANDREA', p_apellido = 'LÓPEZ', s_apellido = 'CONTRERAS' WHERE username = 'UPP014';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. JUAN CARLOS', p_apellido = 'OSEGUERA', s_apellido = 'CORTÉS' WHERE username = 'UPP016';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. LÁZARO', p_apellido = 'CORTES', s_apellido = 'RANGEL' WHERE username = 'UPP017';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. AZUCENA', p_apellido = 'MARIN', s_apellido = 'CORREA' WHERE username = 'UPP019';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ANDREA JANET', p_apellido = 'SERNA', s_apellido = 'HERNÁNDEZ' WHERE username = 'UPP020';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRA. TAMARA', p_apellido = 'SOSA', s_apellido = 'ALANÍS' WHERE username = 'UPP021';");
        DB::unprepared("UPDATE adm_users SET nombre = 'L.A.E. ALAN MARTIN', p_apellido = 'MARTÍNEZ', s_apellido = 'MARROQUIN' WHERE username = 'UPP022';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. JOSUÉ ADRIÁN', p_apellido = 'ORTIZ', s_apellido = 'CALDERÓN' WHERE username = 'UPP023';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. MARCO ANTONIO', p_apellido = 'FLORES', s_apellido = 'MEJÍA' WHERE username = 'UPP024';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. JOSUÉ ADRIÁN', p_apellido = 'ORTIZ', s_apellido = 'CALDERÓN' WHERE username = 'UPP025';");
        DB::unprepared("UPDATE adm_users SET nombre = 'CASTOR', p_apellido = 'ESTRADA', s_apellido = 'ROBLES' WHERE username = 'UPP031';");
        DB::unprepared("UPDATE adm_users SET nombre = 'CESAR ERWIN', p_apellido = 'SÁNCHEZ', s_apellido = 'CORIA' WHERE username = 'UPP032';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. RAÚL', p_apellido = 'MORÓN', s_apellido = 'VIDAL' WHERE username = 'UPP033';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. CESAR AUGUSTO', p_apellido = 'OCEGUEDA', s_apellido = 'ROBLEDO' WHERE username = 'UPP034';");
        DB::unprepared("UPDATE adm_users SET nombre = 'SERGIO', p_apellido = 'PIMENTEL', s_apellido = 'MENDOZA' WHERE username = 'UPP035';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. GUSTAVO ADOLFO', p_apellido = 'MENDOZA', s_apellido = 'GARCÍA' WHERE username = 'UPP036';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. JULIO CESAR', p_apellido = 'MEDINA', s_apellido = 'ÁVILA' WHERE username = 'UPP037';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. YARABÍ', p_apellido = 'ÁVILA', s_apellido = 'GONZÁLEZ' WHERE username = 'UPP038';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. ÓSCAR', p_apellido = 'CELIS', s_apellido = 'SILVA' WHERE username = 'UPP040';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. IGNACIO', p_apellido = 'HURTADO', s_apellido = 'GÓMEZ' WHERE username = 'UPP041';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. YURISHA', p_apellido = 'ANDRADE', s_apellido = 'MORALES' WHERE username = 'UPP042';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MGDO. LIC. HUGO ALBERTO', p_apellido = 'GAMA', s_apellido = 'CORIA' WHERE username = 'UPP044';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. DAVID', p_apellido = 'MENDOZA', s_apellido = 'ARMAS' WHERE username = 'UPP045';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. ROSENDO ANTONIO', p_apellido = 'CARO', s_apellido = 'GÓMEZ' WHERE username = 'UPP046';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. CRISTINA', p_apellido = 'PORTILLO', s_apellido = 'AYALA' WHERE username = 'UPP047';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. JOSÉ', p_apellido = 'ZAVALA', s_apellido = 'NOLASCO' WHERE username = 'UPP048';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. MARTHA BEATRIZ', p_apellido = 'RENDÓN', s_apellido = 'LÓPEZ' WHERE username = 'UPP049';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. RAMÓN', p_apellido = 'HERNÁNDEZ', s_apellido = 'OROZCO' WHERE username = 'UPP050';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. DAVID', p_apellido = 'ALFARO', s_apellido = 'GARCÉS' WHERE username = 'UPP051';");
        DB::unprepared("UPDATE adm_users SET nombre = 'OSVALDO', p_apellido = 'RUIZ', s_apellido = 'RAMÍREZ' WHERE username = 'UPP052';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. GRACIELA CARMINA ANDRADE', p_apellido = 'GARCÍA', s_apellido = 'PELÁEZ' WHERE username = 'UPP053';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. JUAN CARLOS', p_apellido = 'VELASCO', s_apellido = 'PROCELL' WHERE username = 'UPP054';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. PAULA EDITH', p_apellido = 'ESPINOSA', s_apellido = 'BARRIENTOS' WHERE username = 'UPP055';");
        DB::unprepared("UPDATE adm_users SET nombre = 'SERGIO MIGUEL', p_apellido = 'CEDILLO', s_apellido = 'FERNÁNDEZ' WHERE username = 'UPP060';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LUIS EDGARDO', p_apellido = 'AMEZCUA', s_apellido = 'ALCALÁ' WHERE username = 'UPP063';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. FRANCISCO', p_apellido = 'MÁRQUEZ', s_apellido = 'TINOCO' WHERE username = 'UPP068';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. GRACIELA', p_apellido = 'VILLASEÑOR', s_apellido = 'FERREYRA' WHERE username = 'UPP069';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. CAMERINO', p_apellido = 'MORENO', s_apellido = 'SALINAS' WHERE username = 'UPP070';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. GABRIELA', p_apellido = 'MANZO', s_apellido = 'ORTIZ' WHERE username = 'UPP071';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ING. ALBERTO', p_apellido = 'CORTES', s_apellido = 'ARIAS' WHERE username = 'UPP072';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. LIVIER JULIETA', p_apellido = 'SOTO', s_apellido = 'GONZÁLEZ' WHERE username = 'UPP074';");
        DB::unprepared("UPDATE adm_users SET nombre = 'C. MARCO ANTONIO', p_apellido = 'TINOCO', s_apellido = 'ÁLVAREZ' WHERE username = 'UPP075';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. JORGE', p_apellido = 'GÓMEZ', s_apellido = 'RAMÍREZ' WHERE username = 'UPP078';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. ABRAHAM', p_apellido = 'MONTES', s_apellido = 'MAGAÑA' WHERE username = 'UPP079';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. ALEJANDRO VERGARA', p_apellido = 'ABASCAL', s_apellido = 'SHERWELL' WHERE username = 'UPP080';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. LUIS ROBERTO', p_apellido = 'ARIAS', s_apellido = 'REYES' WHERE username = 'UPP081';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ALEJANDRO', p_apellido = 'ESTRADA', s_apellido = 'SALINAS' WHERE username = 'UPP082';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. BLANCA ISALIA', p_apellido = 'LARA', s_apellido = 'LEYVA' WHERE username = 'UPP083';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRA. TERESA', p_apellido = 'LÓPEZ', s_apellido = 'HERNÁNDEZ' WHERE username = 'UPP084';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. FELIPE', p_apellido = 'MORALES', s_apellido = 'CORREA' WHERE username = 'UPP085';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. MARIBEL JULISA', p_apellido = 'SUÁREZ', s_apellido = 'BUCIO' WHERE username = 'UPP087';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. JOSUÉ ALFONSO', p_apellido = 'MEJÍA', s_apellido = 'PINEDA' WHERE username = 'UPP088';");
        DB::unprepared("UPDATE adm_users SET nombre = 'M.V.Z. EMILIO', p_apellido = 'VIEYRA', s_apellido = 'VARGAS' WHERE username = 'UPP089';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRA. ARELI', p_apellido = 'GALLEGOS', s_apellido = 'IBARRA' WHERE username = 'UPP093';");
        DB::unprepared("UPDATE adm_users SET nombre = 'C. LENIN', p_apellido = 'LÓPEZ', s_apellido = 'GARCÍA' WHERE username = 'UPP094';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. ALEJANDRA', p_apellido = 'ANGUIANO', s_apellido = 'GONZÁLEZ' WHERE username = 'UPP095';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. ALEJANDRA', p_apellido = 'OCHOA', s_apellido = 'ZARZOSA' WHERE username = 'UPP096';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRA. BLANCA GABRIELA', p_apellido = 'PÉREZ', s_apellido = 'SANTAMARÍA' WHERE username = 'UPP098';");
        DB::unprepared("UPDATE adm_users SET nombre = 'C.P. YOLANDA', p_apellido = 'GUERRERO', s_apellido = 'BARRERA' WHERE username = 'UPP099';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. IGNACIO', p_apellido = 'MENDOZA', s_apellido = 'JIMÉNEZ' WHERE username = 'UPP100';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ZENAIDA', p_apellido = 'SALVADOR', s_apellido = 'BRÍGIDO' WHERE username = 'UPP101';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DRA. MIRYAM GEORGINA', p_apellido = 'ALCALÁ', s_apellido = 'CASILLAS' WHERE username = 'UPP102';");
        DB::unprepared("UPDATE adm_users SET nombre = 'L.I. ALEJANDRA MARISSA', p_apellido = 'SUÁREZ', s_apellido = 'GONZÁLEZ' WHERE username = 'UPP103';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. JOSÉ ALFREDO', p_apellido = 'FLORES', s_apellido = 'VARGAS' WHERE username = 'UPP104';");
        DB::unprepared("UPDATE adm_users SET nombre = 'ARQ. GLADYS', p_apellido = 'BUTANDA', s_apellido = 'MACIAS' WHERE username = 'UPP105';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. ALEJANDRO', p_apellido = 'MÉNDEZ', s_apellido = 'LÓPEZ' WHERE username = 'UPP106';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. JOSÉ ANTONIO', p_apellido = 'MEDINA', s_apellido = 'GARCÍA' WHERE username = 'UPP107';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRA. MARIANA', p_apellido = 'SOSA', s_apellido = 'OLMEDA' WHERE username = 'UPP108';");
        DB::unprepared("UPDATE adm_users SET nombre = 'MTRO. ANDRÉS', p_apellido = 'MEDINA', s_apellido = 'GUZMÁN' WHERE username = 'UPP109';");
        DB::unprepared("UPDATE adm_users SET nombre = 'C. EDUARDO', p_apellido = 'ORIHUELA', s_apellido = 'ESTEFÁN' WHERE username = 'UPP110';");
        DB::unprepared("UPDATE adm_users SET nombre = 'DR. Y C.P.C. SALVADOR', p_apellido = 'JUÁREZ', s_apellido = 'ÁLVAREZ' WHERE username = 'UPP111';");
        DB::unprepared("UPDATE adm_users SET nombre = 'LIC. JESÚS ANTONIO', p_apellido = 'MORA', s_apellido = 'GONZÁLEZ' WHERE username = 'UPP112';");
        DB::unprepared("UPDATE adm_users SET nombre = 'M.D. ADRIAN', p_apellido = 'LÓPEZ', s_apellido = 'SOLIS' WHERE username = 'UPPA13';");
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
