<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions Matrix
        $permissions = [
            // Tenants
            'tenants.manage',
            'tenants.view',

            // Users
            'users.create',
            'users.edit',
            'users.view',
            'users.delete',

            // Cohorts
            'cohorts.create',
            'cohorts.edit',
            'cohorts.view',
            'cohorts.delete',
            'cohorts.enroll',

            // Courses & Curriculum
            'courses.create',
            'courses.edit',
            'courses.view',
            'courses.delete',

            // Live Sessions (Zoom)
            'live_sessions.create',
            'live_sessions.start',
            'live_sessions.join',
            'live_sessions.attendance',

            // Assignments & Grading
            'assignments.create',
            'assignments.submit',
            'assignments.grade',
            'assignments.view',

            // Certificates
            'certificates.issue',
            'certificates.view',
            'certificates.revoke',

            // Forums & Discussions
            'forums.thread.create',
            'forums.reply.create',
            'forums.moderate',

            // Analytics & Audit
            'analytics.view',
            'audit_logs.view',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 1. Super Admin (Full Platform Access)
        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Tenant Admin (Organization Level)
        $tenantAdmin = Role::findOrCreate('tenant_admin', 'web');
        $tenantAdmin->givePermissionTo([
            'tenants.view',
            'users.create',
            'users.edit',
            'users.view',
            'cohorts.create',
            'cohorts.edit',
            'cohorts.view',
            'cohorts.enroll',
            'courses.create',
            'courses.edit',
            'courses.view',
            'live_sessions.create',
            'live_sessions.start',
            'live_sessions.join',
            'live_sessions.attendance',
            'assignments.create',
            'assignments.grade',
            'assignments.view',
            'certificates.issue',
            'certificates.view',
            'forums.thread.create',
            'forums.reply.create',
            'forums.moderate',
            'analytics.view',
        ]);

        // 3. Instructor (Course & Cohort Delivery)
        $instructor = Role::findOrCreate('instructor', 'web');
        $instructor->givePermissionTo([
            'cohorts.view',
            'courses.create',
            'courses.edit',
            'courses.view',
            'live_sessions.create',
            'live_sessions.start',
            'live_sessions.join',
            'live_sessions.attendance',
            'assignments.create',
            'assignments.grade',
            'assignments.view',
            'certificates.issue',
            'certificates.view',
            'forums.thread.create',
            'forums.reply.create',
            'forums.moderate',
            'analytics.view',
        ]);

        // 4. Teaching Assistant (Support & Mentorship)
        $ta = Role::findOrCreate('teaching_assistant', 'web');
        $ta->givePermissionTo([
            'cohorts.view',
            'courses.view',
            'live_sessions.join',
            'live_sessions.attendance',
            'assignments.grade',
            'assignments.view',
            'forums.thread.create',
            'forums.reply.create',
            'forums.moderate',
        ]);

        // 5. Learner (Cohort Student)
        $learner = Role::findOrCreate('learner', 'web');
        $learner->givePermissionTo([
            'cohorts.view',
            'courses.view',
            'live_sessions.join',
            'assignments.submit',
            'assignments.view',
            'certificates.view',
            'forums.thread.create',
            'forums.reply.create',
        ]);
    }
}
