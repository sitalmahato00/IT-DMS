<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\NoticePortalController;
use App\Http\Controllers\GalleryPortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', [NoticePortalController::class, 'index'])->name('home');
Route::get('/notices/fetch', [NoticePortalController::class, 'fetch'])->name('notices.fetch');
Route::get('/notices/{id}', [NoticePortalController::class, 'show'])->name('notices.show');
Route::get('/gallery/fetch', [GalleryPortalController::class, 'fetch'])->name('gallery.fetch');

// Language switcher: sets the locale in the session and redirects back
Route::post('locale', function (Illuminate\Http\Request $request) {
    $locale = $request->input('locale');
    $supported = array_keys(config('locales.supported'));
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard - using DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // AJAX route for attendance chart data
    Route::get('/dashboard/attendance-data', [DashboardController::class, 'attendanceData'])->name('dashboard.attendance');

    // Students resource (index, show, store, edit, destroy)
    Route::resource('students', StudentController::class)->except(['create'])->names([
        'index' => 'students'
    ]);

    // Toggle student active/inactive
    Route::post('students/{id}/toggle', [StudentController::class, 'toggle'])->name('students.toggle');
    // Toggle alumni flag
    Route::post('students/{id}/toggle-alumni', [StudentController::class, 'toggleAlumni'])->name('students.toggleAlumni');
    // Printable view
    Route::get('students/{id}/print', [StudentController::class, 'print'])->name('students.print');

    Route::get('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('teachers');
    Route::post('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('teachers.destroy');

    Route::get('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'index'])->name('parents');
    Route::get('/parents/students', [\App\Http\Controllers\Admin\ParentController::class, 'getStudents'])->name('parents.students');
    Route::post('/parents', [\App\Http\Controllers\Admin\ParentController::class, 'store'])->name('parents.store');
    Route::get('/parents/{id}/edit', [\App\Http\Controllers\Admin\ParentController::class, 'edit'])->name('parents.edit');
    Route::put('/parents/{id}', [\App\Http\Controllers\Admin\ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{id}', [\App\Http\Controllers\Admin\ParentController::class, 'destroy'])->name('parents.destroy');

    Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/attendance/toggle', [\App\Http\Controllers\Admin\AttendanceController::class, 'toggle'])->name('attendance.toggle');
    Route::post('/attendance/bulk-update', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkUpdate'])->name('attendance.bulk');
    Route::get('/attendance/students', [\App\Http\Controllers\Admin\AttendanceController::class, 'getStudentsForAttendance'])->name('attendance.students');
    Route::post('/attendance/delete', [\App\Http\Controllers\Admin\AttendanceController::class, 'delete'])->name('attendance.delete');
    Route::get('/attendance/student/{id}/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'studentReport'])->name('attendance.student');
    Route::get('/attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('attendance.export');

    Route::get('/assessment', function () {
        return view('admin.assessment');
    })->name('assessment');

    Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses');
    Route::post('/courses', [App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::patch('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.patch');
    Route::delete('/courses/{id}', [App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/teachers', [App\Http\Controllers\Admin\CourseController::class, 'getTeachers'])->name('courses.teachers');
    Route::get('/courses/teachers/{id}', [App\Http\Controllers\Admin\CourseController::class, 'getTeacher'])->name('courses.teacher');
    Route::get('/courses/teachers/semester/{semester}', [App\Http\Controllers\Admin\CourseController::class, 'getTeachersBySemester'])->name('courses.teachers.semester');
    Route::get('/courses/teachers/semester/{semester}', [App\Http\Controllers\Admin\CourseController::class, 'getTeachersBySemester'])->name('courses.teachers.bySemester');

    Route::get('/marks', function () {
        return view('admin.marks');
    })->name('marks');

    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    Route::post('/reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generateReport'])->name('reports.generate');

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
    Route::put('/study-material/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'update'])->name('study-material.update');
    Route::get('/study-material/download/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'download'])->name('study-material.download');
    Route::delete('/study-material/{id}', [\App\Http\Controllers\Admin\StudyMaterialController::class, 'destroy'])->name('study-material.destroy');

    // Audit logs listing and detail
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
});

require __DIR__ . '/auth.php';
