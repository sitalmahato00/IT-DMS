# Specific Student Marksheet Test Case

## Test Student Details (Student 1 from Seeders)

**Student ID**: `1`  
**Roll No**: `S001`  
**Full Name**: `Student 1`  
**Login Email**: `student1@dit.edu.np` / Password: `password`  
**Semester**: `2`  
**Academic Year (BS)**: `२०८१/०८२` (2081/082 AD)  
**Date of Birth**: `NULL` (test without, or set: `2003-05-15`)

## Exact Test Steps for Marksheet Check

### 1. Setup (Run Seeders)
```bash
cd 'd:/DIT MMP/5th sem/minor project/IT-DMS'
php artisan migrate:fresh --seed
```

### 2. Start Application
```bash
php artisan serve
```

### 3. Login as Teacher
- Email: `ram@dit.edu.np` / Password: `password`
- Navigate: Dashboard → Marksheets → **Marksheet Search**

### 4. Fill Form EXACTLY:
```
Academic Year (BS): २०८१/०८२ (or 2081/082)
Semester: 2
Exam Category: Assessment  
Assessment Number: All
Result: All
Student ID / Roll No: 1    ← KEY: Use numeric ID!
Date of Birth (AD): (leave blank)
```
**Click: Search Marksheet**

### 5. Expected Results:
```
✅ Student Found!
Student ID: 1
Roll Number: S001  
Semester: 2
Academic Year (BS): २०८१/०८२
Date of Birth: N/A ✅ (matches NULL)

✅ Marksheet Table (ExamMark data):
- Subjects with Assessment 1,2,3 marks (random 0-50)
- Grades: A/B/C/F
- Red highlights for failed components
```

### 6. Print Test:
```
Click "Print Marksheet" → Verify:
- Header: Department logo
- Student info above
- Detailed Theory/Practical breakdown
- Grand Total
```

### 7. Verify in Database:
```sql
-- Check student
SELECT id, roll_no, semester, academic_year_bs, date_of_birth FROM students WHERE id=1;

-- Check marks for this student
SELECT em.*, s.subject_name, e.exam_name 
FROM exam_marks em
JOIN exams e ON em.exam_id = e.id
JOIN subjects s ON em.subject_id = s.id
WHERE em.student_id = 1 AND e.exam_category = 'assessment'
ORDER BY e.assessment_number;
```

## Quick Test Commands:
```bash
# Seed & serve
php artisan migrate:fresh --seed && php artisan serve

# In new terminal (check data)
php artisan tinker
>>> App\Models\Student::find(1)
>>> App\Models\Student::find(1)->examMarks()->whereHas('exam', fn($q)=>$q->where('exam_category','assessment'))->get()
```

**SUCCESS**: Marksheet loads with Student 1 data from seeders. Print works.

**If No DOB Match**: DOB NULL by default - test proves nullable handling works.
