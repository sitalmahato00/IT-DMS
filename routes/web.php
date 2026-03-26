 <?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\PublicStudyMaterialController;
use App\Http\Controllers\PublicGalleryController;
use App\Http\Controllers\NoticePortalController;
use App\Http\Controllers\GalleryPortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('home');
Route::get('/department/about/{id?}', [\App\Http\Controllers\LandingController::class, 'about'])->name('department.about');
Route::get('/subjects', [\App\Http\Controllers\SubjectsController::class, 'index'])->name('subjects.index');
Route::get('/faculty', [FacultyController::class, 'index'])->name('faculty.index');
Route::get('/resources', [PublicStudyMaterialController::class, 'index'])->name('public.resources.index');
Route::get('/resources/download/{id}', [PublicStudyMaterialController::class, 'download'])->name('materials.download');
Route::get('/notices', [NoticePortalController::class, 'publicIndex'])->name('public.notices.index');
Route::get('/notices/fetch', [NoticePortalController::class, 'fetch'])->name('notices.fetch');
Route::get('/notices/{id}', [NoticePortalController::class, 'show'])->name('notices.show');
Route::get('/gallery/fetch', [GalleryPortalController::class, 'fetch'])->name('gallery.fetch');

Route::get('/gallery', [GalleryPortalController::class, 'index'])->name('gallery.index');
Route::get('/gallery/download/{id}', [PublicGalleryController::class, 'download'])->name('gallery.download');

