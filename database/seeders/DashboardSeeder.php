<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for dashboard data.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        
        // Create sample users (students, teachers, parents)
        $this->createUsers();
        
        // Create sample subjects (courses)
        $this->createSubjects();
        
        // Create attendance records for current year (spread across months)
        $this->createAttendanceRecords($currentYear);
        
        // Create sample notices
        $this->createNotices();
        
        // Create audit logs for recent activities
        $this->createAuditLogs();
        
        $this->command->info('Dashboard seeders completed successfully!');
    }
    
    protected function createUsers(): void
    {
        // Check if users table exists
        if (!Schema::hasTable('users')) {
            $this->command->warn('Users table does not exist. Skipping user creation.');
            return;
        }
        
        // Check if users already exist
        $existingUsers = DB::table('users')->count();
        if ($existingUsers > 5) {
            $this->command->info('Users already exist. Skipping user creation.');
            return;
        }
        
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@dms.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'created_at' => Carbon::now()->subYears(2),
            'updated_at' => Carbon::now()->subYears(2),
        ]);
        
        // Create teachers
        $teachers = [
            ['name' => 'Dr. John Smith', 'email' => 'john.smith@dms.com', 'role' => 'teacher'],
            ['name' => 'Prof. Jane Doe', 'email' => 'jane.doe@dms.com', 'role' => 'teacher'],
            ['name' => 'Dr. Robert Wilson', 'email' => 'robert.wilson@dms.com', 'role' => 'teacher'],
            ['name' => 'Prof. Sarah Johnson', 'email' => 'sarah.johnson@dms.com', 'role' => 'teacher'],
            ['name' => 'Dr. Michael Brown', 'email' => 'michael.brown@dms.com', 'role' => 'teacher'],
        ];
        
        foreach ($teachers as $teacher) {
            DB::table('users')->insert([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => bcrypt('password'),
                'role' => $teacher['role'],
                'created_at' => Carbon::now()->subMonths(rand(12, 24)),
                'updated_at' => Carbon::now()->subMonths(rand(12, 24)),
            ]);
        }
        
        // Create parents
        $parents = [
            ['name' => 'Mr. James Anderson', 'email' => 'james.anderson@email.com'],
            ['name' => 'Mrs. Emily Thompson', 'email' => 'emily.thompson@email.com'],
            ['name' => 'Mr. David Martinez', 'email' => 'david.martinez@email.com'],
            ['name' => 'Mrs. Lisa Taylor', 'email' => 'lisa.taylor@email.com'],
            ['name' => 'Mr. Christopher Lee', 'email' => 'christopher.lee@email.com'],
        ];
        
        foreach ($parents as $parent) {
            DB::table('users')->insert([
                'name' => $parent['name'],
                'email' => $parent['email'],
                'password' => bcrypt('password'),
                'role' => 'parent',
                'created_at' => Carbon::now()->subMonths(rand(6, 18)),
                'updated_at' => Carbon::now()->subMonths(rand(6, 18)),
            ]);
        }
        
        // Create students (with parent IDs)
        $parentIds = DB::table('users')->where('role', 'parent')->pluck('id')->toArray();
        $studentNames = [
            'Alice Johnson', 'Bob Williams', 'Carol Davis', 'Daniel Miller', 
            'Eva Garcia', 'Frank Rodriguez', 'Grace Martinez', 'Henry Anderson',
            'Ivy Thomas', 'Jack Jackson', 'Kate White', 'Liam Harris',
            'Mia Martin', 'Noah Thompson', 'Olivia Robinson', 'Peter Clark',
            'Quinn Lewis', 'Rachel Walker', 'Sam Hall', 'Tina Young'
        ];
        
        foreach ($studentNames as $index => $name) {
            $parentId = $parentIds[$index % count($parentIds)] ?? null;
            DB::table('users')->insert([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@student.dms.com',
                'password' => bcrypt('password'),
                'role' => 'student',
                'parent_id' => $parentId,
                'semester' => rand(1, 6),
                'created_at' => Carbon::now()->subMonths(rand(1, 12)),
                'updated_at' => Carbon::now()->subMonths(rand(1, 12)),
            ]);
        }
        
        $this->command->info('Created users: 1 admin, 5 teachers, 5 parents, 20 students');
    }
    
    protected function createSubjects(): void
    {
        if (!Schema::hasTable('subjects')) {
            $this->command->warn('Subjects table does not exist. Skipping subject creation.');
            return;
        }
        
        // Check if subjects table has the correct columns
        $hasName = Schema::hasColumn('subjects', 'name');
        $hasSubjectName = Schema::hasColumn('subjects', 'subject_name');
        
        $existingSubjects = DB::table('subjects')->count();
        if ($existingSubjects > 5) {
            $this->command->info('Subjects already exist. Skipping subject creation.');
            return;
        }
        
        $subjects = [
            ['name' => 'Introduction to Programming', 'code' => 'CS101', 'semester' => '1', 'status' => 'active', 'credits' => 4],
            ['name' => 'Data Structures', 'code' => 'CS201', 'semester' => '2', 'status' => 'active', 'credits' => 4],
            ['name' => 'Database Management Systems', 'code' => 'CS301', 'semester' => '3', 'status' => 'active', 'credits' => 4],
            ['name' => 'Web Development', 'code' => 'CS401', 'semester' => '4', 'status' => 'active', 'credits' => 3],
            ['name' => 'Software Engineering', 'code' => 'CS501', 'semester' => '5', 'status' => 'active', 'credits' => 4],
            ['name' => 'Machine Learning', 'code' => 'CS601', 'semester' => '6', 'status' => 'active', 'credits' => 4],
            ['name' => 'Computer Networks', 'code' => 'CS701', 'semester' => '5', 'status' => 'active', 'credits' => 3],
            ['name' => 'Operating Systems', 'code' => 'CS801', 'semester' => '4', 'status' => 'active', 'credits' => 4],
        ];
        
        foreach ($subjects as $subject) {
            $data = [
                'subject_code' => $subject['code'],
                'semester' => $subject['semester'],
                'created_at' => Carbon::now()->subMonths(rand(3, 12)),
                'updated_at' => Carbon::now()->subMonths(rand(3, 12)),
            ];
            
            // Use correct column name based on schema
            if ($hasSubjectName) {
                $data['subject_name'] = $subject['name'];
            } elseif ($hasName) {
                $data['name'] = $subject['name'];
            }
            
            // Add optional columns if they exist
            if (Schema::hasColumn('subjects', 'status')) {
                $data['status'] = $subject['status'];
            }
            if (Schema::hasColumn('subjects', 'credits')) {
                $data['credits'] = $subject['credits'];
            }
            
            DB::table('subjects')->insert($data);
        }
        
        $this->command->info('Created 8 subjects/courses');
    }
    
    protected function createAttendanceRecords($currentYear): void
    {
        if (!Schema::hasTable('attendance')) {
            $this->command->warn('Attendance table does not exist. Skipping attendance creation.');
            return;
        }
        
        // Always create attendance records for demo purposes
        // Clear existing records
        DB::table('attendance')->truncate();
        
        // Get student IDs
        $studentIds = DB::table('users')->where('role', 'student')->pluck('id')->toArray();
        if (empty($studentIds)) {
            $this->command->warn('No students found. Skipping attendance creation.');
            return;
        }
        
        $months = range(1, 12);
        $attendanceStatuses = ['present', 'present', 'present', 'present', 'present', 'absent', 'present', 'present', 'present', 'absent'];
        
        foreach ($months as $month) {
            // Generate 15-25 attendance records per month
            $recordsPerMonth = rand(15, 25);
            
            for ($i = 0; $i < $recordsPerMonth; $i++) {
                $studentId = $studentIds[array_rand($studentIds)];
                $day = rand(1, 28); // Safe for all months
                $status = $attendanceStatuses[array_rand($attendanceStatuses)];
                $createdAt = Carbon::create($currentYear, $month, $day, 9, 0, 0);
                
                DB::table('attendance')->insert([
                    'student_id' => $studentId,
                    'status' => $status,
                    'date' => $createdAt->toDateString(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
        
        $totalRecords = DB::table('attendance')->count();
        $this->command->info("Created {$totalRecords} attendance records for dashboard graphs");
    }
    
    protected function createNotices(): void
    {
        if (!Schema::hasTable('notices')) {
            $this->command->warn('Notices table does not exist. Skipping notice creation.');
            return;
        }
        
        $existingNotices = DB::table('notices')->count();
        if ($existingNotices > 3) {
            $this->command->info('Notices already exist. Skipping notice creation.');
            return;
        }
        
        $notices = [
            [
                'title' => 'Mid-Semester Examinations Schedule',
                'body' => 'The mid-semester examinations will be held from 15th to 25th of next month. Students are advised to check their individual schedules on the portal.',
            ],
            [
                'title' => 'Guest Lecture on AI',
                'body' => 'A guest lecture on "Artificial Intelligence in Modern Computing" will be conducted on Friday at 2:00 PM in the main auditorium.',
            ],
            [
                'title' => 'Holiday Notice',
                'body' => 'The institution will remain closed on Monday on account of National Holiday. Regular classes will resume on Tuesday.',
            ],
            [
                'title' => 'Project Submission Deadline',
                'body' => 'The final date for submitting semester projects has been extended by one week. New deadline is 30th of this month.',
            ],
            [
                'title' => 'Library Book Return',
                'body' => 'All students are requested to return borrowed library books by Friday to avoid late fines.',
            ],
        ];
        
        foreach ($notices as $index => $notice) {
            DB::table('notices')->insert([
                'title' => $notice['title'],
                'body' => $notice['body'],
                'created_at' => Carbon::now()->subDays($index * 3),
                'updated_at' => Carbon::now()->subDays($index * 3),
            ]);
        }
        
        $this->command->info('Created 5 notices');
    }
    
    protected function createAuditLogs(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            $this->command->warn('Audit logs table does not exist. Skipping audit log creation.');
            return;
        }
        
        $existingLogs = DB::table('audit_logs')->count();
        if ($existingLogs > 5) {
            $this->command->info('Audit logs already exist. Skipping audit log creation.');
            return;
        }
        
        // Get admin user ID
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        if (!$adminId) {
            $this->command->warn('No admin user found. Skipping audit log creation.');
            return;
        }
        
        // Get teacher IDs
        $teacherIds = DB::table('users')->where('role', 'teacher')->pluck('id')->toArray();
        
        // Sample audit log activities
        $auditLogs = [
            [
                'action' => 'Published new notice: Mid-Semester Examinations Schedule',
                'module' => 'Notice',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'action' => 'Recorded attendance for 25 students in CS101',
                'module' => 'Attendance',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'action' => 'Created new exam: Mid-Term Examination',
                'module' => 'Exam',
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'action' => 'Uploaded marks for Data Structures Exam',
                'module' => 'Marks',
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'action' => 'Enrolled 5 new students for Spring Semester',
                'module' => 'Student',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'action' => 'Updated student records for Class 6A',
                'module' => 'Student',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'action' => 'Published notice: Guest Lecture on AI',
                'module' => 'Notice',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'action' => 'Recorded attendance for 30 students in CS301',
                'module' => 'Attendance',
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'action' => 'Created new exam: Quiz 1 - Programming Fundamentals',
                'module' => 'Exam',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'action' => 'Uploaded marks for Web Development Exam',
                'module' => 'Marks',
                'created_at' => Carbon::now()->subDays(6),
            ],
        ];
        
        foreach ($auditLogs as $index => $log) {
            // Use admin or randomly select a teacher
            $userId = ($index % 2 === 0) ? $adminId : ($teacherIds[array_rand($teacherIds)] ?? $adminId);
            $timestamp = Carbon::parse($log['created_at']);
            
            DB::table('audit_logs')->insert([
                'user_id' => $userId,
                'action' => $log['action'],
                'module' => $log['module'],
                'timestamp' => $log['created_at'],
                'created_at' => $log['created_at'],
                'updated_at' => $log['created_at'],
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);
        }
        
        $this->command->info('Created ' . count($auditLogs) . ' audit logs for dashboard recent activities');
    }
}

