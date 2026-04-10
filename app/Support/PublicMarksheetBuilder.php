<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use Illuminate\Support\Collection;

class PublicMarksheetBuilder
{
    /**
     * Build an admin-style marksheet payload for published student marks only.
     */
    public function build(Student $student, ?int $examId = null): array
    {
        $publicMarks = ExamMark::query()
            ->with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($query) {
                $query->where('status', Exam::STATUS_PUBLISHED)
                    ->whereIn('exam_category', ['assessment', 'ctevt']);
            })
            ->get();

        $selectedExamId = $examId ?: $this->resolveLatestExamId($publicMarks);
        $examMarks = $selectedExamId
            ? $publicMarks->where('exam_id', $selectedExamId)->values()
            : collect();

        $examMarks = $this->collapseMarksBySubject($examMarks);
        $selectedExam = $examMarks->first()?->exam;

        $totalObtained = $examMarks->sum(function (ExamMark $mark) {
            if ($mark->isAbsent()) {
                return 0;
            }

            return $mark->isCtevt()
                ? $mark->calculateTotalMarks()
                : (float) ($mark->marks_obtained ?? 0);
        });
        $totalFull = $examMarks->sum(function (ExamMark $mark) {
            return $mark->isCtevt()
                ? $mark->calculateFullMarks()
                : (float) ($mark->full_marks ?? 0);
        });
        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage);
        $result = $examMarks->isNotEmpty() && $examMarks->every(function (ExamMark $mark) {
            return strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
        }) ? 'PASS' : 'FAIL';

        $academicYear = $selectedExam?->academic_year
            ?? $student->academic_year_bs
            ?? $student->academic_year
            ?? '';
        $semester = $selectedExam?->semester
            ?? $student->semester
            ?? '';
        $examCategory = $selectedExam?->exam_category ?? 'assessment';

        return [
            'student' => $student,
            'filters' => [
                'academic_year' => $academicYear,
                'semester' => $semester,
                'exam_category' => $examCategory,
                'assessment_number' => $selectedExam?->assessment_number ?? '',
                'exam_id' => $selectedExam?->id ?? $selectedExamId ?? '',
                'student_id' => $student->id,
                'result' => strtolower($result),
            ],
            'marksheetData' => [
                'exam_marks' => $examMarks,
                'marks_by_subject' => $examMarks->groupBy('subject_id'),
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => $percentage,
                'grade' => $grade,
                'result' => $result,
            ],
            'selectedExam' => $selectedExam,
            'selectedExamId' => $selectedExam?->id ?? $selectedExamId,
            'selectedExamName' => $selectedExam?->exam_name,
            'selectedExamCategory' => $selectedExam?->formatted_category,
        ];
    }

    /**
     * Build an admin-style marksheet payload from public search filters.
     */
    public function buildForSearch(Student $student, array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);

        $examMarks = $this->querySearchMarks($student, $filters)->get();
        $examMarks = $this->collapseMarksBySubject($examMarks);
        $selectedExam = $examMarks->first()?->exam;

        $totalObtained = $examMarks->sum(function (ExamMark $mark) {
            if ($mark->isAbsent()) {
                return 0;
            }

            return $mark->isCtevt()
                ? $mark->calculateTotalMarks()
                : (float) ($mark->marks_obtained ?? 0);
        });
        $totalFull = $examMarks->sum(function (ExamMark $mark) {
            return $mark->isCtevt()
                ? $mark->calculateFullMarks()
                : (float) ($mark->full_marks ?? 0);
        });
        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage);
        $result = $examMarks->isNotEmpty() && $examMarks->every(function (ExamMark $mark) {
            return strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
        }) ? 'PASS' : 'FAIL';

        $academicYear = $filters['academic_year']
            ?: ($selectedExam?->academic_year ?? $student->academic_year_bs ?? $student->academic_year ?? '');
        $semester = $filters['semester']
            ?: ($selectedExam?->semester ?? $student->semester ?? '');
        $examCategory = $filters['exam_category'] ?: ($selectedExam?->exam_category ?? 'assessment');
        $assessmentNumber = $filters['assessment_number'] ?: ($selectedExam?->assessment_number ?? '');

        return [
            'student' => $student,
            'filters' => [
                'academic_year' => $academicYear,
                'semester' => $semester,
                'exam_category' => $examCategory,
                'assessment_number' => $assessmentNumber,
                'exam_id' => $selectedExam?->id ?? '',
                'student_id' => $student->id,
                'result' => strtolower($result),
            ],
            'marksheetData' => [
                'exam_marks' => $examMarks,
                'marks_by_subject' => $examMarks->groupBy('subject_id'),
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => $percentage,
                'grade' => $grade,
                'result' => $result,
            ],
            'selectedExam' => $selectedExam,
            'selectedExamId' => $selectedExam?->id,
            'selectedExamName' => $selectedExam?->exam_name,
            'selectedExamCategory' => $selectedExam?->formatted_category,
        ];
    }

    private function resolveLatestExamId(Collection $marks): ?int
    {
        if ($marks->isEmpty()) {
            return null;
        }

        $latestMark = $marks->sortByDesc(function (ExamMark $mark) {
            $examDate = $mark->exam?->exam_date;
            $dateValue = $examDate ? $examDate->timestamp : 0;
            $assessmentNumber = (int) ($mark->assessment_number ?? $mark->exam?->assessment_number ?? 0);

            return sprintf(
                '%010d-%04d-%06d',
                $dateValue,
                $assessmentNumber,
                (int) $mark->id
            );
        })->first();

        return $latestMark?->exam_id;
    }

    /**
     * Keep one mark per subject, preferring the newest public entry.
     */
    private function collapseMarksBySubject(Collection $marks): Collection
    {
        return $marks
            ->sortByDesc(function (ExamMark $mark) {
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
            ->unique('subject_id')
            ->values();
    }

    private function normalizeFilters(array $filters): array
    {
        return [
            'academic_year' => trim((string) ($filters['academic_year'] ?? '')),
            'semester' => trim((string) ($filters['semester'] ?? '')),
            'exam_category' => trim((string) ($filters['exam_category'] ?? 'assessment')) ?: 'assessment',
            'assessment_number' => trim((string) ($filters['assessment_number'] ?? '')),
            'exam_id' => trim((string) ($filters['exam_id'] ?? '')),
        ];
    }

    private function querySearchMarks(Student $student, array $filters)
    {
        $query = ExamMark::query()
            ->with(['exam', 'subject'])
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($examQuery) {
                $examQuery->where('status', Exam::STATUS_PUBLISHED)
                    ->whereIn('exam_category', ['assessment', 'ctevt']);
            });

        if (!empty($filters['academic_year'])) {
            $query->whereHas('exam', function ($examQuery) use ($filters) {
                $examQuery->where('academic_year', $filters['academic_year']);
            });
        }

        if (!empty($filters['semester'])) {
            $query->whereHas('exam', function ($examQuery) use ($filters) {
                $examQuery->where('semester', $filters['semester']);
            });
        }

        if (!empty($filters['exam_category'])) {
            $query->whereHas('exam', function ($examQuery) use ($filters) {
                $examQuery->where('exam_category', $filters['exam_category']);
            });
        }

        if (!empty($filters['assessment_number']) && ($filters['exam_category'] ?? '') === 'assessment') {
            $assessmentNumber = $filters['assessment_number'];
            $query->where(function ($markQuery) use ($assessmentNumber) {
                $markQuery->where('assessment_number', $assessmentNumber)
                    ->orWhereHas('exam', function ($examQuery) use ($assessmentNumber) {
                        $examQuery->where('assessment_number', $assessmentNumber);
                    });
            });
        }

        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', (int) $filters['exam_id']);
        }

        return $query;
    }

    private function calculateGrade(float $percentage): string
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