// Language switcher: sets the locale in the session and redirects back
Route::post('locale', function (Illuminate\Http\Request $request) {
    $locale = $request->input('locale');
    $supported = array_keys(config('locales.supported'));
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

Route::get('/dashboard', function () {
    // Redirect to role-based dashboard
    return redirect()->to(auth()->user()->getDashboardRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

// Student Profile Routes
Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Student\StudentProfileController::class, 'update'])->name('profile.update');
    
    // My Courses
    Route::get('/courses', [\App\Http\Controllers\Student\StudentCourseController::class, 'index'])->name('courses');
    Route::get('/courses/{id}', [\App\Http\Controllers\Student\StudentCourseController::class, 'show'])->name('courses.show');
    
    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Student\StudentAttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance/{subjectId}', [\App\Http\Controllers\Student\StudentAttendanceController::class, 'show'])->name('attendance.show');
    
    // Marks/Results
    Route::get('/marks', [\App\Http\Controllers\Student\StudentMarkController::class, 'index'])->name('marks');
    Route::get('/marks/{subjectId}', [\App\Http\Controllers\Student\StudentMarkController::class, 'show'])->name('marks.show');
    
    // Study Materials
    Route::get('/study-materials', [\App\Http\Controllers\Student\StudentStudyMaterialController::class, 'index'])->name('study-materials');
    Route::get('/study-materials/download/{id}', [\App\Http\Controllers\Student\StudentStudyMaterialController::class, 'download'])->name('study-materials.download');
    
    // Notices
    Route::get('/notices', [\App\Http\Controllers\Student\StudentNoticeController::class, 'index'])->name('notices');
    Route::get('/notices/{id}', [\App\Http\Controllers\Student\StudentNoticeController::class, 'show'])->name('notices.show');
    
    // Assignments
    Route::get('/assignments', [\App\Http\Controllers\Student\StudentAssignmentController::class, 'index'])->name('assignments');
    Route::get('/assignments/{id}', [\App\Http\Controllers\Student\StudentAssignmentController::class, 'show'])->name('assignments.show');
    
    // My Teachers
    Route::get('/teachers', [\App\Http\Controllers\Student\StudentTeacherController::class, 'index'])->name('teachers');
    Route::get('/teachers/{id}', [\App\Http\Controllers\Student\StudentTeacherController::class, 'show'])->name('teachers.show');
});

// Teacher Profile Routes
Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Teacher\TeacherProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Teacher\TeacherProfileController::class, 'update'])->name('profile.update');
    Route::get('/export', [\App\Http\Controllers\Teacher\TeacherExportController::class, 'export'])->name('export');
    
    // Teacher pages - using dynamic controllers
    Route::get('/subjects', [\App\Http\Controllers\Teacher\TeacherSubjectController::class, 'index'])->name('subjects');
    Route::get('/subjects/export', [\App\Http\Controllers\Teacher\TeacherSubjectController::class, 'export'])->name('subjects.export');
    Route::get('/subjects/{id}', [\App\Http\Controllers\Teacher\TeacherSubjectController::class, 'show'])->name('subjects.show');
    Route::get('/students', [\App\Http\Controllers\Teacher\TeacherStudentController::class, 'index'])->name('students');
    // Load subjects for a specific semester for dynamic subject loading in filters
    Route::get('/students/subjects-by-semester', [\App\Http\Controllers\Teacher\TeacherStudentController::class, 'subjectsBySemester'])->name('students.subjects-by-semester');
    Route::get('/students/export', [\App\Http\Controllers\Teacher\TeacherStudentController::class, 'export'])->name('students.export');
    Route::get('/attendance', [\App\Http\Controllers\Teacher\TeacherAttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/students', [\App\Http\Controllers\Teacher\TeacherAttendanceController::class, 'getStudentsForAttendance'])->name('attendance.students');
    Route::post('/attendance', [\App\Http\Controllers\Teacher\TeacherAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/print', [\App\Http\Controllers\Teacher\TeacherAttendanceController::class, 'printAttendance'])->name('attendance.print');
    // Bulk add attendance for all subjects for today
    Route::post('/attendance/bulk-add-all', [\App\Http\Controllers\Teacher\TeacherAttendanceController::class, 'bulkAddForAllSubjects'])->name('attendance.bulkAddAll');
    Route::get('/marks', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'index'])->name('marks');
    Route::post('/marks', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'store'])->name('marks.store');
    Route::put('/marks/{id}', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'update'])->name('marks.update');
    Route::get('/marks/exams', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'getExamsByCategory'])->name('marks.exams');
    Route::get('/marks/export', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'export'])->name('marks.export');
    Route::get('/marks/print', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'print'])->name('marks.print');
    
    // Teacher Marksheets (comprehensive view)
    Route::get('/marksheets', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'marksheets'])->name('marksheets');
    
    // Teacher Marksheet Search
    Route::get('/marksheet/search', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'marksheetSearch'])->name('marksheet.search');
    Route::get('/marksheet/print', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'marksheetPrint'])->name('marksheet.print');
    Route::get('/marksheet/export', [\App\Http\Controllers\Teacher\TeacherMarksController::class, 'marksheetExport'])->name('marksheet.export');
    
    Route::get('/study-materials/create', [\App\Http\Controllers\Teacher\TeacherStudyMaterialsController::class, 'create'])->name('study-materials.create');
    Route::post('/study-materials', [\App\Http\Controllers\Teacher\TeacherStudyMaterialsController::class, 'store'])->name('study-materials.store');
    Route::get('/study-materials', [\App\Http\Controllers\Teacher\TeacherStudyMaterialsController::class, 'index'])->name('study-materials');
    Route::get('/study-materials/download/{id}', [\App\Http\Controllers\Teacher\TeacherStudyMaterialsController::class, 'download'])->name('study-materials.download');
    Route::get('/notices/create', [\App\Http\Controllers\Teacher\TeacherNoticesController::class, 'create'])->name('notices.create');
    Route::post('/notices', [\App\Http\Controllers\Teacher\TeacherNoticesController::class, 'store'])->name('notices.store');
    Route::get('/notices', [\App\Http\Controllers\Teacher\TeacherNoticesController::class, 'index'])->name('notices');
    Route::get('/exams', [\App\Http\Controllers\Teacher\TeacherExamsController::class, 'index'])->name('exams');
    Route::get('/exams/{exam}/students', [\App\Http\Controllers\Teacher\TeacherExamsController::class, 'getExamStudents'])->name('exams.students');
    Route::get('/timetable', [\App\Http\Controllers\Teacher\TeacherTimetableController::class, 'index'])->name('timetable');
    Route::get('/reports', [\App\Http\Controllers\Teacher\TeacherReportsController::class, 'index'])->name('reports');
    Route::get('/notifications', function () {
        return view('teacher.notifications');
    })->name('notifications');
});

