<?php

namespace App\Jobs\Product;

use App\Services\TopDiscountsSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AttachToPriceDropDepartmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $productId) {}

    public function tags(): array
    {
        return ['price-drop-department', "product:{$this->productId}"];
    }

    /**
     * Attach the product to dept 154 without removing other products.
     * The daily cleanup command handles stale detachments.
     */
    public function handle(): void
    {
        $alreadyAttached = DB::table('departments_products')
            ->where('department_id', TopDiscountsSyncService::DEPARTMENT_ID)
            ->where('product_id', $this->productId)
            ->exists();

        if (!$alreadyAttached) {
            DB::table('departments_products')->insert([
                'department_id' => TopDiscountsSyncService::DEPARTMENT_ID,
                'product_id'    => $this->productId,
            ]);
        }
    }
}
