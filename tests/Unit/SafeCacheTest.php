<?php

namespace Tests\Unit;

use App\Support\SafeCache;
use Tests\TestCase;

class SafeCacheTest extends TestCase
{
    public function test_safe_cache_memoizes_value_for_same_key(): void
    {
        $calls = 0;
        $key = 'test:safe-cache:' . uniqid('', true);

        $first = SafeCache::remember($key, 60, function () use (&$calls) {
            $calls++;

            return ['status' => 'ok'];
        });

        $second = SafeCache::remember($key, 60, function () use (&$calls) {
            $calls++;

            return ['status' => 'changed'];
        });

        $this->assertSame(['status' => 'ok'], $first);
        $this->assertSame(['status' => 'ok'], $second);
        $this->assertSame(1, $calls);
    }
}
