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
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('status', 30)->default('scheduled')->change();
        });

        Schema::table('test_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('test_schedules', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('end_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('test_schedules', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
