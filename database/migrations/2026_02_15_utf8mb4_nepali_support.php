<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration for UTF-8 Unicode Nepali Support
 * 
 * This migration sets up proper utf8mb4 character set support for storing
 * Nepali (Devanagari script) content directly in the database.
 * 
 * Following Nepal Government Website Standards:
 * - Uses utf8mb4 charset for full Unicode support
 * - utf8mb4_unicode_ci collation for proper Devanagari sorting
 * - Stores Nepali content directly (not transliterated)
 * 
 * Reference: https://language.gov.np, https://nepal.gov.np
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL: Set charset to utf8mb4 for full Unicode support
        Schema::create('nepali_notices', function (Blueprint $table) {
            $table->id();
            
            // Nepali content fields - using TEXT for long content
            $table->string('title_ne', 500)->nullable();      // Nepali title
            $table->text('content_ne')->nullable();           // Nepali content
            $table->string('audience_ne', 50)->nullable();    // Nepali audience label
            
            // English fallback fields
            $table->string('title_en', 500)->nullable();      // English title
            $table->text('content_en')->nullable();           // English content
            
            // Metadata
            $table->string('priority', 20)->default('normal'); // normal, urgent, important
            $table->string('status', 20)->default('published');
            $table->string('locale', 10)->default('ne');       // ne, en, both
            
            // Dates (Bikram Sambat support via separate fields)
            $table->date('published_date_bs')->nullable();    // Nepali date
            $table->date('expiry_date_bs')->nullable();       // Nepali expiry date
            
            // Foreign keys
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('published_date_bs');
            $table->index('locale');
            $table->index(['locale', 'status']);
            
            // Fulltext index for Nepali search (MySQL 5.7+)
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE nepali_notices ADD FULLTEXT INDEX nepalinotices_fulltext (title_ne, content_ne)');
            }
        });

        // Example: Create notices table with bilingual support
        Schema::create('bilingual_notices', function (Blueprint $table) {
            $table->id();
            
            // Bilingual fields - title and content in both languages
            $table->string('title_ne', 500)->comment('सूचना शीर्षक (Nepali)');
            $table->string('title_en', 500)->nullable()->comment('Notice Title (English)');
            $table->text('content_ne')->comment('सूचना सामग्री (Nepali)');
            $table->text('content_en')->nullable()->comment('Notice Content (English)');
            
            // Audience with bilingual labels
            $table->enum('audience', ['all', 'students', 'faculty', 'parents'])
                  ->default('all');
            $table->string('audience_label_ne', 50)->nullable()
                  ->comment('दर्शक लेबल (Nepali) - e.g., सबै, विद्यार्थी, शिक्षक, अभिभावक');
            
            // Category with bilingual support
            $table->string('category', 50)->default('general');
            $table->string('category_label_ne', 100)->nullable()
                  ->comment('श्रेणी लेबल (Nepali) - e.g., सूचना, कार्यक्रम, परीक्षा');
            
            // Priority with bilingual labels
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal');
            $table->string('priority_label_ne', 50)->nullable()
                  ->comment('प्राथमिकता लेबल (Nepali) - e.g., सामान्य, महत्वपूर्ण, अत्यावश्यक');
            
            // Dates
            $table->date('published_date')->useCurrent();
            $table->date('expiry_date')->nullable();
            $table->string('published_date_bs', 20)->nullable()
                  ->comment('प्रकाशित मिति (Bikram Sambat)');
            $table->string('expiry_date_bs', 20)->nullable()
                  ->comment('म्याद समाप्ति मिति (Bikram Sambat)');
            
            // File attachments (with Nepali filenames support)
            $table->string('file_path', 500)->nullable();
            $table->string('file_name_ne', 255)->nullable()
                  ->comment('संलग्नक नाम (Nepali)');
            
            // Status and visibility
            $table->boolean('is_published')->default(true);
            $table->boolean('is_important')->default(false);
            $table->boolean('is_featured')->default(false);
            
            // SEO fields for bilingual support
            $table->string('meta_title_ne', 200)->nullable();
            $table->string('meta_description_ne', 500)->nullable();
            
            // Relationships
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['is_published', 'published_date']);
            $table->index(['is_published', 'audience']);
            $table->index(['is_important', 'is_published']);
            $table->index('category');
            $table->index('locale');
        });

        // Create study materials table with Nepali support
        Schema::create('study_materials_bilingual', function (Blueprint $table) {
            $table->id();
            
            // Bilingual fields
            $table->string('title_ne', 500)->comment('सामग्री शीर्षक (Nepali)');
            $table->string('title_en', 500)->nullable()->comment('Material Title (English)');
            $table->text('description_ne')->nullable()->comment('सामग्री विवरण (Nepali)');
            $table->text('description_en')->nullable()->comment('Material Description (English)');
            
            // Document type with bilingual labels
            $table->enum('document_type', [
                'lecture_notes',    // लेक्चर नोट्स
                'assignment',       // एसाइनमेंट
                'lab_report',       // ल्याब रिपोर्ट
                'study_guide',      // अध्ययन गाइड
                'syllabus',         // पाठ्यक्रम
                'previous_paper',   // गत वर्षको प्रश्नपत्र
                'project_material', // प्रोजेक्ट सामग्री
                'other'             // अन्य
            ]);
            $table->string('document_type_label_ne', 100)->nullable()
                  ->comment('डकुमेंट प्रकार लेबल (Nepali)');
            
            // Visibility with bilingual labels
            $table->enum('visibility', ['all', 'students', 'faculty', 'admin'])
                  ->default('students');
            $table->string('visibility_label_ne', 50)->nullable()
                  ->comment('दृश्यता लेबल (Nepali) - e.g., सबै, विद्यार्थी, शिक्षक');
            
            // Semester with Nepali labels
            $table->integer('semester')->nullable();
            $table->string('semester_label_ne', 50)->nullable()
                  ->comment('सेमेस्टर लेबल (Nepali) - e.g., प्रथम, द्वितीय, तृतीय');
            
            // File information
            $table->string('file_path', 500);
            $table->string('original_file_name', 500);
            $table->string('file_name_ne', 255)->nullable()
                  ->comment('फाइल नाम (Nepali) - supports Unicode filenames');
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');
            
            // Metadata
            $table->string('academic_year', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('download_count')->default(0);
            
            // Relationships
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['is_active', 'document_type']);
            $table->index(['is_active', 'semester']);
            $table->index(['is_active', 'visibility']);
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials_bilingual');
        Schema::dropIfExists('bilingual_notices');
        Schema::dropIfExists('nepali_notices');
    }
};
