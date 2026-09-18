<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkPackage $workPackage,
        public ?User $assignedBy = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $assignerName = $this->assignedBy ? $this->assignedBy->name : 'System';
        $projectName = $this->workPackage->project?->name ?? 'Project';
        $priorityName = $this->workPackage->priority?->name ?? 'Normal';
        $dueDate = $this->workPackage->due_date ? $this->workPackage->due_date->format('M d, Y') : 'Not specified';
        $url = route('work-packages.show', $this->workPackage);

        return (new MailMessage)
            ->subject("[OpenProject] Assigned to #{$this->workPackage->id}: {$this->workPackage->subject}")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("{$assignerName} has assigned you to work package #{$this->workPackage->id} in \"{$projectName}\".")
            ->line("**Subject:** {$this->workPackage->subject}")
            ->line("**Priority:** {$priorityName} | **Due Date:** {$dueDate}")
            ->action('View Work Package', $url)
            ->line('You can view, update progress, and log time directly on the work package page.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $assignerName = $this->assignedBy ? $this->assignedBy->name : 'System';
        $projectName = $this->workPackage->project?->name ?? 'Project';

        return [
            'work_package_id' => $this->workPackage->id,
            'work_package_subject' => $this->workPackage->subject,
            'project_id' => $this->workPackage->project_id,
            'project_name' => $projectName,
            'project_identifier' => $this->workPackage->project?->identifier,
            'assigned_by_id' => $this->assignedBy?->id,
            'assigned_by_name' => $assignerName,
            'priority_name' => $this->workPackage->priority?->name ?? 'Normal',
            'due_date' => $this->workPackage->due_date?->format('Y-m-d'),
            'action_url' => route('work-packages.show', $this->workPackage),
            'message' => "{$assignerName} assigned you to \"#{$this->workPackage->id} {$this->workPackage->subject}\" in {$projectName}.",
        ];
    }
}
