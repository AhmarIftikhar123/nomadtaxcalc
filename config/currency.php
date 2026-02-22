<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Currency API
    |--------------------------------------------------------------------------
    | Used to fetch live exchange rates for local income conversion in Step 2.
    | Responses are cached for `cache_ttl` minutes to avoid excessive API calls.
    */

    'api_url'  => env('CURRENCY_API_URL', 'https://api.frankfurter.app'),

    'cache_ttl' => (int) env('CURRENCY_CACHE_TTL', 1440), // 24 hours in minutes
];
