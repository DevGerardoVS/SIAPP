<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


use DB;

class MultiSeeders extends Seeder
{
    public function run()
    {
       
        echo "\nInicializacion de MULTIPLES SEEDERS";
        try {
            $this->call([
                UppExtras::class,
                UppClasifAdmin::class,
                Status4Ej::class,
            ]);
      
            echo "\n    - Se aplico con exito el MULTIPLES SEEDERS:\n";
        } catch (\Exception $e) {
            echo "\n    - Ocurrio un error al ejecutar la operacion:",$e;
        }

    }
}
