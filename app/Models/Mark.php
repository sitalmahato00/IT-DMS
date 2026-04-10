<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $table = 'marks';
    
    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'exam_type',
        'academic_year',
        'academic_year_bs',
        'marks_obtained',
        'full_marks',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'marks_obtained' => 'integer',
        'full_marks' => 'integer',
    ];

    /**
     * Get the student for this mark record
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the subject for this mark record
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the user through student relationship
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Student::class, 'id', 'id', 'student_id', 'user_id');
    }

    /**
     * Calculate percentage
     */
    public function getPercentageAttribute()
    {
        if ($this->full_marks && $this->full_marks > 0) {
            return round(($this->marks_obtained / $this->full_marks) * 100, 2);
        }
        return 0;
    }

    /**
     * Scope to get marks for a specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get marks for a specific subject
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to get marks by exam type
     */
    public function scopeByExamType($query, $examType)
    {
        return $query->where('exam_type', $examType);
    }

    /**
     * Scope to get marks for a specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->whereHas('student', function($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }
}


