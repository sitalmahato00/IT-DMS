<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class SafeCache
{
    /**
     * Remember a cached value, but fall back gracefully if the cache backend
     * is unavailable in the current environment.
     */
    public static function remember(string $key, int $ttlSeconds, Closure $callback): mixed
    {
        if ($ttlSeconds <= 0) {
            return $callback();
        }

        try {
            return Cache::remember($key, now()->addSeconds($ttlSeconds), $callback);
        } catch (\Throwable) {
            return $callback();
        }
    }
}

