<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class WelcomeProducts extends Component
{
    public string $tab = 'destaques';
    public int $page = 1;
    public bool $hasMore = true;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function loadMore(): void
    {
        $this->page++;
    }

    public function render()
    {
        $limit = $this->page * 16;

        // Only cache the first page (highest traffic); subsequent pages (loadMore) bypass cache.
        if ($this->page === 1) {
            $cacheKey = "welcome_products:{$this->tab}";
            $products = Cache::remember($cacheKey, now()->addMinutes(10), fn () => $this->queryProducts(17));
        } else {
            $products = $this->queryProducts($limit + 1);
        }

        $this->hasMore = $products->count() > $limit;
        $products = $products->take($limit);

        return view('livewire.welcome-products', compact('products', 'limit'));
    }

    /**
     * Execute the products query for the current tab.
     *
     * @param  int  $fetchLimit  Number of rows to fetch (limit + 1 to detect next page).
     */
    private function queryProducts(int $fetchLimit)
    {
        $query = Product::query()
            ->fromPublicStore()
            ->parentProducts()
            ->inStock()
            ->with('store');

        $query = match ($this->tab) {
            'recentes'        => $query->orderByDesc('created_at'),
            'mais-acessados'  => $query->orderByDesc('views_count'),
            default           => $query->whereColumn('price', '<', 'highest_recorded_price')
                                        ->orderByRaw('(discount_percentage) desc'),
        };

        return $query->limit($fetchLimit)->get();
    }
}
