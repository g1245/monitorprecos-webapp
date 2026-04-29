<?php

namespace App\Dto\Products;

use App\Dto\ProductDto;

/**
 * DTO for Olympikus store products.
 *
 * Olympikus price mapping:
 * - `price_min` → price (lowest selling price across variants)
 * - `externalLink` is mapped from the first variant's `merchant_deep_link`
 * - `deepLink` is mapped from the root-level `aw_deep_link`
 */
class OlympikusProductDto extends ProductDto
{
    /**
     * {@inheritdoc}
     *
     * Overrides price mapping to use `price_min` as the selling price.
     * `externalLink` is resolved from the first available variant's `merchant_deep_link`.
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
            name: static::normalizeName($product['product_name'] ?? ''),
            description: $product['description'] ?? null,
            price: $product['price']['price_min'] ?? null,
            sku: $product['merchant_product_id'],
            brand: $product['brand_name'] ?? null,
            imageUrl: $product['merchant_image_url'],
            deepLink: $product['aw_deep_link'] ?? null,
            externalLink: $product['aw_deep_link'] ?? null,
            merchantProductId: $product['merchant_product_id'] ?? null,
            merchantCategory: $rawCategory,
            merchantCategory1: $merchantCategory1,
            merchantCategory2: $merchantCategory2,
            merchantCategory3: $merchantCategory3,
        );
    }

    /**
     * Known color names (Portuguese/English) used as product name suffixes.
     */
    private const COLORS = [
        'preto', 'branco', 'azul', 'vermelho', 'amarelo', 'verde', 'rosa', 'roxo',
        'marrom', 'cinza', 'laranja', 'bege', 'creme', 'dourado', 'prata', 'vinho',
        'coral', 'lilas', 'turquesa', 'navy', 'nude', 'caramelo', 'grafite',
        'off-white', 'offwhite', 'black', 'white', 'red', 'blue', 'grey', 'gray',
        'silver', 'gold', 'pink', 'purple', 'brown', 'orange', 'green', 'yellow',
    ];

    /**
     * Known size values for clothing and footwear.
     */
    private const SIZES = [
        // Brazilian clothing sizes
        'pp', 'p', 'm', 'g', 'gg', 'xg', 'xgg', 'eg', 'egg',
        // International
        'xs', 's', 'l', 'xl', 'xxl', 'xxxl',
        // Numeric shoe sizes (BR 33–48)
        '33', '34', '35', '36', '37', '38', '39', '40',
        '41', '42', '43', '44', '45', '46', '47', '48',
    ];

    /**
     * Normalize the product name by stripping trailing words that are known
     * size or color attributes (e.g. "Tênis Olympikus Soma 34 Preto" → "Tênis Olympikus Soma").
     */
    private static function normalizeName(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $attributeTokens = array_merge(self::COLORS, self::SIZES);

        while (count($words) > 1) {
            $last = mb_strtolower(end($words));

            if (!in_array($last, $attributeTokens, true)) {
                break;
            }

            array_pop($words);
        }

        return implode(' ', $words);
    }

    /**
     * {@inheritdoc}
     *
     * Olympikus requires `price_min`.
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['price_min']);
    }
}
