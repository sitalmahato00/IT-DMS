<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q');
        $status = $request->get('status');
        $department = $request->get('department');
        $perPage = intval($request->get('per_page', 10)) ?: 10;

        $builder = User::where('role', 'teacher');
        
        // Filter by user role
        if ($user->role === 'teacher') {
            // Teachers see only themselves
            $builder->where('id', $user->id);
        } elseif ($user->role === 'parent') {
            // Parents see teachers of their children
            $studentIds = User::where('parent_id', $user->id)->where('role', 'student')->pluck('id');
            if ($studentIds->isNotEmpty()) {
                $builder->whereHas('student', function($q) use ($studentIds) {
                    $q->whereIn('user_id', $studentIds);
                });
            } else {
                $builder->whereRaw('1=0'); // No teachers for parent with no children
            }
        }
        // Admin sees all teachers (no additional filter)
        
        $builder->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($department, function($q) use ($department) {
                $q->where('department', $department);
            })
            ->orderBy('created_at', 'desc');

        $teachers = $builder->paginate($perPage)->withQueryString();

        // Attach teacher_code from teachers table so view/JSON can show it
        try {
            $teachers->getCollection()->transform(function($t){
                $t->teacher_code = \Illuminate\Support\Facades\DB::table('teachers')->where('user_id', $t->id)->value('teacher_code');
                return $t;
            });
        } catch (\Throwable $e) {
            // ignore if table missing
        }

        return view('admin.teachers', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
            'teacher_id' => 'nullable|string|max:50|unique:teachers,teacher_code',
            'department' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Str::random(16),
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'bio' => $data['bio'] ?? null,
            // Force role to 'teacher' for records created from this controller
            'role' => 'teacher',
            'status' => $data['status'] ?? 'active',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Maintain separate teachers table record (avoid requiring users.teacher_id column)
        try {
            \Illuminate\Support\Facades\DB::table('teachers')->insert([
                'user_id' => $user->id,
                'teacher_code' => $data['teacher_id'] ?? null,
                'qualification' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // ignore failures here (e.g., table not migrated); user record is still created
        }

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'teacher' => $user
            ]);
        }

        return redirect()->route('admin.teachers')->with('success', 'Teacher created');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $teacher = \App\Models\User::where('role','teacher')->findOrFail($id);
        
        // Check authorization
        // Admin can view all teachers
        // Teacher can view themselves
        if ($user->role === 'teacher' && $user->id !== $teacher->id) {
            return response()->json(['error' => 'You can only view your own profile'], 403);
        }
        
        // include teacher_code from teachers table for AJAX consumers
        try {
            $teacher->teacher_code = \Illuminate\Support\Facades\DB::table('teachers')->where('user_id', $teacher->id)->value('teacher_code');
        } catch (\Throwable $e) {
            $teacher->teacher_code = null;
        }
        
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $teacher = \App\Models\User::where('role','teacher')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$teacher->id,
            'teacher_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'department' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
        ]);

        $teacher->name = $data['name'];
        $teacher->email = $data['email'];
        // Do not write teacher_id to users table (store in teachers table instead)
        $teacher->phone = $data['phone'] ?? $teacher->phone;
        $teacher->department = $data['department'] ?? $teacher->department;
        $teacher->bio = $data['bio'] ?? $teacher->bio;
        if (!empty($data['status'])) $teacher->status = $data['status'];
        // Ensure role remains 'teacher' for this controller
        $teacher->role = 'teacher';

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $teacher->profile_photo_path = $path;
        }

        $teacher->save();

        // Update teachers table record if exists
        try {
            \Illuminate\Support\Facades\DB::table('teachers')
                ->where('user_id', $teacher->id)
                ->update([
                    'teacher_code' => $data['teacher_id'] ?? null,
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $e) {
            // ignore if table missing
        }

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Teacher updated successfully',
                'teacher' => $teacher
            ]);
        }

        return redirect()->route('admin.teachers')->with('success', 'Teacher updated');
    }

    public function destroy($id)
    {
        $teacher = \App\Models\User::where('role','teacher')->findOrFail($id);
        $teacher->delete();
        return redirect()->route('admin.teachers')->with('success', 'Teacher removed');
    }
}
