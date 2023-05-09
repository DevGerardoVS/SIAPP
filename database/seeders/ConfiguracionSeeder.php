<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) VALUES("tipo_tarifa","{\"tipos\": [\"Fija\", \"M³\"]}" )');
        DB::unprepared('INSERT INTO configuracion (`descripcion`,`valor`) VALUES("años","{\"años\": [\"2022\", \"2021\"]}" )');
    }
}
