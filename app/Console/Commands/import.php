<?php

namespace App\Console\Commands;
use App\Imports\PresupuestosImport;

use Illuminate\Console\Command;

class import extends Command
{
    protected $signature = 'import:excel';

    protected $description = 'Laravel Excel importer';

    public function handle()
    {
        $this->output->title('Starting import');
        (new PresupuestosImport)->withOutput($this->output)->import('Usuarios SIAPP.xlsx');
        $this->output->success('Import successful');
    }
}
