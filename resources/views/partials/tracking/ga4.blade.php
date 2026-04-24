{{--
    Google Analytics 4 – Global base code.
    Fires page_view on every page for all configured measurement IDs.
    Rendered from layouts/app.blade.php inside <head>.
    Custom events are injected per-view via @push('tracking_events')
    using the <x-tracking-event> component.
--}}
@php $ga4Ids = config('tracking.ga4_measurement_ids', []); @endphp

@if(!empty($ga4Ids))
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Ids[0] }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
@foreach($ga4Ids as $measurementId)
gtag('config', '{{ $measurementId }}');
@endforeach
</script>
<!-- End Google Analytics 4 -->
@endif
