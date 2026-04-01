<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Data Cache TTL
    |--------------------------------------------------------------------------
    |
    | Public-facing pages are the most likely to receive the highest traffic.
    | We keep their cached query payloads short-lived so content stays fresh
    | while still reducing repeated database work under load.
    |
    */

    'public_data_cache_ttl' => max(0, (int) env('PUBLIC_DATA_CACHE_TTL', 300)),

    /*
    |--------------------------------------------------------------------------
    | Shared Department Cache TTL
    |--------------------------------------------------------------------------
    |
    | Department branding/details are shared across many views. Caching them
    | prevents repeated queries from the global view composer.
    |
    */

    'department_cache_ttl' => max(0, (int) env('DEPARTMENT_CACHE_TTL', 600)),

];
