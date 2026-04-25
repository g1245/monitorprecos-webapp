<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    /** @use HasFactory<\Database\Factories\ProductAttributeFactory> */
    use HasFactory;

    /**
     * Attribute keys that should be hidden from public-facing views.
     * These are either internal metadata fields or generic custom slots
     * whose values are not meaningful to end users.
     *
     * @var array<int, string>
     */
    public const IGNORED_KEYS = [
        'custom_1',
        'custom_2',
        'custom_4',
        'custom_5',
        'custom_6',
        'custom_7',
        'custom_8',
        'in_stock',
        'stock_quantity',
        'installment',
        'merchant_category',
        'merchant_category_1',
        'merchant_category_2',
        'merchant_category_3',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products_attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'key',
        'description',
    ];

    /**
     * Get the product that owns the attribute.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope to exclude attributes that are hidden from public-facing views.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNotIn('key', self::IGNORED_KEYS);
    }
}