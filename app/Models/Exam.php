<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_number',
        'exam_name',
        'exam_name_ne',
        'academic_year',
        'semester',
        'subject_id',
        'exam_category',
        'exam_type',
        'full_marks',
        'passing_marks',
        'theory_internal_max_marks',
        'theory_external_max_marks',
        'practical_internal_max_marks',
        'practical_external_max_marks',
        'theory_internal_pass_marks',
        'theory_external_pass_marks',
        'practical_internal_pass_marks',
        'practical_external_pass_marks',
        'exam_date',
        'exam_date_bs',
        'status',
        'description',
        'description_ne',
        'instructions',
        'created_by',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'full_marks' => 'integer',
        'passing_marks' => 'integer',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    const TYPE_INTERNAL = 'internal';
    const TYPE_FINAL = 'final';
    const TYPE_MIDTERM = 'midterm';
    const TYPE_PRACTICAL = 'practical';
    const TYPE_VIVA = 'viva';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_ASSESSMENT = 'assessment';

    /**
     * Get the subject for this exam
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the user who created this exam
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get marks for this exam
     */
    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class, 'exam_id');
    }

    /**
     * Get formatted assessment number as "Assessment 1", "Assessment 2", etc.
     */
    public function getFormattedAssessmentAttribute(): string
    {
        return $this->assessment_number 
            ? __('Assessment') . ' ' . $this->assessment_number 
            : '';
    }

    /**
     * Get next assessment number for subject/semester/academic_year (assessment category only)
     */
    public static function getNextAssessmentNumber($subjectId, $semester, $academicYear): int
    {
        $query = static::where('subject_id', $subjectId)
            ->where('semester', $semester);

        if ($academicYear === null || $academicYear === '') {
            $query->whereNull('academic_year');
        } else {
            $query->where('academic_year', $academicYear);
        }

        return $query
            ->where('exam_category', 'assessment')
            ->max('assessment_number') + 1 ?? 1;
    }

    /**
     * Get available assessment numbers for dropdown (existing + next new)
     */
    public static function getAvailableAssessmentNumbers($subjectId, $semester, $academicYear): array
    {
        $query = static::where('subject_id', $subjectId)
            ->where('semester', $semester);

        if ($academicYear === null || $academicYear === '') {
            $query->whereNull('academic_year');
        } else {
            $query->where('academic_year', $academicYear);
        }

        $existing = $query
            ->where('exam_category', 'assessment')
            ->orderBy('assessment_number')
            ->pluck('assessment_number')
            ->toArray();
        
        $next = empty($existing) ? 1 : max($existing) + 1;
        
        return array_merge($existing, [$next]);
    }

    /**
     * Get the localized exam name based on current locale
     */
    public function getLocalizedNameAttribute(): string
    {
        return $this->getLocalizedContent($this->exam_name_ne, $this->exam_name);
    }

    /**
     * Get the localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        return $this->getLocalizedContent($this->description_ne, $this->description);
    }

    /**
     * Helper method to get localized content
     */
    protected function getLocalizedContent($nepali, $english)
    {
        if (app()->getLocale() === 'ne' && !empty($nepali)) {
            return $nepali;
        }
        return $english ?? '';
    }

    /**
     * Get formatted exam type in current locale
     */
    public function getFormattedTypeAttribute(): string
    {
        $locale = app()->getLocale();
        $typeMap = [
            self::TYPE_INTERNAL => ['ne' => 'आंतरिक मूल्यांकन', 'en' => 'Internal'],
            self::TYPE_FINAL => ['ne' => 'अंतिम परीक्षा', 'en' => 'Final'],
            self::TYPE_MIDTERM => ['ne' => 'मध्यावधि परीक्षा', 'en' => 'Midterm'],
            self::TYPE_PRACTICAL => ['ne' => 'प्रायोगिक', 'en' => 'Practical'],
            self::TYPE_VIVA => ['ne' => 'मौखिक परीक्षा', 'en' => 'Viva'],
            self::TYPE_ASSIGNMENT => ['ne' => 'कार्यभार', 'en' => 'Assignment'],
            self::TYPE_ASSESSMENT => ['ne' => 'मूल्यांकन', 'en' => 'Assessment'],
        ];
        
        return $typeMap[$this->exam_type][$locale] ?? $this->exam_type;
    }

    /**
     * Get formatted exam category in current locale
     */
    public function getFormattedCategoryAttribute(): string
    {
        $locale = app()->getLocale();
        $categoryMap = [
            'assessment' => ['ne' => 'मूल्यांकन', 'en' => 'Assessment'],
            'ctevt' => ['ne' => 'CTEVT', 'en' => 'CTEVT'],
            'general' => ['ne' => 'सामान्य', 'en' => 'General'],
        ];
        
        return $categoryMap[$this->exam_category][$locale] ?? ucfirst($this->exam_category ?? 'general');
    }

    /**
     * Get formatted status in current locale
     */
    public function getFormattedStatusAttribute(): string
    {
        $locale = app()->getLocale();
        $statusMap = [
            self::STATUS_DRAFT => ['ne' => 'मस्यौदा', 'en' => 'Draft'],
            self::STATUS_PUBLISHED => ['ne' => 'प्रकाशित', 'en' => 'Published'],
            self::STATUS_ARCHIVED => ['ne' => 'अभिलेखित', 'en' => 'Archived'],
        ];
        
        return $statusMap[$this->status][$locale] ?? $this->status;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PUBLISHED => 'bg-green-100 text-green-700',
            self::STATUS_DRAFT => 'bg-yellow-100 text-yellow-700',
            self::STATUS_ARCHIVED => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Scope to get exams for a specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to get exams for a specific subject
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to get exams by type
     */
    public function scopeByType($query, $examType)
    {
        return $query->where('exam_type', $examType);
    }

    /**
     * Scope to get exams by category (assessment, ctevt, general)
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('exam_category', $category);
    }

    /**
     * Scope to get published exams only
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope to get exams by academic year (BS)
     * Filters exams based on BS academic year from student table
     */
    public function scopeForYear($query, $year)
    {
        if (empty($year)) {
            return $query;
        }
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to order by created date (newest first)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if exam is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Get total number of students who have marks for this exam
     */
    public function getMarkedCountAttribute(): int
    {
        return $this->marks()->distinct('student_id')->count('student_id');
    }

    /**
     * Get average marks for this exam
     */
    public function getAverageMarksAttribute(): float
    {
        return $this->marks()->avg('marks_obtained') ?? 0;
    }

    /**
     * Get list of academic years (for use in views)
     */
    public function getAcademicYears(): array
    {
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[] = ($i - 1) . '-' . $i;
        }
        return $years;
    }

    /**
     * Get the exam date formatted in AD (e.g., 2024-01-15)
     */
    public function getFormattedDateAdAttribute(): string
    {
        return $this->exam_date ? $this->exam_date->format('Y-m-d') : '-';
    }

    /**
     * Get the exam date formatted in BS (e.g., 2080-10-01)
     */
    public function getFormattedDateBsAttribute(): string
    {
        return $this->exam_date_bs ?: '-';
    }

    /**
     * Get the localized exam date in both AD and BS format
     * Returns HTML with both dates displayed
     */
    public function getLocalizedDateAttribute(): string
    {
        $adDate = $this->formatted_date_ad;
        $bsDate = $this->formatted_date_bs;
        
        return "<span class='text-gray-700'>{$adDate}</span><span class='text-gray-500 mx-1'>/</span><span class='text-gray-700'>{$bsDate}</span>";
    }
}
