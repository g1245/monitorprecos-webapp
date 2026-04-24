<?php

namespace App\Jobs\Product;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReindexOldPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $days Time window in days to inspect price history (default: 2)
     */
    public function __construct(
        public int $days = 2
    ) {}

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['reindex-old-price'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activeStoreIds = Store::where('has_public', true)->pluck('id');

        $productsQuery = Product::query()->whereIn('store_id', $activeStoreIds);

        $totalProducts = (clone $productsQuery)->count();

        if ($totalProducts === 0) {
            Log::info('ReindexOldPriceJob: no products found to reindex.');

            return;
        }

        (clone $productsQuery)->toBase()->update(['old_price' => null]);

        $processed = 0;
        $updated = 0;

        $productsQuery
            ->orderBy('id')
            ->chunkById(200, function ($products) use (&$processed, &$updated): void {
                foreach ($products as $product) {
                    $processed++;

                    $historyRecord = $product->priceHistories()
                        ->where('created_at', '>=', now()->subDays($this->days))
                        ->where('price', '>', $product->price)
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->first(['price', 'created_at']);

                    if ($historyRecord !== null) {
                        $formattedOldPrice = number_format((float) $historyRecord->price, 4, '.', '');

                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'old_price'    => $formattedOldPrice,
                                'old_price_at' => $historyRecord->created_at,
                            ]);

                        $updated++;
                    }
                }
            });

        Log::info('ReindexOldPriceJob: reindexing completed.', [
            'total_products' => $totalProducts,
            'processed'      => $processed,
            'updated'        => $updated,
            'skipped'        => $processed - $updated,
            'days'           => $this->days,
        ]);
    }
}
