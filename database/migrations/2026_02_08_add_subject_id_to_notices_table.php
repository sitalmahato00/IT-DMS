
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            // Add subject_id column if it doesn't exist
            if (!Schema::hasColumn('notices', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('semester')->constrained('subjects')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};


