<div>
    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex gap-6" aria-label="Tabs">
            <a href="{{ route('welcome') }}"
               class="{{ $tab === 'destaques' ? 'border-primary text-primary font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 py-3 text-sm transition-colors">
                Novos Descontos
            </a>
            <a href="{{ route('welcome.recentes') }}"
               class="{{ $tab === 'recentes' ? 'border-primary text-primary font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 py-3 text-sm transition-colors">
                Produtos Recentes
            </a>
            <a href="{{ route('welcome.mais-acessados') }}"
               class="{{ $tab === 'mais-acessados' ? 'border-primary text-primary font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 py-3 text-sm transition-colors">
                Mais Acessados
            </a>
        </nav>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        @forelse($products as $product)
            <div wire:key="welcome-product-{{ $product->id }}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
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

    {{-- Infinite Scroll Sentinel --}}
    @if($hasMore)
        <div
            wire:key="sentinel-{{ $limit }}"
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
