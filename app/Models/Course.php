<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'subjects';
    
protected $fillable = [
        'subject_name',
        'subject_name_ne',
        'subject_code',
        'semester',
        'teacher_id',
        'lab_technician_id',
        'credits',
        'status',
        'category',
        'description',
        'description_ne',
        'subject_type',
        'max_students',
        'min_students',
        'elective_group',
        'is_elective_open',
        'lecture_hours',
        'practical_hours',
        'tutorial_hours',
        'prerequisite',
        'remarks',
        'theory_percentage',
        'practical_percentage',
        'internal_percentage',
        'external_percentage',
    ];

    protected $casts = [
        'is_elective_open' => 'boolean',
        'max_students' => 'integer',
        'min_students' => 'integer',
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
     * Get the lab technician for this course
     */
    public function labTechnician()
    {
        return $this->belongsTo(Teacher::class, 'lab_technician_id');
    }

    /**
     * Get students enrolled in this course (via subject_students pivot)
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'subject_students', 'subject_id', 'student_id')->withTimestamps();
    }

    /**
     * Get marks for this course
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'subject_id');
    }

    /**
     * Get teacher assignments for this course/subject (subject_teacher pivot)
     */
    public function teacherAssignments()
    {
        return $this->hasMany(SubjectTeacher::class, 'subject_id');
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

    /**
     * Get assigned teacher from legacy teacher_id field
     * Format: "Name" (from subjects.teacher_id)
     */
    public function getAssignedTeachersAttribute()
    {
        return $this->teacher?->user?->name ?? 'Not Assigned';
    }

    /**
     * Get computed assigned teacher (from legacy teacher_id field)
     */
    public function getComputedAssignedTeachersAttribute()
    {
        return $this->teacher?->user?->name ?? 'Not Assigned';
    }
}

