<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Gallery;
use App\Models\Subject;
use App\Models\Department;
use App\Support\SafeCache;
use Illuminate\Http\Request;

class NoticePortalController extends Controller
{
    public function publicIndex(Request $request)
    {
        $locale = app()->getLocale();
        $audience = (string) $request->get('audience', 'all');
        $query = trim((string) $request->get('q', ''));

        $noticeQuery = Notice::published()
            ->with('creator', 'subject')
            ->orderBy('is_important', 'desc')
            ->orderBy('published_at_bs', 'desc')
            ->orderBy('created_at', 'desc');

        if ($audience !== '' && $audience !== 'all') {
            $noticeQuery->forAudience($audience);
        }

        if ($query !== '') {
            $noticeQuery->where(function ($builder) use ($query) {
                $builder->where('title', 'like', '%' . $query . '%')
                    ->orWhere('title_ne', 'like', '%' . $query . '%')
                    ->orWhere('message', 'like', '%' . $query . '%')
                    ->orWhere('message_ne', 'like', '%' . $query . '%');
            });
        }

        $notices = $noticeQuery->paginate(9)->withQueryString();

        $audienceOptions = [
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'students' => $locale === 'ne' ? 'विद्यार्थी' : 'Students',
            'faculty' => $locale === 'ne' ? 'शिक्षक' : 'Faculty',
            'parents' => $locale === 'ne' ? 'अभिभावक' : 'Parents',
        ];

        return view('notices.index', compact('notices', 'audience', 'query', 'audienceOptions'));
    }

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
        
        // Fetch latest study materials (visible to students only on landing page)
        $materials = \App\Models\StudyMaterial::with('subject')
            ->whereIn('visibility', ['all', 'students'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        
        $materialCounts = [
            'all' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->count(),
            'lecture_notes' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'lecture_notes')->count(),
            'assignment' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'assignment')->count(),
            'assessment' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'assessment')->count(),
            'lab_report' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'lab_report')->count(),
            'study_guide' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'study_guide')->count(),
            'syllabus' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'syllabus')->count(),
            'project_material' => \App\Models\StudyMaterial::whereIn('visibility', ['all', 'students'])->where('document_type', 'project_material')->count(),
        ];
        
        // Fetch subjects for filter dropdown
        $subjects = Subject::active()
            ->orderBy('subject_name')
            ->get();
            
        // Fetch departments for the highlights section
        $departments = Department::all();
        
        return view('welcome', compact(
            'notices', 'audience', 'noticeCounts',
            'galleryItems', 'galleryCategory', 'galleryCounts',
            'materials', 'materialCounts',
            'subjects', 'departments'
        ));
    }
    
    /**
     * Get notices via AJAX for filtering on the landing page.
     */
    public function fetch(Request $request)
    {
        $audience = (string) $request->get('audience', 'all');
        $page = max(1, (int) $request->get('page', 1));
        $ttl = (int) config('performance.public_data_cache_ttl', 300);
        $cacheAudience = $audience === 'teacher' ? 'faculty' : $audience;

        $payload = SafeCache::remember("notices:fetch:{$cacheAudience}:page:{$page}:v1", $ttl, function () use ($cacheAudience, $page) {
            $query = Notice::published()
                ->with('creator', 'subject')
                ->orderBy('is_important', 'desc')
                ->orderBy('published_at_bs', 'desc')
                ->orderBy('created_at', 'desc');

            if ($cacheAudience !== '' && $cacheAudience !== 'all') {
                $query->where(function ($q) use ($cacheAudience) {
                    $q->where('audience', $cacheAudience)
                        ->orWhere('audience', 'all');
                });
            }

            $notices = $query->paginate(6, ['*'], 'page', $page);

            return [
                'notices' => $notices->items(),
                'current_page' => $notices->currentPage(),
                'last_page' => $notices->lastPage(),
                'has_more' => $notices->hasMorePages(),
            ];
        });

        return response()->json($payload);
    }
    
    /**
     * Get a single notice for the public modal.
     */
    public function show($id)
    {
        $ttl = (int) config('performance.public_data_cache_ttl', 300);

        $payload = SafeCache::remember("notices:show:{$id}:v1", $ttl, function () use ($id) {
            $notice = Notice::with('creator', 'subject')->findOrFail($id);

            return [
                'notice' => $notice,
                'audience_text' => $notice->audience_text,
                'formatted_date' => $notice->formatted_date,
            ];
        });

        return response()->json($payload);
    }
}
