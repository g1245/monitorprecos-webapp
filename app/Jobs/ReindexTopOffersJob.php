<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ReindexTopOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @throws \RuntimeException
     */
    public function handle(): void
    {
        $exitCode = Artisan::call('app:sync-top-discounted-products-to-department');

        if ($exitCode !== 0) {
            throw new \RuntimeException("Command app:sync-top-discounted-products-to-department failed with exit code {$exitCode}.");
        }
    }
}
