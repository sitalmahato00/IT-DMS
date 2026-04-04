<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Support\PublicMarksheetBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentMarkController extends Controller
{
    /**
     * Display the student's marks/results.
     */
    public function index(Request $request)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        return view('student.marks.index', $this->buildMarksheetPayload($student));
    }

    /**
     * Display the student's printable marksheet.
     */
    public function marksheet(Request $request)
    {
        $student = Auth::user()?->student;

        $examId = $request->integer('exam_id');

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $payload = app(PublicMarksheetBuilder::class)->build($student, $examId ?: null);

        return view('admin.marks.marksheet-print', $payload);
    }

    /**
     * Display the list of published exam marksheets.
     */
    public function exams(Request $request)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $examGroups = ExamMark::query()
            ->with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($query) {
                $query->where('status', Exam::STATUS_PUBLISHED)
                    ->whereIn('exam_category', ['assessment', 'ctevt']);
            })
            ->get()
            ->groupBy('exam_id')
            ->map(function ($marks) {
                $marks = $marks->sortBy(fn ($mark) => sprintf(
                    '%010d-%06d',
                    optional($mark->exam?->exam_date)?->timestamp ?? 0,
                    (int) $mark->id
                ))->values();

                $first = $marks->first();
                $exam = $first?->exam;
                $full = $marks->sum(fn ($mark) => (float) $mark->effective_full_marks);
                $obtained = $marks->sum(fn ($mark) => $mark->isAbsent() ? 0 : (float) $mark->effective_obtained_marks);
                $percentage = $full > 0 ? round(($obtained / $full) * 100, 2) : null;
                $allPass = $marks->isNotEmpty() && $marks->every(function ($mark) {
                    return strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
                });

                return [
                    'exam_id' => $exam?->id,
                    'exam_name' => $exam?->exam_name ?? __('Exam'),
                    'exam_category' => $exam?->exam_category ?? 'general',
                    'exam_category_label' => $exam?->formatted_category ?? ucfirst($exam?->exam_category ?? 'Exam'),
                    'exam_date' => $exam?->exam_date?->format('M d, Y') ?? __('Date pending'),
                    'assessment_number' => $exam?->assessment_number,
                    'subject_count' => $marks->count(),
                    'full_marks' => $full,
                    'obtained_marks' => $obtained,
                    'percentage' => $percentage,
                    'status' => $percentage === null ? 'pending' : ($allPass ? 'pass' : 'fail'),
                    'rows' => $marks->map(function ($mark) {
                        return [
                            'subject_name' => $mark->subject?->subject_name ?? __('Subject'),
                            'subject_code' => $mark->subject?->subject_code,
                            'full_marks' => (float) $mark->effective_full_marks,
                            'passing_marks' => (float) $mark->effective_passing_marks,
                            'obtained_marks' => $mark->isAbsent() ? 'ABS' : round((float) $mark->effective_obtained_marks, 2),
                            'status' => $mark->isAbsent() ? 'absent' : (strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS' ? 'pass' : 'fail'),
                            'percentage' => $mark->isAbsent() ? null : round((float) $mark->calculatePercentage(), 2),
                        ];
                    }),
                ];
            })
            ->sortByDesc(fn ($exam) => strtotime((string) $exam['exam_date']) ?: 0)
            ->values();

        return view('student.exams', [
            'student' => $student,
            'examGroups' => $examGroups,
        ]);
    }

    /**
     * Display marks details for a specific subject.
     */
    public function show($subjectId)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $subject = $student->subjects()
            ->where('subjects.id', $subjectId)
            ->with(['teacherAssignments.teacher.user'])
            ->first();

        if (!$subject) {
            return redirect()->route('student.marks')->with('error', 'Subject not found or you are not enrolled.');
        }

        $teachers = $subject->teacherAssignments()
            ->with('teacher.user')
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->teacher->user->id ?? 0,
                    'name' => $assignment->teacher->user->name ?? 'Unknown',
                    'role' => $assignment->role,
                ];
            });

        $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment', null, true);
        $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt', null, null, true);

        $componentMarks = [];
        if ($ctevtMarks && isset($ctevtMarks->full)) {
            foreach (['TI', 'TE', 'PI', 'PE'] as $component) {
                $componentMarks[$component] = $student->getComponentMarks($subject->id, $component, true);
            }
        }

        $exams = DB::table('exam_marks')
            ->where('student_id', $student->id)
            ->where('exam_marks.subject_id', $subject->id)
            ->join('exams', function ($join) {
                $join->on('exam_marks.exam_id', '=', 'exams.id')
                    ->where('exams.status', 'published');
            })
            ->select('exams.*', 'exam_marks.marks_obtained', 'exam_marks.full_marks', 'exam_marks.passing_marks')
            ->orderBy('exams.exam_date', 'desc')
            ->get();

        return view('student.marks.show', compact('subject', 'teachers', 'assessmentMarks', 'ctevtMarks', 'componentMarks', 'exams'));
    }

    /**
     * Build the summary payload used by both results and marksheet pages.
     */
    private function buildMarksheetPayload(Student $student): array
    {
        $subjects = $student->subjects()
            ->with(['teacherAssignments.teacher.user'])
            ->orderBy('semester')
            ->get()
            ->map(function ($subject) use ($student) {
                $primaryTeacher = $subject->teacherAssignments()
                    ->where('role', 'primary')
                    ->with('teacher.user')
                    ->first()?->teacher?->user;

                $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment', null, true);
                $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt', null, null, true);
                $primaryMarks = ($assessmentMarks->full ?? 0) > 0
                    ? $assessmentMarks
                    : (((isset($ctevtMarks->full) ? $ctevtMarks->full : 0) > 0) ? $ctevtMarks : null);

                $fullMarks = $primaryMarks && ($primaryMarks->full ?? 0) > 0 ? (float) $primaryMarks->full : 0;
                $obtainedMarks = $primaryMarks && ($primaryMarks->full ?? 0) > 0 ? (float) $primaryMarks->obtained : 0;
                $percentage = $fullMarks > 0 ? round(($obtainedMarks / $fullMarks) * 100, 2) : null;
                $status = $percentage === null ? 'pending' : (($primaryMarks->is_pass ?? false) ? 'pass' : 'fail');

                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'course' => $subject->category ?? $subject->subject_name,
                    'teacher' => $primaryTeacher ? $primaryTeacher->name : 'TBA',
                    'assessment_marks' => $assessmentMarks,
                    'ctevt_marks' => $ctevtMarks,
                    'full_marks' => $fullMarks,
                    'obtained_marks' => $obtainedMarks,
                    'percentage' => $percentage,
                    'status' => $status,
                    'grade' => $percentage === null ? 'N/A' : $this->calculateMarksheetGrade($percentage),
                ];
            });

        $gradedSubjects = $subjects->filter(fn ($subject) => !is_null($subject['percentage']))->values();
        $passedCount = $subjects->where('status', 'pass')->count();
        $failedCount = $subjects->where('status', 'fail')->count();
        $pendingCount = $subjects->where('status', 'pending')->count();
        $subjectCount = $subjects->count();

        $totalObtained = 0;
        $totalFull = 0;
        foreach ($subjects as $subject) {
            if (($subject['assessment_marks']->full ?? 0) > 0) {
                $totalObtained += (float) $subject['assessment_marks']->obtained;
                $totalFull += (float) $subject['assessment_marks']->full;
            } elseif (($subject['ctevt_marks']->full ?? 0) > 0) {
                $totalObtained += (float) $subject['ctevt_marks']->obtained;
                $totalFull += (float) $subject['ctevt_marks']->full;
            }
        }

        $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $cgpa = $overallPercentage > 0 ? round($overallPercentage / 25, 2) : 0;
        $passRate = $gradedSubjects->count() > 0 ? round(($passedCount / $gradedSubjects->count()) * 100, 1) : null;
        $topSubjects = $gradedSubjects->sortByDesc('percentage')->take(4)->values();
        $bestSubject = $gradedSubjects->sortByDesc('percentage')->first();
        $lowestSubject = $gradedSubjects->sortBy('percentage')->first();

        $marksStatusChart = [
            'labels' => [__('Passed'), __('Failed'), __('Pending')],
            'values' => [$passedCount, $failedCount, $pendingCount],
        ];

        $marksPerformanceChart = [
            'labels' => $gradedSubjects->pluck('code')->all(),
            'values' => $gradedSubjects->pluck('percentage')->map(fn ($value) => round((float) $value, 1))->all(),
            'names' => $gradedSubjects->pluck('name')->all(),
        ];

        $marksheetGrade = $this->calculateMarksheetGrade($overallPercentage);
        $result = $gradedSubjects->isEmpty()
            ? 'PENDING'
            : ($failedCount > 0 ? 'FAIL' : ($pendingCount > 0 ? 'PENDING' : 'PASS'));

        return compact(
            'subjects',
            'gradedSubjects',
            'passedCount',
            'failedCount',
            'pendingCount',
            'subjectCount',
            'totalObtained',
            'totalFull',
            'overallPercentage',
            'cgpa',
            'passRate',
            'topSubjects',
            'bestSubject',
            'lowestSubject',
            'marksStatusChart',
            'marksPerformanceChart',
            'marksheetGrade',
            'result'
        );
    }

    /**
     * Build the transcript payload for the separate marksheet page.
     */
    private function buildTranscriptPayload(Student $student, ?int $examId = null): array
    {
        $publicMarks = ExamMark::query()
            ->with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($query) {
                $query->whereIn('exam_category', ['assessment', 'ctevt'])
                    ->where('status', Exam::STATUS_PUBLISHED);
            })
            ->get();

        if ($examId) {
            $publicMarks = $publicMarks->where('exam_id', $examId);
        }

        $assessmentMarks = $publicMarks
            ->filter(fn ($mark) => ($mark->exam?->exam_category ?? null) === 'assessment')
            ->sortBy(function ($mark) {
                $examDate = $mark->exam?->exam_date;
                $dateValue = $examDate ? $examDate->timestamp : 0;
                $assessmentNumber = (int) ($mark->assessment_number ?? $mark->exam?->assessment_number ?? 0);

                return sprintf(
                    '%010d-%04d-%06d',
                    $dateValue,
                    $assessmentNumber,
                    (int) $mark->id
                );
            })
            ->values();

        $ctevtMarks = $publicMarks
            ->filter(fn ($mark) => ($mark->exam?->exam_category ?? null) === 'ctevt')
            ->sortBy(function ($mark) {
                $examDate = $mark->exam?->exam_date;
                $dateValue = $examDate ? $examDate->timestamp : 0;
                $subjectName = strtolower($mark->subject?->subject_name ?? '');

                return sprintf(
                    '%010d-%s-%06d',
                    $dateValue,
                    $subjectName,
                    (int) $mark->id
                );
            })
            ->values();

        $formatDate = function ($date) {
            if (!$date) {
                return 'N/A';
            }

            return $date->format('M d, Y');
        };

        $resolveStatus = function ($mark): string {
            if ($mark->isAbsent()) {
                return 'absent';
            }

            if (!$mark->isFilled()) {
                return 'pending';
            }

            return $mark->isPassedAllComponents() ? 'pass' : 'fail';
        };

        $assessmentRows = $assessmentMarks->values()->map(function ($mark, $index) use ($formatDate, $resolveStatus) {
            $status = $resolveStatus($mark);
            $full = (float) $mark->effective_full_marks;
            $obtained = (float) ($mark->isAbsent() ? 0 : $mark->effective_obtained_marks);
            $percentage = ($status === 'pending' || $full <= 0) ? null : round(($obtained / $full) * 100, 2);

            return [
                'sn' => $index + 1,
                'exam_id' => $mark->exam_id,
                'subject_name' => $mark->subject?->subject_name ?? 'N/A',
                'subject_code' => $mark->subject?->subject_code ?? 'N/A',
                'exam_name' => $mark->exam?->exam_name ?? ($mark->exam?->formatted_assessment ?? 'Assessment'),
                'assessment_number' => $mark->assessment_number ?? $mark->exam?->assessment_number,
                'exam_date' => $formatDate($mark->exam?->exam_date),
                'full_marks' => $full,
                'passing_marks' => (float) $mark->effective_passing_marks,
                'marks_obtained' => $status === 'pending'
                    ? null
                    : ($mark->isAbsent() ? 'ABS' : round($obtained, 2)),
                'percentage' => $percentage,
                'grade' => $status === 'pending' ? 'N/A' : ($mark->isAbsent() ? 'ABS' : $mark->calculateGrade()),
                'result' => strtoupper($status),
                'status' => $status,
            ];
        });

        $ctevtRows = $ctevtMarks->values()->map(function ($mark, $index) use ($formatDate, $resolveStatus) {
            $status = $resolveStatus($mark);
            $subject = $mark->subject;
            $exam = $mark->exam;

            $tiFull = (float) ($mark->theory_internal_full_marks ?? $exam?->theory_internal_max_marks ?? 0);
            $teFull = (float) ($mark->theory_external_full_marks ?? $exam?->theory_external_max_marks ?? 0);
            $piFull = (float) ($mark->practical_internal_full_marks ?? $exam?->practical_internal_max_marks ?? 0);
            $peFull = (float) ($mark->practical_external_full_marks ?? $exam?->practical_external_max_marks ?? 0);

            $tiPass = (float) ($mark->theory_internal_pass_marks ?? $exam?->theory_internal_pass_marks ?? 0);
            $tePass = (float) ($mark->theory_external_pass_marks ?? $exam?->theory_external_pass_marks ?? 0);
            $piPass = (float) ($mark->practical_internal_pass_marks ?? $exam?->practical_internal_pass_marks ?? 0);
            $pePass = (float) ($mark->practical_external_pass_marks ?? $exam?->practical_external_pass_marks ?? 0);

            $tiObt = $mark->theory_internal_marks;
            $teObt = $mark->theory_external_marks;
            $piObt = $mark->practical_internal_marks;
            $peObt = $mark->practical_external_marks;

            $theoryTotal = (float) ($tiObt ?? 0) + (float) ($teObt ?? 0);
            $practicalTotal = (float) ($piObt ?? 0) + (float) ($peObt ?? 0);
            $full = (float) $mark->effective_full_marks;
            $obtained = (float) ($mark->isAbsent() ? 0 : $mark->effective_obtained_marks);
            $percentage = ($status === 'pending' || $full <= 0) ? null : round(($obtained / $full) * 100, 2);

            return [
                'sn' => $index + 1,
                'exam_id' => $mark->exam_id,
                'subject_name' => $subject?->subject_name ?? 'N/A',
                'subject_code' => $subject?->subject_code ?? 'N/A',
                'exam_name' => $exam?->exam_name ?? ($subject?->subject_name ?? 'CTEVT'),
                'exam_date' => $formatDate($exam?->exam_date),
                'ti_full' => $tiFull,
                'te_full' => $teFull,
                'pi_full' => $piFull,
                'pe_full' => $peFull,
                'ti_pass' => $tiPass,
                'te_pass' => $tePass,
                'pi_pass' => $piPass,
                'pe_pass' => $pePass,
                'ti_obtained' => $tiObt,
                'te_obtained' => $teObt,
                'pi_obtained' => $piObt,
                'pe_obtained' => $peObt,
                'theory_total' => $theoryTotal,
                'practical_total' => $practicalTotal,
                'full_marks' => $full,
                'passing_marks' => (float) $mark->effective_passing_marks,
                'marks_obtained' => $status === 'pending'
                    ? null
                    : ($mark->isAbsent() ? 'ABS' : round($obtained, 2)),
                'percentage' => $percentage,
                'grade' => $status === 'pending' ? 'N/A' : ($mark->isAbsent() ? 'ABS' : $mark->calculateGrade()),
                'result' => strtoupper($status),
                'status' => $status,
            ];
        });

        $assessmentTotalObtained = $assessmentMarks->sum(fn ($mark) => $mark->isAbsent() ? 0 : (float) $mark->effective_obtained_marks);
        $assessmentTotalFull = $assessmentMarks->sum(fn ($mark) => (float) $mark->effective_full_marks);
        $ctevtTotalObtained = $ctevtMarks->sum(fn ($mark) => $mark->isAbsent() ? 0 : (float) $mark->effective_obtained_marks);
        $ctevtTotalFull = $ctevtMarks->sum(fn ($mark) => (float) $mark->effective_full_marks);

        $overallObtained = $assessmentTotalObtained + $ctevtTotalObtained;
        $overallFull = $assessmentTotalFull + $ctevtTotalFull;
        $overallPercentage = $overallFull > 0 ? round(($overallObtained / $overallFull) * 100, 2) : 0;

        $hasPending = $publicMarks->contains(fn ($mark) => !$mark->isAbsent() && !$mark->isFilled());
        $hasFail = $publicMarks->contains(fn ($mark) => !$mark->isAbsent() && $mark->isFilled() && !$mark->isPassedAllComponents());
        $hasAbsent = $publicMarks->contains(fn ($mark) => $mark->isAbsent());

        $result = $publicMarks->isEmpty()
            ? 'PENDING'
            : ($hasFail || $hasAbsent ? 'FAIL' : ($hasPending ? 'PENDING' : 'PASS'));

        $selectedExam = $examId ? $publicMarks->first()?->exam : null;

        return [
            'publicExamMarks' => $publicMarks,
            'assessmentTranscriptRows' => $assessmentRows,
            'ctevtTranscriptRows' => $ctevtRows,
            'assessmentTranscriptTotals' => [
                'obtained' => $assessmentTotalObtained,
                'full' => $assessmentTotalFull,
                'count' => $assessmentRows->count(),
            ],
            'ctevtTranscriptTotals' => [
                'obtained' => $ctevtTotalObtained,
                'full' => $ctevtTotalFull,
                'count' => $ctevtRows->count(),
            ],
            'overallTranscriptTotals' => [
                'obtained' => $overallObtained,
                'full' => $overallFull,
                'percentage' => $overallPercentage,
            ],
            'publicEntryCount' => $publicMarks->count(),
            'assessmentEntryCount' => $assessmentRows->count(),
            'ctevtEntryCount' => $ctevtRows->count(),
            'overallPercentage' => $overallPercentage,
            'marksheetGrade' => $this->calculateMarksheetGrade($overallPercentage),
            'result' => $result,
            'selectedExamId' => $examId,
            'selectedExamName' => $selectedExam?->exam_name,
            'selectedExamCategory' => $selectedExam?->formatted_category,
        ];
    }

    private function calculateMarksheetGrade(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'A+';
        }

        if ($percentage >= 80) {
            return 'A';
        }

        if ($percentage >= 70) {
            return 'B+';
        }

        if ($percentage >= 60) {
            return 'B';
        }

        if ($percentage >= 50) {
            return 'C+';
        }

        if ($percentage >= 40) {
            return 'C';
        }

        if ($percentage >= 35) {
            return 'D';
        }

        return 'F';
    }
}
