<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class TimetableSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::query()
            ->with(['teacherAssignments.teacher.user'])
            ->where('status', 'active')
            ->whereNotNull('semester')
            ->orderByRaw('CAST(semester AS UNSIGNED)')
            ->orderBy('subject_name')
            ->get();

        if ($subjects->isEmpty()) {
            $this->command?->warn('TimetableSlotSeeder skipped: no active subjects found.');
            return;
        }

        $this->removePreviouslySeededSlots();
        $subjectAssignmentMap = $this->buildSubjectAssignmentMap($subjects);

        if (empty($subjectAssignmentMap)) {
            $this->command?->warn('TimetableSlotSeeder skipped: no valid subject-teacher assignments found.');
            return;
        }

        $workingDays = collect(TimetableSlot::getDaysOfWeek())
            ->reject(fn ($day) => $day === 'saturday')
            ->values()
            ->all();

        $timeWindows = collect(TimetableSlot::getTimeSlots())
            ->reject(fn ($slot) => (bool) ($slot['break'] ?? false))
            ->map(fn ($slot) => [
                'key' => $slot['start'] . '-' . $slot['end'],
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
            ])
            ->values()
            ->all();

        $slotCells = $this->buildSlotCells($workingDays, $timeWindows);
        $occupiedSlots = [];
        $teacherBusySlots = [];
        $subjectUsedDays = [];
        $createdCount = 0;

        foreach ($subjects->groupBy(fn ($subject) => (string) $subject->semester) as $semester => $semesterSubjects) {
            $section = 'A';

            foreach ($semesterSubjects->values() as $subjectIndex => $subject) {
                $teacherId = $subjectAssignmentMap[$subject->id] ?? null;

                if (!$teacherId) {
                    $this->command?->warn("TimetableSlotSeeder skipped {$subject->subject_code}: no assigned teacher found.");
                    continue;
                }

                $sessionDefinitions = $this->buildSessionDefinitions($subject);

                foreach ($sessionDefinitions as $sessionIndex => $session) {
                    $placement = $this->findPlacement(
                        $slotCells,
                        (string) $semester,
                        $section,
                        $teacherId,
                        $subjectUsedDays[$subject->id] ?? [],
                        $occupiedSlots,
                        $teacherBusySlots,
                        ($subjectIndex * 3) + ($sessionIndex * 5)
                    );

                    if (!$placement) {
                        $sessionNumber = $sessionIndex + 1;
                        $this->command?->warn("TimetableSlotSeeder could not place {$subject->subject_code} {$session['slot_type']} session {$sessionNumber}.");
                        continue;
                    }

                    TimetableSlot::updateOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'semester' => (string) $semester,
                            'section' => $section,
                            'day_of_week' => $placement['day'],
                            'start_time' => $placement['start_time'],
                            'end_time' => $placement['end_time'],
                            'lab_group' => $session['lab_group'],
                        ],
                        [
                            'teacher_id' => $teacherId,
                            'academic_year' => $this->buildAcademicYear(),
                            'room' => $session['room'],
                            'slot_type' => $session['slot_type'],
                            'group_type' => $session['group_type'],
                            'is_active' => true,
                            'is_locked' => false,
                            'is_holiday' => false,
                            'max_capacity' => 60,
                            'remarks' => $session['remarks'],
                        ]
                    );

                    $occupiedSlots[(string) $semester][$section][$placement['day']][$placement['key']] = true;
                    $teacherBusySlots[$teacherId][$placement['day']][$placement['key']] = true;
                    $subjectUsedDays[$subject->id][$placement['day']] = true;
                    $createdCount++;
                }
            }
        }

        $this->command?->info("TimetableSlotSeeder completed. Created or updated {$createdCount} valid slots.");
    }

    /**
     * Build a clean subject-to-teacher map from valid assignments only.
     */
    private function buildSubjectAssignmentMap(Collection $subjects): array
    {
        return $subjects
            ->mapWithKeys(function (Subject $subject) {
                $teacherId = $this->resolveTeacherIdForSubject($subject);

                return $teacherId ? [$subject->id => $teacherId] : [];
            })
            ->all();
    }

    /**
     * Remove synthetic timetable rows generated by the old and current seeders.
     */
    private function removePreviouslySeededSlots(): void
    {
        TimetableSlot::withTrashed()
            ->where(function ($query) {
                $query->where('remarks', 'like', 'Regular % class')
                    ->orWhere('remarks', 'like', 'Seeded routine:%');
            })
            ->forceDelete();
    }

    /**
     * Resolve the primary teacher for a subject.
     */
    private function resolveTeacherIdForSubject(Subject $subject): ?int
    {
        $assignedTeacherId = $subject->teacherAssignments
            ->sortBy(fn ($assignment) => $assignment->role === 'primary' ? 0 : 1)
            ->pluck('teacher_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->first();

        if ($assignedTeacherId) {
            return $assignedTeacherId;
        }

        if (!Schema::hasColumn('subjects', 'teacher_id')) {
            return null;
        }

        $legacyTeacherKey = (int) data_get($subject, 'teacher_id');

        if ($legacyTeacherKey <= 0) {
            return null;
        }

        return Teacher::query()
            ->where('user_id', $legacyTeacherKey)
            ->value('id');
    }

    /**
     * Build deterministic weekly sessions for a subject.
     */
    private function buildSessionDefinitions(Subject $subject): array
    {
        $sessionDefinitions = [];
        $theorySlotType = $subject->subject_type === 'elective' ? 'elective' : 'theory';
        $theoryCount = max(1, (int) ($subject->lecture_hours ?: 1));
        $practicalCount = $subject->has_lab || (int) ($subject->practical_hours ?? 0) > 0
            ? max(1, (int) ceil(((int) ($subject->practical_hours ?? 0)) / 2))
            : 0;

        for ($index = 0; $index < $theoryCount; $index++) {
            $sessionDefinitions[] = [
                'slot_type' => $theorySlotType,
                'lab_group' => null,
                'group_type' => 'shared',
                'room' => $this->pickTheoryRoom($subject, $index),
                'remarks' => "Seeded routine: {$subject->subject_code} lecture " . ($index + 1),
            ];
        }

        for ($index = 0; $index < $practicalCount; $index++) {
            $sessionDefinitions[] = [
                'slot_type' => 'practical',
                'lab_group' => 'A',
                'group_type' => 'group_a',
                'room' => $this->pickLabRoom($subject, $index),
                'remarks' => "Seeded routine: {$subject->subject_code} lab " . ($index + 1),
            ];
        }

        return $sessionDefinitions;
    }

    /**
     * Expand day and time windows into addressable timetable cells.
     */
    private function buildSlotCells(array $workingDays, array $timeWindows): array
    {
        $cells = [];

        foreach ($workingDays as $day) {
            foreach ($timeWindows as $window) {
                $cells[] = [
                    'day' => $day,
                    'key' => $window['key'],
                    'start_time' => $window['start_time'],
                    'end_time' => $window['end_time'],
                ];
            }
        }

        return $cells;
    }

    /**
     * Find the next free timetable cell for the subject and teacher.
     */
    private function findPlacement(
        array $slotCells,
        string $semester,
        string $section,
        int $teacherId,
        array $usedDays,
        array $occupiedSlots,
        array $teacherBusySlots,
        int $preferredOffset
    ): ?array {
        if (empty($slotCells)) {
            return null;
        }

        $totalCells = count($slotCells);
        $passes = [
            fn (array $cell) => !in_array($cell['day'], array_keys($usedDays), true),
            fn (array $cell) => true,
        ];

        foreach ($passes as $allowCell) {
            for ($offset = 0; $offset < $totalCells; $offset++) {
                $cell = $slotCells[($preferredOffset + $offset) % $totalCells];

                if (!$allowCell($cell)) {
                    continue;
                }

                if (($occupiedSlots[$semester][$section][$cell['day']][$cell['key']] ?? false) === true) {
                    continue;
                }

                if (($teacherBusySlots[$teacherId][$cell['day']][$cell['key']] ?? false) === true) {
                    continue;
                }

                return $cell;
            }
        }

        return null;
    }

    /**
     * Pick a deterministic lecture room.
     */
    private function pickTheoryRoom(Subject $subject, int $index): string
    {
        $rooms = ['A101', 'A102', 'A103', 'B101', 'B102', 'C201'];

        return $rooms[($subject->id + $index) % count($rooms)];
    }

    /**
     * Pick a deterministic lab room.
     */
    private function pickLabRoom(Subject $subject, int $index): string
    {
        $labRooms = ['Lab01', 'Lab02', 'Lab03'];

        return $labRooms[($subject->id + $index) % count($labRooms)];
    }

    /**
     * Build the academic-year label used in seeded slots.
     */
    private function buildAcademicYear(): string
    {
        $startYear = now()->year;

        return $startYear . '-' . ($startYear + 1);
    }
}
