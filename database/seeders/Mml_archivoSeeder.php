<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Mml_archivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO mml_catalogos (grupo,valor,created_user,created_at,updated_user,updated_at,deleted_user,deleted_at) values
        ('desagregacion_geografica','Nacional','SISTEMA','2024-01-11 11:39:52',NULL,'2024-01-11 11:39:52',NULL,NULL),
        ('desagregacion_geografica','Regional','SISTEMA','2024-01-11 11:39:52',NULL,'2024-01-11 11:39:52',NULL,NULL),
        ('desagregacion_geografica','Estatal','SISTEMA','2024-01-11 11:39:52',NULL,'2024-01-11 11:39:52',NULL,NULL),
        ('desagregacion_geografica','Municipal','SISTEMA','2024-01-11 11:39:52',NULL,'2024-01-11 11:39:52',NULL,NULL),
        ('desagregacion_geografica','Institucional','SISTEMA','2024-01-11 11:39:52',NULL,'2024-01-11 11:39:52',NULL,NULL);");
    }
}