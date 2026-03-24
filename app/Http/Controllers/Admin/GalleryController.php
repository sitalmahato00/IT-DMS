<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class GalleryController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the gallery items.
     */
    public function index(Request $request)
    {
        $query = Gallery::query();

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === '1');
        }

        $galleries = $query->orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(12);

        // Get statistics
        $stats = [
            'total' => Gallery::count(),
            'active' => Gallery::where('is_active', true)->count(),
            'inactive' => Gallery::where('is_active', false)->count(),
            'by_category' => [
                'campus' => Gallery::where('category', 'campus')->count(),
                'events' => Gallery::where('category', 'events')->count(),
                'activities' => Gallery::where('category', 'activities')->count(),
                'students' => Gallery::where('category', 'students')->count(),
                'faculty' => Gallery::where('category', 'faculty')->count(),
                'facilities' => Gallery::where('category', 'facilities')->count(),
            ],
        ];

        return view('admin.gallery', compact('galleries', 'stats'));
    }

    /**
     * Store a newly created gallery item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|max:10240|mimes:jpeg,jpg,png,gif,webp',
            'category' => 'required|in:campus,events,activities,students,faculty,facilities',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $uploadedCount = 0;
            $files = $request->file('images', []);

            foreach ($files as $index => $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('gallery', $fileName, 'public');

                $gallery = new Gallery();
                $gallery->title = count($files) > 1
                    ? $validated['title'] . ' (' . ($index + 1) . ')'
                    : $validated['title'];
                $gallery->description = $validated['description'] ?? null;
                $gallery->image_path = $filePath;
                $gallery->image_name = $file->getClientOriginalName();
                $gallery->category = $validated['category'];
                $gallery->order = ($validated['order'] ?? 0) + $index;
                $gallery->is_active = $validated['is_active'] ?? true;
                $gallery->save();

                $uploadedCount++;
            }

            // Log activity
            $this->logActivity('Gallery', 'Uploaded Image', "Gallery item '{$validated['title']}' uploaded ({$uploadedCount} image(s)) - Category: {$validated['category']}");

            return redirect()->route('admin.gallery')
                ->with('success', $uploadedCount . ' gallery image(s) added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add gallery item. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update the specified gallery item in storage.
     */
    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240|mimes:jpeg,jpg,png,gif,webp',
            'category' => 'required|in:campus,events,activities,students,faculty,facilities',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }

                $file = $request->file('image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('gallery', $fileName, 'public');
                $gallery->image_path = $filePath;
                $gallery->image_name = $file->getClientOriginalName();
            }

            $gallery->title = $validated['title'];
            $gallery->description = $validated['description'] ?? null;
            $gallery->category = $validated['category'];
            $gallery->order = $validated['order'] ?? 0;
            $gallery->is_active = $validated['is_active'] ?? true;
            $gallery->save();

            return redirect()->route('admin.gallery')
                ->with('success', 'Gallery item updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update gallery item. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified gallery item from storage.
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);

        try {
            $galleryTitle = $gallery->title;
            
            // Delete image file if exists
            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }

            $gallery->delete();

            // Log activity
            $this->logActivity('Gallery', 'Deleted Image', "Gallery item '{$galleryTitle}' was deleted");

            return redirect()->route('admin.gallery')
                ->with('success', 'Gallery item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete gallery item. Please try again.');
        }
    }

    /**
     * Toggle the active status of a gallery item.
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:galleries,id',
            'status' => 'required|boolean',
        ]);

        $gallery = Gallery::findOrFail($request->id);
        $gallery->is_active = $request->status;
        $gallery->save();

        return response()->json([
            'success' => true,
            'message' => 'Gallery item status updated successfully.',
        ]);
    }

    /**
     * Get categories for dropdown.
     */
    public function getCategories()
    {
        return response()->json([
            'campus' => 'Campus',
            'events' => 'Events',
            'activities' => 'Activities',
            'students' => 'Students',
            'faculty' => 'Faculty',
            'facilities' => 'Facilities',
        ]);
    }

    /**
     * Display the specified gallery item (for editing modal).
     */
    public function show($id)
    {
        $gallery = Gallery::findOrFail($id);

        return response()->json([
            'gallery' => [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'description' => $gallery->description,
                'category' => $gallery->category,
                'image_url' => $gallery->image_url,
                'image_path' => $gallery->image_path,
                'order' => $gallery->order,
                'is_active' => $gallery->is_active,
                'created_at' => $gallery->created_at,
            ]
        ]);
    }
}
