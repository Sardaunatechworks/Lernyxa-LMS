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
        // Live Sessions (Zoom Integration)
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('zoom_meeting_id')->nullable()->index();
            $table->text('zoom_join_url')->nullable();
            $table->text('zoom_start_url')->nullable();
            $table->string('zoom_passcode', 100)->nullable();
            $table->text('recording_url')->nullable();
            $table->string('status', 30)->default('scheduled')->index(); // scheduled, live, ended, cancelled
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cohort_id', 'start_time']);
            $table->index(['cohort_id', 'status']);
        });

        // Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->string('status', 30)->default('present')->index(); // present, late, absent, excused
            $table->timestamps();

            $table->unique(['live_session_id', 'user_id']);
            $table->index(['live_session_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('live_sessions');
    }
};
