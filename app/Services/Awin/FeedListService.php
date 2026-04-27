<?php

namespace App\Services\Awin;

use App\Models\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class FeedListService
{
    private const FEED_LIST_URL = 'https://ui.awin.com/productdata-darwin-download/publisher/773041/317c85e8b0d74301ba7d7472617b6c84/1/feedList';

    /**
     * Fetch and return qualifying feeds from AWIN that need to be downloaded.
     *
     * A feed qualifies when:
     *  - Primary Region == 'BR'
     *  - Its advertiser name matches a Store's metadata->SyncStoreName
     *  - Its Last Checked date is newer than $since (unless $ignoreLastChecked is true)
     *
     * @param  Carbon  $since               Timestamp of the last successful sync run.
     * @param  bool    $ignoreLastChecked   When true, skip the Last Checked date filter (used with --force).
     * @return Collection<int, array{feed_id: string, store: Store, last_import: string}>
     */
    public function getQualifyingFeeds(Carbon $since, bool $ignoreLastChecked = false): Collection
    {
        $csv = $this->fetchFeedListCsv();
        $stores = $this->getStoresWithSyncName();

        $syncNameToStore = $stores->keyBy(fn(Store $s) => $s->metadata['SyncStoreName']);

        $results = collect();

        foreach ($csv->getRecords() as $record) {
            if (($record['Primary Region'] ?? '') !== 'BR') {
                continue;
            }

            $storeName = trim($record['Advertiser Name'] ?? '');

            if (!$syncNameToStore->has($storeName)) {
                continue;
            }

            $lastImport = $record['Last Checked'] ?? null;

            if (empty($lastImport)) {
                continue;
            }

            try {
                $lastImportDate = Carbon::parse($lastImport);
            } catch (\Throwable) {
                continue;
            }

            if (!$ignoreLastChecked && $lastImportDate->lte($since)) {
                continue;
            }

            $feedId = $record['Feed ID'];

            if (empty($feedId)) {
                continue;
            }

            $results->push([
                'feed_id' => (string) $feedId,
                'store' => $syncNameToStore->get($storeName),
                'last_import' => $lastImportDate->toIso8601String(),
            ]);
        }

        return $results;
    }

    /**
     * Fetch all stores that have a SyncStoreName defined in metadata.
     *
     * @return Collection<int, Store>
     */
    private function getStoresWithSyncName(): Collection
    {
        return Store::query()
            ->whereRaw("JSON_EXTRACT(metadata, '$.SyncStoreName') IS NOT NULL")
            ->get();
    }

    /**
     * HTTP GET the AWIN feed list and return a parsed league/csv Reader.
     */
    private function fetchFeedListCsv(): Reader
    {
        $response = Http::timeout(60)->get(self::FEED_LIST_URL)->throw();
        $content = mb_convert_encoding($response->body(), 'UTF-8', 'auto');

        $csv = Reader::createFromString($content);
        $csv->setHeaderOffset(0);

        return $csv;
    }
}
