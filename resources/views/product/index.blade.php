@extends('layouts.app')
@section('title', $product->name . ' - Monitor de Preços')
@section('description', 'Compare preços do ' . $product->name . ' em diversas lojas. Encontre a melhor oferta e economize!')

@push('meta')
@php
    $productUrl = route('product.show', ['id' => $product->id, 'slug' => $product->permalink]);
    $productTitle = $product->name . ' - Monitor de Preços';
    $productDescription = 'Compare preços do ' . $product->name . ' em diversas lojas. Encontre a melhor oferta e economize!';
    if ($product->brand) {
        $productDescription = $product->brand . ' - ' . $productDescription;
    }
    $formattedPrice = number_format($product->price, 2, ',', '.');
    $productImage = $product->image_url ?? '';
    $keywords = collect($product->departments)->pluck('name')->push($product->brand)->filter()->implode(', ');
@endphp

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $productUrl }}">

{{-- Open Graph / Facebook / WhatsApp --}}
<meta property="og:type" content="product">
<meta property="og:site_name" content="Monitor de Preços">
<meta property="og:locale" content="pt_BR">
<meta property="og:url" content="{{ $productUrl }}">
<meta property="og:title" content="{{ $productTitle }}">
<meta property="og:description" content="{{ $productDescription }}">
@if($productImage)
<meta property="og:image" content="{{ $productImage }}">
<meta property="og:image:alt" content="{{ $product->name }}">
<meta property="og:image:width" content="800">
<meta property="og:image:height" content="800">
@endif

{{-- Open Graph Product pricing --}}
<meta property="product:price:amount" content="{{ $product->price }}">
<meta property="product:price:currency" content="BRL">
@if($product->brand)
<meta property="product:brand" content="{{ $product->brand }}">
@endif

