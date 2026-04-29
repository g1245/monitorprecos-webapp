<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Nike store products.
 *
 * Nike price mapping:
 * - `price.base_price` → price (base selling price)
 * - `merchant_deep_link` is used as the canonical product URL
 */
class NikeProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `price.base_price` as the selling price.
     */
    public static function fromApiData(int $storeId, array $product): static
    {
        $rawCategory = isset($product['merchant_category']) ? trim((string) $product['merchant_category']) : null;

        if ($rawCategory !== null && $rawCategory !== '') {
            $parts = array_map('trim', explode('>', $rawCategory));
            $merchantCategory1 = $parts[0] ?? null;
            $merchantCategory2 = $parts[1] ?? null;
            $merchantCategory3 = $parts[2] ?? null;
        } else {
            $rawCategory = null;
            $merchantCategory1 = null;
            $merchantCategory2 = null;
            $merchantCategory3 = null;
        }

        return new static(
            storeId: $storeId,
            name: $product['product_name'],
            description: $product['description'] ?? null,
            price: $product['price']['base_price']
                ?? $product['price']['search_price']
                ?? null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $product['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
            merchantCategory: $rawCategory,
            merchantCategory1: $merchantCategory1,
            merchantCategory2: $merchantCategory2,
            merchantCategory3: $merchantCategory3,
        );
    }

    /**
     * {@inheritdoc}
     *
     * Nike requires `price.base_price`.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['base_price']) || !empty($priceData['search_price']);
    }
}
