<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\NepaliContentHelper;

class Semester extends Model
{
    use SoftDeletes;

    protected $table = 'semesters';

    protected $fillable = [
        'number',
        'name',
        'name_ne',
        'academic_year',
        'academic_year_bs',
        'start_date',
        'start_date_bs',
        'end_date',
        'end_date_bs',
        'status',
        'is_active',
        'max_credits',
        'total_weeks',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'max_credits' => 'integer',
        'total_weeks' => 'integer',
    ];

    /**
     * Semester number to ordinal name map
     */
    public static function getOrdinalName(int $number): string
    {
        $map = [
            1 => 'First Semester',
            2 => 'Second Semester',
            3 => 'Third Semester',
            4 => 'Fourth Semester',
            5 => 'Fifth Semester',
            6 => 'Sixth Semester',
            7 => 'Seventh Semester',
            8 => 'Eighth Semester',
        ];
        return $map[$number] ?? "Semester {$number}";
    }

    /**
     * Get localized name
     */
    public function getLocalizedNameAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent($this->name_ne, $this->name) ?? $this->name ?? '';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'open'     => 'bg-green-100 text-green-700 border border-green-200',
            'closed'   => 'bg-gray-100 text-gray-600 border border-gray-200',
            'upcoming' => 'bg-blue-100 text-blue-700 border border-blue-200',
            default    => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'open'     => 'bi-unlock',
            'closed'   => 'bi-lock',
            'upcoming' => 'bi-hourglass-split',
            default    => 'bi-question',
        };
    }

    /**
     * Scope for active semester
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for open semesters
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Get subjects for this semester
     */
    public function subjects()
    {
        return Subject::where('semester', (string) $this->number);
    }

    /**
     * Get student count for this semester
     */
    public function getStudentCountAttribute(): int
    {
        return \App\Models\Student::where('semester', (string) $this->number)
            ->where('status', 'active')
            ->where('is_alumni', false)
            ->count();
    }

    /**
     * Get subject count for this semester
     */
    public function getSubjectCountAttribute(): int
    {
        return \App\Models\Subject::where('semester', (string) $this->number)
            ->where('status', 'active')
            ->count();
    }
}
