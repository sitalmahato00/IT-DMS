<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\Subject;
use App\Notifications\TeacherAccountNotification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('q');
        $status = $request->get('status');
        $subjectId = $request->get('course');
        $perPage = intval($request->get('per_page', 10)) ?: 10;

        $subjects = Subject::orderBy('subject_name')
            ->get()
            ->mapWithKeys(function ($subject) {
                $label = trim(($subject->subject_code ? $subject->subject_code . ' - ' : '') . $subject->subject_name);
                return [$subject->id => $label];
            });
        $teacherSubjectOptions = Subject::with('teachers')
            ->orderByRaw('CASE WHEN semester IS NULL OR semester = "" THEN 1 ELSE 0 END')
            ->orderBy('semester')
            ->orderBy('subject_name')
            ->get()
            ->map(function ($subject) {
                $semesterLabel = $subject->semester ? 'Semester ' . $subject->semester : 'All Semesters';
                return [
                    'id' => $subject->id,
                    'label' => trim(($subject->subject_code ? $subject->subject_code . ' - ' : '') . $subject->subject_name),
                    'semester' => (string) ($subject->semester ?? ''),
                    'semester_label' => $semesterLabel,
                    'status' => $subject->status ?? 'active',
                ];
            })
            ->values();
        $semesterOptions = Semester::orderBy('number')
            ->get()
            ->map(function ($semester) {
                return [
                    'value' => (string) $semester->number,
                    'label' => trim(($semester->name ?: Semester::getOrdinalName((int) $semester->number)) . ' (Sem ' . $semester->number . ')'),
                ];
            })
            ->values();

        $builder = User::where('role', 'teacher')->with('teacher');
        
        // Filter by user role
        if ($user->role === 'teacher') {
            // Teachers see only themselves
            $builder->where('id', $user->id);
        } elseif ($user->role === 'parent') {
            // Parents see teachers of their children
            $studentIds = Student::where('parent_id', $user->id)->pluck('user_id');
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
                $q->whereHas('teacher', function($t) use ($status) {
                    $t->where('status', $status);
                });
            })
            ->when($subjectId, function($q) use ($subjectId) {
                $q->whereHas('teacher.subjects', function($subjectQuery) use ($subjectId) {
                    $subjectQuery->where('subjects.id', $subjectId);
                });
            })
            ->orderBy('created_at', 'desc');

        $teachers = $builder->paginate($perPage)->withQueryString();

        // Add teaching load info to each teacher
        $teachers->getCollection()->transform(function ($teacher) {
            $subjects = $teacher->teacher ? $teacher->teacher->assignedSubjects() : collect();
            $teacher->teaching_load = [
                'subjects_count' => $subjects->count(),
                'total_hours' => $subjects->sum(function($s) {
                    return ($s->lecture_hours ?? 4) + ($s->practical_hours ?? 2) + ($s->tutorial_hours ?? 1);
                }),
                'subjects' => $subjects->pluck('subject_name')->toArray()
            ];
            return $teacher;
        });

        return view('admin.teachers', compact('teachers', 'subjects', 'teacherSubjectOptions', 'semesterOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTeacherData($request);
        $password = Str::random(10);
        $storedPaths = [];

        try {
            $user = DB::transaction(function () use ($data, $request, $password, &$storedPaths) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'department' => $data['department'] ?? null,
                    'bio' => $data['notes'] ?? null,
                    'password' => Hash::make($password),
                ]);
                $user->role = 'teacher';
                $user->save();

                $teacher = $user->teacher()->create($this->buildTeacherPayload($data));
                $storedPaths = $this->storeTeacherUploads($request);

                if ($storedPaths) {
                    $teacher->update(array_filter($storedPaths, fn ($value) => !is_array($value) && $value !== null));
                    if (!empty($storedPaths['certificate_paths'])) {
                        $teacher->update(['certificate_paths' => $storedPaths['certificate_paths']]);
                    }
                }

                $this->syncTeacherAssignments($teacher, $data['subject_ids'] ?? [], $data['assignment_semester'] ?? null);

                return $user->load('teacher.subjects');
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->deleteStoredTeacherPaths($storedPaths);

            Log::error('Teacher create failed', [
                'email' => $request->input('email'),
                'teacher_id' => $request->input('teacher_id'),
                'error' => $exception->getMessage(),
            ]);

            return $this->teacherErrorResponse(
                $request,
                'Teacher could not be created. No partial data was saved.'
            );
        }

        $credentialsEmailSent = $this->sendTeacherCredentials($user, $password);
        $message = $credentialsEmailSent
            ? 'Teacher created successfully. Login credentials have been sent to the teacher email.'
            : 'Teacher created successfully, but the credentials email could not be sent. Check mail settings and logs.';

        return $this->teacherSuccessResponse($request, $message, $user, $credentialsEmailSent);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $teacher = User::where('role','teacher')->with(['teacher.subjects', 'teacher.subjectAssignments'])->findOrFail($id);
        
        // Check authorization
        // Admin can view all teachers
        // Teacher can view themselves
        if ($user->role === 'teacher' && $user->id !== $teacher->id) {
            return response()->json(['error' => 'You can only view your own profile'], 403);
        }
        
        $teacherProfile = $teacher->teacher;
        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'username' => $teacher->username,
            'phone' => $teacherProfile->phone ?? null,
            'alternate_email' => $teacherProfile->alternate_email ?? null,
            'secondary_phone' => $teacherProfile->secondary_phone ?? null,
            'national_id_number' => $teacherProfile->national_id_number ?? null,
            'date_of_birth' => optional($teacherProfile->date_of_birth)->format('Y-m-d'),
            'joining_date' => optional($teacherProfile->joining_date)->format('Y-m-d'),
            'years_of_experience' => $teacherProfile->years_of_experience ?? null,
            'specialization' => $teacherProfile->specialization ?? null,
            'employment_type' => $teacherProfile->employment_type ?? null,
            'previous_institution' => $teacherProfile->previous_institution ?? null,
            'certifications' => $teacherProfile->certifications ?? [],
            'emergency_contact_name' => $teacherProfile->emergency_contact_name ?? null,
            'emergency_contact_phone' => $teacherProfile->emergency_contact_phone ?? null,
            'emergency_relationship' => $teacherProfile->emergency_relationship ?? null,
            'department' => $teacherProfile->department ?? null,
            'qualification' => $teacherProfile->qualification ?? null,
            'address' => $teacherProfile->address ?? null,
            'staff_room_location' => $teacherProfile->staff_room_location ?? null,
            'employee_type' => $teacherProfile->employee_type ?? null,
            'work_shift' => $teacherProfile->work_shift ?? null,
            'timetable_assignment' => $teacherProfile->timetable_assignment ?? null,
            'salary' => $teacherProfile->salary ?? null,
            'bank_name' => $teacherProfile->bank_name ?? null,
            'bank_account_number' => $teacherProfile->bank_account_number ?? null,
            'tax_identification_number' => $teacherProfile->tax_identification_number ?? null,
            'blood_group' => $teacherProfile->blood_group ?? null,
            'medical_conditions' => $teacherProfile->medical_conditions ?? null,
            'emergency_notes' => $teacherProfile->emergency_notes ?? null,
            'resume_path' => $teacherProfile->resume_path ?? null,
            'certificate_paths' => $teacherProfile->certificate_paths ?? [],
            'id_proof_path' => $teacherProfile->id_proof_path ?? null,
            'access_level' => $teacherProfile->access_level ?? null,
            'profile_visibility' => $teacherProfile->profile_visibility ?? 'public',
            'social_links' => $teacherProfile->social_links ?? null,
            'notes' => $teacherProfile->notes ?? null,
            'bio' => $teacherProfile->bio ?? null,
            'status' => $teacherProfile->status ?? 'active',
            'profile_photo_path' => $teacherProfile->profile_photo_path ?? null,
            'teacher_code' => $teacherProfile->teacher_code ?? null,
            'gender' => $teacherProfile->gender ?? null,
            'subject_ids' => $teacherProfile?->subjects?->pluck('id')->values() ?? [],
            'assignment_semester' => $teacherProfile?->subjects?->first()?->pivot?->semester ?? null,
            'assigned_subjects' => $teacherProfile?->subjects?->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'label' => trim(($subject->subject_code ? $subject->subject_code . ' - ' : '') . $subject->subject_name),
                    'semester' => $subject->pivot->semester ?? $subject->semester ?? null,
                ];
            })->values() ?? [],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role','teacher')->with('teacher')->findOrFail($id);
        $data = $this->validateTeacherData($request, $user);
        $storedPaths = [];
        $oldPaths = [
            'profile_photo_path' => $user->teacher?->profile_photo_path,
            'resume_path' => $user->teacher?->resume_path,
            'id_proof_path' => $user->teacher?->id_proof_path,
            'certificate_paths' => $user->teacher?->certificate_paths ?? [],
        ];

        try {
            DB::transaction(function () use ($user, $data, $request, &$storedPaths) {
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->username = $data['username'] ?? null;
                $user->phone = $data['phone'] ?? null;
                $user->department = $data['department'] ?? null;
                $user->bio = $data['notes'] ?? null;
                $user->role = 'teacher';
                $user->save();

                $teacher = $user->teacher;

                if ($teacher) {
                    $teacher->update($this->buildTeacherPayload($data));
                } else {
                    $teacher = Teacher::create(array_merge(
                        ['user_id' => $user->id],
                        $this->buildTeacherPayload($data)
                    ));
                }

                $storedPaths = $this->storeTeacherUploads($request);
                if (!empty($storedPaths)) {
                    $teacher->update(array_filter($storedPaths, fn ($value) => !is_array($value) && $value !== null));
                    if (!empty($storedPaths['certificate_paths'])) {
                        $teacher->update(['certificate_paths' => $storedPaths['certificate_paths']]);
                    }
                }

                $this->syncTeacherAssignments($teacher, $data['subject_ids'] ?? [], $data['assignment_semester'] ?? null);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->deleteStoredTeacherPaths($storedPaths);

            Log::error('Teacher update failed', [
                'teacher_user_id' => $user->id,
                'email' => $request->input('email'),
                'teacher_id' => $request->input('teacher_id'),
                'error' => $exception->getMessage(),
            ]);

            return $this->teacherErrorResponse(
                $request,
                'Teacher could not be updated. No partial data was saved.'
            );
        }

        $this->deleteOldTeacherPaths($oldPaths, $storedPaths);

        $user->refresh()->load('teacher');

        return $this->teacherSuccessResponse($request, 'Teacher updated successfully', $user);
    }

    protected function validateTeacherData(Request $request, ?User $user = null): array
    {
        $teacherIdRule = Rule::unique('teachers', 'teacher_code');
        $usernameRule = Rule::unique('users', 'username');

        if ($user?->teacher) {
            $teacherIdRule->ignore($user->teacher->id);
        }
        if ($user) {
            $usernameRule->ignore($user->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'username' => ['nullable', 'string', 'max:50', $usernameRule],
            'phone' => ['required', 'digits:10'],
            'secondary_phone' => ['nullable', 'digits:10'],
            'teacher_id' => ['required', 'string', 'max:20', $teacherIdRule],
            'department' => ['required', 'string', 'max:100'],
            'alternate_email' => ['nullable', 'email', 'max:255'],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'joining_date' => ['required', 'date'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', Rule::in(['full-time', 'part-time', 'contract'])],
            'qualification' => ['nullable', 'string', 'max:100'],
            'previous_institution' => ['nullable', 'string', 'max:255'],
            'certifications_text' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'staff_room_location' => ['nullable', 'string', 'max:255'],
            'employee_type' => ['nullable', Rule::in(['permanent', 'temporary'])],
            'work_shift' => ['nullable', Rule::in(['morning', 'day', 'evening'])],
            'timetable_assignment' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'tax_identification_number' => ['nullable', 'string', 'max:100'],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'medical_conditions' => ['nullable', 'string'],
            'emergency_notes' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'digits:10'],
            'emergency_relationship' => ['nullable', 'string', 'max:100'],
            'access_level' => ['nullable', Rule::in(['basic', 'editor', 'manager', 'admin'])],
            'profile_visibility' => ['nullable', Rule::in(['public', 'private'])],
            'notes' => ['nullable', 'string'],
            'social_links' => ['nullable', 'string'],
            'assignment_semester' => ['nullable', 'string', 'max:20'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['nullable', 'integer', 'exists:subjects,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'On Leave', 'Retired'])],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'id_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'gender' => ['nullable', 'string', 'max:20'],
        ], [
            'teacher_id.unique' => 'This teacher ID is already in use.',
        ]);
    }

    protected function buildTeacherPayload(array $data): array
    {
        return [
            'teacher_code' => trim($data['teacher_id']),
            'qualification' => $data['qualification'] ?? null,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'alternate_email' => $data['alternate_email'] ?? null,
            'secondary_phone' => $data['secondary_phone'] ?? null,
            'national_id_number' => $data['national_id_number'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'joining_date' => $data['joining_date'] ?? null,
            'years_of_experience' => $data['years_of_experience'] ?? null,
            'specialization' => $data['specialization'] ?? null,
            'employment_type' => $data['employment_type'] ?? null,
            'previous_institution' => $data['previous_institution'] ?? null,
            'certifications' => $data['certifications_text'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'emergency_relationship' => $data['emergency_relationship'] ?? null,
            'department' => $data['department'] ?? null,
            'bio' => $data['bio'] ?? null,
            'address' => $data['address'] ?? null,
            'staff_room_location' => $data['staff_room_location'] ?? null,
            'employee_type' => $data['employee_type'] ?? null,
            'work_shift' => $data['work_shift'] ?? null,
            'timetable_assignment' => $data['timetable_assignment'] ?? null,
            'salary' => $data['salary'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'tax_identification_number' => $data['tax_identification_number'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'emergency_notes' => $data['emergency_notes'] ?? null,
            'access_level' => $data['access_level'] ?? null,
            'profile_visibility' => $data['profile_visibility'] ?? 'public',
            'social_links' => $data['social_links'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];
    }

    protected function storeTeacherUploads(Request $request): array
    {
        $paths = [];

        if ($request->hasFile('profile_photo')) {
            $paths['profile_photo_path'] = $request->file('profile_photo')->store('teachers/photos', 'public');
        }

        if ($request->hasFile('resume')) {
            $paths['resume_path'] = $request->file('resume')->store('teachers/resumes', 'public');
        }

        if ($request->hasFile('id_proof')) {
            $paths['id_proof_path'] = $request->file('id_proof')->store('teachers/id-proofs', 'public');
        }

        if ($request->hasFile('certificates')) {
            $paths['certificate_paths'] = collect($request->file('certificates'))
                ->filter()
                ->map(fn ($file) => $file->store('teachers/certificates', 'public'))
                ->values()
                ->all();
        }

        return $paths;
    }

    protected function syncTeacherAssignments(Teacher $teacher, array $subjectIds = [], ?string $semester = null): void
    {
        $payload = [];

        foreach (array_values(array_filter($subjectIds)) as $subjectId) {
            $subject = Subject::find($subjectId);
            if (!$subject) {
                continue;
            }

            $payload[$subject->id] = [
                'semester' => $semester ?: $subject->semester,
                'role' => 'primary',
                'notes' => null,
                'assigned_at' => now(),
            ];
        }

        $teacher->subjects()->sync($payload);
    }

    protected function deleteStoredTeacherPaths(array $paths): void
    {
        $disk = Storage::disk('public');

        foreach (['profile_photo_path', 'resume_path', 'id_proof_path'] as $key) {
            if (!empty($paths[$key])) {
                $disk->delete($paths[$key]);
            }
        }

        foreach (($paths['certificate_paths'] ?? []) as $path) {
            if ($path) {
                $disk->delete($path);
            }
        }
    }

    protected function deleteOldTeacherPaths(array $oldPaths, array $newPaths): void
    {
        $disk = Storage::disk('public');

        foreach (['profile_photo_path', 'resume_path', 'id_proof_path'] as $key) {
            if (!empty($newPaths[$key]) && !empty($oldPaths[$key]) && $oldPaths[$key] !== $newPaths[$key]) {
                $disk->delete($oldPaths[$key]);
            }
        }

        if (!empty($newPaths['certificate_paths']) && !empty($oldPaths['certificate_paths'])) {
            foreach ($oldPaths['certificate_paths'] as $path) {
                if ($path) {
                    $disk->delete($path);
                }
            }
        }
    }

    protected function sendTeacherCredentials(User $user, string $password): bool
    {
        try {
            $user->notify(new TeacherAccountNotification($password));
            return true;
        } catch (\Throwable $exception) {
            Log::error('Failed to send teacher notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function teacherSuccessResponse(Request $request, string $message, User $user, ?bool $credentialsEmailSent = null)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'email_sent' => $credentialsEmailSent,
                'teacher' => $user,
            ]);
        }

        return redirect()->route('admin.teachers')->with(
            $credentialsEmailSent === false ? 'warning' : 'success',
            $message
        );
    }

    protected function teacherErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }

    public function destroy($id)
    {
        try {
            $teacher = \App\Models\User::where('role','teacher')->findOrFail($id);
            $teacher->delete();
            
            // Always return JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Teacher deleted successfully'
                ]);
            }

            return redirect()->route('admin.teachers')->with('success', 'Teacher removed');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting teacher: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting teacher: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.teachers')->with('error', 'Error deleting teacher');
        }
    }

    public function export(Request $request)
    {
        try {
            $user = auth()->user();
            $builder = User::where('role', 'teacher')->with('teacher');

            // Apply search filter
            if ($q = $request->input('search')) {
                $builder->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            }

            // Apply status filter
            if ($status = $request->input('status')) {
                $builder->whereHas('teacher', function($s) use ($status) {
                    $s->where('status', $status);
                });
            }

            // Apply course/department filter
            if ($course = $request->input('course')) {
                $builder->whereHas('teacher', function($s) use ($course) {
                    $s->where('department', $course);
                });
            }

            $rows = $builder->orderBy('created_at', 'desc')->get();

            // Prepare filter information
            $filterInfo = [];
            if ($request->input('search')) {
                $filterInfo[] = 'Search: ' . $request->input('search');
            }
            if ($request->input('status')) {
                $filterInfo[] = 'Status: ' . $request->input('status');
            }
            if ($request->input('course')) {
                $filterInfo[] = 'Course/Department: ' . $request->input('course');
            }

            $filename = 'teachers_export_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $columns = ['ID', 'Name', 'Teacher ID', 'Email', 'Role', 'Course/Department', 'Status'];

            $callback = function() use ($rows, $columns, $filterInfo) {
                $out = fopen('php://output', 'w');
                
                if (!$out) {
                    throw new \Exception('Failed to open output stream');
                }
                
                // Write BOM for UTF-8 CSV (Excel compatibility)
                fwrite($out, "\xEF\xBB\xBF");
                
                // Write metadata header
                fputcsv($out, ['TEACHER EXPORT REPORT']);
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
                        $r->teacher->teacher_code ?? '',
                        $r->email,
                        ucfirst($r->role),
                        $r->teacher->department ?? '',
                        $r->teacher->status ?? 'active',
                    ];
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Teacher export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate export'], 500);
        }
    }

    public function print($id)
    {
        $teacher = User::where('role','teacher')->with('teacher')->findOrFail($id);
        // Convert image to base64 for PDF
        $teacher->photo_base64 = $this->getImageBase64($teacher);
        return view('admin.partials.teacher-print', compact('teacher'));
    }

    public function download($id)
    {
        try {
            $teacher = User::where('role','teacher')->with('teacher')->findOrFail($id);
            // Convert image to base64 for PDF
            $teacher->photo_base64 = $this->getImageBase64($teacher);
            $pdf = Pdf::loadView('admin.partials.teacher-print', compact('teacher'));
            return $pdf->download('teacher_' . $teacher->id . '_' . Str::slug($teacher->name) . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Teacher download error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }

    private function getImageBase64($user)
    {
        $path = null;
        
        if ($user->teacher && $user->teacher->profile_photo_path) {
            $path = $user->teacher->profile_photo_path;
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

    /**
     * Print list of teachers
     */
    public function printList(Request $request)
    {
        $query = $request->input('q');
        $status = $request->input('status');
        $department = $request->input('department');

        $teachersQuery = User::where('role', 'teacher')
            ->with('teacher')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->whereHas('teacher', function ($t) use ($status) {
                    $t->where('status', $status);
                });
            })
            ->when($department, function ($q) use ($department) {
                $q->whereHas('teacher', function ($t) use ($department) {
                    $t->where('department', $department);
                });
            })
            ->orderBy('name');

        $teachers = $teachersQuery->get();
        $college = \App\Models\Department::first();

        return view('admin.print.teachers-list', compact('teachers', 'college'));
    }
}
