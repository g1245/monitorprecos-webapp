<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recria a coluna virtual `discount_percentage` usando `old_price` e `price`.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `products` DROP INDEX `products_discount_percentage_index`');
        DB::statement('ALTER TABLE `products` DROP COLUMN `discount_percentage`');
        DB::statement('ALTER TABLE `products` ADD COLUMN `discount_percentage` INT GENERATED ALWAYS AS (ROUND(((old_price - price) / NULLIF(old_price, 0) * 100), 0)) STORED AFTER `external_link`');
        DB::statement('CREATE INDEX `products_discount_percentage_index` ON `products` (`discount_percentage`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `products` DROP INDEX `products_discount_percentage_index`');
        DB::statement('ALTER TABLE `products` DROP COLUMN `discount_percentage`');
        DB::statement('ALTER TABLE `products` ADD COLUMN `discount_percentage` INT GENERATED ALWAYS AS (ROUND(((highest_recorded_price - price) / NULLIF(highest_recorded_price, 0) * 100), 0)) STORED AFTER `external_link`');
        DB::statement('CREATE INDEX `products_discount_percentage_index` ON `products` (`discount_percentage`)');
    }
};
