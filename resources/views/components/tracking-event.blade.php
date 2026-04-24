{{--
    Tracking Event Component

    Fires a custom event for all configured tracking providers (Meta Pixel + GA4).
    Intended for use inside @push('tracking_events') blocks in individual views.

    Props:
        $name  – (string) Event name in Meta Pixel convention, e.g. 'ViewContent', 'Lead', 'Purchase'.
        $data  – (array)  Key/value pairs specific to the event. Passed as JSON to both providers.
                          Defaults to an empty array.

    Usage example:
        @push('tracking_events')
            <x-tracking-event
                name="ViewContent"
                :data="[
                    'content_ids'  => [$product->id],
                    'content_name' => $product->name,
                    'value'        => $product->price,
                    'currency'     => 'BRL',
                    'content_type' => 'product',
                ]"
            />
        @endpush
--}}
@php
    use Illuminate\Support\Str;

    $metaPixelIds   = config('tracking.meta_pixel_ids', []);
    $ga4Ids         = config('tracking.ga4_measurement_ids', []);

    $hasAnyProvider = !empty($metaPixelIds) || !empty($ga4Ids);

    // Map Meta Pixel event names to GA4 recommended event names.
    $ga4EventMap = [
        'PageView'        => 'page_view',
        'ViewContent'     => 'view_item',
        'AddToCart'       => 'add_to_cart',
        'InitiateCheckout'=> 'begin_checkout',
        'Purchase'        => 'purchase',
        'Lead'            => 'generate_lead',
        'CompleteRegist…' => 'sign_up',
        'Search'          => 'search',
        'Contact'         => 'contact',
        'Subscribe'       => 'subscribe',
    ];

    $ga4EventName = $ga4EventMap[$name] ?? Str::snake($name);
    $jsonData     = empty($data) ? '{}' : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
@endphp

@if($hasAnyProvider)
    <script>
        @if(!empty($metaPixelIds))
            if (typeof fbq === 'function') {
                fbq('track', '{{ $name }}', {!! $jsonData !!});
            }
        @endif
        @if(!empty($ga4Ids))
            if (typeof gtag === 'function') {
                gtag('event', '{{ $ga4EventName }}', {!! $jsonData !!});
            }
        @endif
    </script>
@endif
