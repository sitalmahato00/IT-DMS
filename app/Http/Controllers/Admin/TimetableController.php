<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\TimetableSlot;
use App\Models\TimetableGapOverride;
use App\Models\Subject;
use App\Models\Teacher;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class TimetableController extends Controller
{
    use LogsActivity;

    /**
     * Display the enhanced timetable management view.
     */
    public function index(Request $request)
    {
        $semesterOptions = TimetableSlot::query()
            ->whereNotNull('semester')
            ->select('semester')
            ->distinct()
            ->orderByRaw('CAST(semester AS UNSIGNED)')
            ->pluck('semester')
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values();

        if ($semesterOptions->isEmpty()) {
            $semesterOptions = Subject::query()
                ->where('status', 'active')
                ->whereNotNull('semester')
                ->select('semester')
                ->distinct()
                ->orderByRaw('CAST(semester AS UNSIGNED)')
                ->pluck('semester')
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->values();
        }

        if ($semesterOptions->isEmpty()) {
            $semesterOptions = collect(['1', '2', '3', '4', '5', '6']);
        }

        // Get filter parameters
        $semester = trim((string) $request->input('semester', ''));
        if ($semester === '' || !$semesterOptions->contains($semester)) {
            $semester = (string) $semesterOptions->first();
        }
        $section = $request->input('section', '');
        $day = $request->input('day', '');
        
        // Build query
        $query = TimetableSlot::with(['subject', 'teacher.user'])
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($section) {
            $query->where('section', $section);
        }

        if ($day) {
            $query->where('day_of_week', $day);
        }

        $slots = $query->get();

        // Group slots by day and time for grid display
        $slotsByDay = $slots->groupBy('day_of_week');
        
        // Get all teacher conflicts
        $conflicts = $this->detectAllConflicts($slots);

        // Available subjects for this semester
        $subjectsQuery = Subject::with([
                'teacherAssignments.teacher.user',
                'labTechnician.user',
            ])
            ->where('status', 'active')
            ->orderBy('subject_name');
        
        if ($semester) {
            $semesterSubjects = Subject::with([
                    'teacherAssignments.teacher.user',
                    'labTechnician.user',
                ])
                ->where('semester', $semester)
                ->where('status', 'active')
                ->orderBy('subject_name')
                ->get();
            $subjects = $semesterSubjects->isNotEmpty() ? $semesterSubjects : $subjectsQuery->get();
        } else {
            $subjects = $subjectsQuery->get();
        }

        // Available teachers
        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->get();

        $semesters = $semesterOptions;

        // Days
        $days = TimetableSlot::getDaysOfWeek();

        // Get all unique time ranges from slots
        $timeSlots = $this->extractTimeSlots($slots);
        $timeRows = $this->buildTimeRows($slots);
        $slotMatrix = [];

        foreach ($days as $dayName) {
            $daySlots = $slotsByDay[$dayName] ?? collect();

            foreach ($timeRows as $timeRow) {
                if ($timeRow['is_break']) {
                    $slotMatrix[$dayName][$timeRow['key']] = collect();
                    continue;
                }

                $slotMatrix[$dayName][$timeRow['key']] = $daySlots
                    ->filter(
                        fn($slot) => $slot->start_time === $timeRow['start']
                            && $slot->end_time <= $timeRow['end']
                    )
                    ->sortBy('end_time')
                    ->values();
            }
        }

        $gapOverrideMatrix = $this->buildGapOverrideMatrix($semester, $section);

        // Sections
        $sections = TimetableSlot::getSections();

        // Lab groups
        $labGroups = TimetableSlot::getLabGroups();

        // Time slots for grid
        $gridTimeSlots = TimetableSlot::getTimeSlots();

        // Department info for routine sheet header
        $college = Department::first();

        // Stats
        $stats = [
            'total_slots'    => TimetableSlot::where('semester', $semester)->count(),
            'theory_slots'   => TimetableSlot::where('semester', $semester)->where('slot_type', 'theory')->count(),
            'practical_slots'=> TimetableSlot::where('semester', $semester)->where('slot_type', 'practical')->count(),
            'elective_slots' => TimetableSlot::where('semester', $semester)->where('slot_type', 'elective')->count(),
            'conflicts'      => $conflicts->count(),
        ];

        return view('admin.timetable', compact(
            'slots', 
            'slotsByDay', 
            'conflicts',
            'timeSlots',
            'days', 
            'subjects', 
            'teachers', 
            'semester', 
            'semesters',
            'section',
            'sections',
            'day',
            'labGroups',
            'stats',
            'timeRows',
            'slotMatrix',
            'college',
            'gapOverrideMatrix'
        ));
    }

    /**
     * Persist a removed break as an empty slot override.
     */
    public function storeGapOverride(Request $request)
    {
        if (!Schema::hasTable('timetable_gap_overrides')) {
            return response()->json([
                'success' => false,
                'message' => 'Gap override storage is not available yet. Run migrations first.',
            ], 500);
        }

        $validated = $request->validate([
            'semester' => 'required|string|max:20',
            'section' => 'nullable|string|max:50',
            'day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $section = trim((string) ($validated['section'] ?? ''));

        TimetableGapOverride::updateOrCreate(
            [
                'semester' => $validated['semester'],
                'section' => $section,
                'day_of_week' => $validated['day_of_week'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ],
            []
        );

        $this->logActivity(
            'Timetable',
            "Converted break to empty slot: {$validated['semester']} {$section} {$validated['day_of_week']} {$validated['start_time']}-{$validated['end_time']}"
        );

        return response()->json(['success' => true]);
    }

    /**
     * Store a new timetable slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'semester'     => 'required|string',
            'section'      => 'nullable|string|max:50',
            'academic_year' => 'nullable|string',
            'day_of_week'  => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'room'         => 'nullable|string|max:100',
            'slot_type'    => 'required|in:theory,practical,tutorial,elective',
            'lab_group'    => 'nullable|string|max:10',
            'group_type'   => 'nullable|string|max:20',
            'is_locked'    => 'nullable|boolean',
            'max_capacity' => 'nullable|integer',
            'remarks'      => 'nullable|string',
        ]);

        // Check for teacher conflicts before saving
        $conflictCheck = $this->checkConflict($validated);
        if ($conflictCheck['has_conflict'] && !$request->input('force_save', false)) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $conflictCheck['conflict'],
                'message' => $conflictCheck['conflict']['message']
            ], 422);
        }

        // Check for room conflicts
        $roomConflict = $this->checkRoomConflict($validated);
        if ($roomConflict['has_conflict'] && !$request->input('force_save', false)) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $roomConflict['conflict'],
                'message' => $roomConflict['conflict']['message'],
                'type' => 'room'
            ], 422);
        }

        // Check: Cannot add practical/lab when theory exists at same time
        // Can only add lab if no theory at that time
        $theoryCheck = $this->checkTheoryPracticalConflict($validated);
        if ($theoryCheck['has_conflict']) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $theoryCheck['conflict'],
                'message' => $theoryCheck['conflict']['message'],
                'type' => 'theory_practical'
            ], 422);
        }

        try {
            $validated['is_locked'] = $request->input('is_locked', false);
            $slot = TimetableSlot::create($validated);
            $this->logActivity('Timetable', "Added timetable slot: {$slot->day_of_week} {$slot->start_time}");

            return response()->json(['success' => true, 'slot' => $slot->load('subject', 'teacher.user')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating slot: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get a timetable slot.
     */
    public function show(int $id)
    {
        $slot = TimetableSlot::with(['subject', 'teacher.user'])->findOrFail($id);
        
        // Include conflicts in response
        $conflicts = $slot->findTeacherConflicts();
        
        return response()->json([
            'slot' => $slot,
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Update a timetable slot.
     */
    public function update(Request $request, int $id)
    {
        $slot = TimetableSlot::findOrFail($id);

        if ($slot->is_locked) {
            return response()->json(['success' => false, 'message' => 'Cannot edit a locked slot'], 403);
        }

        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
            'semester'     => 'required|string',
            'section'      => 'nullable|string|max:50',
            'academic_year' => 'nullable|string',
            'day_of_week'  => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'room'         => 'nullable|string|max:100',
            'slot_type'    => 'required|in:theory,practical,tutorial,elective',
            'lab_group'    => 'nullable|string|max:10',
            'group_type'   => 'nullable|string|max:20',
            'is_locked'    => 'nullable|boolean',
            'max_capacity' => 'nullable|integer',
            'remarks'      => 'nullable|string',
        ]);

        // Check for teacher conflicts before updating
        $conflictCheck = $this->checkConflict($validated, $id);
        if ($conflictCheck['has_conflict'] && !$request->input('force_save', false)) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $conflictCheck['conflict'],
                'message' => $conflictCheck['conflict']['message']
            ], 422);
        }

        // Check for room conflicts
        $roomConflict = $this->checkRoomConflict($validated, $id);
        if ($roomConflict['has_conflict'] && !$request->input('force_save', false)) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $roomConflict['conflict'],
                'message' => $roomConflict['conflict']['message'],
                'type' => 'room'
            ], 422);
        }

        // Check: Cannot add practical/lab when theory exists at same time
        $theoryCheck = $this->checkTheoryPracticalConflict($validated, $id);
        if ($theoryCheck['has_conflict']) {
            return response()->json([
                'success' => false,
                'has_conflict' => true,
                'conflict' => $theoryCheck['conflict'],
                'message' => $theoryCheck['conflict']['message'],
                'type' => 'theory_practical'
            ], 422);
        }

        try {
            $validated['is_locked'] = $request->input('is_locked', $slot->is_locked);
            $slot->update($validated);
            $this->logActivity('Timetable', "Updated timetable slot: ID {$id}");

            return response()->json(['success' => true, 'slot' => $slot->fresh()->load('subject', 'teacher.user')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating slot: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a timetable slot.
     */
    public function destroy(int $id)
    {
        $slot = TimetableSlot::findOrFail($id);

        if ($slot->is_locked) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a locked slot'], 403);
        }

        $slot->delete();
        $this->logActivity('Timetable', "Deleted timetable slot: ID {$id}");

        return response()->json(['success' => true, 'message' => 'Slot deleted.']);
    }

    /**
     * Toggle slot active status.
     */
    public function toggle(int $id)
    {
        $slot = TimetableSlot::findOrFail($id);
        $slot->update(['is_active' => !$slot->is_active]);

        return response()->json(['success' => true, 'is_active' => $slot->is_active]);
    }

    /**
     * Lock/unlock a timetable slot.
     */
    public function lock(int $id)
    {
        $slot = TimetableSlot::findOrFail($id);
        $newStatus = !$slot->is_locked;
        
        $slot->update([
            'is_locked' => $newStatus,
            'locked_at' => $newStatus ? now() : null
        ]);

        $this->logActivity('Timetable', ($newStatus ? 'Locked' : 'Unlocked') . " timetable slot: ID {$id}");

        return response()->json(['success' => true, 'is_locked' => $slot->is_locked]);
    }

    /**
     * Get timetable by semester (AJAX).
     */
    public function getBySemester(Request $request)
    {
        $semester = $request->input('semester', '1');
        $slots = TimetableSlot::with(['subject', 'teacher.user'])
            ->where('semester', $semester)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($slots);
    }

    /**
     * Get all conflicts for the timetable.
     */
    public function getConflicts(Request $request)
    {
        $semester = $request->input('semester', '1');
        
        $slots = TimetableSlot::with(['subject', 'teacher.user', 'teacher'])
            ->where('semester', $semester)
            ->where('is_active', true)
            ->get();

        $conflicts = $this->detectAllConflicts($slots);

        return response()->json([
            'conflicts' => $conflicts,
            'total' => $conflicts->count()
        ]);
    }

    /**
     * Print timetable for a semester.
     */
    public function printTimetable(Request $request, string $semester)
    {
        $section = $request->input('section', '');
        
        $query = TimetableSlot::with(['subject', 'teacher.user'])
            ->where('semester', $semester)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($section) {
            $query->where('section', $section);
        }

        $slots = $query->get();
        $slotsByDay = $slots->groupBy('day_of_week');
        $days = TimetableSlot::getDaysOfWeek();
        $timeRows = $this->buildTimeRows($slots);
        $slotMatrix = [];

        foreach ($days as $dayName) {
            $daySlots = $slotsByDay[$dayName] ?? collect();

            foreach ($timeRows as $timeRow) {
                if ($timeRow['is_break']) {
                    $slotMatrix[$dayName][$timeRow['key']] = collect();
                    continue;
                }

                $slotMatrix[$dayName][$timeRow['key']] = $daySlots
                    ->filter(
                        fn($slot) => $slot->start_time === $timeRow['start']
                            && $slot->end_time <= $timeRow['end']
                    )
                    ->sortBy('end_time')
                    ->values();
            }
        }

        $gapOverrideMatrix = $this->buildGapOverrideMatrix($semester, $section);

        // Get college info
        $college = Department::first();

        return view('admin.print.timetable', compact('slots', 'slotsByDay', 'days', 'semester', 'section', 'college', 'timeRows', 'slotMatrix', 'gapOverrideMatrix'));
    }

    /**
     * Export timetable as PDF.
     */
    public function exportPdf(Request $request)
    {
        $semester = $request->input('semester', '1');
        $section = $request->input('section', '');
        
        // For now, redirect to print view
        // In production, use a PDF library like DomPDF
        return redirect()->route('admin.timetable.print', ['semester' => $semester, 'section' => $section]);
    }

    /**
     * Export timetable as Excel.
     */
    public function exportExcel(Request $request)
    {
        $semester = $request->input('semester', '1');
        $section = $request->input('section', '');
        
        $query = TimetableSlot::with(['subject', 'teacher.user'])
            ->where('semester', $semester)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($section) {
            $query->where('section', $section);
        }

        $slots = $query->get();

        // Generate CSV data
        $data = [];
        $data[] = ['Day', 'Time', 'Subject', 'Teacher', 'Room', 'Type', 'Lab Group', 'Section'];

        foreach ($slots as $slot) {
            $data[] = [
                ucfirst($slot->day_of_week),
                $slot->time_range,
                $slot->subject->subject_name ?? 'N/A',
                $slot->teacher->user->name ?? 'N/A',
                $slot->room ?? 'N/A',
                ucfirst($slot->slot_type),
                $slot->lab_group ?? 'N/A',
                $slot->section ?? 'All',
            ];
        }

        $filename = "timetable_sem{$semester}" . ($section ? "_section{$section}" : "") . ".csv";

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Bulk lock timetable.
     */
    public function bulkLock(Request $request)
    {
        $validated = $request->validate([
            'semester' => 'required|string',
            'section' => 'nullable|string',
        ]);

        $query = TimetableSlot::where('semester', $validated['semester']);
        
        if (!empty($validated['section'])) {
            $query->where('section', $validated['section']);
        }

        $count = $query->update([
            'is_locked' => true,
            'locked_at' => now()
        ]);

        $this->logActivity('Timetable', "Bulk locked {$count} timetable slots");

        return response()->json(['success' => true, 'count' => $count]);
    }

    /**
     * Bulk unlock timetable.
     */
    public function bulkUnlock(Request $request)
    {
        $validated = $request->validate([
            'semester' => 'required|string',
            'section' => 'nullable|string',
        ]);

        $query = TimetableSlot::where('semester', $validated['semester']);
        
        if (!empty($validated['section'])) {
            $query->where('section', $validated['section']);
        }

        $count = $query->update([
            'is_locked' => false,
            'locked_at' => null
        ]);

        $this->logActivity('Timetable', "Bulk unlocked {$count} timetable slots");

        return response()->json(['success' => true, 'count' => $count]);
    }

    /**
     * Check for teacher conflicts.
     */
    private function checkConflict(array $data, ?int $excludeId = null): array
    {
        if (empty($data['teacher_id'])) {
            return ['has_conflict' => false];
        }

        $query = TimetableSlot::where('day_of_week', $data['day_of_week'])
            ->where('teacher_id', $data['teacher_id'])
            ->where('is_active', true)
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflicts = $query->with(['subject', 'teacher.user', 'teacher'])->get();

        if ($conflicts->isEmpty()) {
            return ['has_conflict' => false];
        }

        $conflict = $conflicts->first();
        $teacherName = $conflict->teacher->user->name ?? 'Unknown';
        $subjectName = $conflict->subject->subject_name ?? 'Unknown';
        
        return [
            'has_conflict' => true,
            'conflict' => [
                'type' => 'teacher',
                'message' => "Teacher {$teacherName} is already assigned to {$subjectName} (Semester {$conflict->semester}) at {$conflict->time_range}",
                'teacher_id' => $data['teacher_id'],
                'teacher_name' => $teacherName,
                'existing_slot' => $conflict,
            ]
        ];
    }

    /**
     * Check for room conflicts.
     */
    private function checkRoomConflict(array $data, ?int $excludeId = null): array
    {
        if (empty($data['room'])) {
            return ['has_conflict' => false];
        }

        $query = TimetableSlot::where('day_of_week', $data['day_of_week'])
            ->where('room', $data['room'])
            ->where('is_active', true)
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflicts = $query->with(['subject', 'teacher.user'])->get();

        if ($conflicts->isEmpty()) {
            return ['has_conflict' => false];
        }

        $conflict = $conflicts->first();
        $subjectName = $conflict->subject->subject_name ?? 'Unknown';
        
        return [
            'has_conflict' => true,
            'conflict' => [
                'type' => 'room',
                'message' => "Room {$data['room']} is already booked for {$subjectName} at {$conflict->time_range}",
                'room' => $data['room'],
                'existing_slot' => $conflict,
            ]
        ];
    }

    /**
     * Check for theory/practical conflict.
     * Cannot add practical/lab when theory exists at same time.
     * Can only add lab if no theory at that time.
     * Can add practical for different lab group if no theory.
     */
    private function checkTheoryPracticalConflict(array $data, ?int $excludeId = null): array
    {
        // Only check if trying to add practical/lab
        if ($data['slot_type'] !== 'practical') {
            return ['has_conflict' => false];
        }

        // Check if there's a theory slot at the same time
        $query = TimetableSlot::where('day_of_week', $data['day_of_week'])
            ->where('slot_type', 'theory')
            ->where('is_active', true)
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $theorySlots = $query->with(['subject'])->get();

        if ($theorySlots->isNotEmpty()) {
            $theory = $theorySlots->first();
            $subjectName = $theory->subject->subject_name ?? 'Unknown';
            
            return [
                'has_conflict' => true,
                'conflict' => [
                    'type' => 'theory_practical',
                    'message' => "Cannot add Lab/Practical at this time. Theory class '{$subjectName}' is already scheduled at {$theory->time_range}. Remove the theory class first or choose a different time.",
                    'existing_slot' => $theory,
                ]
            ];
        }

        return ['has_conflict' => false];
    }

    /**
     * Detect all conflicts in a set of slots.
     */
    private function detectAllConflicts($slots): \Illuminate\Support\Collection
    {
        $conflicts = collect();
        $slotArray = $slots->values()->all();

        for ($i = 0; $i < count($slotArray); $i++) {
            for ($j = $i + 1; $j < count($slotArray); $j++) {
                $slot1 = $slotArray[$i];
                $slot2 = $slotArray[$j];

                // Check teacher conflict
                if ($slot1->teacher_id && $slot2->teacher_id && 
                    $slot1->teacher_id === $slot2->teacher_id &&
                    $slot1->day_of_week === $slot2->day_of_week &&
                    $slot1->start_time < $slot2->end_time && 
                    $slot1->end_time > $slot2->start_time) {
                    
                    $conflicts->push([
                        'type' => 'teacher',
                        'slot1_id' => $slot1->id,
                        'slot2_id' => $slot2->id,
                        'message' => "Teacher conflict: " . 
                            ($slot1->teacher->user->name ?? 'Unknown') . 
                            " is assigned to both {$slot1->subject->subject_name} and {$slot2->subject->subject_name}",
                        'slot1' => $slot1,
                        'slot2' => $slot2,
                    ]);
                }

                // Check room conflict
                if ($slot1->room && $slot2->room && 
                    $slot1->room === $slot2->room &&
                    $slot1->day_of_week === $slot2->day_of_week &&
                    $slot1->start_time < $slot2->end_time && 
                    $slot1->end_time > $slot2->start_time) {
                    
                    $conflicts->push([
                        'type' => 'room',
                        'slot1_id' => $slot1->id,
                        'slot2_id' => $slot2->id,
                        'message' => "Room conflict: {$slot1->room} is booked for both {$slot1->subject->subject_name} and {$slot2->subject->subject_name}",
                        'slot1' => $slot1,
                        'slot2' => $slot2,
                    ]);
                }
            }
        }

        return $conflicts;
    }

    /**
     * Extract unique time slots from slots collection.
     */
    private function extractTimeSlots($slots): array
    {
        $timeSlots = [];
        
        foreach ($slots as $slot) {
            $key = $slot->start_time . '-' . $slot->end_time;
            if (!isset($timeSlots[$key])) {
                $timeSlots[$key] = [
                    'start' => $slot->start_time,
                    'end' => $slot->end_time,
                    'label' => Carbon::parse($slot->start_time)->format('g:i A') . ' - ' . Carbon::parse($slot->end_time)->format('g:i A'),
                ];
            }
        }

        ksort($timeSlots);
        
        return array_values($timeSlots);
    }

    /**
     * Build printable time rows and insert break rows between gaps.
     */
    private function buildTimeRows(Collection $timetableSlots): Collection
    {
        $rows = $timetableSlots
            ->groupBy('start_time')
            ->map(function ($slotGroup, $startTime) {
                $resolvedEndTime = $slotGroup->max('end_time');

                return [
                    'key' => $startTime . '-' . $resolvedEndTime,
                    'start' => $startTime,
                    'end' => $resolvedEndTime,
                    'is_break' => false,
                ];
            })
            ->sortBy('start')
            ->values();

        if ($rows->isEmpty()) {
            return collect();
        }

        $rowsWithBreaks = collect();

        foreach ($rows as $index => $row) {
            $rowsWithBreaks->push($row);

            $nextRow = $rows->get($index + 1);

            if (!$nextRow || $row['end'] >= $nextRow['start']) {
                continue;
            }

            $rowsWithBreaks->push([
                'key' => 'break-' . $row['end'] . '-' . $nextRow['start'],
                'start' => $row['end'],
                'end' => $nextRow['start'],
                'is_break' => true,
            ]);
        }

        return $rowsWithBreaks->values();
    }

    /**
     * Build a lookup of break rows that should render as empty slots.
     */
    private function buildGapOverrideMatrix(?string $semester, ?string $section = ''): array
    {
        if (blank($semester) || !Schema::hasTable('timetable_gap_overrides')) {
            return [];
        }

        return TimetableGapOverride::query()
            ->where('semester', (string) $semester)
            ->whereIn('section', array_values(array_unique([
                (string) ($section ?? ''),
                '',
            ])))
            ->get()
            ->groupBy('day_of_week')
            ->map(function ($overrides) {
                return $overrides->mapWithKeys(function ($override) {
                    return ['break-' . $override->start_time . '-' . $override->end_time => true];
                })->all();
            })
            ->all();
    }
}
