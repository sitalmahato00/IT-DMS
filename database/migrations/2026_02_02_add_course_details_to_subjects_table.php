<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Remove old fields if they exist
            if (Schema::hasColumn('subjects', 'department')) {
                $table->dropColumn('department');
            }
            
            // Add comprehensive course detail fields
            $table->string('category', 100)->nullable()->after('subject_name'); // e.g., Software Engineering
            $table->text('description')->nullable()->after('category'); // Course description
            $table->text('syllabus')->nullable()->after('description'); // Topics covered
            $table->text('learning_objectives')->nullable()->after('syllabus'); // Learning outcomes
            $table->integer('theory_percentage')->default(70)->after('learning_objectives'); // Theory %
            $table->integer('practical_percentage')->default(30)->after('theory_percentage'); // Practical %
            $table->integer('internal_percentage')->default(40)->after('practical_percentage'); // Internal %
            $table->integer('external_percentage')->default(60)->after('internal_percentage'); // External %
            $table->integer('lecture_hours')->default(4)->after('external_percentage'); // Lecture hours/week
            $table->integer('practical_hours')->default(2)->after('lecture_hours'); // Practical hours/week
            $table->integer('tutorial_hours')->default(1)->after('practical_hours'); // Tutorial hours/week
            $table->string('prerequisite', 50)->nullable()->after('tutorial_hours'); // Prerequisite course
            $table->date('start_date')->nullable()->after('prerequisite'); // Course start date
            $table->date('end_date')->nullable()->after('start_date'); // Course end date
            $table->text('remarks')->nullable()->after('end_date'); // Additional remarks
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('department', 50)->nullable();
            $table->dropColumn([
                'category',
                'description',
                'syllabus',
                'learning_objectives',
                'theory_percentage',
                'practical_percentage',
                'internal_percentage',
                'external_percentage',
                'lecture_hours',
                'practical_hours',
                'tutorial_hours',
                'prerequisite',
                'start_date',
                'end_date',
                'remarks'
            ]);
        });
    }
};

