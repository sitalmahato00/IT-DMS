<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if audience column doesn't exist, add it
        if (!Schema::hasColumn('notices', 'audience')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->enum('audience', ['all', 'students', 'faculty', 'parents'])->default('all')->after('message');
            });
        }

        // Check if status column doesn't exist, add it
        if (!Schema::hasColumn('notices', 'status')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft')->after('audience');
            });
        }

        // Check if semester column doesn't exist, add it
        if (!Schema::hasColumn('notices', 'semester')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->string('semester', 20)->nullable()->after('status');
            });
        }

        // Check if is_important column doesn't exist, add it
        if (!Schema::hasColumn('notices', 'is_important')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->boolean('is_important')->default(false)->after('semester');
            });
        }

        // Check if published_at column doesn't exist, add it
        if (!Schema::hasColumn('notices', 'published_at')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('is_important');
            });
        }

        // Add indexes if they don't exist
        Schema::table('notices', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasIndex('notices', 'notices_status_index')) {
                $table->index('status');
            }
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasIndex('notices', 'notices_audience_index')) {
                $table->index('audience');
            }
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasIndex('notices', 'notices_published_at_index')) {
                $table->index('published_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['audience']);
            $table->dropIndex(['published_at']);
            $table->dropColumn(['audience', 'status', 'semester', 'is_important', 'published_at']);
        });
    }
};

