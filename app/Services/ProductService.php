<?php

namespace App\Services;

use App\Dto\ProductDto;
use App\Models\Product;
use App\Models\Department;

class ProductService
{
    /**
     * Create a new product using the provided DTO.
     */
    public static function create(ProductDto $dto): Product
    {
        return Product::create($dto->toArray());
    }

    /**
     * Create or update a product using the provided DTO.
     * Uses sku as unique identifier.
     */
    public static function createOrUpdate(ProductDto $dto): Product
    {
        $data = $dto->toArray();
        $data['is_parent'] = 0; // Set is_parent to true for all products

        $saved = Product::updateOrCreate(
            ['store_id' => $dto->storeId, 'sku' => $dto->sku],
            $data
        );

        $saved->addPriceHistory($saved->price);

        return $saved;
    }

    /**
     * Sync product departments from category path string.
     * Parses path like "Calçados > Basquete > TENIS > Tênis" and associates all found departments.
     * Uses case-insensitive search and sync() to replace existing associations.
     * 
     * @param int $productId ID of the product to sync departments for
     * @param string|null $categoryPath Category path from API (e.g., "Category1 > Category2")
     * @return void
     */
    public static function syncDepartmentsFromPath(int $productId, ?string $categoryPath): void
    {
        // Handle null or empty path
        if (empty($categoryPath)) {
            return;
        }

        // Parse category path
        $categoryNames = array_map('trim', explode('>', $categoryPath));
        
        // Find matching departments (case-insensitive)
        $departmentIds = [];
        
        foreach ($categoryNames as $categoryName) {
            if (empty($categoryName)) {
                continue;
            }
            
            $department = Department::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                ->first();
            
            if ($department) {
                $departmentIds[] = $department->id;
            }
        }
        
        // Sync departments to product (replaces existing associations)
        $product = Product::find($productId);
        
        if ($product) {
            // Preserve department 154 (price highlight) if already associated
            $existingIds = $product->departments()->pluck('departments.id')->toArray();
            if (in_array(154, $existingIds)) {
                $departmentIds[] = 154;
            }

            $product->departments()->sync(array_unique($departmentIds));
        }
    }
}