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
use Illuminate\Validation\Rule;
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

        $departmentOptions = Student::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->values()
            ->all();

        $programOptions = Student::query()
            ->whereNotNull('program')
            ->where('program', '!=', '')
            ->distinct()
            ->orderBy('program')
            ->pluck('program')
            ->values()
            ->all();

        $sectionOptions = Student::query()
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->distinct()
            ->orderBy('section')
            ->pluck('section')
            ->values()
            ->all();

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

        return view('admin.students', compact('students', 'academicYears', 'subjects', 'departmentOptions', 'programOptions', 'sectionOptions'));
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
        $data = $this->validateStudentData($request);

        try {
            \DB::beginTransaction();
            $data = $this->normalizeStudentData($data);

            $password = Str::random(10);

            $user = new User([
                ...$this->buildUserData($data),
                'password' => Hash::make($password),
            ]);
            $user->role = 'student';
            $user->save();

            $student = new Student($this->buildStudentData($data));
            $student->user_id = $user->id;
            $student->save();

            $this->syncStudentFiles($request, $student, $data);

            \DB::commit();

            $credentialsEmailSent = true;
            try {
                $user->notify(new StudentAccountNotification($password, 'student'));
            } catch (\Exception $e) {
                $credentialsEmailSent = false;
                \Illuminate\Support\Facades\Log::error('Failed to send student notification: ' . $e->getMessage());
            }

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

            $message = $credentialsEmailSent
                ? 'Student added successfully. Login credentials have been sent to the student\'s email.'
                : 'Student added successfully, but the credentials email could not be sent. Check mail settings and logs.';

            return redirect()->route('admin.students', ['page' => 1])
                ->with($credentialsEmailSent ? 'success' : 'warning', $message);
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

            return redirect()->route('admin.students')->with('success', 'Student removed successfully.');
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
        $data = $this->validateStudentData($request, $user);

        try {
            \DB::beginTransaction();

            $data = $this->normalizeStudentData($data);

            $user->fill($this->buildUserData($data));
            $user->role = 'student';
            $user->save();

            $student = $user->student ?: new Student(['user_id' => $user->id]);
            $student->fill($this->buildStudentData($data));
            $student->user_id = $user->id;
            $student->save();

            $this->syncStudentFiles($request, $student, $data);

            \DB::commit();

            return redirect()->route('admin.students')->with('success', 'Student updated successfully');
        } catch (\Exception $e) {
            \DB::rollBack();

            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage())->withInput();
        }
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
            $studentData = $student->student;
            $certificatePaths = collect($studentData?->certificate_paths ?? [])
                ->filter()
                ->values()
                ->map(fn ($path) => [
                    'name' => basename($path),
                    'path' => $path,
                    'url' => $this->storageUrl($path),
                ])
                ->all();
             
            return response()->json([
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'username' => $student->username,
                    'role' => $student->role,
                    'phone' => $studentData?->phone ?? $student->phone,
                    'secondary_phone' => $studentData?->secondary_phone,
                    'student_id' => $studentData?->roll_no,
                    'roll_no' => $studentData?->roll_no,
                    'department' => $studentData?->department ?? $student->department,
                    'program' => $studentData?->program,
                    'semester' => $studentData?->semester,
                    'section' => $studentData?->section,
                    'academic_year' => $studentData?->academic_year,
                    'academic_year_bs' => $studentData?->academic_year_bs,
                    'enrollment_date' => optional($studentData?->enrollment_date)->format('Y-m-d'),
                    'expected_graduation_year' => $studentData?->expected_graduation_year,
                    'date_of_birth' => optional($studentData?->date_of_birth)->format('Y-m-d'),
                    'date_of_birth_bs' => $studentData?->date_of_birth_bs,
                    'gender' => $studentData?->gender,
                    'blood_group' => $studentData?->blood_group,
                    'national_id_number' => $studentData?->national_id_number,
                    'emergency_contact' => $studentData?->emergency_contact,
                    'emergency_contact_name' => $studentData?->emergency_contact_name,
                    'emergency_relationship' => $studentData?->emergency_relationship,
                    'address' => $studentData?->address,
                    'city' => $studentData?->city,
                    'state_province' => $studentData?->state_province,
                    'postal_code' => $studentData?->postal_code,
                    'country' => $studentData?->country,
                    'medical_conditions' => $studentData?->medical_conditions,
                    'allergies' => $studentData?->allergies,
                    'disability_status' => $studentData?->disability_status,
                    'status' => $studentData?->status ?? 'active',
                    'is_active' => (bool) ($studentData?->is_active ?? true),
                    'is_alumni' => (bool) ($studentData?->is_alumni ?? false),
                    'notes' => $studentData?->notes ?? $studentData?->bio ?? $student->bio,
                    'photo_url' => $this->storageUrl($studentData?->profile_photo_path ?: $student->profile_photo_path),
                    'photo_path' => $studentData?->profile_photo_path ?: $student->profile_photo_path,
                    'id_document' => $studentData?->id_document_path ? [
                        'name' => basename($studentData->id_document_path),
                        'path' => $studentData->id_document_path,
                        'url' => $this->storageUrl($studentData->id_document_path),
                    ] : null,
                    'certificates' => $certificatePaths,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching student detail: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function validateStudentData(Request $request, ?User $user = null): array
    {
        $userId = $user?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'phone' => ['required', 'digits:10'],
            'student_id' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:150'],
            'semester' => ['required', Rule::in(['1', '2', '3', '4', '5', '6'])],
            'section' => ['nullable', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20'],
            'academic_year_bs' => ['nullable', 'string', 'max:20'],
            'enrollment_date' => ['nullable', 'date'],
            'expected_graduation_year' => ['nullable', 'digits:4'],
            'date_of_birth' => ['required', 'date'],
            'date_of_birth_bs' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'digits:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_relationship' => ['nullable', 'string', 'max:100'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'address' => ['required', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state_province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'medical_conditions' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'disability_status' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'role' => ['nullable', Rule::in(['student'])],
            'is_active' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
            'remove_profile_photo' => ['nullable', 'boolean'],
            'id_document' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'remove_id_document' => ['nullable', 'boolean'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'replace_certificates' => ['nullable', 'boolean'],
            'remove_certificates' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'batch_year' => ['nullable', 'string', 'max:10'],
        ]);
    }

    private function normalizeStudentData(array $data): array
    {
        if (empty($data['academic_year_bs']) && !empty($data['academic_year'])) {
            $data['academic_year_bs'] = $this->convertAdToBs($data['academic_year'] . '-01-01');
        }

        if (empty($data['batch_year']) && !empty($data['enrollment_date'])) {
            $data['batch_year'] = date('Y', strtotime($data['enrollment_date']));
        }

        $data['username'] = filled($data['username'] ?? null) ? $data['username'] : null;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return $data;
    }

    private function buildUserData(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? null,
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'bio' => $data['notes'] ?? null,
        ];
    }

    private function buildStudentData(array $data): array
    {
        $notes = $data['notes'] ?? null;

        return [
            'roll_no' => $data['student_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'section' => $data['section'] ?? null,
            'parent_id' => null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'date_of_birth_bs' => $data['date_of_birth_bs'] ?? null,
            'batch_year' => $data['batch_year'] ?? null,
            'academic_year' => $data['academic_year'] ?? null,
            'academic_year_bs' => $data['academic_year_bs'] ?? null,
            'enrollment_date' => $data['enrollment_date'] ?? null,
            'expected_graduation_year' => $data['expected_graduation_year'] ?? null,
            'gender' => $data['gender'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'national_id_number' => $data['national_id_number'] ?? null,
            'phone' => $data['phone'] ?? null,
            'secondary_phone' => $data['secondary_phone'] ?? null,
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_relationship' => $data['emergency_relationship'] ?? null,
            'department' => $data['department'] ?? null,
            'program' => $data['program'] ?? null,
            'status' => $data['status'] ?? 'active',
            'is_active' => $data['is_active'] ?? true,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state_province' => $data['state_province'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'bio' => $notes,
            'notes' => $notes,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'allergies' => $data['allergies'] ?? null,
            'disability_status' => $data['disability_status'] ?? null,
        ];
    }

    private function syncStudentFiles(Request $request, Student $student, array $data): void
    {
        if (!empty($data['remove_profile_photo']) && $student->profile_photo_path) {
            $this->deletePublicFile($student->profile_photo_path);
            $student->profile_photo_path = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->deletePublicFile($student->profile_photo_path);
            $student->profile_photo_path = $request->file('profile_photo')->store('profiles/students', 'public');
        }

        if (!empty($data['remove_id_document']) && $student->id_document_path) {
            $this->deletePublicFile($student->id_document_path);
            $student->id_document_path = null;
        }

        if ($request->hasFile('id_document')) {
            $this->deletePublicFile($student->id_document_path);
            $student->id_document_path = $request->file('id_document')->store('student-documents/id', 'public');
        }

        $existingCertificates = collect($student->certificate_paths ?? [])->filter()->values()->all();

        if (!empty($data['remove_certificates'])) {
            $this->deletePublicFiles($existingCertificates);
            $student->certificate_paths = [];
            $existingCertificates = [];
        }

        if ($request->hasFile('certificates')) {
            $this->deletePublicFiles($existingCertificates);

            $student->certificate_paths = collect($request->file('certificates'))
                ->map(fn ($file) => $file->store('student-documents/certificates', 'public'))
                ->all();
        }

        $student->save();
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deletePublicFiles(array $paths): void
    {
        $paths = array_values(array_filter($paths));

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }

    private function storageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/' . ltrim($path, '/'));
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
            $user = User::where('role', 'student')
                ->with(['student.marks.subject', 'student.attendanceRecords'])
                ->findOrFail($id);
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            $college = Department::first();

            $marks = $student->marks->map(function ($mark) {
                return [
                    'subject' => $mark->subject?->subject_name ?? 'Subject',
                    'obtained_marks' => $mark->marks_obtained ?? 0,
                    'total_marks' => $mark->full_marks ?? 100,
                    'percentage' => $mark->percentage ?? 0,
                ];
            })->values();

            $classAttendance = $student->attendanceRecords
                ->where('attendance_type', 'class')
                ->values();

            $totalDays = $classAttendance->count();
            $presentDays = $classAttendance->where('status', 'present')->count();

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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Student not found'], 404);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Unable to load student record.'], 500);
        }
    }
}
