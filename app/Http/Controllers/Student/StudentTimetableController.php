<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimetableSlot;

class StudentTimetableController extends Controller
{
    /**
     * Display the student's timetable
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Get student's enrolled subject IDs
        $enrolledSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();

        if (empty($enrolledSubjectIds)) {
            return view('student.timetable.index', [
                'timetableByDay' => [],
                'subjects' => [],
                'semesters' => [],
                'sections' => [],
                'selectedSemester' => '',
                'selectedSection' => '',
                'totalSlots' => 0,
                'totalSubjects' => 0,
                'days' => TimetableSlot::getDaysOfWeek(),
                'student' => $student,
            ]);
        }

        // Get available semesters from enrolled subjects' timetable slots
        $availableSemesters = TimetableSlot::whereIn('subject_id', $enrolledSubjectIds)
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $selectedSemester = $request->get('semester', $student->semester ?: '');

        // Get available sections from enrolled subjects' timetable slots
        $availableSections = TimetableSlot::whereIn('subject_id', $enrolledSubjectIds)
            ->where('is_active', true)
            ->where('is_holiday', false)
            ->when($selectedSemester, fn($q) => $q->where('semester', $selectedSemester))
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $selectedSection = $request->get('section', '');

        // Build query for timetable slots
        $query = TimetableSlot::whereIn('subject_id', $enrolledSubjectIds)
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

        // Group by day
        $days = TimetableSlot::getDaysOfWeek();
        $timetableByDay = [];

        foreach ($days as $day) {
            $timetableByDay[$day] = $timetableSlots->filter(
                fn($slot) => $slot->day_of_week === $day
            )->values();
        }

        // Get enrolled subjects for display
        $subjects = $student->subjects()
            ->whereIn('subjects.id', $enrolledSubjectIds)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->subject_name,
                'code' => $s->subject_code,
                'semester' => $s->semester,
            ]);

        // Stats
        $totalSlots = $timetableSlots->count();
        $totalSubjects = count($enrolledSubjectIds);
        $activeDays = collect($timetableByDay)->filter(fn($slots) => $slots->count() > 0)->count();

        return view('student.timetable.index', [
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
        ]);
    }
}
