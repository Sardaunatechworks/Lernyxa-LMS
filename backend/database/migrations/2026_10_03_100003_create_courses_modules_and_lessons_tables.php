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
        // Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('difficulty_level', 30)->default('intermediate'); // beginner, intermediate, advanced
            $table->string('status', 30)->default('draft')->index(); // draft, published, archived
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });

        // Cohort - Course association
        Schema::create('cohort_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('order_index')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['cohort_id', 'course_id']);
            $table->index(['cohort_id', 'is_active']);
        });

        // Modules (Chapters/Weeks)
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order_index')->default(1);
            $table->unsignedInteger('unlock_offset_days')->nullable(); // Pacing: days from cohort start
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'order_index']);
        });

        // Lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('content_type', 30)->default('video'); // video, article, quiz, assignment
            $table->longText('body_content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('order_index')->default(1);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(true)->index();
            $table->json('resources')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['module_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('cohort_courses');
        Schema::dropIfExists('courses');
    }
};
