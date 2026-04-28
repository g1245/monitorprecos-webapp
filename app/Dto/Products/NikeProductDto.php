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
        return new static(
            storeId: $storeId,
            name: $product['product_name'],
            description: $product['description'] ?? null,
            price: $product['price']['base_price'] ?? null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $product['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
        );
    }

    /**
     * {@inheritdoc}
     *
     * Nike requires `price.base_price`.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['base_price']);
    }
}