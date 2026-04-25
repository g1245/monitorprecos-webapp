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
            $table->string('merchant_category')->nullable()->after('merchant_product_id');
            $table->string('merchant_category_1')->nullable()->after('merchant_category')->index();
            $table->string('merchant_category_2')->nullable()->after('merchant_category_1')->index();
            $table->string('merchant_category_3')->nullable()->after('merchant_category_2')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['merchant_category_1']);
            $table->dropIndex(['merchant_category_2']);
            $table->dropIndex(['merchant_category_3']);
            $table->dropColumn(['merchant_category', 'merchant_category_1', 'merchant_category_2', 'merchant_category_3']);
        });
    }
};
