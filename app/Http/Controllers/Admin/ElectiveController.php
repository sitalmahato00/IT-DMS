<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ElectiveEnrollment;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;

class ElectiveController extends Controller
{
    use LogsActivity;

    /**
     * Display elective management dashboard.
     */
    public function index(Request $request)
    {
        // Get all elective/optional subjects
        $electiveSubjects = Subject::whereIn('subject_type', ['elective', 'optional'])
            ->where('status', 'active')
            ->with(['teacherAssignments.teacher.user'])
            ->orderBy('semester')
            ->orderBy('subject_name')
            ->get();

        // Enrich with enrollment stats
        $electiveSubjects = $electiveSubjects->map(function ($subject) {
            $subject->approved_count = ElectiveEnrollment::where('subject_id', $subject->id)
                ->where('status', 'approved')
                ->count();
            $subject->pending_count = ElectiveEnrollment::where('subject_id', $subject->id)
                ->where('status', 'pending')
                ->count();
            return $subject;
        });

        // Pending approvals
        $pendingEnrollments = ElectiveEnrollment::with(['student.user', 'subject'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Stats
        $stats = [
            'total_electives'    => $electiveSubjects->count(),
            'open_enrollment'    => $electiveSubjects->where('is_elective_open', true)->count(),
            'total_enrolled'     => ElectiveEnrollment::where('status', 'approved')->count(),
            'pending_approvals'  => ElectiveEnrollment::where('status', 'pending')->count(),
        ];

        // All active students for assignment
        $students = Student::with('user')
            ->where('status', 'active')
            ->where('is_alumni', false)
            ->orderBy('semester')
            ->get();

        // Semester filter
        $semesters = Subject::whereIn('subject_type', ['elective', 'optional'])
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();

        return view('admin.electives', compact(
            'electiveSubjects', 'pendingEnrollments', 'stats', 'students', 'semesters'
        ));
    }

    /**
     * Assign a student to an elective (admin direct assignment).
     */
    public function assign(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'subject_id'   => 'required|exists:subjects,id',
            'semester'     => 'required|string',
            'academic_year'=> 'nullable|string',
        ]);

        // Check max students limit
        $subject = Subject::findOrFail($request->subject_id);
        if ($subject->max_students) {
            $currentCount = ElectiveEnrollment::where('subject_id', $request->subject_id)
                ->where('status', 'approved')
                ->count();
            if ($currentCount >= $subject->max_students) {
                return response()->json([
                    'success' => false,
                    'message' => "This elective has reached its maximum capacity of {$subject->max_students} students."
                ], 422);
            }
        }

        $enrollment = ElectiveEnrollment::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
                'semester'   => $request->semester,
            ],
            [
                'academic_year' => $request->academic_year,
                'status'        => 'approved',
                'approved_by'   => auth()->id(),
                'approved_at'   => now(),
            ]
        );

        $this->logActivity('Elective', "Admin assigned student to elective: {$subject->subject_name}");

        return response()->json(['success' => true, 'enrollment' => $enrollment]);
    }

    /**
     * Approve a pending elective enrollment.
     */
    public function approve(int $id)
    {
        $enrollment = ElectiveEnrollment::findOrFail($id);

        // Check capacity
        $subject = Subject::find($enrollment->subject_id);
        if ($subject && $subject->max_students) {
            $currentApproved = ElectiveEnrollment::where('subject_id', $enrollment->subject_id)
                ->where('status', 'approved')
                ->where('id', '!=', $id)
                ->count();
            if ($currentApproved >= $subject->max_students) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot approve - elective is at full capacity ({$subject->max_students} students)."
                ], 422);
            }
        }

        $enrollment->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->logActivity('Elective', "Approved elective enrollment: ID {$id}");

        return response()->json(['success' => true, 'message' => 'Enrollment approved.']);
    }

    /**
     * Reject a pending elective enrollment.
     */
    public function reject(Request $request, int $id)
    {
        $enrollment = ElectiveEnrollment::findOrFail($id);
        $enrollment->update([
            'status'  => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        $this->logActivity('Elective', "Rejected elective enrollment: ID {$id}");

        return response()->json(['success' => true, 'message' => 'Enrollment rejected.']);
    }

    /**
     * Withdraw an elective enrollment.
     */
    public function withdraw(int $id)
    {
        $enrollment = ElectiveEnrollment::findOrFail($id);
        $enrollment->update(['status' => 'withdrawn']);

        $this->logActivity('Elective', "Withdrawn elective enrollment: ID {$id}");

        return response()->json(['success' => true, 'message' => 'Enrollment withdrawn.']);
    }

    /**
     * Toggle enrollment open/closed for an elective subject.
     */
    public function toggleEnrollment(Request $request)
    {
        $request->validate(['subject_id' => 'required|exists:subjects,id']);
        
        $subject = Subject::findOrFail($request->subject_id);
        $subject->update(['is_elective_open' => !$subject->is_elective_open]);

        $this->logActivity('Elective', "Toggled enrollment for: {$subject->subject_name} to " . ($subject->is_elective_open ? 'open' : 'closed'));

        return response()->json([
            'success'      => true,
            'is_open'      => $subject->is_elective_open,
            'message'      => "Enrollment " . ($subject->is_elective_open ? 'opened' : 'closed') . " for {$subject->subject_name}",
        ]);
    }

    /**
     * Get elective enrollments for a specific student.
     */
    public function studentElectives(int $studentId)
    {
        $enrollments = ElectiveEnrollment::with('subject')
            ->where('student_id', $studentId)
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Delete an elective enrollment.
     */
    public function destroy(int $id)
    {
        $enrollment = ElectiveEnrollment::findOrFail($id);
        $enrollment->delete();

        $this->logActivity('Elective', "Deleted elective enrollment: ID {$id}");

        return response()->json(['success' => true, 'message' => 'Enrollment removed.']);
    }
}

