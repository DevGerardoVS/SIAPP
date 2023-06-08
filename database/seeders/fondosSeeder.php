<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class fondosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO `grupos` (`id`, `grupo`, `ejercicio`, `deleted_at`, `updated_at`, `created_at`) VALUES
            (1, 'Clasificación Administrativa', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (2, 'Clasificación Geográfica', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (3, 'Entidad Ejecutora', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (4, 'Clasificación Funcional', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (5, 'PLADIEM', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (6, 'Clasificación Programatica del Gasto', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (7, 'Clasificación Economica', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (8, 'Clasificador por Fuente de Financiamiento (CONAC)', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (9, 'SHCP', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (10, 'Tipo de Recurso Financiero', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (11, 'Inversión (Obra Pública)', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25');");

        DB::unprepared("INSERT INTO `subgrupos` (`id`, `grupo_id`, `largo_clave`, `subgrupo`, `ejercicio`, `deleted_at`, `updated_at`, `created_at`) VALUES
            (1, 1, 1, 'SECTOR PÚBLICO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (2, 1, 1, 'SECTOR PÚBLICO FINANCIERO/NO FINANCIERO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (3, 1, 1, 'SECTOR DE ECONOMÍA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (4, 1, 1, 'SUBSECTOR DE ECONOMÍA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (5, 1, 1, 'ENTE PÚBLICO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (6, 2, 2, 'ENTIDAD FEDERATIVA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (7, 2, 2, 'REGIÓN', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (8, 2, 3, 'MUNICIPIO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (9, 2, 3, 'LOCALIDAD', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (10, 3, 3, 'UNIDAD PROGRAMATICA PRESUPUESTAL', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (11, 3, 1, 'SUBSECRETARÍA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (12, 3, 2, 'UNIDAD RESPONSABLE', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (13, 4, 1, 'FINALIDAD', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (14, 4, 1, 'FUNCIÓN', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (15, 4, 1, 'SUBFUNCIÓN', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (16, 5, 1, 'EJE', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (17, 5, 2, 'LINEA DE ACCIÓN (PRIORIDAD TRANSVERSAL)', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (18, 5, 1, 'PROGRAMA SECTORIAL', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (19, 6, 1, 'TIPOLOGIA CONAC', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (20, 6, 2, 'PROGRAMA PRESUPUESTARIO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (21, 6, 3, 'SUBPROGRAMA PRESUPUESTARIO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (22, 6, 3, 'PROYECTO PRESUPUESTARIO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (23, 7, 1, 'CAPÍTULO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (24, 7, 1, 'CONCEPTO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (25, 7, 1, 'PARTIDA GENERICA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (26, 7, 2, 'PARTIDA ESPECIFICA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (27, 7, 1, 'TIPO DE GASTO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (28, 8, 1, 'ETIQUETADO/NO ETIQUETADO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (29, 8, 1, 'FUENTE DE FINANCIAMIENTO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (30, 9, 2, 'RAMO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (31, 9, 2, 'FONDO DEL RAMO', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (32, 10, 1, 'CAPITAL/INTERES', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25'),
            (33, 11, 6, 'PROYECTO DE OBRA', 2023, NULL, '2023-05-16 16:14:25', '2023-05-16 16:14:25');");
    }
}
