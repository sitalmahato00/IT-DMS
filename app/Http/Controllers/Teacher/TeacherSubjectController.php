<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\SubjectTeacher;
use App\Models\Subject;
use App\Support\TeacherSubjectRoster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TeacherSubjectController extends Controller
{
    /**
     * Display teacher's assigned subjects.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);
        
        if (!$teacher) {
            return view('teacher.subjects', [
                'subjectAssignments' => collect([]),
                'courses' => collect([]),
                'availableSemesters' => [],
            ]);
        }

        $subjectData = $this->getSubjectAssignmentsData($request, $user, $teacher);

        return view('teacher.subjects', [
            'subjectAssignments' => $subjectData['subjectAssignments'],
            'courses' => $subjectData['courses'],
            'availableSemesters' => $subjectData['availableSemesters'],
        ]);
    }

    /**
     * Display a specific subject's details.
     */
    public function show($id)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.subjects')->with('error', 'Teacher profile not found.');
        }

        $teacherIds = array_values(array_unique(array_filter([
            $teacher->id ?? null,
            $user->id ?? null,
        ])));

        // Resolve either an assignment ID or a direct subject ID.
        $assignment = null;
        $subject = null;

        // If ID starts with 'legacy_', it's a legacy assignment reference.
        if (strpos((string) $id, 'legacy_') === 0) {
            $subjectId = str_replace('legacy_', '', (string) $id);

            $subject = Subject::query()
                ->where('id', $subjectId)
                ->where(function ($q) use ($teacher) {
                    if (Schema::hasColumn('subjects', 'teacher_id')) {
                        $q->whereIn('teacher_id', [$teacher->id, auth()->id()]);
                    } else {
                        $q->whereRaw('1 = 1');
                    }
                })
                ->first();

            if (!$subject) {
                $subject = $teacher->subjects()
                    ->where('subjects.id', $subjectId)
                    ->first();
            }

            if ($subject) {
                $assignment = SubjectTeacher::query()
                    ->whereIn('teacher_id', $teacherIds)
                    ->where('subject_id', $subject->id)
                    ->with('subject')
                    ->orderByDesc('assigned_at')
                    ->orderByDesc('id')
                    ->first();
            }
        } else {
            // Prefer the pivot assignment ID used by the subjects list.
            $assignment = SubjectTeacher::query()
                ->whereIn('teacher_id', $teacherIds)
                ->where('id', $id)
                ->with('subject')
                ->first();

            // Fall back to direct subject ID used by other teacher screens.
            if (!$assignment) {
                $subject = $teacher->subjects()
                    ->where('subjects.id', $id)
                    ->first();

                if (!$subject && Schema::hasColumn('subjects', 'teacher_id')) {
                    $subject = Subject::query()
                        ->where('id', $id)
                        ->whereIn('teacher_id', [$teacher->id, auth()->id()])
                        ->first();
                }

                if ($subject) {
                    $assignment = SubjectTeacher::query()
                        ->whereIn('teacher_id', $teacherIds)
                        ->where('subject_id', $subject->id)
                        ->with('subject')
                        ->orderByDesc('assigned_at')
                        ->orderByDesc('id')
                        ->first();
                }
            }
        }

        if (!$assignment && !$subject) {
            return redirect()->route('teacher.subjects')->with('error', 'Subject not found or not assigned to you.');
        }

        if (!$subject && $assignment) {
            $subject = $assignment->subject;
        }

        if (!$subject) {
            return redirect()->route('teacher.subjects')->with('error', 'Subject not found or not assigned to you.');
        }

        if (!$assignment) {
            $assignment = new \stdClass();
            $assignment->assignment_id = 'subject_' . $subject->id;
            $assignment->subject_id = $subject->id;
            $assignment->teacher_id = $teacher->id;
            $assignment->semester = $subject->semester;
            $assignment->role = 'primary';
            $assignment->subject = $subject;
        } elseif (!$assignment->subject) {
            $assignment->subject = $subject;
        }
        
        $students = TeacherSubjectRoster::studentRowsForSubject($subject->id)
            ->map(function ($student) {
                return (object) [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'roll_no' => $student->roll_no,
                    'registration_number' => $student->registration_number,
                ];
            });

        // Get recent attendance for this subject
        $recentAttendance = DB::table('attendance')
            ->where('subject_id', $subject->id)
            ->where('attendance_type', 'class')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->groupBy('date');

        return view('teacher.subject-show', compact('assignment', 'subject', 'students', 'recentAttendance'));
    }

    /**
     * Export subjects to CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);
        
        if (!$teacher) {
            return redirect()->route('teacher.subjects')->with('error', 'Teacher profile not found.');
        }

        $subjectAssignments = $this->getSubjectAssignmentsData($request, $user, $teacher)['subjectAssignments'];

        // Create CSV
        $filename = 'subjects-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($subjectAssignments) {
            $handle = fopen('php://output', 'w');
            
            // Header row
            fputcsv($handle, ['Subject Name', 'Subject Code', 'Semester', 'Course', 'Role', 'Student Count', 'Attendance Rate (%)']);
            
            // Data rows
            foreach ($subjectAssignments as $assignment) {
                fputcsv($handle, [
                    $assignment['subject_name'],
                    $assignment['subject_code'],
                    $assignment['semester'],
                    $assignment['course_name'],
                    $assignment['role'],
                    $assignment['student_count'],
                    $assignment['attendance_rate'],
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Render the filtered subject list for the shared teacher print modal.
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);

        if (!$teacher) {
            return redirect()->route('teacher.subjects')->with('error', 'Teacher profile not found.');
        }

        $subjectData = $this->getSubjectAssignmentsData($request, $user, $teacher);
        $subjectAssignments = $subjectData['subjectAssignments'];
        $college = Department::first();
        $filters = [
            'semester' => $request->input('semester'),
            'search' => trim((string) $request->input('q', '')),
        ];

        return view('teacher.print.subjects-list', compact('subjectAssignments', 'college', 'filters'));
    }

    private function resolveTeacherProfile($user)
    {
        $teacher = $user->teacher;

        if (!$teacher && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => 'TCH-' . $user->id,
                'status' => 'active',
            ]);
        }

        return $teacher;
    }

    private function getSubjectAssignmentsData(Request $request, $user, $teacher): array
    {
        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with('subject')
            ->get();

        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }

        $legacyAssignments = $legacySubjects->map(function ($subject) use ($teacher) {
            $assignment = new \stdClass();
            $assignment->assignment_id = 'legacy_' . $subject->id;
            $assignment->subject_id = $subject->id;
            $assignment->teacher_id = $teacher->id;
            $assignment->semester = $subject->semester;
            $assignment->role = 'primary';
            $assignment->created_at = $subject->updated_at ?? $subject->created_at ?? now();
            $assignment->subject = $subject;

            return $assignment;
        });

        $rawResults = $pivotAssignments
            ->concat($legacyAssignments)
            ->filter(fn ($assignment) => !empty($assignment->subject))
            ->unique('subject_id')
            ->values()
            ->sortBy([
                fn ($assignment) => is_numeric($assignment->semester ?? ($assignment->subject->semester ?? null))
                    ? (int) ($assignment->semester ?? $assignment->subject->semester)
                    : 999,
                fn ($assignment) => strtolower((string) ($assignment->subject->subject_name ?? '')),
            ]);

        $subjectAssignments = $rawResults->map(function ($assignment) {
            $subject = $assignment->subject;
            $studentCount = TeacherSubjectRoster::studentCountForSubject($subject->id);

            $attendanceStats = DB::table('attendance')
                ->where('subject_id', $subject->id)
                ->where('attendance_type', 'class')
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
                ')
                ->first();

            $attendanceRate = $attendanceStats && $attendanceStats->total > 0
                ? round(($attendanceStats->present_count / $attendanceStats->total) * 100, 1)
                : 0;

            return [
                'id' => $subject->id,
                'assignment_id' => $assignment->assignment_id ?? $assignment->id,
                'subject_name' => $subject->subject_name,
                'subject_code' => $subject->subject_code,
                'semester' => $assignment->semester ?? $subject->semester,
                'course_name' => $subject->category ?? $subject->subject_name,
                'course_id' => $subject->course_id ?? $subject->id,
                'role' => $assignment->role ?? 'teacher',
                'student_count' => $studentCount,
                'attendance_rate' => $attendanceRate,
                'created_at' => $assignment->created_at ?? now(),
            ];
        });

        $availableSemesters = $rawResults
            ->map(function ($assignment) {
                $semester = $assignment->semester ?? ($assignment->subject->semester ?? null);
                return is_numeric($semester) ? (int) $semester : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $semester = $request->get('semester');
        if ($semester !== null && $semester !== '') {
            $subjectAssignments = $subjectAssignments
                ->filter(fn ($assignment) => (string) ($assignment['semester'] ?? '') === (string) $semester)
                ->values();
        }

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $subjectAssignments = $subjectAssignments
                ->filter(function ($assignment) use ($search) {
                    $searchLower = strtolower($search);
                    return str_contains(strtolower($assignment['subject_name']), $searchLower)
                        || str_contains(strtolower($assignment['subject_code']), $searchLower)
                        || str_contains(strtolower($assignment['course_name']), $searchLower);
                })
                ->values();
        }

        $courses = $rawResults
            ->map(function ($assignment) {
                $subject = $assignment->subject;
                return [
                    'id' => $subject->course_id ?? $subject->id,
                    'name' => $subject->category ?? $subject->subject_name,
                ];
            })
            ->filter(fn ($course) => !empty($course['name']))
            ->unique('id')
            ->values();

        return [
            'subjectAssignments' => $subjectAssignments,
            'courses' => $courses,
            'availableSemesters' => $availableSemesters,
        ];
    }
}

