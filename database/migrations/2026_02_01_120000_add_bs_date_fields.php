<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'date_of_birth_bs')) {
                $table->string('date_of_birth_bs', 30)->nullable()->after('date_of_birth');
            }
        });

        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'date_bs')) {
                $table->string('date_bs', 30)->nullable()->after('date');
            }
        });

        Schema::table('notices', function (Blueprint $table) {
            if (!Schema::hasColumn('notices', 'published_at_bs')) {
                $table->string('published_at_bs', 50)->nullable()->after('published_at');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'assessment_date_bs')) {
                $table->string('assessment_date_bs', 30)->nullable()->after('assessment_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'date_of_birth_bs')) {
                $table->dropColumn('date_of_birth_bs');
            }
        });

        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'date_bs')) {
                $table->dropColumn('date_bs');
            }
        });

        Schema::table('notices', function (Blueprint $table) {
            if (Schema::hasColumn('notices', 'published_at_bs')) {
                $table->dropColumn('published_at_bs');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'assessment_date_bs')) {
                $table->dropColumn('assessment_date_bs');
            }
        });
    }
};
