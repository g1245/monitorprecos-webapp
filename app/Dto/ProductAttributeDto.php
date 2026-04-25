<?php

namespace App\Dto;

class ProductAttributeDto
{
    /**
     * Create new ProductAttributeDto instance.
     * 
     * Note: custom3 is intentionally omitted as per the API specification.
     * The API provides custom_1, custom_2, custom_4, custom_5, custom_6, custom_7, custom_8
     * but does not include custom_3.
     */
    public function __construct(
        public int $productId,
        public ?string $inStock = null,
        public ?string $stockQuantity = null,
        public ?string $condition = null,
        public ?string $colour = null,
        public ?string $color = null,
        public ?string $gender = null,
        public ?string $size = null,
        public ?string $sizeType = null,
        public ?string $custom1 = null,
        public ?string $custom2 = null,
        public ?string $custom4 = null, // Note: custom3 not provided by API
        public ?string $custom5 = null,
        public ?string $custom6 = null,
        public ?string $custom7 = null,
        public ?string $custom8 = null,
        public ?string $deliveryWeight = null,
        public ?string $fashionSuitableFor = null,
        public ?string $fashionSize = null,
        public ?string $merchantProductCategoryPath = null,
        public ?string $productGTIN = null,
        public ?string $installment = null,
        public ?string $merchantProductId = null,
        public ?string $merchantCategory = null,
        public ?string $merchantCategory1 = null,
        public ?string $merchantCategory2 = null,
        public ?string $merchantCategory3 = null,
    ) { }

    /**
     * Convert DTO to array of attributes.
     * Returns only non-null values with their key-description pairs.
     */
    public function toAttributesArray(): array
    {
        $attributes = [];
        // Mapping property names to database keys
        // Note: custom3 is intentionally omitted as it's not provided by the API
        $mapping = [
            'inStock' => 'in_stock',
            'stockQuantity' => 'stock_quantity',
            'condition' => 'condition',
            'colour' => 'colour',
            'color' => 'color',
            'gender' => 'gender',
            'size' => 'size',
            'sizeType' => 'size_type',
            'custom1' => 'custom_1',
            'custom2' => 'custom_2',
            'custom4' => 'custom_4', // custom_3 not provided by API
            'custom5' => 'custom_5',
            'custom6' => 'custom_6',
            'custom7' => 'custom_7',
            'custom8' => 'custom_8',
            'deliveryWeight' => 'delivery_weight',
            'fashionSuitableFor' => 'fashion_suitable_for',
            'fashionSize' => 'fashion_size',
            'merchantProductCategoryPath' => 'merchant_product_category_path',
            'productGTIN' => 'product_GTIN',
            'installment' => 'installment',
            'merchantProductId' => 'merchant_product_id',
            'merchantCategory' => 'merchant_category',
            'merchantCategory1' => 'merchant_category_1',
            'merchantCategory2' => 'merchant_category_2',
            'merchantCategory3' => 'merchant_category_3',
        ];

        foreach ($mapping as $property => $key) {
            if ($this->$property !== null) {
                $attributes[] = [
                    'key' => $key,
                    'description' => (string) $this->$property,
                ];
            }
        }

        return $attributes;
    }

    /**
     * Create DTO from API response data.
     * 
     * Note: custom_3 is not included as it's not provided by the API specification.
     */
    public static function fromApiData(int $productId, array $data): self
    {
        $rawCategory = isset($data['merchant_category']) ? trim((string) $data['merchant_category']) : null;

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

        return new self(
            productId: $productId,
            inStock: $data['in_stock'] ?? null,
            stockQuantity: $data['stock_quantity'] ?? null,
            condition: $data['condition'] ?? null,
            colour: $data['colour'] ?? null,
            color: $data['color'] ?? null,
            gender: $data['gender'] ?? null,
            size: $data['size'] ?? null,
            sizeType: $data['size_type'] ?? null,
            custom1: $data['custom_1'] ?? null,
            custom2: $data['custom_2'] ?? null,
            custom4: $data['custom_4'] ?? null, // custom_3 not in API
            custom5: $data['custom_5'] ?? null,
            custom6: $data['custom_6'] ?? null,
            custom7: $data['custom_7'] ?? null,
            custom8: $data['custom_8'] ?? null,
            deliveryWeight: $data['delivery_weight'] ?? null,
            fashionSuitableFor: $data['fashion_suitable_for'] ?? null,
            fashionSize: $data['fashion_size'] ?? null,
            merchantProductCategoryPath: $data['merchant_product_category_path'] ?? null,
            productGTIN: $data['product_GTIN'] ?? null,
            installment: $data['installment'] ?? null,
            merchantProductId: $data['merchant_product_id'] ?? null,
            merchantCategory: $rawCategory,
            merchantCategory1: $merchantCategory1,
            merchantCategory2: $merchantCategory2,
            merchantCategory3: $merchantCategory3,
        );
    }
}
