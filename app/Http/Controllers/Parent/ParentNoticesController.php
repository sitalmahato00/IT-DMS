<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParentNoticesController extends Controller
{
    /**
     * Display all notices for parents.
     */
    public function index(Request $request)
    {
        $parent = Auth::user();
        
        // Get notices for parents
        $query = Notice::where('audience', 'parent')
            ->where('status', 'published');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_ne', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('message_ne', 'like', "%{$search}%");
            });
        }

        // Filter by importance
        if ($request->filled('importance')) {
            if ($request->importance === 'important') {
                $query->where('is_important', true);
            } elseif ($request->importance === 'normal') {
                $query->where('is_important', false);
            }
        }

        $notices = $query->orderBy('published_at', 'desc')
            ->paginate(20);

        // Get unread count
        $unreadCount = Notice::where('audience', 'parent')
            ->where('status', 'published')
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        return view('parent.notices.index', compact('notices', 'unreadCount'));
    }

    /**
     * Display a specific notice.
     */
    public function show($id)
    {
        $notice = Notice::findOrFail($id);

        // Verify it's a parent notice
        if ($notice->audience !== 'parent') {
            abort(403, 'Unauthorized action.');
        }

        return view('parent.notices.show', compact('notice'));
    }

    /**
     * Mark notice as read (via AJAX).
     */
    public function markAsRead($id)
    {
        $notice = Notice::findOrFail($id);

        if ($notice->audience !== 'parent') {
            abort(403, 'Unauthorized action.');
        }

        // You could implement a read_by table if needed for tracking
        // For now, this endpoint exists for future use

        return response()->json(['success' => true]);
    }
}

