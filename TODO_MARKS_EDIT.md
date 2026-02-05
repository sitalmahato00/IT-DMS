# TODO: Marks Edit Functionality Implementation

## Task Summary
- ✅ Update fail threshold from 35% to 40%
- ✅ Add edit button for each mark row
- ✅ Create modal to edit marks
- ✅ Implement backend update functionality

## Implementation Steps

### Step 1: Update Fail Threshold (35% → 40%)
- ✅ Updated `app/Models/ExamMark.php`:
  - ✅ Changed `isPassed()` method to use 40% threshold
  - ✅ Updated `scopeFailed()` method for new passing threshold

### Step 2: Add Backend Route & Controller Methods
- ✅ Added route in `routes/web.php`:
  - ✅ `GET /admin/assessment/marks/{mark}/edit` - get mark data for modal
  - ✅ `PUT /admin/assessment/marks/{mark}` - update mark
- ✅ Added controller methods in `app/Http/Controllers/Admin/ExamController.php`:
  - ✅ `updateMark(Request $request, ExamMark $mark)` - update single mark
  - ✅ `getMarkData(ExamMark $mark)` - return mark data for edit modal

### Step 3: Add Edit Button to Marks Table
- ✅ Updated `resources/views/admin/exam-show.blade.php`:
  - ✅ Added "Actions" column header in marks table
  - ✅ Added edit button icon for each mark row
  - ✅ Added row ID for DOM updates

### Step 4: Create Edit Mark Modal
- ✅ Added modal HTML in `resources/views/admin/exam-show.blade.php`:
  - ✅ Modal structure with form fields
  - ✅ Student name display (read-only)
  - ✅ Obtained marks input field
  - ✅ Remarks textarea field
  - ✅ Save and Cancel buttons

### Step 5: Add JavaScript Functions
- ✅ Updated script section in `resources/views/admin/exam-show.blade.php`:
  - ✅ `openEditMarkModal(markId)` function
  - ✅ `closeEditMarkModal()` function
  - ✅ `submitEditMark()` AJAX function
  - ✅ Modal close on outside click and ESC key
  - ✅ Dynamic form population with mark data

## Files Modified
1. `app/Models/ExamMark.php`
2. `routes/web.php`
3. `app/Http/Controllers/Admin/ExamController.php`
4. `resources/views/admin/exam-show.blade.php`

