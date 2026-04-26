<?php

namespace App\Console\Commands\Product;

use App\Jobs\Product\RecalculateProductMedianJob;
use App\Models\Product;
use Illuminate\Console\Command;

class ReindexProductMedianCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reindex-product-median
                            {--store_id= : Reindex only products belonging to this store ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch RecalculateProductMedianJob for all products (or a specific store) without delay';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storeIdOption = $this->option('store_id');
        $storeId       = null;

        if ($storeIdOption !== null) {
            $storeId = (int) $storeIdOption;

            if ($storeId <= 0) {
                $this->error('The --store_id option must be a positive integer.');

                return Command::FAILURE;
            }
        }

        $query = Product::query()
            ->when($storeId !== null, fn ($q) => $q->where('store_id', $storeId));

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->warn('No products found for the given parameters.');

            return Command::SUCCESS;
        }

        $scope = $storeId !== null ? "store ID {$storeId}" : 'all stores';

        $this->info("Dispatching median recalculation for {$total} product(s) [{$scope}]...");

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $query->chunkById(200, function ($products) use ($progressBar): void {
            foreach ($products as $product) {
                RecalculateProductMedianJob::dispatch($product);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();
        $this->info("Done. {$total} job(s) dispatched to the [median] queue.");

        return Command::SUCCESS;
    }
}
