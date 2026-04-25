@props([
    'product',
    'size' => 'md',
])

@php
    $hasDiscount = $product->old_price && $product->old_price > $product->price;
    $discountPct = $hasDiscount
        ? round((1 - $product->price / $product->old_price) * 100)
        : 0;

    $sizeCurrent = match ($size) {
        'lg'    => 'text-[44px] leading-none',
        'sm'    => 'text-xl leading-none',
        default => 'text-3xl leading-none',
    };
    $sizeOld = match ($size) {
        'lg'    => 'text-xl',
        'sm'    => 'text-sm',
        default => 'text-base',
    };
    $sizeLabel = match ($size) {
        'lg'    => 'text-base',
        'sm'    => 'text-xs',
        default => 'text-sm',
    };
    $sizeBadge = match ($size) {
        'lg'    => 'text-sm',
        'sm'    => 'text-[10px]',
        default => 'text-xs',
    };
    $sizeStats = match ($size) {
        'lg'    => 'text-xs',
        'sm'    => 'text-[10px]',
        default => 'text-[11px]',
    };
@endphp

<div class="flex flex-col gap-1">

    {{-- "de" row: old price + discount badge --}}
    @if($hasDiscount)
        <div class="flex items-center gap-2">
            <span class="{{ $sizeLabel }} font-medium text-slate-400">de</span>
            <span class="{{ $sizeOld }} font-medium text-slate-400 line-through whitespace-nowrap">
                R$&nbsp;{{ number_format($product->old_price, 2, ',', '.') }}
            </span>
            <span class="{{ $sizeBadge }} inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-green-100 text-green-700 whitespace-nowrap">
                {{ $discountPct }}% OFF
            </span>
        </div>
    @endif

    {{-- "por" row: current price --}}
    <div class="flex items-center gap-1 flex-wrap">
        @if($hasDiscount)
            <span class="{{ $sizeLabel }} font-medium text-slate-500 mr-1">por</span>
        @endif
        <span class="{{ $sizeCurrent }} font-extrabold text-blue-600 whitespace-nowrap">
            R$&nbsp;{{ number_format($product->price, 2, ',', '.') }}
        </span>
    </div>

    {{-- Badges: lowest / highest recorded price --}}
    @if($product->lowest_recorded_price || $product->highest_recorded_price)
        <div class="flex items-center gap-2 mt-4 flex-wrap">

            @if($product->highest_recorded_price)
                <span class="{{ $sizeStats }} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-red-50 text-red-600 border border-red-200 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                    Maior&nbsp;<strong>R$&nbsp;{{ number_format($product->highest_recorded_price, 2, ',', '.') }}</strong>
                </span>
            @endif

            @if($product->lowest_recorded_price)
                <span class="{{ $sizeStats }} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-green-50 text-green-700 border border-green-200 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 shrink-0"></span>
                    Menor&nbsp;<strong>R$&nbsp;{{ number_format($product->lowest_recorded_price, 2, ',', '.') }}</strong>
                </span>
            @endif

        </div>
    @endif

</div>
