<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryPortalController extends Controller
{
    /**
     * Display gallery for the public portal.
     */
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        
        $query = Gallery::active()
            ->ordered();
        
        // Filter by category
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }
        
        $galleryItems = $query->get();
        
        // Get category counts
        $counts = [
            'all' => Gallery::active()->count(),
            'campus' => Gallery::active()->byCategory('campus')->count(),
            'events' => Gallery::active()->byCategory('events')->count(),
            'activities' => Gallery::active()->byCategory('activities')->count(),
            'students' => Gallery::active()->byCategory('students')->count(),
            'faculty' => Gallery::active()->byCategory('faculty')->count(),
            'facilities' => Gallery::active()->byCategory('facilities')->count(),
        ];
        
        return view('welcome', compact('galleryItems', 'category', 'counts'));
    }
    
    /**
     * Get gallery items via AJAX for filtering.
     */
    public function fetch(Request $request)
    {
        $category = $request->get('category', 'all');
        
        $query = Gallery::active()
            ->ordered();
        
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }
        
        $items = $query->get();
        
        return response()->json([
            'items' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image_url' => $item->image_url,
                    'category' => $item->category,
                    'category_text' => $item->category_text,
                ];
            })->toArray(),
        ]);
    }
}

