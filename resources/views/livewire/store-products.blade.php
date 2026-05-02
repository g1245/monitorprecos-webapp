<div class="store-products">
    <div class="container mx-auto px-4 py-8">
        <!-- Store Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-6">
                @if($store->logo)
                    <img src="{{ Storage::disk('public')->url($store->logo) }}" 
                         alt="{{ $store->name }}" 
                         class="w-24 h-24 object-contain rounded-lg border border-gray-200">
                @else
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $store->name }}</h1>

                    @if($store->metadata && isset($store->metadata['description']))
                        <div class="text-gray-700 mb-2">{{ $store->metadata['description'] }}</div>
                    @endif
                    
                    @if($store->metadata && isset($store->metadata['website']))
                        <div class="text-sm text-gray-500">
                            <a href="{{ $store->metadata['website'] }}" 
                               target="_blank" 
                               rel="nofollow noopener"
                               class="text-primary hover:underline">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    {{ parse_url($store->metadata['website'], PHP_URL_HOST) }}
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Linha 1: Título e total de produtos -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <h2 class="text-2xl font-bold text-gray-800">Produtos</h2>
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
                        <option value="created_at">Ordenar por: Mais recentes</option>
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
                @if($minPrice || $maxPrice || $brand || $recentDiscountOnly || $keyword || !$filterInStock)
                    <button wire:click="clearFilters" class="text-sm text-primary hover:underline">
                        Limpar filtros
                    </button>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <!-- Filtro por palavra-chave -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Palavra-chave</label>
                    <input type="text" wire:model="keyword" placeholder="Buscar por nome, descrição ou SKU"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" wire:model="recentDiscountOnly" class="rounded border-gray-300 text-primary focus:ring-primary">
                        Somente com descontos recentes
                    </label>
                </div>

                <div class="md:col-span-2">
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

        @if($products->count() > 0)
            <div class="relative">
            <div wire:loading wire:target="applyFilters,sortField,sortDirection"
                 class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center rounded-lg">
                <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-label="Carregando">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                    <div wire:key="store-product-{{ $product->id }}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        {{-- Imagem --}}
                        <a href="{{ route('product.show', ['slug' => $product->permalink, 'id' => $product->id]) }}"
                           class="block p-4"
                           x-data="{ loaded: false }"
                           x-init="loaded = $refs.img.complete && $refs.img.naturalWidth > 0">
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
                        <div class="px-4 py-2 border-t border-gray-100 flex items-center gap-1.5">
                            @if($store->logo)
                                <img src="{{ Storage::disk('public')->url($store->logo) }}"
                                     alt="{{ $store->name }}"
                                     class="w-4 h-4 object-contain">
                            @endif
                            <span class="text-xs text-gray-500 truncate">{{ $store->name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-600 text-lg">Nenhum produto disponível no momento.</p>
            </div>
        @endif

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
