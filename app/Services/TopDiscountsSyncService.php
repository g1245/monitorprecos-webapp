<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopDiscountsSyncService
{
    /**
     * The department ID for top discounts (price highlight).
     */
    public const DEPARTMENT_ID = 154;

    /**
     * Sync products with biggest price reductions to the top discounts department.
     * When $storeId is provided, only removes and re-attaches products from that store,
     * keeping products from other stores intact.
     *
     * @param int|null $storeId Restrict sync to a specific store, or null for all stores.
     * @param callable|null $output Optional callback for stdout (receives string messages).
     * @return bool Returns false if the department does not exist, true otherwise.
     */
    public static function sync(?int $storeId = null, ?callable $output = null): bool
    {
        $department = Department::find(self::DEPARTMENT_ID);

        if (!$department) {
            Log::error('TopDiscountsSyncService: Department with ID ' . self::DEPARTMENT_ID . ' does not exist.');

            return false;
        }

        // Get IDs of products that qualify in this cycle (scoped to store if provided)
        $query = Product::query()
            ->fromPublicStore()
            ->parentProducts()
            ->withRecentPriceChange();

        if ($storeId !== null) {
            $query->where('store_id', $storeId);
        }

        $eligibleIds = $query->pluck('id')->toArray();

        // Attach new eligible products without touching existing ones,
        // keeping the department populated during the entire sync.
        if (!empty($eligibleIds)) {
            $department->products()->syncWithoutDetaching($eligibleIds);
        }

        $output && $output('Products to be attached (' . count($eligibleIds) . '): ' . implode(', ', $eligibleIds));

        // Remove products that no longer qualify in this cycle (scoped to store if provided)
        $deleteQuery = DB::table('departments_products')
            ->where('department_id', self::DEPARTMENT_ID)
            ->whereNotIn('product_id', $eligibleIds);

        if ($storeId !== null) {
            $deleteQuery->whereIn('product_id', function ($query) use ($storeId) {
                $query->select('id')->from('products')->where('store_id', $storeId);
            });
        }

        $removed = $deleteQuery->delete();

        $output && $output('Products removed from department: ' . $removed);

        Log::info('TopDiscountsSyncService: Synced ' . count($eligibleIds) . ' products to department ' . self::DEPARTMENT_ID . '.', [
            'store_id' => $storeId,
        ]);

        return true;
    }
}
