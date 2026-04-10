<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Support\SafeCache;
use Illuminate\Http\Request;

class GalleryPortalController extends Controller
{
    /**
     * Display gallery for the public portal.
     */
    public function index(Request $request)
    {
        $category = (string) $request->get('category', 'all');
        $ttl = (int) config('performance.public_data_cache_ttl', 300);

        $galleryItems = SafeCache::remember("gallery:index:{$category}:v1", $ttl, function () use ($category) {
            $query = Gallery::active()->ordered();

            if ($category !== '' && $category !== 'all') {
                $query->where('category', $category);
            }

            return $query->get();
        });

        $counts = SafeCache::remember('gallery:counts:v1', $ttl, function () {
            $rawCounts = Gallery::active()
                ->selectRaw('category, COUNT(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category');

            return [
                'all' => (int) $rawCounts->sum(),
                'campus' => (int) ($rawCounts['campus'] ?? 0),
                'events' => (int) ($rawCounts['events'] ?? 0),
                'activities' => (int) ($rawCounts['activities'] ?? 0),
                'students' => (int) ($rawCounts['students'] ?? 0),
                'faculty' => (int) ($rawCounts['faculty'] ?? 0),
                'facilities' => (int) ($rawCounts['facilities'] ?? 0),
            ];
        });
        
        return view('gallery', compact('galleryItems', 'category', 'counts'));
    }
    
    /**
     * Get gallery items via AJAX for filtering.
     */
    public function fetch(Request $request)
    {
        $category = (string) $request->get('category', 'all');
        $ttl = (int) config('performance.public_data_cache_ttl', 300);

        $payload = SafeCache::remember("gallery:fetch:{$category}:v1", $ttl, function () use ($category) {
            $query = Gallery::active()->ordered();

            if ($category !== '' && $category !== 'all') {
                $query->where('category', $category);
            }

            return [
                'items' => $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'image_url' => $item->image_url,
                        'category' => $item->category,
                        'category_text' => $item->category_text,
                    ];
                })->values()->all(),
            ];
        });

        return response()->json($payload);
    }
}

