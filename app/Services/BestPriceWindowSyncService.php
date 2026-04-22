<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BestPriceWindowSyncService
{
    /**
     * Reconcile department attachment for products whose current price is at or below
     * the minimum price recorded in the given day window.
     *
     * Products with no history in the window are excluded (they don't qualify).
     *
     * @param int      $departmentId Target department.
     * @param int      $days         Look-back window in days.
     * @param int|null $storeId      Restrict to a specific store, or null for all stores.
     */
    public static function sync(int $departmentId, int $days, ?int $storeId = null): bool
    {
        $department = Department::find($departmentId);

        if (!$department) {
            Log::error("BestPriceWindowSyncService: Department {$departmentId} does not exist.");
            return false;
        }

        $windowStart = now()->subDays($days)->startOfDay();

        // One query: products that qualify (price <= MIN history price within window)
        $qualifyingIds = DB::table('products')
            ->join('products_prices_histories', 'products_prices_histories.product_id', '=', 'products.id')
            ->where('products.is_store_visible', true)
            ->where('products.is_parent', 0)
            ->whereNotNull('products.price')
            ->where('products_prices_histories.created_at', '>=', $windowStart)
            ->when($storeId !== null, fn($q) => $q->where('products.store_id', $storeId))
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.price')
            ->havingRaw('products.price <= MIN(products_prices_histories.price)')
            ->pluck('products.id')
            ->flip()
            ->all(); // keyed by id for fast lookup

        // IDs already attached for this department (scoped to store)
        $attachedIds = DB::table('departments_products')
            ->where('department_id', $departmentId)
            ->when($storeId !== null, fn($q) => $q->whereIn('product_id', function ($sub) use ($storeId) {
                $sub->select('id')->from('products')->where('store_id', $storeId);
            }))
            ->pluck('product_id')
            ->flip()
            ->all(); // keyed by id

        $toDetach = array_keys(array_diff_key($attachedIds, $qualifyingIds));
        $toAttach = array_keys(array_diff_key($qualifyingIds, $attachedIds));

        DB::transaction(function () use ($departmentId, $toDetach, $toAttach) {
            if (!empty($toDetach)) {
                DB::table('departments_products')
                    ->where('department_id', $departmentId)
                    ->whereIn('product_id', $toDetach)
                    ->delete();
            }

            if (!empty($toAttach)) {
                $rows = array_map(fn($id) => ['department_id' => $departmentId, 'product_id' => $id], $toAttach);
                DB::table('departments_products')->insert($rows);
            }
        });

        Log::info("BestPriceWindowSyncService: synced department {$departmentId} ({$days}d window).", [
            'store_id' => $storeId,
            'attached' => count($toAttach),
            'detached' => count($toDetach),
            'total_qualifying' => count($qualifyingIds),
        ]);

        return true;
    }
}
