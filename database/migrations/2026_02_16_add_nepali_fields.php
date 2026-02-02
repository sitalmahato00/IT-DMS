<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds Nepali (Devanagari) content fields to existing tables for bilingual support.
     * Following Nepal Government Website Standards:
     * - Store Nepali content directly in Unicode (utf8mb4)
     * - Translate only UI labels via lang files
     */
    public function up(): void
    {
        // Add Nepali fields to notices table (if table exists)
        if (Schema::hasTable('notices')) {
            Schema::table('notices', function (Blueprint $table) {
                if (!Schema::hasColumn('notices', 'title_ne')) {
                    $table->string('title_ne', 500)->nullable()->after('title');
                }
                if (!Schema::hasColumn('notices', 'message_ne')) {
                    $table->text('message_ne')->nullable()->after('message');
                }
                if (!Schema::hasColumn('notices', 'audience_ne')) {
                    $table->string('audience_ne', 50)->nullable()->after('audience');
                }
            });
        }

        // Add Nepali fields to subjects table (if table exists)
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                if (!Schema::hasColumn('subjects', 'subject_name_ne')) {
                    $table->string('subject_name_ne', 255)->nullable()->after('subject_name');
                }
                if (!Schema::hasColumn('subjects', 'description_ne')) {
                    $table->text('description_ne')->nullable()->after('description');
                }
            });
        }

        // Add Nepali fields to study_materials table (if table exists)
        if (Schema::hasTable('study_materials')) {
            Schema::table('study_materials', function (Blueprint $table) {
                if (!Schema::hasColumn('study_materials', 'title_ne')) {
                    $table->string('title_ne', 500)->nullable()->after('title');
                }
                if (!Schema::hasColumn('study_materials', 'description_ne')) {
                    $table->text('description_ne')->nullable()->after('description');
                }
            });
        }

        // Add Nepali fields to gallery table (if table exists)
        if (Schema::hasTable('gallery')) {
            Schema::table('gallery', function (Blueprint $table) {
                if (!Schema::hasColumn('gallery', 'title_ne')) {
                    $table->string('title_ne', 255)->nullable()->after('title');
                }
                if (!Schema::hasColumn('gallery', 'description_ne')) {
                    $table->text('description_ne')->nullable()->after('description');
                }
                if (!Schema::hasColumn('gallery', 'category_ne')) {
                    $table->string('category_ne', 100)->nullable()->after('category');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Nepali fields from notices table
        Schema::table('notices', function (Blueprint $table) {
            $columns = ['title_ne', 'message_ne', 'audience_ne'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('notices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove Nepali fields from subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $columns = ['subject_name_ne', 'description_ne'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('subjects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove Nepali fields from study_materials table
        Schema::table('study_materials', function (Blueprint $table) {
            $columns = ['title_ne', 'description_ne'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('study_materials', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove Nepali fields from gallery table
        Schema::table('gallery', function (Blueprint $table) {
            $columns = ['title_ne', 'description_ne', 'category_ne'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('gallery', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
