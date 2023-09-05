<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO configuracion (id, descripcion, valor, tipo, usuario_creacion, usuario_modificacion, created_at, updated_at) values
            (1,'enlaces','{\"mml\":\"10.8.7.95\",\"siapp\":\"127.0.0.1\",\"cap\":\"10.8.7.65\"}',null,'SEEDER',null,now(),now())
        ");

    }
}
