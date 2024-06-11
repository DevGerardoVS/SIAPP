<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class RefreshMaterializedView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materialized-view:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the materialized view for v_epp';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement('CALL refresh_materialized_v_epp()');
        $this->info('Materialized view refreshed successfully.');
        DB::statement('CALL refresh_entidad_ejecutora_table()');
        $this->info('Materialized view refreshed successfully.');
        return Command::SUCCESS;
    }
}
