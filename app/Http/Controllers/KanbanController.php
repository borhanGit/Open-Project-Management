<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkPackagePriority;
use App\Models\WorkPackageStatus;
use App\Models\WorkPackageType;

class KanbanController extends Controller
{
    public function show(Project $project)
    {
        if (! $project->isVisibleTo(auth()->user())) {
            abort(403, 'This is a private project. You must be an assigned project member or administrator to access it.');
        }

        $statuses = WorkPackageStatus::orderBy('position')->get();
        $types = WorkPackageType::orderBy('position')->get();
        $priorities = WorkPackagePriority::orderBy('position')->get();
        $members = $project->users()->orderBy('name')->get();
        if ($members->isEmpty()) {
            $members = User::all();
        }

        $workPackages = $project->workPackages()
            ->with(['type', 'priority', 'assignee'])
            ->orderBy('position')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Group packages by status id
        $groupedPackages = [];
        foreach ($statuses as $status) {
            $groupedPackages[$status->id] = $workPackages->where('status_id', $status->id)->values();
        }

        $allProjects = Project::visibleTo(auth()->user())->where('status', 'active')->orderBy('name')->get();

        return view('projects.kanban', [
            'project' => $project,
            'statuses' => $statuses,
            'groupedPackages' => $groupedPackages,
            'types' => $types,
            'priorities' => $priorities,
            'members' => $members,
            'allProjects' => $allProjects,
        ]);
    }
}
