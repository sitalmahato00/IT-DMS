<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add critical indexes for performance optimization
        // Based on actual column names in the schema

        // Students table - for parent lookups (critical for parent portal)
        if (Schema::hasColumn('students', 'parent_id')) {
            Schema::table('students', function (Blueprint $table) {
                // Parent ID is used frequently
                if (!$this->hasIndex('students', 'idx_parent_id')) {
                    $table->index('parent_id', 'idx_parent_id');
                }
            });
        }

        // Exams table - for status and type lookups  
        if (Schema::hasColumn('exams', 'status')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!$this->hasIndex('exams', 'idx_status')) {
                    $table->index('status', 'idx_status');
                }
            });
        }

        // Attendance table - critical for attendance queries
        if (Schema::hasColumn('attendance', 'student_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                if (!$this->hasIndex('attendance', 'idx_student_id')) {
                    $table->index('student_id', 'idx_student_id');
                }
                // For batch attendance percentage queries
                if (Schema::hasColumn('attendance', 'date') && !$this->hasIndex('attendance', 'idx_student_date')) {
                    $table->index(['student_id', 'date'], 'idx_student_date');
                }
            });
        }

        // Exam marks table - for results lookups
        if (Schema::hasColumn('exam_marks', 'exam_id')) {
            Schema::table('exam_marks', function (Blueprint $table) {
                if (!$this->hasIndex('exam_marks', 'idx_exam_id')) {
                    $table->index('exam_id', 'idx_exam_id');
                }
            });
        }

        // Sessions table - for Redis fallback session management
        if (Schema::hasTable('sessions')) {
            if (Schema::hasColumn('sessions', 'user_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    if (!$this->hasIndex('sessions', 'idx_user_id')) {
                        $table->index('user_id', 'idx_user_id');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_parent_id');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_status');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_student_id');
            $table->dropIndexIfExists('idx_student_date');
        });

        Schema::table('exam_marks', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_exam_id');
        });

        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_user_id');
            });
        }
    }

    private function hasIndex($table, $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
