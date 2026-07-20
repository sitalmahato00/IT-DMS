<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\Media;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'roll_no',
        'registration_number',
        'semester',
        'section',
        'parent_id',
        'date_of_birth',
        'date_of_birth_bs',
        'address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'batch_year',
        'academic_year',
        'academic_year_bs',
        'enrollment_date',
        'expected_graduation_year',
        'gender',
        'blood_group',
        'national_id_number',
        'emergency_contact',
        'emergency_contact_name',
        'emergency_relationship',
        'is_active',
        'phone',
        'secondary_phone',
        'profile_photo_path',
        'id_document_path',
        'certificate_paths',
        'department',
        'program',
        'bio',
        'notes',
        'medical_conditions',
        'allergies',
        'disability_status',
        'status',
        'is_alumni',
        'alumni_from',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'certificate_paths' => 'array',
        'is_active' => 'boolean',
        'is_alumni' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the linked parent user account.
     */
    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get attendance records for this student
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_students')->withTimestamps();
    }

    /**
     * Get exam marks for this student
     */
    public function examMarks()
    {
        return $this->hasMany(ExamMark::class, 'student_id');
    }

    /**
     * Get legacy marks for this student.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if (!empty($this->profile_photo_path) && Storage::disk('public')->exists($this->profile_photo_path)) {
            return Storage::url($this->profile_photo_path);
        }
        return asset('images/default-logo.svg');
    }

    public function getIdDocumentUrlAttribute(): ?string
    {
        if (!empty($this->id_document_path) && Storage::disk('public')->exists($this->id_document_path)) {
            return Storage::url($this->id_document_path);
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

    /**
     * Get attendance percentage for this student
     */
    public function getAttendancePercentage($subjectId = null, $attendanceType = 'class')
    {
        $query = DB::table('attendance')
            ->where('student_id', $this->id)
            ->where('attendance_type', $attendanceType);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $total = $query->count();
        if ($total === 0) {
            return 100; // Default to 100% if no attendance records
        }

        $present = $query->where('status', 'present')->count();
        return round(($present / $total) * 100, 1);
    }

    /**
     * Cache examined marks per subject/category (examId optional).
     *
     * @var array
     */
    protected $examMarkCache = [];

    /**
     * Apply assessment number filtering with fallback to the linked exam record.
     */
    private function scopeAssessmentNumber($query, $assessmentNumber)
    {
        if ($assessmentNumber === null || $assessmentNumber === '') {
            return $query;
        }

        return $query->where(function ($assessmentQuery) use ($assessmentNumber) {
            $assessmentQuery->where('assessment_number', $assessmentNumber)
                ->orWhereHas('exam', function ($examQuery) use ($assessmentNumber) {
                    $examQuery->where('assessment_number', $assessmentNumber);
                });
        });
    }

    /**
     * Return cached ExamMark for the given subject/category/exam/assessment number.
     */
    public function getExamMarkForSubject($subjectId, $category = 'assessment', $examId = null, $assessmentNumber = null, $publishedOnly = false)
    {
        $cacheKey = "{$subjectId}:{$category}:" . ($examId ?? 'any') . ':' . ($assessmentNumber ?? 'any');

        if (array_key_exists($cacheKey, $this->examMarkCache)) {
            return $this->examMarkCache[$cacheKey];
        }

        $query = ExamMark::with('exam')
            ->where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->when($examId, fn($q) => $q->where('exam_id', $examId))
            ->whereHas('exam', function($q) use ($category, $publishedOnly) {
                $q->where('exam_category', $category);
                if ($publishedOnly) {
                    $q->where('status', Exam::STATUS_PUBLISHED);
                }
            });

        $query = $this->scopeAssessmentNumber($query, $assessmentNumber);

        // Prefer the same assessment number (if provided), else return latest entry.
        $mark = $query->orderByDesc('updated_at')->first();
        $this->examMarkCache[$cacheKey] = $mark;
        return $mark;
    }

    /**
     * Get component marks for CTEVT exams
     */
    public function getComponentMarks($subjectId, $component, $publishedOnly = false)
    {
        $examMark = $this->getExamMarkForSubject($subjectId, 'ctevt', null, null, $publishedOnly);

        if (!$examMark) {
            return (object)[
                'full' => 0,
                'pass' => 0,
                'obtained' => 0,
                'is_pass' => null,
            ];
        }

        $componentConfig = [
            'TI' => [
                'full_field' => 'theory_internal_full_marks',
                'pass_field' => 'theory_internal_pass_marks',
                'obtained_field' => 'theory_internal_marks',
                'exam_full_field' => 'theory_internal_max_marks',
                'exam_pass_field' => 'theory_internal_pass_marks'
            ],
            'TE' => [
                'full_field' => 'theory_external_full_marks',
                'pass_field' => 'theory_external_pass_marks',
                'obtained_field' => 'theory_external_marks',
                'exam_full_field' => 'theory_external_max_marks',
                'exam_pass_field' => 'theory_external_pass_marks'
            ],
            'PI' => [
                'full_field' => 'practical_internal_full_marks',
                'pass_field' => 'practical_internal_pass_marks',
                'obtained_field' => 'practical_internal_marks',
                'exam_full_field' => 'practical_internal_max_marks',
                'exam_pass_field' => 'practical_internal_pass_marks'
            ],
            'PE' => [
                'full_field' => 'practical_external_full_marks',
                'pass_field' => 'practical_external_pass_marks',
                'obtained_field' => 'practical_external_marks',
                'exam_full_field' => 'practical_external_max_marks',
                'exam_pass_field' => 'practical_external_pass_marks'
            ],
        ];

        $config = $componentConfig[$component] ?? null;

        if (!$config) {
            return (object)[
                'full' => 0,
                'pass' => 0,
                'obtained' => 0,
                'is_pass' => null,
            ];
        }

        $full = 0;
        $pass = 0;
        $obtained = 0;

        if ($examMark) {
            if (isset($examMark->{$config['full_field']}) && $examMark->{$config['full_field']} !== null) {
                $full = floatval($examMark->{$config['full_field']});
            } elseif ($examMark->exam && isset($examMark->exam->{$config['exam_full_field']}) && $examMark->exam->{$config['exam_full_field']} !== null) {
                $full = floatval($examMark->exam->{$config['exam_full_field']});
            }

            if (isset($examMark->{$config['pass_field']}) && $examMark->{$config['pass_field']} !== null) {
                $pass = floatval($examMark->{$config['pass_field']});
            } elseif ($examMark->exam && isset($examMark->exam->{$config['exam_pass_field']}) && $examMark->exam->{$config['exam_pass_field']} !== null) {
                $pass = floatval($examMark->exam->{$config['exam_pass_field']});
            }

            if (isset($examMark->{$config['obtained_field']}) && $examMark->{$config['obtained_field']} !== null) {
                $obtained = floatval($examMark->{$config['obtained_field']});
            }
        }

        // If no per-component marks, fallback to overall marks_obtained (for legacy data)
        if ($obtained === 0 && $examMark && isset($examMark->marks_obtained) && !is_null($examMark->marks_obtained)) {
            $obtained = floatval($examMark->marks_obtained);
        }

        $isPass = null;
        if ($full > 0) {
            $isPass = $obtained >= $pass;
        }

        return (object)[
            'full' => $full,
            'pass' => $pass,
            'obtained' => $obtained,
            'is_pass' => $isPass,
        ];
    }

    /**
     * Get assessment marks (sum of all exams for subject/category, optional number filter)
     */
    public function getAssessmentMarks($subjectId, $category = 'assessment', $assessmentNumber = null, $publishedOnly = false)
    {
        $query = ExamMark::with('exam')
            ->where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->where('marks_status', '!=', 'absent')
            ->whereHas('exam', function($q) use ($category, $publishedOnly) {
                $q->where('exam_category', $category);
                if ($publishedOnly) {
                    $q->where('status', Exam::STATUS_PUBLISHED);
                }
            });

        $marks = $this->scopeAssessmentNumber($query, $assessmentNumber)->get();

        if ($marks->isEmpty()) {
            return (object)[
                'full' => 0,
                'pass' => 0,
                'obtained' => 0,
                'is_pass' => null,
                'is_absent' => false,
            ];
        }

        $totalObtained = $marks->sum(fn ($mark) => (float) $mark->effective_obtained_marks);
        $totalFull = $marks->sum(fn ($mark) => (float) $mark->effective_full_marks);
        $totalPass = $marks->sum(fn ($mark) => (float) $mark->effective_passing_marks);
        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;

        return (object)[
            'full' => $totalFull,
            'pass' => $totalPass,
            'obtained' => $totalObtained,
            'is_pass' => $percentage >= 40,
            'is_absent' => false,
        ];
    }

    /**
     * Get total marks across subjects for a specific category
     */
    public function getTotalMarks($subjectIds = [], $category = null, $assessmentNumber = null, $publishedOnly = false)
    {
        $query = ExamMark::with('exam')
            ->where('student_id', $this->id)
            ->whereNotNull('marks_obtained')
            ->where('marks_status', '!=', 'absent');

        if (!empty($subjectIds)) {
            $query->whereIn('subject_id', $subjectIds);
        }

        $query->whereHas('exam', function ($q) use ($category, $publishedOnly) {
            if ($category) {
                $q->where('exam_category', $category);
            }
            if ($publishedOnly) {
                $q->where('status', Exam::STATUS_PUBLISHED);
            }
        });

        if ($category === 'assessment') {
            $query = $this->scopeAssessmentNumber($query, $assessmentNumber);
        }

        return $query->get()->sum(fn ($mark) => (float) $mark->effective_obtained_marks);
    }

    /**
     * Get total full marks across subjects for a specific category
     */
    public function getTotalFullMarks($subjectIds = [], $category = null, $assessmentNumber = null, $publishedOnly = false)
    {
        $query = ExamMark::with('exam')
            ->where('student_id', $this->id);

        if (!empty($subjectIds)) {
            $query->whereIn('subject_id', $subjectIds);
        }

        $query->whereHas('exam', function ($q) use ($category, $publishedOnly) {
            if ($category) {
                $q->where('exam_category', $category);
            }
            if ($publishedOnly) {
                $q->where('status', Exam::STATUS_PUBLISHED);
            }
        });

        if ($category === 'assessment') {
            $query = $this->scopeAssessmentNumber($query, $assessmentNumber);
        }

        return $query->get()->sum(fn ($mark) => (float) $mark->effective_full_marks);
    }
}

