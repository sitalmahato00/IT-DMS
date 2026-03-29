# Student Timetable Module - Implementation Plan

## Overview

Implement a read-only timetable view for students that queries existing `timetable_slots` data (created by admins) filtered by the student's enrolled subjects (`subject_students` pivot) and semester. No new database tables are needed since the data layer already exists.

---

## Architecture Summary

```
Admin creates timetable_slots
        |
        v
timetable_slots.subject_id -> subject_students.subject_id -> Student
timetable_slots.semester   -> Student.semester (filter)
        |
        v
Student views filtered timetable (read-only)
```

**Key insight:** The `subject_students` pivot table links students to subjects. The `timetable_slots` table links subjects to time/room/teacher. The student's timetable is simply the `timetable_slots` records for subjects the student is enrolled in.

---

## Files to Create/Modify

### 1. NEW: Controller
**File:** `app/Http/Controllers/Student/StudentTimetableController.php`

Controller with single `index()` method that:
1. Gets the student from `auth()->user()->student`
2. Queries enrolled subject IDs via `$student->subjects()->pluck('subjects.id')`
3. Queries `TimetableSlot::whereIn('subject_id', $enrolledSubjectIds)->where('is_active', true)->where('is_holiday', false)`
4. Applies semester and section filters from request params
5. Eager loads `subject` and `teacher.user` relationships
6. Groups slots by `day_of_week`
7. Passes data to `student.timetable.index` view

**Pattern to follow:** `TeacherTimetableController` (128 lines) but filtering by `subject_students` pivot instead of `subject_teacher`.

### 2. NEW: View
**File:** `resources/views/student/timetable/index.blade.php`

Structure:
- `@extends('student.layouts.studentlayout')`
- Welcome banner (gradient header matching `student/attendance/index.blade.php`)
- Stats cards row: Total Subjects, Total Slots, Active Days, Current Semester
- Filter bar: Semester dropdown + Section dropdown + Filter/Reset buttons
- Day-by-day timetable sections (7 sections, one per day):
  - Day header with class count
  - Table: Time | Subject (name + code) | Teacher | Room | Slot Type badge
  - Empty state per day if no classes
- Global empty state when no slots exist

**Design:** Follow `teacher/timetable.blade.php` layout + `student/attendance/index.blade.php` styling (red accent, Tailwind, Bootstrap Icons).

### 3. MODIFY: Sidebar
**File:** `resources/views/student/components/studentsidebar.blade.php`

Changes:
- Line 4: Add `$isTimetable = request()->routeIs('student.timetable*');`
- Line 10: Add `$isTimetable => 'schedule'` to the `$activeGroup` match
- Lines 72-79: Replace the disabled "Timetable" placeholder (with "Soon" badge) with an active `<a>` link to `route('student.timetable')` with active state styling

### 4. MODIFY: Routes
**File:** `routes/web.php`

Add inside the student route group (around line 61, after marks routes):
```php
// Timetable
Route::get('/timetable', [\App\Http\Controllers\Student\StudentTimetableController::class, 'index'])->name('timetable');
```

---

## Step-by-Step Implementation Order

### Step 1: Create Controller
- Create `app/Http/Controllers/Student/StudentTimetableController.php`
- Implement `index()` method following `TeacherTimetableController` pattern
- Key difference: filter by `subject_students` pivot instead of `subject_teacher`

### Step 2: Add Route
- Add single GET route in `routes/web.php` inside the student middleware group
- Route: `GET /student/timetable` -> `student.timetable`

### Step 3: Create View Directory & Template
- Create `resources/views/student/timetable/` directory
- Create `index.blade.php` following teacher timetable + student attendance patterns
- Include: welcome banner, stats cards, filters, day-by-day tables, empty state

### Step 4: Update Sidebar
- Replace "Soon" placeholder with active link in `studentsidebar.blade.php`
- Add `$isTimetable` variable and update `$activeGroup` match

