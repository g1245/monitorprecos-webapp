<section
    x-data="{
        loading: true,
        products: [],
        canScrollPrev: false,
        canScrollNext: false,
        fetchSimilar() {
            fetch('{{ route('product.similar', $product->id) }}')
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(json => {
                    this.products = json.data ?? [];
                    this.$nextTick(() => this.updateArrows());
                })
                .catch(() => { this.products = []; })
                .finally(() => { this.loading = false; });
        },
        updateArrows() {
            const track = this.$refs.track;
            if (!track) return;
            this.canScrollPrev = track.scrollLeft > 4;
            this.canScrollNext = track.scrollLeft + track.clientWidth < track.scrollWidth - 4;
        },
        scroll(dir) {
            const track = this.$refs.track;
            if (!track) return;
            const cardWidth = track.querySelector('a')?.offsetWidth ?? 192;
            const step = (cardWidth + 16) * 6;
            track.scrollBy({ left: dir === 'next' ? step : -step, behavior: 'smooth' });
        }
    }"
    x-init="fetchSimilar()"
    x-show="loading || products.length > 0"
    class="space-y-4 mb-12"
>
    <h2 class="text-xl font-semibold text-gray-900">Produtos Relacionados</h2>

    {{-- Skeleton loader --}}
    <div x-show="loading" class="flex gap-4 overflow-hidden" aria-hidden="true">
        @for ($i = 0; $i < 6; $i++)
            <div class="shrink-0 w-[calc((100%-5rem)/6)] min-w-36 rounded-lg border border-gray-100 bg-gray-50 animate-pulse">
                <div class="aspect-square bg-gray-200 rounded-t-lg"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Carousel --}}
    <div
        x-show="!loading && products.length > 0"
        x-cloak
        class="relative"
    >
        {{-- Prev arrow --}}
        <button
            x-show="canScrollPrev"
            @click="scroll('prev')"
            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
            aria-label="Produtos anteriores"
        >
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Track --}}
        <div
            x-ref="track"
            @scroll.passive="updateArrows()"
            class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2 scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
            role="list"
            aria-label="Produtos similares"
            style="--card-width: 176px;"
        >
            <template x-for="product in products" :key="product.id">
                <a
                    :href="product.url"
                    class="shrink-0 w-[calc((100%-5rem)/6)] min-w-36 rounded-lg border border-gray-200 bg-white hover:border-primary hover:shadow-md transition-all snap-start group"
                    role="listitem"
                >
                    <div class="aspect-square bg-white rounded-t-lg overflow-hidden flex items-center justify-center p-3">
                        <img
                            :src="product.image_url"
                            :alt="product.name"
                            class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200"
                            loading="lazy"
                        >
                    </div>
                    <div class="p-3 space-y-1">
                        <p
                            class="text-xs text-gray-500 truncate"
                            x-text="product.store_name"
                        ></p>
                        <p
                            class="text-sm text-gray-800 font-medium leading-snug line-clamp-2"
                            x-text="product.name"
                        ></p>
                        <div class="flex flex-col gap-0.5 pt-1">
                            <span
                                x-show="product.old_price && product.old_price > product.price"
                                x-text="'de R$ ' + Number(product.old_price).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"
                                class="text-xs text-gray-500 line-through"
                            ></span>
                            <span
                                class="text-xl font-bold text-primary"
                                x-text="'R$ ' + Number(product.price).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"
                            ></span>
                            <div x-show="product.old_price && product.old_price > product.price" class="mt-1">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded"
                                    x-text="Math.round((1 - product.price / product.old_price) * 100) + '% OFF'"
                                ></span>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        {{-- Next arrow --}}
        <button
            x-show="canScrollNext"
            @click="scroll('next')"
            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
            aria-label="Próximos produtos"
        >
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</section>
