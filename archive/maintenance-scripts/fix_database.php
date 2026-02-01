<?php
/**
 * Database Fix Script
 * Fixes attendance table and adds sample users
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Database Fix Script ===\n\n";

try {
    // 1. Create students table if it doesn't exist
    if (!Capsule::schema()->hasTable('students')) {
        echo "Creating students table...\n";
        Capsule::schema()->create('students', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('roll_no')->nullable();
            $table->string('semester')->nullable();
            $table->string('course')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });
    }

    // 2. Create teachers table if it doesn't exist
    if (!Capsule::schema()->hasTable('teachers')) {
        echo "Creating teachers table...\n";
        Capsule::schema()->create('teachers', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('department')->nullable();
            $table->string('qualification')->nullable();
            $table->timestamps();
        });
    }

    // 3. Create attendance table if it doesn't exist
    if (!Capsule::schema()->hasTable('attendance')) {
        echo "Creating attendance table...\n";
        Capsule::schema()->create('attendance', function ($table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->date('date')->nullable();
            $table->string('status')->default('present');
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamps();
        });
    }

    // 4. Create subjects table if it doesn't exist
    if (!Capsule::schema()->hasTable('subjects')) {
        echo "Creating subjects table...\n";
        Capsule::schema()->create('subjects', function ($table) {
            $table->id();
            $table->string('subject_code')->nullable();
            $table->string('subject_name')->nullable();
            $table->string('semester')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    // 5. Create notices table if it doesn't exist
    if (!Capsule::schema()->hasTable('notices')) {
        echo "Creating notices table...\n";
        Capsule::schema()->create('notices', function ($table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    // 6. Check users table
    $userCount = Capsule::table('users')->count();
    echo "Users count: $userCount\n";

    // 7. Create users if none exist
    if ($userCount == 0) {
        echo "Creating admin user...\n";
        Capsule::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@dms.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        echo "Creating teachers...\n";
        $teacherIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $userId = Capsule::table('users')->insertGetId([
                'name' => "Teacher $i",
                'email' => "teacher$i@dms.com",
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'teacher',
                'created_at' => Carbon::now()->subMonths($i),
                'updated_at' => Carbon::now()->subMonths($i),
            ]);
            $teacherIds[] = $userId;

            Capsule::table('teachers')->insert([
                'user_id' => $userId,
                'department' => 'Computer Science',
                'created_at' => Carbon::now()->subMonths($i),
                'updated_at' => Carbon::now()->subMonths($i),
            ]);
        }

        echo "Creating parents...\n";
        $parentIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $userId = Capsule::table('users')->insertGetId([
                'name' => "Parent $i",
                'email' => "parent$i@email.com",
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'parent',
                'created_at' => Carbon::now()->subMonths($i),
                'updated_at' => Carbon::now()->subMonths($i),
            ]);
            $parentIds[] = $userId;
        }

        echo "Creating students...\n";
        $studentNames = [
            'Alice Johnson', 'Bob Williams', 'Carol Davis', 'Daniel Miller',
            'Eva Garcia', 'Frank Rodriguez', 'Grace Martinez', 'Henry Anderson',
            'Ivy Thomas', 'Jack Jackson', 'Kate White', 'Liam Harris',
            'Mia Martin', 'Noah Thompson', 'Olivia Robinson', 'Peter Clark',
            'Quinn Lewis', 'Rachel Walker', 'Sam Hall', 'Tina Young'
        ];

        foreach ($studentNames as $index => $name) {
            $parentId = $parentIds[$index % count($parentIds)] ?? null;
            $userId = Capsule::table('users')->insertGetId([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@student.dms.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'student',
                'parent_id' => $parentId,
                'semester' => rand(1, 6),
                'created_at' => Carbon::now()->subMonths(rand(1, 12)),
                'updated_at' => Carbon::now()->subMonths(rand(1, 12)),
            ]);

            Capsule::table('students')->insert([
                'user_id' => $userId,
                'roll_no' => 'STU' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'semester' => rand(1, 6),
                'parent_id' => $parentId,
                'created_at' => Carbon::now()->subMonths(rand(1, 12)),
                'updated_at' => Carbon::now()->subMonths(rand(1, 12)),
            ]);
        }

        echo "Created 1 admin, 5 teachers, 5 parents, 20 students\n";
    } else {
        echo "Users already exist, checking for students...\n";
        $studentCount = Capsule::table('students')->count();
        echo "Students count: $studentCount\n";

        if ($studentCount == 0) {
            echo "Creating students from users table...\n";
            $studentUsers = Capsule::table('users')->where('role', 'student')->get();
            foreach ($studentUsers as $index => $user) {
                Capsule::table('students')->insert([
                    'user_id' => $user->id,
                    'roll_no' => 'STU' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'semester' => $user->semester ?? rand(1, 6),
                    'parent_id' => $user->parent_id,
                    'created_at' => $user->created_at ?? Carbon::now(),
                    'updated_at' => $user->updated_at ?? Carbon::now(),
                ]);
            }
            echo "Created $studentCount student records\n";
        }
    }

    // 8. Create subjects if none exist
    $subjectCount = Capsule::table('subjects')->count();
    echo "Subjects count: $subjectCount\n";

    if ($subjectCount == 0) {
        echo "Creating subjects...\n";
        $subjects = [
            ['subject_code' => 'CS101', 'subject_name' => 'Introduction to Programming', 'semester' => '1'],
            ['subject_code' => 'CS201', 'subject_name' => 'Data Structures', 'semester' => '2'],
            ['subject_code' => 'CS301', 'subject_name' => 'Database Management Systems', 'semester' => '3'],
            ['subject_code' => 'CS401', 'subject_name' => 'Web Development', 'semester' => '4'],
            ['subject_code' => 'CS501', 'subject_name' => 'Software Engineering', 'semester' => '5'],
            ['subject_code' => 'CS601', 'subject_name' => 'Machine Learning', 'semester' => '6'],
            ['subject_code' => 'CS701', 'subject_name' => 'Computer Networks', 'semester' => '5'],
            ['subject_code' => 'CS801', 'subject_name' => 'Operating Systems', 'semester' => '4'],
        ];

        foreach ($subjects as $subject) {
            Capsule::table('subjects')->insert(array_merge($subject, [
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
        echo "Created " . count($subjects) . " subjects\n";
    }

    // 9. Clear and create attendance records
    echo "Creating attendance records...\n";
    Capsule::table('attendance')->truncate();

    $students = Capsule::table('students')->get();
    $subjects = Capsule::table('subjects')->get();
    $statuses = ['present', 'present', 'present', 'present', 'present', 'absent', 'present', 'absent'];
    $attendanceCount = 0;

    // Create attendance for last 30 days
    for ($daysAgo = 0; $daysAgo < 30; $daysAgo++) {
        $date = Carbon::now()->subDays($daysAgo)->toDateString();

        foreach ($students as $student) {
            // Randomly skip some records to simulate real attendance
            if (rand(1, 3) == 1) continue; // 33% chance of no record for this student on this day

            $status = $statuses[array_rand($statuses)];
            $subject = $subjects->random();

            Capsule::table('attendance')->insert([
                'student_id' => $student->id,
                'date' => $date,
                'status' => $status,
                'remarks' => $status === 'absent' ? 'Absent' : ($status === 'leave' ? 'Leave' : 'Present'),
                'subject_id' => $subject->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $attendanceCount++;
        }
    }

    echo "Created $attendanceCount attendance records\n";

    // 10. Create notices if none exist
    $noticeCount = Capsule::table('notices')->count();
    echo "Notices count: $noticeCount\n";

    if ($noticeCount == 0) {
        echo "Creating notices...\n";
        $notices = [
            ['title' => 'Mid-Semester Examinations', 'body' => 'The mid-semester examinations will be held from 15th to 25th of next month.'],
            ['title' => 'Guest Lecture on AI', 'body' => 'A guest lecture on "Artificial Intelligence in Modern Computing" will be conducted on Friday.'],
            ['title' => 'Holiday Notice', 'body' => 'The institution will remain closed on Monday on account of National Holiday.'],
            ['title' => 'Project Submission Deadline', 'body' => 'The final date for submitting semester projects has been extended by one week.'],
            ['title' => 'Library Book Return', 'body' => 'All students are requested to return borrowed library books by Friday.'],
        ];

        foreach ($notices as $index => $notice) {
            Capsule::table('notices')->insert(array_merge($notice, [
                'created_at' => Carbon::now()->subDays($index * 3),
                'updated_at' => Carbon::now()->subDays($index * 3),
            ]));
        }
        echo "Created " . count($notices) . " notices\n";
    }

    echo "\n=== Database Fix Complete ===\n\n";
    echo "Sample login credentials:\n";
    echo "  Admin: admin@dms.com / password\n";
    echo "  Teachers: teacher1@dms.com / password\n";
    echo "  Students: alice.johnson@student.dms.com / password\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

