<?php

/**
 * Tracking / Analytics Configuration
 *
 * Multiple IDs are supported for each provider.
 * Set the corresponding env var as a comma-separated list, e.g.:
 *   META_PIXEL_IDS=111111111111111,222222222222222
 *   GA4_MEASUREMENT_IDS=G-XXXXXXXXXX,G-YYYYYYYYYY
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Meta Pixel IDs
    |--------------------------------------------------------------------------
    | Supports multiple pixels separated by comma in the env variable.
    | The PageView event is fired automatically for every ID via the layout.
    | Custom events can be injected per-view using <x-tracking-event>.
    */
    'meta_pixel_ids' => array_values(array_filter(
        array_map('trim', explode(',', env('META_PIXEL_IDS', '')))
    )),

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 Measurement IDs
    |--------------------------------------------------------------------------
    | Supports multiple measurement IDs separated by comma in the env variable.
    | The page_view event is fired automatically for every ID via the layout.
    | Custom events can be injected per-view using <x-tracking-event>.
    */
    'ga4_measurement_ids' => array_values(array_filter(
        array_map('trim', explode(',', env('GA4_MEASUREMENT_IDS', '')))
    )),

];
