<?php

namespace App\Console\Commands\Awin;

use App\Jobs\Awin\DownloadAwinFeedJob;
use App\Services\Awin\FeedListService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FeedSyncCommand extends Command
{
    private const LAST_RUN_CACHE_KEY = 'awin:feeds_sync:last_run';

    protected $signature = 'app:awin-feeds-sync';

    protected $description = 'Fetch AWIN feed list and dispatch download jobs for updated BR feeds';

    public function handle(FeedListService $feedListService): int
    {
        $startedAt = now();
        $since = Cache::get(self::LAST_RUN_CACHE_KEY, Carbon::today());

        Log::channel('awin')->info('Feed sync started', [
            'since' => $since instanceof Carbon ? $since->toIso8601String() : (string) $since,
            'started_at' => $startedAt->toIso8601String(),
        ]);

        try {
            $feeds = $feedListService->getQualifyingFeeds(
                $since instanceof Carbon ? $since : Carbon::parse($since)
            );
        } catch (\Throwable $e) {
            $this->error('Failed to fetch AWIN feed list: ' . $e->getMessage());

            Log::channel('awin')->error('Feed list fetch failed', ['error' => $e->getMessage()]);

            return Command::FAILURE;
        }

        if ($feeds->isEmpty()) {
            $this->info('No qualifying feeds found.');
        }

        foreach ($feeds as $feed) {
            DownloadAwinFeedJob::dispatch($feed['store'], $feed['feed_id']);

            $this->info("Dispatched download for feed {$feed['feed_id']} ({$feed['store']->name})");

            Log::channel('awin')->info('Download job dispatched', [
                'feed_id'     => $feed['feed_id'],
                'store'       => $feed['store']->internal_name,
                'last_import' => $feed['last_import'],
            ]);
        }

        Cache::forever(self::LAST_RUN_CACHE_KEY, now());

        Log::channel('awin')->info('Feed sync finished', [
            'feeds_dispatched' => $feeds->count(),
            'finished_at'      => now()->toIso8601String(),
        ]);

        return Command::SUCCESS;
    }
}
