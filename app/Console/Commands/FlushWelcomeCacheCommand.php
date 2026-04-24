<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushWelcomeCacheCommand extends Command
{
    protected $signature = 'app:flush-welcome-cache';

    protected $description = 'Flush all per-tab welcome page cache entries';

    public function handle(): int
    {
        Cache::forget('welcome_products:destaques');
        Cache::forget('welcome_products:recentes');
        Cache::forget('welcome_products:mais-acessados');

        $this->info('Welcome page cache flushed successfully.');

        return self::SUCCESS;
    }
}
