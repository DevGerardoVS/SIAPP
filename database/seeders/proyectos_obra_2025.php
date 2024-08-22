<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class proyectos_obra_2025 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO catalogo(padre_id,ejercicio,grupo_id,clave,descripcion,descripcion_larga,descripcion_corta,created_at,updated_at,deleted_at,created_user,updated_user,deleted_user) VALUES
        (NULL,2025,39,'250001','Rehabilitación de muelles y panteones en la zona lacustre, municipios de Pátzcuaro, Erongaricuaro',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250002','Remodelación oficinas de Secretaría de Contraloría',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250003','Edificios, sitios y monumentos históricos y artísticos',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250004','Espacios deportivos, recreativos y turísticos',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250005','Plazas, parques, jardines y espacios abiertos',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250006','Vialidades urbanas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250007','Otros sitios y edificaciones de infraestructura pública',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250008','Edificios, sitios y monumentos históricos y artísticos',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250009','Infraestructura hospitalaria',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250010','Infraestructura para drenaje y alcantarillado residual',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250011','Vialidades urbanas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250012','Proyectos de otras construcciones de ingeniería civil u obra pesada',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250013','Caminos rurales',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250014','Infraestructura para agua potable',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250015','Vialidades urbanas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250016','Caminos rurales',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250017','Plazas, parques, jardines y espacios abiertos',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250018','Vialidades urbanas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250019','Infraestructura educativa y de investigación',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250020','Infraestructura educativa y de investigación',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250021','Infraestructura educativa y de investigación',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250022','Infraestructura para drenaje y alcantarillado residual',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250023','Espacios deportivos, recreativos, turísticos y culturales',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250024','Carreteras, autopistas y aeropistas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'250025','Carreteras, autopistas y aeropistas',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL),
        (NULL,2025,39,'000000','Sin obra',NULL,NULL,NOW(),NOW(),NULL,'SISTEMA',NULL,NULL);
        ");
    }
}
