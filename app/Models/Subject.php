<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    
    protected $fillable = [
        'subject_name',
        'subject_code',
        'semester',
        'teacher_id',
    ];

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
     * Get average marks for this subject
     */
    public function getAverageMarksAttribute()
    {
        return $this->marks()->avg('marks_obtained');
    }
}

