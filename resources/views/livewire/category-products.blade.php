<div class="category-products">
    <div class="container mx-auto px-4 py-8">
        <!-- Linha 1: Título da categoria -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800">{{ ucfirst(str_replace('-', ' ', $category)) }}</h1>
                <div class="ml-4 text-gray-600">{{ $total }} produtos</div>
            </div>
        </div>

        <!-- Linha 2: Ordenação -->
        <div class="flex flex-wrap justify-between items-center mb-8 bg-white p-4 rounded-lg shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <div class="mb-2 md:mb-0">
                    <select wire:model.live="sortField" class="bg-white border border-gray-300 rounded-md text-gray-700 h-10 pl-5 pr-10 hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="discount_percentage">Ordenar por: Desconto</option>
                        <option value="name">Ordenar por: Nome</option>
                        <option value="price">Ordenar por: Preço</option>
                    </select>
                </div>

                <div class="mb-2 md:mb-0">
                    <select wire:model.live="sortDirection" class="bg-white border border-gray-300 rounded-md text-gray-700 h-10 pl-5 pr-10 hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="asc">Crescente</option>
                        <option value="desc">Decrescente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filtros avançados -->
        <div id="filters" class="mb-6 bg-white p-4 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Filtros</h3>
                @if($minPrice || $maxPrice || $brand || $storeId || $recentDiscountOnly || $keyword || !$filterInStock)
                    <button wire:click="clearFilters" class="text-sm text-primary hover:underline">
                        Limpar filtros
                    </button>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Filtro de preço -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preço</label>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="minPrice" placeholder="Mín." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <span class="text-gray-500">-</span>
                        <input type="number" wire:model="maxPrice" placeholder="Máx." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <!-- Filtro de marca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                    <input type="text" wire:model="brand" placeholder="Digite a marca" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <!-- Filtro de loja -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loja (ID)</label>
                    <input type="number" wire:model="storeId" placeholder="ID da loja" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-gray-500 mt-1">Informe o ID da loja para filtrar</p>
                </div>

                <!-- Filtro por palavra-chave -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Palavra-chave</label>
                    <input type="text" wire:model="keyword" placeholder="Buscar por nome, descrição ou SKU"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" wire:model="recentDiscountOnly" class="rounded border-gray-300 text-primary focus:ring-primary">
                        Somente com descontos recentes
                    </label>
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" wire:model="filterInStock" class="rounded border-gray-300 text-primary focus:ring-primary">
                        Somente produtos disponíveis
                    </label>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button wire:click="applyFilters" type="button"
                        wire:loading.attr="disabled" wire:target="applyFilters"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary disabled:opacity-60 transition-opacity cursor-pointer">
                    <svg wire:loading wire:target="applyFilters" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Filtrar</span>
                </button>
            </div>
        </div>

        <div class="relative">
        <div wire:loading wire:target="applyFilters,sortField,sortDirection"
             class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center rounded-lg">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-label="Carregando">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
        <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div wire:key="category-product-{{ $product->id }}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    {{-- Imagem --}}
                    <a href="{{ route('product.show', ['slug' => $product->permalink, 'id' => $product->id]) }}" class="block p-4" x-data="{ loaded: false }" x-init="loaded = $refs.img.complete && $refs.img.naturalWidth > 0">
                        <div class="aspect-square relative overflow-hidden rounded-lg bg-gray-50">
                            <div x-show="!loaded" class="absolute inset-0 bg-gray-200 animate-pulse rounded"></div>
                            <img src="{{ $product->image_url ?? 'https://placehold.co/800x800' }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-contain transition-opacity duration-300"
                                :class="loaded ? 'opacity-100' : 'opacity-0'"
                                x-ref="img"
                                x-on:load="loaded = true"
                                x-on:error="loaded = true">
                        </div>
                    </a>

                    {{-- Informações --}}
                    <div class="px-4 pb-3 flex-1 flex flex-col">
                        <a href="{{ route('product.show', ['slug' => $product->permalink, 'id' => $product->id]) }}" class="block mb-2">
                            <h2 class="text-sm font-medium text-gray-800 line-clamp-2 hover:text-primary transition-colors">
                                {{ $product->name }}
                            </h2>
                        </a>

                        <x-product-card-price :product="$product" />
                    </div>

                    {{-- Footer: Loja --}}
                    @if($product->store)
                        <div class="px-4 py-2 border-t border-gray-100 flex items-center gap-1.5">
                            @if($product->store->logo)
                                <img src="{{ Storage::disk('public')->url($product->store->logo) }}"
                                    alt="{{ $product->store->name }}"
                                    class="w-4 h-4 object-contain">
                            @endif
                            <span class="text-xs text-gray-500 truncate">{{ $product->store->name }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <p class="col-span-3 text-center text-gray-500 py-12">Nenhum produto encontrado.</p>
            @endforelse
        </div>
        </div>

        {{-- Infinite Scroll Sentinel --}}
        @if($hasMore)
            <div
                wire:key="sentinel-{{ $products->count() }}"
                x-data="{}"
                x-init="
                    let observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting) {
                            $wire.loadMore();
                        }
                    }, { rootMargin: '300px' });
                    observer.observe($el);
                "
                class="h-4 mt-6">
            </div>
        @endif

        {{-- Loading Spinner --}}
        <div wire:loading class="flex justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-label="Carregando">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>
</div>