<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortEnrollment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CohortAndCurriculumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_list_cohorts(): void
    {
        $learner = User::where('email', 'learner@sardaunatechlab.com')->first();
        Sanctum::actingAs($learner, ['*']);

        $response = $this->getJson('/api/v1/cohorts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'capacity',
                        'enrolled_count',
                        'available_slots',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_admin_can_create_cohort_with_default_500_capacity(): void
    {
        $admin = User::where('email', 'admin@sardaunatechlab.com')->first();
        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/v1/cohorts', [
            'name' => 'Data Engineering — Cohort 2',
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addWeeks(14)->format('Y-m-d'),
            'description' => 'Comprehensive data pipelines and cloud infrastructure.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.capacity', 500)
            ->assertJsonPath('data.available_slots', 500);

        $this->assertDatabaseHas('cohorts', [
            'name' => 'Data Engineering — Cohort 2',
            'capacity' => 500,
        ]);
    }

    public function test_enrollment_fails_when_cohort_reaches_capacity(): void
    {
        $admin = User::where('email', 'admin@sardaunatechlab.com')->first();
        $tenant = Tenant::first();

        // Create a cohort with capacity of 1
        $miniCohort = Cohort::create([
            'tenant_id' => $tenant->id,
            'name' => 'VIP Micro-Cohort',
            'slug' => 'vip-micro-cohort',
            'start_date' => now(),
            'end_date' => now()->addWeeks(4),
            'capacity' => 1,
            'status' => 'active',
        ]);

        // Student 1
        $student1 = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $student1->assignRole('learner');

        // Student 2
        $student2 = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student Two',
            'email' => 'student2@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $student2->assignRole('learner');

        // Enroll Student 1 -> Should succeed
        Sanctum::actingAs($student1, ['*']);
        $res1 = $this->postJson("/api/v1/cohorts/{$miniCohort->id}/enroll");
        $res1->assertStatus(201);

        // Enroll Student 2 -> Should fail with 422 because capacity is 1
        Sanctum::actingAs($student2, ['*']);
        $res2 = $this->postJson("/api/v1/cohorts/{$miniCohort->id}/enroll");
        $res2->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cohort has reached its maximum capacity of 1 learners.',
            ]);
    }

    public function test_instructor_can_create_course_and_attach_to_cohort(): void
    {
        $instructor = User::where('email', 'instructor@sardaunatechlab.com')->first();
        $cohort = Cohort::where('slug', 'fsw-cohort-1')->first();
        Sanctum::actingAs($instructor, ['*']);

        // 1. Create Course
        $res = $this->postJson('/api/v1/courses', [
            'title' => 'DevOps & CI/CD Pipelines',
            'summary' => 'Automated deployment with Docker and GitHub Actions',
            'difficulty_level' => 'advanced',
            'status' => 'published',
        ]);

        $res->assertStatus(201);
        $courseId = $res->json('data.id');

        // 2. Attach to Cohort
        $attachRes = $this->postJson("/api/v1/courses/{$courseId}/attach-cohort", [
            'cohort_id' => $cohort->id,
            'instructor_id' => $instructor->id,
        ]);

        $attachRes->assertStatus(200);
        $this->assertTrue($cohort->courses()->where('courses.id', $courseId)->exists());
    }

    public function test_student_can_complete_lesson_and_track_cohort_progress(): void
    {
        $learner = User::where('email', 'learner@sardaunatechlab.com')->first();
        $cohort = Cohort::where('slug', 'fsw-cohort-1')->first();
        $lesson = Lesson::where('slug', 'lms-architecture-overview')->first();

        Sanctum::actingAs($learner, ['*']);

        $res = $this->postJson("/api/v1/lessons/{$lesson->id}/complete", [
            'cohort_id' => $cohort->id,
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.lesson_id', $lesson->id);

        $this->assertGreaterThan(0, $res->json('data.progress_percentage'));

        $enrollment = CohortEnrollment::where('cohort_id', $cohort->id)
            ->where('user_id', $learner->id)
            ->first();

        $this->assertEquals($res->json('data.progress_percentage'), (float) $enrollment->progress_percentage);
    }
}
