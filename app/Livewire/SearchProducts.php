<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Store;
use Livewire\Component;

class SearchProducts extends Component
{
    public string $q = '';

    public string $sortField = 'discount_percentage';
    public string $sortDirection = 'desc';
    public int $page = 1;
    public bool $hasMore = true;

    // Filter properties
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public ?string $brand = null;
    public ?int $storeId = null;
    public bool $recentDiscountOnly = false;
    public bool $filterInStock = true;

    protected $queryString = [
        'q'                  => ['except' => '', 'as' => 'q'],
        'sortField'          => ['except' => 'discount_percentage'],
        'sortDirection'      => ['except' => 'desc'],
        'page'               => ['except' => 1],
        'minPrice'           => ['except' => null],
        'maxPrice'           => ['except' => null],
        'brand'              => ['except' => null],
        'storeId'            => ['except' => null],
        'recentDiscountOnly' => ['except' => false],
        'filterInStock'      => ['except' => true],
    ];

    public function mount(string $query = ''): void
    {
        $this->q = $query;
    }

    public function loadMore(): void
    {
        $this->page++;
    }

    public function updatingQ(): void
    {
        $this->page = 1;
    }

    public function updatingSortField(): void
    {
        $this->page = 1;
    }

    public function updatingSortDirection(): void
    {
        $this->page = 1;
    }

    public function updatingMinPrice(): void
    {
        $this->page = 1;
    }

    public function updatingMaxPrice(): void
    {
        $this->page = 1;
    }

    public function updatingBrand(): void
    {
        $this->page = 1;
    }

    public function updatingStoreId(): void
    {
        $this->page = 1;
    }

    public function updatingRecentDiscountOnly(): void
    {
        $this->page = 1;
    }

    public function updatingFilterInStock(): void
    {
        $this->page = 1;
    }

    public function applyFilters(): void
    {
        $this->page = 1;
    }

    public function clearFilters(): void
    {
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->brand = null;
        $this->storeId = null;
        $this->recentDiscountOnly = false;
        $this->filterInStock = true;
        $this->page = 1;
    }

    public function render()
    {
        $limit = $this->page * 30;
        $parsed = $this->parseSearchQuery();

        $query = Product::search($this->q)
            ->where('is_store_visible', true)
            ->where('is_parent', 0)
            ->when($this->minPrice !== null, fn($q) => $q->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn($q) => $q->where('price', '<=', $this->maxPrice))
            ->when($this->brand !== null && $this->brand !== '', fn($q) => $q->where('brand', $this->brand))
            ->when($this->storeId !== null, fn($q) => $q->where('store_id', $this->storeId))
            ->when($this->recentDiscountOnly, fn($q) => $q->query(fn($eq) => $eq->withRecentPriceChange()))
            ->when($this->filterInStock, fn($q) => $q->where('in_stock', 1))
            ->when($this->sortField, fn($q) => $q->orderBy($this->sortField, $this->sortDirection));

        $paginator = $query->paginate($limit, 'page', 1);
        $total = $paginator->total();
        $products = $paginator->getCollection();

        $this->hasMore = $paginator->hasMorePages();

        $stores = Store::where('has_public', true)->orderBy('name')->get(['id', 'name']);

        return view('livewire.search-products', [
            'products'    => $products,
            'total'       => $total,
            'searchField' => $parsed['field'],
            'stores'      => $stores,
        ]);
    }

    /**
     * Parse a key:value search query into field and value components.
     *
     * Supported syntax:
     *   sku:ABC123
     *   name:"tênis nike"
     *   brand:samsung
     *
     * @return array{field: string|null, value: string}
     */
    private function parseSearchQuery(): array
    {
        $pattern = '/^(sku|name|brand):(?:"([^"]+)"|(\S+))/u';

        if ($this->q && preg_match($pattern, $this->q, $matches)) {
            return [
                'field' => $matches[1],
                'value' => $matches[2] !== '' ? $matches[2] : $matches[3],
            ];
        }

        return ['field' => null, 'value' => $this->q];
    }
}
