<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
{
    /**
     * Display a listing of the notices.
     */
    public function index(Request $request)
    {
        $query = Notice::with('creator');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('semester') && $request->semester) {
            $query->forSemester($request->semester);
        }

        if ($request->has('audience') && $request->audience) {
            $query->forAudience($request->audience);
        }

        // Filter by published BS date if provided
        if ($request->has('date_bs') && $request->date_bs) {
            $query->where('published_at_bs', $request->date_bs);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Get notices ordered by important first, then by BS date
        $notices = $query->with('subject') // Eager load subject relationship for course display
                        ->orderBy('is_important', 'desc')
                        ->orderBy('published_at_bs', 'desc')
                        ->paginate(10);

        // Get statistics
        $stats = [
            'total' => Notice::count(),
            'published' => Notice::published()->count(),
            'draft' => Notice::draft()->count(),
            'scheduled' => Notice::scheduled()->count(),
        ];

        return view('admin.notice-board', compact('notices', 'stats'));
    }

    /**
     * Store a newly created notice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'audience' => 'nullable|in:all,students,faculty,parents,teacher',
            'status' => 'required|in:draft,published,scheduled',
            'semester' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'is_important' => 'nullable|boolean',
            'published_at_bs' => 'nullable|string|max:50',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            $notice = new Notice();
            $notice->title = $validated['title'];
            $notice->message = $validated['message'];
            // normalize audience aliases (accept 'teacher' as alias for 'faculty')
            $aud = $validated['audience'] ?? 'all';
            if ($aud === 'teacher') {
                $aud = 'faculty';
            }
            $notice->audience = $aud;
            $notice->status = $validated['status'];
            $notice->semester = $validated['semester'] ?? null;
            $notice->subject_id = $validated['subject_id'] ?? null;
            $notice->is_important = $validated['is_important'] ?? false;
            $notice->created_by = Auth::id();
            
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Use configured disk (local) instead of non-existent 'private' disk
                $filePath = $file->storeAs('notice-attachments', $fileName, 'local');
                $notice->file_name = $file->getClientOriginalName();
                $notice->file_path = $filePath;
            }
            
            // Save BS published date if provided
            if (isset($validated['published_at_bs'])) {
                $notice->published_at_bs = $validated['published_at_bs'];
            }
            
            $notice->save();

            DB::commit();

            return redirect()->route('admin.notice-board')
                ->with('success', 'Notice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create notice. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update the specified notice in storage.
     */
    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'audience' => 'nullable|in:all,students,faculty,parents,teacher',
            'status' => 'required|in:draft,published,scheduled',
            'semester' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'is_important' => 'nullable|boolean',
            'published_at_bs' => 'nullable|string|max:50',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            $notice->title = $validated['title'];
            $notice->message = $validated['message'];
            // normalize audience aliases when updating as well
            $aud = $validated['audience'] ?? 'all';
            if ($aud === 'teacher') {
                $aud = 'faculty';
            }
            $notice->audience = $aud;
            $notice->status = $validated['status'];
            $notice->semester = $validated['semester'] ?? null;
            $notice->subject_id = $validated['subject_id'] ?? null;
            $notice->is_important = $validated['is_important'] ?? false;
            
            // Handle file upload/update
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notice-attachments', $fileName, 'local');
                $notice->file_name = $file->getClientOriginalName();
                $notice->file_path = $filePath;
            }
            
            // Save BS published date if provided
            if (isset($validated['published_at_bs'])) {
                $notice->published_at_bs = $validated['published_at_bs'];
            }
            
            $notice->save();

            DB::commit();

            return redirect()->route('admin.notice-board')
                ->with('success', 'Notice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update notice. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified notice from storage.
     */
    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);

        try {
            $notice->delete();
            return redirect()->route('admin.notice-board')
                ->with('success', 'Notice deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete notice. Please try again.');
        }
    }

    /**
     * Toggle the status of a notice.
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notices,id',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        $notice = Notice::findOrFail($request->id);
        $notice->status = $request->status;
        

        
        $notice->save();

        return response()->json([
            'success' => true,
            'message' => 'Notice status updated successfully.',
            'status' => $request->status,
            'status_badge_class' => $notice->status_badge_class,
        ]);
    }

    /**
     * Get a single notice for viewing.
     */
    public function show($id)
    {
        $notice = Notice::with('creator', 'subject')->findOrFail($id);
        
        return response()->json([
            'notice' => $notice,
            'status_badge_class' => $notice->status_badge_class,
            'audience_text' => $notice->audience_text,
            'formatted_date' => $notice->formatted_date,
        ]);
    }

    /**
     * Get subjects by semester for dropdown selection.
     * Uses the Course model which references the same subjects table.
     */
    public function getSubjectsBySemester(Request $request)
    {
        $semester = $request->get('semester');
        
        // Build query to fetch from subjects table
        $query = DB::table('subjects')
            ->select('id', 'subject_name', 'subject_code', 'semester')
            ->where('status', 'active');
        
        // If no semester provided, return all subjects (allow creating notices without semester)
        if (!$semester) {
            $subjects = $query->orderBy('subject_name')->get();
            return response()->json($subjects);
        }

        // Build candidate semester representations to match stored formats
        $map = [
            'first' => '1', 'second' => '2', 'third' => '3', 'fourth' => '4', 'fifth' => '5', 'sixth' => '6',
            '1st' => '1', '2nd' => '2', '3rd' => '3', '4th' => '4', '5th' => '5', '6th' => '6',
        ];

        $candidates = [];
        $candidates[] = $semester;
        $candidates[] = ucfirst($semester);
        $candidates[] = strtoupper($semester);
        $lower = strtolower($semester);
        if (isset($map[$lower])) {
            $candidates[] = $map[$lower];
        }

        $candidates = array_values(array_unique(array_filter($candidates)));

        $subjects = $query->where(function($q) use ($candidates) {
                foreach ($candidates as $c) {
                    $q->orWhere('semester', $c);
                }
            })
            ->orderBy('subject_name')
            ->get();

        return response()->json($subjects);
    }
}

