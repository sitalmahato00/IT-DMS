<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('department');
            }
            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('students', 'batch_year')) {
                $table->string('batch_year', 10)->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'batch_year')) {
                $table->dropColumn('batch_year');
            }
            if (Schema::hasColumn('students', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('students', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
        });
    }
};