<?php

namespace App\Jobs\Awin;

use App\Models\AwinFeedImport;
use App\Models\Store;
use App\Services\Awin\FeedDownloadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DownloadAwinFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    /**
     * @param  Store   $store
     * @param  string  $feedId
     */
    public function __construct(
        public Store $store,
        public string $feedId,
    ) {
        $this->onQueue('awin-feeds');
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['awin', 'awin-download', "store:{$this->store->id}", "feed:{$this->feedId}"];
    }

    public function handle(FeedDownloadService $downloadService): void
    {
        $import = AwinFeedImport::create([
            'store_id' => $this->store->id,
            'feed_id'  => $this->feedId,
            'status'   => AwinFeedImport::STATUS_PENDING,
        ]);

        try {
            $csvRelativePath = $downloadService->download($this->feedId);

            $import->update([
                'filename' => basename($csvRelativePath),
                'status'   => AwinFeedImport::STATUS_PENDING,
            ]);

            ImportAwinCsvJob::dispatch($import, $this->store);

            Log::channel('awin')->info('Feed downloaded, import job dispatched', [
                'feed_id'  => $this->feedId,
                'store'    => $this->store->internal_name,
                'filename' => $import->filename,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => AwinFeedImport::STATUS_FAILED,
                'error'  => $e->getMessage(),
            ]);

            Log::channel('awin')->error('Feed download failed', [
                'feed_id' => $this->feedId,
                'store'   => $this->store->internal_name,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
