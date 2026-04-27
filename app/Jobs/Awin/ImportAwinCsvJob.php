<?php

namespace App\Jobs\Awin;

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
    public int $timeout = 3600;

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

            $tableName = $csvImportService->import(
                $csvRelativePath,
                $this->store->internal_name,
            );

            $this->feedImport->update([
                'table_name'  => $tableName,
                'status'      => AwinFeedImport::STATUS_DONE,
                'finished_at' => now(),
                'error'       => null,
            ]);

            Log::channel('awin')->info('CSV import finished', [
                'feed_id'    => $this->feedImport->feed_id,
                'store'      => $this->store->internal_name,
                'table_name' => $tableName,
            ]);
        } catch (\Throwable $e) {
            $this->feedImport->update([
                'status'      => AwinFeedImport::STATUS_FAILED,
                'finished_at' => now(),
                'error'       => $e->getMessage(),
            ]);

            Log::channel('awin')->error('CSV import failed', [
                'feed_id'  => $this->feedImport->feed_id,
                'store'    => $this->store->internal_name,
                'error'    => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
