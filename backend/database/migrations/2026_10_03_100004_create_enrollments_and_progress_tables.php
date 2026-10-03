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
        // Student Cohort Enrollments
        Schema::create('cohort_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('enrolled')->index(); // enrolled, active, completed, dropped, suspended
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['cohort_id', 'user_id']);
            $table->index(['cohort_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Individual Lesson Progress
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('status', 30)->default('completed'); // started, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['cohort_id', 'user_id', 'lesson_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('cohort_enrollments');
    }
};
