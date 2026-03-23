<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\ExamMark;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'roll_no',
        'semester',
        'parent_id',
        'date_of_birth',
        'date_of_birth_bs',
        'address',
        'batch_year',
        'academic_year',
        'academic_year_bs',
        'gender',
        'blood_group',
        'emergency_contact',
        'is_active',
        'phone',
        'profile_photo_path',
        'department',
        'bio',
        'status',
        'is_alumni',
        'alumni_from',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Get attendance percentage for this student
     */
    public function getAttendancePercentage($subjectId = null)
    {
        $query = DB::table('attendance')
            ->where('student_id', $this->id);

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
     * Return cached ExamMark for the given subject/category/exam/assessment number.
     */
    public function getExamMarkForSubject($subjectId, $category = 'assessment', $examId = null, $assessmentNumber = null)
    {
        $cacheKey = "{$subjectId}:{$category}:" . ($examId ?? 'any') . ':' . ($assessmentNumber ?? 'any');

        if (array_key_exists($cacheKey, $this->examMarkCache)) {
            return $this->examMarkCache[$cacheKey];
        }

        $query = ExamMark::where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->when($examId, fn($q) => $q->where('exam_id', $examId))
            ->when($assessmentNumber, fn($q) => $q->where('assessment_number', $assessmentNumber))
            ->whereHas('exam', function($q) use ($category) {
                $q->where('exam_category', $category);
            });

        // Prefer the same assessment number (if provided), else return latest entry.
        $mark = $query->orderByDesc('updated_at')->first();
        $this->examMarkCache[$cacheKey] = $mark;
        return $mark;
    }

    /**
     * Get component marks for CTEVT exams
     */
    public function getComponentMarks($subjectId, $component)
    {
        $examMark = $this->getExamMarkForSubject($subjectId, 'ctevt');

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
    public function getAssessmentMarks($subjectId, $category = 'assessment', $assessmentNumber = null)
    {
        $marks = ExamMark::where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->where('marks_status', '!=', 'absent')
            ->when($assessmentNumber, fn($q) => $q->where('assessment_number', $assessmentNumber))
            ->whereHas('exam', function($q) use ($category) {
                $q->where('exam_category', $category);
            })
            ->get();

        if ($marks->isEmpty()) {
            return (object)[
                'full' => 0,
                'pass' => 0,
                'obtained' => 0,
                'is_pass' => null,
                'is_absent' => false,
            ];
        }

        $totalObtained = $marks->sum('marks_obtained');
        $totalFull = $marks->sum('full_marks');
        $totalPass = $marks->sum('passing_marks');
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
    public function getTotalMarks($subjectIds = [], $category = null, $assessmentNumber = null)
    {
        $query = ExamMark::where('student_id', $this->id)
            ->whereNotNull('marks_obtained')
            ->where('marks_status', '!=', 'absent');

        if (!empty($subjectIds)) {
            $query->whereIn('subject_id', $subjectIds);
        }

        if ($category) {
            $query->whereHas('exam', function($q) use ($category) {
                $q->where('exam_category', $category);
            });
        }

        if ($category === 'assessment' && $assessmentNumber) {
            $query->where('assessment_number', $assessmentNumber);
        }

        return $query->sum('marks_obtained');
    }

    /**
     * Get total full marks across subjects for a specific category
     */
    public function getTotalFullMarks($subjectIds = [], $category = null, $assessmentNumber = null)
    {
        $query = ExamMark::where('student_id', $this->id);

        if (!empty($subjectIds)) {
            $query->whereIn('subject_id', $subjectIds);
        }

        if ($category) {
            $query->whereHas('exam', function($q) use ($category) {
                $q->where('exam_category', $category);
            });
        }

        if ($category === 'assessment' && $assessmentNumber) {
            $query->where('assessment_number', $assessmentNumber);
        }

        return $query->sum('full_marks');
    }
}
