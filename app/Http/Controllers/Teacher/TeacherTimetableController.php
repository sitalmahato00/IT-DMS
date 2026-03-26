<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\TimetableSlot;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class TeacherTimetableController extends Controller
{
    /**
     * Get teacher's assigned subjects with semester info
     */
    private function getTeacherAssignments()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [
                'subjectIds' => [],
                'semesters' => [],
            ];
        }
        
        $assignments = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get();
        
        $subjectIds = $assignments->pluck('subject_id')->toArray();
        
        // Get unique semesters from assignments
        $semesters = $assignments->pluck('semester')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        
        // Also get semesters from subjects if not in assignments
        $subjectSemesters = Subject::whereIn('id', $subjectIds)
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->values()
            ->toArray();
        
        // Merge and unique
        $allSemesters = array_unique(array_merge($semesters, $subjectSemesters));
        sort($allSemesters);
        
        return [
            'subjectIds' => $subjectIds,
            'semesters' => $allSemesters,
        ];
    }

    /**
     * Display teacher's timetable
     */
    public function index(Request $request)
    {
        $assignments = $this->getTeacherAssignments();
        $subjectIds = $assignments['subjectIds'];
        $semesters = $assignments['semesters'];
        
        $selectedSemester = $request->get('semester', '');
        
        // Get subjects for dropdown
        $subjectsQuery = SubjectTeacher::whereHas('subject', function($q) use ($subjectIds) {
            $q->whereIn('id', $subjectIds);
        })->with('subject');
        
        if ($selectedSemester) {
            $subjectsQuery->where('semester', $selectedSemester);
        }
        
        $subjects = $subjectsQuery->get()->map(function ($st) {
            return [
                'id' => $st->subject->id,
                'name' => $st->subject->subject_name,
                'code' => $st->subject->subject_code,
                'semester' => $st->semester ?? $st->subject->semester,
            ];
        })->values();
        
        // Get subject IDs for this semester filter
        $filteredSubjectIds = $subjects->pluck('id')->toArray();
        
        // If no semester selected, use all subjects
        $timetableSubjectIds = empty($selectedSemester) ? $subjectIds : $filteredSubjectIds;
        
        // Get timetable slots for teacher's subjects
        $timetableQuery = TimetableSlot::whereIn('subject_id', $timetableSubjectIds)
            ->with(['subject', 'teacher']);
        
        $timetableSlots = $timetableQuery->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
        
        // Timetable slots store day names in lowercase.
        $days = TimetableSlot::getDaysOfWeek();
        $timetableByDay = [];
        
        foreach ($days as $day) {
            $timetableByDay[$day] = $timetableSlots->filter(function ($slot) use ($day) {
                return $slot->day_of_week === $day;
            })->values();
        }
        
        // Stats
        $totalSlots = $timetableSlots->count();
        $totalSubjects = count($timetableSubjectIds);
        
        return view('teacher.timetable', [
            'timetableByDay' => $timetableByDay,
            'subjects' => $subjects,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'totalSlots' => $totalSlots,
            'totalSubjects' => $totalSubjects,
            'days' => $days,
        ]);
    }
}
