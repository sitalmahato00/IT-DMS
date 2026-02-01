<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'subjects';
    
    protected $fillable = [
        'subject_name',
        'subject_code',
        'semester',
        'teacher_id',
        'credits',
        'status',
        // Comprehensive course detail fields
        'category',
        'description',
        'syllabus',
        'learning_objectives',
        'theory_percentage',
        'practical_percentage',
        'internal_percentage',
        'external_percentage',
        'lecture_hours',
        'practical_hours',
        'tutorial_hours',
        'prerequisite',
        'start_date',
        'end_date',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'theory_percentage' => 'integer',
        'practical_percentage' => 'integer',
        'internal_percentage' => 'integer',
        'external_percentage' => 'integer',
        'lecture_hours' => 'integer',
        'practical_hours' => 'integer',
        'tutorial_hours' => 'integer',
    ];

    /**
     * Get the teacher for this course
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Get the user through teacher relationship
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Teacher::class, 'id', 'id', 'teacher_id', 'user_id');
    }

    /**
     * Get students enrolled in this course
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student')->withTimestamps();
    }

    /**
     * Get marks for this course
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'subject_id');
    }

    /**
     * Scope to get active courses
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get courses for a specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to get courses by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get status attribute with default
     */
    public function getStatusAttribute()
    {
        return $this->attributes['status'] ?? 'active';
    }

    /**
     * Get credits attribute with default
     */
    public function getCreditsAttribute()
    {
        return $this->attributes['credits'] ?? 3;
    }

    /**
     * Get syllabus as array (split by newlines)
     */
    public function getSyllabusArrayAttribute()
    {
        if (empty($this->syllabus)) {
            return [];
        }
        return array_filter(array_map('trim', explode("\n", $this->syllabus)));
    }

    /**
     * Get learning objectives as array
     */
    public function getObjectivesArrayAttribute()
    {
        if (empty($this->learning_objectives)) {
            return [];
        }
        return array_filter(array_map('trim', explode("\n", $this->learning_objectives)));
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('Y-m-d') . ' to ' . $this->end_date->format('Y-m-d');
        }
        return 'Not specified';
    }

    /**
     * Get total teaching hours per week
     */
    public function getTotalHoursAttribute()
    {
        return ($this->lecture_hours ?? 4) + ($this->practical_hours ?? 2) + ($this->tutorial_hours ?? 1);
    }
}

