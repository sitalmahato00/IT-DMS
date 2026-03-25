<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
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

        return view('admin.teachers', compact('teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTeacherData($request);
        $password = Str::random(10);
        $storedPhotoPath = null;

        try {
            $user = DB::transaction(function () use ($data, $request, $password, &$storedPhotoPath) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($password),
                    'role' => 'teacher',
                ]);

                $teacher = $user->teacher()->create($this->buildTeacherPayload($data));

                if ($request->hasFile('profile_photo')) {
                    $storedPhotoPath = $request->file('profile_photo')->store('profiles', 'public');
                    $teacher->update([
                        'profile_photo_path' => $storedPhotoPath,
                    ]);
                }

                return $user->load('teacher');
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            if ($storedPhotoPath) {
                Storage::disk('public')->delete($storedPhotoPath);
            }

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
        $teacher = User::where('role','teacher')->with('teacher')->findOrFail($id);
        
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
            'phone' => $teacherProfile->phone ?? null,
            'department' => $teacherProfile->department ?? null,
            'qualification' => $teacherProfile->qualification ?? null,
            'address' => $teacherProfile->address ?? null,
            'bio' => $teacherProfile->bio ?? null,
            'status' => $teacherProfile->status ?? 'active',
            'profile_photo_path' => $teacherProfile->profile_photo_path ?? null,
            'teacher_code' => $teacherProfile->teacher_code ?? null,
            'gender' => $teacherProfile->gender ?? null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role','teacher')->with('teacher')->findOrFail($id);
        $data = $this->validateTeacherData($request, $user);
        $storedPhotoPath = null;
        $oldPhotoPath = $user->teacher?->profile_photo_path;

        try {
            DB::transaction(function () use ($user, $data, $request, &$storedPhotoPath) {
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => 'teacher',
                ]);

                $teacher = $user->teacher;

                if ($teacher) {
                    $teacher->update($this->buildTeacherPayload($data));
                } else {
                    $teacher = Teacher::create(array_merge(
                        ['user_id' => $user->id],
                        $this->buildTeacherPayload($data)
                    ));
                }

                if ($request->hasFile('profile_photo')) {
                    $storedPhotoPath = $request->file('profile_photo')->store('profiles', 'public');
                    $teacher->update([
                        'profile_photo_path' => $storedPhotoPath,
                    ]);
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            if ($storedPhotoPath) {
                Storage::disk('public')->delete($storedPhotoPath);
            }

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

        if ($storedPhotoPath && $oldPhotoPath && $oldPhotoPath !== $storedPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        $user->refresh()->load('teacher');

        return $this->teacherSuccessResponse($request, 'Teacher updated successfully', $user);
    }

    protected function validateTeacherData(Request $request, ?User $user = null): array
    {
        $teacherIdRule = Rule::unique('teachers', 'teacher_code');

        if ($user?->teacher) {
            $teacherIdRule->ignore($user->teacher->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['required', 'digits:10'],
            'teacher_id' => ['required', 'string', 'max:20', $teacherIdRule],
            'department' => ['required', 'string', 'max:100'],
            'qualification' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'On Leave', 'Retired'])],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
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
            'department' => $data['department'] ?? null,
            'bio' => $data['bio'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];
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
