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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('certificate_code', 64)->unique()->index();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('issued_at')->useCurrent();
            $table->text('pdf_path')->nullable();
            $table->text('qr_code_path')->nullable();
            $table->decimal('grade_percentage', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_revoked')->default(false)->index();
            $table->text('revocation_reason')->nullable();
            $table->timestamps();

            $table->unique(['cohort_id', 'course_id', 'user_id']);
            $table->index(['tenant_id', 'issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
