<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkPackage;
use App\Models\WorkPackageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::first();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_user_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Contributor',
            'email' => 'contributor@openproject.local',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'contributor@openproject.local',
        ]);
    }

    public function test_dashboard_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('OpenProject');
        $response->assertSee('Active Projects');
    }

    public function test_projects_screens_can_be_rendered(): void
    {
        $project = Project::first();
        $this->assertNotNull($project);

        $indexResponse = $this->actingAs($this->user)->get('/projects');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($project->name);

        $showResponse = $this->actingAs($this->user)->get("/projects/{$project->identifier}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($project->name);
    }

    public function test_user_can_create_private_project_with_unchecked_public_box(): void
    {
        // When checkbox is unchecked in browser, is_public key is omitted from payload
        $response = $this->actingAs($this->user)->post('/projects', [
            'name' => 'Confidential Vault Project',
            'description' => 'Private project description',
            // is_public intentionally omitted
        ]);

        $response->assertRedirect('/projects/confidential-vault-project');
        $this->assertDatabaseHas('projects', [
            'identifier' => 'confidential-vault-project',
            'is_public' => false,
        ]);
    }

    public function test_user_can_create_public_project_with_checked_public_box(): void
    {
        $response = $this->actingAs($this->user)->post('/projects', [
            'name' => 'Community Open Project',
            'description' => 'Public project description',
            'is_public' => '1',
        ]);

        $response->assertRedirect('/projects/community-open-project');
        $this->assertDatabaseHas('projects', [
            'identifier' => 'community-open-project',
            'is_public' => true,
        ]);
    }

    public function test_kanban_board_can_be_rendered(): void
    {
        $project = Project::first();
        $response = $this->actingAs($this->user)->get("/projects/{$project->identifier}/kanban");
        $response->assertStatus(200);
        $response->assertSee('Agile Board');
    }

    public function test_work_packages_screens_can_be_rendered(): void
    {
        $workPackage = WorkPackage::first();
        $this->assertNotNull($workPackage);

        $indexResponse = $this->actingAs($this->user)->get('/work-packages');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($workPackage->subject);

        $showResponse = $this->actingAs($this->user)->get("/work-packages/{$workPackage->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($workPackage->subject);
    }

    public function test_work_package_status_can_be_updated_via_api(): void
    {
        $workPackage = WorkPackage::first();
        $newStatus = WorkPackageStatus::where('id', '!=', $workPackage->status_id)->first();

        $response = $this->actingAs($this->user)->postJson("/work-packages/{$workPackage->id}/status", [
            'status_id' => $newStatus->id,
            'position' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('work_packages', [
            'id' => $workPackage->id,
            'status_id' => $newStatus->id,
        ]);
    }

    public function test_documentation_screen_can_be_rendered_without_auth(): void
    {
        // Docs are public
        $response = $this->get('/docs');
        $response->assertStatus(200);
        $response->assertSee('OpenProject Laravel Documentation');

        $archResponse = $this->get('/docs/architecture');
        $archResponse->assertStatus(200);
        $archResponse->assertSee('Architecture & System Design');
    }

    public function test_private_project_is_not_visible_to_non_member_standard_user(): void
    {
        $nonMember = User::where('email', 'alex@openproject.local')->first();
        $privateProject = Project::where('is_public', false)->first();

        // Project index should not list private project for non-member
        $response = $this->actingAs($nonMember)->get('/projects');
        $response->assertStatus(200);
        $response->assertDontSee($privateProject->name);

        // Accessing private project show directly should yield 403
        $showResponse = $this->actingAs($nonMember)->get("/projects/{$privateProject->identifier}");
        $showResponse->assertStatus(403);

        // Accessing private project kanban should yield 403
        $kanbanResponse = $this->actingAs($nonMember)->get("/projects/{$privateProject->identifier}/kanban");
        $kanbanResponse->assertStatus(403);
    }

    public function test_private_project_is_always_accessible_by_admin(): void
    {
        $admin = User::where('is_admin', true)->first();
        $privateProject = Project::where('is_public', false)->first();

        $response = $this->actingAs($admin)->get('/projects');
        $response->assertStatus(200);
        $response->assertSee($privateProject->name);

        $showResponse = $this->actingAs($admin)->get("/projects/{$privateProject->identifier}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($privateProject->name);
    }

    public function test_private_project_becomes_accessible_when_user_is_assigned_as_member(): void
    {
        $user = User::where('email', 'alex@openproject.local')->first();
        $privateProject = Project::where('is_public', false)->first();
        $role = Role::first();

        // Assign user as project member
        $privateProject->members()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/projects');
        $response->assertStatus(200);
        $response->assertSee($privateProject->name);

        $showResponse = $this->actingAs($user)->get("/projects/{$privateProject->identifier}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($privateProject->name);
    }
}
