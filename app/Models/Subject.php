<?php

namespace App\Models;

use App\Helpers\NepaliContentHelper;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    
    protected $fillable = [
        'subject_name',
        'subject_name_ne',
        'subject_code',
        'semester',
        'teacher_id',
        'status',
        'description',
        'description_ne',
    ];

    /**
     * Get the localized subject name based on current locale
     */
    public function getLocalizedNameAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->subject_name_ne,
            $this->subject_name
        ) ?? $this->subject_name ?? '';
    }

    /**
     * Get the localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->description_ne,
            $this->description
        ) ?? $this->description ?? '';
    }

    /**
     * Get formatted semester in current locale
     */
    public function getFormattedSemesterAttribute(): string
    {
        if (!$this->semester) return '';
        
        $locale = app()->getLocale();
        $semesterMap = [
            '1' => ['ne' => 'प्रथम सेमेस्टर', 'en' => '1st Semester'],
            '2' => ['ne' => 'द्वितीय सेमेस्टर', 'en' => '2nd Semester'],
            '3' => ['ne' => 'तृतीय सेमेस्टर', 'en' => '3rd Semester'],
            '4' => ['ne' => 'चतुर्थ सेमेस्टर', 'en' => '4th Semester'],
            '5' => ['ne' => 'पंचम सेमेस्टर', 'en' => '5th Semester'],
            '6' => ['ne' => 'षष्ठ सेमेस्टर', 'en' => '6th Semester'],
        ];
        
        return $semesterMap[$this->semester][$locale] ?? $this->semester;
    }

    /**
     * Get status label in current locale
     */
    public function getStatusLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return match($this->status) {
            'active' => $locale === 'ne' ? 'सक्रिय' : 'Active',
            'inactive' => $locale === 'ne' ? 'निस्क्रिय' : 'Inactive',
            default => $locale === 'ne' ? 'सक्रिय' : 'Active',
        };
    }

    /**
     * Get students enrolled in this subject
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'subject_students')->withTimestamps();
    }

    /**
     * Get marks for this subject
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'subject_id');
    }

    /**
     * Scope to get subjects for a specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to order subjects by semester and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('semester')->orderBy('subject_name');
    }

    /**
     * Get average marks for this subject
     */
    public function getAverageMarksAttribute()
    {
        return $this->marks()->avg('marks_obtained');
    }

    /**
     * Scope to get active subjects only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

