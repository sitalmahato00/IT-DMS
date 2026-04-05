<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MigrationController extends Controller
{
    /**
     * Run all pending migrations
     * Temporary endpoint for production troubleshooting
     * DELETE THIS FILE AFTER migrations complete
     */
    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Migrations completed',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Check migration status
     */
    public function status()
    {
        try {
            Artisan::call('migrate:status');
            $output = Artisan::output();
            
            return response()->json([
                'status' => 'ok',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
