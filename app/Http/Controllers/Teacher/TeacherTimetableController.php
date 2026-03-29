<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\TimetableSlot;
use App\Traits\BuildsRoutineTimetable;
use Illuminate\Http\Request;

class TeacherTimetableController extends Controller
{
    use BuildsRoutineTimetable;

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
        $teacher = auth()->user()?->teacher;
        $college = Department::first();

        if (!$teacher) {
            return [
                'timetableByDay' => [],
                'subjects' => collect(),
                'semesters' => [],
                'selectedSemester' => '',
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

        $semesters = TimetableSlot::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $selectedSemester = trim((string) $request->get('semester', ''));

        if ($selectedSemester !== '' && !in_array($selectedSemester, $semesters, true)) {
            $selectedSemester = '';
        }

        $timetableSlots = TimetableSlot::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->when($selectedSemester, fn ($query) => $query->where('semester', $selectedSemester))
            ->with(['subject', 'teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $subjects = $timetableSlots
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('subject_name')
            ->values()
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
        $gapOverrideMatrix = $this->buildRoutineGapOverrideMatrix($selectedSemester);

        // Stats
        $totalSlots = $timetableSlots->count();
        $totalSubjects = $subjects->count();
        $activeDays = collect($timetableByDay)->filter(fn ($slots) => $slots->count() > 0)->count();
        
        return [
            'timetableByDay' => $timetableByDay,
            'subjects' => $subjects,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'totalSlots' => $totalSlots,
            'totalSubjects' => $totalSubjects,
            'activeDays' => $activeDays,
            'days' => $days,
            'teacher' => $teacher,
            'college' => $college,
            'timeRows' => $timeRows,
            'slotMatrix' => $slotMatrix,
            'gapOverrideMatrix' => $gapOverrideMatrix,
        ];
    }
}
