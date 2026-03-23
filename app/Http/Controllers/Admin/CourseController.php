<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use App\Models\Course;
use App\Models\Exam;
use App\Models\SubjectTeacher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    use LogsActivity;

    private function subjectsHasColumn(string $column): bool
    {
        static $cache = [];
        if (!array_key_exists($column, $cache)) {
            $cache[$column] = Schema::hasColumn('subjects', $column);
        }
        return (bool) $cache[$column];
    }

    private function hasTable(string $table): bool
    {
        static $cache = [];
        if (!array_key_exists($table, $cache)) {
            $cache[$table] = Schema::hasTable($table);
        }
        return (bool) $cache[$table];
    }

    public function index(Request $request)
    {
        try {
            $search = $request->get("q", "");
            $status = $request->get("status", "");
            $category = $request->get("category", "");
            $semester = $request->get("semester", "");
            $teacher_id = $request->get("teacher_id", "");
            $subject_type = $request->get("subject_type", "");
            $perPage = intval($request->get('per_page', 25)) ?: 25;

            $query = \App\Models\Course::query()
                ->with(['teacher.user', 'labTechnician.user'])
                ->select('subjects.*')
                ->withCount(['students as students_count' => function ($q) {
                    $q->where('students.status', 'active')
                      ->where(function ($q) {
                          $q->where('students.is_alumni', 0)
                            ->orWhereNull('students.is_alumni');
                       });
                }]);

            // If `subjects.teacher_id` column doesn't exist, compute it from the `subject_teacher` pivot
            // so the existing `teacher()` relationship + table UI can still display assigned teachers.
            //
            // If the legacy column exists but is null, compute a `pivot_teacher_id` so we can
            // fall back to it after pagination and (re)load the relationship.
            if ($this->hasTable('subject_teacher')) {
                $teacherSubquery = "(select st.teacher_id
                      from subject_teacher st
                      where st.subject_id = subjects.id
                      order by CASE WHEN st.role = 'primary' THEN 0 ELSE 1 END, st.assigned_at desc, st.id desc
                      limit 1)";

                if (!$this->subjectsHasColumn('teacher_id')) {
                    $query->addSelect(DB::raw($teacherSubquery . " as teacher_id"));
                } else {
                    $query->addSelect(DB::raw($teacherSubquery . " as pivot_teacher_id"));
                }
            }

            // Apply filters
            $query->when($search, function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('subject_name', 'like', "%{$search}%")
                         ->orWhere('subject_code', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%");
                });
            })->when($status, fn($q, $status) => $q->where('status', $status))

              ->when($semester, fn($q, $semester) => $q->where('semester', $semester))
              ->when($teacher_id, function($q, $teacher_id) {
                  $teacherId = intval($teacher_id);
                  $q->where(function($sub) use ($teacherId) {
                      if ($this->subjectsHasColumn('teacher_id')) {
                          $sub->where('subjects.teacher_id', $teacherId);
                      }
                      $sub->orWhereExists(function($exists) use ($teacherId) {
                              $exists->select(DB::raw(1))
                                     ->from('subject_teacher')
                                     ->whereColumn('subject_teacher.subject_id', 'subjects.id')
                                     ->where('subject_teacher.teacher_id', $teacherId);
                          });
                  });
              })
              ->when($subject_type, fn($q, $subject_type) => $q->where('subject_type', $subject_type));

            $courses = $query->orderBy('subject_name')->paginate($perPage)->withQueryString();

            // If legacy `teacher_id` exists but isn't used, fall back to pivot-computed `pivot_teacher_id`
            // and ensure relations are available for Blade accessors.
            if ($this->subjectsHasColumn('teacher_id') && $this->hasTable('subject_teacher')) {
                $courses->getCollection()->transform(function ($course) {
                    if (empty($course->teacher_id) && !empty($course->pivot_teacher_id)) {
                        $course->teacher_id = $course->pivot_teacher_id;
                    }
                    return $course;
                });
                $courses->getCollection()->load(['teacher.user']);
            }

            // fallback student counts by subject from subject_students pivot (if relation count is 0)
            $subjectStudentCounts = DB::table('subject_students')
                ->join('students', 'subject_students.student_id', '=', 'students.id')
                ->where('students.status', 'active')
                ->where(function ($q) {
                    $q->where('students.is_alumni', 0)
                      ->orWhereNull('students.is_alumni');
                })
                ->select('subject_students.subject_id', DB::raw('COUNT(DISTINCT subject_students.student_id) as cnt'))
                ->groupBy('subject_students.subject_id')
                ->pluck('cnt', 'subject_id');

            $studentsBySemester = DB::table('students')
                ->where('students.status', 'active')
                ->where(function ($q) {
                    $q->where('students.is_alumni', 0)
                      ->orWhereNull('students.is_alumni');
                })
                ->select('semester', DB::raw('COUNT(*) as cnt'))
                ->groupBy('semester')
                ->pluck('cnt', 'semester');

            $courses->getCollection()->transform(function ($course) use ($subjectStudentCounts, $studentsBySemester) {
                $subjectCount = (int) ($subjectStudentCounts[$course->id] ?? 0);
                if ($course->students_count === null || $course->students_count == 0) {
                    $course->students_count = $subjectCount > 0 ? $subjectCount : (int) ($studentsBySemester[$course->semester] ?? 0);
                }
                return $course;
            });

            // Eloquent stats
            $stats = [
                "total" => \App\Models\Course::count(),
                "active" => \App\Models\Course::where('status', 'active')->count(),
                "archived" => \App\Models\Course::where('status', 'archived')->count(),
                "core" => \App\Models\Course::where('subject_type', 'core')->count(),
            ];

            $departments = collect(["Major", "Elective I", "Elective II", "Elective III", "Project"]);

            $allTeachersQuery = DB::table("teachers")
                ->leftJoin("users", "teachers.user_id", "=", "users.id")
                ->select("teachers.id", DB::raw("COALESCE(users.name, 'Unassigned') as name"))
                ->orderBy("name");

            $allTeachers = $allTeachersQuery->get();
            $labTechnicians = $allTeachers;

            $semesterCards = $this->buildSemesterCards($request);
            $selectedSemesterLabel = $this->getSelectedSemesterLabel($semester);
            $semesters = \App\Models\Semester::orderBy('number')->get();

            return view("admin.courses", compact(
                "courses",
                "stats",
                "departments",
                "allTeachers",
                "labTechnicians",
                "search",
                "status",
                "category",
                "semester",
                "teacher_id",
                "subject_type",
                "semesterCards",
                "selectedSemesterLabel",
                "semesters"
            ));

        } catch (\Exception $e) {
            \Log::error("Courses index error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view("admin.courses", [
                "courses" => collect([]),
                "stats" => ["total" => 0, "active" => 0, "archived" => 0, "core" => 0],
                "departments" => collect(["Major", "Elective I", "Elective II", "Elective III", "Project"]),
                "allTeachers" => collect([]),
                "labTechnicians" => collect([]),
                "search" => "",
                "status" => "",
                "category" => "",
                "semester" => "",
                "teacher_id" => "",
                "subject_type" => "",
                "semesterCards" => [],
                "selectedSemesterLabel" => "",
                "semesters" => collect([]),
            ]);
        }
    }

    private function buildSemesterCards(Request $request): array
    {
        $semesterRecords = \App\Models\Semester::orderBy('number')->get();

        if ($semesterRecords->isEmpty()) {
            $semesterRecords = collect([
                (object)['number' => 1, 'name' => 'First Semester'],
                (object)['number' => 2, 'name' => 'Second Semester'],
                (object)['number' => 3, 'name' => 'Third Semester'],
                (object)['number' => 4, 'name' => 'Fourth Semester'],
                (object)['number' => 5, 'name' => 'Fifth Semester'],
                (object)['number' => 6, 'name' => 'Sixth Semester'],
            ]);
        }

        $typeCountsRaw = Course::query()
            ->where('status', 'active')
            ->select('semester', 'subject_type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('semester', 'subject_type')
            ->get();

        $typeCountsBySemester = [];
        foreach ($typeCountsRaw as $row) {
            $sem = (string) ($row->semester ?? '');
            $type = (string) ($row->subject_type ?? 'core');
            if ($sem === '') continue;
            $typeCountsBySemester[$sem] ??= ['core' => 0, 'elective' => 0, 'optional' => 0];
            if (!array_key_exists($type, $typeCountsBySemester[$sem])) {
                $typeCountsBySemester[$sem][$type] = 0;
            }
            $typeCountsBySemester[$sem][$type] = (int) ($row->cnt ?? 0);
        }

        $subjectCountsRaw = Course::query()
            ->where('status', 'active')
            ->select('semester', DB::raw('COUNT(*) as cnt'))
            ->groupBy('semester')
            ->pluck('cnt', 'semester');

        $studentCountsRaw = DB::table('subject_students')
            ->join('subjects', 'subject_students.subject_id', '=', 'subjects.id')
            ->join('students', 'subject_students.student_id', '=', 'students.id')
            ->where('subjects.status', 'active')
            ->where('students.status', 'active')
            ->where(function ($q) {
                $q->where('students.is_alumni', 0)
                  ->orWhereNull('students.is_alumni');
            })
            ->select('subjects.semester', DB::raw('COUNT(DISTINCT subject_students.student_id) as cnt'))
            ->groupBy('subjects.semester')
            ->pluck('cnt', 'semester');

        $studentCountsBySemester = DB::table('students')
            ->where('students.status', 'active')
            ->where(function ($q) {
                $q->where('students.is_alumni', 0)
                  ->orWhereNull('students.is_alumni');
            })
            ->select('semester', DB::raw('COUNT(*) as cnt'))
            ->groupBy('semester')
            ->pluck('cnt', 'semester');

        $allStudentsCount = DB::table('subject_students')
            ->join('subjects', 'subject_students.subject_id', '=', 'subjects.id')
            ->join('students', 'subject_students.student_id', '=', 'students.id')
            ->where('subjects.status', 'active')
            ->where('students.status', 'active')
            ->where(function ($q) {
                $q->where('students.is_alumni', 0)
                  ->orWhereNull('students.is_alumni');
            })
            ->distinct('subject_students.student_id')
            ->count('subject_students.student_id');

        if ($allStudentsCount === 0) {
            $allStudentsCount = DB::table('students')
                ->where('students.status', 'active')
                ->where(function ($q) {
                    $q->where('students.is_alumni', 0)
                      ->orWhereNull('students.is_alumni');
                })
                ->count();
        }

        $baseParams = $request->except(['semester', 'page']);
        $selected = trim((string) $request->get('semester', ''));

        $cards = [];

        $allCore = 0;
        $allElective = 0;
        $allOptional = 0;
        foreach ($typeCountsBySemester as $counts) {
            $allCore += (int) ($counts['core'] ?? 0);
            $allElective += (int) ($counts['elective'] ?? 0);
            $allOptional += (int) ($counts['optional'] ?? 0);
        }

        $cards[] = [
            'semester' => ['number' => null, 'name' => 'All Semesters', 'academic_year' => null],
            'subjectCount' => (int) ($subjectCountsRaw->sum() ?? 0),
            'metrics' => [
                ['icon' => 'bi bi-bookmark-check', 'label' => 'Core', 'value' => $allCore],
                ['icon' => 'bi bi-star', 'label' => 'Elective', 'value' => $allElective],
                ['icon' => 'bi bi-list-stars', 'label' => 'Optional', 'value' => $allOptional],
                ['icon' => 'bi bi-people', 'label' => 'Students', 'value' => (int) $allStudentsCount],
            ],
            'isActive' => $selected === '',
            'url' => route('admin.courses', $baseParams),
        ];

        foreach ($semesterRecords as $semesterRecord) {
            $number = (string) ($semesterRecord->number ?? $semesterRecord['number'] ?? '');
            $name = $semesterRecord->name ?? $semesterRecord['name'] ?? (\App\Models\Semester::getOrdinalName((int)$number) ?? 'Semester '.$number);
            $subjectCount = (int) ($subjectCountsRaw[$number] ?? 0);
            $typeCounts = $typeCountsBySemester[$number] ?? ['core' => 0, 'elective' => 0, 'optional' => 0];

            $studentCountForSem = (int) ($studentCountsRaw[$number] ?? 0);
            if ($studentCountForSem === 0) {
                $studentCountForSem = (int) ($studentCountsBySemester[$number] ?? 0);
            }

            $cards[] = [
                'semester' => ['number' => $number, 'name' => $name, 'academic_year' => $semesterRecord->academic_year ?? $semesterRecord['academic_year'] ?? null],
                'subjectCount' => $subjectCount,
                'metrics' => [
                    ['icon' => 'bi bi-bookmark-check', 'label' => 'Core', 'value' => (int) ($typeCounts['core'] ?? 0)],
                    ['icon' => 'bi bi-star', 'label' => 'Elective', 'value' => (int) ($typeCounts['elective'] ?? 0)],
                    ['icon' => 'bi bi-list-stars', 'label' => 'Optional', 'value' => (int) ($typeCounts['optional'] ?? 0)],
                    ['icon' => 'bi bi-people', 'label' => 'Students', 'value' => $studentCountForSem],
                ],
                'isActive' => $selected === $number,
                'url' => route('admin.courses', array_merge($baseParams, ['semester' => $number])),
            ];
        }

        return $cards;
    }

    private function getSelectedSemesterLabel(?string $semester): string
    {
        $semester = trim((string) ($semester ?? ''));
        if ($semester === '') {
            return '';
        }

        $map = [
            '1' => 'First Semester',
            '2' => 'Second Semester',
            '3' => 'Third Semester',
            '4' => 'Fourth Semester',
            '5' => 'Fifth Semester',
            '6' => 'Sixth Semester',
        ];

        return $map[$semester] ?? ('Semester ' . $semester);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                "subject_name" => "required|string|max:100",
                "subject_code" => "required|string|max:20|unique:subjects,subject_code",
                "semester" => "nullable|string|max:20",
                "teacher_id" => "nullable|exists:teachers,id",
                "credits" => "nullable|integer|min:1|max:10",
                "status" => "nullable|string|in:active,archived",
                "description" => "nullable|string|max:500",
                "prerequisite" => "nullable|string|max:255",
                "remarks" => "nullable|string|max:500",
                "subject_type" => "nullable|string|in:core,elective,optional",
                "has_lab" => "nullable|boolean",
                "lab_technician_id" => "nullable|exists:teachers,id",
                "lab_document" => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip",
                "syllabus_document" => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip",
                "practical_full_marks" => "nullable|integer|min:0",
                "practical_pass_marks" => "nullable|integer|min:0",
                "practical_obtained_marks" => "nullable|integer|min:0",
                "max_students" => "nullable|integer|min:1",
                "min_students" => "nullable|integer|min:1",
                "elective_group" => "nullable|string|max:100",
                "is_elective_open" => "nullable|boolean",
                "lecture_hours" => "nullable|integer|min:0|max:10",
                "practical_hours" => "nullable|integer|min:0|max:10",
                "tutorial_hours" => "nullable|integer|min:0|max:10",
            ]);

            // If the course is an elective, always open enrollment by default.
            if (($data['subject_type'] ?? null) === 'elective') {
                $data['is_elective_open'] = true;
            }

            // Clear practical mark fields when lab is not enabled
            if (empty($data['has_lab'])) {
                $data['practical_full_marks'] = null;
                $data['practical_pass_marks'] = null;
                $data['practical_obtained_marks'] = null;
            }

            // Handle optional lab document upload
            $labDocumentPath = null;
            if ($request->hasFile('lab_document')) {
                $labDocumentPath = $request->file('lab_document')->store('subject_lab_documents', 'public');
            }

            // Handle optional syllabus document upload
            $syllabusDocumentPath = null;
            if ($request->hasFile('syllabus_document')) {
                $syllabusDocumentPath = $request->file('syllabus_document')->store('subject_syllabi', 'public');
            }

            $insertData = [
                "subject_name" => $data["subject_name"],
                "subject_code" => $data["subject_code"],
                "semester" => $data["semester"] ?? null,
                "credits" => $data["credits"] ?? 3,
                "status" => $data["status"] ?? "active",
                "description" => $data["description"] ?? null,
                "prerequisite" => $data["prerequisite"] ?? null,
                "remarks" => $data["remarks"] ?? null,
                "subject_type" => $data["subject_type"] ?? "core",
                "has_lab" => $data["has_lab"] ?? false,
                "lab_technician_id" => $data["lab_technician_id"] ?? null,
                "lab_document" => $labDocumentPath,
                "syllabus_document_path" => $syllabusDocumentPath,
                "practical_full_marks" => $data["practical_full_marks"] ?? null,
                "practical_pass_marks" => $data["practical_pass_marks"] ?? null,
                "practical_obtained_marks" => $data["practical_obtained_marks"] ?? null,
                "max_students" => $data["max_students"] ?? null,
                "min_students" => $data["min_students"] ?? null,
                "elective_group" => $data["elective_group"] ?? null,
                "is_elective_open" => $data["is_elective_open"] ?? false,
                "lecture_hours" => $data["lecture_hours"] ?? 4,
                "practical_hours" => $data["practical_hours"] ?? 2,
                "tutorial_hours" => $data["tutorial_hours"] ?? 1,
                "created_at" => now(),
                "updated_at" => now()
            ];

            if ($this->subjectsHasColumn('teacher_id')) {
                $insertData["teacher_id"] = $data["teacher_id"] ?? null;
            }

            $courseId = DB::table("subjects")->insertGetId($insertData);

            $this->syncPrimaryTeacherAssignment($courseId, $data["teacher_id"] ?? null, $data["semester"] ?? null);

            // Log activity
            $this->logActivity('Course', 'Created Course', "Course '{$data['subject_name']}' created");

            if ($request->expectsJson()) {
                return response()->json(["success" => true, "message" => "Course created successfully", "course_id" => $courseId]);
            }

            return redirect()->route('admin.courses')->with('success', 'Course created successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                "subject_name" => "required|string|max:100",
                "subject_code" => "required|string|max:20|unique:subjects,subject_code," . $id,
                "semester" => "nullable|string|max:20",
                "teacher_id" => "nullable|exists:teachers,id",
                "credits" => "nullable|integer|min:1|max:10",
                "status" => "nullable|string|in:active,archived",
                "description" => "nullable|string|max:500",
                "description_ne" => "nullable|string|max:500",
                "prerequisite" => "nullable|string|max:255",
                "start_date" => "nullable|date",
                "end_date" => "nullable|date",
                "remarks" => "nullable|string|max:500",
                "subject_type" => "nullable|string|in:core,elective,optional,major,project",
                "theory_percentage" => "nullable|integer|min:0|max:100",
                "practical_percentage" => "nullable|integer|min:0|max:100",
                "internal_percentage" => "nullable|integer|min:0|max:100",
                "external_percentage" => "nullable|integer|min:0|max:100",
                "lecture_hours" => "nullable|integer|min:0|max:10",
                "practical_hours" => "nullable|integer|min:0|max:10",
                "tutorial_hours" => "nullable|integer|min:0|max:10",
                // Lab settings
                "has_lab" => "nullable|boolean",
                "lab_technician_id" => "nullable|exists:teachers,id",
                "lab_document" => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip",
                "syllabus_document" => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip",
                "practical_full_marks" => "nullable|integer|min:0",
                "practical_pass_marks" => "nullable|integer|min:0",
                "practical_obtained_marks" => "nullable|integer|min:0",
                "max_students" => "nullable|integer|min:1",
                "min_students" => "nullable|integer|min:1",
                "elective_group" => "nullable|string|max:100",
                "is_elective_open" => "nullable|boolean",
            ]);

            // If the course is an elective, always open enrollment by default.
            if (($data['subject_type'] ?? null) === 'elective') {
                $data['is_elective_open'] = true;
            }

            // Clear practical mark fields when lab is not enabled
            if (empty($data['has_lab'])) {
                $data['practical_full_marks'] = null;
                $data['practical_pass_marks'] = null;
                $data['practical_obtained_marks'] = null;
            }

            // Handle optional lab document upload
            $labDocumentPath = null;
            if ($request->hasFile('lab_document')) {
                $labDocumentPath = $request->file('lab_document')->store('subject_lab_documents', 'public');
            }

            // Handle optional syllabus document upload
            $syllabusDocumentPath = null;
            if ($request->hasFile('syllabus_document')) {
                $syllabusDocumentPath = $request->file('syllabus_document')->store('subject_syllabi', 'public');
            }

            $updateData = [
                "subject_name" => $data["subject_name"],
                "subject_code" => $data["subject_code"],
                "semester" => $data["semester"] ?? null,
                "credits" => $data["credits"] ?? 3,
                "status" => $data["status"] ?? "active",
                "description" => $data["description"] ?? null,
                "prerequisite" => $data["prerequisite"] ?? null,
                "remarks" => $data["remarks"] ?? null,
                "subject_type" => $data["subject_type"] ?? "core",
                "has_lab" => $data["has_lab"] ?? false,
                "lab_technician_id" => $data["lab_technician_id"] ?? null,
                "practical_full_marks" => $data["practical_full_marks"] ?? null,
                "practical_pass_marks" => $data["practical_pass_marks"] ?? null,
                "practical_obtained_marks" => $data["practical_obtained_marks"] ?? null,
                "max_students" => $data["max_students"] ?? null,
                "min_students" => $data["min_students"] ?? null,
                "elective_group" => $data["elective_group"] ?? null,
                "is_elective_open" => $data["is_elective_open"] ?? false,
                "lecture_hours" => $data["lecture_hours"] ?? 4,
                "practical_hours" => $data["practical_hours"] ?? 2,
                "tutorial_hours" => $data["tutorial_hours"] ?? 1,
                "updated_at" => now(),
            ];

            if ($this->subjectsHasColumn('teacher_id')) {
                $updateData["teacher_id"] = $data["teacher_id"] ?? null;
            }

            if ($labDocumentPath) {
                $updateData['lab_document'] = $labDocumentPath;
            }
            if ($syllabusDocumentPath) {
                $updateData['syllabus_document_path'] = $syllabusDocumentPath;
            }

            DB::table("subjects")->where("id", $id)->update($updateData);
            $this->syncPrimaryTeacherAssignment($id, $data["teacher_id"] ?? null, $data["semester"] ?? null);
            if ($request->expectsJson()) {
                return response()->json(["success" => true, "message" => "Course updated successfully"]);
            }

            return redirect()->route('admin.courses')->with('success', 'Course updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $course = DB::table("subjects")->find($id);
            if (!$course) {
                return response()->json(["success" => false, "message" => "Course not found"], 404);
            }

            $hasMarks = DB::table("marks")->where("subject_id", $id)->exists();
            $hasAttendance = DB::table("attendance")->where("subject", $id)->exists();
            $courseName = $course->subject_name;

            if ($hasMarks || $hasAttendance) {
                DB::table("subjects")->where("id", $id)->update(["status" => "archived", "updated_at" => now()]);
                
                // Log activity
                $this->logActivity('Course', 'Archived Course', "Course '{$courseName}' was archived");
                
                return response()->json(["success" => true, "message" => "Course archived"]);
            }

            DB::table("subjects")->where("id", $id)->delete();
            
            // Log activity
            $this->logActivity('Course', 'Deleted Course', "Course '{$courseName}' was deleted");
            
            return response()->json(["success" => true, "message" => "Course deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $course = DB::table("subjects")->where("id", $id)->first();
            
            if (!$course) {
                return response()->json(["success" => false, "message" => "Course not found"], 404);
            }

            $teacherId = null;
            if ($this->subjectsHasColumn('teacher_id')) {
                $teacherId = $course->teacher_id ?? null;
            } elseif ($this->hasTable('subject_teacher')) {
                $teacherId = DB::table('subject_teacher')
                    ->where('subject_id', $id)
                    ->orderByRaw("CASE WHEN role = 'primary' THEN 0 ELSE 1 END")
                    ->orderByDesc('assigned_at')
                    ->orderByDesc('id')
                    ->value('teacher_id');
            }
             
            $courseData = [
                'id' => $course->id,
                'subject_name' => $course->subject_name,
                'subject_code' => $course->subject_code,
                'semester' => $course->semester,
                'teacher_id' => $teacherId,
                'credits' => $course->credits,
                'status' => $course->status,
                'description' => $course->description,
                'prerequisite' => $course->prerequisite,
                'remarks' => $course->remarks,
                'subject_type' => $course->subject_type,
                'has_lab' => (bool) ($course->has_lab ?? false),
                'lab_technician_id' => $course->lab_technician_id,
                'lab_document' => $course->lab_document ? Storage::url($course->lab_document) : null,
                'syllabus_document' => $course->syllabus_document_path ? Storage::url($course->syllabus_document_path) : null,
                'practical_full_marks' => $course->practical_full_marks,
                'practical_pass_marks' => $course->practical_pass_marks,
                'practical_obtained_marks' => $course->practical_obtained_marks,
            ];

            return response()->json([
                'success' => true,
                'course' => (object) $courseData
            ]);
        } catch (\Exception $e) {
            Log::error('Course edit error', [
                'id' => $id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Server error loading course data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTeachers()
    {
        // Include all teachers that are users with role 'teacher' or are assigned to any subject
        $assignedTeacherIds = [];
        if ($this->subjectsHasColumn('teacher_id')) {
            $assignedTeacherIds = DB::table('subjects')
                ->whereNotNull('teacher_id')
                ->distinct()
                ->pluck('teacher_id')
                ->filter()
                ->toArray();
        }

        $pivotTeacherIds = [];
        if ($this->hasTable('subject_teacher')) {
            $pivotTeacherIds = DB::table('subject_teacher')
                ->distinct()
                ->pluck('teacher_id')
                ->filter()
                ->toArray();
        }

        $assignedTeacherIds = array_values(array_unique(array_merge($assignedTeacherIds, $pivotTeacherIds)));

        $teachers = DB::table("teachers")
            ->join("users", "teachers.user_id", "=", "users.id")
            ->where(function($q) use ($assignedTeacherIds) {
                $q->where("users.role", "teacher");
                if (!empty($assignedTeacherIds)) {
                    $q->orWhereIn('teachers.id', $assignedTeacherIds);
                }
            })
            ->select("teachers.id", "users.name", "users.email")
            ->orderBy("users.name")
            ->get();

        // Log for debugging - how many teachers returned and any assigned teacher ids
        \Log::info('getTeachers called: count=' . $teachers->count() . ', assigned_teacher_ids=' . json_encode($assignedTeacherIds));

        return response()->json(["success" => true, "teachers" => $teachers]);
    }

    /**
     * Return teachers assigned to subjects in a specific semester.
     * If `current_teacher_id` query param is provided, include that teacher even
     * if they are not assigned to the semester so the edit form can show current value.
     */
    public function getTeachersBySemester(Request $request, $semester)
    {
        $currentTeacher = $request->query('current_teacher_id');

        // Find teachers assigned to subjects in the requested semester
        $assignedTeacherIds = [];
        if ($this->subjectsHasColumn('teacher_id')) {
            $assignedTeacherIds = DB::table('subjects')
                ->where('semester', $semester)
                ->whereNotNull('teacher_id')
                ->distinct()
                ->pluck('teacher_id')
                ->filter()
                ->toArray();
        }

        $pivotTeacherIds = [];
        if ($this->hasTable('subject_teacher')) {
            $pivotTeacherIds = DB::table('subject_teacher')
                ->where('semester', $semester)
                ->distinct()
                ->pluck('teacher_id')
                ->filter()
                ->toArray();
        }

        $assignedTeacherIds = array_values(array_unique(array_merge($assignedTeacherIds, $pivotTeacherIds)));

        $query = DB::table('teachers')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->select('teachers.id', 'users.name', 'users.email')
            ->orderBy('users.name');

        if (!empty($assignedTeacherIds)) {
            $query->whereIn('teachers.id', $assignedTeacherIds);
        } else {
            // No assigned teachers for this semester, return empty collection
            $teachers = collect([]);
            // If current teacher provided, attempt to include them
            if ($currentTeacher) {
                $t = DB::table('teachers')
                    ->join('users', 'teachers.user_id', '=', 'users.id')
                    ->where('teachers.id', $currentTeacher)
                    ->select('teachers.id', 'users.name', 'users.email')
                    ->first();
                if ($t) {
                    $teachers = collect([$t]);
                }
            }

            return response()->json(["success" => true, "teachers" => $teachers]);
        }

        // If a current teacher is provided but not in the assigned list, include them
        if ($currentTeacher && !in_array($currentTeacher, $assignedTeacherIds)) {
            $query->orWhere('teachers.id', $currentTeacher);
        }

        $teachers = $query->get();

        return response()->json(["success" => true, "teachers" => $teachers]);
    }

    /**
     * Return a single teacher's details by id (used as a fallback when teacher isn't in list)
     */
    public function getTeacher($id)
    {
        $teacher = DB::table("teachers")
            ->join("users", "teachers.user_id", "=", "users.id")
            ->where("teachers.id", $id)
            ->select("teachers.id", "users.name", "users.email")
            ->first();

        if (!$teacher) {
            \Log::warning('getTeacher: teacher not found', ['id' => $id]);
            return response()->json(["success" => false, "message" => "Teacher not found"], 404);
        }

        \Log::info('getTeacher: found', ['id' => $id, 'name' => $teacher->name]);
        return response()->json(["success" => true, "teacher" => $teacher]);
    }

    /**
     * Get complete subject details for the detail view modal
     * Returns all subject information including relationships
     */
    public function getDetail($id)
    {
        $course = Course::with(['teacher.user', 'labTechnician.user'])->find($id);
        
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found'
            ], 404);
        }
        
        $courseData = [
            'id' => $course->id,
            'subject_name' => $course->subject_name,
            'subject_code' => $course->subject_code,
            'credits' => $course->credits,
            'semester' => $course->semester,
            'status' => $course->status,

            'subject_type' => $course->subject_type,
            'teacher_id' => $course->teacher_id,
            'teacher_name' => $course->teacher?->user?->name ?? 'Not Assigned',
            'lab_technician_id' => $course->lab_technician_id,
            'lab_technician_name' => $course->labTechnician?->user?->name ?? null,

            'max_students' => $course->max_students,
            'min_students' => $course->min_students,
            'elective_group' => $course->elective_group,
            'is_elective_open' => (bool) ($course->is_elective_open ?? false),

            'description' => $course->description,
            'prerequisite' => $course->prerequisite,
            'remarks' => $course->remarks,
            'lecture_hours' => $course->lecture_hours,
            'practical_hours' => $course->practical_hours,
            'tutorial_hours' => $course->tutorial_hours,

            'updated_at' => $course->updated_at?->toISOString(),
        ];
        
        return response()->json([
            'success' => true,
            'subject' => $courseData
        ]);
    }

    /**
     * Return teachers assigned to courses in a specific semester
     * This is used to filter teachers in the course edit modal based on selected semester
     */

    private function syncPrimaryTeacherAssignment($subjectId, $teacherId, $semester = null)
    {
        if (!$teacherId) {
            return;
        }

        try {
            // Ensure only one "primary" teacher per subject by demoting others.
            SubjectTeacher::where('subject_id', $subjectId)
                ->where('role', 'primary')
                ->where('teacher_id', '!=', $teacherId)
                ->update([
                    'role' => 'assistant',
                    'updated_at' => now(),
                ]);

            SubjectTeacher::updateOrCreate(
                ['subject_id' => $subjectId, 'teacher_id' => $teacherId],
                [
                    'semester' => $semester ?: null,
                    'role' => 'primary',
                    'assigned_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::warning('SubjectTeacher sync failed', [
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Print list of courses/subjects
     */
    public function printList(Request $request)
    {
        $search = $request->input('q', '');
        $status = $request->input('status', '');
        $category = $request->input('category', '');
        $semester = $request->input('semester', '');

        $coursesQuery = DB::table("subjects");

        if ($this->subjectsHasColumn('teacher_id')) {
            $coursesQuery
                ->leftJoin("teachers", "subjects.teacher_id", "=", "teachers.id")
                ->leftJoin("users", "teachers.user_id", "=", "users.id")
                ->select("subjects.*", "users.name as teacher_name", "users.email as teacher_email");
        } elseif ($this->hasTable('subject_teacher')) {
            $coursesQuery
                ->leftJoin("subject_teacher as st", function ($join) {
                    $join->on("subjects.id", "=", "st.subject_id")
                        ->where("st.role", "primary");
                })
                ->leftJoin("teachers", "st.teacher_id", "=", "teachers.id")
                ->leftJoin("users", "teachers.user_id", "=", "users.id")
                ->select("subjects.*", "users.name as teacher_name", "users.email as teacher_email");
        } else {
            $coursesQuery->select("subjects.*");
        }

        if (!empty($search)) {
            $coursesQuery->where(function($q) use ($search) {
                $q->where("subjects.subject_name", "like", "%{$search}%")
                  ->orWhere("subjects.subject_code", "like", "%{$search}%")
                  ->orWhere("subjects.category", "like", "%{$search}%");
            });
        }

        if (!empty($status)) {
            $coursesQuery->where("subjects.status", $status);
        }

        if (!empty($category)) {
            $coursesQuery->where("subjects.category", $category);
        }

        if (!empty($semester)) {
            $coursesQuery->where("subjects.semester", $semester);
        }

        $courses = $coursesQuery->orderBy("subjects.subject_name")->get();
        $college = \App\Models\Department::first();

        return view('admin.print.course-list', compact('courses', 'college'));
    }

    /**
     * Show full course view page
     */
    public function showView($id)
    {
        try {
            $course = Course::with(['teacher.user', 'labTechnician.user'])
                           ->findOrFail($id);
            
            return view('admin.course-show', compact('course'));
        } catch (\Exception $e) {
            Log::error('Course showView error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Course not found');
        }
    }
}
