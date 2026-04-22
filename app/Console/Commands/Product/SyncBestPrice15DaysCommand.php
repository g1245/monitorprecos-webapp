<?php

namespace App\Console\Commands\Product;

use App\Services\BestPriceWindowSyncService;
use Illuminate\Console\Command;

class SyncBestPrice15DaysCommand extends Command
{
    protected $signature = 'app:sync-best-price-15-days {--store= : Restrict sync to a specific store ID}';

    protected $description = 'Reconcile "Melhor Preço Últimos 15 dias" department (991)';

    public function handle(): int
    {
        $storeId = $this->option('store') ? (int) $this->option('store') : null;

        $success = BestPriceWindowSyncService::sync(991, 15, $storeId);

        if (!$success) {
            $this->error('Department 991 does not exist.');
            return Command::FAILURE;
        }

        $this->info('Sync completed.');
        return Command::SUCCESS;
    }
}
