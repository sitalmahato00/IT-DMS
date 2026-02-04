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
        'exam_name',
        'exam_name_ne',
        'academic_year',
        'semester',
        'subject_id',
        'course_id',
        'exam_type',
        'full_marks',
        'passing_marks',
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
     * Get the course for this exam
     */
    public function course(): BelongsTo
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
     * Scope to get published exams only
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope to get exams by academic year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to order by exam date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('exam_date', 'desc');
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
}

