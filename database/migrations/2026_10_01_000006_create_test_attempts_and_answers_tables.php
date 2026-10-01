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
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->foreignId('test_package_id')->constrained('test_packages')->onDelete('cascade');
            $table->dateTime('started_at');
            $table->dateTime('server_end_time');
            $table->dateTime('submitted_at')->nullable();
            $table->integer('duration_seconds_used')->default(0);

            $table->integer('total_questions')->default(0);
            $table->integer('total_answered')->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_wrong')->default(0);
            $table->integer('total_unanswered')->default(0);

            $table->decimal('score_multiple_choice', 5, 2)->nullable();
            $table->json('aspect_scores_json')->nullable(); // Skor rata-rata 1-100 per aspek kepribadian
            $table->boolean('is_passed')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
            $table->json('question_order_json')->nullable(); // Urutan ID soal diacak untuk attempt ini

            $table->timestamps();
        });

        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained('test_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->tinyInteger('likert_value')->nullable(); // 1 - 5
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['test_attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_answers');
        Schema::dropIfExists('test_attempts');
    }
};
