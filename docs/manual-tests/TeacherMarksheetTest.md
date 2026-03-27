# Manual Test Case: Teacher Marksheet Generation

## Test Objective
Verify teacher can successfully generate and view individual student marksheets using the search functionality.

## Preconditions
- Teacher logged in
- Test data exists in DB: Student with marks (ExamMark/Mark records), Exams, Subjects assigned to teacher
- Navigate to Teacher Dashboard → Marksheets → Marksheet Search (route: teacher.marksheet.search)

## Test Steps

1. **Navigate to Marksheet Search**
   - Go to `/teacher/marksheets` 
   - Click or find link to individual marksheet search (marks/marksheet-search.blade.php)

2. **Fill Search Form with Test Data**
   | Field | Value | Notes |
   |-------|--------|-------|
   | Academic Year (BS) | `2079` (or any BS year from dropdown) | Select from dropdown |
   | Semester | `3` (or valid semester) | Select from dropdown |
   | Exam Category | `Assessment` | Select from dropdown |
   | Assessment Number | `All` (or `1`) | Select from conditional dropdown (shows when Assessment selected) |
   | Result | `All` | Select from dropdown |
| Student ID / Roll No | `1` (Student ID from DB) or `S001` (Roll No) | Enter exact value. Student 1: email `student1@dit.edu.np` |
| Date of Birth (AD) | Leave **blank** initially (NULL in seeders) | DOB is nullable; test without DOB first. Add DOB via DB if needed: `2003-05-15` |

3. **Submit Form**
   - Click **Search Marksheet** button
   - Verify form submits to `teacher.marksheet.search` with parameters

4. **Verify Student Found & Details Displayed**
   ```
   Expected:
   - Student details section appears
   - Name: [Student Name]
   - Student ID: STU001
   - Roll Number: [Roll No]
   - Semester: Semester 3
   - Academic Year (BS): 2079
   - Date of Birth: 2003-05-15 (from DB ✅)
   ```

5. **Verify Marksheet Data Table**
   ```
   Expected:
   - Table shows subjects with marks (from ExamMark model)
   | S.N. | Subject | Full Marks | Pass Marks | Marks Obtained |
   - Grand Total calculated correctly
   - Failed components highlighted (red background)
   - No marks message if empty
   ```

6. **Test Print Functionality**
   - Click **Print Marksheet**
   - Opens print preview (marks/marksheet-print.blade.php)
   - Verify:
     - Header with department logo/address
     - Student info including DOB from DB
     - Filters summary (Academic Year, Semester, Category)
     - Detailed marks table (Theory/Practical breakdown)
     - Footer with issue date
   - Print document

7. **Test Export**
   - Click **Export CSV**
   - Downloads CSV with marks data

## Negative Test Cases
| Scenario | Steps | Expected Result |
|----------|--------|-----------------|
| Invalid Student ID | Enter non-existent ID | \"No student found\" message |
| Mismatched DOB | Correct ID, wrong DOB | \"No student found\" |
| No filters | Submit empty form | No results or error handling |
| Reset button | Fill form → Reset | Form clears |

## Pass Criteria
- ✅ Student found by ID/Roll + DOB match
- ✅ Marksheet displays correct data from DB
- ✅ Print preview matches expected format
- ✅ All fields functional per UI

## Sample Test Data (Insert to DB if needed)
```sql
-- Student
INSERT INTO students (id, roll_no, date_of_birth, semester, academic_year_bs) VALUES ('STU001', '001', '2003-05-15', 3, '2079');

-- Sample ExamMark
INSERT INTO exam_marks (student_id, exam_id, subject_id, theory_internal_marks, ...) VALUES (...);
```

**Test Status: READY TO EXECUTE**
