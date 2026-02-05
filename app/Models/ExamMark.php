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
        'student_id',
        'marks_obtained',
        'full_marks',
        'percentage',
        'grade',
        'remarks',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'marks_obtained' => 'integer',
        'full_marks' => 'integer',
        'percentage' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    /**
     * Get the exam for this mark
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
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
     * Calculate percentage
     */
    public function calculatePercentage(): float
    {
        if ($this->full_marks && $this->full_marks > 0) {
            return round(($this->marks_obtained / $this->full_marks) * 100, 2);
        }
        return 0;
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
        return $this->percentage >= 40;
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
}

