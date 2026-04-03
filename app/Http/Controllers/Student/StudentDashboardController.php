<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\NepaliContentHelper;
use App\Models\TimetableSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.profile.edit')->with('error', 'Student profile not found.');
        }

        $subjectStats = $student->subjects()
            ->with(['teacherAssignments.teacher.user'])
            ->orderBy('semester')
            ->get()
            ->map(function ($subject) use ($student) {
                $primaryTeacher = $subject->teacherAssignments()
                    ->where('role', 'primary')
                    ->first()
                    ->teacher?->user;

                $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment');
                $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt');
                $primaryMarks = ($assessmentMarks->full ?? 0) > 0 ? $assessmentMarks : (((isset($ctevtMarks->full) ? $ctevtMarks->full : 0) > 0) ? $ctevtMarks : null);
                $fullMarks = $primaryMarks && ($primaryMarks->full ?? 0) > 0 ? (float) $primaryMarks->full : 0;
                $obtainedMarks = $primaryMarks && ($primaryMarks->obtained ?? 0) > 0 ? (float) $primaryMarks->obtained : 0;
                $percentage = $fullMarks > 0 ? round(($obtainedMarks / $fullMarks) * 100, 2) : null;
                $status = $percentage === null ? 'pending' : (($primaryMarks->is_pass ?? false) ? 'pass' : 'fail');

                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'teacher' => $primaryTeacher?->name ?? 'TBA',
                    'attendance_percentage' => $student->getAttendancePercentage($subject->id),
                    'full_marks' => $fullMarks,
                    'obtained_marks' => $obtainedMarks,
                    'percentage' => $percentage,
                    'status' => $status,
                ];
            });

        $totalObtained = $subjectStats->sum('obtained_marks');
        $totalFull = $subjectStats->sum('full_marks');
        $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $cgpa = $overallPercentage > 0 ? round($overallPercentage / 25, 2) : 0;

        $todayAd = Carbon::now('Asia/Kathmandu')->toDateString();
        $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

        $todayAttendance = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->where(function ($query) use ($todayAd, $todayBs) {
                $query->whereDate('date', $todayAd)
                    ->orWhere('date_bs', $todayBs);
            })
            ->join('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select('attendance.*', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date', 'desc')
            ->orderBy('subjects.subject_name', 'asc')
            ->get()
            ->map(function ($record) use ($todayAd, $todayBs) {
                $recordDate = $record->date instanceof Carbon
                    ? $record->date
                    : ($record->date ? Carbon::parse($record->date) : Carbon::parse($todayAd));

                return [
                    'subject_name' => $record->subject_name ?? __('Subject'),
                    'subject_code' => $record->subject_code,
                    'status' => $record->status,
                    'remarks' => $record->remarks,
                    'date' => $recordDate,
                    'date_label' => $recordDate->format('M d, Y'),
                    'date_bs' => $record->date_bs ?? $todayBs,
                ];
            })
            ->values();

        $recentAttendance = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->join('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select('attendance.*', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date', 'desc')
            ->limit(5)
            ->get();

        $timetableDays = TimetableSlot::getDaysOfWeek();
        $enrolledSubjectIds = $subjectStats->pluck('id')->all();
        $timetableSlots = collect();

        if (!empty($enrolledSubjectIds)) {
            $timetableQuery = TimetableSlot::query()
                ->whereIn('subject_id', $enrolledSubjectIds)
                ->where('is_active', true)
                ->where('is_holiday', false)
                ->with(['subject', 'teacher.user']);

            if ($student->semester) {
                $timetableQuery->where('semester', $student->semester);
            }

            $timetableSlots = $timetableQuery
                ->get()
                ->sortBy(fn ($slot) => sprintf(
                    '%02d_%s',
                    array_search($slot->day_of_week, $timetableDays, true),
                    $slot->start_time
                ))
                ->values();
        }

        $timetableByDay = collect($timetableDays)
            ->mapWithKeys(fn ($day) => [$day => $timetableSlots->where('day_of_week', $day)->values()]);

        return view('student.studentdashboard', [
            'user' => $user,
            'student' => $student,
            'subjectStats' => $subjectStats,
            'overallAttendance' => $student->getAttendancePercentage(),
            'overallPercentage' => $overallPercentage,
            'cgpa' => $cgpa,
            'subjectCount' => $subjectStats->count(),
            'gradedSubjectCount' => $subjectStats->whereNotNull('percentage')->count(),
            'passedSubjects' => $subjectStats->where('status', 'pass')->count(),
            'failedSubjects' => $subjectStats->where('status', 'fail')->count(),
            'pendingSubjects' => $subjectStats->where('status', 'pending')->count(),
            'todayAttendance' => $todayAttendance,
            'recentAttendance' => $recentAttendance,
            'timetableByDay' => $timetableByDay,
            'timetableDays' => $timetableDays,
            'timetableTotalSlots' => $timetableSlots->count(),
            'timetableActiveDays' => $timetableByDay->filter(fn ($slots) => $slots->isNotEmpty())->count(),
        ]);
    }
}
