<?php

namespace App\Dto;

class ProductDto
{
    public function __construct(
        public int $storeId,
        public string $name,
        public ?string $description = null,
        public ?float $price = null,
        public ?float $priceRegular = null,
        public ?string $sku = null,
        public ?string $brand = null,
        public ?string $imageUrl = null,
        public ?string $deepLink = null,
        public ?string $externalLink = null,
        public ?string $merchantProductId = null,
        public ?string $merchantCategory = null,
        public ?string $merchantCategory1 = null,
        public ?string $merchantCategory2 = null,
        public ?string $merchantCategory3 = null,
    ) { }

    /**
     * Create a DTO instance from raw API product data.
     * Store-specific DTOs should override this method to handle different field structures.
     *
     * @param int   $storeId
     * @param array $product Raw product data from the API
     * @return static
     */
    public static function fromApiData(int $storeId, array $product): static
    {
        $priceData = $product['price'] ?? [];

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
            price: isset($priceData['search_price']) ? (float) $priceData['search_price'] : null,
            priceRegular: null,
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
     * Validate that the price data contains the required fields for this DTO type.
     * Store-specific DTOs should override this to match their own price field requirements.
     *
     * @param array $priceData The price sub-array from the raw API product
     * @return bool
     */
    public static function hasValidPrices(array $priceData): bool
    {
        return !empty($priceData['search_price']) || !empty($priceData['base_price']);
    }

    /**
     * Convert DTO to array for mass assignment.
     */
    public function toArray(): array
    {
        return [
            'store_id' => $this->storeId,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_regular' => null,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'image_url' => $this->imageUrl,
            'deep_link' => $this->deepLink,
            'external_link' => $this->externalLink,
            'merchant_product_id' => $this->merchantProductId,
            'merchant_category' => $this->merchantCategory,
            'merchant_category_1' => $this->merchantCategory1,
            'merchant_category_2' => $this->merchantCategory2,
            'merchant_category_3' => $this->merchantCategory3,
        ];
    }
}