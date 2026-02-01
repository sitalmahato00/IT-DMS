<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q');
        $status = $request->get('status');
        $relationship = $request->get('relationship');
        $perPage = intval($request->get('per_page', 10)) ?: 10;

        $builder = User::where('role', 'parent');
        
        // Filter by user role
        if ($user->role === 'parent') {
            // Parents see only themselves
            $builder->where('id', $user->id);
        } elseif ($user->role === 'teacher' && $user->semester) {
            // Teachers see parents of their students (students in their semester)
            $studentIds = User::where('role', 'student')
                ->whereHas('student', function($q) use ($user) {
                    $q->where('semester', $user->semester);
                })
                ->pluck('id');
            
            if ($studentIds->isNotEmpty()) {
                $builder->whereIn('id', 
                    User::where('role', 'student')
                        ->whereIn('id', $studentIds)
                        ->whereNotNull('parent_id')
                        ->distinct('parent_id')
                        ->pluck('parent_id')
                );
            } else {
                $builder->whereRaw('1=0'); // No parents for students
            }
        }
        // Admin sees all parents (no additional filter)
        
        // Apply search filter (ensure we're still only searching parents)
        $builder->when($query, function($q) use ($query) {
                $q->where(function($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                             ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($relationship, function($q) use ($relationship) {
                $q->where('department', $relationship);
            })
            ->orderBy('created_at', 'desc');

        $parents = $builder->paginate($perPage)->withQueryString();

        // Attach children count so view can display it
        $parents->getCollection()->transform(function($p){
            $p->children_count = User::where('parent_id', $p->id)->where('role', 'student')->count();
            $p->children = User::where('parent_id', $p->id)->where('role', 'student')->get();
            return $p;
        });

        return view('admin.parents', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
            'relationship' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'children' => 'nullable|array',
            'children.*' => 'integer|exists:users,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Str::random(16),
            'phone' => $data['phone'] ?? null,
            'department' => $data['relationship'] ?? null,
            'bio' => $data['bio'] ?? null,
            'role' => 'parent',
            'status' => $data['status'] ?? 'active',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Assign children
        if (!empty($data['children'])) {
            $childrenIds = array_filter($data['children'], fn($id) => !empty($id));
            if (!empty($childrenIds)) {
                User::whereIn('id', $childrenIds)->where('role', 'student')->update(['parent_id' => $user->id]);
            }
        }

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Parent created successfully',
                'parent' => $user
            ]);
        }

        return redirect()->route('admin.parents')->with('success', 'Parent created successfully');
    }

    public function getStudents()
    {
        $user = auth()->user();
        
        $query = User::where('role', 'student');
        
        // Filter by user role
        if ($user->role === 'teacher' && $user->semester) {
            // Teachers see only students from their semester
            $query->whereHas('student', function($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        } elseif ($user->role === 'parent') {
            // Parents see only their children
            $query->where('parent_id', $user->id);
        }
        // Admin sees all students
        
        $students = $query->select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                ];
            });
        
        return response()->json($students);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $parent = User::where('role', 'parent')->findOrFail($id);
        
        // Check authorization
        if ($user->role === 'parent' && $user->id !== $parent->id) {
            abort(403, 'Unauthorized');
        }
        
        $assignedChildren = User::where('parent_id', $id)->where('role', 'student')->get(['id', 'name', 'email'])->toArray();
        
        // Get available students based on role
        $availableStudentsQuery = User::where('role', 'student');
        if ($user->role === 'teacher' && $user->semester) {
            $availableStudentsQuery->whereHas('student', function($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        }
        $availableStudents = $availableStudentsQuery->orderBy('name')->get();
        
        return response()->json([
            'id' => $parent->id,
            'name' => $parent->name,
            'email' => $parent->email,
            'phone' => $parent->phone,
            'department' => $parent->department,
            'bio' => $parent->bio,
            'status' => $parent->status,
            'profile_photo_path' => $parent->profile_photo_path,
            'assigned_children' => $assignedChildren,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function update(Request $request, $id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'relationship' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'children' => 'nullable|array',
            'children.*' => 'integer|exists:users,id',
        ]);

        $parent->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'department' => $data['relationship'] ?? null,
            'bio' => $data['bio'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $parent->profile_photo_path = $path;
            $parent->save();
        }

        // Update children assignments
        // First, remove this parent from all students
        User::where('parent_id', $parent->id)->update(['parent_id' => null]);
        
        // Then assign selected children
        if (!empty($data['children'])) {
            $childrenIds = array_filter($data['children'], fn($id) => !empty($id));
            if (!empty($childrenIds)) {
                User::whereIn('id', $childrenIds)->where('role', 'student')->update(['parent_id' => $parent->id]);
            }
        }

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Parent updated successfully',
                'parent' => $parent
            ]);
        }

        return redirect()->route('admin.parents')->with('success', 'Parent updated successfully');
    }

    public function destroy($id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);
        // Unassign all children before deleting parent
        User::where('parent_id', $parent->id)->update(['parent_id' => null]);
        $parent->delete();
        return redirect()->route('admin.parents')->with('success', 'Parent deleted successfully');
    }
}

