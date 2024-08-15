<?php

namespace App\Console\Commands;

use App\Imports\sapp_movimientos;
use Illuminate\Console\Command;

class importSeederSappMov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:seederSappMov';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa a tu base local y genera un seeder para la tabla sapp_mov';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //para funcionamento comodo de la importacion cambiar el nombre del archivo a importar aqui y en el archivo import spp_movimientos.php
        $this->output->title('Starting import');
        (new sapp_movimientos)->withOutput($this->output)->import('Enero-marzo.xlsx');
        $this->output->success('Import successful');
    }
}
