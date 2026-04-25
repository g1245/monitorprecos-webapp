@props(['product'])

@php
    $hasDiscount = $product->old_price && $product->old_price > $product->price;
    $discountPct = $hasDiscount
        ? round((1 - $product->price / $product->old_price) * 100)
        : 0;
@endphp

@if($hasDiscount)
    <div class="text-xs text-gray-500 line-through">
        de R$&nbsp;{{ number_format($product->old_price, 2, ',', '.') }}
    </div>
@endif

<div class="text-xl font-bold text-primary">
    R$&nbsp;{{ number_format($product->price, 2, ',', '.') }}
</div>

@if($hasDiscount && $discountPct > 1)
    <div class="mt-2">
        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">
            {{ $discountPct }}% OFF
        </span>
    </div>
@endif