<?php

namespace App\Jobs\Product;

use App\Models\Store;
use App\Services\OutOfStockSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkOutOfStockForStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Store $store
     * @param int $page
     * @param int|null $totalPages
     */
    public function __construct(
        public Store $store,
        public int $page = 1,
        public ?int $totalPages = null,
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
        return ['out-of-stock', "out-of-stock:{$this->store->id}"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        OutOfStockSyncService::markOutOfStockForStore($this->store, $this->page, $this->totalPages);
    }
}
