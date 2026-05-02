<?php

namespace App\Services;

use App\Jobs\Product\MarkOutOfStockForStoreJob;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OutOfStockSyncService
{
    private const LIMIT = 500;

    /**
     * Process a single page of out-of-feed products and mark them as out of stock.
     * Dispatches the next job page if there are more pages to process.
     *
     * @param Store $store
     * @param int $page
     * @param int|null $totalPages
     * @return void
     */
    public static function markOutOfStockForStore(Store $store, int $page = 1, ?int $totalPages = null): void
    {
        $startTime = now();
        $storeName = $store->metadata['SyncStoreName'];

        Log::channel('sync-store')->info("Processing out-of-stock page {$page} for store: {$store->name}", [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'page' => $page,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
        ]);

        $result = self::fetchPage($storeName, $page);

        if ($result === null) {
            Log::channel('sync-store')->error('Failed to fetch out-of-feed products', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'page' => $page,
            ]);

            return;
        }

        if ($totalPages === null) {
            $totalPages = $result['totalPages'];
        }

        $merchantProductIds = $result['ids'];

        if (!empty($merchantProductIds)) {
            $updated = Product::query()
                ->where('store_id', $store->id)
                ->whereIn('merchant_product_id', $merchantProductIds)
                ->where('in_stock', true)
                ->update(['in_stock' => false]);
        } else {
            $updated = 0;
        }

        $endTime = now();

        Log::channel('sync-store')->info("Completed out-of-stock page {$page}/{$totalPages} for store: {$store->name}", [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'page' => $page,
            'total_pages' => $totalPages,
            'out_of_feed_count' => count($merchantProductIds),
            'products_marked_out_of_stock' => $updated,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
            'finished_at' => $endTime->format('Y-m-d H:i:s'),
            'duration_seconds' => $endTime->diffInSeconds($startTime),
        ]);

        if ($page < $totalPages) {
            MarkOutOfStockForStoreJob::dispatch($store, $page + 1, $totalPages);
        } else {
            Log::channel('sync-store')->info("All out-of-stock pages processed for store: {$store->name}", [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'total_pages' => $totalPages,
                'finished_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Fetch a single page of out-of-feed merchant_product_ids from the API.
     *
     * @param string $storeName
     * @param int $page
     * @return array{ids: array<int, string>, totalPages: int}|null Returns null on failure.
     */
    private static function fetchPage(string $storeName, int $page): ?array
    {
        Log::info("Fetching out-of-feed page {$page} from API for store: {$storeName}", [
            'endpoint' => config('services.awin.url') . '/merchants/out-of-feed',
            'page' => $page,
            'limit' => self::LIMIT,
        ]);

        $response = Http::withHeaders([
            'x-api-key' => config('services.awin.token'),
        ])->get(config('services.awin.url') . '/merchants/out-of-feed', [
            'merchant_name' => $storeName,
            'page' => $page,
            'limit' => self::LIMIT,
        ]);

        if ($response->failed()) {
            Log::error("API request failed for out-of-feed products", [
                'store_name' => $storeName,
                'page' => $page,
                'status' => $response->status(),
            ]);

            return null;
        }

        $body = $response->json();

        if (!is_array($body)) {
            return null;
        }

        return [
            'ids' => array_column($body['data'] ?? $body, 'merchant_product_id'),
            'totalPages' => $body['totalPages'] ?? 1,
        ];
    }
}
