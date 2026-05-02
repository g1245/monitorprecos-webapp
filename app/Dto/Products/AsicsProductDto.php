<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Asics store products.
 *
 * Asics does not use `base_price`, `product_price_old` or `rrp_price`. Prices are mapped as:
 * - `search_price` → price (actual selling price)
 */
class AsicsProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `search_price` as the selling price.
     */
    public static function fromApiData(int $storeId, array $product): static
    {
        $priceData = $product['price'] ?? [];

        return new static(
            storeId: $storeId,
            name: $product['product_name'],
            description: $product['description'] ?? null,
            price: isset($priceData['search_price']) ? (float) $priceData['search_price'] : (float) $priceData['display_price'],
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $product['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
            inStock: (bool) ($product['in_feed'] ?? true),
        );
    }

    /**
     * {@inheritdoc}
     *
     * Asics requires either `search_price` or `display_price` to be present.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['search_price']) || !empty($priceData['display_price']);
    }
}
