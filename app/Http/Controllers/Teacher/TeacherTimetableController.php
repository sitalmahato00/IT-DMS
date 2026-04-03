<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TimetableSlot;
use App\Traits\BuildsRoutineTimetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TeacherTimetableController extends Controller
{
    use BuildsRoutineTimetable;

    /**
     * Get the subject IDs assigned to the given teacher.
     */
    private function getAssignedSubjectIds($teacher): array
    {
        $assignedSubjectIds = SubjectTeacher::query()
            ->where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjectIds = Subject::query()
                ->where('teacher_id', $teacher->user_id)
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $assignedSubjectIds = array_values(array_unique(array_merge($assignedSubjectIds, $legacySubjectIds)));
        }

        return $assignedSubjectIds;
    }

    /**
     * Build a base query for active, non-holiday timetable slots.
     */
    private function buildSemesterTimetableQuery()
    {
        return TimetableSlot::query()
            ->where('is_active', true)
            ->where('is_holiday', false);
    }

    /**
     * Display teacher's timetable
     */
    public function index(Request $request)
    {
        return view('teacher.timetable', $this->buildTimetableContext($request));
    }

    /**
     * Display a printable teacher routine sheet.
     */
    public function print(Request $request)
    {
        return view('teacher.timetable-print', $this->buildTimetableContext($request));
    }

    /**
     * Build the teacher timetable context for screen and print.
     */
    private function buildTimetableContext(Request $request): array
    {
        $user = auth()->user();
        $teacher = $user?->teacher;
        $college = Department::first();

        if (!$teacher) {
            return [
                'timetableByDay' => [],
                'subjects' => collect(),
                'semesters' => [],
                'sections' => [],
                'selectedSemester' => '',
                'selectedSection' => '',
                'totalSlots' => 0,
                'totalSubjects' => 0,
                'activeDays' => 0,
                'days' => TimetableSlot::getDaysOfWeek(),
                'teacher' => null,
                'college' => $college,
                'timeRows' => collect(),
                'slotMatrix' => [],
                'gapOverrideMatrix' => [],
            ];
        }

        $selectedSemester = trim((string) $request->get('semester', ''));
        $selectedSection = trim((string) $request->get('section', ''));
        $assignedSubjectIds = $this->getAssignedSubjectIds($teacher);

        $semesters = $this->buildSemesterTimetableQuery()
            ->when(!empty($assignedSubjectIds), fn ($query) => $query->whereIn('subject_id', $assignedSubjectIds))
            ->whereNotNull('semester')
            ->distinct()
            ->orderByRaw('CAST(semester AS UNSIGNED)')
            ->pluck('semester')
            ->filter()
            ->map(fn ($semester) => (string) $semester)
            ->values()
            ->toArray();

        if (empty($semesters) && !empty($assignedSubjectIds)) {
            $semesters = Subject::query()
                ->whereIn('id', $assignedSubjectIds)
                ->whereNotNull('semester')
                ->distinct()
                ->orderByRaw('CAST(semester AS UNSIGNED)')
                ->pluck('semester')
                ->filter()
                ->map(fn ($semester) => (string) $semester)
                ->values()
                ->toArray();
        }

        if (empty($semesters)) {
            $semesters = $this->buildSemesterTimetableQuery()
                ->whereNotNull('semester')
                ->distinct()
                ->orderByRaw('CAST(semester AS UNSIGNED)')
                ->pluck('semester')
                ->filter()
                ->map(fn ($semester) => (string) $semester)
                ->values()
                ->toArray();
        }

        if ($selectedSemester === '' && !empty($semesters)) {
            $selectedSemester = (string) $semesters[0];
        }

        if ($selectedSemester !== '' && !in_array($selectedSemester, $semesters, true)) {
            $selectedSemester = !empty($semesters) ? (string) $semesters[0] : '';
        }

        $sections = $this->buildSemesterTimetableQuery()
            ->when($selectedSemester, fn ($query) => $query->where('semester', $selectedSemester))
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->filter()
            ->map(fn ($section) => (string) $section)
            ->sort()
            ->values()
            ->toArray();

        if ($selectedSection !== '' && !in_array($selectedSection, $sections, true)) {
            $selectedSection = '';
        }

        $timetableSlots = $this->buildSemesterTimetableQuery()
            ->when($selectedSemester, fn ($query) => $query->where('semester', $selectedSemester))
            ->when($selectedSection, fn ($query) => $query->where('section', $selectedSection))
            ->with(['subject', 'teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $visibleSubjects = $timetableSlots
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('subject_name')
            ->values();

        $subjects = $visibleSubjects
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                ];
            });

        $days = TimetableSlot::getDaysOfWeek();
        $timetableByDay = $this->buildTimetableByDay($timetableSlots, $days);
        $timeRows = $this->buildRoutineTimeRows($timetableSlots);
        $slotMatrix = $this->buildRoutineSlotMatrix($days, $timetableByDay, $timeRows);
        $gapOverrideMatrix = $this->buildRoutineGapOverrideMatrix($selectedSemester, $selectedSection);

        // Stats
        $totalSlots = $timetableSlots->count();
        $totalSubjects = $subjects->count();
        $activeDays = collect($timetableByDay)->filter(fn ($slots) => $slots->count() > 0)->count();
        $highlightedSubjectCount = $visibleSubjects
            ->filter(function ($subject) use ($timetableSlots, $assignedSubjectIds, $teacher) {
                return $timetableSlots->contains(function ($slot) use ($subject, $assignedSubjectIds, $teacher) {
                    if ((int) $slot->subject_id !== (int) $subject->id) {
                        return false;
                    }

                    if ((int) $slot->teacher_id === (int) $teacher->id) {
                        return true;
                    }

                    return empty($slot->teacher_id) && in_array((int) $slot->subject_id, $assignedSubjectIds, true);
                });
            })
            ->count();
        $highlightedSlotCount = $timetableSlots
            ->filter(fn ($slot) => (int) $slot->teacher_id === (int) $teacher->id
                || (empty($slot->teacher_id) && in_array((int) $slot->subject_id, $assignedSubjectIds, true)))
            ->count();
        $teacherFallbackMap = collect($assignedSubjectIds)
            ->mapWithKeys(fn ($subjectId) => [(int) $subjectId => $user?->name ?? __('Teacher')])
            ->all();
        
        return [
            'timetableByDay' => $timetableByDay,
            'subjects' => $subjects,
            'semesters' => $semesters,
            'sections' => $sections,
            'selectedSemester' => $selectedSemester,
            'selectedSection' => $selectedSection,
            'totalSlots' => $totalSlots,
            'totalSubjects' => $totalSubjects,
            'activeDays' => $activeDays,
            'days' => $days,
            'teacher' => $teacher,
            'college' => $college,
            'timeRows' => $timeRows,
            'slotMatrix' => $slotMatrix,
            'gapOverrideMatrix' => $gapOverrideMatrix,
            'highlightSubjectIds' => $assignedSubjectIds,
            'highlightTeacherId' => (int) $teacher->id,
            'highlightedSubjectCount' => $highlightedSubjectCount,
            'highlightedSlotCount' => $highlightedSlotCount,
            'teacherFallbackMap' => $teacherFallbackMap,
        ];
    }
}
