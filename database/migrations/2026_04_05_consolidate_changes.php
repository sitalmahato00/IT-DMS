<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove broken migration entries
        DB::table('migrations')->whereIn('migration', [
            '2026_04_03_000001_expand_parent_management_fields',
            '2026_04_01_220000_add_production_performance_indexes',
            '2026_04_04_000000_add_performance_indexes',
        ])->delete();

        // Add parent management enhancements
        if (Schema::hasTable('parents')) {
            Schema::table('parents', function (Blueprint $table) {
                // Only add columns if they don't exist
                $columns = DB::connection()->getSchemaBuilder()->getColumnListing('parents');
                
                if (!in_array('national_id_number', $columns)) {
                    $table->string('national_id_number', 100)->nullable()->after('occupation');
                }
                if (!in_array('date_of_birth', $columns)) {
                    $table->date('date_of_birth')->nullable()->after('national_id_number');
                }
                if (!in_array('relationship', $columns)) {
                    $table->string('relationship', 60)->nullable()->after('date_of_birth');
                }
                if (!in_array('blood_group', $columns)) {
                    $table->string('blood_group', 10)->nullable()->after('gender');
                }
                if (!in_array('medical_conditions', $columns)) {
                    $table->text('medical_conditions')->nullable()->after('blood_group');
                }
                if (!in_array('emergency_notes', $columns)) {
                    $table->text('emergency_notes')->nullable()->after('medical_conditions');
                }
            });
        }

        // Add performance indexes (only if they don't exist)
        $this->addIndexIfNotExists('teachers', ['status', 'department'], 'teachers_status_department_idx');
        $this->addIndexIfNotExists('students', ['parent_id', 'is_alumni', 'status'], 'students_parent_alumni_status_idx');
        $this->addIndexIfNotExists('students', ['semester', 'status', 'is_alumni'], 'students_semester_status_alumni_idx');
        $this->addIndexIfNotExists('students', ['academic_year_bs', 'status'], 'students_year_status_idx');
        $this->addIndexIfNotExists('students', 'parent_id', 'idx_parent_id');
        $this->addIndexIfNotExists('exams', 'status', 'idx_status');
        $this->addIndexIfNotExists('attendance', 'student_id', 'idx_student_id');
        $this->addIndexIfNotExists('attendance', ['student_id', 'date'], 'idx_student_date');
        $this->addIndexIfNotExists('exam_marks', 'exam_id', 'idx_exam_id');
        $this->addIndexIfNotExists('study_materials', ['visibility', 'is_published', 'created_at'], 'materials_visibility_publish_created_idx');
        $this->addIndexIfNotExists('study_materials', ['visibility', 'document_type', 'is_published'], 'materials_visibility_type_publish_idx');
        $this->addIndexIfNotExists('notices', ['status', 'audience', 'created_at'], 'notices_status_audience_created_idx');
        $this->addIndexIfNotExists('notices', ['status', 'is_important', 'published_at_bs'], 'notices_status_priority_bsdate_idx');
        $this->addIndexIfNotExists('attendance', ['subject_id', 'attendance_type', 'date'], 'attendance_subject_type_date_idx');
    }

    public function down(): void
    {
        // Drop all added indexes
        $this->dropIndexIfExists('teachers', 'teachers_status_department_idx');
        $this->dropIndexIfExists('students', 'students_parent_alumni_status_idx');
        $this->dropIndexIfExists('students', 'students_semester_status_alumni_idx');
        $this->dropIndexIfExists('students', 'students_year_status_idx');
        $this->dropIndexIfExists('students', 'idx_parent_id');
        $this->dropIndexIfExists('exams', 'idx_status');
        $this->dropIndexIfExists('attendance', 'idx_student_id');
        $this->dropIndexIfExists('attendance', 'idx_student_date');
        $this->dropIndexIfExists('exam_marks', 'idx_exam_id');
        $this->dropIndexIfExists('study_materials', 'materials_visibility_publish_created_idx');
        $this->dropIndexIfExists('study_materials', 'materials_visibility_type_publish_idx');
        $this->dropIndexIfExists('notices', 'notices_status_audience_created_idx');
        $this->dropIndexIfExists('notices', 'notices_status_priority_bsdate_idx');
        $this->dropIndexIfExists('attendance', 'attendance_subject_type_date_idx');

        // Drop parent columns if they exist
        if (Schema::hasTable('parents')) {
            Schema::table('parents', function (Blueprint $table) {
                $columns = DB::connection()->getSchemaBuilder()->getColumnListing('parents');
                $toDrop = array_intersect(['national_id_number', 'date_of_birth', 'relationship', 'blood_group', 'medical_conditions', 'emergency_notes'], $columns);
                
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }
    }

    private function addIndexIfNotExists($table, $columns, $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = is_array($columns) ? $columns : [$columns];
        
        // Check if all columns exist
        $tableColumns = DB::connection()->getSchemaBuilder()->getColumnListing($table);
        foreach ($columns as $column) {
            if (!in_array($column, $tableColumns)) {
                return;
            }
        }

        // Check if index exists
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (count($indexes) > 0) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        // Add the index
        Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
            if (count($columns) === 1) {
                $table->index($columns[0], $indexName);
            } else {
                $table->index($columns, $indexName);
            }
        });
    }

    private function dropIndexIfExists($table, $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (count($indexes) > 0) {
                Schema::table($table, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        } catch (\Exception $e) {
            // Index doesn't exist or error checking, skip
        }
    }
};
