<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Seeders are executed in dependency order
     */
    public function run(): void
    {
        // Step 1: System Configuration (No dependencies)
        $this->call([
            ErpSettingSeeder::class,
            DepartmentSeeder::class,
            SemesterSeeder::class,
        ]);

        // Step 2: Create all users with their roles
        $this->call([
            UserSeeder::class,
        ]);

        // Step 3: User details  (depends on Users)
        $this->call([
            UserDetailSeeder::class,
        ]);

        // Step 4: User profiles (depends on Users)
        $this->call([
            TeacherSeeder::class,
            StudentParentSeeder::class,
            StudentSeeder::class,
        ]);

        // Step 5: Academic structure (Independent)
        $this->call([
            SubjectSeeder::class,
        ]);

        // Step 6: Teacher-Subject mapping (depends on Teachers & Subjects)
        $this->call([
            SubjectTeacherSeeder::class,
        ]);

        // Step 7: Timetable (depends on Subjects & Teachers)
        $this->call([
            TimetableSlotSeeder::class,
        ]);

        // Step 8: Exams (depends on Subjects)
        $this->call([
            ExamSeeder::class,
        ]);

        // Step 9: Exam marks (depends on Exams, Students, Subjects)
        $this->call([
            ExamMarkSeeder::class,
        ]);

        // Step 10: General marks (depends on Students, Subjects, Teachers)
        $this->call([
            MarkSeeder::class,
        ]);

        // Step 11: Attendance (depends on Students, Teachers, Subjects)
        $this->call([
            AttendanceSeeder::class,
        ]);

        // Step 12: Elective enrollments (depends on Students, Subjects)
        $this->call([
            ElectiveEnrollmentSeeder::class,
        ]);

        // Step 13: Study materials (depends on Subjects, Teachers)
        $this->call([
            StudyMaterialSeeder::class,
        ]);

        // Step 14: Content management (mostly independent)
        $this->call([
            NoticeSeeder::class,
            BilingualNoticeSeeder::class,
            GallerySeeder::class,
        ]);

        // Step 15: System audit (depends on Users)
        $this->call([
            AuditLogSeeder::class,
        ]);
    }
}
