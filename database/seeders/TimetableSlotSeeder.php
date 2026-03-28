<?php

namespace Database\Seeders;

use App\Models\TimetableSlot;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TimetableSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $subjects = Subject::all();
        $teachers = Teacher::all();

        $timeSlots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:15', 'end' => '11:15'],
            ['start' => '11:15', 'end' => '12:15'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
        ];

        $rooms = ['A101', 'A102', 'A103', 'B101', 'Lab01'];

        $slotIndex = 0;
        foreach ($daysOfWeek as $dayIndex => $day) {
            foreach ($timeSlots as $timeIndex => $timeSlot) {
                $subject = $subjects->get($slotIndex % $subjects->count());
                $teacher = $teachers->get($slotIndex % $teachers->count());
                $room = $rooms[$slotIndex % count($rooms)];

                if ($subject && $teacher) {
                    TimetableSlot::firstOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'day_of_week' => $day,
                            'start_time' => $timeSlot['start'],
                            'end_time' => $timeSlot['end'],
                        ],
                        [
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'semester' => 5,
                            'section' => 'A',
                            'academic_year' => '2080-2081',
                            'day_of_week' => $day,
                            'start_time' => $timeSlot['start'],
                            'end_time' => $timeSlot['end'],
                            'room' => $room,
                            'slot_type' => $slotIndex % 2 === 0 ? 'theory' : 'practical',
                            'is_active' => true,
                            'max_capacity' => 60,
                            'remarks' => 'Regular ' . $subject->subject_name . ' class',
                        ]
                    );
                }
                $slotIndex++;
            }
        }
    }
}
