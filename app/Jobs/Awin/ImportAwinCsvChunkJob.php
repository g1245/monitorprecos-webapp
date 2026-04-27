<?php

namespace App\Jobs\Awin;

use App\Models\AwinFeedImport;
use App\Models\Store;
use App\Services\Awin\CsvImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportAwinCsvChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    /**
     * @param  AwinFeedImport    $feedImport
     * @param  Store             $store
     * @param  string            $tableName
     * @param  string            $safeCsvPath    Relative path (already ok_-prefixed).
     * @param  array<int,string> $headers        Renamed headers in original order.
     * @param  array<int,string> $validHeaders   Sanitized column names in original order.
     * @param  int               $offset         Zero-based data-row offset for this chunk.
     */
    public function __construct(
        public AwinFeedImport $feedImport,
        public Store $store,
        public string $tableName,
        public string $safeCsvPath,
        public array $headers,
        public array $validHeaders,
        public int $offset,
    ) {
        $this->onQueue('awin-csv');
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'awin',
            'awin-csv',
            "store:{$this->store->id}",
            "feed:{$this->feedImport->feed_id}",
            "chunk:offset:{$this->offset}",
        ];
    }

    public function handle(CsvImportService $csvImportService): void
    {
        Log::channel('awin')->info('CSV chunk started', [
            'feed_id'    => $this->feedImport->feed_id,
            'store'      => $this->store->internal_name,
            'table_name' => $this->tableName,
            'offset'     => $this->offset,
        ]);

        try {
            $rowsRead = $csvImportService->importChunk(
                $this->safeCsvPath,
                $this->tableName,
                $this->headers,
                $this->validHeaders,
                $this->offset,
            );

            Log::channel('awin')->info('CSV chunk finished', [
                'feed_id'   => $this->feedImport->feed_id,
                'store'     => $this->store->internal_name,
                'offset'    => $this->offset,
                'rows_read' => $rowsRead,
            ]);

            // Increment chunks_done atomically and check if all are complete.
            DB::table('awin_feed_imports')
                ->where('id', $this->feedImport->id)
                ->increment('chunks_done');

            $this->feedImport->refresh();

            if ($this->feedImport->chunks_done >= $this->feedImport->total_chunks) {
                $this->feedImport->update([
                    'status'      => AwinFeedImport::STATUS_DONE,
                    'finished_at' => now(),
                    'error'       => null,
                ]);

                Log::channel('awin')->info('CSV import fully finished', [
                    'feed_id'    => $this->feedImport->feed_id,
                    'store'      => $this->store->internal_name,
                    'table_name' => $this->tableName,
                ]);
            }
        } catch (\Throwable $e) {
            $this->feedImport->update([
                'status'      => AwinFeedImport::STATUS_FAILED,
                'finished_at' => now(),
                'error'       => $e->getMessage(),
            ]);

            Log::channel('awin')->error('CSV chunk failed', [
                'feed_id'  => $this->feedImport->feed_id,
                'store'    => $this->store->internal_name,
                'offset'   => $this->offset,
                'error'    => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
