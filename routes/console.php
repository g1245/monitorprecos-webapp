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

// Schedule command to sync top discounted products to Department every hour
Schedule::command('app:sync-top-discounted-products-to-department')->hourly();

/** 
 * Schedule commands for backup and cleanup of old backups
 **/
Schedule::command('backup:run')->dailyAt('01:00');
Schedule::command('backup:clean')->dailyAt('02:00');