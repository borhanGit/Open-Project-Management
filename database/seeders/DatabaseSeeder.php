<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Role;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkPackage;
use App\Models\WorkPackageComment;
use App\Models\WorkPackagePriority;
use App\Models\WorkPackageStatus;
use App\Models\WorkPackageType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full administrative control over the project and settings.',
            'permissions' => ['all'],
        ]);

        $pmRole = Role::create([
            'name' => 'Project Manager',
            'slug' => 'project-manager',
            'description' => 'Can manage work packages, milestones, team members, and timelines.',
            'permissions' => ['view_project', 'edit_project', 'manage_tasks', 'manage_members', 'log_time'],
        ]);

        $devRole = Role::create([
            'name' => 'Developer',
            'slug' => 'developer',
            'description' => 'Can work on assigned packages, change statuses, and log spent time.',
            'permissions' => ['view_project', 'manage_tasks', 'log_time'],
        ]);

        $viewerRole = Role::create([
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only access to tasks and project timelines.',
            'permissions' => ['view_project'],
        ]);

        // 2. Types
        $taskType = WorkPackageType::create(['name' => 'Task', 'color' => '#3b82f6', 'icon' => 'check-square', 'is_milestone' => false, 'position' => 1]);
        $bugType = WorkPackageType::create(['name' => 'Bug', 'color' => '#ef4444', 'icon' => 'alert-circle', 'is_milestone' => false, 'position' => 2]);
        $featureType = WorkPackageType::create(['name' => 'Feature', 'color' => '#10b981', 'icon' => 'sparkles', 'is_milestone' => false, 'position' => 3]);
        $milestoneType = WorkPackageType::create(['name' => 'Milestone', 'color' => '#8b5cf6', 'icon' => 'flag', 'is_milestone' => true, 'position' => 4]);

        // 3. Statuses
        $statusNew = WorkPackageStatus::create(['name' => 'New', 'color' => '#64748b', 'is_closed' => false, 'is_default' => true, 'position' => 1]);
        $statusInProgress = WorkPackageStatus::create(['name' => 'In Progress', 'color' => '#3b82f6', 'is_closed' => false, 'is_default' => false, 'position' => 2]);
        $statusInReview = WorkPackageStatus::create(['name' => 'In Review', 'color' => '#f59e0b', 'is_closed' => false, 'is_default' => false, 'position' => 3]);
        $statusResolved = WorkPackageStatus::create(['name' => 'Resolved', 'color' => '#10b981', 'is_closed' => false, 'is_default' => false, 'position' => 4]);
        $statusClosed = WorkPackageStatus::create(['name' => 'Closed', 'color' => '#475569', 'is_closed' => true, 'is_default' => false, 'position' => 5]);

        // 4. Priorities
        $priorityLow = WorkPackagePriority::create(['name' => 'Low', 'color' => '#94a3b8', 'is_default' => false, 'position' => 1]);
        $priorityNormal = WorkPackagePriority::create(['name' => 'Normal', 'color' => '#3b82f6', 'is_default' => true, 'position' => 2]);
        $priorityHigh = WorkPackagePriority::create(['name' => 'High', 'color' => '#f97316', 'is_default' => false, 'position' => 3]);
        $priorityUrgent = WorkPackagePriority::create(['name' => 'Urgent', 'color' => '#ef4444', 'is_default' => false, 'position' => 4]);
        $priorityImmediate = WorkPackagePriority::create(['name' => 'Immediate', 'color' => '#b91c1c', 'is_default' => false, 'position' => 5]);

        // 5. Users
        $admin = User::create([
            'name' => 'Borhan Uddin (Admin)',
            'email' => 'admin@openproject.local',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $pm = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@openproject.local',
            'password' => Hash::make('password'),
        ]);

        $dev1 = User::create([
            'name' => 'Alex Chen',
            'email' => 'alex@openproject.local',
            'password' => Hash::make('password'),
        ]);

        $dev2 = User::create([
            'name' => 'Priya Patel',
            'email' => 'priya@openproject.local',
            'password' => Hash::make('password'),
        ]);

        // 6. Projects
        $project1 = Project::create([
            'name' => 'Cloud Infrastructure & Kubernetes Modernization',
            'identifier' => 'cloud-infrastructure',
            'description' => 'Migrate legacy microservices to high-availability multi-region Kubernetes clusters with automated GitOps CI/CD pipelines.',
            'status' => 'active',
            'is_public' => true,
        ]);

        $project2 = Project::create([
            'name' => 'Design System & Blade UI Refresh',
            'identifier' => 'design-system',
            'description' => 'Create a world-class, accessible, and responsive component library using Laravel Blade, Tailwind CSS, and Alpine.js.',
            'status' => 'active',
            'is_public' => true,
        ]);

        $project3 = Project::create([
            'name' => 'NextGen Mobile Client (iOS & Android)',
            'identifier' => 'mobile-client',
            'description' => 'Native mobile application delivering real-time work package synchronization and offline mode capabilities.',
            'status' => 'active',
            'is_public' => false,
        ]);

        // Memberships
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $admin->id, 'role_id' => $adminRole->id]);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $pm->id, 'role_id' => $pmRole->id]);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $dev1->id, 'role_id' => $devRole->id]);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $dev2->id, 'role_id' => $devRole->id]);

        ProjectMember::create(['project_id' => $project2->id, 'user_id' => $admin->id, 'role_id' => $adminRole->id]);
        ProjectMember::create(['project_id' => $project2->id, 'user_id' => $dev1->id, 'role_id' => $devRole->id]);

        // 7. Work Packages for Project 1
        $milestone1 = WorkPackage::create([
            'project_id' => $project1->id,
            'type_id' => $milestoneType->id,
            'status_id' => $statusInProgress->id,
            'priority_id' => $priorityUrgent->id,
            'author_id' => $pm->id,
            'assignee_id' => $admin->id,
            'subject' => 'Milestone 1: Production Cluster Provisioning & Ingress Setup',
            'description' => 'Zero-downtime cluster bootstrap across US-East and EU-West regions.',
            'start_date' => now()->subDays(5),
            'due_date' => now()->addDays(10),
            'estimated_hours' => 80.0,
            'done_ratio' => 60,
            'position' => 1,
        ]);

        $wp1 = WorkPackage::create([
            'project_id' => $project1->id,
            'type_id' => $taskType->id,
            'status_id' => $statusResolved->id,
            'priority_id' => $priorityHigh->id,
            'author_id' => $pm->id,
            'assignee_id' => $dev1->id,
            'subject' => 'Configure Terraform modules for VPC peering & NAT Gateways',
            'description' => 'Automate network layout with multi-AZ redundancy and security groups.',
            'start_date' => now()->subDays(10),
            'due_date' => now()->subDays(2),
            'estimated_hours' => 24.0,
            'done_ratio' => 100,
            'position' => 2,
        ]);

        $wp2 = WorkPackage::create([
            'project_id' => $project1->id,
            'type_id' => $taskType->id,
            'status_id' => $statusInProgress->id,
            'priority_id' => $priorityNormal->id,
            'author_id' => $admin->id,
            'assignee_id' => $dev2->id,
            'subject' => 'Deploy ArgoCD GitOps controller & Helm chart repositories',
            'description' => 'Setup automated repository synchronizations and webhooks.',
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(5),
            'estimated_hours' => 16.0,
            'done_ratio' => 45,
            'position' => 3,
        ]);

        $wp3 = WorkPackage::create([
            'project_id' => $project1->id,
            'type_id' => $bugType->id,
            'status_id' => $statusInReview->id,
            'priority_id' => $priorityUrgent->id,
            'author_id' => $dev1->id,
            'assignee_id' => $admin->id,
            'subject' => 'Fix SSL cert manager ACME DNS challenge timeout on Cloudflare',
            'description' => 'Cert-manager renewal fails intermittently when propagating TXT records.',
            'start_date' => now()->subDays(1),
            'due_date' => now()->addDays(1),
            'estimated_hours' => 8.0,
            'done_ratio' => 80,
            'position' => 4,
        ]);

        $wp4 = WorkPackage::create([
            'project_id' => $project1->id,
            'type_id' => $featureType->id,
            'status_id' => $statusNew->id,
            'priority_id' => $priorityNormal->id,
            'author_id' => $pm->id,
            'assignee_id' => $dev1->id,
            'subject' => 'Prometheus & Grafana unified observability dashboards',
            'description' => 'Create golden signals dashboards for latency, traffic, errors, and saturation.',
            'start_date' => now()->addDays(2),
            'due_date' => now()->addDays(14),
            'estimated_hours' => 32.0,
            'done_ratio' => 0,
            'position' => 5,
        ]);

        // Work Packages for Project 2
        WorkPackage::create([
            'project_id' => $project2->id,
            'type_id' => $featureType->id,
            'status_id' => $statusInProgress->id,
            'priority_id' => $priorityHigh->id,
            'author_id' => $admin->id,
            'assignee_id' => $dev1->id,
            'subject' => 'Build reactive Drag-and-Drop Kanban Board with Alpine.js & Sortable',
            'description' => 'Real-time column card dragging with instant API persistence and status updates.',
            'start_date' => now()->subDays(2),
            'due_date' => now()->addDays(3),
            'estimated_hours' => 20.0,
            'done_ratio' => 70,
            'position' => 1,
        ]);

        WorkPackage::create([
            'project_id' => $project2->id,
            'type_id' => $taskType->id,
            'status_id' => $statusResolved->id,
            'priority_id' => $priorityNormal->id,
            'author_id' => $admin->id,
            'assignee_id' => $admin->id,
            'subject' => 'Modern Tailwind CSS 4 Design Tokens & Typography hierarchy',
            'description' => 'Curated dark/light theme tokens and accessible contrast ratios.',
            'start_date' => now()->subDays(4),
            'due_date' => now()->subDays(1),
            'estimated_hours' => 12.0,
            'done_ratio' => 100,
            'position' => 2,
        ]);

        // Time Entries & Comments
        TimeEntry::create([
            'project_id' => $project1->id,
            'work_package_id' => $wp1->id,
            'user_id' => $dev1->id,
            'hours' => 8.5,
            'spent_on' => now()->subDays(5),
            'comments' => 'Created VPC subnets and NAT route tables.',
        ]);

        TimeEntry::create([
            'project_id' => $project1->id,
            'work_package_id' => $wp1->id,
            'user_id' => $dev1->id,
            'hours' => 7.0,
            'spent_on' => now()->subDays(3),
            'comments' => 'Finalized security group rules and tested peering latency.',
        ]);

        TimeEntry::create([
            'project_id' => $project1->id,
            'work_package_id' => $wp2->id,
            'user_id' => $dev2->id,
            'hours' => 6.0,
            'spent_on' => now()->subDays(1),
            'comments' => 'Configured ArgoCD helm application manifests.',
        ]);

        WorkPackageComment::create([
            'work_package_id' => $wp3->id,
            'user_id' => $admin->id,
            'content' => 'I verified that increasing the Cloudflare DNS propagation timeout from 60s to 180s resolves the ACME TXT verification delay. Patch ready for review.',
        ]);

        WorkPackageComment::create([
            'work_package_id' => $wp3->id,
            'user_id' => $dev1->id,
            'content' => 'Tested in staging, SSL certificate issued instantly! Great job.',
        ]);
    }
}
