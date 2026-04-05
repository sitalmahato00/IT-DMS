<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Clean up broken migration entries that prevent app from starting
     */
    public function up(): void
    {
        // Remove migration entries for files that were deleted
        try {
            DB::table('migrations')->whereIn('migration', [
                '2026_04_03_000001_expand_parent_management_fields',
                '2026_04_01_220000_add_production_performance_indexes',
                '2026_04_04_000000_add_performance_indexes',
                '2026_04_05_consolidate_changes',
                '2026_04_05_reset_migrations_table',
            ])->delete();
            
            \Log::info('Cleaned up broken migration entries');
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup migrations: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // Cannot rollback - deleted entries are already gone
    }
};
