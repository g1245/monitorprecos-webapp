<?php

namespace App\Console\Commands\Product;

use App\Services\BestPriceWindowSyncService;
use Illuminate\Console\Command;

class SyncBestPrice7DaysCommand extends Command
{
    protected $signature = 'app:sync-best-price-7-days {--store= : Restrict sync to a specific store ID}';

    protected $description = 'Reconcile "Melhor Preço Últimos 7 dias" department (990)';

    public function handle(): int
    {
        $storeId = $this->option('store') ? (int) $this->option('store') : null;

        $success = BestPriceWindowSyncService::sync(990, 7, $storeId);

        if (!$success) {
            $this->error('Department 990 does not exist.');
            return Command::FAILURE;
        }

        $this->info('Sync completed.');
        return Command::SUCCESS;
    }
}
