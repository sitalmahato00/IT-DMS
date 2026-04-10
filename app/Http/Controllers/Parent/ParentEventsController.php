<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ParentEventsController extends Controller
{
    /**
     * Display events and gallery items.
     */
    public function index()
    {
        $parent = Auth::user();
        
        // Get recent events/gallery items visible to parents
        // Filter by status and sort by date
        $events = Gallery::where('status', 'published')
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('parent.events.index', compact('events'));
    }

    /**
     * Display event details.
     */
    public function show($id)
    {
        $event = Gallery::findOrFail($id);

        // Verify event is published
        if ($event->status !== 'published') {
            abort(403, 'Unauthorized action.');
        }

        return view('parent.events.show', compact('event'));
    }
}

