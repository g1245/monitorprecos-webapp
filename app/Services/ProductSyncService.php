<?php

namespace App\Services;

use App\Dto\ProductAttributeDto;
use App\Jobs\Product\SyncProductsForStoreJob;
use App\Models\Store;
use App\Services\ProductAttributeService;
use App\Services\ProductDtoResolver;
use App\Services\ProductService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductSyncService
{
    /**
     * Sync products for a given store.
     *
     * @param Store $store
     * @param int $page
     * @param int|null $totalPages
     * @param string|null $updatedAtFrom
     * @return void
     */
    public static function syncForStore(Store $store, int $page = 1, ?int $totalPages = null, ?string $updatedAtFrom = null): void
    {
        $startTime = now();
        $storeName = $store->metadata['SyncStoreName'];

        Log::channel('sync-store')->info("Processing page {$page} for store: {$store->name}", [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'page' => $page,
            'updated_at_from' => $updatedAtFrom,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
        ]);

        $products = self::fetchProducts($storeName, $page, 500, $updatedAtFrom);

        if (empty($products)) {
            Log::error("Failed to fetch products for store: {$store->name} on page: {$page}");

            Log::channel('sync-store')->error("Failed to fetch products", [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'page' => $page,
                'updated_at_from' => $updatedAtFrom,
            ]);
            
            return;
        }

        if ($totalPages === null) {
            $totalPages = $products['totalPages'];
        }

        Log::info("Fetched page {$page} of products for store: {$store->name}");
        Log::info("Total Pages: {$totalPages}");

        $productsProcessed = 0;
        $dtoClass = ProductDtoResolver::resolve($store);

        foreach ($products['data'] as $product) {
            Log::info("Processing product ID", [
                'merchant_product_id' => $product['merchant_product_id'] ?? 'N/A',
                'aw_product_id' => $product['aw_product_id'] ?? 'N/A',
                'store_name' => $store->name,
            ]);

            try {
                $priceData = $product['price'] ?? [];

                if (!$dtoClass::hasValidPrices($priceData)) {
                    Log::error("Missing price fields for product, skipping", [
                        'store_name' => $store->name,
                        'sku' => $product['merchant_product_id'] ?? 'N/A',
                        'price' => $priceData,
                    ]);

                    continue;
                }

                $savedProduct = ProductService::createOrUpdate(
                    $dtoClass::fromApiData($store->id, $product)
                );

                // Sync product attributes after saving the product
                ProductAttributeService::sync(
                    ProductAttributeDto::fromApiData($savedProduct->id, $product)
                );
            } catch (\Throwable $e) {
                Log::error("Failed to create or update product", [
                    'merchant_product_id' => $product['merchant_product_id'] ?? 'N/A',
                    'aw_product_id' => $product['aw_product_id'] ?? 'N/A',
                    'store_name' => $store->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                continue;
            }

            $productsProcessed++;
        }

        $endTime = now();

        Log::channel('sync-store')->info("Completed page {$page} for store: {$store->name}", [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'page' => $page,
            'total_pages' => $totalPages,
            'updated_at_from' => $updatedAtFrom,
            'products_processed' => $productsProcessed,
            'started_at' => $startTime->format('Y-m-d H:i:s'),
            'finished_at' => $endTime->format('Y-m-d H:i:s'),
            'duration_seconds' => $endTime->diffInSeconds($startTime),
        ]);

        // If there are more pages, dispatch the next job
        if ($page < $totalPages) {
            SyncProductsForStoreJob::dispatch($store, $page + 1, $totalPages, $updatedAtFrom);
        } else {
            Log::info("Products synchronized for store: {$store->name}");

            Log::channel('sync-store')->info("All products synchronized for store: {$store->name}", [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'total_pages' => $totalPages,
                'updated_at_from' => $updatedAtFrom,
                'finished_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Fetch products from the API.
     *
     * @param string $storeName
     * @param int $page
     * @param int $limit
     * @param string|null $updatedAtFrom
     * @return array
     */
    private static function fetchProducts(string $storeName, int $page = 1, int $limit = 500, ?string $updatedAtFrom = null): array
    {
        $query = [
            'merchant_name' => $storeName,
            'page' => $page,
            'limit' => $limit,
        ];

        if ($updatedAtFrom !== null) {
            $query['updated_at_from'] = $updatedAtFrom;
        }

        Log::info("Fetching products from API for store: {$storeName}", [
            'query' => $query,
            'endpoint' => config('services.awin.url') . '/products',
        ]);

        $request = Http::withHeaders([
            'x-api-key' => config('services.awin.token')
        ])->get(config('services.awin.url') . '/products', $query);

        if ($request->failed()) {
            return [];
        }

        return $request->json();
    }
}