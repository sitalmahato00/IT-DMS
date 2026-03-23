<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectiveEnrollment extends Model
{
    use HasFactory;

    protected $table = 'elective_enrollments';

    protected $fillable = [
        'student_id',
        'subject_id',
        'semester',
        'academic_year',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the student for this enrollment
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the subject for this enrollment
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the approver (admin user)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: only approved enrollments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: only pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'approved'  => 'bg-green-100 text-green-700 border border-green-200',
            'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
            'rejected'  => 'bg-red-100 text-red-700 border border-red-200',
            'withdrawn' => 'bg-gray-100 text-gray-600 border border-gray-200',
            default     => 'bg-gray-100 text-gray-600',
        };
    }
}
