<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Cohort;
use App\Models\CohortEnrollment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LiveSession;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantAndDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Sardauna Tech Lab Tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'sardauna-tech-lab'],
            [
                'name' => 'Sardauna Tech Lab',
                'domain' => 'sardaunatechlab.com',
                'primary_color' => '#6366F1',
                'is_active' => true,
                'settings' => [
                    'theme' => 'dark',
                    'contact_email' => 'contact@sardaunatechlab.com',
                    'max_cohort_size' => 500,
                ],
            ]
        );

        $defaultPassword = Hash::make('Password123!');

        // 2. Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@lernyxa.com'],
            [
                'name' => 'Super Administrator',
                'first_name' => 'Lernyxa',
                'last_name' => 'SuperAdmin',
                'password' => $defaultPassword,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // 3. Create Tenant Admin
        $tenantAdmin = User::firstOrCreate(
            ['email' => 'admin@sardaunatechlab.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Sardauna Admin',
                'first_name' => 'Sardauna',
                'last_name' => 'Admin',
                'password' => $defaultPassword,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $tenantAdmin->assignRole('tenant_admin');

        // 4. Create Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@sardaunatechlab.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Lead Instructor',
                'first_name' => 'Dr. Aminu',
                'last_name' => 'Kano',
                'password' => $defaultPassword,
                'status' => 'active',
                'bio' => 'Senior Full-Stack Architect with 10+ years in distributed systems.',
                'email_verified_at' => now(),
            ]
        );
        $instructor->assignRole('instructor');

        // 5. Create Teaching Assistant
        $ta = User::firstOrCreate(
            ['email' => 'ta@sardaunatechlab.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Academic TA',
                'first_name' => 'Fatima',
                'last_name' => 'Bello',
                'password' => $defaultPassword,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $ta->assignRole('teaching_assistant');

        // 6. Create Demo Learner
        $learner = User::firstOrCreate(
            ['email' => 'learner@sardaunatechlab.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Ibrahim Danladi',
                'first_name' => 'Ibrahim',
                'last_name' => 'Danladi',
                'password' => $defaultPassword,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $learner->assignRole('learner');

        // 7. Create Demo Cohort (Capacity: 500 per approved specs)
        $cohort = Cohort::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'fsw-cohort-1'],
            [
                'name' => 'Full-Stack Web Engineering — Cohort 1',
                'description' => 'Comprehensive 12-week immersive software engineering bootcamp focusing on Next.js, Laravel, and Cloud DevOps.',
                'start_date' => now()->startOfWeek(),
                'end_date' => now()->addWeeks(12)->endOfWeek(),
                'capacity' => 500,
                'status' => 'active',
                'settings' => [
                    'passing_grade_percentage' => 70,
                    'minimum_attendance_rate' => 80,
                ],
            ]
        );

        // 8. Create Course
        $course = Course::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'fullstack-web-nextjs-laravel'],
            [
                'created_by' => $instructor->id,
                'title' => 'Modern Full-Stack Development with Next.js & Laravel',
                'summary' => 'Build enterprise-grade web applications with a modern React frontend and resilient PHP API backend.',
                'description' => 'From database schema architecture to real-time Zoom attendance and cryptographically signed PDF certificates.',
                'difficulty_level' => 'intermediate',
                'status' => 'published',
            ]
        );

        // Attach Course to Cohort
        $cohort->courses()->syncWithoutDetaching([
            $course->id => [
                'instructor_id' => $instructor->id,
                'order_index' => 1,
                'is_active' => true,
            ],
        ]);

        // 9. Create Curriculum Modules & Lessons
        $module1 = Module::firstOrCreate(
            ['course_id' => $course->id, 'order_index' => 1],
            [
                'title' => 'Module 1: Architecture & Environment Foundations',
                'description' => 'Monorepo setup, Docker containers, PostgreSQL schema design, and Laravel Sanctum auth.',
                'unlock_offset_days' => 0,
                'is_published' => true,
            ]
        );

        Lesson::firstOrCreate(
            ['module_id' => $module1->id, 'slug' => 'lms-architecture-overview'],
            [
                'title' => 'Decoupled LMS Architecture Deep Dive',
                'content_type' => 'video',
                'body_content' => 'Overview of the Lernyxa LMS architecture, decoupling Next.js 14+ and Laravel 11+ via cookie-based SPA authentication.',
                'duration_minutes' => 45,
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        Lesson::firstOrCreate(
            ['module_id' => $module1->id, 'slug' => 'docker-compose-stack-walkthrough'],
            [
                'title' => 'Docker Compose 7-Service Environment Walkthrough',
                'content_type' => 'video',
                'body_content' => 'Setting up PHP-FPM, Nginx, PostgreSQL, Redis, MinIO, and Mailpit for local and production deployment.',
                'duration_minutes' => 35,
                'order_index' => 2,
                'is_published' => true,
            ]
        );

        $module2 = Module::firstOrCreate(
            ['course_id' => $course->id, 'order_index' => 2],
            [
                'title' => 'Module 2: Real-Time Cohort Pacing & Live Classrooms',
                'description' => 'Integrating Zoom Meeting SDK, automated webhooks, and duration-based attendance logging.',
                'unlock_offset_days' => 7,
                'is_published' => true,
            ]
        );

        Lesson::firstOrCreate(
            ['module_id' => $module2->id, 'slug' => 'zoom-sdk-integration-and-security'],
            [
                'title' => 'Zoom Meeting SDK Client Embedding & Security Tokens',
                'content_type' => 'video',
                'body_content' => 'Generating Server-to-Server OAuth tokens and signature verification for embedded in-app Zoom sessions.',
                'duration_minutes' => 50,
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        // 10. Create Live Session
        LiveSession::firstOrCreate(
            ['cohort_id' => $cohort->id, 'title' => 'Cohort 1: Orientation & Architecture Kickoff'],
            [
                'course_id' => $course->id,
                'instructor_id' => $instructor->id,
                'description' => 'Welcome session, syllabus review, environment verification, and Q&A with Dr. Aminu.',
                'start_time' => now()->addDays(1)->setTime(18, 0),
                'end_time' => now()->addDays(1)->setTime(19, 30),
                'zoom_meeting_id' => '98765432101',
                'zoom_join_url' => 'https://zoom.us/j/98765432101',
                'zoom_passcode' => 'Lernyxa2026',
                'status' => 'scheduled',
            ]
        );

        // 11. Create Capstone Assignment
        Assignment::firstOrCreate(
            ['cohort_id' => $cohort->id, 'title' => 'Milestone 1: Multi-Tenant Schema & API Implementation'],
            [
                'course_id' => $course->id,
                'module_id' => $module1->id,
                'created_by' => $instructor->id,
                'description' => 'Design and implement the complete multi-tenant database layer and publish working endpoints with tests.',
                'submission_type' => 'github_repo',
                'max_score' => 100,
                'passing_score' => 70,
                'due_date' => now()->addDays(14)->setTime(23, 59),
                'is_published' => true,
            ]
        );

        // 12. Enroll Demo Learner in Cohort
        CohortEnrollment::firstOrCreate(
            ['cohort_id' => $cohort->id, 'user_id' => $learner->id],
            [
                'status' => 'active',
                'enrolled_at' => now(),
                'progress_percentage' => 25.00,
            ]
        );
    }
}
