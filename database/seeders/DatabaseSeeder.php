<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data
            CollegeSeeder::class,
            SemesterSeeder::class,
            SubjectSeeder::class,
            
            // Users and profiles
            UserSeeder::class,
            ParentSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            
            // Relations
            SubjectTeacherSeeder::class,
            SubjectStudentSeeder::class,
            
            // Operational data
            ExamSeeder::class,
            ExamMarkSeeder::class,
            AttendanceSeeder::class,
            StudyMaterialSeeder::class,
            NoticeSeeder::class,
        ]);
    }
}