### Step 5: Test & Verify
- Run `php artisan route:list --name=student.timetable` to verify route
- Test in browser as a student user
- Verify filters work (semester, section)
- Verify empty state when no timetable exists

---

## Data Flow (Detailed)

```
1. Student logs in -> GET /student/timetable

2. StudentTimetableController@index:
   a. auth()->user()->student -> get Student model
   b. $student->subjects() -> query subject_students pivot
      SELECT subject_id FROM subject_students WHERE student_id = ?
   c. TimetableSlot query:
      SELECT timetable_slots.*
      FROM timetable_slots
      WHERE subject_id IN (enrolled subject IDs)
        AND semester = ? (student's semester or filter)
        AND is_active = true
        AND is_holiday = false
        AND (section IS NULL OR section = ?) -- if section filter
      ORDER BY day_of_week, start_time
   d. Eager load: subject (name, code), teacher.user (name)
   e. Group slots by day_of_week

3. Pass to view:
   - $timetableByDay (7 arrays, one per day)
   - $semesters (available filter options)
   - $sections (available filter options)
   - $selectedSemester, $selectedSection
   - $totalSlots, $totalSubjects
   - $student (for display context)
```

---

## Role-Based Access Control

- Route protected by middleware: `['auth', 'verified', 'role:student']`
- Controller queries only subjects the student is enrolled in via `subject_students` pivot
- Students can ONLY see slots for subjects they are enrolled in
- No write/modify endpoints - strictly read-only
- Locked timetable slots are still visible (locking prevents admin edits, not student views)

---

## Conflict Handling

Students see data from a single source (`timetable_slots` table populated by admin), so there are no cross-module conflicts. If admin creates overlapping slots for different subjects the student is enrolled in, both will show in the timetable. This is acceptable since the admin's conflict detection already prevents teacher/room conflicts.

---

## UI/UX Design Considerations

1. **Today's Day Highlight:** Auto-scroll to or highlight the current day of the week
2. **Responsive Tables:** Use `overflow-x-auto` on table wrappers for mobile
3. **Color Coding:**
   - Slot types: blue (theory), green (practical), amber (tutorial), purple (elective)
   - Lab groups: rose/orange/emerald/etc. (reuse `lab_group_color` accessor from TimetableSlot)
4. **Quick Stats:** 4 summary cards at top (enrolled subjects, total slots, active days, current semester)
5. **Print Support:** Add a print button using `window.print()` like other pages
6. **Empty States:** Friendly messages when no slots exist or student not enrolled in any subjects

---

## Testing Scenarios

| # | Scenario | Expected Result |
|---|----------|-----------------|
| 1 | Student enrolled in subjects with active timetable slots | Shows weekly timetable with all slots |
| 2 | Student with no enrolled subjects | Empty state message |
| 3 | Student enrolled but no timetable slots exist | Empty state: "Timetable will appear once assigned" |
| 4 | Filter by semester | Only slots for that semester shown |
| 5 | Filter by section | Only slots for that section shown |
| 6 | Slot with lab_group | Shows correct lab group badge + color |
| 7 | Holiday slot | Not shown (filtered by is_holiday=false) |
| 8 | Inactive slot | Not shown (filtered by is_active=true) |
| 9 | Teacher not assigned to slot | Shows "TBA" for teacher name |
| 10 | Mobile viewport | Tables scroll horizontally, layout responsive |

---

## Summary of Changes

| Action | File | Description |
|--------|------|-------------|
| CREATE | `app/Http/Controllers/Student/StudentTimetableController.php` | Controller with `index()` method |
| CREATE | `resources/views/student/timetable/index.blade.php` | Timetable view template |
| MODIFY | `resources/views/student/components/studentsidebar.blade.php` | Activate timetable link |
| MODIFY | `routes/web.php` | Add student timetable route |

No new migrations needed. No new models needed. Existing `TimetableSlot` model and `subject_students` pivot table provide all data required.
