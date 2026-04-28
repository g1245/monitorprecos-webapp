<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Composite index to optimize store listing queries:
            // WHERE is_store_visible = 1 AND store_id = ? AND is_parent = 0 AND deleted_at IS NULL
            $table->index(
                ['store_id', 'is_parent', 'is_store_visible', 'deleted_at'],
                'idx_products_store_listing'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_store_listing');
        });
    }
};
