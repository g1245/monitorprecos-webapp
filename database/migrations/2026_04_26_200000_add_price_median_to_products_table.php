<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona as colunas `price_median` e `discount_percentage_median` à tabela `products`.
     * Ambas são colunas simples (não geradas), pois dependem de cálculo assíncrono
     * via MariaDB MEDIAN() OVER () window function.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `products` ADD COLUMN `price_median` DECIMAL(10,2) NULL DEFAULT NULL AFTER `lowest_recorded_price`');
        DB::statement('ALTER TABLE `products` ADD COLUMN `discount_percentage_median` DECIMAL(5,2) NULL DEFAULT NULL AFTER `discount_percentage`');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `products` DROP COLUMN `discount_percentage_median`');
        DB::statement('ALTER TABLE `products` DROP COLUMN `price_median`');
    }
};
