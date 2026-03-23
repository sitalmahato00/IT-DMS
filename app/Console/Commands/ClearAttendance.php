<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Illuminate\Console\Command;

class ClearAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all attendance records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Are you sure you want to delete all attendance records? This action cannot be undone.')) {
            $count = Attendance::count();
            Attendance::truncate();
            $this->info("Deleted {$count} attendance records successfully.");
        } else {
            $this->warn('Operation cancelled.');
        }
    }
}
