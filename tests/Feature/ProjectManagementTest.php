<?php

namespace Tests\Feature;

use App\Models\Project;
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
}
