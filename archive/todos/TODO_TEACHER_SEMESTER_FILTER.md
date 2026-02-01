# TODO: Filter Teachers by Semester in Course Edit Modal

## Task
When selecting a semester in the course edit modal, only show teachers assigned to courses in that semester.

## Implementation Steps

### Step 1: Add Backend Endpoint (CourseController.php)
- [x] Add `getTeachersBySemester($semester)` method
- [x] Query teachers currently teaching in the selected semester

### Step 2: Add Route (routes/web.php)
- [x] Add route: `GET /admin/courses/teachers/semester/{semester}`

### Step 3: Update Frontend (courses.blade.php)
- [x] Add event listener on semester dropdown change
- [x] Fetch teachers for selected semester and populate dropdown
- [x] On edit modal, load teachers based on course's assigned semester
- [x] Include currently assigned teacher even if not in filtered list

### Step 4: Add Reset Filter to All Pages
- [x] Add Reset button to Courses page with `resetCoursesFilter()` function
- [x] Add Reset button to Teachers page with `resetTeachersFilter()` function
- [x] Students page already has Reset link (existing implementation)

## Status: COMPLETED ✅

## Summary of Changes

### Backend (CourseController.php)
- Added `getTeachersBySemester($semester)` method that returns teachers assigned to courses in a specific semester
- The method also includes the current course's assigned teacher even if not in the semester

### Route (routes/web.php)
- Added `GET /admin/courses/teachers/semester/{semester}` route

### Frontend (courses.blade.php)
- Added `loadTeachersBySemester()` function that fetches teachers filtered by semester
- Modified `editCourse()` to load teachers based on the course's assigned semester
- Added Reset button and `resetCoursesFilter()` function to clear all filters

### Frontend (teachers.blade.php)
- Added Reset button to filter section
- Added `resetTeachersFilter()` function to clear all filter inputs and show all teachers
