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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('name');
            $table->string('name_nepali')->nullable();
            $table->string('short_name')->nullable();
            
            // Logo
            $table->string('logo_path')->nullable();

            // Landing page media
            $table->json('hero_images')->nullable();
            
            // Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Address
            $table->text('address')->nullable();
            $table->text('address_nepali')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();

            // Location / map
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('map_label')->nullable();
            
            // Principal Info
            $table->string('principal_name')->nullable();
            $table->string('principal_phone')->nullable();
            $table->string('principal_email')->nullable();
            
            // Additional Info
            $table->year('established_year')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('description')->nullable();
            $table->text('description_nepali')->nullable();

            // Landing: programs section content
            $table->string('programs_title')->nullable();
            $table->string('programs_title_nepali')->nullable();
            $table->text('programs_content')->nullable();
            $table->text('programs_content_nepali')->nullable();
            $table->string('programs_image_path')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
