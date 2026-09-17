<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\WorkPackage;
use App\Models\WorkPackageStatus;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalProjects = Project::visibleTo($user)->where('status', 'active')->count();
        $totalWorkPackages = WorkPackage::whereHas('project', fn ($q) => $q->visibleTo($user))->count();

        $closedStatusIds = WorkPackageStatus::where('is_closed', true)->pluck('id');
        $openWorkPackagesCount = WorkPackage::whereHas('project', fn ($q) => $q->visibleTo($user))->whereNotIn('status_id', $closedStatusIds)->count();

        $inProgressStatus = WorkPackageStatus::where('name', 'In Progress')->first();
        $inProgressCount = $inProgressStatus ? WorkPackage::whereHas('project', fn ($q) => $q->visibleTo($user))->where('status_id', $inProgressStatus->id)->count() : 0;

        $totalLoggedHours = TimeEntry::whereHas('project', fn ($q) => $q->visibleTo($user))->sum('hours');

        $myAssignedTasks = $user ? WorkPackage::with(['project', 'type', 'status', 'priority'])
            ->where('assignee_id', $user->id)
            ->whereNotIn('status_id', $closedStatusIds)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get() : collect();

        $recentWorkPackages = WorkPackage::whereHas('project', fn ($q) => $q->visibleTo($user))
            ->with(['project', 'type', 'status', 'priority', 'assignee'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        $projects = Project::visibleTo($user)
            ->withCount(['workPackages', 'members'])
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'totalProjects' => $totalProjects,
            'totalWorkPackages' => $totalWorkPackages,
            'openWorkPackagesCount' => $openWorkPackagesCount,
            'inProgressCount' => $inProgressCount,
            'totalLoggedHours' => $totalLoggedHours,
            'myAssignedTasks' => $myAssignedTasks,
            'recentWorkPackages' => $recentWorkPackages,
            'projects' => $projects,
        ]);
    }
}
