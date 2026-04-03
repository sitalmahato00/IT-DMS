<?php

namespace App\Models;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Support\Media;

class Teacher extends Model
{
    use Notifiable;
    protected $fillable = [
        'user_id',
        'teacher_code',
        'qualification',
        'phone',
        'alternate_email',
        'secondary_phone',
        'national_id_number',
        'date_of_birth',
        'joining_date',
        'years_of_experience',
        'specialization',
        'employment_type',
        'previous_institution',
        'certifications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
        'address',
        'staff_room_location',
        'employee_type',
        'work_shift',
        'timetable_assignment',
        'salary',
        'bank_name',
        'bank_account_number',
        'tax_identification_number',
        'blood_group',
        'medical_conditions',
        'emergency_notes',
        'resume_path',
        'certificate_paths',
        'id_proof_path',
        'access_level',
        'profile_visibility',
        'social_links',
        'notes',
        'profile_photo_path',
        'department',
        'bio',
        'status',
        'gender',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'years_of_experience' => 'integer',
        'salary' => 'decimal:2',
        'certifications' => 'array',
        'certificate_paths' => 'array',
        'social_links' => 'array',
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

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!empty($this->profile_photo_path) && Storage::disk('public')->exists($this->profile_photo_path)) {
            return Storage::url($this->profile_photo_path);
        }
        return null;
    }

    public function getResumeUrlAttribute(): ?string
    {
        if (!empty($this->resume_path) && Storage::disk('public')->exists($this->resume_path)) {
            return Storage::url($this->resume_path);
        }
        return null;
    }

    public function getIdProofUrlAttribute(): ?string
    {
        if (!empty($this->id_proof_path) && Storage::disk('public')->exists($this->id_proof_path)) {
            return Storage::url($this->id_proof_path);
        }
        return null;
    }

    public function getCertificateUrlsAttribute(): array
    {
        return collect($this->certificate_paths ?? [])
            ->filter()
            ->map(function ($path) {
                if (Storage::disk('public')->exists($path)) {
                    return Storage::url($path);
                }
                return null;
            })
            ->filter()
            ->values()
            ->all();
    }
}
