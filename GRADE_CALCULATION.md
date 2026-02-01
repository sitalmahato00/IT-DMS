# Grade Calculation System

## Overview
The DMS Dashboard now displays student grade distribution in a pie chart. The grades are calculated based on the percentage of marks obtained in all assessments.

## Grade Calculation Formula

For each mark record, the grade is determined by the following formula:

```
Grade Percentage = (marks_obtained / full_marks) × 100
```

## Grade Brackets

| Grade | Percentage Range | Color |
|-------|------------------|-------|
| **A** | ≥ 90% | Green |
| **B** | 80% - 89% | Blue |
| **C** | 70% - 79% | Orange |
| **D** | 60% - 69% | Red |
| **F** | < 60% | Dark Red |

## Distribution Calculation

The pie chart shows the percentage distribution of students across each grade:

1. **Count students in each grade bracket**
   - Total marks records where `marks_obtained / full_marks * 100 >= 90` → A Grade
   - Total marks records where `80 <= marks_obtained / full_marks * 100 < 90` → B Grade
   - Total marks records where `70 <= marks_obtained / full_marks * 100 < 80` → C Grade
   - Total marks records where `60 <= marks_obtained / full_marks * 100 < 70` → D Grade
   - Total marks records where `marks_obtained / full_marks * 100 < 60` → F Grade

2. **Calculate percentage for each grade**
   ```
   Grade % = (Count in Grade / Total Marks Records) × 100
   ```

## Example Calculation

If you have the following marks:
- Student A: 92/100 marks → 92% → **A Grade**
- Student B: 85/100 marks → 85% → **B Grade**
- Student C: 75/100 marks → 75% → **C Grade**
- Student D: 55/100 marks → 55% → **F Grade**

**Distribution:**
- A Grade: 1/4 = 25%
- B Grade: 1/4 = 25%
- C Grade: 1/4 = 25%
- F Grade: 1/4 = 25%
- D Grade: 0/4 = 0%

## Database Query

The calculation is done using the following SQL logic:

```sql
-- Count A Grade (>= 90%)
SELECT COUNT(*) FROM marks 
WHERE (marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 90

-- Count B Grade (80-89%)
SELECT COUNT(*) FROM marks 
WHERE (marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 80 
  AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 90

-- Count C Grade (70-79%)
SELECT COUNT(*) FROM marks 
WHERE (marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 70 
  AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 80

-- Count D Grade (60-69%)
SELECT COUNT(*) FROM marks 
WHERE (marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 60 
  AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 70

-- Count F Grade (< 60%)
SELECT COUNT(*) FROM marks 
WHERE (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 60
```

## Implementation Details

- **File**: `app/Http/Controllers/Admin/DashboardController.php`
- **Method**: `getGradeDistribution()`
- **Display**: Dashboard Pie Chart (Grade Distribution)
- **Default Values**: If no marks exist, default percentages are used (A: 28%, B: 35%, C: 22%, D: 10%, F: 5%)

## Related Files
- Dashboard View: `resources/views/admin/dashboard.blade.php`
- Mark Model: `app/Models/Mark.php` (has `getPercentageAttribute()` method)
- Database Table: `marks`
