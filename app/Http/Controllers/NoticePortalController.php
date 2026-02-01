<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Gallery;
use Illuminate\Http\Request;

class NoticePortalController extends Controller
{
    /**
     * Display notices and gallery for the public portal.
     */
    public function index(Request $request)
    {
        // Fetch notices
        $audience = $request->get('audience', 'all');
        
        $noticeQuery = Notice::published()
            ->with('creator', 'subject')
            ->orderBy('is_important', 'desc')
            ->orderBy('published_at_bs', 'desc')
            ->orderBy('created_at', 'desc');
        
        if ($audience && $audience !== 'all') {
            if ($audience === 'teacher') {
                $audience = 'faculty';
            }
            $noticeQuery->where(function($q) use ($audience) {
                $q->where('audience', $audience)
                  ->orWhere('audience', 'all');
            });
        }
        
        $notices = $noticeQuery->paginate(6, ['*'], 'notice_page', 1);
        
        $noticeCounts = [
            'all' => Notice::published()->count(),
            'students' => Notice::published()->forAudience('students')->count(),
            'faculty' => Notice::published()->forAudience('faculty')->count(),
            'parents' => Notice::published()->forAudience('parents')->count(),
        ];

        // Fetch gallery items
        $galleryCategory = $request->get('gallery_category', 'all');
        
        $galleryQuery = Gallery::active()
            ->ordered();
        
        if ($galleryCategory && $galleryCategory !== 'all') {
            $galleryQuery->where('category', $galleryCategory);
        }
        
        $galleryItems = $galleryQuery->get();
        
        $galleryCounts = [
            'all' => Gallery::active()->count(),
            'campus' => Gallery::active()->byCategory('campus')->count(),
            'events' => Gallery::active()->byCategory('events')->count(),
            'activities' => Gallery::active()->byCategory('activities')->count(),
            'students' => Gallery::active()->byCategory('students')->count(),
            'faculty' => Gallery::active()->byCategory('faculty')->count(),
            'facilities' => Gallery::active()->byCategory('facilities')->count(),
        ];
        
        return view('welcome', compact(
            'notices', 'audience', 'noticeCounts',
            'galleryItems', 'galleryCategory', 'galleryCounts'
        ));
    }
    
    /**
     * Get notices via AJAX for filtering on the landing page.
     */
    public function fetch(Request $request)
    {
        $audience = $request->get('audience', 'all');
        $page = $request->get('page', 1);
        
        $query = Notice::published()
            ->with('creator', 'subject')
            ->orderBy('is_important', 'desc')
            ->orderBy('published_at_bs', 'desc')
            ->orderBy('created_at', 'desc');
        
        if ($audience && $audience !== 'all') {
            if ($audience === 'teacher') {
                $audience = 'faculty';
            }
            $query->where(function($q) use ($audience) {
                $q->where('audience', $audience)
                  ->orWhere('audience', 'all');
            });
        }
        
        $notices = $query->paginate(6, ['*'], 'page', $page);
        
        return response()->json([
            'notices' => $notices->items(),
            'current_page' => $notices->currentPage(),
            'last_page' => $notices->lastPage(),
            'has_more' => $notices->hasMorePages(),
        ]);
    }
}

