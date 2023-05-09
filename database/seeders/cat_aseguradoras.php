<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class cat_aseguradoras extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'AXA', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Qualitas', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Zurich', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'ABA', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'MAPFRE', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'GNP', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Quálitas', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Seguros Banorte', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Sura', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Inbursa', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
        DB::unprepared("INSERT INTO `cat_aseguradoras` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (NULL, 'Otro', 'Carga_inicial', 'Admin', current_timestamp(), current_timestamp())");
       
    }
}