{{-- Schema.org JSON-LD Structured Data --}}
@php
    $schemaOffer = [
        '@type'         => 'Offer',
        'url'           => $productUrl,
        'priceCurrency' => 'BRL',
        'price'         => number_format($product->price, 2, '.', ''),
        'availability'  => 'https://schema.org/InStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ];
    if ($product->lowest_recorded_price) {
        $schemaOffer['priceValidUntil'] = now()->addDays(30)->toDateString();
    }

    $schemaProduct = [
        '@context' => 'https://schema.org/',
        '@type'    => 'Product',
        'name'     => $product->name,
        'url'      => $productUrl,
        'offers'   => $schemaOffer,
    ];
    if ($product->description) {
        $schemaProduct['description'] = strip_tags($product->description);
    }
    if ($productImage) {
        $schemaProduct['image'] = $productImage;
    }
    if ($product->brand) {
        $schemaProduct['brand'] = ['@type' => 'Brand', 'name' => $product->brand];
    }
    if ($product->sku) {
        $schemaProduct['sku'] = $product->sku;
    }
    if ($product->departments->isNotEmpty()) {
        $schemaProduct['category'] = $product->departments->first()->name;
    }
@endphp
<script type="application/ld+json">{!! json_encode($schemaProduct, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-6">
        <nav class="mb-6 overflow-x-auto">
            <ol class="flex items-center space-x-2 text-sm text-gray-600 whitespace-nowrap">
                <li>
                    <a href="/" class="hover:text-primary transition-colors">Inicial</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900">{{ ucwords($product->name) }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3">
                <div class="mb-8">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ ucwords($product->name) }}</h1>
                    <div class="text-sm text-gray-700 mb-2">
                        {{ $product->sku }}
                    </div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-500">sem avaliações</span>
                    </div>
                </div>

                <!-- Product Details Grid -->
                <div class="grid lg:grid-cols-2 gap-8 mb-8">
                    <!-- Image Gallery -->
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div class="aspect-square bg-white rounded-lg border border-gray-200 p-6">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain product-image-main">
                        </div>
                    </div>

                    <!-- Price and Actions -->
                    <div class="space-y-6">
                        <!-- Price Section -->
                        <div class="space-y-2">
                            @if($product->highest_recorded_price && $product->highest_recorded_price > $product->price)
                                <div class="text-lg price-original line-through text-gray-400">
                                    de R$ {{ number_format($product->highest_recorded_price, 2, ',', '.') }}
                                </div>
                                <div class="inline-block px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold mb-1">
                                    {{ round((($product->highest_recorded_price - $product->price) / $product->highest_recorded_price) * 100) }}% OFF
                                </div>
                            @endif
                            <div class="text-3xl lg:text-4xl font-bold price-current">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <a rel="nofollow" target="_blank-{{ $product->id }}" href="{{ route('product.redirect', $product->id) }}" class="block w-full text-center action-button-primary text-white font-semibold py-3 px-6 rounded-lg transition-colors cursor-pointer" title="Comprar {{ $product->name }} na loja {{ $product->store->name }}">
                                Comprar
                            </a>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <button class="action-button-secondary flex items-center justify-center space-x-2 text-gray-700 py-2 px-4 rounded-lg transition-colors cursor-pointer" data-action="copy-link">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Copiar link</span>
                                </button>
                                
                                <a href="{{ route('product.share.whatsapp', $product->id) }}" target="_blank" class="action-button-secondary flex items-center justify-center space-x-2 text-gray-700 py-2 px-4 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    <span class="hidden sm:inline">Compartilhar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="sidebar-card bg-white border border-gray-200 rounded-lg p-4 space-y-3 cursor-pointer hover:border-primary hover:shadow-md transition-all" id="price-history-card">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-teal-500 rounded-full shrink-0"></div>
                        <span class="text-sm font-medium text-gray-900">Veja o histórico de preço</span>
                    </div>
                    <p class="text-sm text-gray-600">
                        Acesso ao gráfico com alterações de preço
                    </p>
                    <div class="flex items-center justify-between">
                        <button class="text-primary hover:text-primary-dark text-sm font-medium cursor-pointer">
                            Ver Histórico
                        </button>
                    </div>
                </div>

                <!-- Want to Pay Less Card -->
                @auth
                    <div class="sidebar-card bg-white border border-gray-200 rounded-lg p-4 space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Quer pagar mais barato?</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Avisamos quando o preço baixar
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <button id="saveProductBtnCard" data-product-id="{{ $product->id }}" class="flex items-center justify-center space-x-1 border border-gray-300 text-gray-700 text-sm font-medium py-2 px-3 rounded-lg hover:border-primary hover:text-primary transition-colors save-btn-card cursor-pointer">
                                <svg class="w-4 h-4 save-icon-card" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                <span class="save-text-card">Salvar</span>
                            </button>
                            <button class="flex items-center justify-center space-x-1 bg-purple-600 text-white text-sm font-medium py-2 px-3 rounded-lg hover:bg-purple-700 transition-colors price-alert-trigger cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span>Alerta</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="sidebar-card bg-white border border-gray-200 rounded-lg p-4 space-y-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Quer pagar mais barato?</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Avisamos quando o preço baixar
                        </p>
                        <a href="{{ route('auth.login') }}" class="block w-full text-center bg-purple-600 text-white text-sm font-medium py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors cursor-pointer">
                            Fazer login para criar alerta
                        </a>
                    </div>
                @endauth
            </div>
        </div>
        
        @if($priceHistory['has_history'])
            <div id="price-history" class="space-y-6 mb-12">
                <h2 class="text-xl font-semibold text-gray-900">Histórico de Preços</h2>
                
                <!-- Chart + Stats Layout -->
                <div class="price-history-layout">

                    <!-- Chart -->
                    <div class="price-history-chart bg-white border border-gray-200 rounded-lg p-6">
                        <div class="h-80">
                            <canvas id="priceChart"></canvas>
                        </div>
                    </div>

                    <!-- Stats Card -->
                    <div class="price-history-stats bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center gap-1.5 mb-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Preços registrados</p>
                            <!-- Tooltip trigger -->
                            <div class="price-stats-tooltip-wrapper" aria-describedby="price-stats-tooltip">
                                <svg class="w-3.5 h-3.5 text-gray-400 cursor-help shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div id="price-stats-tooltip" role="tooltip" class="price-stats-tooltip">
                                    <p class="font-semibold text-white mb-1">Sobre estes valores</p>
                                    <p class="text-gray-300 leading-relaxed">Os preços registrados refletem apenas os <span class="text-white font-medium">preços regulares</span> do produto — sem considerar promoções temporárias.</p>
                                    <p class="text-gray-300 leading-relaxed mt-2">Alguns vendedores inflam artificialmente o preço original antes de datas como Black Friday para exagerar o percentual de desconto. Aqui você vê o histórico real.</p>
                                    <div class="price-stats-tooltip__arrow"></div>
                                </div>
                            </div>
                        </div>

                        <div class="price-history-stats__items">

                            <!-- Menor preço registrado -->
                            <div class="flex-1 flex flex-col gap-2 p-3 bg-teal-50 rounded-lg">
                                <div class="flex items-center gap-1.5">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-teal-100">
                                        <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0l-4-4m4 4l-4 4M6 10V3m0 0L2 7m4-4l4 4" />
                                        </svg>
                                    </span>
                                    <span class="text-xs text-teal-700 font-medium leading-tight">Menor preço</span>
                                </div>
                                <span class="text-base font-bold text-teal-800">
                                    @if($product->lowest_recorded_price)
                                        R$ {{ number_format($product->lowest_recorded_price, 2, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <!-- Maior preço registrado -->
                            <div class="flex-1 flex flex-col gap-2 p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center gap-1.5">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100">
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 7H3m0 0l4 4M3 7l4-4m7 14h8m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                    </span>
                                    <span class="text-xs text-red-600 font-medium leading-tight">Maior preço</span>
                                </div>
                                <span class="text-base font-bold text-red-700">
                                    @if($product->highest_recorded_price)
                                        R$ {{ number_format($product->highest_recorded_price, 2, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                        </div>

                        <!-- Quantidade de registros -->
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs text-gray-500">{{ count($priceHistory['data']) }} {{ count($priceHistory['data']) === 1 ? 'registro' : 'registros' }}</span>
                        </div>
                    </div>

                </div>
            </div>
        @else
            <!-- Mensagem quando não há histórico -->
            <div class="space-y-6 mb-12">
                <h2 class="text-xl font-semibold text-gray-900">Histórico de Preços</h2>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                    <div class="max-w-sm mx-auto">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Ainda não há histórico de preços</h3>
                        <p class="text-gray-600">O gráfico com o histórico de preços aparecerá quando tivermos dados suficientes para este produto.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ficha Técnica Section -->
        <div class="space-y-6 mb-12">
            <h2 class="text-xl font-semibold text-gray-900">Ficha técnica</h2>
            
            @if($product->visibleAttributes->count() > 0)
                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Product Specifications -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Características do Produto</h3>
                        
                        <div class="space-y-3">
                            @foreach($product->visibleAttributes as $attribute)
                                <div class="flex flex-col sm:flex-row py-3 border-b border-gray-100">
                                    <div class="sm:w-1/2 text-sm text-gray-600 font-medium mb-1 sm:mb-0">
                                        {{ __('product_attributes.' . $attribute->key) !== 'product_attributes.' . $attribute->key
                                            ? __('product_attributes.' . $attribute->key)
                                            : ucwords(str_replace('_', ' ', $attribute->key)) }}
                                    </div>
                                    <div class="sm:w-1/2 text-sm text-gray-900">{{ $attribute->description }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Descrição</h3>
                        @if($product->description)
                            <div class="bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500 leading-relaxed italic">
                                    Descrição não disponível para este produto.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- No attributes available -->
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Ficha técnica em breve</h3>
                    <p class="text-gray-600">As especificações técnicas deste produto serão disponibilizadas em breve.</p>
                </div>
            @endif
        </div>

        {{-- Similar Products Carousel --}}
        @include('product.partials.similar-products')

        <!-- Price Alert Modal -->
        @auth
        <div id="priceAlertModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Configurar Alerta de Preço</h3>
                        <button class="text-gray-400 hover:text-gray-600 cursor-pointer" id="closeModal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4" id="modalDescription">
                            Digite o preço que você deseja pagar por este produto. Vamos notificá-lo quando o preço atingir ou ficar abaixo desse valor.
                        </p>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preço Atual: <span class="text-primary font-semibold">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </label>
                        </div>
                        
                        <div class="mb-4">
                            <label for="targetPrice" class="block text-sm font-medium text-gray-700 mb-2">
                                Preço Desejado
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">R$</span>
                                <input 
                                    type="text" 
                                    id="targetPrice"
                                    inputmode="numeric"
                                    placeholder="Ex: {{ number_format($product->price * 0.9, 2, ',', '.') }}"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    autocomplete="off"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Deixe em branco para ser notificado sobre qualquer mudança de preço</p>
                        </div>
                        
                        <div id="existingAlertActions" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-800 mb-2">
                                Você já tem um alerta configurado para este produto.
                            </p>
                            <p class="text-sm font-medium text-yellow-900">
                                Preço alvo atual: <span id="currentTargetPrice"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-5">
                        <button 
                            id="removeAlertBtn" 
                            class="hidden flex-1 bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 transition-colors cursor-pointer"
                        >
                            Remover Alerta
                        </button>
                        <button 
                            id="saveAlertBtn" 
                            class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors cursor-pointer"
                        >
                            Salvar Alerta
                        </button>
                        <button 
                            id="cancelBtn" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global chart instance variable
            let priceChartInstance = null;

            // Price history chart
            const ctx = document.getElementById('priceChart');

            if (ctx) {
                const priceHistory = @json($priceHistory['data']);
                const hasHistory = @json($priceHistory['has_history']);
                
                if (hasHistory && priceHistory.length > 0) {
                    // Destroy existing chart instance if it exists
                    if (priceChartInstance) {
                        priceChartInstance.destroy();
                    }

                    priceChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: priceHistory.map(item => item.formatted_date),
                            datasets: [{
                                label: 'Preço',
                                data: priceHistory.map(item => item.price),
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.1,
                                pointBackgroundColor: '#06b6d4',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            elements: {
                                point: {
                                    hoverBackgroundColor: '#0891b2'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        color: '#f3f4f6'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                        },
                                        font: {
                                            size: 11
                                        },
                                        maxTicksLimit: 4
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: '#06b6d4',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Copy link functionality
            const copyLinkBtn = document.querySelector('[data-action="copy-link"]');
            
            if (copyLinkBtn) {
                copyLinkBtn.addEventListener('click', function() {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        // Simple feedback - you could replace with a toast notification
                        const originalText = this.innerHTML;
                        this.innerHTML = this.innerHTML.replace('Copiar link', 'Copiado!');
                        this.classList.add('text-green-600', 'border-green-500');
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.remove('text-green-600', 'border-green-500');
                        }, 2000);
                    }).catch(() => {
                        console.log('Erro ao copiar link');
                    });
                });
            }

            // Image gallery functionality
            const thumbnails = document.querySelectorAll('.product-thumbnail');
            const mainImage = document.querySelector('.product-image-main');
            
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', function() {
                    // Remove active class from all thumbnails
                    thumbnails.forEach(thumb => thumb.classList.remove('active'));
                    
                    // Add active class to clicked thumbnail
                    this.classList.add('active');
                    
                    // Update main image (in a real app, you'd have actual different images)
                    if (mainImage) {
                        mainImage.style.opacity = '0.5';
                        setTimeout(() => {
                            // Here you would update the src with the actual image
                            mainImage.style.opacity = '1';
                        }, 150);
                    }
                });
            });

            // Store offer cards hover effects
            const storeCards = document.querySelectorAll('.store-offer-card');
            
            storeCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            });

            // Price alert toggle functionality
            const priceAlertToggle = document.getElementById('price-alert-toggle');
            
            if (priceAlertToggle) {
                priceAlertToggle.addEventListener('change', function() {
                    if (this.checked) {
                        // Here you would implement the actual alert subscription
                        console.log('Alert de preço ativado');
                    } else {
                        console.log('Alert de preço desativado');
                    }
                });
            }

            // Price history card click functionality
            const priceHistoryCard = document.getElementById('price-history-card');
            
            if (priceHistoryCard) {
                priceHistoryCard.addEventListener('click', function() {
                    const priceHistorySection = document.getElementById('price-history');
                    
                    if (priceHistorySection) {
                        priceHistorySection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }

            // Time period filter for price history
            const periodButtons = document.querySelectorAll('.period-filter-btn');
            periodButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active state from all buttons
                    periodButtons.forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-white');
                        btn.classList.add('border', 'border-gray-300');
                    });
                    
                    // Add active state to clicked button
                    this.classList.remove('border', 'border-gray-300');
                    this.classList.add('bg-primary', 'text-white');
                    
                    // In a real app, you would update the chart data here
                    console.log('Período selecionado:', this.textContent);
                });
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (priceChartInstance) {
                    priceChartInstance.destroy();
                    priceChartInstance = null;
                }
            });
        });

        // Product Save and Alert functionality
        @auth
        (function() {
            // Prevent multiple initializations
            if (window.__productActionsInitialized) {
                return;
            }
            window.__productActionsInitialized = true;

            document.addEventListener('DOMContentLoaded', function() {
                const productId = {{ $product->id }};
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                // Get save button in the card
                const saveProductBtnCard = document.getElementById('saveProductBtnCard');
                
                // Modal elements
                const modal = document.getElementById('priceAlertModal');
                const closeModalBtn = document.getElementById('closeModal');
                const cancelBtn = document.getElementById('cancelBtn');
                const saveAlertBtn = document.getElementById('saveAlertBtn');
                const removeAlertBtn = document.getElementById('removeAlertBtn');
                const targetPriceInput = document.getElementById('targetPrice');
                const existingAlertActions = document.getElementById('existingAlertActions');
                const currentTargetPriceSpan = document.getElementById('currentTargetPrice');
                
                // Get all price alert triggers (only the button, not the card)
                const priceAlertTriggers = document.querySelectorAll('.price-alert-trigger');
                
                let currentAlertData = null;

            // Currency mask functions
            function formatCurrency(value) {
                // Remove tudo exceto números
                let numericValue = value.replace(/\D/g, '');
                
                if (!numericValue || numericValue === '0') {
                    return '';
                }
                
                // Converte para número (centavos)
                let numberValue = parseInt(numericValue);
                
                // Limita a 1 bilhão (1.000.000.000,00)
                const maxValue = 100000000000; // 1 bilhão em centavos
                if (numberValue > maxValue) {
                    numberValue = maxValue;
                }
                
                // Converte centavos para reais
                const floatValue = numberValue / 100;
                
                // Formata no padrão pt-BR
                return floatValue.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function parseCurrencyToFloat(value) {
                if (!value) return null;
                
                // Remove pontos (separadores de milhar) e substitui vírgula por ponto
                const numericValue = value.replace(/\./g, '').replace(',', '.');
                const floatValue = parseFloat(numericValue);
                
                return isNaN(floatValue) ? null : floatValue;
            }

            // Apply currency mask to input
            targetPriceInput.addEventListener('input', function(e) {
                let value = e.target.value;
                
                // Guarda a posição do cursor
                const cursorPosition = e.target.selectionStart;
                const oldLength = value.length;
                
                // Formata o valor
                const formatted = formatCurrency(value);
                e.target.value = formatted;
                
                // Ajusta a posição do cursor após formatação
                const newLength = formatted.length;
                const lengthDiff = newLength - oldLength;
                const newCursorPosition = cursorPosition + lengthDiff;
                
                // Reposiciona o cursor (se não estiver no final)
                if (cursorPosition !== oldLength) {
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            });

            targetPriceInput.addEventListener('blur', function(e) {
                if (e.target.value) {
                    const formatted = formatCurrency(e.target.value);
                    e.target.value = formatted || '';
                }
            });

            // Check initial state
            checkWishStatus();

            // Save button handler (card)
            if (saveProductBtnCard) {
                saveProductBtnCard.addEventListener('click', function() {
                    toggleSaveProductCard();
                });
            }

            // Price alert triggers - open modal
            priceAlertTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    openPriceAlertModal();
                });
            });

            // Modal close handlers
            [closeModalBtn, cancelBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => closeModal());
                }
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Save alert button handler
            saveAlertBtn.addEventListener('click', function() {
                saveOrUpdateAlert();
            });

            // Remove alert button handler
            removeAlertBtn.addEventListener('click', function() {
                removeAlert();
            });

            function openPriceAlertModal() {
                // Fetch current wish/alert status first
                fetch(`/wish-products/${productId}/check`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    currentAlertData = data;
                    
                    if (data.has_alert && data.wish && data.wish.target_price) {
                        // Show existing alert info
                        existingAlertActions.classList.remove('hidden');
                        removeAlertBtn.classList.remove('hidden');
                        
                        // Format the price display
                        const priceValue = parseFloat(data.wish.target_price);
                        currentTargetPriceSpan.textContent = `R$ ${priceValue.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}`;
                        
                        // Format the input value
                        targetPriceInput.value = formatCurrency(priceValue.toString());
                    } else {
                        // New alert
                        existingAlertActions.classList.add('hidden');
                        removeAlertBtn.classList.add('hidden');
                        targetPriceInput.value = '';
                    }
                    
                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.ToastManager.error('Erro ao buscar informações do alerta');
                });
            }

            function closeModal() {
                modal.classList.add('hidden');
                targetPriceInput.value = '';
                targetPriceInput.classList.remove('border-red-500');
                existingAlertActions.classList.add('hidden');
                removeAlertBtn.classList.add('hidden');
            }

            function saveOrUpdateAlert() {
                const targetPriceValue = targetPriceInput.value.trim();
                
                const body = {
                    product_id: productId,
                };
                
                // Parse currency formatted value
                if (targetPriceValue) {
                    const parsedPrice = parseCurrencyToFloat(targetPriceValue);
                    
                    // Validação do valor
                    if (parsedPrice === null || parsedPrice < 0) {
                        window.ToastManager.error('Por favor, insira um valor válido');
                        targetPriceInput.focus();
                        return;
                    }
                    
                    // Validação do limite máximo (1 bilhão)
                    if (parsedPrice > 1000000000) {
                        window.ToastManager.error('O valor máximo permitido é R$ 1.000.000.000,00');
                        targetPriceInput.focus();
                        return;
                    }
                    
                    body.target_price = parsedPrice;
                }

                // Desabilita o botão durante o envio
                saveAlertBtn.disabled = true;
                saveAlertBtn.textContent = 'Salvando...';

                // Always POST to /wish-products - controller handles both create and update
                fetch('/wish-products', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(body)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    window.ToastManager.success(data.message || 'Alerta configurado com sucesso!');
                    closeModal();
                    checkWishStatus();
                })
                .catch(error => {
                    console.error('Error:', error);
                    const message = error.message || 'Erro ao salvar alerta';
                    window.ToastManager.error(message);
                })
                .finally(() => {
                    // Reabilita o botão
                    saveAlertBtn.disabled = false;
                    saveAlertBtn.textContent = 'Salvar Alerta';
                });
            }

            function removeAlert() {
                if (!confirm('Tem certeza que deseja remover este alerta de preço?')) {
                    return;
                }

                fetch(`/wish-products/${productId}/price-alert`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ target_price: null })
                })
                .then(response => response.json())
                .then(data => {
                    window.ToastManager.success(data.message || 'Alerta removido com sucesso!');
                    closeModal();
                    checkWishStatus();
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.ToastManager.error('Erro ao remover alerta');
                });
            }

            function checkWishStatus() {
                fetch(`/wish-products/${productId}/check`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateSaveButtonCard(data.wished);
                    updatePriceAlertCard(data.has_alert);
                })
                .catch(error => console.error('Error:', error));
            }

            let isTogglingProduct = false;

            function toggleSaveProductCard() {
                // Previne cliques múltiplos
                if (isTogglingProduct) {
                    return;
                }

                isTogglingProduct = true;
                
                const isSaved = saveProductBtnCard.classList.contains('saved');
                const url = isSaved ? `/wish-products/${productId}` : '/wish-products';
                const method = isSaved ? 'DELETE' : 'POST';

                // Desabilita botão durante a requisição
                saveProductBtnCard.disabled = true;
                saveProductBtnCard.style.opacity = '0.6';

                const options = {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                };

                if (!isSaved) {
                    options.body = JSON.stringify({ product_id: productId });
                }

                fetch(url, options)
                    .then(response => response.json().then(data => ({ status: response.status, data })))
                    .then(({ status, data }) => {
                        if (status >= 200 && status < 300) {
                            // Sucesso
                            updateSaveButtonCard(data.wished);
                            window.ToastManager.success(data.message);
                        } else {
                            // Erro
                            window.ToastManager.error(data.message || 'Erro ao processar solicitação');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.ToastManager.error('Erro ao processar solicitação');
                    })
                    .finally(() => {
                        // Reabilita botão
                        saveProductBtnCard.disabled = false;
                        saveProductBtnCard.style.opacity = '1';
                        isTogglingProduct = false;
                    });
            }

            function updateSaveButtonCard(isSaved) {
                if (!saveProductBtnCard) return;
                
                const icon = saveProductBtnCard.querySelector('.save-icon-card');
                const text = saveProductBtnCard.querySelector('.save-text-card');
                
                if (isSaved) {
                    saveProductBtnCard.classList.add('saved', 'bg-blue-50', 'text-blue-700', 'border-blue-300');
                    saveProductBtnCard.classList.remove('text-gray-700', 'hover:text-primary', 'hover:border-primary', 'border-gray-300');
                    icon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                    icon.setAttribute('fill', 'currentColor');
                    text.textContent = 'Salvo';
                } else {
                    saveProductBtnCard.classList.remove('saved', 'bg-blue-50', 'text-blue-700', 'border-blue-300');
                    saveProductBtnCard.classList.add('text-gray-700', 'hover:text-primary', 'hover:border-primary', 'border-gray-300');
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>';
                    icon.setAttribute('fill', 'none');
                    text.textContent = 'Salvar';
                }
            }

            function updatePriceAlertCard(hasAlert) {
                // Update alert button text and color based on alert status
                const alertButtons = document.querySelectorAll('.price-alert-trigger');
                
                alertButtons.forEach(button => {
                    const span = button.querySelector('span');
                    if (span && span.textContent === 'Alerta') {
                        // This is the card button with just "Alerta" text
                        if (hasAlert) {
                            button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                            button.classList.add('bg-green-600', 'hover:bg-green-700');
                        } else {
                            button.classList.remove('bg-green-600', 'hover:bg-green-700');
                            button.classList.add('bg-purple-600', 'hover:bg-purple-700');
                        }
                    }
                });
            }
            });
        })();
        @endauth
    </script>

@push('tracking_events')
@php
    $trackingCategory = $product->departments->isNotEmpty()
        ? $product->departments->first()->name
        : null;
@endphp
<x-tracking-event
    name="ViewContent"
    :data="array_filter([
        'content_ids'  => [(string) $product->id],
        'content_name' => $product->name,
        'content_type' => 'product',
        'value'        => (float) $product->price,
        'currency'     => 'BRL',
        'brand'        => $product->brand ?: null,
        'category'     => $trackingCategory,
    ])"
/>
@endpush
@endpush