<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        // Add Nepali fields to notices table
        Schema::table('notices', function (Blueprint $table) {
            $table->string('title_ne', 500)->nullable()->after('title');
            $table->text('message_ne')->nullable()->after('message');
            $table->string('audience_ne', 50)->nullable()->after('audience');
        });

        // Add Nepali fields to subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('subject_name_ne', 255)->nullable()->after('subject_name');
            $table->text('description_ne')->nullable()->after('description');
        });

        // Add Nepali fields to study_materials table
        Schema::table('study_materials', function (Blueprint $table) {
            $table->string('title_ne', 500)->nullable()->after('title');
            $table->text('description_ne')->nullable()->after('description');
        });

        // Add Nepali fields to gallery table
        Schema::table('gallery', function (Blueprint $table) {
            $table->string('title_ne', 255)->nullable()->after('title');
            $table->text('description_ne')->nullable()->after('description');
            $table->string('category_ne', 100)->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Nepali fields from notices table
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['title_ne', 'message_ne', 'audience_ne']);
        });

        // Remove Nepali fields from subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['subject_name_ne', 'description_ne']);
        });

        // Remove Nepali fields from study_materials table
        Schema::table('study_materials', function (Blueprint $table) {
            $table->dropColumn(['title_ne', 'description_ne']);
        });

        // Remove Nepali fields from gallery table
        Schema::table('gallery', function (Blueprint $table) {
            $table->dropColumn(['title_ne', 'description_ne', 'category_ne']);
        });
    }
};
