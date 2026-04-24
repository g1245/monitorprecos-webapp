<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get user wish products (wishlist with optional price alerts).
     */
    public function userWishProducts(): HasMany
    {
        return $this->hasMany(UserWishProduct::class);
    }

    /**
     * Get products wished by this user through the pivot table.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'users_wish_products')
            ->withTimestamps();
    }

    /**
     * Get browsing history for this user.
     */
    public function browsingHistory(): HasMany
    {
        return $this->hasMany(UserBrowsingHistory::class);
    }

    /**
     * Check if user has wished for a specific product.
     */
    public function hasWishProduct(int $productId): bool
    {
        return $this->userWishProducts()->where('product_id', $productId)->exists();
    }

    /**
     * Check if user has a price alert for a specific product.
     */
    public function hasPriceAlert(int $productId): bool
    {
        return $this->userWishProducts()
            ->where('product_id', $productId)
            ->whereNotNull('target_price')
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get the wish product for a specific product.
     */
    public function getWishProduct(int $productId): ?UserWishProduct
    {
        return $this->userWishProducts()->where('product_id', $productId)->first();
    }
}
