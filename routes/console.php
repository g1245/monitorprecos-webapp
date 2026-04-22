<?php

use App\Jobs\Product\ReindexOldPriceJob;
use Illuminate\Support\Facades\Schedule;

// Schedule command to sync product views_count from browsing history every fifteen minutes
Schedule::command('app:sync-product-views')->everyFifteenMinutes();

// Schedule command to flush welcome page cache every fifteen minutes
Schedule::command('app:flush-welcome-cache')->everyFifteenMinutes();

// Schedule job to reindex old_price for all products with active store catalog every fifteen minutes
Schedule::job(new ReindexOldPriceJob())->everyFifteenMinutes();

// Schedule command to create daily price history entries for all active products
Schedule::command('app:create-today-price')->dailyAt('00:01');

// Schedule command to sync product data by store every three hours
Schedule::command('app:sync-product-by-store')->hourly();

// Price-category sync commands
Schedule::command('app:sync-price-drop-today')->dailyAt('00:30');
Schedule::command('app:sync-best-price-7-days')->dailyAt('00:45');
Schedule::command('app:sync-best-price-15-days')->dailyAt('01:00');

/** 
 * Schedule commands for backup and cleanup of old backups
 **/
Schedule::command('backup:run')->dailyAt('01:00');
Schedule::command('backup:clean')->dailyAt('02:00');