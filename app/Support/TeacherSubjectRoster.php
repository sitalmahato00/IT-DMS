<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeacherSubjectRoster
{
    public static function studentIdsForSubject(int $subjectId): array
    {
        return self::studentRowsForSubject($subjectId)
            ->pluck('student_id')
            ->unique()
            ->values()
            ->all();
    }

    public static function studentIdsForSubjects(array $subjectIds): array
    {
        return collect($subjectIds)
            ->filter()
            ->map(fn ($subjectId) => self::studentIdsForSubject((int) $subjectId))
            ->flatten()
            ->unique()
            ->values()
            ->all();
    }

    public static function studentCountForSubject(int $subjectId): int
    {
        return count(self::studentIdsForSubject($subjectId));
    }

    public static function studentRowsForSubject(int $subjectId): Collection
    {
        $enrolledRows = self::currentEnrolledStudentsQuery($subjectId)->get();

        if ($enrolledRows->isNotEmpty()) {
            return $enrolledRows;
        }

        return self::semesterFallbackStudentsQuery($subjectId)->get();
    }

    private static function currentEnrolledStudentsQuery(int $subjectId)
    {
        return self::applyVisibilityRules(
            DB::table('subject_students')
                ->join('students', 'subject_students.student_id', '=', 'students.id')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->join('subjects', 'subject_students.subject_id', '=', 'subjects.id')
                ->where('subject_students.subject_id', $subjectId)
                ->whereColumn('students.semester', 'subjects.semester')
                ->select(
                    'students.id as student_id',
                    'students.user_id',
                    'students.roll_no',
                    'students.registration_number',
                    'students.phone',
                    'students.gender',
                    'students.date_of_birth',
                    'students.date_of_birth_bs',
                    'students.academic_year',
                    'students.academic_year_bs',
                    'students.address',
                    'students.bio',
                    'students.status',
                    'students.is_alumni',
                    'students.blood_group',
                    'students.emergency_contact',
                    'students.semester',
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.profile_photo_path',
                    'users.role'
                )
                ->orderBy('users.name', 'asc')
        );
    }

    private static function semesterFallbackStudentsQuery(int $subjectId)
    {
        return self::applyVisibilityRules(
            DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->join('subjects', function ($join) use ($subjectId) {
                    $join->on('students.semester', '=', 'subjects.semester')
                        ->where('subjects.id', '=', $subjectId);
                })
                ->select(
                    'students.id as student_id',
                    'students.user_id',
                    'students.roll_no',
                    'students.registration_number',
                    'students.phone',
                    'students.gender',
                    'students.date_of_birth',
                    'students.date_of_birth_bs',
                    'students.academic_year',
                    'students.academic_year_bs',
                    'students.address',
                    'students.bio',
                    'students.status',
                    'students.is_alumni',
                    'students.blood_group',
                    'students.emergency_contact',
                    'students.semester',
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.profile_photo_path',
                    'users.role'
                )
                ->orderBy('users.name', 'asc')
        );
    }

    private static function applyVisibilityRules($query)
    {
        return $query
            ->where('users.role', 'student')
            ->where(function ($q) {
                $q->where('students.status', 'active')
                    ->orWhereNull('students.status');
            })
            ->where(function ($q) {
                $q->where('students.is_active', 1)
                    ->orWhereNull('students.is_active');
            })
            ->where(function ($q) {
                $q->where('students.is_alumni', 0)
                    ->orWhereNull('students.is_alumni');
            });
    }
}

