<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductChangeLog;
use App\Services\ProductLifecycleService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function __construct(
        private readonly ProductLifecycleService $lifecycle,
    ) {}

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->lifecycle->onCreated($product);
        $this->writeAuditLog($product, 'created');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->lifecycle->onUpdated($product);
        $this->writeAuditLog($product, 'updated');
        $this->recordChangeLog($product);
    }

    /**
     * Record a before/after change log entry for the updated product,
     * saving only the fields that were actually modified (excluding updated_at).
     * A backtrace is also stored as JSON so it is possible to identify which
     * process or file triggered the product update.
     */
    private function recordChangeLog(Product $product): void
    {
        $changed = collect($product->getChanges())
            ->except('updated_at')
            ->keys();

        if ($changed->isEmpty()) {
            return;
        }

        $before = collect($product->getOriginal())->only($changed)->all();
        $after  = collect($product->getChanges())->only($changed)->all();

        $basePath  = base_path() . DIRECTORY_SEPARATOR;
        $backtrace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20))
            ->map(fn (array $frame): array => array_filter([
                'file'     => isset($frame['file'])
                    ? str_replace($basePath, '', $frame['file'])
                    : null,
                'line'     => $frame['line'] ?? null,
                'class'    => $frame['class'] ?? null,
                'function' => $frame['function'] ?? null,
            ]))
            ->values()
            ->all();

        ProductChangeLog::create([
            'product_id' => $product->id,
            'before'     => $before,
            'after'      => $after,
            'backtrace'  => $backtrace,
        ]);
    }

    /**
     * Write an audit log entry for the given product event.
     * Logs are written to the 'product-audit' channel (daily rotation, 2-day retention).
     *
     * @param Product $product The product that was created or updated.
     * @param string  $event   The event type ('created' or 'updated').
     */
    /**
     * Handle pivot attachment on the Product "departments" relationship.
     * Clears the department menu cache when a product is attached to a department.
     */
    public function pivotAttached(Product $product, string $relationName, array $pivotIds, array $pivotIdsAttributes): void
    {
        if ($relationName === 'departments') {
            Cache::forget('department_menu');
        }
    }

    /**
     * Handle pivot detachment on the Product "departments" relationship.
     * Clears the department menu cache when a product is detached from a department.
     */
    public function pivotDetached(Product $product, string $relationName, array $pivotIds): void
    {
        if ($relationName === 'departments') {
            Cache::forget('department_menu');
        }
    }

    /**
     * Handle pivot sync on the Product "departments" relationship.
     * Clears the department menu cache when a product's department list is synced.
     */
    public function pivotSynced(Product $product, string $relationName, array $changes): void
    {
        if ($relationName === 'departments') {
            Cache::forget('department_menu');
        }
    }

    private function writeAuditLog(Product $product, string $event): void
    {
        Log::channel('product-audit')->info("Product {$event}", [
            'event'      => $event,
            'store_id'   => $product->store_id,
            'product_id' => $product->id,
            'snapshot'   => $product->getAttributes(),
        ]);
    }
}
