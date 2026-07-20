<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;

class SubjectTeacherController extends Controller
{
    /**
     * Store a new subject-teacher assignment.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,id',
                'semester' => 'nullable|string|max:20',
                'role' => 'nullable|string|max:50|in:primary,assistant,guest',
                'notes' => 'nullable|string',
            ]);

            // Check if assignment already exists
            $exists = SubjectTeacher::where('subject_id', $validated['subject_id'])
                ->where('teacher_id', $validated['teacher_id'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This teacher is already assigned to this subject.'
                ], 422);
            }

            $assignment = SubjectTeacher::create([
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'semester' => $validated['semester'] ?? null,
                'role' => $validated['role'] ?? 'primary',
                'notes' => $validated['notes'] ?? null,
                'assigned_at' => now(),
            ]);

            // Log activity
            $subject = Subject::find($validated['subject_id']);
            $teacher = Teacher::with('user')->find($validated['teacher_id']);
            $teacherName = $teacher->user->name ?? 'Unknown';
            
            Log::info('Subject-Teacher Assignment Created', [
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'subject_name' => $subject->subject_name ?? 'Unknown',
                'teacher_name' => $teacherName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher assigned to subject successfully.',
                'assignment' => $assignment->load(['subject', 'teacher.user'])
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error assigning teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a subject-teacher assignment.
     */
    public function update(Request $request, $id)
    {
        try {
            $assignment = SubjectTeacher::findOrFail($id);

            $validated = $request->validate([
                'semester' => 'nullable|string|max:20',
                'role' => 'nullable|string|max:50|in:primary,assistant,guest',
                'notes' => 'nullable|string',
            ]);

            $assignment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully.',
                'assignment' => $assignment->fresh()->load(['subject', 'teacher.user'])
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a subject-teacher assignment.
     */
    public function destroy($id)
    {
        try {
            $assignment = SubjectTeacher::findOrFail($id);
            
            // Log before deletion
            $subject = $assignment->subject;
            $teacher = $assignment->teacher;
            $teacherName = $teacher->user->name ?? 'Unknown';
            
            Log::info('Subject-Teacher Assignment Deleted', [
                'subject_id' => $assignment->subject_id,
                'teacher_id' => $assignment->teacher_id,
                'subject_name' => $subject->subject_name ?? 'Unknown',
                'teacher_name' => $teacherName,
            ]);

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignments for a specific subject.
     */
    public function getBySubject($subjectId)
    {
        try {
            $assignments = SubjectTeacher::with(['teacher.user'])
                ->where('subject_id', $subjectId)
                ->get();

            return response()->json([
                'success' => true,
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher GetBySubject Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignments for a specific teacher.
     */
    public function getByTeacher($teacherId)
    {
        try {
            $assignments = SubjectTeacher::with(['subject'])
                ->where('teacher_id', $teacherId)
                ->get();

            return response()->json([
                'success' => true,
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher GetByTeacher Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teachers available for a specific subject (not already assigned).
     */
    public function getAvailableTeachers($subjectId)
    {
        try {
            $subject = Subject::findOrFail($subjectId);
            
            // Get teachers already assigned to this subject
            $assignedTeacherIds = SubjectTeacher::where('subject_id', $subjectId)
                ->pluck('teacher_id')
                ->toArray();

            // Get all teachers except those already assigned
            $teachers = Teacher::with('user')
                ->whereNotIn('id', $assignedTeacherIds)
                ->whereHas('user', function($query) {
                    $query->where('role', 'teacher');
                })
                ->get();

            return response()->json([
                'success' => true,
                'teachers' => $teachers,
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher GetAvailableTeachers Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available teachers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects available for a specific teacher (not already assigned).
     */
    public function getAvailableSubjects($teacherId)
    {
        try {
            $teacher = Teacher::findOrFail($teacherId);
            
            // Get subjects already assigned to this teacher
            $assignedSubjectIds = SubjectTeacher::where('teacher_id', $teacherId)
                ->pluck('subject_id')
                ->toArray();

            // Get all subjects except those already assigned
            $subjects = Subject::whereNotIn('id', $assignedSubjectIds)->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects,
                'teacher' => $teacher
            ]);
        } catch (\Exception $e) {
            Log::error('SubjectTeacher GetAvailableSubjects Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available subjects: ' . $e->getMessage()
            ], 500);
        }
    }
}


