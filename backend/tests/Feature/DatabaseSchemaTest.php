<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LiveSession;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_migrations_and_seeders_execute_cleanly(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Verify 5 Core Roles
        $expectedRoles = ['super_admin', 'tenant_admin', 'instructor', 'teaching_assistant', 'learner'];
        foreach ($expectedRoles as $roleName) {
            $this->assertTrue(Role::where('name', $roleName)->exists(), "Role {$roleName} missing");
        }

        // 2. Verify Tenant
        $tenant = Tenant::where('slug', 'sardauna-tech-lab')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Sardauna Tech Lab', $tenant->name);
        $this->assertTrue($tenant->is_active);

        // 3. Verify Users & Roles
        $superAdmin = User::where('email', 'superadmin@lernyxa.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('super_admin'));

        $tenantAdmin = User::where('email', 'admin@sardaunatechlab.com')->first();
        $this->assertNotNull($tenantAdmin);
        $this->assertTrue($tenantAdmin->hasRole('tenant_admin'));
        $this->assertEquals($tenant->id, $tenantAdmin->tenant_id);

        $instructor = User::where('email', 'instructor@sardaunatechlab.com')->first();
        $this->assertNotNull($instructor);
        $this->assertTrue($instructor->hasRole('instructor'));

        $ta = User::where('email', 'ta@sardaunatechlab.com')->first();
        $this->assertNotNull($ta);
        $this->assertTrue($ta->hasRole('teaching_assistant'));

        $learner = User::where('email', 'learner@sardaunatechlab.com')->first();
        $this->assertNotNull($learner);
        $this->assertTrue($learner->hasRole('learner'));

        // 4. Verify Cohort (500 capacity)
        $cohort = Cohort::where('slug', 'fsw-cohort-1')->first();
        $this->assertNotNull($cohort);
        $this->assertEquals(500, $cohort->capacity);
        $this->assertEquals('active', $cohort->status);

        // 5. Verify Course & Curriculum Hierarchy
        $course = Course::where('slug', 'fullstack-web-nextjs-laravel')->first();
        $this->assertNotNull($course);
        $this->assertTrue($cohort->courses->contains($course->id));

        $modules = Module::where('course_id', $course->id)->get();
        $this->assertCount(2, $modules);

        $lessons = Lesson::whereIn('module_id', $modules->pluck('id'))->get();
        $this->assertGreaterThanOrEqual(3, $lessons->count());

        // 6. Verify Live Session & Assignment
        $session = LiveSession::where('cohort_id', $cohort->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals('98765432101', $session->zoom_meeting_id);

        $assignment = Assignment::where('cohort_id', $cohort->id)->first();
        $this->assertNotNull($assignment);
        $this->assertEquals(100, $assignment->max_score);

        // 7. Verify Enrollment
        $this->assertTrue($cohort->students->contains($learner->id));
        $this->assertEquals(499, $cohort->available_slots);
    }
}
