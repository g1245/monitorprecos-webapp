<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Dafiti store products.
 *
 * Dafiti price mapping:
 * - `price_min` → price (lowest selling price synced from the feed)
 * - `externalLink` is mapped from the first variant's `merchant_deep_link`
 *   because the Dafiti feed does not expose a top-level `merchant_deep_link`.
 */
class DafitiProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `price_min` as the selling price.
     * The canonical product URL is resolved from the first available variant.
     */
    public static function fromApiData(int $storeId, array $product): static
    {
        $priceData = $product['price'] ?? [];
        $firstVariant = $product['variants'][0] ?? [];

        return new static(
            storeId: $storeId,
            name: $product['product_name'],
            description: $product['description'] ?? null,
            price: isset($priceData['price_min']) ? (float) $priceData['price_min'] : null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $firstVariant['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
        );
    }

    /**
     * {@inheritdoc}
     *
     * Dafiti requires `price_min` to be present and non-zero.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['price_min']);
    }
}
