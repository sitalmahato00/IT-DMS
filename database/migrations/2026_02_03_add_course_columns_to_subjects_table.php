<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'credits')) {
                $table->integer('credits')->default(3)->after('subject_code');
            }
            if (!Schema::hasColumn('subjects', 'category')) {
                $table->string('category', 100)->nullable()->after('credits');
            }
            if (!Schema::hasColumn('subjects', 'theory_percentage')) {
                $table->integer('theory_percentage')->default(70)->after('category');
            }
            if (!Schema::hasColumn('subjects', 'practical_percentage')) {
                $table->integer('practical_percentage')->default(30)->after('theory_percentage');
            }
            if (!Schema::hasColumn('subjects', 'internal_percentage')) {
                $table->integer('internal_percentage')->default(40)->after('practical_percentage');
            }
            if (!Schema::hasColumn('subjects', 'external_percentage')) {
                $table->integer('external_percentage')->default(60)->after('internal_percentage');
            }
            if (!Schema::hasColumn('subjects', 'lecture_hours')) {
                $table->integer('lecture_hours')->default(4)->after('external_percentage');
            }
            if (!Schema::hasColumn('subjects', 'practical_hours')) {
                $table->integer('practical_hours')->default(2)->after('lecture_hours');
            }
            if (!Schema::hasColumn('subjects', 'tutorial_hours')) {
                $table->integer('tutorial_hours')->default(1)->after('practical_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'credits',
                'category',
                'theory_percentage',
                'practical_percentage',
                'internal_percentage',
                'external_percentage',
                'lecture_hours',
                'practical_hours',
                'tutorial_hours'
            ]);
        });
    }
};
