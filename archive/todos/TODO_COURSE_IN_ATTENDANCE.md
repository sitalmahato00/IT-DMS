# TODO: Add Course to Attendance List of Student

## Objective
Add course/subject information to the attendance list so students' attendance can be tracked per course.

## Changes Required

### 1. Update Attendance Model
- [x] Add `subject_id` to `$fillable` array
- [x] Add `subject()` relationship method

### 2. Update Attendance View
- [x] Add "Course" column to the attendance records table
- [x] Display course name for each attendance record

### 3. Fix Database Issue
- [x] Fixed status column to accept 'archived' value
- [x] Updated migration to include 'archived' in enum values
- [x] Ran fix script to update existing database

## Status: Completed ✅

