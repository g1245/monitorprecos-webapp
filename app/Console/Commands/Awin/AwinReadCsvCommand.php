<?php

namespace App\Console\Commands\Awin;

use Illuminate\Console\Command;

class AwinReadCsvCommand extends Command
{
    protected $signature = 'app:awin-read-csv';

    protected $description = 'Process pending AWIN CSV imports from the awin-csv queue (run via Horizon workers)';

    public function handle(): int
    {
        $this->info('AWIN CSV processing is handled by Horizon workers listening on the [awin-csv] queue.');
        $this->info('Run: php artisan horizon  — or use the dedicated supervisor for awin-csv.');

        return Command::SUCCESS;
    }
}
