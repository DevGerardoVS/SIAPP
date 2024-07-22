<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProduccionLinks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 
        DB::unprepared("UPDATE configuracion SET valor = '{\"cap\": \"https://anteproyecto.sfa.michoacan.gob.mx/login\", \"mml\": \"https://mml.sfa.michoacan.gob.mx/\", \"siapp\": \"https://seguimientoapp.sfa.michoacan.gob.mx/\",\"epp\":\"https://epp.sfa.michoacan.gob.mx/\"}' WHERE id=1");
    }
}
