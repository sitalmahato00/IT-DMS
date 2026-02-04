<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = request('q');
        $semester = request('semester');
        $status = request('status');
        $alumni = request('alumni');
        $perPage = intval(request('per_page', 10)) ?: 10;

        $builder = User::where('role', 'student');
        
        // Filter by user role
        if ($user->role === 'teacher' && $user->semester) {
            // Teachers see only students from their semester
            $builder->whereHas('student', function($q) use ($user) {
                $q->where('semester', $user->semester);
            });
        } elseif ($user->role === 'parent') {
            // Parents see only their children
            $builder->where('parent_id', $user->id);
        }
        // Admin sees all students (no additional filter)
        
        $batchYear = request('batch_year');

        $builder->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($semester, function($q) use ($semester) {
                $q->whereHas('student', function($s) use ($semester) {
                    $s->where('semester', $semester);
                });
            })
            ->when($batchYear, function($q) use ($batchYear) {
                $q->whereHas('student', function($s) use ($batchYear) {
                    $s->where('batch_year', $batchYear);
                });
            })
            ->when($status && $status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($alumni === '1', function($q) {
                $q->where('is_alumni', 1);
            })
            ->with('student')
            ->orderBy('created_at', 'desc');

        // Get distinct batch years for filter dropdown
        $batchYears = \App\Models\Student::whereNotNull('batch_year')->where('batch_year', '<>', '')->distinct()->orderBy('batch_year', 'desc')->pluck('batch_year');

        // Export as CSV if requested
        if (request('export') === 'csv') {
            $rows = $builder->get();
            $filename = 'students_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $columns = ['id','name','email','roll_no','semester','department','date_of_birth_bs','batch_year','address','status','is_alumni'];

            $callback = function() use ($rows, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($rows as $r) {
                    $line = [
                        $r->id,
                        $r->name,
                        $r->email,
                        $r->student->roll_no ?? '',
                        $r->student->semester ?? '',
                        $r->department ?? '',
                        $r->student->date_of_birth_bs ?? '',
                        $r->student->batch_year ?? '',
                        $r->student->address ?? '',
                        $r->status ?? '',
                        $r->is_alumni ?? 0,
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $students = $builder->paginate($perPage)->withQueryString();

        return view('admin.students', compact('students','batchYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'department' => 'required|string|max:100',
            // semester prefers numeric values 1-6 from the select
            'semester' => ['required','in:1,2,3,4,5,6'],
            'student_id' => 'required|string|max:50|unique:students,roll_no',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'batch_year' => 'required|string|max:10',
        ]);

        $password = Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'bio' => $data['bio'] ?? null,
            'role' => 'student',
            'status' => 'active',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        $student = Student::create([
            'user_id' => $user->id,
            'roll_no' => $data['student_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'department' => $data['department'] ?? null,
            'parent_id' => null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? null,
            'batch_year' => $data['batch_year'] ?? null,
        ]);

        // Defensive: ensure semester saved (some environments might cast/ignore values)
        if (isset($data['semester'])) {
            $student->semester = $data['semester'];
            $student->save();
        }

        return redirect()->route('admin.students')->with('success', 'Student added');
    }

    public function show($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        // If request is AJAX, return a lightweight modal partial so the list page can inject it
        if (request()->ajax() || request()->wantsJson()) {
            return view('admin.partials.student-modal', compact('student'));
        }

        return view('admin.student-show', compact('student'));
    }

    public function edit($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        return view('admin.student-edit', compact('student'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.students')->with('success', 'Student removed');
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => $user->status,
                'message' => 'Student status updated.'
            ]);
        }

        return redirect()->back()->with('success', 'Student status updated.');
    }

    // Toggle alumni flag
    public function toggleAlumni($id)
    {
        $user = User::findOrFail($id);
        $user->is_alumni = $user->is_alumni ? 0 : 1;
        $user->save();
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'is_alumni' => $user->is_alumni,
                'message' => 'Alumni status updated.'
            ]);
        }

        return redirect()->back()->with('success', 'Alumni status updated.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $student = $user->student;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => ['required','string','max:30', Rule::unique('users','phone')->ignore($user->id)],
            'department' => 'required|string|max:100',
            'semester' => ['required','in:1,2,3,4,5,6'],
            'student_id' => [
                'required','string','max:50',
                $student ? Rule::unique('students','roll_no')->ignore($student->id) : Rule::unique('students','roll_no')
            ],
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'date_of_birth_bs' => 'required|string|max:30',
            'address' => 'required|string',
            'batch_year' => 'required|string|max:10',
        ]);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->department = $data['department'] ?? $user->department;
        $user->bio = $data['bio'] ?? $user->bio;
        if (!empty($data['status'])) $user->status = $data['status'];
        // Ensure role remains 'student' regardless of incoming data
        $user->role = 'student';

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        // Update or create student record
        $student = $user->student;
        if (!$student) {
            $student = Student::create([
                'user_id' => $user->id,
                'roll_no' => $data['student_id'] ?? null,
                'semester' => $data['semester'] ?? null,
                'department' => $data['department'] ?? null,
                'date_of_birth_bs' => $data['date_of_birth_bs'] ?? null,
                'address' => $data['address'] ?? null,
                'batch_year' => $data['batch_year'] ?? null,
            ]);
        } else {
            $student->roll_no = $data['student_id'] ?? $student->roll_no;
            $student->semester = $data['semester'] ?? $student->semester;
            $student->department = $data['department'] ?? $student->department;
            $student->date_of_birth_bs = $data['date_of_birth_bs'] ?? $student->date_of_birth_bs;
            $student->address = $data['address'] ?? $student->address;
            $student->batch_year = $data['batch_year'] ?? $student->batch_year;
            $student->save();
        }

        return redirect()->route('admin.students.show', $user->id)->with('success', 'Student updated');
    }

    // Return a printable HTML view (no layout chrome)
    public function print($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        return view('admin.partials.student-print', compact('student'));
    }
}
