<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class mml_seeder extends Seeder
{

    public function run()
    {


        DB::unprepared("INSERT INTO mml_definicion_problema (clv_upp,clv_pp,poblacion_objetivo,descripcion,magnitud,necesidad_atender,delimitacion_geografica,region,municipio,localidad,problema_central,objetivo_central,comentarios_upp,ejercicio,created_at,updated_at,deleted_at) VALUES
             ('080','YJ','La población objetivo para este programa son los ciudadanos','El objetivo del programa 4X es que se generen programas para ....','magnitudes','necesidades',7,'03','001','001','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:18:10','2023-08-01 09:32:58',NULL),
            	 ('012','AA','La población objetivo son los niños','El objetivo del programa AA es que se generen programas para ....','magnitudes','necesidades',6,'03','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:20:07','2023-08-01 09:32:58',NULL),
            	 ('012','DH','La población objetivo son los adultos mayores','El objetivo del programa DH es que se generen programas para ....','magnitudes','necesidades',7,'03','001','003','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','DA','La población objetivo son los jóvenes','El objetivo del programa DA es que se generen programas para ....','magnitudes','necesidades',6,'05','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','DD','La población objetivo son las mujeres ','El objetivo del programa DD es que se generen programas para ....','magnitudes','necesidades',5,'','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','DG','La población objetivo son los índigenas','El objetivo del programa DG es que se generen programas para ....','magnitudes','necesidades',7,'05','002','003','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','DF','La población objetivo son los adultos','El objetivo del programa DF es que se generen programas para ....','magnitudes','necesidades',5,'','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','BM','La población objetivo son los adolescentes','El objetivo del programa BM es que se generen programas para ....','magnitudes','necesidades',6,'03','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','DL','Niños y jóvenes','El objetivo del programa DL es que se generen programas para ....','magnitudes','necesidades',7,'03','001','005','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
            	 ('012','3V','Mujeres y niños','El objetivo del programa 3V es que se generen programas para ....','magnitudes','necesidades',5,'','','','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL),
                 ('012','3W','Ciudadanía','El objetivo del programa 3W es que se generen programas para ....','magnitudes','necesidades',7,'03','001','007','Las Dependencias y Entidades del Estado de Michoacán tienen una ineficaz planeación estratégica
            ','Las Dependencias y Entidades del Estado de Michoacán tienen una eficaz planeación estratégica
            ',NULL,2024,'2023-08-16 14:21:05','2023-08-01 09:32:58',NULL);"
        );

        DB::unprepared("INSERT INTO mml_arbol_problema (problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,ejercicio,created_at,updated_at,deleted_at) VALUES
	 (1,'080','YJ','Efecto',0,NULL,'Superior','Bajo desarrollo económico',2024,'2023-08-16 14:08:15','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',0,'1','Directo','Proyectos y acciones  sin beneficio, socioeconómico',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',2,'1.1','Indirecto','Baja incidencia en el mejoramiento de la calidad de vida',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',3,'1.1.1','Indirecto','Recursos con baja orientación a resultados',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',0,'2','Directo','Ineficiente ejercicio de los recursos',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',0,'3','Directo','Malversación de los recursos públicos',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',6,'3.1','Indirecto','Pérdida de credibilidad en las  instituciones',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',0,'4','Directo','Desarticulación entre sistemas de información y toma de decisiones',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Efecto',8,'4.1','Indirecto','Indicadores inadecuados',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',0,'1','Directo','Desarticulación de los instrumentos de planeación',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',10,'1.1','Indirecto','Desconocimiento de las metodologías de planeación',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',11,'1.1.1','Indirecto','Desconocimiento de los objetivos institucionales',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',10,'1.2','Indirecto','Débil liderazgo',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',13,'1.2.1','Indirecto','Marco normativo obsoleto',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',0,'2','Directo','Deficientes diagnósticos para el desarrollo',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',15,'2.1','Indirecto','Deficiente retroalimentación de la planeación',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',0,'3','Directo','Desvinculación entre planeación e inversión para el desarrollo',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Causa',17,'3.1','Indirecto','Deficiente Sistema Integral de Planeación',2024,'2023-08-16 17:37:36','2023-08-02 10:19:50',NULL);
        ");

        DB::unprepared("INSERT INTO mml_observaciones_pp (clv_upp,clv_pp,problema_id,etapa,comentario,ruta,nombre,ejercicio,created_at,updated_at,created_user,updated_user,deleted_at) VALUES
    ('080','YJ',1,1,'Falta definir bien el árbol del problema',NULL,NULL,2024,'2023-08-21 14:19:11.0','2023-08-21 14:19:11.0','SISTEMA',NULL,NULL),
    ('080','YJ',1,2,'Los efectos se deben redactar de acuerdo a la metodología',NULL,NULL,2024,'2023-08-21 14:21:31.0','2023-08-21 14:21:31.0','SISTEMA',NULL,NULL),
    ('080','YJ',1,3,'Todo bien',NULL,NULL,2024,'2023-08-21 14:23:47.0','2023-08-21 14:23:47.0','SISTEMA',NULL,NULL),
    ('080','YJ',1,5,'En la MIR se debe redactar de forma diferente el FIN',NULL,NULL,2024,'2023-08-21 14:26:14.0','2023-08-21 14:26:14.0','SISTEMA',NULL,NULL);
     ");

        DB::unprepared("INSERT INTO mml_arbol_objetivos (problema_id,clv_upp,clv_pp,tipo,padre_id,indice,tipo_objeto,descripcion,calificacion_id,seleccion_mir,tipo_indicador,ejercicio,created_at,updated_at,deleted_at) VALUES
	 (1,'080','YJ','Fin',0,NULL,'Superior','Incremento en el desarrollo económico',2,1,NULL,2024,'2023-08-16 14:00:02','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',0,'1','Directo','Proyectos y acciones con beneficio socioeconómico',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',2,'1.1','Indirecto','Incidencia en el mejoramiento de la calidad de vida',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',3,'1.1.1','Indirecto','Recursos Administrados orientados a resultados',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',0,'2','Directo','Eficiente ejercicio de los recursos',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',5,'2.1','Indirecto','Incidencia en el mejoramiento de la calidad de vida',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',6,'2.1.1','Indirecto','Recursos Administrados orientados a resultados',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',0,'3','Directo','Correcto uso de los recursos públicos',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Fin',8,'3.1','Indirecto','Recuperación y fortalecimiento de credibilidad en las  instituciones',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',0,'1','Directo','Desarticulación entre sistemas de información y toma de decisiones',4,1,'Componente',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',10,'1.1','Indirecto','Indicadores adecuados',2,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',0,'2','Directo','Articulación de los instrumentos de planeación',4,1,'Componente',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',12,'2.1','Indirecto','Conocimiento de las metodologías de planeación',4,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',13,'2.1.1','Indirecto','Comprensión de los objetivos institucionales',3,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',12,'2.2','Indirecto','Liderazgo fuerte',2,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',15,'2.2.1','Indirecto','Marco normativo actualizado',2,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',0,'3','Directo','Eficientes diagnósticos para el desarrollo',4,1,'Componente',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',17,'3.1','Indirecto','Eficiente retroalimentación de la planeación',4,1,'Actividad',2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',0,'4','Directo','Vinculación entre planeación e inversión para el desarrollo',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL),
	 (1,'080','YJ','Medio',19,'4.1','Indirecto','Eficiente Sistema Integral de Planeación',1,0,NULL,2024,'2023-08-16 17:36:07','2023-08-02 10:19:50',NULL);");

        DB::unprepared("INSERT INTO mml_mir (entidad_ejecutora,area_funcional,clv_upp,clv_ur,clv_pp,nivel,id_epp,componente_padre,objetivo,indicador,definicion_indicador,metodo_calculo,descripcion_metodo,tipo_indicador,unidad_medida,dimension,comportamiento_indicador,frecuencia_medicion,medios_verificacion,lb_valor_absoluto,lb_valor_relativo,lb_anio,lb_periodo_i,lb_periodo_f,mp_valor_absoluto,mp_valor_relativo,mp_anio,mp_anio_meta,mp_periodo_i,mp_periodo_f,supuestos,estrategias,ejercicio,deleted_at,created_at,updated_at) VALUES
             ('','','080','','YJ',8,NULL,NULL,'Contribuir a fortalecer el uso de los recursos orientados a resultados mediante la eficaz planeación estratégica de las Dependencias y Entidades en el Estado de Michoacán','Porcentaje de avance en la implementación del PbR- SED en Michoacán','Permite conocer el avance alcanzado, en la implantación y operación del Presupuesto Basado en Resultados (PbR) y del Sistema de Evaluación del Desempeño (SED)','A=(B+C+D+E+F+G+H+I+J)','Donde: A=Porcentaje Planeación (10)+B= Porcentaje Programación(10)+C= Porcentaje Presupuestación (10)+D= Porcentaje Ejercicio y control(12)+ E=Porcentaje Seguimiento(16)+F= Evaluación(16)+ G= Rendición de Cuentas(10)+ H=Consolidación(16)+ I=Buenas Prácticas (0)',12,14,21,25,35,'Transparencia Presupuestaria, SHCP https://www.transparenciapresupuestaria.gob.mx/es/PTP/EntidadesFederativas#DiagnosticoPbR-SED','No disponible','70.9%',2021,'Enero','Diciembre','No disponible','80%',2022,2022,'Enero','Diciembre','Que las Dependencias, Entidades sigan el modelo de Gestión para Resultados en el uso de su presupuesto','Promoción de la importancia de la Implementación del modelo de Presupuesto basado en Resultados y Sistema de Evaluación del Desempeño en los procesos del ciclo presupuestario',2024,NULL,'2023-08-14 00:27:28','2023-08-16 18:43:46'),
        	 ('','','080','','YJ',9,NULL,NULL,'Las Dependencias y Entidades del Estado de Michoacán cuentan una eficaz planeación estratégica','Porcentaje  de las Dependencias y Entidades que cuentan con instrumentos de planeación publicados','Permite conocer el avance que las Dependencias y Entidades tienen en la construcción de sus instrumentos de planeación','A=(B/C)*100','Donde A: Porcentaje  de las Dependencias y Entidades que cuentan con instrumentos de planeación publicados B: Dependencias y Entidades que cuentan con instrumentos de planeación publicados/Total de Dependencias y Entidades',12,14,21,25,35,'Publicaciones oficiales de los instrumentos','No disponible','No disponible',2021,'Enero','Diciembre','0','100%',2022,2022,'Enero','Diciembre','Que las Dependencias, Entidades tengan interés en formular sus instrumentos de planeación','Promoción, Asesorias, Difusión de Metodologías y Guías Técnicas, Seguimiento',2024,NULL,'2023-08-14 00:33:49','2023-08-16 18:43:46'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',10,85,NULL,'Presupuesto efectivamente ejercido','Porcentaje de presupuesto','El indicador representa el prorcentaje del presupuesto devengado con el ejercido para el cumplimiento de los programas anuales de capacitación, de adquisiciones, de manteminiminto vehicular e inventarios programandos  por la CPLADEM.','A=(B/C)*100','Porcentaje del Presupuesto Efectivamente Ejercido =Prespuesto Devengado/Presupuesto autorizado * 100',12,14,22,25,34,'Documentos de Ejecución tramitados ante la SFA, contra comprobación de gastos.','39518788.34','77.45%',2021,'Enero','Diciembre','51154414','80%',2022,2022,'Enero','Diciembre','La Secretaria de Finanzas y Administración realiza en tiempo y forma las transferencias de recursos  al CPLADEM,   para llevar a cabo  las actividades programadas.','Gestión, Información, capacitación, sistematización, homologación de procesos, supervisión ',2024,NULL,'2023-08-14 00:43:47','2023-08-16 18:43:46'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',11,85,3,'Gestión y Administración de los Recursos Financieros  ','Porcentaje de presentación  de Estados Financieros de acuerdo a la normatividad vigente.','El indicador mide el porcentaje del cumplimiento en la presentación de los Estados Financieros de acuerdo a la normatividad vigente y en los tiempos establecidos.','A=(B/C)*100','A= Porcentaje de Presentación de estados financieros                                                                                                          B=Estados financieros presentados                                                                                                              C=Estados financieros programados',13,14,22,26,30,'Cumplimiento de presentacion de Estados Financieros en los periodos establecidos.','12','100%',2021,'Enero','Diciembre','12','100%',2022,2022,'Enero','Diciembre','Transferencia oportuna de los recursos finacieros por parte de la Secreatria de Finanzas y Administración, para la integración de los estados financieros.','Gestión, análisis',2024,NULL,'2023-08-14 12:25:49','2023-08-16 18:43:46'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',11,85,3,'Profesionalización de los Recursos Humanos','Porcentaje de Elaboración y cumplimiento del programa anual de capacitación del organismo.','El indicador mide el cumplimiento del programa anual de capacitación aplicable al personal del CPLADEM.','A=(B/C)*100','A = Porcentaje del cumplimiento del programa anual de capacitación B=Cursos Impartidos C= Cursos Programados *100 ',13,14,22,25,32,'Expedientes del Programa anual de capacitación','0','0',2022,'Enero','Diciembre','7','100%',2022,2022,'Enero','Diciembre',' Se autoriza presupuesto para capacitaciones','Gestión, vinculación',2024,NULL,'2023-08-14 14:03:22','2023-08-17 12:00:20'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',11,85,3,'Administración de los Recursos Materiales y Servicios Generales para lograr el buen funcionamiento de los bienes y la consolidación del patrimonio de la CPLADEM.','Porcentaje de recursos destinado a gastos de operación.','El indicador mide la eficiencia en la aplicación del recurso a los gastos operativos de la CPLADEM a efectos de generar politicas de austeridad','A= B/C*100','A=  Porcentaje de Rescursos destinados a Gastos de Operación B= Total de gasto ejercido en sumistro de materiales, mantenimientos y gastos de Operación / C= Total de presupuesto autorizado *100',13,14,22,26,32,'Expedientes  con los reportes de los mantenimientos de  vehiculos , actualización de inventarios, y compras de materiales e insumos comprobación de gasto.','1339532.82','3.39%',2022,'Enero','Diciembre','2046176.56','4%',2022,2022,'Enero','Diciembre','Que exista liquidez en las cuentas de la CPLADEM','Gestión, sistematización, seguimiento.',2024,NULL,'2023-08-14 14:06:49','2023-08-17 12:00:22'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',10,85,NULL,'Presupuesto efectivamente ejercido','Porcentaje de presupuesto','El indicador representa el prorcentaje del presupuesto devengado con el ejercido para el cumplimiento de los programas anuales de capacitación, de adquisiciones, de manteminiminto vehicular e inventarios programandos  por la CPLADEM.','A=(B/C)*100','Porcentaje del Presupuesto Efectivamente Ejercido =Prespuesto Devengado/Presupuesto autorizado * 100',12,14,22,25,34,'Documentos de Ejecución tramitados ante la SFA, contra comprobación de gastos.','39518788.34','77.45%',2021,'Enero','Diciembre','51154414','80%',2022,2022,'Enero','Diciembre','La Secretaria de Finanzas y Administración realiza en tiempo y forma las transferencias de recursos  al CPLADEM,   para llevar a cabo  las actividades programadas.','Gestión, Información, capacitación, sistematización, homologación de procesos, supervisión ',2024,NULL,'2023-08-14 14:09:43','2023-08-16 18:43:46'),
        	 ('080005','1393NAMMYJ0ZZ800','080','05','YJ',11,85,3,'Gestión y Administración de los Recursos Financieros  ','Porcentaje de presentación  de Estados Financieros de acuerdo a la normatividad vigente.','El indicador mide el porcentaje del cumplimiento en la presentación de los Estados Financieros de acuerdo a la normatividad vigente y en los tiempos establecidos.','A=(B/C)*100','A= Porcentaje de Presentación de estados financieros                                                                                                          B=Estados financieros presentados                                                                                                              C=Estados financieros programados',13,14,22,26,30,'Cumplimiento de presentacion de Estados Financieros en los periodos establecidos.','12','100%',2021,'Enero','Diciembre','12','100%',2022,2022,'Enero','Diciembre','Transferencia oportuna de los recursos finacieros por parte de la Secreatria de Finanzas y Administración, para la integración de los estados financieros.','Gestión, análisis',2024,NULL,'2023-08-16 18:46:17','2023-08-16 18:46:17');
        ");

    }
}