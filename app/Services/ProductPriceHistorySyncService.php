<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductPriceHistorySyncService
{
    /**
     * Sync price history for a product from external API.
     *
     * @param Product $product
     * @return array ['success' => bool, 'synced_count' => int, 'message' => string]
     */
    public static function syncPriceHistoryForProduct(Product $product): array
    {
        $startTime = now();

        Log::channel('sync-price-history')->info("Starting price history sync for product", [
            'product_id' => $product->id,
            'product_sku' => $product->sku,
            'product_name' => $product->name,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
        ]);

        // First, try to fetch by merchant_product_id
        $priceHistory = self::fetchPriceHistory($product->sku, 'merchant_product_id');

        // If empty, try with aw_product_id
        if (empty($priceHistory)) {
            Log::channel('sync-price-history')->info("No price history found with merchant_product_id, trying aw_product_id", [
                'product_id' => $product->id,
                'sku' => $product->sku,
            ]);

            $priceHistory = self::fetchPriceHistory($product->sku, 'aw_product_id');
        }

        if (empty($priceHistory)) {
            Log::channel('sync-price-history')->warning("No price history found for product", [
                'product_id' => $product->id,
                'sku' => $product->sku,
            ]);

            return [
                'success' => false,
                'synced_count' => 0,
                'message' => 'No price history found in API',
            ];
        }

        $syncedCount = 0;

        // The first item in the array is the most recent (latest date)
        // Use it to update the product's current price fields
        $mostRecentEntry = $priceHistory[0];
        $currentPrices = self::calculatePrices($mostRecentEntry);

        foreach ($priceHistory as $priceEntry) {
            try {
                $prices = self::calculatePrices($priceEntry);

                if ($prices['price'] === null) {
                    Log::channel('sync-price-history')->warning("No valid price found in entry", [
                        'product_id' => $product->id,
                        'entry' => $priceEntry,
                    ]);
                    continue;
                }

                // Extract date from timestamp
                $timestamp = $priceEntry['timestamp'] ?? null;
                if (!$timestamp) {
                    Log::channel('sync-price-history')->warning("No timestamp in price entry", [
                        'product_id' => $product->id,
                        'entry' => $priceEntry,
                    ]);
                    continue;
                }

                $date = date('Y-m-d', strtotime($timestamp));

                // Add price to history (updates if already exists for this date)
                $product->addPriceHistory($prices['price'], $date);

                $syncedCount++;

                Log::channel('sync-price-history')->debug("Synced price history entry", [
                    'product_id' => $product->id,
                    'date' => $date,
                    'price' => $prices['price'],
                ]);

            } catch (\Exception $e) {
                Log::channel('sync-price-history')->error("Error syncing price entry", [
                    'product_id' => $product->id,
                    'entry' => $priceEntry,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Update product's current price with the most recent data (first item)
        if ($currentPrices['price'] !== null) {
            $product->update([
                'price' => $currentPrices['price'],
            ]);

            Log::channel('sync-price-history')->info("Updated product current prices", [
                'product_id' => $product->id,
                'price' => $currentPrices['price'],
            ]);
        }

        $endTime = now();

        Log::channel('sync-price-history')->info("Completed price history sync for product", [
            'product_id' => $product->id,
            'synced_count' => $syncedCount,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
            'finished_at' => $endTime->format('Y-m-d H:i:s'),
            'duration_seconds' => $endTime->diffInSeconds($startTime),
        ]);

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => "Synced {$syncedCount} price history entries",
        ];
    }

    /**
     * Fetch price history from the external API.
     *
     * @param string $identifier
     * @param string $parameterName (merchant_product_id or aw_product_id)
     * @return array
     */
    private static function fetchPriceHistory(string $identifier, string $parameterName): array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'x-api-key' => config('services.awin.token')
            ])->get(config('services.awin.url') . '/products/price-history', [
                $parameterName => $identifier,
            ]);

            if (!$response->successful()) {
                Log::channel('sync-price-history')->error("API request failed", [
                    'parameter' => $parameterName,
                    'identifier' => $identifier,
                    'status' => $response->status(),
                ]);
                return [];
            }

            $data = $response->json();

            return $data['prices'] ?? [];

        } catch (\Exception $e) {
            Log::channel('sync-price-history')->error("Exception fetching price history", [
                'parameter' => $parameterName,
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Calculate price from API price data.
     *
     * Price = minimum value among all *_price fields
     *
     * @param array $priceData
     * @return array ['price' => float|null]
     */
    private static function calculatePrices(array $priceData): array
    {
        $priceFields = ['search_price', 'base_price', 'display_price', 'product_price_old'];
        $prices = [];

        foreach ($priceFields as $field) {
            if (isset($priceData[$field]) && is_numeric($priceData[$field])) {
                $prices[] = (float) $priceData[$field];
            }
        }

        if (empty($prices)) {
            return ['price' => null];
        }

        // Price is the minimum value
        $price = min($prices);

        return [
            'price' => $price,
        ];
    }
}
