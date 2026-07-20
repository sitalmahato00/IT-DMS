<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherExportController extends Controller
{
    /**
     * Export teacher data to CSV
     * Exports assigned subjects and students
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Get teacher's assigned subjects with students
        $subjectTeachers = DB::table('subject_teacher')
            ->where('teacher_id', $user->id)
            ->get();
        
        $data = [];
        
        // Header row
        $data[] = ['Subject Code', 'Subject Name', 'Semester', 'Course', 'Student Name', 'Student Email', 'Roll No'];
        
        foreach ($subjectTeachers as $st) {
            $subject = DB::table('subjects')->where('id', $st->subject_id)->first();
            $course = $subject ? DB::table('courses')->where('id', $subject->course_id)->first() : null;
            
            // Get students enrolled in this subject
            $students = DB::table('subject_students')
                ->where('subject_id', $st->subject_id)
                ->get();
            
            foreach ($students as $studentRecord) {
                $student = DB::table('users')
                    ->where('id', $studentRecord->student_id)
                    ->first();
                
                $studentDetail = $student ? DB::table('students')->where('user_id', $student->id)->first() : null;
                
                $data[] = [
                    $subject->code ?? '',
                    $subject->name ?? '',
                    $subject->semester ?? '',
                    $course->name ?? '',
                    $student->name ?? '',
                    $student->email ?? '',
                    $studentDetail->roll_no ?? '',
                ];
            }
            
            // If no students, still add subject row
            if ($students->isEmpty()) {
                $data[] = [
                    $subject->code ?? '',
                    $subject->name ?? '',
                    $subject->semester ?? '',
                    $course->name ?? '',
                    'No students enrolled',
                    '',
                    '',
                ];
            }
        }
        
        // If no subjects assigned
        if (empty($data)) {
            $data[] = ['No subjects assigned to this teacher'];
        }
        
        // Generate CSV
        $filename = 'teacher_export_' . date('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response()->make('', 200, $headers);
    }
}


