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
        Schema::create('test_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('total_questions')->default(40);
            $table->integer('duration_minutes')->default(50);
            $table->decimal('passing_grade', 5, 2)->default(70.00);
            $table->boolean('is_randomized')->default(true);
            $table->boolean('is_options_randomized')->default(true);
            $table->boolean('show_result_to_candidate')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('test_package_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_package_id')->constrained('test_packages')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('order_num')->default(0);
            $table->timestamps();
        });

        Schema::create('test_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_package_id')->constrained('test_packages')->onDelete('cascade');
            $table->string('name');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_schedules');
        Schema::dropIfExists('test_package_questions');
        Schema::dropIfExists('test_packages');
    }
};
