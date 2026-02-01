<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // Add nullable teacher_id if it does not exist
        if (!Schema::hasColumn('users', 'teacher_id')) {
            Schema::table('users', function (Blueprint $table) {
                // avoid `after()` to keep compatibility with SQLite
                $table->string('teacher_id', 50)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'teacher_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('teacher_id');
                });
            } catch (\Throwable $e) {
                // Some drivers (SQLite) may not support dropColumn; ignore rollback failures
            }
        }
    }
};
