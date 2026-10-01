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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vacancy_id')->constrained('vacancies')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('access_token', 64)->unique();
            $table->string('nik', 30)->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth_date')->nullable();
            $table->string('education', 100)->nullable();
            $table->text('address')->nullable();

            // Status seleksi: REGISTERED, PSIKOTES, LULUS_PSIKOTES, INTERVIEW, LULUS_INTERVIEW, CADANGAN, TIDAK_LULUS, DITERIMA
            $table->enum('status', [
                'REGISTERED',
                'PSIKOTES',
                'LULUS_PSIKOTES',
                'INTERVIEW',
                'LULUS_INTERVIEW',
                'CADANGAN',
                'TIDAK_LULUS',
                'DITERIMA',
            ])->default('REGISTERED');

            $table->decimal('final_psychotest_score', 5, 2)->nullable();
            $table->decimal('final_interview_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->integer('final_rank')->nullable();
            $table->enum('final_status', ['LOLOS', 'CADANGAN', 'TIDAK_LOLOS'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
