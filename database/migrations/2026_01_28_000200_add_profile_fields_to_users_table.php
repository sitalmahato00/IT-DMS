<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('profile_photo_path');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // Attempt to drop columns; SQLite may not support dropColumn.
            $cols = ['phone','department','bio','profile_photo_path','role','status'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('users', $c)) {
                    try {
                        $table->dropColumn($c);
                    } catch (\Throwable $e) {
                        // ignore on SQLite or other drivers where drop isn't supported
                    }
                }
            }
        });
    }
};
