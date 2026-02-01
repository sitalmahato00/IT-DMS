<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->get("q", "");
            $status = $request->get("status", "");
            $category = $request->get("category", "");
            $semester = $request->get("semester", "");

            $coursesQuery = DB::table("subjects")
                ->leftJoin("teachers", "subjects.teacher_id", "=", "teachers.id")
                ->leftJoin("users", "teachers.user_id", "=", "users.id")
                ->select("subjects.*", "users.name as teacher_name", "users.email as teacher_email");

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

            $stats = [
                "total" => DB::table("subjects")->count(),
                "active" => DB::table("subjects")->where("status", "active")->count(),
                "archived" => DB::table("subjects")->where("status", "archived")->count(),
            ];

            $departments = DB::table("subjects")->distinct()->pluck("category")->filter()->sort()->values();

            if ($departments->isEmpty()) {
                $departments = collect(["Software Engineering", "Computer Science", "Information Technology"]);
            }

            $allTeachers = DB::table("teachers")
                ->join("users", "teachers.user_id", "=", "users.id")
                ->where("users.role", "teacher")
                ->select("teachers.id", "users.name")
                ->orderBy("users.name")
                ->get();

            return view("admin.courses", compact("courses", "stats", "departments", "allTeachers", "search", "status", "category", "semester"));

        } catch (\Exception $e) {
            Log::error("Courses error: " . $e->getMessage());
            return view("admin.courses", [
                "courses" => collect([]),
                "stats" => ["total" => 0, "active" => 0, "archived" => 0],
                "departments" => collect(["Software Engineering", "Computer Science", "Information Technology"]),
                "allTeachers" => collect([]),
                "search" => "",
                "status" => "",
                "category" => "",
                "semester" => ""
            ]);
        }
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
                "category" => "nullable|string|max:100",
                "theory_percentage" => "nullable|integer|min:0|max:100",
                "practical_percentage" => "nullable|integer|min:0|max:100",
                "internal_percentage" => "nullable|integer|min:0|max:100",
                "external_percentage" => "nullable|integer|min:0|max:100",
                "lecture_hours" => "nullable|integer|min:0|max:10",
                "practical_hours" => "nullable|integer|min:0|max:10",
                "tutorial_hours" => "nullable|integer|min:0|max:10",
            ]);

            $courseId = DB::table("subjects")->insertGetId([
                "subject_name" => $data["subject_name"],
                "subject_code" => $data["subject_code"],
                "semester" => $data["semester"] ?? null,
                "teacher_id" => $data["teacher_id"] ?? null,
                "credits" => $data["credits"] ?? 3,
                "status" => $data["status"] ?? "active",
                "category" => $data["category"] ?? null,
                "theory_percentage" => $data["theory_percentage"] ?? 70,
                "practical_percentage" => $data["practical_percentage"] ?? 30,
                "internal_percentage" => $data["internal_percentage"] ?? 40,
                "external_percentage" => $data["external_percentage"] ?? 60,
                "lecture_hours" => $data["lecture_hours"] ?? 4,
                "practical_hours" => $data["practical_hours"] ?? 2,
                "tutorial_hours" => $data["tutorial_hours"] ?? 1,
                "created_at" => now(),
                "updated_at" => now()
            ]);

            return response()->json(["success" => true, "message" => "Course created successfully", "course_id" => $courseId]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
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
                "category" => "nullable|string|max:100",
                "theory_percentage" => "nullable|integer|min:0|max:100",
                "practical_percentage" => "nullable|integer|min:0|max:100",
                "internal_percentage" => "nullable|integer|min:0|max:100",
                "external_percentage" => "nullable|integer|min:0|max:100",
                "lecture_hours" => "nullable|integer|min:0|max:10",
                "practical_hours" => "nullable|integer|min:0|max:10",
                "tutorial_hours" => "nullable|integer|min:0|max:10",
            ]);

            DB::table("subjects")->where("id", $id)->update([
                "subject_name" => $data["subject_name"],
                "subject_code" => $data["subject_code"],
                "semester" => $data["semester"] ?? null,
                "teacher_id" => $data["teacher_id"] ?? null,
                "credits" => $data["credits"] ?? 3,
                "status" => $data["status"] ?? "active",
                "category" => $data["category"] ?? null,
                "theory_percentage" => $data["theory_percentage"] ?? 70,
                "practical_percentage" => $data["practical_percentage"] ?? 30,
                "internal_percentage" => $data["internal_percentage"] ?? 40,
                "external_percentage" => $data["external_percentage"] ?? 60,
                "lecture_hours" => $data["lecture_hours"] ?? 4,
                "practical_hours" => $data["practical_hours"] ?? 2,
                "tutorial_hours" => $data["tutorial_hours"] ?? 1,
                "updated_at" => now()
            ]);
            return response()->json(["success" => true, "message" => "Course updated successfully"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $hasMarks = DB::table("marks")->where("subject_id", $id)->exists();
            $hasAttendance = DB::table("attendance")->where("subject", $id)->exists();

            if ($hasMarks || $hasAttendance) {
                DB::table("subjects")->where("id", $id)->update(["status" => "archived", "updated_at" => now()]);
                return response()->json(["success" => true, "message" => "Course archived"]);
            }

            DB::table("subjects")->where("id", $id)->delete();
            return response()->json(["success" => true, "message" => "Course deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        // First get the course without join to get the actual teacher_id from subjects table
        $course = DB::table("subjects")->where("id", $id)->first();
        
        if (!$course) {
            return response()->json(["success" => false, "message" => "Course not found"], 404);
        }
        
        // Ensure teacher_id is always present in response (even if null)
        $courseData = [
            'id' => $course->id,
            'subject_name' => $course->subject_name,
            'subject_code' => $course->subject_code,
            'semester' => $course->semester,
            'teacher_id' => $course->teacher_id,
            'credits' => $course->credits,
            'status' => $course->status,
            'category' => $course->category,
            'theory_percentage' => $course->theory_percentage,
            'practical_percentage' => $course->practical_percentage,
            'internal_percentage' => $course->internal_percentage,
            'external_percentage' => $course->external_percentage,
            'lecture_hours' => $course->lecture_hours,
            'practical_hours' => $course->practical_hours,
            'tutorial_hours' => $course->tutorial_hours,
        ];
        
        // Get teacher info separately
        if ($course->teacher_id) {
            $teacher = DB::table("teachers")
                ->join("users", "teachers.user_id", "=", "users.id")
                ->where("teachers.id", $course->teacher_id)
                ->select("users.name as teacher_name", "users.email as teacher_email")
                ->first();
            
            if ($teacher) {
                $courseData['teacher_name'] = $teacher->teacher_name;
                $courseData['teacher_email'] = $teacher->teacher_email;
            } else {
                $courseData['teacher_name'] = null;
                $courseData['teacher_email'] = null;
            }
        } else {
            $courseData['teacher_name'] = null;
            $courseData['teacher_email'] = null;
        }

        \Log::info('Course edit response', ['id' => $id, 'teacher_id' => $course->teacher_id]);
        
        return response()->json(["success" => true, "course" => $courseData]);
    }

    public function show($id)
    {
        $course = DB::table("subjects")
            ->leftJoin("teachers", "subjects.teacher_id", "=", "teachers.id")
            ->leftJoin("users", "teachers.user_id", "=", "users.id")
            ->where("subjects.id", $id)
            ->select("subjects.*", "users.name as teacher_name", "users.email as teacher_email")
            ->first();

        if (!$course) {
            return redirect()->route("admin.courses")->with("error", "Course not found");
        }

        return view("admin.course-show", compact("course"));
    }

    public function getTeachers()
    {
        // Include all teachers that are users with role 'teacher' or are assigned to any subject
        $assignedTeacherIds = DB::table('subjects')
            ->whereNotNull('teacher_id')
            ->distinct()
            ->pluck('teacher_id')
            ->filter()
            ->toArray();

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
        $assignedTeacherIds = DB::table('subjects')
            ->where('semester', $semester)
            ->whereNotNull('teacher_id')
            ->distinct()
            ->pluck('teacher_id')
            ->filter()
            ->toArray();

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
     * Return teachers assigned to courses in a specific semester
     * This is used to filter teachers in the course edit modal based on selected semester
     */
    
}