// Parent Profile Routes
Route::middleware(['auth', 'verified', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Parent\ParentProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Parent\ParentProfileController::class, 'update'])->name('profile.update');
    Route::get('/export', [\App\Http\Controllers\Parent\ParentExportController::class, 'export'])->name('export');
});

// Student-facing dashboard
Route::get('/student', function () {
    return view('student.studentdashboard');
})->middleware(['auth', 'verified', 'role:student'])->name('student.dashboard');

// Parent-facing dashboard
Route::get('/parent', function () {
    return view('parent.parentdashboard');
})->middleware(['auth', 'verified', 'role:parent'])->name('parent.dashboard');

// Teacher-facing dashboard
Route::get('/teacher', [\App\Http\Controllers\Teacher\TeacherDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:teacher'])
    ->name('teacher.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Shared conversion helper endpoints for AD<->BS (usable by teacher/admin pages)
Route::middleware(['auth', 'verified'])->prefix('convert')->group(function () {
    Route::post('/ad-to-bs', function (\Illuminate\Http\Request $request) {
        $date = $request->input('date');
        $bs = \App\Helpers\NepaliContentHelper::convertAdToBs($date ?? '');
        return response()->json(['bs' => $bs]);
    })->name('util.convert.ad-to-bs');

    Route::post('/bs-to-ad', function (\Illuminate\Http\Request $request) {
        $date = $request->input('date');
        $ad = \App\Helpers\NepaliContentHelper::convertBsToAd($date ?? '');
        return response()->json(['ad' => $ad]);
    })->name('util.convert.bs-to-ad');
});

Route::middleware(['auth', 'verified', 'role:admin'])->get('students/print-list', [StudentController::class, 'printList'])->name('students.print-list');
Route::middleware(['auth', 'verified', 'role:admin'])->post('students/{id}/move-to-alumni', [StudentController::class, 'moveToAlumni'])->name('students.moveToAlumni');
Route::middleware(['auth', 'verified', 'role:admin'])->get('teachers/print-list', [\App\Http\Controllers\Admin\TeacherController::class, 'printList'])->name('teachers.print-list');
Route::middleware(['auth', 'verified', 'role:admin'])->get('parents/print-list', [\App\Http\Controllers\Admin\ParentController::class, 'printList'])->name('parents.print-list');
Route::middleware(['auth', 'verified', 'role:admin'])->get('courses/print-list', [\App\Http\Controllers\Admin\CourseController::class, 'printList'])->name('courses.print-list');
Route::get('alumni-students/print-list', [StudentController::class, 'printAlumniList'])->name('alumni-students.print-list');

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard - using DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semester Management (NEW)
    Route::get('/semesters', [\App\Http\Controllers\Admin\SemesterController::class, 'index'])->name('semesters');
    Route::post('/semesters', [\App\Http\Controllers\Admin\SemesterController::class, 'store'])->name('semesters.store');
    Route::get('/semesters/{id}', [\App\Http\Controllers\Admin\SemesterController::class, 'show'])->name('semesters.show');
    Route::put('/semesters/{id}', [\App\Http\Controllers\Admin\SemesterController::class, 'update'])->name('semesters.update');
    Route::post('/semesters/{id}/toggle', [\App\Http\Controllers\Admin\SemesterController::class, 'toggle'])->name('semesters.toggle');
    Route::post('/semesters/{id}/set-active', [\App\Http\Controllers\Admin\SemesterController::class, 'setActive'])->name('semesters.setActive');
    Route::delete('/semesters/{id}', [\App\Http\Controllers\Admin\SemesterController::class, 'destroy'])->name('semesters.destroy');

    // Elective Management (NEW)
    Route::get('/electives', [\App\Http\Controllers\Admin\ElectiveController::class, 'index'])->name('electives');
    Route::post('/electives/assign', [\App\Http\Controllers\Admin\ElectiveController::class, 'assign'])->name('electives.assign');
    Route::post('/electives/{id}/approve', [\App\Http\Controllers\Admin\ElectiveController::class, 'approve'])->name('electives.approve');
    Route::post('/electives/{id}/reject', [\App\Http\Controllers\Admin\ElectiveController::class, 'reject'])->name('electives.reject');
    Route::post('/electives/{id}/withdraw', [\App\Http\Controllers\Admin\ElectiveController::class, 'withdraw'])->name('electives.withdraw');
    Route::post('/electives/toggle-enrollment', [\App\Http\Controllers\Admin\ElectiveController::class, 'toggleEnrollment'])->name('electives.toggle');
    Route::get('/electives/student/{studentId}', [\App\Http\Controllers\Admin\ElectiveController::class, 'studentElectives'])->name('electives.student');
    Route::delete('/electives/{id}', [\App\Http\Controllers\Admin\ElectiveController::class, 'destroy'])->name('electives.destroy');

    // Timetable Management (ENHANCED)
    Route::get('/timetable', [\App\Http\Controllers\Admin\TimetableController::class, 'index'])->name('timetable');
    Route::post('/timetable', [\App\Http\Controllers\Admin\TimetableController::class, 'store'])->name('timetable.store');
    Route::get('/timetable/{id}', [\App\Http\Controllers\Admin\TimetableController::class, 'show'])->name('timetable.show');
    Route::put('/timetable/{id}', [\App\Http\Controllers\Admin\TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('/timetable/{id}', [\App\Http\Controllers\Admin\TimetableController::class, 'destroy'])->name('timetable.destroy');
    Route::post('/timetable/{id}/toggle', [\App\Http\Controllers\Admin\TimetableController::class, 'toggle'])->name('timetable.toggle');
    Route::post('/timetable/{id}/lock', [\App\Http\Controllers\Admin\TimetableController::class, 'lock'])->name('timetable.lock');
    Route::get('/timetable/data/by-semester', [\App\Http\Controllers\Admin\TimetableController::class, 'getBySemester'])->name('timetable.bySemester');
    Route::get('/timetable/conflicts', [\App\Http\Controllers\Admin\TimetableController::class, 'getConflicts'])->name('timetable.conflicts');
    Route::get('/timetable/print/{semester}', [\App\Http\Controllers\Admin\TimetableController::class, 'printTimetable'])->name('timetable.print');
    Route::get('/timetable/export/pdf', [\App\Http\Controllers\Admin\TimetableController::class, 'exportPdf'])->name('timetable.exportPdf');
    Route::get('/timetable/export/excel', [\App\Http\Controllers\Admin\TimetableController::class, 'exportExcel'])->name('timetable.exportExcel');
    Route::post('/timetable/bulk/lock', [\App\Http\Controllers\Admin\TimetableController::class, 'bulkLock'])->name('timetable.bulkLock');
    Route::post('/timetable/bulk/unlock', [\App\Http\Controllers\Admin\TimetableController::class, 'bulkUnlock'])->name('timetable.bulkUnlock');
    // AJAX route for attendance chart data
    Route::get('/dashboard/attendance-data', [DashboardController::class, 'attendanceData'])->name('dashboard.attendance');

    // Export students CSV for current filters or selected ids (MUST be BEFORE resource route)
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    // Bulk actions (MUST be BEFORE resource route)
    Route::post('students/bulk', [StudentController::class, 'bulk'])->name('students.bulk');

    // Alumni Students route (MUST be BEFORE resource route)
    Route::get('alumni-students', [StudentController::class, 'alumni'])->name('alumni-students');
    Route::post('alumni-students/bulk', [StudentController::class, 'bulk'])->name('alumni-students.bulk');
    Route::get('alumni-students/export', [StudentController::class, 'export'])->name('alumni-students.export');

    // Students resource (index, show, store, edit, destroy)
    Route::resource('students', StudentController::class)->except(['create'])->names([
        'index' => 'students'
    ]);

    // Toggle student active/inactive
    Route::post('students/{id}/toggle', [StudentController::class, 'toggle'])->name('students.toggle');
    // Toggle alumni flag
    Route::post('students/{id}/toggle-alumni', [StudentController::class, 'toggleAlumni'])->name('students.toggleAlumni');
    // Printable view
    Route::middleware(['auth', 'verified', 'role:admin'])->get('students/{id}/print', [StudentController::class, 'print'])->name('students.print');
    Route::middleware(['auth', 'verified', 'role:admin'])->get('students/{id}/print-detail', [StudentController::class, 'printDetail'])->name('students.print-detail');
    // JSON endpoint for modal (returns student details as JSON)
    Route::middleware(['auth', 'verified'])->get('students/{id}/json', [StudentController::class, 'jsonDetail'])->name('students.json');
    Route::get('students/{id}/download', [StudentController::class, 'download'])->name('students.download');
    Route::get('students/{id}/exam-report', [StudentController::class, 'examReport'])->name('students.exam-report');

    Route::get('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('teachers');
    Route::get('/teachers/export', [\App\Http\Controllers\Admin\TeacherController::class, 'export'])->name('teachers.export');
    Route::post('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::get('teachers/{id}/print', [\App\Http\Controllers\Admin\TeacherController::class, 'print'])->name('teachers.print');
    Route::get('teachers/{id}/download', [\App\Http\Controllers\Admin\TeacherController::class, 'download'])->name('teachers.download');

    // Subject-Teacher Assignment Routes
    Route::prefix('subject-teacher')->name('subject-teacher.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'destroy'])->name('destroy');
        Route::get('/subject/{subjectId}', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'getBySubject'])->name('bySubject');
        Route::get('/teacher/{teacherId}', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'getByTeacher'])->name('byTeacher');
        Route::get('/subject/{subjectId}/available-teachers', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'getAvailableTeachers'])->name('availableTeachers');
        Route::get('/teacher/{teacherId}/available-subjects', [\App\Http\Controllers\Admin\SubjectTeacherController::class, 'getAvailableSubjects'])->name('availableSubjects');
    });

    Route::get('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'index'])->name('parents');
    Route::get('/parents/export', [\App\Http\Controllers\Admin\ParentController::class, 'export'])->name('parents.export');
    Route::get('/parents/students', [\App\Http\Controllers\Admin\ParentController::class, 'getStudents'])->name('parents.students');
    Route::post('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'store'])->name('parents.store');
    Route::get('/parents/{id}/edit', [\App\Http\Controllers\Admin\ParentController::class, 'edit'])->name('parents.edit');
    Route::put('/parents/{id}', [\App\Http\Controllers\Admin\ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{id}', [\App\Http\Controllers\Admin\ParentController::class, 'destroy'])->name('parents.destroy');
    Route::get('parents/{id}/print', [\App\Http\Controllers\Admin\ParentController::class, 'print'])->name('parents.print');
    Route::get('parents/{id}/download', [\App\Http\Controllers\Admin\ParentController::class, 'download'])->name('parents.download');

    Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/attendance/toggle', [\App\Http\Controllers\Admin\AttendanceController::class, 'toggle'])->name('attendance.toggle');
    Route::post('/attendance/bulk-update', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkUpdate'])->name('attendance.bulk');
    Route::get('/attendance/students', [\App\Http\Controllers\Admin\AttendanceController::class, 'getStudentsForAttendance'])->name('attendance.students');
    // New route for viewing subject attendance (by date and subject)
    Route::get('/attendance/subject-students', [\App\Http\Controllers\Admin\AttendanceController::class, 'getSubjectStudentsForAttendance'])->name('attendance.subject-students');
    Route::post('/attendance/delete', [\App\Http\Controllers\Admin\AttendanceController::class, 'delete'])->name('attendance.delete');
    Route::post('/attendance/bulk-delete', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkDelete'])->name('attendance.bulk-delete');
    
    // Print attendance list
    Route::get('/attendance/print', [\App\Http\Controllers\Admin\AttendanceController::class, 'printAttendance'])->name('attendance.print');
    Route::get('/attendance/print-list', [\App\Http\Controllers\Admin\AttendanceController::class, 'printList'])->name('attendance.print-list');
    
    // Get subjects by semester for attendance
    Route::get('/attendance/subjects/by-semester', [\App\Http\Controllers\Admin\AttendanceController::class, 'getSubjectsBySemester'])->name('attendance.subjects');
    Route::get('/attendance/student/{id}/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'studentReport'])->name('attendance.student');
    Route::get('/attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('attendance.export');

    Route::get('/exam', [App\Http\Controllers\Admin\ExamController::class, 'index'])->name('exam');
    Route::post('/exam', [App\Http\Controllers\Admin\ExamController::class, 'store'])->name('exam.store');
    Route::get('/exam/data', [App\Http\Controllers\Admin\ExamController::class, 'index'])->name('exam.data');
    
    // Specific routes MUST come before generic routes
    Route::get('/exam/available-bs-years', [App\Http\Controllers\Admin\ExamController::class, 'getAvailableBSYears'])->name('exam.available-bs-years');
    Route::get('/exam/available-years-semesters', [App\Http\Controllers\Admin\ExamController::class, 'getAvailableAcademicYearsAndSemesters'])->name('exam.available-years-semesters');
    Route::get('/exam/subjects/by-semester', [App\Http\Controllers\Admin\ExamController::class, 'getSubjectsBySemester'])->name('exam.subjects');
    Route::get('/exam/all-subjects', [App\Http\Controllers\Admin\ExamController::class, 'getAllSubjects'])->name('exam.all-subjects');
    Route::get('/exam/{exam}/edit-data', [App\Http\Controllers\Admin\ExamController::class, 'getEditExamData'])->name('exam.edit-data');
    
    // Generic routes come last
    Route::put('/exam/{exam}', [App\Http\Controllers\Admin\ExamController::class, 'update'])->name('exam.update');
    Route::get('/exam/assessment-numbers', [App\Http\Controllers\Admin\ExamController::class, 'getAssessmentNumbers'])->name('exam.assessment-numbers');
    Route::get('/exam/{exam}', [App\Http\Controllers\Admin\ExamController::class, 'show'])->name('exam.show');
    Route::delete('/exam/{exam}', [App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('exam.destroy');
    Route::post('/exam/{exam}/toggle-status', [App\Http\Controllers\Admin\ExamController::class, 'toggleStatus'])->name('exam.toggle');
    Route::post('/exam/{exam}/upload-marks', [App\Http\Controllers\Admin\ExamController::class, 'uploadMarks'])->name('exam.upload-marks');
    Route::post('/exam/{exam}/upload-marks-ajax', [App\Http\Controllers\Admin\ExamController::class, 'uploadMarksAjax'])->name('exam.upload-marks-ajax');
    Route::delete('/exam/marks/{mark}', [App\Http\Controllers\Admin\ExamController::class, 'deleteMark'])->name('exam.mark.delete');
    Route::get('/exam/{exam}/students', [App\Http\Controllers\Admin\ExamController::class, 'getStudentsForExam'])->name('exam.students');
    Route::get('/exam/{exam}/students-with-marks', [App\Http\Controllers\Admin\ExamController::class, 'getStudentsWithMarks'])->name('exam.students-with-marks');
    Route::get('/exam/{exam}/subjects', [App\Http\Controllers\Admin\ExamController::class, 'getSubjectsForExam'])->name('exam.subjects.exam');
    Route::get('/exam/{exam}/subject-marks/{subjectId}', [App\Http\Controllers\Admin\ExamController::class, 'getSubjectMarks'])->name('exam.subject-marks');
    
    // Mark edit routes
    Route::get('/exam/marks/{mark}/edit', [App\Http\Controllers\Admin\ExamController::class, 'getMarkData'])->name('exam.marks.edit');
    Route::put('/exam/marks/{mark}', [App\Http\Controllers\Admin\ExamController::class, 'updateMark'])->name('exam.marks.update');

    // Date conversion route
    Route::post('/exam/convert-date', [App\Http\Controllers\Admin\ExamController::class, 'convertDate'])->name('exam.convert-date');

    // Conversion helper endpoints for AD<->BS using server-side calendar data
    Route::post('/convert/ad-to-bs', function (\Illuminate\Http\Request $request) {
        $date = $request->input('date');
        $bs = \App\Helpers\NepaliContentHelper::convertAdToBs($date ?? '');
        return response()->json(['bs' => $bs]);
    })->name('convert.ad-to-bs');

    Route::post('/convert/bs-to-ad', function (\Illuminate\Http\Request $request) {
        $date = $request->input('date');
        $ad = \App\Helpers\NepaliContentHelper::convertBsToAd($date ?? '');
        return response()->json(['ad' => $ad]);
    })->name('convert.bs-to-ad');

    Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses');
    Route::post('/courses', [App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/view', [App\Http\Controllers\Admin\CourseController::class, 'showView'])->name('courses.view');
    Route::get('/courses/{id}/edit', [App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::patch('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.patch');
    Route::delete('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/teachers', [App\Http\Controllers\Admin\CourseController::class, 'getTeachers'])->name('courses.teachers');
    Route::get('/courses/teachers/{id}', [App\Http\Controllers\Admin\CourseController::class, 'getTeacher'])->name('courses.teacher');
    Route::get('/courses/teachers/semester/{semester}', [App\Http\Controllers\Admin\CourseController::class, 'getTeachersBySemester'])->name('courses.teachers.semester');
    Route::get('/courses/teachers/semester/{semester}', [App\Http\Controllers\Admin\CourseController::class, 'getTeachersBySemester'])->name('courses.teachers.bySemester');
    Route::get('/courses/{id}/detail', [App\Http\Controllers\Admin\CourseController::class, 'getDetail'])->name('courses.detail');

    // Marks Search/View Route - using ExamController
    Route::get('/marks', [App\Http\Controllers\Admin\ExamController::class, 'marksIndex'])->name('marks');
    Route::get('/marks/search', [App\Http\Controllers\Admin\ExamController::class, 'searchMarks'])->name('marks.search');
    Route::get('/marks/filter-data', [App\Http\Controllers\Admin\ExamController::class, 'getMarksFilterData'])->name('marks.filter-data');

    // Marksheet Search & Generation Routes
    Route::get('/marksheet/search', [App\Http\Controllers\Admin\ExamController::class, 'marksheetSearch'])->name('marksheet.search');
    Route::get('/marksheet/print', [App\Http\Controllers\Admin\ExamController::class, 'marksheetPrint'])->name('marksheet.print');
    Route::get('/marksheet/export', [App\Http\Controllers\Admin\ExamController::class, 'marksheetExport'])->name('marksheet.export');

    // Dynamic Marks Page (Assessment & CTEVT)
    Route::get('/marks/dynamic', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksIndex'])->name('marks.dynamic');
    Route::get('/marks/dynamic/data', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksData'])->name('marks.dynamic.data');
    Route::get('/marks/dynamic/export/{format}', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksExport'])->name('marks.dynamic.export');
    Route::get('/marks/dynamic/print', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksPrint'])->name('marks.dynamic.print');
    Route::post('/marks/dynamic/clear', [App\Http\Controllers\Admin\ExamController::class, 'clearDynamicMarks'])->name('marks.dynamic.clear');
    Route::get('/marks/dynamic/student/{studentId}', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksStudentDetail'])->name('marks.dynamic.student');
    Route::get('/marks/student/detail', [App\Http\Controllers\Admin\ExamController::class, 'dynamicMarksStudentDetail'])->name('marks.student.detail');

    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/chart-data', [App\Http\Controllers\Admin\ReportController::class, 'chartData'])->name('reports.chartData');
    Route::get('/reports/export-csv', [App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('reports.exportCsv');
    Route::get('/reports/export-pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/print', [App\Http\Controllers\Admin\ReportController::class, 'printReport'])->name('reports.print');

    Route::get('/notifications', [App\Http\Controllers\Admin\DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Admin\DashboardController::class, 'markAsRead'])->name('notifications.markRead');
    Route::get('/notifications/unread-count', [App\Http\Controllers\Admin\DashboardController::class, 'unreadCount'])->name('notifications.unreadCount');

    Route::get('/notice-board', [App\Http\Controllers\Admin\NoticeController::class, 'index'])->name('notice-board');
    Route::post('/notice-board', [App\Http\Controllers\Admin\NoticeController::class, 'store'])->name('notice-board.store');
    Route::put('/notice-board/{id}', [App\Http\Controllers\Admin\NoticeController::class, 'update'])->name('notice-board.update');
    Route::delete('/notice-board/{id}', [App\Http\Controllers\Admin\NoticeController::class, 'destroy'])->name('notice-board.destroy');
    Route::post('/notice-board/toggle-status', [App\Http\Controllers\Admin\NoticeController::class, 'toggleStatus'])->name('notice-board.toggle');
    Route::get('/notice-board/{id}', [App\Http\Controllers\Admin\NoticeController::class, 'show'])->name('notice-board.show');
    Route::get('/notice-board/subjects/by-semester', [App\Http\Controllers\Admin\NoticeController::class, 'getSubjectsBySemester'])->name('notice-board.subjects');

    // Gallery management
    Route::get('/gallery', [App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('gallery');
    Route::post('/gallery', [App\Http\Controllers\Admin\GalleryController::class, 'store'])->name('gallery.store');
    Route::get('/gallery/{id}', [App\Http\Controllers\Admin\GalleryController::class, 'show'])->name('gallery.show');
    Route::put('/gallery/{id}', [App\Http\Controllers\Admin\GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{id}', [App\Http\Controllers\Admin\GalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::post('/gallery/toggle-status', [App\Http\Controllers\Admin\GalleryController::class, 'toggleStatus'])->name('gallery.toggle');
    Route::get('/gallery/categories', [App\Http\Controllers\Admin\GalleryController::class, 'getCategories'])->name('gallery.categories');


    // Study Material management
    Route::get('/study-material', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'index'])->name('study-material');
    Route::post('/study-material', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'store'])->name('study-material.store');
    Route::post('/study-material/store-ajax', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'storeAjax'])->name('study-material.store-ajax');
    Route::get('/study-material/{id}/row', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'getMaterialRow'])->name('study-material.row');
    Route::put('/study-material/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'update'])->name('study-material.update');
    Route::get('/study-material/download/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'download'])->name('study-material.download');
    Route::delete('/study-material/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'destroy'])->name('study-material.destroy');

    // Audit logs listing and detail
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');

    // Settings management
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');

    // Department details (renamed from "college" settings)
    Route::get('/department', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::post('/department', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/department/logo', [DepartmentController::class, 'deleteLogo'])->name('department.logo.delete');
    Route::delete('/department/hero-images/{index}', [DepartmentController::class, 'deleteHeroImage'])->name('department.hero-image.delete');
});

require __DIR__ . '/auth.php';
