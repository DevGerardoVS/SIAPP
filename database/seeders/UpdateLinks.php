<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateLinks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::unprepared("UPDATE configuracion SET valor = '{\"cap\": \"https://anteproyecto.sfa.michoacan.gob.mx/login\", \"mml\": \"https://mml.sfa.michoacan.gob.mx/\", \"siapp\": \"#\",\"epp\":\"http://10.8.7.98\"}' WHERE id=1");
    }
}
