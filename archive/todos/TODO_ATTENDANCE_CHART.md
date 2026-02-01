# TODO: Attendance Chart Fix

## Task Summary
Implement month-wise attendance percentage chart with semester filter.

## Changes Required

### 1. Create DashboardController
- [x] Create `app/Http/Controllers/Admin/DashboardController.php`
- [x] Move dashboard logic from routes/web.php to controller
- [x] Add `getAttendancePercentageByMonth($semester = null)` method
- [x] Add `getAttendanceData(Request $request)` for AJAX calls

### 2. Update Routes (routes/web.php)
- [x] Add route for new DashboardController
- [x] Add AJAX route for attendance data: `/admin/dashboard/attendance-data`

### 3. Update Dashboard View (resources/views/admin/dashboard.blade.php)
- [x] Add semester filter dropdown (Semester 1, Semester 2, Whole Year)
- [x] Replace dual line chart with single green line chart
- [x] Update chart canvas ID and data attributes
- [x] Pass initial attendance percentage data

### 4. Update JavaScript (resources/js/admin-dashboard.js)
- [x] Update chart config for single green line "Attendance %"
- [x] Add event listener for semester filter dropdown
- [x] Implement AJAX call to fetch new data on filter change
- [x] Update chart dynamically without page reload

## Implementation Steps Completed

### Step 1: Create DashboardController ✓
Created controller with:
- `index()` method with dashboard logic
- `getAttendancePercentageByMonth($semester = null)` for month-wise data
- `attendanceData(Request $request)` for AJAX responses

### Step 2: Update Routes ✓
Updated routes to use DashboardController and added AJAX endpoint.

### Step 3: Update Dashboard View ✓
- Added semester filter dropdown before chart
- Changed chart from dual line (Present/Absent) to single green line (Attendance %)
- Updated canvas data attributes

### Step 4: Update JavaScript ✓
- Replaced dual line chart with single green line
- Added `initAttendanceChart()` function
- Added `fetchAttendanceData()` for AJAX updates
- Added event listener for filter changes

## Formula for Attendance Percentage
```
attendance_percentage = (present_count / total_count) * 100
```

## Filter Logic
- **Whole Year (default)**: Show all 12 months (Jan-Dec)
- **Semester 1**: Show months Jan-Jun (1-6)
- **Semester 2**: Show months Jul-Dec (7-12)

## Expected Output Format
```json
{
    "success": true,
    "labels": ["Jan", "Feb", "Mar", ...],
    "data": [85, 78, 92, ...],
    "semester": "1" // or "2" or ""
}
```

## Files Modified
1. `app/Http/Controllers/Admin/DashboardController.php` - NEW
2. `routes/web.php` - Updated routes
3. `resources/views/admin/dashboard.blade.php` - Updated chart and filter
4. `resources/js/admin-dashboard.js` - Updated chart configuration

