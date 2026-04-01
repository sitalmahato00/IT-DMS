<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->index(['status', 'department'], 'teachers_status_department_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['parent_id', 'is_alumni', 'status'], 'students_parent_alumni_status_idx');
            $table->index(['semester', 'status', 'is_alumni'], 'students_semester_status_alumni_idx');
            $table->index(['academic_year_bs', 'status'], 'students_year_status_idx');
        });

        Schema::table('study_materials', function (Blueprint $table) {
            $table->index(['visibility', 'is_published', 'created_at'], 'materials_visibility_publish_created_idx');
            $table->index(['visibility', 'document_type', 'is_published'], 'materials_visibility_type_publish_idx');
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->index(['status', 'audience', 'created_at'], 'notices_status_audience_created_idx');
            $table->index(['status', 'is_important', 'published_at_bs'], 'notices_status_priority_bsdate_idx');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->index(['subject_id', 'attendance_type', 'date'], 'attendance_subject_type_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_subject_type_date_idx');
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->dropIndex('notices_status_priority_bsdate_idx');
            $table->dropIndex('notices_status_audience_created_idx');
        });

        Schema::table('study_materials', function (Blueprint $table) {
            $table->dropIndex('materials_visibility_type_publish_idx');
            $table->dropIndex('materials_visibility_publish_created_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_year_status_idx');
            $table->dropIndex('students_semester_status_alumni_idx');
            $table->dropIndex('students_parent_alumni_status_idx');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropIndex('teachers_status_department_idx');
        });
    }
};
