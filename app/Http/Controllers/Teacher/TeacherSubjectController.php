<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $teacher = $user->teacher;
          
        // Auto-create teacher record if it doesn't exist
        if (!$teacher && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => 'TCH-' . $user->id,
                'status' => 'active',
            ]);
        }
        
        if (!$teacher) {
            return view('teacher.subjects', [
                'subjectAssignments' => collect([]),
                'courses' => collect([]),
                'availableSemesters' => [],
            ]);
        }

        // Get subjects assigned to this teacher from BOTH sources:
        // 1. From the pivot table (subject_teacher)
        // 2. From legacy teacher_id field on subjects table
        
        \Log::info('Starting teacher subjects index', ['teacher_id' => $teacher->id, 'user_name' => $user->name]);
        
        // Get from pivot table (and fallback to user ID mapping if data was inserted incorrectly)
        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with(['subject'])
            ->get();
        
        \Log::info('Pivot assignments fetched', ['count' => $pivotAssignments->count()]);
        
        // Get from legacy teacher_id field if the column exists
        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }
        
        \Log::info('Legacy subjects fetched', ['count' => $legacySubjects->count()]);
        
        // Convert legacy subjects to a lightweight assignment object for unified processing
        $legacyAssignments = $legacySubjects->map(function ($subject) use ($teacher) {
            $model = new \stdClass();
            $model->assignment_id = 'legacy_' . $subject->id;
            $model->subject_id = $subject->id;
            $model->teacher_id = $teacher->id;
            $model->semester = $subject->semester;
            $model->role = 'primary';
            $model->created_at = $subject->updated_at ?? $subject->created_at ?? now();
            $model->subject = $subject;
            return $model;
        });
        
        \Log::info('Legacy assignments converted', ['count' => $legacyAssignments->count()]);
        
        // Combine both (pivot table has priority, then add legacy assignments not already in pivot)
        $allAssignments = $pivotAssignments->concat($legacyAssignments)
            ->unique('subject_id');
        
        \Log::info('Combined assignments', ['count' => $allAssignments->count()]);
        
        $rawResults = $allAssignments->values()->sortBy('semester');
        
        \Log::info('Raw results sorted', ['count' => $rawResults->count()]);

        $subjectAssignments = $rawResults->map(function ($assignment) {
                $subject = $assignment->subject;
                
                $studentCount = TeacherSubjectRoster::studentCountForSubject($subject->id);
                
                // Get attendance stats for this subject
                $attendanceStats = DB::table('attendance')
                    ->where('subject_id', $subject->id)
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
                    'course_id' => $subject->course_id ?? null,
                    'role' => $assignment->role,
                    'student_count' => $studentCount,
                    'attendance_rate' => $attendanceRate,
                    'created_at' => $assignment->created_at,
                ];
            });

        // Available semesters for dropdown (only semesters that exist in assigned subjects)
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

        // Debug: Log the mapped results
        \Log::info('Mapped subject assignments', [
            'count' => $subjectAssignments->count(),
        ]);

        // Apply semester filter
        $semester = $request->get('semester');
        if ($semester) {
            $subjectAssignments = $subjectAssignments->filter(function ($assignment) use ($semester) {
                return $assignment['semester'] == $semester;
            })->values();
        }

        // Apply search filter
        $search = $request->get('q');
        if ($search) {
            $subjectAssignments = $subjectAssignments->filter(function ($assignment) use ($search) {
                $searchLower = strtolower($search);
                return str_contains(strtolower($assignment['subject_name']), $searchLower) ||
                    str_contains(strtolower($assignment['subject_code']), $searchLower) ||
                    str_contains(strtolower($assignment['course_name']), $searchLower);
            })->values();
        }

        // Get unique courses from teacher's subjects for filter dropdown - from both sources
        $pivotCourses = $teacher->subjectAssignments()
            ->with('subject')
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->subject->course_id ?? $assignment->id,
                    'name' => $assignment->subject->category ?? $assignment->subject->subject_name,
                ];
            });
        
        $legacyCourses = $legacySubjects->map(function ($subject) {
            return [
                'id' => $subject->course_id ?? $subject->id,
                'name' => $subject->category ?? $subject->subject_name,
            ];
        });

        $allCourses = $pivotCourses->concat($legacyCourses)
            ->unique('id')
            ->filter()
            ->values();

        $courses = $allCourses;

        // Debug: Final result
        \Log::info('Final subject assignments before view', [
            'count' => $subjectAssignments->count(),
        ]);

        return view('teacher.subjects', compact('subjectAssignments', 'courses', 'availableSemesters'));
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

        // Verify the teacher is assigned to this subject - check both pivot and legacy
        // If ID starts with 'legacy_', it's a legacy assignment
        if (strpos($id, 'legacy_') === 0) {
            $subjectId = str_replace('legacy_', '', $id);
            $subject = Subject::where('id', $subjectId)
                ->where('teacher_id', $teacher->id)
                ->first();
            
            if (!$subject) {
                return redirect()->route('teacher.subjects')->with('error', 'Subject not found or not assigned to you.');
            }
            
            // Create a pseudo-assignment object for legacy subjects
            $assignment = new \stdClass();
            $assignment->assignment_id = $id;
            $assignment->subject_id = $subject->id;
            $assignment->teacher_id = $teacher->id;
            $assignment->semester = $subject->semester;
            $assignment->role = 'primary';
            $assignment->subject = $subject;
        } else {
            // Regular pivot table assignment
            $assignment = $teacher->subjectAssignments()
                ->where('id', $id)
                ->with('subject')
                ->first();
        }

        if (!$assignment) {
            return redirect()->route('teacher.subjects')->with('error', 'Subject not found or not assigned to you.');
        }

        $subject = $assignment->subject;
        
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
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.subjects')->with('error', 'Teacher profile not found.');
        }

        // Get subjects assigned to this teacher with details
        $subjectAssignmentsQuery = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with(['subject']);

        // Apply semester filter
        $semester = $request->get('semester');
        if ($semester) {
            $subjectAssignmentsQuery->where('semester', $semester);
        }

        $subjectAssignments = $subjectAssignmentsQuery->orderBy('semester', 'asc')
            ->get()
            ->map(function ($assignment) {
                $subject = $assignment->subject;
                
                $studentCount = TeacherSubjectRoster::studentCountForSubject($subject->id);
                
                // Get attendance stats for this subject
                $attendanceStats = DB::table('attendance')
                    ->where('subject_id', $subject->id)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
                    ')
                    ->first();
                
                $attendanceRate = $attendanceStats && $attendanceStats->total > 0
                    ? round(($attendanceStats->present_count / $attendanceStats->total) * 100, 1)
                    : 0;
                
                return [
                    'subject_name' => $subject->subject_name,
                    'subject_code' => $subject->subject_code,
                    'semester' => $assignment->semester ?? $subject->semester,
                    'course_name' => $subject->category ?? $subject->subject_name,
                    'role' => $assignment->role,
                    'student_count' => $studentCount,
                    'attendance_rate' => $attendanceRate,
                ];
            });

        // Apply search filter
        $search = $request->get('q');
        if ($search) {
            $subjectAssignments = $subjectAssignments->filter(function ($assignment) use ($search) {
                $searchLower = strtolower($search);
                return str_contains(strtolower($assignment['subject_name']), $searchLower) ||
                       str_contains(strtolower($assignment['subject_code']), $searchLower) ||
                       str_contains(strtolower($assignment['course_name']), $searchLower);
            })->values();
        }

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
}
