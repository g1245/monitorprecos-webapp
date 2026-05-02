<?php

use Illuminate\Support\Facades\Schedule;

// Schedule command to create daily price history entries for all active products
Schedule::command('app:create-today-price')->dailyAt('00:10');

// Schedule command to sync product data by store every three hours
Schedule::command('app:sync-product-by-store')->hourly();

// Schedule command to mark out-of-stock products by store every hour
Schedule::command('app:mark-out-of-stock-by-store')->hourly();

// Schedule command to sync top discounted products to Department every hour
Schedule::command('app:sync-top-discounted-products-to-department')
    ->everyMinute();

/** 
 * Schedule commands for backup and cleanup of old backups
 **/
Schedule::command('backup:run')->dailyAt('01:00');
Schedule::command('backup:clean')->dailyAt('02:00');