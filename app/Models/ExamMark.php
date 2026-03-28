<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    use HasFactory;

    protected $table = 'exam_marks';

    protected $fillable = [
        'exam_id',
        'subject_id',
        'student_id',
        'academic_year',
        'academic_year_bs',
        'marks_obtained',
        'full_marks',
        'passing_marks',
        'percentage',
        'grade',
        'marks_status',
        'theory_internal_marks',
        'theory_external_marks',
        'practical_internal_marks',
        'practical_external_marks',
        'theory_internal_full_marks',
        'theory_external_full_marks',
        'practical_internal_full_marks',
        'practical_external_full_marks',
        'theory_internal_pass_marks',
        'theory_external_pass_marks',
        'practical_internal_pass_marks',
        'practical_external_pass_marks',
        'status',
        'entered_by',
        'remarks',
        'graded_by',
        'graded_at',
        'assessment_number',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'full_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'theory_internal_marks' => 'decimal:2',
        'theory_external_marks' => 'decimal:2',
        'practical_internal_marks' => 'decimal:2',
        'practical_external_marks' => 'decimal:2',
        'theory_internal_full_marks' => 'decimal:2',
        'theory_external_full_marks' => 'decimal:2',
        'practical_internal_full_marks' => 'decimal:2',
        'practical_external_full_marks' => 'decimal:2',
        'theory_internal_pass_marks' => 'integer',
        'theory_external_pass_marks' => 'integer',
        'practical_internal_pass_marks' => 'integer',
        'practical_external_pass_marks' => 'integer',
        'graded_at' => 'datetime',
    ];

    // CTEVT Components constants
    const COMPONENT_TI = 'TI'; // Theory Internal
    const COMPONENT_TE = 'TE'; // Theory External
    const COMPONENT_PI = 'PI'; // Practical Internal
    const COMPONENT_PE = 'PE'; // Practical External

    const COMPONENTS = ['TI', 'TE', 'PI', 'PE'];

    /**
     * Get the exam for this mark
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    /**
     * Get the subject for this mark
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the student for this mark
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the user who graded this mark
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if this is a CTEVT exam
     */
    public function isCtevt(): bool
    {
        return $this->exam && $this->exam->exam_category === 'ctevt';
    }

    /**
     * Check if this is an Assessment exam
     */
    public function isAssessment(): bool
    {
        return $this->exam && $this->exam->exam_category === 'assessment';
    }

    /**
     * Get component marks (for CTEVT)
     */
    public function getComponentMarks(string $component): array
    {
        $marks = match($component) {
            self::COMPONENT_TI => [
                'obtained' => $this->theory_internal_marks ?? 0,
                'full' => $this->exam->theory_internal_max_marks ?? 0,
                'pass' => $this->theory_internal_pass_marks ?? 0,
            ],
            self::COMPONENT_TE => [
                'obtained' => $this->theory_external_marks ?? 0,
                'full' => $this->exam->theory_external_max_marks ?? 0,
                'pass' => $this->theory_external_pass_marks ?? 0,
            ],
            self::COMPONENT_PI => [
                'obtained' => $this->practical_internal_marks ?? 0,
                'full' => $this->exam->practical_internal_max_marks ?? 0,
                'pass' => $this->practical_internal_pass_marks ?? 0,
            ],
            self::COMPONENT_PE => [
                'obtained' => $this->practical_external_marks ?? 0,
                'full' => $this->exam->practical_external_max_marks ?? 0,
                'pass' => $this->practical_external_pass_marks ?? 0,
            ],
            default => ['obtained' => 0, 'full' => 0, 'pass' => 0],
        };

        return $marks;
    }

    /**
     * Get all CTEVT component marks as array
     */
    public function getAllComponentMarks(): array
    {
        $components = [];
        foreach (self::COMPONENTS as $component) {
            $components[$component] = $this->getComponentMarks($component);
        }
        return $components;
    }

    /**
     * Get full marks that should be used for percentage/grade.
     * Falls back to exam full marks if not set on the record.
     */
    public function getEffectiveFullMarksAttribute(): float
    {
        if ($this->full_marks && $this->full_marks > 0) {
            return floatval($this->full_marks);
        }

        if ($this->exam) {
            if ($this->isCtevt()) {
                return floatval($this->exam->theory_internal_max_marks ?? 0)
                    + floatval($this->exam->theory_external_max_marks ?? 0)
                    + floatval($this->exam->practical_internal_max_marks ?? 0)
                    + floatval($this->exam->practical_external_max_marks ?? 0);
            }

            return floatval($this->exam->full_marks ?? 0);
        }

        return 0;
    }

    /**
     * Get passing marks that should be used for pass/fail.
     * Falls back to exam pass marks if not set on the record.
     */
    public function getEffectivePassingMarksAttribute(): float
    {
        if ($this->passing_marks && $this->passing_marks > 0) {
            return floatval($this->passing_marks);
        }

        if ($this->exam) {
            if ($this->isCtevt()) {
                // Use component pass marks sum for CTEVT if available, else use 40% of full.
                $componentPass = floatval($this->exam->theory_internal_pass_marks ?? 0)
                    + floatval($this->exam->theory_external_pass_marks ?? 0)
                    + floatval($this->exam->practical_internal_pass_marks ?? 0)
                    + floatval($this->exam->practical_external_pass_marks ?? 0);

                return $componentPass > 0 ? $componentPass : ($this->effective_full_marks * 0.4);
            }

            return floatval($this->exam->passing_marks ?? ($this->effective_full_marks * 0.4));
        }

        return 0;
    }

    /**
     * Get obtained marks that should be used for calculations.
     * For CTEVT, totals component marks.
     */
    public function getEffectiveObtainedMarksAttribute(): float
    {
        if ($this->isCtevt()) {
            return $this->calculateTotalMarks();
        }

        return floatval($this->marks_obtained ?? 0);
    }

    /**
     * Calculate total marks based on exam category
     */
    public function calculateTotalMarks(): float
    {
        if ($this->isCtevt()) {
            $componentTotal = floatval($this->theory_internal_marks ?? 0) +
                              floatval($this->theory_external_marks ?? 0) +
                              floatval($this->practical_internal_marks ?? 0) +
                              floatval($this->practical_external_marks ?? 0);
            return $componentTotal > 0 ? $componentTotal : floatval($this->marks_obtained ?? 0);
        }

        // For assessment, use marks_obtained
        return floatval($this->marks_obtained ?? 0);
    }

    /**
     * Calculate full marks based on exam category
     */
    public function calculateFullMarks(): float
    {
        if ($this->isCtevt()) {
            return floatval($this->exam->theory_internal_max_marks ?? 0) +
                   floatval($this->exam->theory_external_max_marks ?? 0) +
                   floatval($this->exam->practical_internal_max_marks ?? 0) +
                   floatval($this->exam->practical_external_max_marks ?? 0);
        }

        return floatval($this->full_marks ?? 0);
    }

    /**
     * Calculate percentage
     */
    public function calculatePercentage(): float
    {
        $fullMarks = $this->calculateFullMarks();
        if ($fullMarks && $fullMarks > 0) {
            return round(($this->calculateTotalMarks() / $fullMarks) * 100, 2);
        }
        return 0;
    }

    /**
     * Check if marks are filled (not empty/null and not absent)
     */
    public function isFilled(): bool
    {
        if ($this->isAbsent()) {
            return false;
        }
        
        if ($this->isCtevt()) {
            return $this->theory_internal_marks !== null ||
                   $this->theory_external_marks !== null ||
                   $this->practical_internal_marks !== null ||
                   $this->practical_external_marks !== null;
        }

        return $this->marks_obtained !== null;
    }

    /**
     * Check if student was absent
     */
    public function isAbsent(): bool
    {
        return $this->marks_status === 'absent' || $this->grade === 'ABS';
    }

    /**
     * Check if marks are empty
     */
    public function isEmpty(): bool
    {
        return !$this->isFilled() && !$this->isAbsent();
    }

    /**
     * Calculate grade based on percentage
     */
    public function calculateGrade(): string
    {
        $percentage = $this->percentage ?? $this->calculatePercentage();
        
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
     * Check if student passed (threshold: 40%)
     */
    public function isPassed(): bool
    {
        return $this->calculatePercentage() >= 40;
    }

    /**
     * Check if student passed all components (for CTEVT)
     */
    public function isPassedAllComponents(): bool
    {
        if (!$this->isCtevt()) {
            return $this->isPassed();
        }

        foreach (self::COMPONENTS as $component) {
            $marks = $this->getComponentMarks($component);
            if ($marks['full'] > 0 && floatval($marks['obtained']) < floatval($marks['pass'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get result status (PASS or FAIL)
     */
    public function getResultAttribute(): string
    {
        return $this->isPassedAllComponents() ? 'PASS' : 'FAIL';
    }

    /**
     * Scope to get marks for a specific exam
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    /**
     * Scope to get marks for a specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get passed marks only
     */
    public function scopePassed($query)
    {
        return $query->where('percentage', '>=', 35);
    }

    /**
     * Scope to get failed marks only (threshold: 40%)
     */
    public function scopeFailed($query)
    {
        return $query->where('percentage', '<', 40);
    }

    /**
     * Scope to get filled marks (has obtained marks)
     */
    public function scopeFilled($query)
    {
        return $query->whereNotNull('marks_obtained');
    }

    /**
     * Scope to get empty marks (no obtained marks)
     */
    public function scopeEmpty($query)
    {
        return $query->whereNull('marks_obtained');
    }
}
