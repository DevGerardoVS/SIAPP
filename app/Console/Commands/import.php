<?php

namespace App\Console\Commands;
use App\Imports\ProgramacionPresupuestosImport;

use Illuminate\Console\Command;

class import extends Command
{
    protected $signature = 'import:excel';

    protected $description = 'Laravel Excel importer';

    public function handle()
    {
        $this->output->title('Starting import');
        (new ProgramacionPresupuestosImport)->withOutput($this->output)->import('CALENDARIZACION GENERAL DE PRESUPUESTO 2023 05-01-2023-1.xlsx');
        $this->output->success('Import successful');
    }
}
