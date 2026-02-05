<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Traits\LogsActivity;

class ExamController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::with(['subject', 'course']);

            // Apply filters
            if ($request->has('academic_year') && $request->academic_year) {
                $query->forYear($request->academic_year);
            }

            if ($request->has('semester') && $request->semester) {
                $query->forSemester($request->semester);
            }

            if ($request->has('subject_id') && $request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->has('subject_id') && $request->subject_id) {
                $query->forSubject($request->subject_id);
            }

            if ($request->has('exam_type') && $request->exam_type) {
                $query->byType($request->exam_type);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('exam_name', 'like', "%{$search}%")
                      ->orWhere('exam_name_ne', 'like', "%{$search}%");
                });
            }

            // Validate per_page - only allow safe values
            $allowedPerPage = [10, 25, 50];
            $perPage = (int) $request->query('per_page', 10);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
            }
            // normalize request param so pagination links use a sanitized value
            $request->merge(['per_page' => $perPage]);

            $exams = $query->ordered()->paginate($perPage)->appends($request->all());

            // Get filter data
            $academicYears = $this->getAcademicYears();
            $semesters = $this->getSemesters();
            $courses = Course::all();
            $subjects = Subject::all();

            $stats = $this->getStatistics();

            if ($request->ajax()) {
                $tableRows = view('admin.partials.exams_table_rows', compact('exams'))->render();
                $tableFooter = view('admin.partials.exams_table_footer', compact('exams'))->render();
                $statsHtml = view('admin.partials.exams_stats', compact('stats'))->render();

                return response()->json([
                    'success' => true,
                    'table_rows' => $tableRows,
                    'table_footer' => $tableFooter,
                    'stats' => $statsHtml,
                ]);
            }

            return view('admin.assessment', compact('exams', 'academicYears', 'semesters', 'courses', 'subjects', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load exams: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'exam_name' => 'required|string|max:255',
                'exam_name_ne' => 'nullable|string|max:255',
                'academic_year' => 'required|string',
                'semester' => 'required|string',
                'subject_id' => 'nullable|exists:subjects,id',
                
                'exam_type' => 'required|string|in:internal,final,midterm,practical,viva,assignment,assessment',
                'full_marks' => 'required|integer|min:0',
                'passing_marks' => 'required|integer|min:0',
'exam_date' => 'required|date',
                'exam_date_bs' => 'nullable|string',
                'status' => 'required|in:draft,published,archived',
                'description' => 'nullable|string',
                'description_ne' => 'nullable|string',
                'instructions' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $validated['created_by'] = auth()->id();

            $exam = Exam::create($validated);

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Created Exam', "Exam '{$exam->exam_name}' created for {$exam->semester} semester");

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Exam created successfully!',
                    'exam' => $exam,
                ], 201);
            }

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create exam: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'exam_name' => 'required|string|max:255',
                'exam_name_ne' => 'nullable|string|max:255',
                'academic_year' => 'required|string',
                'semester' => 'required|string',
                'subject_id' => 'nullable|exists:subjects,id',
                
                'exam_type' => 'required|string|in:internal,final,midterm,practical,viva,assignment,assessment',
                'full_marks' => 'required|integer|min:0',
                'passing_marks' => 'required|integer|min:0',
                'exam_date' => 'required|date',
                'exam_date_bs' => 'nullable|string',
                'status' => 'required|in:draft,published,archived',
                'description' => 'nullable|string',
                'description_ne' => 'nullable|string',
                'instructions' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $exam->update($validated);

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Updated Exam', "Exam '{$exam->exam_name}' was updated");

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        try {
            DB::beginTransaction();

            // Delete associated marks first
            ExamMark::where('exam_id', $exam->id)->delete();

            $examName = $exam->exam_name;
            $exam->delete();

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Deleted Exam', "Exam '{$examName}' was deleted");

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        try {
            $exam->load(['subject', 'course', 'marks.student']);
            
            // Get statistics
            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained');
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            return view('admin.exam-show', compact('exam', 'totalStudents', 'averageMarks', 'passRate'));
        } catch (\Exception $e) {
            Log::error('Error showing exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to load exam details: ' . $e->getMessage());
        }
    }

    /**
     * Get exam data for editing via AJAX.
     */
    public function getExamData(Exam $exam)
    {
        try {
            return response()->json([
                'success' => true,
                'exam' => [
                    'id' => $exam->id,
                    'exam_name' => $exam->exam_name,
                    'exam_name_ne' => $exam->exam_name_ne,
                    'academic_year' => $exam->academic_year,
                    'semester' => $exam->semester,
                    'subject_id' => $exam->subject_id,
                    'exam_type' => $exam->exam_type,
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                    'exam_date' => $exam->exam_date->format('Y-m-d'),
                    'status' => $exam->status,
                    'description' => $exam->description,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting exam data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exam data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle exam status.
     */
    public function toggleStatus(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,published,archived'
            ]);

            $exam->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Exam status updated successfully!',
                'new_status' => $exam->formatted_status
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling exam status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload marks for an exam (traditional form submission).
     */
    public function uploadMarks(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($request->marks as $markData) {
                $fullMarks = $exam->full_marks;
                $marksObtained = $markData['marks_obtained'];
                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

                $existing = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $markData['student_id'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                } else {
                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'student_id' => $markData['student_id'],
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            // Log activity
            $this->logActivity('Marks', 'Uploaded Marks', "Marks uploaded for exam '{$exam->exam_name}': Created {$createdCount}, Updated {$updatedCount}");

            return redirect()->back()
                ->with('success', "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload marks for an exam (AJAX).
     */
    public function uploadMarksAjax(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($request->marks as $markData) {
                $fullMarks = $exam->full_marks;
                $marksObtained = $markData['marks_obtained'];
                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

                $existing = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $markData['student_id'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                } else {
                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'student_id' => $markData['student_id'],
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            // Reload exam with marks
            $exam->load(['subject', 'course', 'marks.student.user']);

            // Get updated statistics
            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained');
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            // Get updated marks rows HTML
            $marksRowsHtml = view('admin.exam-show-partials.marks-rows', ['exam' => $exam])->render();

            return response()->json([
                'success' => true,
                'message' => "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}",
                'stats' => [
                    'total_students' => $totalStudents,
                    'average_marks' => round($averageMarks, 2),
                    'pass_rate' => $passRate,
                ],
                'marks_html' => $marksRowsHtml,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks (AJAX): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload marks: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get students for a specific exam.
     */
    public function getStudentsForExam(Request $request, Exam $exam)
    {
        try {
            // Validate - allow empty values for "All" selection
            $request->validate([
                'semester' => 'nullable|string',
                'batch' => 'nullable|string',
                'subject_id' => 'nullable|string',
            ]);

            $query = Student::with(['user', 'subjects']);

            // Filter by subject if selected (not "All")
            if ($request->subject_id && $request->subject_id !== '') {
                // Filter through the many-to-many relationship
                $query->whereHas('subjects', function ($q) use ($request) {
                    $q->where('subjects.id', $request->subject_id);
                });
            }

            // Filter by semester if selected (not "All")
            if ($request->semester && $request->semester !== '') {
                $query->where('semester', $request->semester);
            }

            // Filter by batch if selected (not "All")
            if ($request->batch && $request->batch !== '') {
                $query->where('batch_year', $request->batch);
            }

            $students = $query->orderBy('roll_no')->get();

            // Get existing marks for this exam
            $existingMarks = ExamMark::where('exam_id', $exam->id)
                ->pluck('marks_obtained', 'student_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'students' => $students,
                'existing_marks' => $existingMarks,
                'full_marks' => $exam->full_marks,
                'passing_marks' => $exam->passing_marks,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting students for exam: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects by semester for exam creation.
     */
    public function getSubjectsBySemester(Request $request)
    {
        try {
            $request->validate([
                'semester' => 'required|string',
                
            ]);

            $query = Subject::where('semester', $request->semester);

            if ($request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }

            $subjects = $query->orderBy('subject_name')->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate grade from percentage.
     */
    protected function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    /**
     * Get list of academic years.
     */
    protected function getAcademicYears(): array
    {
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[] = ($i - 1) . '-' . $i;
        }
        return $years;
    }

    /**
     * Get list of semesters.
     */
    protected function getSemesters(): array
    {
        return [
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'third' => 'Third Semester',
            'fourth' => 'Fourth Semester',
            'fifth' => 'Fifth Semester',
            'sixth' => 'Sixth Semester',
        ];
    }

    /**
     * Get exam statistics for dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'total_exams' => Exam::count(),
            'published_exams' => Exam::where('status', 'published')->count(),
            'draft_exams' => Exam::where('status', 'draft')->count(),
            'total_marks_entries' => ExamMark::count(),
        ];
    }

    /**
     * Get mark data for editing via AJAX.
     */
    public function getMarkData(ExamMark $mark)
    {
        try {
            $mark->load(['student.user', 'exam']);
            
            return response()->json([
                'success' => true,
                'mark' => [
                    'id' => $mark->id,
                    'student_id' => $mark->student_id,
                    'student_name' => $mark->student->user->name ?? 'N/A',
                    'roll_no' => $mark->student->roll_no ?? '-',
                    'exam_id' => $mark->exam_id,
                    'exam_name' => $mark->exam->localized_name ?? 'N/A',
                    'marks_obtained' => $mark->marks_obtained,
                    'full_marks' => $mark->full_marks,
                    'percentage' => $mark->percentage,
                    'grade' => $mark->grade,
                    'remarks' => $mark->remarks,
                    'passing_marks' => $mark->exam->passing_marks ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting mark data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load mark data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a single mark via AJAX.
     */
    public function updateMark(Request $request, ExamMark $mark)
    {
        try {
            $request->validate([
                'marks_obtained' => 'required|numeric|min:0',
                'remarks' => 'nullable|string|max:500',
            ]);

            $fullMarks = $mark->full_marks;
            $marksObtained = $request->marks_obtained;
            $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

            DB::beginTransaction();

            $mark->update([
                'marks_obtained' => $marksObtained,
                'percentage' => $percentage,
                'grade' => $this->calculateGrade($percentage),
                'graded_by' => Auth::id(),
                'graded_at' => now(),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            // Reload mark with relationships
            $mark->load(['student.user', 'exam']);

            // Determine new pass/fail status based on 40% threshold
            $isPassed = $percentage >= 40;
            $statusBadgeClass = $isPassed 
                ? 'bg-green-100 text-green-700' 
                : 'bg-red-100 text-red-700';
            $statusText = $isPassed 
                ? '<span class="text-green-600 text-xs"><i class="bi bi-check-circle"></i> Passed</span>' 
                : '<span class="text-red-600 text-xs"><i class="bi bi-x-circle"></i> Failed</span>';

            return response()->json([
                'success' => true,
                'message' => 'Mark updated successfully!',
                'mark' => [
                    'id' => $mark->id,
                    'marks_obtained' => $mark->marks_obtained,
                    'percentage' => $mark->percentage,
                    'grade' => $mark->grade,
                    'is_passed' => $isPassed,
                    'status_badge_class' => $statusBadgeClass,
                    'status_text' => $statusText,
                    'student_name' => $mark->student->user->name ?? 'N/A',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating mark: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mark: ' . $e->getMessage()
            ], 500);
        }
    }
}

