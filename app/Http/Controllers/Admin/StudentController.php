<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\Mark;
use App\Models\ParentModel;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\AuditLog;
use App\Notifications\StudentAccountNotification;
use App\Notifications\ParentAccountNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

        $academicYears = $this->getAcademicYears();

        // Get active subjects for filter dropdown
        $subjects = \App\Models\Subject::active()
            ->select('id', \DB::raw('subject_name as name'))
            ->orderBy('semester', 'asc')
            ->orderBy('subject_name', 'asc')
            ->get();

        $departmentOptions = $this->getDistinctStudentColumnValues('department');
        $programOptions = $this->getDistinctStudentColumnValues('program');
        $sectionOptions = $this->getDistinctStudentColumnValues('section');

        // Export as CSV if requested
        if (request('export') === 'csv') {
            $rows = $builder->with('student')->get();  // FIX: Eager load to avoid N+1 queries
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

    public function create()
    {
        return view('admin.students.create', $this->getStudentFormViewData());
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
            $rows = $builder->with('student')->get();  // FIX: Eager load to avoid N+1 queries
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

        // FIX #15: Cache alumni statistics to avoid re-calculating on every request
        $alumniStats = \Illuminate\Support\Facades\Cache::remember('alumni_statistics', 3600, function() {
            return [
                'total' => User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1); })->count(),
                'active' => User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1)->where('status','active'); })->count(),
                'inactive' => User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1)->where('status','inactive'); })->count(),
            ];
        });

        return view('admin.alumni-students', compact('students','academicYears','alumniStats'));
    }

    public function store(Request $request)
    {
        $data = $this->validateStudentData($request);

        try {
            \DB::beginTransaction();
            $data = $this->normalizeStudentData($data);
            $parentAccount = $this->resolveParentAccountFromStudentData($data);

            $password = Str::random(10);

            $user = new User([
                ...$this->buildUserData($data),
                'password' => Hash::make($password),
            ]);
            $user->role = 'student';
            $user->save();

            $student = new Student($this->buildStudentData($data, $parentAccount['parent']?->id));
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

            if (($parentAccount['created'] ?? false) && !empty($parentAccount['parent'])) {
                try {
                    $parentAccount['parent']->notify(new ParentAccountNotification(
                        $parentAccount['password'],
                        $parentAccount['notification_context'] ?? []
                    ));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send parent notification: ' . $e->getMessage());
                }
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
                    'parent_id' => $student->parent_id,
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
        $student = User::where('role', 'student')
            ->with([
                'student.parentUser.parent',
                'student.attendanceRecords.subject',
                'student.examMarks.exam.subject',
                'student.marks.subject',
                'student.subjects.teacherAssignments.teacher.user',
            ])
            ->findOrFail($id);

        return view('admin.students.show', array_merge([
            'student' => $student,
        ], $this->buildStudentShowViewData($student)));
    }

    public function edit($id)
    {
        $student = User::where('role','student')->with('student')->findOrFail($id);
        return view('admin.students.edit', array_merge(['student' => $student], $this->getStudentFormViewData()));
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
            $student = $user->student ?: new Student(['user_id' => $user->id]);
            $parentAccount = $this->resolveParentAccountFromStudentData($data, $student->parent_id);

            $user->fill($this->buildUserData($data));
            $user->role = 'student';
            $user->save();

            $student->fill($this->buildStudentData($data, $parentAccount['parent']?->id ?? $student->parent_id));
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

    private function resolveParentAccountFromStudentData(array $data, ?int $fallbackParentId = null): array
    {
        $parentEmail = trim((string) ($data['parent_email'] ?? ''));

        if ($parentEmail === '') {
            $fallbackParent = $fallbackParentId
                ? User::where('role', 'parent')->with('parent')->find($fallbackParentId)
                : null;

            return [
                'parent' => $fallbackParent,
                'created' => false,
                'password' => null,
                'notification_context' => [],
            ];
        }

        $existingParent = User::where('email', $parentEmail)->with('parent')->first();

        if ($existingParent && $existingParent->role !== 'parent') {
            throw ValidationException::withMessages([
                'parent_email' => 'This email is already used by another account type.',
            ]);
        }

        $created = false;
        $password = null;
        $parentUser = $existingParent ?: new User();

        if (!$existingParent) {
            $created = true;
            $password = Str::random(10);
            $parentUser->password = Hash::make($password);
        }

        $parentUser->name = filled($data['parent_name'] ?? null)
            ? trim((string) $data['parent_name'])
            : ($parentUser->name ?: trim((string) ($data['name'] ?? '')));
        $parentUser->email = $parentEmail;
        $parentUser->phone = filled($data['parent_phone'] ?? null)
            ? preg_replace('/\D+/', '', (string) $data['parent_phone'])
            : ($parentUser->phone ?? null);
        $parentUser->bio = filled($data['parent_bio'] ?? null)
            ? trim((string) $data['parent_bio'])
            : ($parentUser->bio ?? null);
        $parentUser->role = 'parent';
        $parentUser->save();

        $parentProfile = $parentUser->parent ?: new ParentModel();
        $parentProfile->user_id = $parentUser->id;
        $parentProfile->parent_code = $parentProfile->parent_code ?: $this->generateParentCode($parentUser->id);
        $parentProfile->occupation = filled($data['parent_occupation'] ?? null)
            ? trim((string) $data['parent_occupation'])
            : ($parentProfile->occupation ?? null);
        $parentProfile->phone = filled($data['parent_phone'] ?? null)
            ? preg_replace('/\D+/', '', (string) $data['parent_phone'])
            : ($parentProfile->phone ?? null);
        $parentProfile->address = filled($data['parent_address'] ?? null)
            ? trim((string) $data['parent_address'])
            : ($parentProfile->address ?? null);
        $parentProfile->bio = filled($data['parent_bio'] ?? null)
            ? trim((string) $data['parent_bio'])
            : ($parentProfile->bio ?? null);
        $parentProfile->status = filled($data['parent_status'] ?? null)
            ? (string) $data['parent_status']
            : ($parentProfile->status ?? 'active');
        $parentProfile->gender = filled($data['parent_gender'] ?? null)
            ? (string) $data['parent_gender']
            : ($parentProfile->gender ?? null);
        $parentProfile->save();

        return [
            'parent' => $parentUser->fresh(['parent']),
            'created' => $created,
            'password' => $password,
            'notification_context' => [
                'student_name' => $data['name'] ?? '',
                'student_email' => $data['email'] ?? '',
                'student_roll_no' => $data['student_id'] ?? '',
                'relationship' => $data['parent_relationship'] ?? '',
                'parent_name' => $parentUser->name,
                'parent_email' => $parentUser->email,
            ],
        ];
    }

    private function generateParentCode(int $userId): string
    {
        return 'P' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT);
    }

    private function buildStudentShowViewData(User $student): array
    {
        $profile = $student->student;
        $parentUser = $profile?->parentUser;
        $parentProfile = $parentUser?->parent;

        $attendanceTimeline = collect($profile?->attendanceRecords ?? [])
            ->sortByDesc(fn (Attendance $record) => optional($record->date)?->timestamp ?? optional($record->created_at)?->timestamp ?? 0)
            ->values();

        $examMarkTimeline = collect($profile?->examMarks ?? [])
            ->sortByDesc(fn (ExamMark $mark) => optional($mark->exam?->exam_date)?->timestamp ?? optional($mark->graded_at)?->timestamp ?? optional($mark->updated_at)?->timestamp ?? 0)
            ->values();

        $legacyMarkTimeline = collect($profile?->marks ?? [])
            ->sortByDesc(fn (Mark $mark) => optional($mark->date)?->timestamp ?? optional($mark->created_at)?->timestamp ?? 0)
            ->values();

        $attendanceRecords = $attendanceTimeline->map(function (Attendance $record) {
            return [
                'id' => $record->id,
                'subject_id' => $record->subject_id,
                'subject_name' => $record->subject?->subject_name ?? ($record->subject ?? 'Subject'),
                'subject_code' => $record->subject?->subject_code,
                'status' => $record->status,
                'status_label' => ucfirst((string) $record->status),
                'remarks' => $record->remarks,
                'date' => $record->date,
                'date_label' => optional($record->date)?->format('M d, Y') ?? 'Date pending',
                'sort_key' => optional($record->date)?->timestamp ?? optional($record->created_at)?->timestamp ?? 0,
            ];
        });

        $attendanceBySubject = $attendanceTimeline
            ->groupBy('subject_id')
            ->map(function ($records) {
                $records = collect($records)->sortByDesc(fn (Attendance $record) => optional($record->date)?->timestamp ?? optional($record->created_at)?->timestamp ?? 0)->values();
                $total = $records->count();
                $present = $records->where('status', 'present')->count();
                $absent = $records->where('status', 'absent')->count();
                $late = $records->where('status', 'late')->count();
                $leave = $records->whereIn('status', ['leave', 'excused'])->count();
                $first = $records->first();

                return [
                    'subject_id' => $first?->subject_id,
                    'subject_name' => $first?->subject?->subject_name ?? ($first?->subject ?? 'Subject'),
                    'subject_code' => $first?->subject?->subject_code,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'leave' => $leave,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 100,
                    'latest_status' => $first?->status,
                    'latest_status_label' => ucfirst((string) ($first?->status ?? '')),
                ];
            })
            ->sortByDesc('percentage')
            ->values();

        $examMarks = $examMarkTimeline->map(function (ExamMark $mark) {
            $examDate = $mark->exam?->exam_date;
            $percentage = $mark->percentage ?? $mark->calculatePercentage();
            $status = $mark->isAbsent() ? 'absent' : ($mark->isPassedAllComponents() ? 'pass' : 'fail');

            return [
                'id' => $mark->id,
                'subject_id' => $mark->subject_id,
                'subject_name' => $mark->subject?->subject_name ?? 'Subject',
                'subject_code' => $mark->subject?->subject_code,
                'exam_id' => $mark->exam_id,
                'exam_category' => $mark->exam?->exam_category,
                'assessment_number' => $mark->exam?->assessment_number,
                'exam_name' => $mark->exam?->exam_name ?? 'Exam',
                'category_label' => $mark->exam?->formatted_category ?? 'Exam',
                'type_label' => $mark->exam?->formatted_type ?? 'Assessment',
                'date' => $examDate,
                'date_label' => $examDate?->format('M d, Y') ?? 'Date pending',
                'obtained_marks' => round((float) $mark->effective_obtained_marks, 2),
                'full_marks' => round((float) $mark->effective_full_marks, 2),
                'passing_marks' => round((float) $mark->effective_passing_marks, 2),
                'percentage' => $percentage !== null ? round((float) $percentage, 2) : null,
                'grade' => $mark->calculateGrade(),
                'status' => $status,
                'status_label' => match ($status) {
                    'pass' => 'Pass',
                    'fail' => 'Needs Attention',
                    'absent' => 'Absent',
                    default => 'Pending',
                },
                'remarks' => $mark->remarks,
                'sort_key' => optional($examDate)?->timestamp ?? optional($mark->graded_at)?->timestamp ?? optional($mark->updated_at)?->timestamp ?? 0,
            ];
        });

        $legacyMarks = $legacyMarkTimeline->map(function (Mark $mark) {
            $markDate = $mark->date;
            $percentage = $mark->percentage;
            $status = $percentage >= 40 ? 'pass' : 'fail';

            return [
                'id' => $mark->id,
                'subject_id' => $mark->subject_id,
                'subject_name' => $mark->subject?->subject_name ?? 'Subject',
                'subject_code' => $mark->subject?->subject_code,
                'exam_id' => null,
                'exam_category' => 'legacy',
                'assessment_number' => null,
                'exam_name' => ucfirst(str_replace('_', ' ', $mark->exam_type ?? 'mark')),
                'category_label' => 'Recorded Mark',
                'type_label' => ucfirst(str_replace('_', ' ', $mark->exam_type ?? 'mark')),
                'date' => $markDate,
                'date_label' => optional($markDate)?->format('M d, Y') ?? 'Date pending',
                'obtained_marks' => round((float) $mark->marks_obtained, 2),
                'full_marks' => round((float) $mark->full_marks, 2),
                'passing_marks' => $mark->full_marks > 0 ? round((float) $mark->full_marks * 0.4, 2) : 0,
                'percentage' => $percentage !== null ? round((float) $percentage, 2) : null,
                'grade' => $percentage >= 90 ? 'A+' : ($percentage >= 80 ? 'A' : ($percentage >= 70 ? 'B+' : ($percentage >= 60 ? 'B' : ($percentage >= 50 ? 'C+' : ($percentage >= 40 ? 'C' : 'F'))))),
                'status' => $status,
                'status_label' => $status === 'pass' ? 'Pass' : 'Needs Attention',
                'remarks' => null,
                'sort_key' => optional($markDate)?->timestamp ?? optional($mark->created_at)?->timestamp ?? 0,
            ];
        });

        $markTimeline = $examMarks
            ->concat($legacyMarks)
            ->sortByDesc('sort_key')
            ->values();

        $marksWithPercentage = $markTimeline->filter(fn ($mark) => $mark['percentage'] !== null);
        $averageMark = $marksWithPercentage->isNotEmpty() ? round((float) $marksWithPercentage->avg('percentage'), 1) : 0;

        $subjectPerformance = collect($profile?->subjects ?? [])
            ->map(function ($subject) use ($attendanceBySubject, $markTimeline) {
                $teacherAssignment = $subject->teacherAssignments->firstWhere('role', 'primary')
                    ?? $subject->teacherAssignments->first();
                $subjectAttendance = $attendanceBySubject->firstWhere('subject_id', $subject->id) ?? [
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'leave' => 0,
                    'percentage' => 100,
                ];

                $subjectMarks = $markTimeline->where('subject_id', $subject->id)->values();
                $latestSubjectMark = $subjectMarks->first();
                $markAverage = $subjectMarks->whereNotNull('percentage')->isNotEmpty()
                    ? round((float) $subjectMarks->whereNotNull('percentage')->avg('percentage'), 1)
                    : null;

                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'teacher_name' => $teacherAssignment?->teacher?->user?->name,
                    'attendance_percentage' => $subjectAttendance['percentage'] ?? 100,
                    'attendance_total' => $subjectAttendance['total'] ?? 0,
                    'marks_average' => $markAverage,
                    'marks_count' => $subjectMarks->count(),
                    'latest_mark' => $latestSubjectMark ? (
                        isset($latestSubjectMark['full_marks']) && (float) $latestSubjectMark['full_marks'] > 0
                            ? trim(($latestSubjectMark['obtained_marks'] ?? 0) . ' / ' . ($latestSubjectMark['full_marks'] ?? 0))
                            : (string) ($latestSubjectMark['obtained_marks'] ?? 0)
                    ) : null,
                    'latest_percentage' => $latestSubjectMark['percentage'] ?? null,
                    'latest_exam_name' => $latestSubjectMark['exam_name'] ?? null,
                    'latest_grade' => $latestSubjectMark['grade'] ?? null,
                ];
            })
            ->sortByDesc('attendance_percentage')
            ->values();

        $attendanceSummary = [
            'total' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'leave' => $attendanceRecords->whereIn('status', ['leave', 'excused'])->count(),
            'percentage' => $attendanceRecords->count() > 0
                ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1)
                : 100,
        ];

        $markSummary = [
            'total' => $markTimeline->count(),
            'average' => $averageMark,
            'pass' => $markTimeline->where('status', 'pass')->count(),
            'fail' => $markTimeline->where('status', 'fail')->count(),
            'absent' => $markTimeline->where('status', 'absent')->count(),
            'latest' => $markTimeline->first(),
        ];

        $examGroups = $markTimeline
            ->groupBy(function (array $mark) {
                return !empty($mark['exam_id'])
                    ? 'exam-' . $mark['exam_id']
                    : 'legacy-' . md5(($mark['exam_name'] ?? 'Exam') . '|' . ($mark['date_label'] ?? ''));
            })
            ->map(function ($marks, $groupKey) {
                $marks = collect($marks)
                    ->sortBy(fn (array $mark) => $mark['subject_name'] ?? '')
                    ->values();

                $totalFull = round((float) $marks->sum(fn (array $mark) => (float) ($mark['full_marks'] ?? 0)), 2);
                $totalObtained = round((float) $marks->sum(fn (array $mark) => (float) ($mark['obtained_marks'] ?? 0)), 2);
                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
                $grade = $percentage >= 90 ? 'A+' : ($percentage >= 80 ? 'A' : ($percentage >= 70 ? 'B+' : ($percentage >= 60 ? 'B' : ($percentage >= 50 ? 'C+' : ($percentage >= 40 ? 'C' : 'F')))));
                $statuses = $marks->pluck('status')->map(fn ($status) => strtolower((string) $status));
                $resultStatus = $statuses->contains('absent') ? 'absent' : ($statuses->contains('fail') ? 'fail' : 'pass');
                $resultLabel = match ($resultStatus) {
                    'pass' => 'Pass',
                    'fail' => 'Needs Attention',
                    'absent' => 'Absent',
                    default => 'Pending',
                };
                $first = $marks->first();

                return [
                    'group_key' => $groupKey,
                    'exam_id' => $first['exam_id'] ?? null,
                    'exam_category' => $first['exam_category'] ?? null,
                    'assessment_number' => $first['assessment_number'] ?? null,
                    'exam_name' => $first['exam_name'] ?? 'Exam',
                    'category_label' => $first['category_label'] ?? 'Exam',
                    'type_label' => $first['type_label'] ?? 'Assessment',
                    'date_label' => $first['date_label'] ?? 'Date pending',
                    'sort_key' => $marks->max('sort_key') ?? 0,
                    'total_subjects' => $marks->count(),
                    'total_full' => $totalFull,
                    'total_obtained' => $totalObtained,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'result_status' => $resultStatus,
                    'result_label' => $resultLabel,
                    'marks' => $marks,
                ];
            })
            ->sortByDesc('sort_key')
            ->values();

        $photoUrl = $this->storageUrl($profile?->profile_photo_path ?: $student->profile_photo_path) ?? $student->profile_photo_url;
        $idDocument = $profile?->id_document_path ? [
            'name' => basename($profile->id_document_path),
            'path' => $profile->id_document_path,
            'url' => $this->storageUrl($profile->id_document_path),
        ] : null;
        $certificates = collect($profile?->certificate_paths ?? [])
            ->filter()
            ->values()
            ->map(fn ($path) => [
                'name' => basename($path),
                'path' => $path,
                'url' => $this->storageUrl($path),
            ]);

        return [
            'studentProfile' => $profile,
            'photoUrl' => $photoUrl,
            'attendanceSummary' => $attendanceSummary,
            'attendanceRecords' => $attendanceRecords->take(10),
            'attendanceBySubject' => $attendanceBySubject,
            'markSummary' => $markSummary,
            'examGroups' => $examGroups,
            'markTimeline' => $markTimeline->take(10),
            'subjectPerformance' => $subjectPerformance->take(6),
            'parentInfo' => [
                'name' => $parentUser?->name,
                'email' => $parentUser?->email,
                'phone' => $parentProfile?->phone ?? $parentUser?->phone,
                'occupation' => $parentProfile?->occupation,
                'address' => $parentProfile?->address,
                'gender' => $parentProfile?->gender,
                'status' => $parentProfile?->status ?? 'active',
                'parent_code' => $parentProfile?->parent_code,
                'bio' => $parentProfile?->bio,
                'children_count' => $parentUser ? Student::where('parent_id', $parentUser->id)->count() : 0,
            ],
            'documents' => [
                'id_document' => $idDocument,
                'certificates' => $certificates,
            ],
            'quickStats' => [
                'attendance' => $attendanceSummary['percentage'],
                'marks' => $averageMark,
                'subjects' => $subjectPerformance->count(),
                'documents' => ($idDocument ? 1 : 0) + $certificates->count(),
            ],
            'notesValue' => $profile?->notes ?? $profile?->bio ?? $student->bio,
            'basicInfo' => [
                'name' => $student->name,
                'email' => $student->email,
                'username' => $student->username,
                'phone' => $profile?->phone ?? $student->phone,
                'student_id' => $profile?->roll_no,
                'department' => $profile?->department ?? $student->department,
                'program' => $profile?->program,
                'semester' => $profile?->semester,
                'section' => $profile?->section,
                'academic_year' => $profile?->academic_year,
                'academic_year_bs' => $profile?->academic_year_bs,
                'date_of_birth' => optional($profile?->date_of_birth)?->format('Y-m-d') ?? null,
                'date_of_birth_bs' => $profile?->date_of_birth_bs,
                'gender' => $profile?->gender,
                'blood_group' => $profile?->blood_group,
                'national_id_number' => $profile?->national_id_number,
                'secondary_phone' => $profile?->secondary_phone,
                'emergency_contact' => $profile?->emergency_contact,
                'emergency_contact_name' => $profile?->emergency_contact_name,
                'emergency_relationship' => $profile?->emergency_relationship,
                'address' => $profile?->address,
                'city' => $profile?->city,
                'state_province' => $profile?->state_province,
                'postal_code' => $profile?->postal_code,
                'country' => $profile?->country,
                'medical_conditions' => $profile?->medical_conditions,
                'allergies' => $profile?->allergies,
                'disability_status' => $profile?->disability_status,
                'status' => $profile?->status ?? 'active',
                'is_active' => (bool) ($profile?->is_active ?? true),
                'is_alumni' => (bool) ($profile?->is_alumni ?? false),
                'enrollment_date' => optional($profile?->enrollment_date)?->format('Y-m-d') ?? null,
                'expected_graduation_year' => $profile?->expected_graduation_year,
            ],
        ];
    }

    private function validateStudentData(Request $request, ?User $user = null): array
    {
        $userId = $user?->id;
        $parentRules = $userId
            ? ['nullable', 'string', 'max:255']
            : ['required', 'string', 'max:255'];
        $parentEmailRules = $userId
            ? ['nullable', 'email', 'max:255']
            : ['required', 'email', 'max:255'];
        $parentPhoneRules = $userId
            ? ['nullable', 'digits:10']
            : ['required', 'digits:10'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'phone' => ['required', 'digits:10'],
            'student_id' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:150'],
            'semester' => ['required', Rule::in($this->getSemesterValues())],
            'section' => ['nullable', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20'],
            'academic_year_bs' => ['nullable', 'string', 'max:20'],
            'enrollment_date' => ['nullable', 'date'],
            'expected_graduation_year' => ['nullable', 'digits:4'],
            'parent_name' => $parentRules,
            'parent_email' => $parentEmailRules,
            'parent_phone' => $parentPhoneRules,
            'parent_gender' => ['nullable', 'string', 'max:20'],
            'parent_occupation' => ['nullable', 'string', 'max:100'],
            'parent_address' => ['nullable', 'string'],
            'parent_bio' => ['nullable', 'string'],
            'parent_status' => ['nullable', Rule::in(['active', 'pending', 'inactive'])],
            'parent_relationship' => ['nullable', 'string', 'max:100'],
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

    private function buildStudentData(array $data, ?int $parentId = null): array
    {
        $notes = $data['notes'] ?? null;

        return [
            'roll_no' => $data['student_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'section' => $data['section'] ?? null,
            'parent_id' => $parentId,
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

    private function getDistinctStudentColumnValues(string $column): array
    {
        if (!Schema::hasColumn('students', $column)) {
            return [];
        }

        return Student::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->values()
            ->all();
    }

    private function getAcademicYears(): array
    {
        if (!Schema::hasColumn('students', 'academic_year_bs')) {
            return [];
        }

        return Student::selectRaw("DISTINCT academic_year_bs as year")
            ->whereNotNull('academic_year_bs')
            ->where('academic_year_bs', '<>', '')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    private function getStudentFormViewData(): array
    {
        return [
            'academicYears' => $this->getAcademicYears(),
            'departmentOptions' => $this->getDistinctStudentColumnValues('department'),
            'programOptions' => $this->getDistinctStudentColumnValues('program'),
            'sectionOptions' => $this->getDistinctStudentColumnValues('section'),
            'semesterOptions' => $this->getSemesterOptions(),
        ];
    }

    private function getSemesterOptions(): array
    {
        return $this->getSemesterRecords()
            ->map(function ($semester) {
                $number = (string) ($semester->number ?? '');
                $label = trim((string) ($semester->name ?? Semester::getOrdinalName((int) $number)));

                return [
                    'value' => $number,
                    'label' => $label !== '' ? $label : ('Semester ' . $number),
                ];
            })
            ->filter(fn ($semester) => $semester['value'] !== '')
            ->values()
            ->all();
    }

    private function getSemesterValues(): array
    {
        $values = collect($this->getSemesterOptions())
            ->pluck('value')
            ->filter()
            ->map(fn ($value) => (string) $value)
            ->values()
            ->all();

        return $values !== [] ? $values : ['1', '2', '3', '4', '5', '6'];
    }

    private function getSemesterRecords()
    {
        if (!Schema::hasTable('semesters')) {
            return collect(range(1, 6))->map(fn ($number) => (object) [
                'number' => $number,
                'name' => Semester::getOrdinalName($number),
            ]);
        }

        $semesters = Semester::query()
            ->orderBy('number')
            ->get(['number', 'name']);

        if ($semesters->isEmpty()) {
            return collect(range(1, 6))->map(fn ($number) => (object) [
                'number' => $number,
                'name' => Semester::getOrdinalName($number),
            ]);
        }

        return $semesters;
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
