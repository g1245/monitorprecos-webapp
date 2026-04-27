<?php

namespace App\Jobs\Awin;

use App\Jobs\Awin\ImportAwinCsvChunkJob;
use App\Models\AwinFeedImport;
use App\Models\Store;
use App\Services\Awin\CsvImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportAwinCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 1800;

    /**
     * @param  AwinFeedImport  $feedImport
     * @param  Store           $store
     */
    public function __construct(
        public AwinFeedImport $feedImport,
        public Store $store,
    ) {
        $this->onQueue('awin-csv');
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['awin', 'awin-csv', "store:{$this->store->id}", "feed:{$this->feedImport->feed_id}"];
    }

    public function handle(CsvImportService $csvImportService): void
    {
        $this->feedImport->update([
            'status'     => AwinFeedImport::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        Log::channel('awin')->info('CSV import started', [
            'feed_id'  => $this->feedImport->feed_id,
            'store'    => $this->store->internal_name,
            'filename' => $this->feedImport->filename,
        ]);

        try {
            $csvRelativePath = 'awin/' . $this->feedImport->filename;

            $meta = $csvImportService->prepare(
                $csvRelativePath,
                $this->store->internal_name,
                $this->feedImport->feed_id,
            );

            $this->feedImport->update(['table_name' => $meta['table_name']]);

            ImportAwinCsvChunkJob::dispatch(
                $this->feedImport,
                $this->store,
                $meta['table_name'],
                $meta['safe_csv_path'],
                $meta['headers'],
                $meta['valid_headers'],
                offset: 0,
            );

            Log::channel('awin')->info('CSV first chunk dispatched', [
                'feed_id'    => $this->feedImport->feed_id,
                'store'      => $this->store->internal_name,
                'table_name' => $meta['table_name'],
            ]);
        } catch (\Throwable $e) {
            $this->feedImport->update([
                'status'      => AwinFeedImport::STATUS_FAILED,
                'finished_at' => now(),
                'error'       => $e->getMessage(),
            ]);

            Log::channel('awin')->error('CSV import setup failed', [
                'feed_id'  => $this->feedImport->feed_id,
                'store'    => $this->store->internal_name,
                'error'    => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
