<?php

namespace App\Jobs\Product;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Calculates and persists `price_median` and `discount_percentage_median` for a product.
 *
 * Implements ShouldBeUnique to achieve a debounce effect: if the same product
 * receives many price updates in quick succession, only one recalculation job
 * will be executed at the end of the uniqueFor window.
 *
 * The job is dispatched with a 30-second delay from ProductObserver.
 * uniqueFor(60) ensures the lock outlasts the dispatch delay.
 */
class RecalculateProductMedianJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Product $product,
    ) {
        $this->onQueue('median');
    }

    /**
     * The unique ID of the job — one job per product in the queue.
     */
    public function uniqueId(): string
    {
        return (string) $this->product->id;
    }

    /**
     * The number of seconds the unique lock should be maintained.
     * Must exceed the dispatch delay (30s) to guarantee the debounce effect.
     */
    public function uniqueFor(): int
    {
        return 60;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['median', "product:{$this->product->id}"];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = $this->product->fresh();

        if (! $product) {
            return;
        }

        $row = DB::selectOne(
            'SELECT MEDIAN(price) OVER () AS median FROM products_prices_histories WHERE product_id = ? LIMIT 1',
            [$product->id],
        );

        if (! $row || $row->median === null) {
            return;
        }

        $median = (float) $row->median;

        $discountPercentageMedian = null;

        if ($product->old_price !== null && (float) $product->old_price > 0) {
            $discountPercentageMedian = round(
                ((float) $product->old_price - $median) / (float) $product->old_price * 100,
                2,
            );
        }

        $product->updateQuietly([
            'price_median'                => $median,
            'discount_percentage_median'  => $discountPercentageMedian,
        ]);

        Log::channel('product-audit')->info('Product median recalculated', [
            'product_id'                 => $product->id,
            'price_median'               => $median,
            'discount_percentage_median' => $discountPercentageMedian,
        ]);
    }
}
