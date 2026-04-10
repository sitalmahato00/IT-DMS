<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\TimetableSlot;
use App\Traits\BuildsRoutineTimetable;
use Illuminate\Http\Request;

class StudentTimetableController extends Controller
{
    use BuildsRoutineTimetable;

    /**
     * Display the student's timetable
     */
    public function index(Request $request)
    {
        $context = $this->buildTimetableContext($request);

        if (isset($context['redirect'])) {
            return $context['redirect'];
        }

        return view('student.timetable.index', $context);
    }

    /**
     * Display a printable routine sheet for the student.
     */
    public function print(Request $request)
    {
        $context = $this->buildTimetableContext($request);

        if (isset($context['redirect'])) {
            return $context['redirect'];
        }

        return view('student.timetable.print', $context);
    }

    /**
     * Build the shared timetable context for screen and print views.
     */
    private function buildTimetableContext(Request $request): array
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return [
                'redirect' => redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.'),
            ];
        }

        $college = Department::first();
        $days = TimetableSlot::getDaysOfWeek();

        $semesterOptionsQuery = TimetableSlot::query()
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->whereNotNull('semester');

        if ($student->semester) {
            $semesterOptionsQuery->where('semester', $student->semester);
        }

        $availableSemesters = $semesterOptionsQuery
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        if (empty($availableSemesters) && $student->semester) {
            $availableSemesters = [(string) $student->semester];
        }

        $selectedSemester = trim((string) $request->get('semester', $student->semester ?: ''));

        if ($selectedSemester === '' && !empty($availableSemesters)) {
            $selectedSemester = (string) $availableSemesters[0];
        }

        if (!empty($availableSemesters) && !in_array($selectedSemester, $availableSemesters, true)) {
            $selectedSemester = (string) $availableSemesters[0];
        }

        $availableSections = TimetableSlot::query()
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->when($selectedSemester, fn ($query) => $query->where('semester', $selectedSemester))
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $selectedSection = trim((string) $request->get('section', ''));

        if ($selectedSection === '' && count($availableSections) === 1) {
            $selectedSection = (string) $availableSections[0];
        }

        if ($selectedSection !== '' && !in_array($selectedSection, $availableSections, true)) {
            $selectedSection = count($availableSections) === 1 ? (string) $availableSections[0] : '';
        }

        $query = TimetableSlot::query()
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->with(['subject', 'teacher.user']);

        if ($selectedSemester) {
            $query->where('semester', $selectedSemester);
        }

        if ($selectedSection) {
            $query->where('section', $selectedSection);
        }

        $timetableSlots = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $timetableByDay = $this->buildTimetableByDay($timetableSlots, $days);

        $subjects = $timetableSlots
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('subject_name')
            ->values()
            ->map(fn ($subject) => [
                'id' => $subject->id,
                'name' => $subject->subject_name,
                'code' => $subject->subject_code,
                'semester' => $subject->semester,
            ]);

        $timeRows = $this->buildRoutineTimeRows($timetableSlots);
        $slotMatrix = $this->buildRoutineSlotMatrix($days, $timetableByDay, $timeRows);
        $gapOverrideMatrix = $this->buildRoutineGapOverrideMatrix($selectedSemester ?: ($student->semester ?: ''), $selectedSection);

        // Stats
        $totalSlots = $timetableSlots->count();
        $totalSubjects = $subjects->count();
        $activeDays = collect($timetableByDay)->filter(fn ($slots) => $slots->count() > 0)->count();

        return [
            'timetableByDay' => $timetableByDay,
            'subjects' => $subjects,
            'semesters' => $availableSemesters,
            'sections' => $availableSections,
            'selectedSemester' => $selectedSemester,
            'selectedSection' => $selectedSection,
            'totalSlots' => $totalSlots,
            'totalSubjects' => $totalSubjects,
            'activeDays' => $activeDays,
            'days' => $days,
            'student' => $student,
            'college' => $college,
            'timeRows' => $timeRows,
            'slotMatrix' => $slotMatrix,
            'gapOverrideMatrix' => $gapOverrideMatrix,
            'displaySemester' => $selectedSemester ?: ($student->semester ?: ''),
            'displaySection' => $selectedSection,
        ];
    }
}

