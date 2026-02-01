<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Carbon\Carbon;

class AuditLogSeeder extends Seeder
{
    public function run()
    {
        if (!Schema::hasTable('audit_logs')) return;

        $user = User::first();

        $now = Carbon::now();

        $sample = [
            [
                'user_id' => $user ? $user->id : null,
                'action' => 'Created new course "Intro to Programming"',
                'module' => 'Course',
                'timestamp' => $now->subMinutes(5)->toDateTimeString(),
                'created_at' => $now->subMinutes(5)->toDateTimeString(),
                'updated_at' => $now->subMinutes(5)->toDateTimeString(),
            ],
            [
                'user_id' => $user ? $user->id : null,
                'action' => 'Marked attendance for class A - 2026-01-27',
                'module' => 'Attendance',
                'timestamp' => $now->subHours(2)->toDateTimeString(),
                'created_at' => $now->subHours(2)->toDateTimeString(),
                'updated_at' => $now->subHours(2)->toDateTimeString(),
            ],
            [
                'user_id' => $user ? $user->id : null,
                'action' => 'Published notice: Exam Schedule',
                'module' => 'Notice',
                'timestamp' => $now->subDays(1)->toDateTimeString(),
                'created_at' => $now->subDays(1)->toDateTimeString(),
                'updated_at' => $now->subDays(1)->toDateTimeString(),
            ],
            [
                'user_id' => null,
                'action' => 'System migrated attendance ids',
                'module' => 'System',
                'timestamp' => $now->subDays(2)->toDateTimeString(),
                'created_at' => $now->subDays(2)->toDateTimeString(),
                'updated_at' => $now->subDays(2)->toDateTimeString(),
            ],
        ];

        DB::table('audit_logs')->insert($sample);
    }
}
