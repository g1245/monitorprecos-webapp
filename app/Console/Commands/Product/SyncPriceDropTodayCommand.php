<?php

namespace App\Console\Commands\Product;

use App\Services\TopDiscountsSyncService;
use Illuminate\Console\Command;

class SyncPriceDropTodayCommand extends Command
{
    protected $signature = 'app:sync-price-drop-today {--store= : Restrict sync to a specific store ID}';

    protected $description = 'Reconcile "Baixou hoje" department (154): attach products whose price fell today, detach those that no longer qualify';

    public function handle(): int
    {
        $storeId = $this->option('store') ? (int) $this->option('store') : null;

        $success = TopDiscountsSyncService::sync($storeId);

        if (!$success) {
            $this->error('Department ' . TopDiscountsSyncService::DEPARTMENT_ID . ' does not exist.');
            return Command::FAILURE;
        }

        $this->info('Sync completed.');
        return Command::SUCCESS;
    }
}
