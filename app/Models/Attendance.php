<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Attendance extends Model
{
    use Notifiable;
    protected $table = 'attendance';
    
    protected $fillable = [
        'student_id',
        'teacher_id',
        'date',
        'date_bs',
        'subject',
        'subject_id',
        'attendance_type',
        'academic_year',
        'academic_year_bs',
        'status',
        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student for this attendance record
     * Uses students.id (role-specific ID, not users.id)
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the user through student relationship
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Student::class, 'id', 'id', 'student_id', 'user_id');
    }

    /**
     * Get the subject/course for this attendance record
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scope to get attendance for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to get attendance for a student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get attendance by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
