<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ramo_33 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO grupos (id, grupo, deleted_at, created_user, updated_user, deleted_user, created_at, updated_at) VALUES
        (21, 'FONDO FEDERAL', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:25:02', '2023-11-24 16:25:02');");

        DB::unprepared("INSERT INTO catalogo (id, grupo_id, ejercicio, clave, descripcion, deleted_at, created_user, updated_user, deleted_user, created_at, updated_at) VALUES
        (4263, 21, 2024, 'FONE', 'Fondo de Aportaciones para la Nómina Educativa y Gasto Operativo (FONE)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4264, 21, 2024, 'FASSA', 'Fondo de Aportaciones para los Servicios de Salud (FASSA)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4265, 21, 2024, 'FAIS', 'Fondo de Aportaciones para la Infraestructura Social \"FAIS\" (FISE-FISMDF)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4266, 21, 2024, 'FAM', 'Fondo de Aportaciones Múltiples \"FAM\" (Asistencia Social-Infraestructura Educativa)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4267, 21, 2024, 'FAETA', 'Fondo de Aportaciones para la Educación Tecnológica y de Adultos (FAETA)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4268, 21, 2024, 'FASP', 'Fondo de Aportaciones para la Seguridad Pública de los Estados y del Distrito Federal (FASP)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12'),
        (4269, 21, 2024, 'FAFEF', 'Fondo de Aportaciones para el Fortalecimiento de las Entidades Federativas (FAFEF)', NULL, 'SEEDER', NULL, NULL, '2023-11-24 16:39:12', '2023-11-24 16:39:12');");

        DB::unprepared("INSERT INTO ramo_33(id,id_ramo_fondo,id_fondo_federal,id_programa,ejercicio) values
        (1,39,4263,3068,2024),
        (2,39,4263,3075,2024),
        (3,39,4263,3077,2024),
        (4,39,4263,3079,2024),
        (5,39,4263,3080,2024),
        (6,39,4263,3081,2024),
        (7,41,4263,3054,2024),
        (8,41,4263,3073,2024),
        (9,41,4263,3075,2024),
        (10,41,4263,3077,2024),
        (11,41,4263,3079,2024),
        (12,41,4263,3080,2024),
        (13,41,4263,3081,2024),
        (14,42,4263,3054,2024),
        (15,42,4263,3055,2024),
        (16,42,4263,3062,2024),
        (17,42,4263,3068,2024),
        (18,42,4263,3073,2024),
        (19,42,4263,3075,2024),
        (20,42,4263,3077,2024),
        (21,42,4263,3078,2024),
        (22,42,4263,3079,2024),
        (23,42,4263,3080,2024),
        (24,42,4263,3081,2024),
        (25,42,4263,3082,2024),
        (26,43,4264,3029,2024),
        (27,44,4265,3030,2024),
        (28,44,4265,3139,2024),
        (29,44,4265,3137,2024),
        (30,45,4265,3050,2024),
        (31,46,4265,3050,2024),
        (32,48,4266,3032,2024),
        (33,48,4266,3111,2024),
        (34,40,4266,3033,2024),
        (35,40,4266,3111,2024),
        (36,49,4266,3033,2024),
        (37,49,4266,3111,2024),
        (38,68,4266,3030,2024),
        (39,68,4266,3032,2024),
        (40,68,4266,3033,2024),
        (41,50,4267,3034,2024),
        (42,51,4268,3036,2024),
        (43,52,4269,3139,2024);");
    }
}
