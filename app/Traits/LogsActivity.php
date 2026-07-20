<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log activity to audit_logs table
     */
    protected function logActivity($module, $action, $description = null)
    {
        try {
            if (Schema::hasTable('audit_logs')) {
                DB::table('audit_logs')->insert([
                    'module' => $module,
                    'action' => $action,
                    'description' => $description,
                    'user_id' => Auth::id(),
                    'timestamp' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to log activity: ' . $e->getMessage());
        }
    }
}

