<?php

namespace App\Console\Commands\Awin;

use App\Jobs\Awin\DownloadAwinFeedJob;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FeedImportCommand extends Command
{
    protected $signature = 'app:awin-feed-import {feed_id} {store_internal_name}';

    protected $description = 'Manually trigger download and import of a single AWIN feed by feed ID';

    public function handle(): int
    {
        $feedId = $this->argument('feed_id');
        $storeInternalName = $this->argument('store_internal_name');

        $store = Store::where('internal_name', $storeInternalName)->first();

        if (!$store) {
            $this->error("Store not found: {$storeInternalName}");

            return Command::FAILURE;
        }

        DownloadAwinFeedJob::dispatch($store, $feedId);

        $this->info("Download job dispatched for feed {$feedId} (store: {$store->name})");

        Log::channel('awin')->info('Manual feed import dispatched', [
            'feed_id' => $feedId,
            'store'   => $storeInternalName,
        ]);

        return Command::SUCCESS;
    }
}
