<?php

namespace App\Services;

use App\Jobs\Product\AttachToPriceDropDepartmentJob;
use App\Jobs\SendPriceAlertNotificationsJob;
use App\Models\Product;

class ProductLifecycleService
{
    /**
     * Handle side-effects when a product is first created:
     * triggers processing job and seeds the lowest recorded price.
     *
     * @param Product $product The newly created product.
     */
    public function onCreated(Product $product): void
    {
        if ($product->price !== null) {
            $product->updateQuietly([
                'lowest_recorded_price' => $product->price,
            ]);
        }
    }

    /**
     * Handle side-effects when a product is updated:
     * triggers processing job and handles price-change logic when applicable.
     *
     * @param Product $product The updated product.
     */
    public function onUpdated(Product $product): void
    {
        if ($product->wasChanged('price')) {
            $this->handlePriceChange($product);
        }
    }

    /**
     * Handle all side-effects triggered by a price change:
     * snapshots the previous price, updates recorded extremes,
     * dispatches alert notifications, and records price history.
     *
     * @param Product $product The product whose price has changed.
     */
    private function handlePriceChange(Product $product): void
    {
        $previousPrice = $product->getOriginal('price');

        if ($previousPrice != $product->price) {
            $product->updateQuietly([
                'old_price'    => $previousPrice,
                'old_price_at' => now(),
            ]);
        }

        if ($product->price < $previousPrice) {
            AttachToPriceDropDepartmentJob::dispatch($product->id);
        }

        $this->updateRecordedPriceExtremes($product);

        SendPriceAlertNotificationsJob::dispatch($product->id);

        if ($product->shouldRecordPriceHistory()) {
            $product->addPriceHistory($product->price, now());
        }
    }

    /**
     * Update highest and lowest recorded prices if the current price sets a new extreme.
     *
     * @param Product $product The product to evaluate.
     */
    private function updateRecordedPriceExtremes(Product $product): void
    {
        $updates = [];

        if ($product->highest_recorded_price === null || $product->price > $product->highest_recorded_price) {
            $updates['highest_recorded_price'] = $product->price;
        }

        if ($product->lowest_recorded_price === null || $product->price < $product->lowest_recorded_price) {
            $updates['lowest_recorded_price'] = $product->price;
        }

        if (!empty($updates)) {
            $product->updateQuietly($updates);
        }
    }
}
