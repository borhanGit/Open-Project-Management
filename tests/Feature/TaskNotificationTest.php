<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkPackage;
use App\Models\WorkPackagePriority;
use App\Models\WorkPackageStatus;
use App\Models\WorkPackageType;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $developer;

    protected User $developer2;

    protected Project $project;

    protected WorkPackageType $type;

    protected WorkPackageStatus $status;

    protected WorkPackagePriority $priority;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@openproject.local')->first();
        $this->developer = User::where('email', 'alex@openproject.local')->first();
        $this->developer2 = User::where('email', 'priya@openproject.local')->first();
        $this->project = Project::first();
        $this->type = WorkPackageType::first();
        $this->status = WorkPackageStatus::first();
        $this->priority = WorkPackagePriority::first();
    }

    public function test_creating_task_with_assignee_dispatches_notification(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post('/work-packages', [
            'project_id' => $this->project->id,
            'subject' => 'Implement WebSocket Realtime Bus',
            'description' => 'Realtime notifications and events dispatch.',
            'type_id' => $this->type->id,
            'status_id' => $this->status->id,
            'priority_id' => $this->priority->id,
            'assignee_id' => $this->developer->id,
        ]);

        $response->assertSessionHasNoErrors();
        $workPackage = WorkPackage::where('subject', 'Implement WebSocket Realtime Bus')->first();
        $this->assertNotNull($workPackage);

        Notification::assertSentTo(
            $this->developer,
            TaskAssignedNotification::class,
            function (TaskAssignedNotification $notification) use ($workPackage) {
                return $notification->workPackage->id === $workPackage->id
                    && $notification->assignedBy->id === $this->admin->id;
            }
        );
    }

    public function test_updating_task_assignee_notifies_new_assignee(): void
    {
        Notification::fake();

        $workPackage = WorkPackage::create([
            'project_id' => $this->project->id,
            'subject' => 'Refactor Database Seeders',
            'type_id' => $this->type->id,
            'status_id' => $this->status->id,
            'priority_id' => $this->priority->id,
            'author_id' => $this->admin->id,
            'assignee_id' => $this->developer->id,
        ]);

        // Reassign to developer2
        $response = $this->actingAs($this->admin)->put("/work-packages/{$workPackage->id}", [
            'subject' => $workPackage->subject,
            'status_id' => $this->status->id,
            'priority_id' => $this->priority->id,
            'assignee_id' => $this->developer2->id,
        ]);

        $response->assertSessionHasNoErrors();

        Notification::assertSentTo(
            $this->developer2,
            TaskAssignedNotification::class,
            function (TaskAssignedNotification $notification) use ($workPackage) {
                return $notification->workPackage->id === $workPackage->id;
            }
        );
    }

    public function test_in_app_database_notification_is_stored_and_rendered(): void
    {
        $workPackage = WorkPackage::first();

        // Send real notification to developer
        $this->developer->notify(new TaskAssignedNotification($workPackage, $this->admin));

        $this->assertEquals(1, $this->developer->unreadNotifications()->count());

        // Developer visits /notifications
        $response = $this->actingAs($this->developer)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notifications');
        $response->assertSee($workPackage->subject);
    }

    public function test_user_can_read_and_redirect_to_work_package(): void
    {
        $workPackage = WorkPackage::first();
        $this->developer->notify(new TaskAssignedNotification($workPackage, $this->admin));

        $notification = $this->developer->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($this->developer)->get("/notifications/{$notification->id}/read");
        $response->assertRedirect(route('work-packages.show', $workPackage));

        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertEquals(0, $this->developer->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $workPackage = WorkPackage::first();
        $this->developer->notify(new TaskAssignedNotification($workPackage, $this->admin));

        $this->assertEquals(1, $this->developer->unreadNotifications()->count());

        $response = $this->actingAs($this->developer)->post('/notifications/mark-all-read');
        $response->assertRedirect();

        $this->assertEquals(0, $this->developer->unreadNotifications()->count());
    }
}
