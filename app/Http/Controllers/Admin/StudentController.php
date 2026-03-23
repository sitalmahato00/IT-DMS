<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use App\Models\AuditLog;
use App\Notifications\StudentAccountNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Convert AD date to BS (Bikram Sambat) Nepali date
     */
    private function convertAdToBs($adDate)
    {
        if (!$adDate) return '';
        
        // Simple conversion: Add 56 years, 8 months, and 17 days (approximate)
        try {
            $date = new \DateTime($adDate);
            $date->modify('+56 years +8 months +17 days');
            return $date->format('Y');
        } catch (\Exception $e) {
            return '';
        }
    }

    public function __construct()
    {
        // Restrict sensitive actions to admin users only
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            // allow index/show for non-admins (teachers/parents)
            $adminOnly = ['export', 'bulk', 'toggle', 'toggleAlumni', 'destroy', 'store', 'update', 'alumni'];
            $action = $request->route()?->getActionMethod();
            if (in_array($action, $adminOnly)) {
                if (!$user || !in_array($user->role, ['admin', 'super-admin'])) {
                    abort(403);
                }
            }
            return $next($request);
        });
    }
    public function index()
    {
        $user = auth()->user();
        $query = request('q');
        $subjectId = request('subject');
        $semester = request('semester');
        $status = request('status');
        $alumni = request('alumni');
        $tab = request('tab', 'active');
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
            $builder->whereHas('student', function($q) use ($user) {
                $q->where('parent_id', $user->id);
            });
        }
        // Admin sees all students (no additional filter)
        
        $academicYearFilter = request('academic_year');

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
            ->when($academicYearFilter, function($q) use ($academicYearFilter) {
                $q->whereHas('student', function($s) use ($academicYearFilter) {
                    $s->where('academic_year_bs', $academicYearFilter);
                });
            })
            ->when($status && $status !== 'all', function($q) use ($status) {
                $q->whereHas('student', function($s) use ($status) {
                    $s->where('status', $status);
                });
            })
            // Respect the tab parameter: if viewing alumni tab, show alumni;
            // otherwise exclude alumni from active listing by default.
            ->when($tab === 'alumni', function($q) {
                $q->whereHas('student', function($s) {
                    $s->where('is_alumni', 1);
                });
            }, function($q) use ($alumni) {
                // If not on alumni tab and explicit alumni filter isn't set, exclude alumni
                if ($alumni !== '1') {
                    $q->whereHas('student', function($s) {
                        $s->where(function($sub) {
                            $sub->where('is_alumni', 0)->orWhereNull('is_alumni');
                        });
                    });
                }
            })
            ->with('student')
            ->orderBy('created_at', 'desc');

        // Get distinct academic years for filter dropdown (only BS)
        $academicYears = \App\Models\Student::selectRaw("DISTINCT academic_year_bs as year")
            ->whereNotNull('academic_year_bs')
            ->where('academic_year_bs', '<>', '')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Get active subjects for filter dropdown
        $subjects = \App\Models\Subject::active()
            ->select('id', \DB::raw('subject_name as name'))
            ->orderBy('semester', 'asc')
            ->orderBy('subject_name', 'asc')
            ->get();

        // Export as CSV if requested
        if (request('export') === 'csv') {
            $rows = $builder->get();
            $filename = 'students_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

                $columns = ['id','name','email','roll_no','semester','department','date_of_birth_bs','academic_year','academic_year_bs','address','status','is_alumni','academic_year_range','academic_year_bs_range'];

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
                        $r->student->department ?? '',
                        $r->student->date_of_birth_bs ?? '',
                        $r->student->academic_year ?? '',
                        $r->student->academic_year_bs ?? '',
                        $r->student->address ?? '',
                        $r->student->status ?? '',
                        $r->student->is_alumni ?? 0,
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $students = $builder->paginate($perPage)->withQueryString();

        return view('admin.students', compact('students','academicYears', 'subjects'));
    }

    /**
     * Show alumni students only
     */
    public function alumni()
    {
        $user = auth()->user();
        $query = request('q');
        $semester = request('semester');
        $status = request('status');
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
            $builder->whereHas('student', function($q) use ($user) {
                $q->where('parent_id', $user->id);
            });
        }
        // Admin sees all students (no additional filter)
        
        $academicYearFilter = request('academic_year');

        // Always show only alumni
        $builder->whereHas('student', function($q) {
            $q->where('is_alumni', 1);
        })
        ->when($query, function($q) use ($query) {
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
            ->when($academicYearFilter, function($q) use ($academicYearFilter) {
                $q->whereHas('student', function($s) use ($academicYearFilter) {
                    $s->where('academic_year_bs', $academicYearFilter);
                });
            })
            ->when($status && $status !== 'all', function($q) use ($status) {
                $q->whereHas('student', function($s) use ($status) {
                    $s->where('status', $status);
                });
            })
            ->with('student')
            ->orderBy('created_at', 'desc');

        // Get distinct academic years for filter dropdown (only BS)
        $academicYears = \App\Models\Student::selectRaw("DISTINCT academic_year_bs as year")
            ->whereNotNull('academic_year_bs')
            ->where('academic_year_bs', '<>', '')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Export as CSV if requested
        if (request('export') === 'csv') {
            $rows = $builder->get();
            $filename = 'alumni_students_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

                $columns = ['id','name','email','roll_no','semester','department','date_of_birth_bs','academic_year','academic_year_bs','address','status','academic_year_range','academic_year_bs_range'];

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
                        $r->student->department ?? '',
                        $r->student->date_of_birth_bs ?? '',
                        $r->student->academic_year ?? '',
                        $r->student->academic_year_bs ?? '',
                        $r->student->address ?? '',
                        $r->student->status ?? '',
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $students = $builder->paginate($perPage)->withQueryString();

        return view('admin.alumni-students', compact('students','academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10',
            'department' => 'required|string|max:100',
            // semester prefers numeric values 1-6 from the select
            'semester' => ['required','in:1,2,3,4,5,6'],
            'student_id' => 'required|string|max:50',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'date_of_birth' => 'required|date',
            'date_of_birth_bs' => 'nullable|string|max:20',
            'batch_year' => 'nullable|string|max:10',
            'address' => 'required|string',
            'academic_year' => 'required|string|max:10',
            'academic_year_bs' => 'nullable|string|max:10|regex:/^\d{4}$/',
            'gender' => 'nullable|string|max:20',
        ]);

        try {
            \DB::beginTransaction();
            // Auto-calculate academic_year_bs from academic_year if not provided
            if (empty($data['academic_year_bs']) && !empty($data['academic_year'])) {
                $data['academic_year_bs'] = $this->convertAdToBs($data['academic_year'] . '-01-01');
            }

            $password = Str::random(10);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => 'student',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'roll_no' => $data['student_id'] ?? null,
                'semester' => $data['semester'] ?? null,
                'parent_id' => null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'date_of_birth_bs' => $data['date_of_birth_bs'] ?? null,
                'batch_year' => $data['batch_year'] ?? null,
                'academic_year' => $data['academic_year'] ?? null,
                'academic_year_bs' => $data['academic_year_bs'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'department' => $data['department'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => 'active',
                'address' => $data['address'] ?? null,
            ]);

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profiles', 'public');
                $student->profile_photo_path = $path;
                $student->save();
            }

            // Defensive: ensure semester saved (some environments might cast/ignore values)
            if (isset($data['semester'])) {
                $student->semester = $data['semester'];
                $student->save();
            }

            \DB::commit();

            // Send notification with login credentials to the student
            $user->notify(new StudentAccountNotification($password, 'student'));

            // Log the activity
            AuditLog::create([
                'timestamp' => now(),
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
                'old_values' => null,
                'new_values' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'student_roll_no' => $student->roll_no,
                    'student_semester' => $student->semester,
                    'student_department' => $student->department,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('admin.students', ['page' => 1])->with('success', 'Student added successfully. Login credentials have been sent to the student\'s email.');
        } catch (\Exception $e) {
            \DB::rollBack();
            // Log the failed attempt
            AuditLog::create([
                'timestamp' => now(),
                'user_id' => Auth::id(),
                'action' => 'create_failed',
                'model_type' => 'App\Models\User',
                'model_id' => null,
                'old_values' => null,
                'new_values' => [
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'error' => $e->getMessage(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->back()->with('error', 'Failed to add student: ' . $e->getMessage())->withInput();
        }
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
        try {
            $user = User::findOrFail($id);

            // Delete related student record first to avoid FK constraint issues
            if ($user->student) {
                $user->student->delete();
            }

            $user->delete();

            return redirect()->route('admin.students')->with('success', 'Student removed');
        } catch (\Exception $e) {
            // Log and return error
            \Log::error('Failed to delete student user id ' . $id . ': ' . $e->getMessage());
            return redirect()->route('admin.students')->with('error', 'Failed to remove student: ' . $e->getMessage());
        }
    }

    /**
     * Move student to alumni status
     */
    public function moveToAlumni($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if (!$user->student) {
                return response()->json(['success' => false, 'message' => 'Student record not found'], 404);
            }

            $user->student->update([
                'is_alumni' => true,
                'alumni_from' => now(),
                'status' => 'inactive'
            ]);

            return response()->json(['success' => true, 'message' => 'Student moved to alumni successfully']);
        } catch (\Exception $e) {
            \Log::error('Failed to move student to alumni: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to move student to alumni'], 500);
        }
    }

    public function toggle($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Load student relationship
            $student = $user->student()->first();
            
            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found.');
            }
            
            // Toggle status
            $currentStatus = $student->status ?? 'active';
            $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';
            
            $student->status = $newStatus;
            $student->save();
            
            return redirect()->back()->with('success', 'Student status updated to ' . $newStatus . '.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    // Toggle alumni flag
    public function toggleAlumni($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Load student relationship
            $student = $user->student()->first();
            
            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found.');
            }
            
            // Toggle alumni status
            $currentStatus = $student->is_alumni ?? 0;
            $newStatus = $currentStatus ? 0 : 1;
            
            $student->is_alumni = $newStatus;
            $student->save();
            
            $message = $newStatus ? 'Student marked as alumni.' : 'Student removed from alumni.';
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating alumni status: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $student = $user->student;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'department' => 'required|string|max:100',
            'semester' => ['required','in:1,2,3,4,5,6'],
            'student_id' => 'required|string|max:50',
            'bio' => 'nullable|string',
            'status' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:4096',
            'date_of_birth' => 'required|date',
            'date_of_birth_bs' => 'nullable|string|max:20',
            'batch_year' => 'nullable|string|max:10',
            'address' => 'required|string',
            'academic_year' => 'required|string|max:10',
            'academic_year_bs' => 'nullable|string|max:10|regex:/^\d{4}$/',
            'gender' => 'nullable|string|max:20',
        ]);

        // Auto-calculate academic_year_bs from academic_year if not provided
        if (empty($data['academic_year_bs']) && !empty($data['academic_year'])) {
            $data['academic_year_bs'] = $this->convertAdToBs($data['academic_year'] . '-01-01');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = 'student';
        $user->save();

        // Update or create student record
        $student = $user->student;
        if (!$student) {
            $student = Student::create([
                'user_id' => $user->id,
                'roll_no' => $data['student_id'] ?? null,
                'semester' => $data['semester'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'date_of_birth_bs' => $data['date_of_birth_bs'] ?? null,
                'batch_year' => $data['batch_year'] ?? null,
                'academic_year' => $data['academic_year'] ?? null,
                'academic_year_bs' => $data['academic_year_bs'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'department' => $data['department'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
                'address' => $data['address'] ?? null,
            ]);
        } else {
            $student->roll_no = $data['student_id'] ?? $student->roll_no;
            $student->semester = $data['semester'] ?? $student->semester;
            $student->date_of_birth = $data['date_of_birth'] ?? $student->date_of_birth;
            $student->date_of_birth_bs = $data['date_of_birth_bs'] ?? $student->date_of_birth_bs;
            $student->batch_year = $data['batch_year'] ?? $student->batch_year;
            $student->academic_year = $data['academic_year'] ?? $student->academic_year;
            $student->academic_year_bs = $data['academic_year_bs'] ?? $student->academic_year_bs;
            $student->gender = $data['gender'] ?? $student->gender;
            $student->phone = $data['phone'] ?? $student->phone;
            $student->department = $data['department'] ?? $student->department;
            $student->bio = $data['bio'] ?? $student->bio;
            $student->address = $data['address'] ?? $student->address;
            if (!empty($data['status'])) $student->status = $data['status'];
            $student->save();
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $student->profile_photo_path = $path;
            $student->save();
        }

        return redirect()->route('admin.students')->with('success', 'Student updated successfully');
    }

    public function printList(Request $request)
    {
        $query = $request->input('q');
        $semester = $request->input('semester');
        $status = $request->input('status');
        $academicYear = $request->input('academic_year');
        $tab = $request->input('tab', 'active');
        $alumni = $request->input('alumni');

        $studentsQuery = User::where('role', 'student')
            ->with('student')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($semester, function ($q) use ($semester) {
                $q->whereHas('student', function ($s) use ($semester) {
                    $s->where('semester', $semester);
                });
            })
            ->when($academicYear, function ($q) use ($academicYear) {
                $q->whereHas('student', function ($s) use ($academicYear) {
                    $s->where(function ($sub) use ($academicYear) {
                        $sub->where('academic_year', $academicYear)
                            ->orWhere('academic_year_bs', $academicYear);
                    });
                });
            })
            ->when($status && $status !== 'all', function ($q) use ($status) {
                $q->whereHas('student', function ($s) use ($status) {
                    $s->where('status', $status);
                });
            })
            ->when($tab === 'alumni', function ($q) {
                $q->whereHas('student', function ($s) {
                    $s->where('is_alumni', 1);
                });
            }, function ($q) use ($alumni) {
                if ($alumni !== '1') {
                    $q->whereHas('student', function ($s) {
                        $s->where(function ($sub) {
                            $sub->where('is_alumni', 0)->orWhereNull('is_alumni');
                        });
                    });
                }
            })
            ->orderBy('name');

        $students = $studentsQuery->get();
        $college = \App\Models\Department::first();

        return view('admin.print.students-list', compact('students', 'college', 'semester', 'status', 'academicYear'));
    }

    public function printAlumniList(Request $request)
    {
        $students = User::where('role', 'student')
            ->whereHas('student', function($q) { $q->where('is_alumni', 1); })
            ->with('student')
            ->get();
        $college = Department::first();
        $academicYear = $request->academic_year ?? date('Y');
        return view('admin.print.alumni-list', compact('students', 'college', 'academicYear'));
    }

    // Return a printable HTML view (no layout chrome)
    public function print($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        // Convert image to base64 for PDF
        $student->photo_base64 = $this->getImageBase64($student);
        return view('admin.partials.student-print', compact('student'));
    }
    
    // Return a detailed printable HTML view (like report print)
    public function printDetail($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        // Convert image to base64 for PDF
        $student->photo_base64 = $this->getImageBase64($student);
        $college = Department::first();
        return view('admin.print.student-detail', compact('student', 'college'));
    }
    
    // Return JSON for modal
    public function jsonDetail($id)
    {
        try {
            $student = User::where('role','student')->with('student')->findOrFail($id);
            
            // Get profile photo URL
            $photoUrl = null;
            if (!empty($student->profile_photo_path)) {
                $photoUrl = asset('storage/' . $student->profile_photo_path);
            } elseif ($student->student && !empty($student->student->profile_photo_path)) {
                $photoUrl = asset('storage/' . $student->student->profile_photo_path);
            }
            
            // Get student data with null checks
            $studentData = $student->student;
            
            return response()->json([
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone ?? ($studentData->phone ?? null),
                    'student_id' => $studentData ? ($studentData->id ?? $student->id) : $student->id,
                    'roll_no' => $studentData ? $studentData->roll_no : null,
                    'program' => $studentData ? ($studentData->department ?? null) : null,
                    'semester' => $studentData ? $studentData->semester : null,
                    'status' => $studentData ? ($studentData->status ?? 'active') : 'active',
                    'photo_url' => $photoUrl,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching student detail: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        try {
            $student = User::where('role','student')->with('student')->findOrFail($id);
            // Convert image to base64 for PDF
            $student->photo_base64 = $this->getImageBase64($student);
            $pdf = Pdf::loadView('admin.partials.student-print', compact('student'));
            return $pdf->download('student_' . $student->id . '_' . Str::slug($student->name) . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Student download error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }

    private function getImageBase64($user)
    {
        $path = null;
        
        if ($user->student && $user->student->profile_photo_path) {
            $path = $user->student->profile_photo_path;
        } elseif ($user->profile_photo_path) {
            $path = $user->profile_photo_path;
        }

        if (!$path) {
            return null;
        }

        // Handle both with and without storage/ prefix
        if (strpos($path, 'storage/') === 0) {
            $fullPath = public_path($path);
        } else {
            $fullPath = public_path('storage/' . $path);
        }

        if (file_exists($fullPath)) {
            $data = file_get_contents($fullPath);
            $base64 = base64_encode($data);
            $mime = mime_content_type($fullPath);
            return 'data:' . $mime . ';base64,' . $base64;
        }

        return null;
    }

    // Dedicated export route (accepts optional ids[] or filters)
    public function export(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $user = auth()->user();

            if (!empty($ids)) {
                $rows = User::where('role', 'student')->whereIn('id', $ids)->with('student')->get();
            } else {
                // Use the same filtering logic as the index view
                $builder = User::where('role', 'student');

                // Filter by user role (same as index)
                if ($user->role === 'teacher' && $user->semester) {
                    $builder->whereHas('student', function($q) use ($user) {
                        $q->where('semester', $user->semester);
                    });
                } elseif ($user->role === 'parent') {
                    $builder->whereHas('student', function($q) use ($user) {
                        $q->where('parent_id', $user->id);
                    });
                }

                // Apply search filter
                if ($q = $request->input('q')) {
                    $builder->where(function($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
                }

                // Apply semester filter
                if ($semester = $request->input('semester')) {
                    $builder->whereHas('student', function($s) use ($semester) {
                        $s->where('semester', $semester);
                    });
                }

                // Apply subject filter
                $subjectId = $request->input('subject');
                if ($subjectId) {
                    $builder->whereHas('student.subjects', function($s) use ($subjectId) {
                        $s->where('subjects.id', $subjectId);
                    });
                }

                // Apply academic year filter
                if ($academicYear = $request->input('academic_year')) {
                    $builder->whereHas('student', function($s) use ($academicYear) {
                        $s->where(function($sub) use ($academicYear) {
                            $sub->where('academic_year', $academicYear)
                                ->orWhere('academic_year_bs', $academicYear);
                        });
                    });
                }

                // Handle tab and alumni filters
                $tab = $request->input('tab', 'active');
                $alumni = $request->input('alumni');

                if ($tab === 'alumni') {
                    $builder->whereHas('student', function($s) {
                        $s->where('is_alumni', 1);
                    });
                } else {
                    // Exclude alumni if not on alumni tab
                    if ($alumni !== '1') {
                        $builder->whereHas('student', function($s) {
                            $s->where(function($sub) {
                                $sub->where('is_alumni', 0)->orWhereNull('is_alumni');
                            });
                        });
                    }
                }

                // Apply status filter if present
                if ($status = $request->input('status')) {
                    if ($status !== 'all') {
                        $builder->whereHas('student', function($s) use ($status) {
                            $s->where('status', $status);
                        });
                    }
                }

                $builder->with('student')->orderBy('created_at', 'desc');
                $rows = $builder->get();
            }

            // Prepare filter information for export header
            $filterInfo = [];
            
            if ($request->input('q')) {
                $filterInfo[] = 'Search: ' . $request->input('q');
            }
            if ($request->input('semester')) {
                $filterInfo[] = 'Semester: ' . $request->input('semester');
            }
            if ($request->input('academic_year')) {
                $filterInfo[] = 'Academic Year: ' . $request->input('academic_year');
            }
            if ($request->input('status') && $request->input('status') !== 'all') {
                $filterInfo[] = 'Status: ' . $request->input('status');
            }
            if ($request->input('tab') && $request->input('tab') !== 'active') {
                $filterInfo[] = 'Tab: ' . ucfirst($request->input('tab'));
            } else {
                $filterInfo[] = 'Tab: Active Students';
            }

            $filename = 'students_export_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $columns = ['ID', 'Name', 'Student IT', 'Email', 'Semester', 'Department', 'Date of Birth (BS)', 'Academic Year', 'Academic Year (BS)', 'Address', 'Status', 'Alumni'];

            $callback = function() use ($rows, $columns, $filterInfo) {
                $out = fopen('php://output', 'w');
                
                if (!$out) {
                    throw new \Exception('Failed to open output stream');
                }
                
                // Write BOM for UTF-8 CSV (Excel compatibility)
                fwrite($out, "\xEF\xBB\xBF");
                
                // Write metadata header
                fputcsv($out, ['STUDENT EXPORT REPORT']);
                fputcsv($out, ['']);
                fputcsv($out, ['Export Date & Time: ' . date('Y-m-d H:i:s')]);
                fputcsv($out, ['Total Records: ' . count($rows)]);
                fputcsv($out, ['']);
                
                // Write filter information
                fputcsv($out, ['APPLIED FILTERS:']);
                foreach ($filterInfo as $filter) {
                    fputcsv($out, [$filter]);
                }
                fputcsv($out, ['']);
                fputcsv($out, ['']);
                
                // Write column headers
                fputcsv($out, $columns);
                
                // Write data rows
                foreach ($rows as $r) {
                    $line = [
                        $r->id,
                        $r->name,
                        $r->student->roll_no ?? '',
                        $r->email,
                        $r->student->semester ?? '',
                        $r->student->department ?? '',
                        $r->student->date_of_birth_bs ?? '',
                        $r->student->academic_year ?? '',
                        $r->student->academic_year_bs ?? '',
                        $r->student->address ?? '',
                        $r->student->status ?? '',
                        ($r->student->is_alumni ?? 0) ? 'Yes' : 'No',
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Student export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate export'], 500);
        }
    }

    // Handle bulk actions from the UI
    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = array_filter((array)$request->input('ids', []));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No students selected for bulk action.');
        }

        try {
            switch ($action) {
                case 'set_status_active':
                case 'set_status_inactive':
                    $status = $action === 'set_status_active' ? 'active' : 'inactive';
                    Student::whereIn('user_id', $ids)->update(['status' => $status]);
                    return redirect()->back()->with('success', 'Status updated for selected students.');

                case 'set_semester':
                    $semester = $request->input('semester');
                    if (!in_array($semester, ['1','2','3','4','5','6'])) {
                        return redirect()->back()->with('error', 'Invalid semester for bulk update.');
                    }
                    Student::whereIn('user_id', $ids)->update(['semester' => $semester]);
                    return redirect()->back()->with('success', 'Semester updated for selected students.');

                case 'move_alumni':
                    // Move to alumni, set status to inactive, and remove semester
                    Student::whereIn('user_id', $ids)->update([
                        'is_alumni' => 1, 
                        'alumni_from' => now(),
                        'status' => 'inactive',
                        'semester' => 0
                    ]);
                    return redirect()->back()->with('success', 'Selected students moved to alumni and set to inactive.');

                case 'remove_alumni':
                    $semester = $request->input('semester');
                    if (!in_array($semester, ['1','2','3','4','5','6'])) {
                        return redirect()->back()->with('error', 'Invalid semester for bulk update.');
                    }
                    Student::whereIn('user_id', $ids)->update([
                        'is_alumni' => 0, 
                        'alumni_from' => null,
                        'semester' => $semester,
                        'status' => 'active'
                    ]);
                    return redirect()->back()->with('success', 'Selected students removed from alumni and assigned to semester.');

                case 'delete':
                    User::whereIn('id', $ids)->delete();
                    return redirect()->back()->with('success', 'Selected students deleted.');

                default:
                    return redirect()->back()->with('error', 'Unknown bulk action.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Get student data via API for printing
     */
    public function apiGetStudent($id)
    {
        try {
            $user = User::findOrFail($id);
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            // Get college
            $college = $student->college ?? Auth::user()->college;

            // Get marks
            $marks = $student->marks()->get()->map(function($mark) {
                return [
                    'subject' => $mark->subject ?? 'Subject',
                    'obtained_marks' => $mark->obtained_marks ?? 0,
                    'total_marks' => $mark->total_marks ?? 100,
                ];
            });

            // Get attendance summary
            $totalDays = $student->attendanceRecords()->count();
            $presentDays = $student->attendanceRecords()->where('status', 'present')->count();

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'roll_no' => $student->roll_no,
                    'date_of_birth' => $student->date_of_birth,
                    'gender' => $student->gender,
                    'status' => $student->status,
                    'phone' => $student->phone,
                    'address' => $student->address,
                ],
                'college' => [
                    'name' => $college->name ?? 'Department',
                    'address' => $college->address ?? '',
                    'phone' => $college->phone ?? '',
                    'email' => $college->email ?? '',
                    'logo_path' => $college->logo_path ?? null,
                ],
                'marks' => $marks,
                'attendance' => [
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
