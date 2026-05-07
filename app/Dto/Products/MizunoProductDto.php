<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Mizuno BR store products.
 *
 * Mizuno price mapping:
 * - `price.price_min` → price (lowest selling price synced from the feed)
 *
 * Mizuno category mapping:
 * - `product_type` → category path (format: "Feminino - Roupas - Jaquetas e Moletons")
 *   split by " - " into up to three category levels.
 *
 * Mizuno link mapping:
 * - `externalLink` is mapped from the first variant's `merchant_deep_link`
 *   because the Mizuno feed does not expose a top-level `merchant_deep_link`.
 */
class MizunoProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `price.price_min` as the selling price.
     * Category is parsed from the `product_type` field using " - " as delimiter.
     * The canonical product URL is resolved from the first available variant.
     */
    public static function fromApiData(int $storeId, array $product): static
    {
        $priceData = $product['price'] ?? [];
        $firstVariant = $product['variants'][0] ?? [];

        $rawCategory = isset($product['product_type'])
            ? trim((string) $product['product_type'])
            : null;

        if ($rawCategory !== null && $rawCategory !== '') {
            $parts = array_map('trim', explode(' - ', $rawCategory));
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
            price: isset($priceData['price_min']) ? (float) $priceData['price_min'] : null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $firstVariant['merchant_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
            merchantCategory: $rawCategory,
            merchantCategory1: $merchantCategory1,
            merchantCategory2: $merchantCategory2,
            merchantCategory3: $merchantCategory3,
            inStock: (bool) ($product['in_feed'] ?? true),
        );
    }

    /**
     * {@inheritdoc}
     *
     * Mizuno requires `price_min` to be present and non-zero.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['price_min']);
    }
}
