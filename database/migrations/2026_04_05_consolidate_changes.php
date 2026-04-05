<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ONLY remove broken migration entries
        // This fixes the 502 error caused by broken migration records
        try {
            DB::table('migrations')->whereIn('migration', [
                '2026_04_03_000001_expand_parent_management_fields',
                '2026_04_01_220000_add_production_performance_indexes',
                '2026_04_04_000000_add_performance_indexes',
            ])->delete();
        } catch (\Exception $e) {
            // Ignore errors - table might not exist yet
        }
    }

    public function down(): void
    {
        // No rollback needed - we only deleted migration entries
    }
};
