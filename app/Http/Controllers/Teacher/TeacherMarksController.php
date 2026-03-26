<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Course;
use App\Helpers\NepaliContentHelper;
use App\Support\TeacherSubjectRoster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Traits\LogsActivity;

class TeacherMarksController extends Controller
{
    private function getTeacherSubjectIds()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [];
        }
        
        return SubjectTeacher::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();
    }

    /**
     * Display marks for teacher's subjects - using new simplified view
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return view('teacher.marks', [
                    'students' => collect([]),
                    'subjects' => collect([]),
                    'semesters' => collect([]),
                    'academicYears' => collect([]),
                    'assessments' => [],
                    'selectedCategory' => 'assessment',
                    'currentFilters' => [],
                ]);
            }
            
            $subjectIds = $this->getTeacherSubjectIds();
            
            if (empty($subjectIds)) {
                return view('teacher.marks', [
                    'students' => collect([]),
                    'subjects' => collect([]),
                    'semesters' => collect([]),
                    'academicYears' => collect([]),
                    'assessments' => [],
                    'selectedCategory' => 'assessment',
                    'currentFilters' => [],
                ]);
            }
            
            // Get filter parameters
            $category = $request->get('category', 'assessment');
            $semester = $request->get('semester', '');
            $subjectId = $request->get('subject_id', '');
            $academicYear = $request->get('academic_year', '');
            $assessmentId = $request->get('assessment_id', '');
            $search = $request->get('search', '');
            
            // Get unique semesters from teacher's subjects
            $semesters = Subject::whereIn('id', $subjectIds)
                ->distinct()
                ->pluck('semester')
                ->filter()
                ->sort()
                ->values();
            
            // Get unique academic years from exam marks for teacher's subjects
            $academicYears = ExamMark::whereHas('exam', function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->pluck('academic_year')
                ->filter()
                ->values();
            
            // Get unique assessments (Assessment category only)
            $assessments = Exam::whereIn('subject_id', $subjectIds)
                ->where('exam_category', 'assessment')
                ->distinct()
                ->orderBy('exam_date', 'asc')
                ->pluck('exam_name', 'id')
                ->toArray();
            
            // Get subjects for teacher (format for display)
            $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
                ->with('subject')
                ->get()
                ->map(function ($st) {
                    return [
                        'id' => $st->subject->id,
                        'name' => $st->subject->subject_name,
                        'code' => $st->subject->subject_code,
                        'semester' => $st->subject->semester,
                    ];
                })
                ->values();
            
            // Get students with marks if subject is selected
            $students = collect([]);
            if (!empty($subjectId)) {
                // Validate that this subject is owned by the teacher
                $subject = Subject::find($subjectId);
                if (!$subject || !in_array($subjectId, $subjectIds)) {
                    $subjectId = '';
                } else {
                    // Use the shared roster helper so subjects without pivot rows still resolve students.
                    $studentIds = TeacherSubjectRoster::studentIdsForSubject((int) $subjectId);

                    $studentQuery = Student::query()
                        ->whereIn('id', $studentIds)
                        ->with('user')
                        ->orderBy('roll_no');

                    $studentQuery->when($search, function ($q) use ($search) {
                        return $q->where(function ($studentQuery) use ($search) {
                            $studentQuery->whereHas('user', function ($uq) use ($search) {
                                $uq->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('email', 'like', '%' . $search . '%')
                                    ->orWhere('phone', 'like', '%' . $search . '%');
                            })->orWhere('roll_no', 'like', '%' . $search . '%');
                        });
                    });

                    $students = $studentQuery->paginate(25)->withQueryString();
                    
                    // Format student marks data
                    $students->getCollection()->transform(function ($student) use ($subjectId, $category, $academicYear, $assessmentId) {
                        // Get exams for this subject/category
                        $exams = Exam::where('subject_id', $subjectId)
                            ->where('exam_category', $category)
                            ->orderBy('exam_date', 'asc');
                        
                        // Filter by academic year if specified
                        if (!empty($academicYear)) {
                            $exams->where('academic_year', $academicYear);
                        }

                        if ($category === 'assessment' && !empty($assessmentId)) {
                            $exams->where('id', $assessmentId);
                        }
                        
                        $exams = $exams->get();
                        $attendancePercentage = $this->calculateAttendancePercentage($student, $subjectId);
                        
                        $totalMarks = 0;
                        $totalFull = 0;
                        $totalPass = 0;
                        $allPassed = true;
                        $assessments = [];
                        $components = [
                            'ti' => ['full' => 0, 'pass' => 0, 'obtained' => 0, 'is_pass' => null],
                            'te' => ['full' => 0, 'pass' => 0, 'obtained' => 0, 'is_pass' => null],
                            'pi' => ['full' => 0, 'pass' => 0, 'obtained' => 0, 'is_pass' => null],
                            'pe' => ['full' => 0, 'pass' => 0, 'obtained' => 0, 'is_pass' => null],
                        ];
                        
                        // Calculate CTEVT or Assessment marks
                        if ($category === 'ctevt') {
                            foreach ($exams as $exam) {
                                $mark = ExamMark::where('student_id', $student->id)
                                    ->where('exam_id', $exam->id)
                                    ->first();
                                
                                if ($mark) {
                                    $components['ti']['full'] += (float) ($mark->theory_internal_full_marks ?? $exam->theory_internal_max_marks ?? 0);
                                    $components['ti']['pass'] += (float) ($mark->theory_internal_pass_marks ?? $exam->theory_internal_pass_marks ?? 0);
                                    $components['ti']['obtained'] += (float) ($mark->theory_internal_marks ?? 0);

                                    $components['te']['full'] += (float) ($mark->theory_external_full_marks ?? $exam->theory_external_max_marks ?? 0);
                                    $components['te']['pass'] += (float) ($mark->theory_external_pass_marks ?? $exam->theory_external_pass_marks ?? 0);
                                    $components['te']['obtained'] += (float) ($mark->theory_external_marks ?? 0);

                                    $components['pi']['full'] += (float) ($mark->practical_internal_full_marks ?? $exam->practical_internal_max_marks ?? 0);
                                    $components['pi']['pass'] += (float) ($mark->practical_internal_pass_marks ?? $exam->practical_internal_pass_marks ?? 0);
                                    $components['pi']['obtained'] += (float) ($mark->practical_internal_marks ?? 0);

                                    $components['pe']['full'] += (float) ($mark->practical_external_full_marks ?? $exam->practical_external_max_marks ?? 0);
                                    $components['pe']['pass'] += (float) ($mark->practical_external_pass_marks ?? $exam->practical_external_pass_marks ?? 0);
                                    $components['pe']['obtained'] += (float) ($mark->practical_external_marks ?? 0);
                                }
                            }

                            foreach ($components as $key => $component) {
                                if ($component['full'] > 0) {
                                    $components[$key]['is_pass'] = $component['obtained'] >= $component['pass'];
                                }
                            }

                            $totalMarks = collect($components)->sum('obtained');
                            $totalFull = collect($components)->sum('full');
                            $totalPass = collect($components)->sum('pass');
                            $allPassed = collect($components)->every(function ($component) {
                                return $component['full'] <= 0 || $component['obtained'] >= $component['pass'];
                            });
                        } else {
                            // Assessment marks - track individual assessments
                            foreach ($exams as $exam) {
                                $mark = ExamMark::where('student_id', $student->id)
                                    ->where('exam_id', $exam->id)
                                    ->first();
                                
                                $obtained = $mark ? ($mark->marks_obtained ?? 0) : 0;
                                $full = $exam->full_marks ?? 0;
                                $pass = $exam->passing_marks ?? 0;
                                
                                $assessments[] = [
                                    'exam_id' => $exam->id,
                                    'exam_name' => $exam->exam_name,
                                    'exam_date' => $exam->exam_date,
                                    'obtained' => $obtained,
                                    'full' => $full,
                                    'pass' => $pass,
                                    'percentage' => $full > 0 ? round(($obtained / $full) * 100, 1) : 0,
                                    'is_passed' => $obtained >= $pass && $full > 0,
                                ];
                                
                                $totalMarks += $obtained;
                                $totalFull += $full;
                                $totalPass += $pass;
                                
                                if ($obtained < $pass) {
                                    $allPassed = false;
                                }
                            }
                        }
                        
                        return [
                            'id' => $student->id,
                            'roll_no' => $student->roll_no,
                            'name' => $student->user->name ?? 'N/A',
                            'attendance_percentage' => $attendancePercentage,
                            'total_marks' => $totalMarks,
                            'full_marks' => $totalFull,
                            'pass_marks' => $totalPass,
                            'is_passed' => $allPassed && $totalFull > 0,
                            'assessments' => $assessments,
                            'ti_full' => $components['ti']['full'],
                            'ti_pass' => $components['ti']['pass'],
                            'ti_obtained' => $components['ti']['obtained'],
                            'ti_is_pass' => $components['ti']['is_pass'],
                            'te_full' => $components['te']['full'],
                            'te_pass' => $components['te']['pass'],
                            'te_obtained' => $components['te']['obtained'],
                            'te_is_pass' => $components['te']['is_pass'],
                            'pi_full' => $components['pi']['full'],
                            'pi_pass' => $components['pi']['pass'],
                            'pi_obtained' => $components['pi']['obtained'],
                            'pi_is_pass' => $components['pi']['is_pass'],
                            'pe_full' => $components['pe']['full'],
                            'pe_pass' => $components['pe']['pass'],
                            'pe_obtained' => $components['pe']['obtained'],
                            'pe_is_pass' => $components['pe']['is_pass'],
                        ];
                    });
                }
            }
            
            return view('teacher.marks', [
                'students' => $students,
                'subjects' => $subjects,
                'semesters' => $semesters,
                'academicYears' => $academicYears,
                'assessments' => $assessments,
                'selectedCategory' => $category,
                'currentFilters' => [
                    'category' => $category,
                    'semester' => $semester,
                    'subject_id' => $subjectId,
                    'academic_year' => $academicYear,
                    'assessment_id' => $assessmentId,
                    'search' => $search,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher marks error: ' . $e->getMessage());
            return view('teacher.marks', [
                'students' => collect([]),
                'subjects' => collect([]),
                'semesters' => collect([]),
                'academicYears' => collect([]),
                'assessments' => [],
                'selectedCategory' => 'assessment',
                'currentFilters' => [],
            ]);
        }
    }

    /**
     * Display marksheets for all students in teacher's subjects
     */
    public function marksheets(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return view('teacher.marksheets', [
                    'students' => collect([]),
                    'subjects' => collect([]),
                    'semesters' => collect([]),
                    'academicYears' => collect([]),
                    'assessments' => [],
                    'filteredSubjects' => [],
                    'currentFilters' => [],
                ]);
            }
            
            $subjectIds = $this->getTeacherSubjectIds();
            
            if (empty($subjectIds)) {
                return view('teacher.marksheets', [
                    'students' => collect([]),
                    'subjects' => collect([]),
                    'semesters' => collect([]),
                    'academicYears' => collect([]),
                    'assessments' => [],
                    'filteredSubjects' => [],
                    'currentFilters' => [],
                ]);
            }
            
            // Get filter parameters
            $semester = $request->get('semester', '');
            $subjectId = $request->get('subject_id', '');
            $academicYear = $request->get('academic_year', '');
            $search = $request->get('search', '');
            $examCategory = $request->get('exam_category', '');
            $assessmentId = $request->get('assessment_id', '');
            
            // Get unique semesters from teacher's subjects
            $semesters = Subject::whereIn('id', $subjectIds)
                ->distinct()
                ->pluck('semester')
                ->filter()
                ->sort()
                ->values();
            
            // Get unique academic years from exam marks for teacher's subjects
            $academicYears = ExamMark::whereHas('exam', function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
                ->distinct()
                ->orderBy('academic_year', 'desc')
                ->pluck('academic_year')
                ->filter()
                ->values();
            
            // Get subjects for teacher (format for display)
            $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
                ->with('subject')
                ->get()
                ->map(function ($st) {
                    return [
                        'id' => $st->subject->id,
                        'name' => $st->subject->subject_name,
                        'code' => $st->subject->subject_code,
                        'semester' => $st->subject->semester,
                    ];
                })
                ->values();
            
            // Get filtered subjects based on selected semester
            $filteredSubjects = $subjects;
            if (!empty($semester)) {
                $filteredSubjects = $subjects->filter(function ($s) use ($semester) {
                    return $s['semester'] == $semester;
                })->values();
            }
            
            // Get assessments for the selected filters (only for Assessment category)
            $assessments = [];
            if ($examCategory === 'assessment' || empty($examCategory)) {
                $assessmentQuery = Exam::where('exam_category', 'assessment')
                    ->whereIn('subject_id', $subjectIds)
                    ->when(!empty($semester), function ($q) use ($semester) {
                        $q->where('semester', $semester);
                    })
                    ->when(!empty($academicYear), function ($q) use ($academicYear) {
                        $q->where('academic_year', $academicYear);
                    })
                    ->when(!empty($subjectId), function ($q) use ($subjectId) {
                        $q->where('subject_id', $subjectId);
                    });
                
                $assessments = $assessmentQuery->pluck('assessment_number', 'id')
                    ->filter()
                    ->toArray();
            }
            
            // Get students with marksheet data if subject is selected
            $students = collect([]);
            if (!empty($subjectId)) {
                // Validate that this subject is owned by the teacher
                $subject = Subject::find($subjectId);
                if (!$subject || !in_array($subjectId, $subjectIds)) {
                    $subjectId = '';
                } else {
                    // Get all exams for this subject, filtered by exam_category if specified
                    $studentQuery = Student::whereHas('subjects', function ($q) use ($subjectId) {
                        $q->where('subject_id', $subjectId);
                    })
                    ->with('user', 'examMarks');
                    
                    // Filter by academic year if specified
                    if (!empty($academicYear)) {
                        $studentQuery->whereHas('examMarks', function ($q) use ($academicYear) {
                            $q->where('academic_year', $academicYear);
                        });
                    }
                    
                    $studentQuery->when($search, function ($q) use ($search) {
                        return $q->whereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', '%' . $search . '%')
                              ->orWhere('email', 'like', '%' . $search . '%')
                              ->orWhere('phone', 'like', '%' . $search . '%');
                        })->orWhere('roll_no', 'like', '%' . $search . '%');
                    })
                    ->paginate(25);
                    
                    // Format student marksheet data
                    $students = $studentQuery->map(function ($student) use ($subjectId, $academicYear, $examCategory, $assessmentId) {
                        // Get exams for this subject, filtered by category if specified
                        $examsQuery = Exam::where('subject_id', $subjectId)
                            ->orderBy('exam_date', 'asc');
                        
                        // Filter by exam category if specified
                        if (!empty($examCategory)) {
                            $examsQuery->where('exam_category', $examCategory);
                        }
                        
                        // Filter by assessment ID if specified
                        if (!empty($assessmentId)) {
                            $examsQuery->where('id', $assessmentId);
                        }
                        
                        // Filter by academic year if specified
                        if (!empty($academicYear)) {
                            $examsQuery->where('academic_year', $academicYear);
                        }
                        
                        $exams = $examsQuery->get();
                        
                        $totalMarks = 0;
                        $totalFull = 0;
                        $totalPass = 0;
                        $allPassed = true;
                        $grade = 'N/A';
                        
                        // Calculate total marks from filtered exams
                        foreach ($exams as $exam) {
                            $mark = ExamMark::where('student_id', $student->id)
                                ->where('exam_id', $exam->id)
                                ->first();
                            
                            if ($mark) {
                                if ($exam->exam_category === 'ctevt') {
                                    $totalMarks += ($mark->theory_internal_marks ?? 0) +
                                                  ($mark->theory_external_marks ?? 0) +
                                                  ($mark->practical_internal_marks ?? 0) +
                                                  ($mark->practical_external_marks ?? 0);
                                    
                                    $totalFull += ($exam->theory_internal_max_marks ?? 0) +
                                               ($exam->theory_external_max_marks ?? 0) +
                                               ($exam->practical_internal_max_marks ?? 0) +
                                               ($exam->practical_external_max_marks ?? 0);
                                } else {
                                    // Assessment marks
                                    $totalMarks += ($mark->marks_obtained ?? 0);
                                    $totalFull += ($exam->full_marks ?? 0);
                                    $totalPass += ($exam->passing_marks ?? 0);
                                    
                                    if (($mark->marks_obtained ?? 0) < ($exam->passing_marks ?? 0)) {
                                        $allPassed = false;
                                    }
                                }
                            } else {
                                if ($exam->exam_category === 'ctevt') {
                                    $totalFull += ($exam->theory_internal_max_marks ?? 0) +
                                               ($exam->theory_external_max_marks ?? 0) +
                                               ($exam->practical_internal_max_marks ?? 0) +
                                               ($exam->practical_external_max_marks ?? 0);
                                } else {
                                    $totalFull += ($exam->full_marks ?? 0);
                                    $totalPass += ($exam->passing_marks ?? 0);
                                }
                                $allPassed = false;
                            }
                        }
                        
                        // Calculate grade
                        if ($totalFull > 0) {
                            $percentage = ($totalMarks / $totalFull) * 100;
                            if ($percentage >= 90) $grade = 'A+';
                            elseif ($percentage >= 80) $grade = 'A';
                            elseif ($percentage >= 70) $grade = 'B+';
                            elseif ($percentage >= 60) $grade = 'B';
                            elseif ($percentage >= 50) $grade = 'C+';
                            elseif ($percentage >= 40) $grade = 'C';
                            elseif ($percentage >= 35) $grade = 'D';
                            else $grade = 'F';
                        }
                        
                        return [
                            'id' => $student->id,
                            'roll_no' => $student->roll_no,
                            'name' => $student->user->name ?? 'N/A',
                            'total_marks' => $totalMarks,
                            'full_marks' => $totalFull,
                            'pass_marks' => $totalPass,
                            'is_passed' => $allPassed && $totalFull > 0,
                            'grade' => $grade,
                        ];
                    })->values();
                }
            }
            
            return view('teacher.marksheets', [
                'students' => $students,
                'subjects' => $subjects,
                'filteredSubjects' => $filteredSubjects,
                'semesters' => $semesters,
                'academicYears' => $academicYears,
                'assessments' => $assessments,
                'currentFilters' => [
                    'semester' => $semester,
                    'subject_id' => $subjectId,
                    'academic_year' => $academicYear,
                    'search' => $search,
                    'exam_category' => $examCategory,
                    'assessment_id' => $assessmentId,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher marksheets error: ' . $e->getMessage());
            return view('teacher.marksheets', [
                'students' => collect([]),
                'subjects' => collect([]),
                'semesters' => collect([]),
                'academicYears' => collect([]),
                'assessments' => [],
                'filteredSubjects' => [],
                'currentFilters' => [],
            ]);
        }
    }

    /**
     * Get students with marks based on filters
     */
    private function getStudentsWithMarks(Request $request, array $subjectIds, array $filters)
    {
        $filteredSubjectIds = $subjectIds;

        if (!empty($filters['program'])) {
            $filteredSubjectIds = Subject::whereIn('id', $filteredSubjectIds)
                ->where('category', $filters['program'])
                ->pluck('id')
                ->all();
        }

        if (!empty($filters['semester'])) {
            $filteredSubjectIds = Subject::whereIn('id', $filteredSubjectIds)
                ->where('semester', $filters['semester'])
                ->pluck('id')
                ->all();
        }

        if (!empty($filters['subject']) && in_array($filters['subject'], $filteredSubjectIds)) {
            $studentIds = TeacherSubjectRoster::studentIdsForSubject((int) $filters['subject']);
        } else {
            $studentIds = TeacherSubjectRoster::studentIdsForSubjects($filteredSubjectIds);
        }

        if (empty($studentIds)) {
            return collect([]);
        }

        // Build student query
        $studentsQuery = Student::with(['user', 'subjects'])
            ->whereIn('students.id', $studentIds);

        // Apply semester filter
        if (!empty($filters['semester'])) {
            $studentsQuery->where('semester', $filters['semester']);
        }

        // Apply batch filter
        if (!empty($filters['batch'])) {
            $studentsQuery->where('batch_year', $filters['batch']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $studentsQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('roll_no', 'like', "%{$search}%");
        }

        switch ($filters['sortBy'] ?? 'roll_no') {
            case 'name':
                $studentsQuery->join('users', 'users.id', '=', 'students.user_id')
                    ->orderBy('users.name')
                    ->select('students.*');
                break;
            case 'roll_no':
            default:
                $studentsQuery->orderBy('students.roll_no');
                break;
        }

        $students = $studentsQuery->paginate($filters['perPage']);

        // Get exam and subject info
        $exam = null;
        $subject = null;

        if (!empty($filters['exam'])) {
            $exam = Exam::with('subject')->find($filters['exam']);
        }

        if (!empty($filters['subject'])) {
            $subject = Subject::find($filters['subject']);
        }

        // Attach marks to each student
        $students->getCollection()->transform(function ($student) use ($exam, $subject, $filters) {
            $mark = null;
            
            if ($exam) {
                $markQuery = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $student->id);
                
                if ($subject) {
                    $markQuery->where('subject_id', $subject->id);
                }
                
                $mark = $markQuery->first();
            }

            // Calculate attendance percentage
            $attendancePercentage = $this->calculateAttendancePercentage($student, $subject);

            $student->exam_mark = $mark;
            $student->attendance_percentage = $attendancePercentage;
            $student->is_filled = $mark && $mark->isFilled();
            $student->result = $mark ? $mark->result : null;

            return $student;
        });

        return $students;
    }

    /**
     * Calculate attendance percentage for a student
     */
    private function calculateAttendancePercentage($student, $subject = null)
    {
        $query = DB::table('attendance')
            ->where('student_id', $student->id);

        $subjectId = is_numeric($subject) ? (int) $subject : ($subject->id ?? null);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $present = $query->where('status', 'present')->count();
        return round(($present / $total) * 100, 1);
    }

    /**
     * Get statistics
     */
    private function getStats(array $subjectIds, Request $request)
    {
        $examId = $request->get('exam');
        $subjectId = $request->get('subject');
        $status = $request->get('status');

        $query = ExamMark::whereIn('subject_id', $subjectIds);

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $totalStudents = $query->distinct('student_id')->count('student_id');
        $filledMarks = clone $query;
        $filledCount = $filledMarks->whereNotNull('marks_obtained')
            ->orWhereNotNull('theory_internal_marks')
            ->orWhereNotNull('theory_external_marks')
            ->orWhereNotNull('practical_internal_marks')
            ->orWhereNotNull('practical_external_marks')
            ->distinct('student_id')
            ->count('student_id');

        $emptyCount = $totalStudents - $filledCount;

        // Calculate pass/fail
        $passedQuery = clone $query;
        $passedCount = $passedQuery->where(function($q) {
            $q->whereRaw(
                "(COALESCE(theory_internal_marks, 0) + COALESCE(theory_external_marks, 0) + 
                  COALESCE(practical_internal_marks, 0) + COALESCE(practical_external_marks, 0)) >= 
                 (COALESCE(theory_internal_pass_marks, 0) + COALESCE(theory_external_pass_marks, 0) + 
                  COALESCE(practical_internal_pass_marks, 0) + COALESCE(practical_external_pass_marks, 0))"
            )->orWhereRaw(
                "COALESCE(marks_obtained, 0) >= COALESCE(passing_marks, 0)"
            );
        })->distinct('student_id')->count('student_id');

        $failedCount = $totalStudents - $passedCount - $emptyCount;
        if ($failedCount < 0) $failedCount = 0;

        return [
            'total' => $totalStudents,
            'filled' => $filledCount,
            'empty' => $emptyCount,
            'passed' => $passedCount,
            'failed' => $failedCount,
        ];
    }

    /**
     * Get empty stats
     */
    private function getEmptyStats()
    {
        return [
            'total' => 0,
            'filled' => 0,
            'empty' => 0,
            'passed' => 0,
            'failed' => 0,
        ];
    }

    /**
     * Get exams by category (AJAX)
     */
    public function getExamsByCategory(Request $request)
    {
        $category = $request->get('category', 'assessment');
        $subjectId = $request->get('subject_id');
        $assessmentNumber = $request->get('assessment_number');

        $subjectIds = $this->getTeacherSubjectIds();

        $examQuery = Exam::whereIn('subject_id', $subjectIds)
            ->where('exam_category', $category)
            ->orderBy('exam_date', 'desc');

        if ($subjectId) {
            $examQuery->where('subject_id', $subjectId);
        }

        if ($category === 'assessment' && $assessmentNumber !== null && $assessmentNumber !== '') {
            $examQuery->where('assessment_number', $assessmentNumber);
        }

        $exams = $examQuery->get(['id', 'exam_name', 'exam_category', 'full_marks', 'passing_marks']);

        return response()->json([
            'success' => true,
            'exams' => $exams,
        ]);
    }

    /**
     * Get subjects by category (AJAX)
     */
    public function getSubjectsByCategory(Request $request)
    {
        $category = $request->get('category', 'assessment');
        $semester = $request->get('semester');

        $subjectIds = $this->getTeacherSubjectIds();

        $subjectQuery = Subject::whereIn('id', $subjectIds)
            ->where('category', $category);

        if ($semester) {
            $subjectQuery->where('semester', $semester);
        }

        $subjects = $subjectQuery->get(['id', 'subject_name', 'subject_code', 'semester']);

        return response()->json([
            'success' => true,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store/update marks
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'marks' => 'required|array',
            ]);

            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return response()->json(['error' => 'Teacher profile not found'], 404);
            }

            $subjectIds = $this->getTeacherSubjectIds();

            $exam = Exam::findOrFail($validated['exam_id']);

            if (!in_array($exam->subject_id, $subjectIds)) {
                return response()->json(['error' => 'Subject not assigned to you'], 403);
            }

            $now = now()->toDateTimeString();
            $category = $exam->exam_category;
            $saved = 0;

            DB::transaction(function () use ($validated, $exam, $category, $subjectIds, $teacher, $user, $now, &$saved) {
                foreach ($validated['marks'] as $item) {
                    $studentId = $item['student_id'];
                    
                    // Determine subject_id
                    $subjectId = $exam->subject_id;

                    // Validation for entered marks
                    if ($category === 'ctevt') {
                        $maxTi = $exam->theory_internal_max_marks ?? 0;
                        $maxTe = $exam->theory_external_max_marks ?? 0;
                        $maxPi = $exam->practical_internal_max_marks ?? 0;
                        $maxPe = $exam->practical_external_max_marks ?? 0;

                        if (isset($item['ti_marks']) && $item['ti_marks'] > $maxTi) {
                            throw new \Exception("TI marks cannot exceed full marks ({$maxTi})");
                        }
                        if (isset($item['te_marks']) && $item['te_marks'] > $maxTe) {
                            throw new \Exception("TE marks cannot exceed full marks ({$maxTe})");
                        }
                        if (isset($item['pi_marks']) && $item['pi_marks'] > $maxPi) {
                            throw new \Exception("PI marks cannot exceed full marks ({$maxPi})");
                        }
                        if (isset($item['pe_marks']) && $item['pe_marks'] > $maxPe) {
                            throw new \Exception("PE marks cannot exceed full marks ({$maxPe})");
                        }
                    } else {
                        $fullMarks = $exam->full_marks ?? 0;
                        if (isset($item['marks']) && $item['marks'] > $fullMarks) {
                            throw new \Exception("Obtained marks cannot exceed full marks ({$fullMarks})");
                        }
                    }

                    $markData = [
                        'exam_id' => $exam->id,
                        'subject_id' => $subjectId,
                        'student_id' => $studentId,
                        'academic_year' => $exam->academic_year,
                        'academic_year_bs' => $exam->academic_year_bs,
                        'assessment_number' => $category === 'assessment' ? $exam->assessment_number : null,
                        'entered_by' => $teacher->id,
                        'graded_by' => $user->id,
                        'graded_at' => $now,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ];

                    if ($category === 'ctevt') {
                        // CTEVT marks with components
                        $markData['theory_internal_marks'] = floatval($item['ti_marks'] ?? 0);
                        $markData['theory_external_marks'] = floatval($item['te_marks'] ?? 0);
                        $markData['practical_internal_marks'] = floatval($item['pi_marks'] ?? 0);
                        $markData['practical_external_marks'] = floatval($item['pe_marks'] ?? 0);

                        $markData['theory_internal_pass_marks'] = $exam->theory_internal_pass_marks ?? 0;
                        $markData['theory_external_pass_marks'] = $exam->theory_external_pass_marks ?? 0;
                        $markData['practical_internal_pass_marks'] = $exam->practical_internal_pass_marks ?? 0;
                        $markData['practical_external_pass_marks'] = $exam->practical_external_pass_marks ?? 0;

                        // Calculate total for CTEVT
                        $totalMarks = $markData['theory_internal_marks'] +
                                     $markData['theory_external_marks'] +
                                     $markData['practical_internal_marks'] +
                                     $markData['practical_external_marks'];

                        $fullMarks = ($exam->theory_internal_max_marks ?? 0) +
                                    ($exam->theory_external_max_marks ?? 0) +
                                    ($exam->practical_internal_max_marks ?? 0) +
                                    ($exam->practical_external_max_marks ?? 0);

                        $markData['marks_obtained'] = $totalMarks;
                        $markData['full_marks'] = $fullMarks;
                        $markData['passing_marks'] = $fullMarks * 0.4;
                    } else {
                        // Assessment marks (simple)
                        $markData['marks_obtained'] = floatval($item['marks'] ?? 0);
                        $markData['full_marks'] = $exam->full_marks;
                        $markData['passing_marks'] = $exam->passing_marks;
                    }

                    // Calculate percentage and grade
                    if ($markData['full_marks'] > 0) {
                        $markData['percentage'] = round(($markData['marks_obtained'] / $markData['full_marks']) * 100, 2);
                    }

                    // Track whether marks are filled/empty/absent (pass/fail is derived)
                    $markData['marks_status'] = 'filled';

                    // Determine grade
                    $markData['grade'] = $this->calculateGrade($markData['marks_obtained'], $markData['full_marks']);

                    DB::table('exam_marks')
                        ->updateOrInsert(
                            [
                                'student_id' => $studentId,
                                'exam_id' => $exam->id,
                                'subject_id' => $subjectId,
                            ],
                            $markData
                        );

                    $saved++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Marks saved successfully!',
                'saved' => $saved,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Teacher marks store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update single mark (AJAX for inline editing)
     */
    public function update(Request $request, $id)
    {
        try {
            $mark = ExamMark::findOrFail($id);

            // Verify teacher has access to this subject
            $subjectIds = $this->getTeacherSubjectIds();
            if (!in_array($mark->subject_id, $subjectIds)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'marks_obtained' => 'nullable|numeric|min:0',
                'ti_marks' => 'nullable|numeric|min:0',
                'te_marks' => 'nullable|numeric|min:0',
                'pi_marks' => 'nullable|numeric|min:0',
                'pe_marks' => 'nullable|numeric|min:0',
            ]);

            $exam = $mark->exam;
            $category = $exam->exam_category;

            // Validate against exam full marks
            if ($category === 'ctevt') {
                $maxTi = $exam->theory_internal_max_marks ?? 0;
                $maxTe = $exam->theory_external_max_marks ?? 0;
                $maxPi = $exam->practical_internal_max_marks ?? 0;
                $maxPe = $exam->practical_external_max_marks ?? 0;

                if (isset($validated['ti_marks']) && $validated['ti_marks'] > $maxTi) {
                    return response()->json(['success' => false, 'message' => "TI marks cannot exceed full marks ({$maxTi})"], 422);
                }
                if (isset($validated['te_marks']) && $validated['te_marks'] > $maxTe) {
                    return response()->json(['success' => false, 'message' => "TE marks cannot exceed full marks ({$maxTe})"], 422);
                }
                if (isset($validated['pi_marks']) && $validated['pi_marks'] > $maxPi) {
                    return response()->json(['success' => false, 'message' => "PI marks cannot exceed full marks ({$maxPi})"], 422);
                }
                if (isset($validated['pe_marks']) && $validated['pe_marks'] > $maxPe) {
                    return response()->json(['success' => false, 'message' => "PE marks cannot exceed full marks ({$maxPe})"], 422);
                }

                // Update CTEVT components
                if (isset($validated['ti_marks'])) {
                    $mark->theory_internal_marks = floatval($validated['ti_marks']);
                }
                if (isset($validated['te_marks'])) {
                    $mark->theory_external_marks = floatval($validated['te_marks']);
                }
                if (isset($validated['pi_marks'])) {
                    $mark->practical_internal_marks = floatval($validated['pi_marks']);
                }
                if (isset($validated['pe_marks'])) {
                    $mark->practical_external_marks = floatval($validated['pe_marks']);
                }

                // Recalculate total
                    $totalMarks = $mark->calculateTotalMarks();

                    // Determine full marks (mark stored or exam default)
                    $fullMarks = $mark->effective_full_marks;

                    // Determine passing marks (mark stored or exam default)
                    $passingMarks = $mark->effective_passing_marks;
                } else {
                // Validate against exam full marks for assessment
                $fullMarks = $exam->full_marks ?? 0;
                if (isset($validated['marks_obtained']) && $validated['marks_obtained'] > $fullMarks) {
                    return response()->json(['success' => false, 'message' => "Obtained marks cannot exceed full marks ({$fullMarks})"], 422);
                }

                    if (isset($validated['marks_obtained'])) {
                        $mark->marks_obtained = floatval($validated['marks_obtained']);
                    }
                    $totalMarks = floatval($mark->marks_obtained ?? 0);
                    $fullMarks = $mark->effective_full_marks;
                    $passingMarks = $mark->effective_passing_marks;
                }

                // Recalculate percentage
                $percentage = 0;
                if ($fullMarks > 0) {
                    $percentage = round(($totalMarks / $fullMarks) * 100, 2);
                }

                // Update the mark record
                $mark->marks_obtained = $totalMarks;
                $mark->full_marks = $fullMarks;
                $mark->passing_marks = $passingMarks;
                
                // Update grade and status
                $mark->grade = $this->calculateGrade($totalMarks, $fullMarks);
                $passFailStatus = $totalMarks >= $passingMarks ? 'passed' : 'failed';
                $mark->marks_status = $mark->isAbsent() ? 'absent' : 'filled';
            $mark->graded_at = now();

            $mark->save();

            return response()->json([
                'success' => true,
                'message' => 'Marks updated successfully!',
                'mark' => [
                    'id' => $mark->id,
                    'marks_obtained' => $mark->marks_obtained,
                    'full_marks' => $mark->full_marks,
                    'passing_marks' => $mark->passing_marks,
                    'percentage' => $percentage,
                    'grade' => $mark->grade,
                    'status' => $passFailStatus,
                    'marks_status' => $mark->marks_status,
                    'result' => $mark->isPassedAllComponents() ? 'PASS' : 'FAIL',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher marks update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export marks
     */
    public function export(Request $request)
    {
        if (!$request->filled('exam')) {
            return $this->exportCurrentMarksCsv($request);
        }

        $format = $request->get('format', 'excel');
        $examId = $request->get('exam');
        $subjectId = $request->get('subject');

        if (!$examId) {
            return back()->with('error', 'Please select an exam to export');
        }

        $exam = Exam::with('subject')->findOrFail($examId);
        $subjectIds = $this->getTeacherSubjectIds();

        if (!in_array($exam->subject_id, $subjectIds)) {
            return back()->with('error', 'Unauthorized');
        }

        // Get all students for this exam
        $studentIds = DB::table('subject_students')
            ->where('subject_id', $exam->subject_id)
            ->pluck('student_id');

        $students = Student::with('user')
            ->whereIn('id', $studentIds)
            ->orderBy('roll_no')
            ->get();

        // Get marks
        $marks = ExamMark::where('exam_id', $examId)
            ->where('subject_id', $subjectId ?? $exam->subject_id)
            ->get()
            ->keyBy('student_id');

        $category = $exam->exam_category;

        // Generate filename
        $filename = 'marks_' . $exam->exam_name . '_' . date('Y-m-d');

        if ($format === 'csv') {
            return $this->exportCsv($students, $marks, $exam, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($students, $marks, $exam, $filename);
        } else {
            return $this->exportExcel($students, $marks, $exam, $filename);
        }
    }

    private function exportCurrentMarksCsv(Request $request)
    {
        try {
            $marksPage = $this->index($request);

            if (!($marksPage instanceof \Illuminate\View\View)) {
                return $marksPage;
            }

            $data = $marksPage->getData();
            $currentFilters = $data['currentFilters'] ?? [];
            $subjects = collect($data['subjects'] ?? []);
            $selectedSubject = null;

            if (!empty($currentFilters['subject_id'])) {
                $selectedSubject = $subjects->firstWhere('id', (int) $currentFilters['subject_id'])
                    ?? $subjects->firstWhere('id', $currentFilters['subject_id']);
            }

            if (!$selectedSubject) {
                return back()->with('error', 'Please select a subject to export marks');
            }

            $students = $data['students'] ?? collect([]);
            if ($students instanceof LengthAwarePaginator) {
                $students = $students->getCollection();
            } else {
                $students = collect($students);
            }

            $category = $data['selectedCategory'] ?? 'assessment';
            $selectedAssessmentId = !empty($currentFilters['assessment_id']) ? (int) $currentFilters['assessment_id'] : null;
            $subjectName = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($selectedSubject['name'] ?? 'subject'));
            $filename = sprintf('teacher_marks_%s_%s', trim($subjectName, '_') ?: 'subject', now()->format('Ymd_His'));

            $callback = function () use ($students, $category, $selectedAssessmentId) {
                $out = fopen('php://output', 'w');

                if ($category === 'assessment') {
                    fputcsv($out, ['Roll No', 'Student Name', 'Attendance %', 'Full Marks', 'Pass Marks', 'Obtained', 'Percentage', 'Result']);

                    foreach ($students as $student) {
                        $selectedAssessment = null;
                        if ($selectedAssessmentId && isset($student['assessments']) && is_array($student['assessments'])) {
                            $selectedAssessment = collect($student['assessments'])->firstWhere('exam_id', $selectedAssessmentId);
                        }

                        $displayFull = $selectedAssessment['full'] ?? ($student['full_marks'] ?? 0);
                        $displayPass = $selectedAssessment['pass'] ?? ($student['pass_marks'] ?? 0);
                        $displayObtained = $selectedAssessment['obtained'] ?? ($student['total_marks'] ?? 0);
                        $displayPercentage = $selectedAssessment['percentage']
                            ?? ($displayFull > 0 ? round(($displayObtained / $displayFull) * 100, 1) : 0);
                        $isPassed = isset($selectedAssessment['is_passed'])
                            ? $selectedAssessment['is_passed']
                            : ($student['is_passed'] ?? false);

                        fputcsv($out, [
                            $student['roll_no'] ?? '',
                            $student['name'] ?? 'N/A',
                            ($student['attendance_percentage'] ?? 0) . '%',
                            $displayFull > 0 ? $displayFull : '',
                            $displayPass > 0 ? $displayPass : '',
                            $displayFull > 0 ? $displayObtained : '',
                            $displayFull > 0 ? ($displayPercentage . '%') : '',
                            $displayFull > 0 ? ($isPassed ? 'PASS' : 'FAIL') : 'Pending',
                        ]);
                    }
                } else {
                    fputcsv($out, [
                        'Roll No',
                        'Student Name',
                        'Attendance %',
                        'TI Full',
                        'TI Pass',
                        'TI Obtained',
                        'TE Full',
                        'TE Pass',
                        'TE Obtained',
                        'PI Full',
                        'PI Pass',
                        'PI Obtained',
                        'PE Full',
                        'PE Pass',
                        'PE Obtained',
                        'Total',
                        'Result',
                    ]);

                    foreach ($students as $student) {
                        fputcsv($out, [
                            $student['roll_no'] ?? '',
                            $student['name'] ?? 'N/A',
                            ($student['attendance_percentage'] ?? 0) . '%',
                            $student['ti_full'] ?? 0,
                            $student['ti_pass'] ?? 0,
                            $student['ti_obtained'] ?? 0,
                            $student['te_full'] ?? 0,
                            $student['te_pass'] ?? 0,
                            $student['te_obtained'] ?? 0,
                            $student['pi_full'] ?? 0,
                            $student['pi_pass'] ?? 0,
                            $student['pi_obtained'] ?? 0,
                            $student['pe_full'] ?? 0,
                            $student['pe_pass'] ?? 0,
                            $student['pe_obtained'] ?? 0,
                            $student['total_marks'] ?? 0,
                            ($student['is_passed'] ?? false) ? 'PASS' : 'FAIL',
                        ]);
                    }
                }

                fclose($out);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}.csv",
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher marks export error: ' . $e->getMessage());

            return back()->with('error', 'Failed to export marks: ' . $e->getMessage());
        }
    }

    private function calculateGrade($marks, $fullMarks)
    {
        if ($fullMarks <= 0) return 'N/A';
        
        $percentage = ($marks / $fullMarks) * 100;
        
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    private function exportCsv($students, $marks, $exam, $filename)
    {
        $category = $exam->exam_category;
        
        $headers = ['Roll No', 'Student Name', 'Attendance %'];
        
        if ($category === 'ctevt') {
            $headers = array_merge($headers, [
                'TI Full/Pass', 'TI Obtained',
                'TE Full/Pass', 'TE Obtained',
                'PI Full/Pass', 'PI Obtained',
                'PE Full/Pass', 'PE Obtained',
            ]);
        } else {
            $headers = array_merge($headers, [
                'Full Marks', 'Pass Marks', 'Obtained Marks',
            ]);
        }
        
        $headers = array_merge($headers, ['Total', 'Percentage', 'Grade', 'Result']);

        $callback = function() use ($students, $marks, $exam, $category) {
            echo implode(',', ['Roll No', 'Student Name', 'Attendance %']) . "\n";
            
            foreach ($students as $student) {
                $mark = $marks->get($student->id);
                $row = [
                    $student->roll_no,
                    $student->user->name ?? 'N/A',
                    '100%', // Attendance placeholder
                ];

                if ($category === 'ctevt' && $mark) {
                    $row = array_merge($row, [
                        ($exam->theory_internal_max_marks ?? 0) . '/' . ($exam->theory_internal_pass_marks ?? 0),
                        $mark->theory_internal_marks ?? 0,
                        ($exam->theory_external_max_marks ?? 0) . '/' . ($exam->theory_external_pass_marks ?? 0),
                        $mark->theory_external_marks ?? 0,
                        ($exam->practical_internal_max_marks ?? 0) . '/' . ($exam->practical_internal_pass_marks ?? 0),
                        $mark->practical_internal_marks ?? 0,
                        ($exam->practical_external_max_marks ?? 0) . '/' . ($exam->practical_external_pass_marks ?? 0),
                        $mark->practical_external_marks ?? 0,
                    ]);
                } elseif ($mark) {
                    $row = array_merge($row, [
                        $exam->full_marks,
                        $exam->passing_marks,
                        $mark->marks_obtained ?? 0,
                    ]);
                } else {
                    $row = array_merge($row, [0, 0, 0]);
                }

                $total = $mark ? $mark->marks_obtained : 0;
                $full = $mark ? $mark->full_marks : $exam->full_marks;
                $percentage = $full > 0 ? round(($total / $full) * 100, 1) : 0;
                $grade = $mark ? $mark->grade : 'N/A';
                $result = $mark ? $mark->result : 'N/A';

                $row = array_merge($row, [$total, $percentage . '%', $grade, $result]);

                echo implode(',', $row) . "\n";
            }
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ]);
    }

    private function exportExcel($students, $marks, $exam, $filename)
    {
        // For now, redirect to CSV as a fallback
        return $this->exportCsv($students, $marks, $exam, $filename);
    }

    private function exportPdf($students, $marks, $exam, $filename)
    {
        // For PDF, we'll return a simple view
        return view('teacher.exports.marks_pdf', [
            'students' => $students,
            'marks' => $marks,
            'exam' => $exam,
        ]);
    }

    /**
     * Print marks
     */
    public function print(Request $request)
    {
        try {
            $marksPage = $this->index($request);

            if (!($marksPage instanceof \Illuminate\View\View)) {
                return $marksPage;
            }

            $data = $marksPage->getData();
            $currentFilters = $data['currentFilters'] ?? [];
            $subjects = collect($data['subjects'] ?? []);
            $selectedSubject = null;

            if (!empty($currentFilters['subject_id'])) {
                $selectedSubject = $subjects->firstWhere('id', (int) $currentFilters['subject_id'])
                    ?? $subjects->firstWhere('id', $currentFilters['subject_id']);
            }

            return view('teacher.marks.print', [
                'students' => $data['students'] ?? collect([]),
                'selectedCategory' => $data['selectedCategory'] ?? 'assessment',
                'currentFilters' => $currentFilters,
                'selectedSubject' => $selectedSubject,
                'subjectLabel' => $selectedSubject['name'] ?? 'Selected Subject',
            ]);
        } catch (\Exception $e) {
            Log::error('Teacher marks print error: ' . $e->getMessage());

            return back()->with('error', 'Failed to load print preview: ' . $e->getMessage());
        }
    }

    /**
     * Display marksheet search page for teacher.
     */
    public function marksheetSearch(Request $request)
    {
        try {
            $teacher = auth()->user()->teacher;
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')->with('error', 'Teacher profile not found');
            }
            
            // Get teacher's subjects
            $subjectIds = $this->getTeacherSubjectIds();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('subject_name')->get();
            
            // Get filter data
            $years = \App\Models\Exam::distinct()
                ->whereNotNull('academic_year')
                ->pluck('academic_year')
                ->filter()
                ->sortDesc()
                ->values()
                ->toArray();
            
            $semesters = ['1', '2', '3', '4', '5', '6'];
            
            // Get exam types
            $examTypes = \App\Models\Exam::distinct()
                ->whereNotNull('exam_type')
                ->pluck('exam_type')
                ->filter()
                ->values()
                ->toArray();
            
            $examCategories = ['assessment', 'ctevt', 'general'];

            $assessmentNumbers = Exam::query()
                ->where('exam_category', 'assessment')
                ->when($request->filled('academic_year'), function ($q) use ($request) {
                    $q->where('academic_year', $request->academic_year);
                })
                ->when($request->filled('semester'), function ($q) use ($request) {
                    $q->where('semester', $request->semester);
                })
                ->when(!empty($subjectIds), function ($q) use ($subjectIds) {
                    $q->whereIn('subject_id', $subjectIds);
                })
                ->whereNotNull('assessment_number')
                ->pluck('assessment_number')
                ->filter()
                ->unique()
                ->sort()
                ->values();
            
            $filters = [
                'academic_year' => $request->get('academic_year', ''),
                'semester' => $request->get('semester', ''),
                'exam_category' => $request->get('exam_category', 'assessment'),
                'assessment_number' => $request->get('assessment_number', ''),
                'student_id' => $request->get('student_id', ''),
                'dob' => $request->get('dob', ''),
                'dob_bs' => $request->get('dob_bs', ''),
                'result' => $request->get('result', ''),
            ];
            
            $student = null;
            $marksheetData = null;
            
            // If search parameters are present, search for student
            if ($request->has('search_student')) {
                $student = $this->findStudentByIdOrDob($request);
                
                if ($student) {
                    $marksheetData = $this->getMarksheetDataForTeacher($student, $request, $subjectIds);
                }
            }
            
            return view('teacher.marks.marksheet-search', [
                'years' => $years,
                'semesters' => $semesters,
                'examTypes' => $examTypes,
                'examCategories' => $examCategories,
                'subjects' => $subjects,
                'assessmentNumbers' => $assessmentNumbers,
                'filters' => $filters,
                'student' => $student,
                'marksheetData' => $marksheetData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading teacher marksheet search: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load search page: ' . $e->getMessage());
        }
    }

    /**
     * Find student by ID or DOB.
     */
    private function findStudentByIdOrDob(Request $request)
    {
        $studentId = $request->get('student_id', '');
        $dob = $request->get('dob', '');
        $dobBs = $this->normalizeBsDateOfBirth($request->get('dob_bs', ''));
        
        $query = \App\Models\Student::with('user');
        
        if (!empty($studentId)) {
            $query->where(function($q) use ($studentId) {
                $q->where('id', $studentId)
                  ->orWhere('roll_no', 'like', "%{$studentId}%");
            });
        }
        
        $normalizedDob = !empty($dob) ? $this->normalizeDateOfBirth($dob) : null;
        $convertedDobBs = !empty($dobBs) ? NepaliContentHelper::convertBsToAd($dobBs) : null;

        if ($normalizedDob || !empty($dob) || !empty($dobBs) || $convertedDobBs) {
            $query->where(function ($q) use ($normalizedDob, $dob, $dobBs, $convertedDobBs) {
                if ($normalizedDob) {
                    $q->whereDate('date_of_birth', $normalizedDob);
                } elseif (!empty($dob)) {
                    $q->where('date_of_birth', $dob);
                }

                if (!empty($dobBs)) {
                    $q->orWhere('date_of_birth_bs', $dobBs);
                }

                if ($convertedDobBs) {
                    $q->orWhereDate('date_of_birth', $convertedDobBs);
                }
            });
        }
        
        $student = $query->first();

        // If no student found and DOB is in YYYY-MM-DD, try swapping day/month (some browsers/locales may flip them)
        if (!$student && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m)) {
            $year = $m[1];
            $month = $m[2];
            $day = $m[3];

            if (checkdate((int)$day, (int)$month, (int)$year)) {
                $swappedDob = sprintf('%s-%s-%s', $year, $day, $month);
                $student = \App\Models\Student::with('user')
                    ->where(function($q) use ($studentId) {
                        $q->where('id', $studentId)
                          ->orWhere('roll_no', 'like', "%{$studentId}%");
                    })
                    ->whereDate('date_of_birth', $swappedDob)
                    ->first();
            }
        }

        return $student;
    }

    private function normalizeBsDateOfBirth(?string $dobBs): string
    {
        if (empty($dobBs)) {
            return '';
        }

        $normalized = NepaliContentHelper::toEnglishNumber(trim($dobBs));
        $normalized = str_replace(['/', '.'], '-', $normalized);

        return preg_replace('/\s+/', '', $normalized) ?? '';
    }

    /**
     * Normalize a date of birth input into YYYY-MM-DD.
     * Accepts common formats like DD/MM/YYYY, DD-MM-YYYY, YYYY/MM/DD, YYYY-MM-DD.
     */
    private function normalizeDateOfBirth(string $dob): ?string
    {
        $formats = [
            'Y-m-d',
            'Y/m/d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'm/d/Y',
            'm-d-Y',
        ];

        foreach ($formats as $format) {
            try {
                $parsed = \Carbon\Carbon::createFromFormat($format, trim($dob));
                if ($parsed) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // ignore parse errors, try next format
            }
        }

        return null;
    }

    /**
     * Get marksheet data for teacher (filtered by teacher's subjects).
     */
    private function getMarksheetDataForTeacher(\App\Models\Student $student, Request $request, array $subjectIds)
    {
        $academicYear = $request->get('academic_year', '');
        $semester = $request->get('semester', '');
        $examCategory = $request->get('exam_category', 'assessment');
        
        $examMarksQuery = \App\Models\ExamMark::where('student_id', $student->id)
            ->whereIn('subject_id', $subjectIds)
            ->with(['exam', 'subject']);
        
        // Filter by academic year
        if (!empty($academicYear)) {
            $examMarksQuery->whereHas('exam', function($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }
        
        // Filter by semester
        if (!empty($semester)) {
            $examMarksQuery->whereHas('exam', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }
        
        // Filter by exam category
        if (!empty($examCategory)) {
            $examMarksQuery->whereHas('exam', function($q) use ($examCategory) {
                $q->where('exam_category', $examCategory);
            });
        }

        // Filter by assessment number (only for assessment category)
        $assessmentNumber = $request->get('assessment_number', '');
        if ($examCategory === 'assessment' && !empty($assessmentNumber)) {
            $examMarksQuery->whereHas('exam', function($q) use ($assessmentNumber) {
                $q->where('assessment_number', $assessmentNumber);
            });
        }
        
        $examMarks = $examMarksQuery->get();

        // If multiple marks exist for the same subject (from different exams/uploads), keep only the latest entry.
        $examMarks = $examMarks->sortByDesc(function ($mark) {
            return $mark->exam?->exam_date ? strtotime($mark->exam->exam_date) : $mark->exam_id;
        })->unique('subject_id')->values();

        // Filter by pass/fail result if requested
        $resultFilter = $request->get('result', '');
        if (in_array($resultFilter, ['pass', 'fail'])) {
            $examMarks = $examMarks->filter(function ($mark) use ($resultFilter) {
                $isPass = strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
                return $resultFilter === 'pass' ? $isPass : !$isPass;
            })->values();
        }

        // Group marks by subject
        $marksBySubject = $examMarks->groupBy('subject_id');
        
        // Calculate totals
        $totalObtained = $examMarks->sum('marks_obtained');
        $totalFull = $examMarks->sum('full_marks');
        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        
        // Calculate grade and overall result
        $grade = $this->calculateMarksheetGrade($percentage);
        $result = $examMarks->isNotEmpty() && $examMarks->every(function ($mark) {
            return strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
        }) ? 'PASS' : 'FAIL';
        
        return [
            'exam_marks' => $examMarks,
            'marks_by_subject' => $marksBySubject,
            'total_obtained' => $totalObtained,
            'total_full' => $totalFull,
            'percentage' => $percentage,
            'grade' => $grade,
            'result' => $result,
        ];
    }

    /**
     * Calculate grade based on percentage.
     */
    private function calculateMarksheetGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    /**
     * Print marksheet for teacher.
     */
    public function marksheetPrint(Request $request)
    {
        try {
            $studentId = $request->get('student_id', '');
            $dob = $request->get('dob', '');
            
            if (empty($studentId) && empty($dob)) {
                return redirect()->route('teacher.marksheet.search')
                    ->with('error', 'Please provide student ID or Date of Birth');
            }
            
            $student = $this->findStudentByIdOrDob($request);
            
            if (!$student) {
                return redirect()->route('teacher.marksheet.search')
                    ->with('error', 'Student not found');
            }
            
            $subjectIds = $this->getTeacherSubjectIds();
            $marksheetData = $this->getMarksheetDataForTeacher($student, $request, $subjectIds);
            
            return view('teacher.marks.marksheet-print', [
                'student' => $student,
                'marksheetData' => $marksheetData,
                'filters' => $request->all(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error printing teacher marksheet: ' . $e->getMessage());
            return redirect()->route('teacher.marksheet.search')
                ->with('error', 'Failed to print marksheet: ' . $e->getMessage());
        }
    }

    /**
     * Export marksheet for teacher.
     */
    public function marksheetExport(Request $request)
    {
        try {
            $studentId = $request->get('student_id', '');
            $dob = $request->get('dob', '');
            
            if (empty($studentId) && empty($dob)) {
                return redirect()->route('teacher.marksheet.search')
                    ->with('error', 'Please provide student ID or Date of Birth');
            }
            
            $student = $this->findStudentByIdOrDob($request);
            
            if (!$student) {
                return redirect()->route('teacher.marksheet.search')
                    ->with('error', 'Student not found');
            }
            
            $subjectIds = $this->getTeacherSubjectIds();
            $marksheetData = $this->getMarksheetDataForTeacher($student, $request, $subjectIds);
            
            $fileName = sprintf('marksheet_%s_%s.csv', $student->id, now()->format('Ymd_His'));

            $callback = function () use ($marksheetData) {
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'S.N.',
                    'Subject',
                    'Full Mark (Int)',
                    'Full Mark (Ext)',
                    'Pass Mark (Int)',
                    'Pass Mark (Ext)',
                    'Marks Obtained (Int)',
                    'Marks Obtained (Ext)',
                    'Total',
                ]);

                foreach ($marksheetData['exam_marks'] as $index => $mark) {
                    $tiFull = $mark->exam->theory_internal_max_marks ?? 0;
                    $teFull = $mark->exam->theory_external_max_marks ?? 0;
                    $tiPass = $mark->exam->theory_internal_pass_marks ?? 0;
                    $tePass = $mark->exam->theory_external_pass_marks ?? 0;
                    $piFull = $mark->exam->practical_internal_max_marks ?? 0;
                    $peFull = $mark->exam->practical_external_max_marks ?? 0;
                    $piPass = $mark->exam->practical_internal_pass_marks ?? 0;
                    $pePass = $mark->exam->practical_external_pass_marks ?? 0;
                    $tiObt = $mark->theory_internal_marks ?? 0;
                    $teObt = $mark->theory_external_marks ?? 0;
                    $piObt = $mark->practical_internal_marks ?? 0;
                    $peObt = $mark->practical_external_marks ?? 0;

                    fputcsv($out, [
                        $index + 1,
                        ($mark->subject->subject_name ?? 'N/A') . ' (Th.)',
                        $tiFull,
                        $teFull,
                        $tiPass,
                        $tePass,
                        $tiObt,
                        $teObt,
                        $tiObt + $teObt,
                    ]);

                    fputcsv($out, [
                        '',
                        ($mark->subject->subject_name ?? 'N/A') . ' (Pr.)',
                        $piFull,
                        $peFull,
                        $piPass,
                        $pePass,
                        $piObt,
                        $peObt,
                        $piObt + $peObt,
                    ]);
                }

                fclose($out);
            };

            return response()->streamDownload($callback, $fileName, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        } catch (\Exception $e) {
            \Log::error('Error exporting teacher marksheet: ' . $e->getMessage());
            return redirect()->route('teacher.marksheet.search')
                ->with('error', 'Failed to export marksheet: ' . $e->getMessage());
        }
    }
}
