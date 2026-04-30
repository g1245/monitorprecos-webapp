<?php

namespace App\Console\Commands;

use App\Services\TopDiscountsSyncService;
use Illuminate\Console\Command;

class SyncTopDiscountedProductsToDepartmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-top-discounted-products-to-department {--store= : Restrict sync to a specific store ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products with biggest price reductions to Department (Top Discounts)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting synchronization of top discounted products...');

        $storeId = $this->option('store') ? (int) $this->option('store') : null;

        $success = TopDiscountsSyncService::sync($storeId, fn (string $message) => $this->info($message));

        if (!$success) {
            $this->error('Department with ID ' . TopDiscountsSyncService::DEPARTMENT_ID . ' does not exist. Please create it first.');

            return Command::FAILURE;
        }

        $this->info('Synchronization completed successfully!');

        return Command::SUCCESS;
    }
}
