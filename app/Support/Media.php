<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    public static function publicUrl(?string $path, ?string $fallback = null): ?string
    {
        $path = is_string($path) ? trim($path) : '';

        if ($path === '') {
            return $fallback;
        }

        if (preg_match('/^(https?:|data:|blob:)/i', $path)) {
            return $path;
        }

        // Normalize path separators and remove leading slashes.
        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }

        $normalized = ltrim($normalized, '/');

        if ($normalized === '') {
            return $fallback;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($normalized)) {
            // Prefer standard storage URL (links to /storage via storage:link if available).
            return $disk->url($normalized);
        }

        // If the file exists directly in public folder, serve it directly.
        if (file_exists(public_path($normalized))) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'images/')) {
            return asset($normalized);
        }

        // Fallback to /media custom route for older paths.
        $encoded = implode('/', array_map('rawurlencode', explode('/', $normalized)));

        return '/media/' . $encoded;
    }
}
