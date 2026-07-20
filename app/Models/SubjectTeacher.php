<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubjectTeacher extends Model
{
    use HasFactory;

    protected $table = 'subject_teacher';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'semester',
        'role',
        'notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the subject for this assignment
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the teacher for this assignment
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
        return $this->hasOneThrough(
            User::class,
            Teacher::class,
            'id',
            'id',
            'teacher_id',
            'user_id'
        );
    }

    /**
     * Scope to get assignments for a specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to get primary teachers only
     */
    public function scopePrimary($query)
    {
        return $query->where('role', 'primary');
    }

    /**
     * Scope to get assistant teachers only
     */
    public function scopeAssistant($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Get formatted semester display
     */
    public function getFormattedSemesterAttribute()
    {
        if (!$this->semester) {
            return 'N/A';
        }
        
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
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        $locale = app()->getLocale();
        $roleName = $this->role;
        
        if ($roleName === 'primary') {
            return $locale === 'ne' ? 'प्राथमिक शिक्षक' : 'Primary Teacher';
        } elseif ($roleName === 'assistant') {
            return $locale === 'ne' ? 'सहायक शिक्षक' : 'Assistant Teacher';
        } elseif ($roleName === 'guest') {
            return $locale === 'ne' ? 'अतिथि शिक्षक' : 'Guest Teacher';
        }
        
        return ucfirst($roleName ?? '');
    }
}


