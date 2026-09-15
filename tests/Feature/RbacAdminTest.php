<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected Project $project;

    protected Role $devRole;

    protected Role $pmRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@openproject.local')->first();
        $this->regularUser = User::where('email', 'alex@openproject.local')->first();
        $this->project = Project::first();
        $this->devRole = Role::where('slug', 'developer')->first();
        $this->pmRole = Role::where('slug', 'project-manager')->first();
    }

    public function test_non_admin_cannot_access_admin_console(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/users');
        $response->assertStatus(403);

        $rolesResponse = $this->actingAs($this->regularUser)->get('/admin/roles');
        $rolesResponse->assertStatus(403);
    }

    public function test_admin_can_access_users_and_roles_console(): void
    {
        $usersResponse = $this->actingAs($this->admin)->get('/admin/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('User Management');
        $usersResponse->assertSee($this->regularUser->name);

        $rolesResponse = $this->actingAs($this->admin)->get('/admin/roles');
        $rolesResponse->assertStatus(200);
        $rolesResponse->assertSee('Roles & Permissions Matrix');
        $rolesResponse->assertSee('Developer');
    }

    public function test_admin_can_create_new_user_and_assign_initial_project_role(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Emma Watson',
            'email' => 'emma@openproject.local',
            'password' => 'secretPass123',
            'status' => 'active',
            'is_admin' => '0',
            'project_id' => $this->project->id,
            'role_id' => $this->pmRole->id,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'emma@openproject.local',
            'name' => 'Emma Watson',
            'is_admin' => false,
        ]);

        $newUser = User::where('email', 'emma@openproject.local')->first();
        $this->assertNotNull($newUser);

        $this->assertDatabaseHas('project_members', [
            'user_id' => $newUser->id,
            'project_id' => $this->project->id,
            'role_id' => $this->pmRole->id,
        ]);
    }

    public function test_admin_can_assign_or_change_project_role(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/users/{$this->regularUser->id}/projects", [
            'project_id' => $this->project->id,
            'role_id' => $this->pmRole->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('project_members', [
            'user_id' => $this->regularUser->id,
            'project_id' => $this->project->id,
            'role_id' => $this->pmRole->id,
        ]);
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $newPermissions = ['view_project', 'manage_tasks', 'log_time'];

        $response = $this->actingAs($this->admin)->put("/admin/roles/{$this->devRole->id}", [
            'name' => 'Software Engineer',
            'description' => 'Updated developer scope',
            'permissions' => $newPermissions,
        ]);

        $response->assertRedirect();
        $this->devRole->refresh();
        $this->assertEquals('Software Engineer', $this->devRole->name);
        $this->assertEquals($newPermissions, $this->devRole->permissions);
    }
}
