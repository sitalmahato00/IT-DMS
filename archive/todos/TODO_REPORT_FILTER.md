# TODO: Report Filter and Reset Button

## Task
Add filter and reset buttons to the reports page.

## Steps Completed
- [x] 1. Replace "Generate Report" button with "Filter" button
- [x] 2. Add Reset button next to Filter button
- [x] 3. Update JavaScript for Filter and Reset functionality
- [x] 4. Move Filter and Reset buttons to same row as filter dropdowns

## File Edited
- `resources/views/admin/reports.blade.php`

## Changes Made

### Filter Form Section:
- Grid layout changed from 4 columns to 5 columns
- Semester dropdown
- Course dropdown
- Report Type dropdown
- Filter button (red) - submits the form
- Reset button (gray) - clears all filters and reloads default data

### JavaScript Changes:
- Filter button: Now a form submit button (no JS needed)
- Reset button: Clears all select dropdowns and submits the form to reload with default data


