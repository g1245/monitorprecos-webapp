<?php

namespace App\Jobs;

use App\Models\UserBrowsingHistory;
use App\Services\BotDetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TrackBrowsingHistoryJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $data
    ) {
        $this->onQueue('browsing-history');
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['browsing-history'];
    }

    /**
     * Execute the job.
     */
    public function handle(BotDetectionService $botDetector): void
    {
        if ($botDetector->isBot($this->data['user_agent'] ?? null)) {
            return;
        }

        UserBrowsingHistory::create($this->data);
    }
}
