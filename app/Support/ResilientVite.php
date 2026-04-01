<?php

namespace App\Support;

use Illuminate\Foundation\Vite as BaseVite;

class ResilientVite extends BaseVite
{
    protected ?bool $hotServerReachable = null;

    public function isRunningHot()
    {
        if (! parent::isRunningHot()) {
            return false;
        }

        return $this->hotServerReachable ??= $this->hotServerReachable();
    }

    protected function hotServerReachable(): bool
    {
        $hotUrl = trim((string) @file_get_contents($this->hotFile()));

        if ($hotUrl === '') {
            return false;
        }

        $parts = parse_url($hotUrl);

        if (! isset($parts['host'])) {
            return false;
        }

        $scheme = $parts['scheme'] ?? 'http';
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        $hosts = array_values(array_unique(array_filter([
            $parts['host'],
            ...$this->fallbackHostsFor($parts['host']),
        ])));

        foreach ($hosts as $host) {
            $connection = @fsockopen($host, $port, $errorCode, $errorMessage, 0.3);

            if (is_resource($connection)) {
                fclose($connection);

                return true;
            }
        }

        return false;
    }

    protected function fallbackHostsFor(string $host): array
    {
        if (! in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return [];
        }

        return [
            env('VITE_HEALTH_HOST', 'vite'),
            'host.docker.internal',
        ];
    }
}
