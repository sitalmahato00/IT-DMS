<?php

namespace App\Models;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class Teacher extends Model
{
    use Notifiable;
    protected $fillable = [
        'user_id',
        'teacher_code',
        'qualification',
        'phone',
        'address',
        'profile_photo_path',
        'department',
        'bio',
        'status',
        'gender',
    ];

    /**
     * Get the user that owns the teacher record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subjects assigned to this teacher through the pivot table.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->withPivot('semester', 'role', 'notes', 'assigned_at')
                    ->withTimestamps();
    }

    /**
     * Get the subject_teacher pivot records.
     */
    public function subjectAssignments()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
    }

    /**
     * Get the subjects as a formatted string (for display).
     */
    public function getAssignedSubjectsAttribute()
    {
        return $this->subjects()->get()->map(function ($subject) {
            $pivot = $subject->pivot;
            $semester = $pivot->semester ? " ({$pivot->semester} Sem)" : '';
            return "{$subject->subject_name}{$semester}";
        })->implode(', ');
    }

    /**
     * Get subjects assigned to this teacher.
     *
     * Uses pivot table first (subject_teacher). If legacy subjects.teacher_id exists,
     * includes those as well, but avoids querying a missing column.
     */
    public function assignedSubjects()
    {
        $subjects = $this->subjects()->get();

        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $this->id)->get();
            $subjects = $subjects->concat($legacySubjects);
        }

        return $subjects->unique('id')->values();
    }

    /**
     * Get semesters this teacher is assigned to.
     */
    public function getAssignedSemestersAttribute()
    {
        return $this->subjectAssignments()
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();
    }
}
