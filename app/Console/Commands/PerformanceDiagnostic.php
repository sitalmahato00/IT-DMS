<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Semester;
use App\Models\User;

class PerformanceDiagnostic extends Command
{
    protected $signature = 'perf:diagnose {--endpoint=semester}';
    protected $description = 'Diagnose performance issues by analyzing SQL queries';

    public function handle()
    {
        $this->info('=== PERFORMANCE DIAGNOSTIC ===');
        $this->line('');

        DB::enableQueryLog();
        
        $endpoint = $this->option('endpoint');
        
        match($endpoint) {
            'semester' => $this->testSemesterController(),
            'dashboard' => $this->testDashboardController(),
            'exam' => $this->testExamController(),
            default => $this->testSemesterController(),
        };

        $this->analyzeQueries();
    }

    private function testSemesterController()
    {
        $this->info('Testing: Semester Controller');
        $this->line(str_repeat('─', 40));

        $start = microtime(true);

        // Get semesters
        $semesters = Semester::orderBy('number')->get();

        // Run the optimized aggregation query
        $counts = DB::table('semesters')
            ->leftJoin('students', function($j) {
                $j->on(DB::raw('CAST(students.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('students.status', 'active')
                  ->where('students.is_alumni', false);
            })
            ->leftJoin('subjects as subjects_all', function($j) {
                $j->on(DB::raw('CAST(subjects_all.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('subjects_all.status', 'active');
            })
            ->leftJoin('subjects as elective_subjects', function($j) {
                $j->on(DB::raw('CAST(elective_subjects.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('elective_subjects.subject_type', 'elective')
                  ->where('elective_subjects.status', 'active');
            })
            ->select(
                'semesters.id',
                'semesters.number',
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('COUNT(DISTINCT subjects_all.id) as subject_count'),
                DB::raw('COUNT(DISTINCT elective_subjects.id) as elective_count')
            )
            ->groupBy('semesters.id', 'semesters.number')
            ->get();

        $elapsed = (microtime(true) - $start) * 1000;

        $this->line("Time: {$elapsed}ms");
        $this->line("Semesters: " . $semesters->count());
    }

    private function testDashboardController()
    {
        $this->info('Testing: Dashboard Controller');
        $this->line(str_repeat('─', 40));

        $start = microtime(true);

        $userCounts = DB::table('users')
            ->selectRaw('
                SUM(CASE WHEN role = "student" THEN 1 ELSE 0 END) as total_students_all,
                SUM(CASE WHEN role = "teacher" THEN 1 ELSE 0 END) as teachers,
                SUM(CASE WHEN role = "parent" THEN 1 ELSE 0 END) as parents
            ')
            ->first();

        $elapsed = (microtime(true) - $start) * 1000;
        $this->line("Time: {$elapsed}ms");
    }

    private function testExamController()
    {
        $this->info('Testing: Exam Controller');
        $this->line(str_repeat('─', 40));

        $start = microtime(true);

        // This would test exam queries
        $exams = DB::table('exams')->limit(10)->get();

        $elapsed = (microtime(true) - $start) * 1000;
        $this->line("Time: {$elapsed}ms");
    }

    private function analyzeQueries()
    {
        $this->line('');
        $this->info('=== QUERY ANALYSIS ===');
        
        $queries = DB::getQueryLog();
        
        $this->line("Total queries: " . count($queries));
        $this->line('');

        $totalTime = 0;
        $slowCount = 0;

        foreach ($queries as $i => $query) {
            $totalTime += $query['time'];
            
            if ($query['time'] > 500) {
                $slowCount++;
                $this->error("Query " . ($i + 1) . " [SLOW]: {$query['time']}ms");
            } else {
                $this->line("Query " . ($i + 1) . ": {$query['time']}ms");
            }

            // Show SQL (truncated)
            $sql = substr($query['query'], 0, 100);
            $this->comment("  → " . $sql . (strlen($query['query']) > 100 ? '...' : ''));
            $this->line('');
        }

        $this->line('');
        $this->info('=== SUMMARY ===');
        $this->line("Slow queries (>500ms): {$slowCount}");
        $this->line("Total time: {$totalTime}ms");
        $this->line("Average per query: " . round($totalTime / max(1, count($queries)), 2) . "ms");

        if ($slowCount === 0 && $totalTime < 1000) {
            $this->line('');
            $this->info('✅ Database queries are fast!');
            $this->line('Slowness likely caused by:');
            $this->line('  • View rendering (Blade templates)');
            $this->line('  • Middleware overhead');
            $this->line('  • Authentication/authorization');
        } elseif ($slowCount > 0) {
            $this->line('');
            $this->warn('⚠️  Database queries are slow!');
            $this->line('Check for:');
            $this->line('  • Missing indexes on WHERE/JOIN columns');
            $this->line('  • Full table scans on large tables');
            $this->line('  • Database server performance');
        }
    }
}

