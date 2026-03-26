<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::query()
            ->where('status', 'active')
            ->orderByRaw('CAST(semester AS UNSIGNED)')
            ->orderBy('subject_name')
            ->get();

        if ($subjects->isEmpty()) {
            return;
        }

        $teacherIds = Teacher::query()->orderBy('id')->pluck('id')->values();

        $assignedTeacherBySubject = SubjectTeacher::query()
            ->orderByRaw("CASE WHEN role = 'primary' THEN 0 ELSE 1 END")
            ->orderByDesc('assigned_at')
            ->get()
            ->groupBy('subject_id')
            ->map(fn (Collection $rows) => $rows->first()->teacher_id);

        $days = TimetableSlot::getDaysOfWeek();
        $teachingSlots = collect(TimetableSlot::getTimeSlots())
            ->filter(fn (array $slot) => empty($slot['break']))
            ->values()
            ->all();

        $queue = [];
        foreach ($teachingSlots as $slotIndex => $slot) {
            foreach ($days as $day) {
                $queue[] = [
                    'day' => $day,
                    'slot_index' => $slotIndex,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ];
            }
        }

        $semesterOccupancy = [];
        $semesterPointers = [];
        $fallbackTeacherIndex = 0;

        foreach ($subjects->groupBy(fn ($subject) => (string) $subject->semester) as $semester => $semesterSubjects) {
            $semesterPointers[$semester] = $semesterPointers[$semester] ?? 0;

            foreach ($semesterSubjects as $subject) {
                $teacherId = $assignedTeacherBySubject->get($subject->id);
                if (!$teacherId && $teacherIds->isNotEmpty()) {
                    $teacherId = $teacherIds[$fallbackTeacherIndex % $teacherIds->count()];
                    $fallbackTeacherIndex++;
                }

                foreach ($this->buildSessionsForSubject($subject) as $session) {
                    $placement = $this->findPlacement(
                        $queue,
                        $semester,
                        $semesterPointers,
                        $semesterOccupancy
                    );

                    if (!$placement) {
                        continue;
                    }

                    $slotKey = implode('|', [$placement['day'], $placement['slot_index']]);
                    $semesterOccupancy[$semester][$slotKey] = true;

                    TimetableSlot::updateOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'semester' => (string) $subject->semester,
                            'section' => 'A',
                            'day_of_week' => $placement['day'],
                            'start_time' => $placement['start_time'],
                            'end_time' => $placement['end_time'],
                            'slot_type' => $session['slot_type'],
                            'lab_group' => $session['lab_group'],
                        ],
                        [
                            'teacher_id' => $teacherId,
                            'academic_year' => '2081/082',
                            'room' => $session['room'],
                            'group_type' => $session['group_type'],
                            'is_active' => true,
                            'is_locked' => false,
                            'is_holiday' => false,
                            'max_capacity' => $subject->max_students ?: 48,
                            'remarks' => 'Auto-seeded timetable slot',
                        ]
                    );
                }
            }
        }
    }

    private function buildSessionsForSubject(Subject $subject): array
    {
        $semester = (string) ($subject->semester ?: '1');
        $theoryCount = max(1, (int) ($subject->lecture_hours ?: 1));
        $tutorialCount = max(0, (int) ($subject->tutorial_hours ?: 0));
        $practicalCount = max(0, (int) ($subject->practical_hours ?: 0));

        $sessions = [];

        $theoryType = $subject->subject_type === 'elective' ? 'elective' : 'theory';
        for ($i = 0; $i < $theoryCount; $i++) {
            $sessions[] = [
                'slot_type' => $theoryType,
                'room' => "SEM-{$semester}-R1",
                'group_type' => 'shared',
                'lab_group' => null,
            ];
        }

        for ($i = 0; $i < $tutorialCount; $i++) {
            $sessions[] = [
                'slot_type' => 'tutorial',
                'room' => "SEM-{$semester}-R2",
                'group_type' => 'shared',
                'lab_group' => null,
            ];
        }

        $practicalType = $subject->subject_type === 'elective' ? 'elective' : 'practical';
        for ($i = 0; $i < $practicalCount; $i++) {
            $sessions[] = [
                'slot_type' => $practicalType,
                'room' => "SEM-{$semester}-LAB",
                'group_type' => 'shared',
                'lab_group' => $subject->has_lab ? 'A' : null,
            ];
        }

        return $sessions;
    }

    private function findPlacement(
        array $queue,
        string $semester,
        array &$semesterPointers,
        array $semesterOccupancy
    ): ?array {
        $queueCount = count($queue);
        if ($queueCount === 0) {
            return null;
        }

        $startIndex = $semesterPointers[$semester] ?? 0;

        for ($attempt = 0; $attempt < $queueCount; $attempt++) {
            $index = ($startIndex + $attempt) % $queueCount;
            $candidate = $queue[$index];
            $slotKey = implode('|', [$candidate['day'], $candidate['slot_index']]);

            if (!empty($semesterOccupancy[$semester][$slotKey])) {
                continue;
            }

            $semesterPointers[$semester] = ($index + 1) % $queueCount;

            return $candidate;
        }

        return null;
    }
}
