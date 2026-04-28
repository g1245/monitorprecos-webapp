<?php

namespace App\Console\Commands\Product;

use App\Jobs\Product\SyncProductsForStoreJob;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SyncProductByStoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-product-by-store {name?} {--updated-at-from=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize products by store';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = now();
        $name = $this->argument('name');

        try {
            $updatedAtFrom = $this->resolveUpdatedAtFrom();
        } catch (\Throwable) {
            $this->error('Invalid value for --updated-at-from. Use a valid date/time.');

            Log::channel('sync-store')->error('Invalid updated_at_from value', [
                'updated_at_from' => $this->option('updated-at-from'),
                'started_at' => $startTime->format('Y-m-d H:i:s'),
                'finished_at' => now()->format('Y-m-d H:i:s'),
            ]);

            return Command::FAILURE;
        }

        Log::channel('sync-store')->info('Sync process started', [
            'started_at' => $startTime->format('Y-m-d H:i:s'),
            'store_filter' => $name ?? 'all stores',
            'updated_at_from' => $updatedAtFrom,
        ]);

        if ($name) {
            $store = Store::query()
                ->whereJsonContains('metadata->SyncStoreName', $name)
                ->first();

            if (!$store) {
                $this->error("Store not found: {$name}");

                Log::channel('sync-store')->error('Store not found', [
                    'store_name' => $name,
                    'updated_at_from' => $updatedAtFrom,
                    'started_at' => $startTime->format('Y-m-d H:i:s'),
                    'finished_at' => now()->format('Y-m-d H:i:s'),
                ]);
                
                return Command::FAILURE;
            }

            SyncProductsForStoreJob::dispatch($store, 1, null, $updatedAtFrom);

            $this->info("Job dispatched for store: {$store->name} from updated_at: {$updatedAtFrom}");
        } else {
            $stores = Store::query()
                ->whereRaw("JSON_EXTRACT(metadata, '$.SyncStoreName') IS NOT NULL")
                ->get();

            foreach ($stores as $store) {
                SyncProductsForStoreJob::dispatch($store, 1, null, $updatedAtFrom);

                $this->info("Job dispatched for store: {$store->name}");
            }
        }

        $endTime = now();

        Log::channel('sync-store')->info('Sync jobs dispatched', [
            'started_at' => $startTime->format('Y-m-d H:i:s'),
            'finished_at' => $endTime->format('Y-m-d H:i:s'),
            'duration_seconds' => $endTime->diffInSeconds($startTime),
        ]);

        return Command::SUCCESS;
    }

    /**
     * Resolve the updated_at_from value used in the API request.
     */
    private function resolveUpdatedAtFrom(): string
    {
        $updatedAtFrom = $this->option('updated-at-from');

        if (empty($updatedAtFrom)) {
            return now()->subHours(24)->toIso8601String();
        }

        return Carbon::parse($updatedAtFrom)->toIso8601String();
    }
}
