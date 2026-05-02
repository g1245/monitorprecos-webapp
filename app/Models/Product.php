<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'description',
        'price',
        'old_price',
        'old_price_at',
        'highest_recorded_price',
        'lowest_recorded_price',
        'sku',
        'merchant_product_id',
        'brand',
        'image_url',
        'is_store_visible',
        'is_parent',
        'views_count',
        'deep_link',
        'external_link',
        'merchant_category',
        'merchant_category_1',
        'merchant_category_2',
        'merchant_category_3',
        'price_median',
        'discount_percentage_median',
        'in_stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'store_id' => 'integer',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'old_price_at' => 'datetime',
        'highest_recorded_price' => 'decimal:2',
        'lowest_recorded_price' => 'decimal:2',
        'is_store_visible' => 'boolean',
        'is_parent' => 'integer',
        'views_count' => 'integer',
        'deep_link' => 'string',
        'external_link' => 'string',
        'discount_percentage' => 'integer',
        'price_median' => 'decimal:2',
        'discount_percentage_median' => 'decimal:2',
        'in_stock' => 'boolean',
    ];

    /**
     * Get the public URL for the store page.
     *
     * @return string
     */
    public function getPermalinkAttribute(): string
    {
        return Str::slug($this->name);
    }

    /**
     * Get all departments that this product belongs to.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'departments_products');
    }

    /**
     * Get the primary store for this product.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get all attributes for this product.
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    /**
     * Get only the public-facing attributes for this product,
     * excluding internal metadata keys defined in ProductAttribute::IGNORED_KEYS.
     */
    public function visibleAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->visible();
    }

    /**
     * Get price history for this product.
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    /**
     * Get all stores that sell this product.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->withPivot('price', 'product_url')
            ->withTimestamps();
    }

    /**
     * Scope to get only products that are currently in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Scope to get only products from stores with public visibility.
     *
     * Uses LEFT JOIN with the has_public condition in the ON clause so the
     * query planner can prune non-public stores early, avoiding a correlated
     * subquery (EXISTS) that degrades performance at scale.
     * The alias `public_stores` prevents conflicts with any eager-loaded
     * `store` relationship or other joins on the same query.
     */
    public function scopeFromPublicStore($query)
    {
        return $query->where('products.is_store_visible', true);
    }

    /**
     * Scope to get only parent (listable) products, excluding child variants.
     */
    public function scopeParentProducts($query)
    {
        return $query->where('is_parent', 0);
    }

    /**
     * Scope to search products by name, description, brand or exact SKU match.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('products.sku', '=', $search)
                ->orWhere('products.name', 'LIKE', "%{$search}%")
                ->orWhere('products.description', 'LIKE', "%{$search}%")
                ->orWhere('products.brand', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope to filter products by price range.
     */
    public function scopePriceBetween($query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope to get products with a real discount (old_price > price).
     */
    public function scopeWithDiscount($query)
    {
        return $query->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price');
    }

    /**
     * Scope to get products with price changes in the given day window.
     * Uses old_price_at for precision — tracks exactly when old_price was last set.
     */
    public function scopeWithRecentPriceChange($query, int $days = 1)
    {
        return $query->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price')
            ->where('old_price_at', '>=', now()->subDays($days)->startOfDay());
    }

    /**
     * Add price to history.
     * Ensures only one price per specified date by updating if already exists.
     * If $date is null, uses today's date.
     */
    public function addPriceHistory(float $price, ?string $date = null): ProductPriceHistory
    {
        $targetDate = $date ? \Carbon\Carbon::parse($date)->toDateString() : now()->toDateString();

        $record = ProductPriceHistory::firstOrNew([
            'product_id' => $this->id,
            'created_at' => $targetDate . ' 12:00:00',
        ]);

        $record->price = $price;
        $record->save();

        return $record;
    }

    /**
     * Get latest price from history.
     */
    public function getLatestHistoricalPrice(): ?float
    {
        $latestHistory = $this->priceHistories()->latest('created_at')->first();

        return $latestHistory?->price;
    }

    /**
     * Check if current price should be recorded in history.
     * Only record if price has changed from last recorded price.
     */
    public function shouldRecordPriceHistory(): bool
    {
        $latestPrice = $this->getLatestHistoricalPrice();

        return $latestPrice === null || $latestPrice !== $this->price;
    }

    /**
     * Get users who wished for this product.
     */
    public function wishedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_wish_products')
            ->withTimestamps();
    }

    /**
     * Get user wish products for this product.
     */
    public function userWishProducts(): HasMany
    {
        return $this->hasMany(UserWishProduct::class);
    }

    /**
     * Get active price alerts for this product (wishes with target price).
     */
    public function activePriceAlerts(): HasMany
    {
        return $this->hasMany(UserWishProduct::class)
            ->whereNotNull('target_price')
            ->where('is_active', true);
    }

    /**
     * Check if this product is a parent product.
     */
    public function isParentProduct(): bool
    {
        return $this->is_parent === 0;
    }

    /**
     * Check if this product is a child product.
     */
    public function isChildProduct(): bool
    {
        return $this->is_parent !== null && $this->is_parent > 0;
    }

    /**
     * Get the parent product if this is a child product.
     */
    public function parentProduct(): ?Product
    {
        if (!$this->isChildProduct()) {
            return null;
        }

        return Product::fromPublicStore()->find($this->is_parent);
    }

    /**
     * Get child products if this is a parent product.
     */
    public function childProducts()
    {
        if (!$this->isParentProduct()) {
            return collect([]);
        }

        return Product::where('is_parent', $this->id)->fromPublicStore()->get();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'discount_percentage' => (int) $this->discount_percentage,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'store_id' => (int) $this->store_id,
            'is_parent' => (int) $this->is_parent,
            'is_store_visible' => (bool) $this->is_store_visible,
            'merchant_category' => $this->merchant_category,
            'merchant_category_1' => $this->merchant_category_1,
            'merchant_category_2' => $this->merchant_category_2,
            'merchant_category_3' => $this->merchant_category_3,
            'in_stock' => (bool) $this->in_stock,
        ];
    }

    /**
     * Determine if the model should be re-indexed based on changed attributes.
     */
    public function searchIndexShouldBeUpdated(): bool
    {
        $watchedAttributes = [
            'name',
            'discount_percentage',
            'merchant_category',
            'merchant_category_1',
            'merchant_category_2',
            'merchant_category_3',
            'in_stock',
        ];

        return $this->isDirty($watchedAttributes);
    }
}
