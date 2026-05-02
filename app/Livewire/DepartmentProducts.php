<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Product;
use App\Models\Store;
use Livewire\Component;

class DepartmentProducts extends Component
{
    public Department $department;
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
    public ?string $keyword = null;
    public bool $filterInStock = true;

    protected $queryString = [
        'sortField'          => ['except' => 'discount_percentage'],
        'sortDirection'      => ['except' => 'desc'],
        'page'               => ['except' => 1],
        'minPrice'           => ['except' => null],
        'maxPrice'           => ['except' => null],
        'brand'              => ['except' => null],
        'storeId'            => ['except' => null],
        'recentDiscountOnly' => ['except' => false],
        'keyword'            => ['except' => null],
        'filterInStock'      => ['except' => true],
    ];

    public function mount(Department $department): void
    {
        $this->department = $department;
    }

    public function loadMore(): void
    {
        $this->page++;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

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

    public function updatingKeyword(): void
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
        $this->keyword = null;
        $this->filterInStock = true;
        $this->page = 1;
    }

    public function render()
    {
        $limit = $this->page * 30;

        $departmentIds = [$this->department->id];

        if ($this->department->hasChildren()) {
            $departmentIds = array_merge($departmentIds, $this->department->getAllDescendantIds());
        }

        $query = Product::query()
            ->fromPublicStore()
            ->parentProducts()
            ->whereHas('departments', fn ($q) => $q->whereIn('departments.id', $departmentIds))
            ->when($this->minPrice !== null, fn ($q) => $q->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn ($q) => $q->where('price', '<=', $this->maxPrice))
            ->when($this->brand !== null && $this->brand !== '', fn ($q) => $q->where('brand', 'LIKE', "%{$this->brand}%"))
            ->when($this->storeId !== null, fn ($q) => $q->where('store_id', $this->storeId))
            ->when($this->recentDiscountOnly, fn ($q) => $q->withRecentPriceChange())
            ->when($this->filterInStock, fn ($q) => $q->inStock())
            ->when($this->keyword !== null && $this->keyword !== '', function ($q) {
                $ids = Product::search($this->keyword)->keys();
                $q->whereIn('products.id', $ids);
            })
            ->when(
                $this->sortField === 'discount_percentage',
                fn ($q) => $q->orderByRaw('(discount_percentage) ' . ($this->sortDirection === 'asc' ? 'asc' : 'desc')),
                fn ($q) => $q->orderBy($this->sortField, $this->sortDirection)
            );

        $total = (clone $query)->count();
        $products = $query->with('store')->limit($limit + 1)->get();

        $this->hasMore = $products->count() > $limit;
        $products = $products->take($limit);

        $stores = Store::where('has_public', true)->orderBy('name')->get(['id', 'name']);

        return view('livewire.department-products', [
            'products' => $products,
            'total'    => $total,
            'stores'   => $stores,
        ]);
    }
}