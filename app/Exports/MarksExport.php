<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $students;
    protected $columnStructure;
    protected $category;

    public function __construct($students, $columnStructure, $category)
    {
        $this->students = $students;
        $this->columnStructure = $columnStructure;
        $this->category = $category;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->students;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = ['Roll No', 'Student Name', 'Semester', 'Attendance'];

        // Add subject columns
        foreach ($this->columnStructure->subjects as $subject) {
            if ($this->category === 'ctevt') {
                foreach ($this->columnStructure->components as $component) {
                    $headings[] = $subject->subject_name . ' - ' . $component . ' Full';
                    $headings[] = $subject->subject_name . ' - ' . $component . ' Pass';
                    $headings[] = $subject->subject_name . ' - ' . $component . ' Obtained';
                }
            } else {
                $headings[] = $subject->subject_name . ' - Full';
                $headings[] = $subject->subject_name . ' - Pass';
                $headings[] = $subject->subject_name . ' - Obtained';
            }
        }

        $headings[] = 'Total';
        $headings[] = 'Percentage';
        $headings[] = 'Result';

        return $headings;
    }

    /**
     * @param mixed $student
     * @return array
     */
    public function map($student): array
    {
        $row = [
            $student->roll_no,
            $student->user->name ?? 'N/A',
            $student->semester,
            $student->getAttendancePercentage() . '%',
        ];

        $subjectIds = $this->columnStructure->subjects->pluck('id')->toArray();

        // Add subject marks
        foreach ($this->columnStructure->subjects as $subject) {
            if ($this->category === 'ctevt') {
                foreach ($this->columnStructure->components as $component) {
                    $compMarks = $student->getComponentMarks($subject->id, $component);
                    $row[] = $compMarks->full;
                    $row[] = $compMarks->pass;
                    $row[] = $compMarks->obtained;
                }
            } else {
                $assessMarks = $student->getAssessmentMarks($subject->id);
                $row[] = $assessMarks->full;
                $row[] = $assessMarks->pass;
                $row[] = $assessMarks->obtained;
            }
        }

        // Add totals
        $totalMarks = $student->getTotalMarks($subjectIds);
        $totalFull = $student->getTotalFullMarks($subjectIds);
        $percentage = $totalFull > 0 ? round(($totalMarks / $totalFull) * 100, 1) : 0;
        $result = $percentage >= 40 ? 'PASS' : 'FAIL';

        $row[] = $totalMarks . ' / ' . $totalFull;
        $row[] = $percentage . '%';
        $row[] = $result;

        return $row;
    }
}
