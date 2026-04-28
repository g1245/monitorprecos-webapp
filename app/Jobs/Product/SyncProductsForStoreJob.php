<?php

namespace App\Jobs\Product;

use App\Models\Store;
use App\Services\ProductSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductsForStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Store $store
     * @param int $page
     * @param int|null $totalPages
     * @param string|null $updatedAtFrom
     */
    public function __construct(
        public Store $store,
        public int $page = 1,
        public ?int $totalPages = null,
        public ?string $updatedAtFrom = null
    ) {
        $this->onQueue('imports');
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['sync-store', "sync-store:{$this->store->id}"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        ProductSyncService::syncForStore(
            $this->store,
            $this->page,
            $this->totalPages,
            $this->updatedAtFrom
        );
    }
}
