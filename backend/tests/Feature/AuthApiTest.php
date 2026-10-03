<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'learner@sardaunatechlab.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'permissions',
                    'status',
                    'tenant_id',
                ],
            ]);

        $this->assertTrue(in_array('learner', $response->json('user.roles')));
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'learner@sardaunatechlab.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::where('email', 'learner@sardaunatechlab.com')->first();
        $user->update(['status' => 'suspended']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'learner@sardaunatechlab.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Your account is suspended. Please contact your administrator.',
            ]);
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::where('email', 'instructor@sardaunatechlab.com')->first();
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', 'instructor@sardaunatechlab.com')
            ->assertJsonPath('user.first_name', 'Dr. Aminu');

        $this->assertTrue(in_array('instructor', $response->json('user.roles')));
    }

    public function test_learner_can_register_and_receives_learner_role(): void
    {
        $tenant = Tenant::first();

        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Maryam',
            'last_name' => 'Abubakar',
            'email' => 'maryam@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'tenant_id' => $tenant->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', 'maryam@example.com');

        $createdUser = User::where('email', 'maryam@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('learner'));
        $this->assertEquals($tenant->id, $createdUser->tenant_id);
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'learner@sardaunatechlab.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(0, $user->tokens);
    }

    public function test_multi_tenant_isolation_via_tenant_scope(): void
    {
        // 1. Create a second tenant
        $tenantB = Tenant::create([
            'name' => 'Tech Corp',
            'slug' => 'tech-corp',
            'is_active' => true,
        ]);

        $tenantA = Tenant::where('slug', 'sardauna-tech-lab')->first();

        // 2. Create Cohort for Tenant B
        $cohortB = Cohort::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Tenant B Cohort',
            'slug' => 'tenant-b-cohort',
            'start_date' => now(),
            'end_date' => now()->addWeeks(8),
            'capacity' => 100,
        ]);

        // 3. Authenticate as a user belonging to Tenant A
        $userA = User::where('email', 'instructor@sardaunatechlab.com')->first();
        Sanctum::actingAs($userA, ['*']);

        // Querying Cohorts should only see Tenant A cohorts
        $visibleCohorts = Cohort::all();
        $this->assertTrue($visibleCohorts->every(fn ($c) => $c->tenant_id === $tenantA->id));
        $this->assertFalse($visibleCohorts->contains('id', $cohortB->id));

        // 4. Authenticate as Super Admin
        $superAdmin = User::where('email', 'superadmin@lernyxa.com')->first();
        Sanctum::actingAs($superAdmin, ['*']);

        // Super Admin sees all cohorts across all tenants
        $allCohorts = Cohort::all();
        $this->assertTrue($allCohorts->contains('id', $cohortB->id));
    }
}
