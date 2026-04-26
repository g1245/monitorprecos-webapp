<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Centauro store products.
 *
 * Centauro price mapping:
 * - `price_min` → price (lowest selling price across variants)
 * - `externalLink` is mapped from `custom_1` (canonical product URL)
 */
class CentauroProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `price_min` as the selling price.
     * Variant-level fields (imageUrl, deepLink, externalLink) are not mapped.
     */
    public static function fromApiData(int $storeId, array $product): static
    {        
        return new static(
            storeId: $storeId,
            name: $product['product_name'],
            description: $product['description'] ?? null,
            price: $product['price']['price_min'] ?? null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $product['custom_1'] ?? $product['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
        );
    }

    /**
     * {@inheritdoc}
     *
     * Centauro requires `price_min`.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['price_min']);
    }
}
