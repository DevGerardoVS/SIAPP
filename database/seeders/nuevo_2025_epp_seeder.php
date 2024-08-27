<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class nuevo_2025_epp_seeder extends Seeder
{
    public function run()
    {
        DB::unprepared("INSERT INTO catalogo(padre_id,ejercicio,grupo_id,clave,descripcion,descripcion_larga,descripcion_corta,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user) VALUES
            (21492,2025,18,'MGP','Atención a la Población sin Seguridad Social Laboral U013',NULL,NULL,now(),now(),null,'ADMIN',null,null),
            (21493,2025,18,'PCP','Impartición de Programas de Calidad de Posgrado, para comunidades originarias',NULL,NULL,now(),now(),null,'ADMIN',null,null),
            (21494,2025,18,'PEM','Proyecto Ejecutivo de Modernización Integral 2024 (PEMI)',NULL,NULL,now(),now(),null,'ADMIN',null,null),
            (21495,2025,18,'PES','Proyecto Estratégico de Salud para el Bienestar',NULL,NULL,now(),now(),null,'ADMIN',null,null),
            (21496,2025,18,'MAI','Modificación y Adecuación de Infraestructura',NULL,NULL,now(),now(),null,'ADMIN',null,null),
            (21497,2025,18,'VYU','Vestuarios y Uniformes',NULL,NULL,now(),now(),null,'ADMIN',null,null);
        ");

        DB::unprepared("INSERT INTO epp(id,ejercicio,mes_i,mes_f,upp_id,clasificacion_administrativa_id,entidad_ejecutora_id,clasificacion_funcional_id,pladiem_id,conac_id,programa_id,subprograma_id,proyecto_id,estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user) VALUES
            (6783,2025,NULL,NULL,18981,11,1388,164,693,26,19986,20088,21493,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (6784,2025,NULL,NULL,19008,12,1299,144,749,24,19982,20337,21494,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (6785,2025,NULL,NULL,18949,11,1255,183,727,25,19980,20139,20542,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (6786,2025,NULL,NULL,18949,11,1418,183,727,25,19980,20137,21495,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (6787,2025,NULL,NULL,18949,11,1121,157,728,25,19980,20281,21496,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (6788,2025,NULL,NULL,18955,12,1305,149,748,32,20065,20182,21497,0,0,0,0,NULL,NOW(),NOW(),NULL,'ADMIN',NULL,NULL);
        ");

        DB::unprepared("INSERT INTO v_epp (id,ejercicio,mes_i,mes_f,clv_sector_publico,sector_publico,clv_sector_publico_f,sector_publico_f,clv_sector_economia,sector_economia,clv_subsector_economia,subsector_economia,clv_ente_publico,ente_publico,clv_upp,upp,clv_subsecretaria,subsecretaria,clv_ur,ur,clv_finalidad,finalidad,clv_funcion,funcion,clv_subfuncion,subfuncion,clv_eje,eje,clv_linea_accion,linea_accion,clv_programa_sectorial,programa_sectorial,clv_tipologia_conac,tipologia_conac,clv_programa,programa,clv_subprograma,subprograma,clv_proyecto,proyecto,estatus,presupuestable,con_mir,confirmado,tipo_presupuesto,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user) VALUES
            (6783,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','2','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','0','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','068','Universidad Intercultural Indígena de Michoacán','0','Sin Subsecretaría','02','Dirección Académica','2','Desarrollo Social','5','Educación','3','Educación Superior','2','Bienestar','HR','2.2.2.1. Garantizar el acceso a la educación a la población indígena, afrodescendientes y personas con alguna discapacidad, así como las que viven en zonas prioritarias de atención con algún tipo de rezago, con enfoque de género.','G','Educación','E','Prestación de Servicios Públicos','YD','Educación Superior, Ciencia, Cultura Física e Investigación Científica','0UT','Impartición de Educación Superior y de Posgrado de Calidad en Materia Indígena','PCP','Impartición de Programas de Calidad de Posgrado, para comunidades originarias',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL),
            (6784,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','1','Gobierno Estatal o del Distrito Federal','1','Poder Ejecutivo','104','Instituto Registral y Catastral del Estado de Michoacán de Ocampo','0','Sin Subsecretaría','01','Dirección General','1','Gobierno','8','Otros Servicios Generales','1','Servicios Registrales, Administrativos y Patrimoniales','3','Prosperidad Económica','NB','3.1.1.2 Generar y fortalecer una plataforma digital de consulta de información y orientación gratuita para obligaciones ﬁscales de contribuyentes, así como para realizar trámites y pagos.','M','Gestión Pública','S','Sujetos a Reglas de Operación','XB','Garantía Jurídica de la Propiedad y el Comercio','0HT','Garantía de la Certeza Jurídica de la Propiedad y el Comercio','PEM','Proyecto Ejecutivo de Modernización Integral 2024 (PEMI)',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL),
            (6785,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','2','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','0','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','017','Servicios de Salud de Michoacán','0','Sin Subsecretaría','07','Dirección de Salud Mental','2','Desarrollo Social','3','Salud','5','Protección Social en Salud','2','Bienestar','KH','2.4.4.2. Garantizar el uso adecuado, racional, pero sin limitación de los recursos para atender las necesidades de salud estableciendo la prevención como eje central del modelo.','F','Salud','U','Otros Subsidios','SS','Atención a la Salud y Medicamentos Gratuitos para la Población sin Seguridad Social Laboral','0XP','Recursos Humanos','MGP','Atención a la Población sin Seguridad Social Laboral U013',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL),
            (6786,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','2','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','0','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','017','Servicios de Salud de Michoacán','0','Sin Subsecretaría','02','Dirección de Salud Pública','2','Desarrollo Social','3','Salud','5','Protección Social en Salud','2','Bienestar','KH','2.4.4.2. Garantizar el uso adecuado, racional, pero sin limitación de los recursos para atender las necesidades de salud estableciendo la prevención como eje central del modelo.','F','Salud','U','Otros Subsidios','SS','Atención a la Salud y Medicamentos Gratuitos para la Población sin Seguridad Social Laboral','0XN','Rectoría del Sistema Estatal de Salud','PES','Proyecto Estratégico de Salud para el Bienestar',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL),
            (6787,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','2','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','0','Entidades Paraestatales y Fideicomisos No Empresariales y No Financieros','017','Servicios de Salud de Michoacán','0','Sin Subsecretaría','04','Dirección Administrativa','2','Desarrollo Social','3','Salud','2','Prestación de Servicios de Salud a la Persona','2','Bienestar','KI','2.4.4.3. Crear o rehabilitar la infraestructura hospitalaria priorizando el primer nivel de atención y fortaleciendo el nivel de especialidades.','F','Salud','U','Otros Subsidios','SS','Atención a la Salud y Medicamentos Gratuitos para la Población sin Seguridad Social Laboral','0EF','Fortalecimiento de la Infraestructura Hospitalaria','MAI','Modificación y Adecuación de Infraestructura',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL),
            (6788,2025,NULL,NULL,'2','Sector Público de las Entidades Federativas','1','Sector Público No Financiero','1','Gobierno General Estatal o del Distrito Federal','1','Gobierno Estatal o del Distrito Federal','1','Poder Ejecutivo','024','Erogaciones Adicionales y Provisiones','0','Sin Subsecretaría','01','Erogaciones Adicionales y Provisiones','1','Gobierno','5','Asuntos Financieros y Hacendarios','2','Asuntos Hacendarios','3','Prosperidad Económica','NA','3.1.1.1 Contribuir a la sostenibilidad de las ﬁnanzas públicas a través del saneamiento derivado de un manejo eﬁcaz de la deuda pública e implementación de medidas de austeridad del gasto público acorde a la austeridad republicana.','M','Gestión Pública','R','Específicos','5H','Provisiones Transitorias','21A','Medidas de Racionalidad y Austeridad del Gasto','VYU','Vestuarios y Uniformes',0,0,0,0,NULL,'2024-07-19 12:38:59.000','2024-07-19 12:38:59.000',NULL,'PRUEBAS',NULL,NULL);
        ");

        DB::unprepared("INSERT INTO upp_extras(id,ejercicio,upp_id,clasificacion_administrativa_id,estatus_epp,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user) VALUES
            (163,2023,11,3,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (164,2023,12,4,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (165,2023,25,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (166,2023,27,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (167,2023,28,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (168,2023,31,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (169,2023,32,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (170,2023,34,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (171,2023,35,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (172,2023,36,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (173,2023,39,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (174,2023,40,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (175,2023,41,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (176,2023,42,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (177,2023,44,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (178,2023,46,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (179,2023,47,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (180,2023,48,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (181,2023,49,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (182,2023,54,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (183,2023,56,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (184,2023,57,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (185,2023,58,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (186,2023,60,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (187,2023,63,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (188,2023,65,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (189,2023,66,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (190,2023,69,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (191,2023,70,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (192,2023,72,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (193,2023,73,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (194,2023,74,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (195,2023,78,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (196,2023,79,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (197,2023,80,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (198,2023,82,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (199,2023,86,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (200,2023,20,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (201,2023,62,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (202,2023,24,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (203,2023,21,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (204,2023,18,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (205,2023,77,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (206,2023,19,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (207,2023,26,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (208,2023,30,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (209,2023,43,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (210,2023,51,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (211,2023,52,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (212,2023,55,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (213,2023,85,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (214,2023,90,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (215,2023,13,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (216,2023,14,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (217,2023,16,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (218,2023,17,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (219,2023,22,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (220,2023,23,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (221,2023,33,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (222,2023,38,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (223,2023,45,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (224,2023,50,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (225,2023,59,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (226,2023,64,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (227,2023,67,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (228,2023,68,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (229,2023,71,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (230,2023,75,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (231,2023,81,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (232,2023,87,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (233,2023,88,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (234,2023,15,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (235,2023,76,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (236,2023,83,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (237,2023,84,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (238,2023,53,1,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (239,2023,37,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (240,2023,61,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (241,2023,29,2,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL),
            (242,2023,89,5,4,NOW(),NOW(),NULL,'ADMIN',NULL,NULL);
        ");
    }
}
