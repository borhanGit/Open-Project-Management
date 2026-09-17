<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkPackageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::visibleTo($request->user())
            ->with(['parent', 'members.user', 'members.role'])
            ->withCount(['workPackages', 'members']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $projects = $query->latest()->paginate(10)->withQueryString();
        $allProjects = Project::visibleTo($request->user())->where('status', 'active')->orderBy('name')->get();

        return view('projects.index', [
            'projects' => $projects,
            'allProjects' => $allProjects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:100|alpha_dash|unique:projects,identifier',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:projects,id',
            'is_public' => 'nullable|boolean',
        ]);

        if (empty($validated['identifier'])) {
            $validated['identifier'] = Str::slug($validated['name']);
            // Ensure unique slug
            $original = $validated['identifier'];
            $count = 1;
            while (Project::where('identifier', $validated['identifier'])->exists()) {
                $validated['identifier'] = "{$original}-{$count}";
                $count++;
            }
        }

        $validated['is_public'] = $request->boolean('is_public');
        $validated['status'] = 'active';

        $project = Project::create($validated);

        // Add creator as Administrator
        $adminRole = Role::where('slug', 'admin')->first();
        if (auth()->check() && $adminRole) {
            $project->members()->create([
                'user_id' => auth()->id(),
                'role_id' => $adminRole->id,
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', "Project '{$project->name}' was created successfully.");
    }

    public function show(Project $project)
    {
        if (! $project->isVisibleTo(auth()->user())) {
            abort(403, 'This is a private project. You must be an assigned project member or administrator to access it.');
        }

        $project->load(['parent', 'subprojects', 'members.user', 'members.role']);

        $statuses = WorkPackageStatus::orderBy('position')->get();
        $statusCounts = [];
        foreach ($statuses as $status) {
            $statusCounts[$status->name] = [
                'count' => $project->workPackages()->where('status_id', $status->id)->count(),
                'color' => $status->color,
            ];
        }

        $recentWorkPackages = $project->workPackages()
            ->with(['type', 'status', 'priority', 'assignee'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $totalEstimated = $project->workPackages()->sum('estimated_hours');
        $totalSpent = $project->timeEntries()->sum('hours');
        $allUsers = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('projects.show', [
            'project' => $project,
            'statusCounts' => $statusCounts,
            'recentWorkPackages' => $recentWorkPackages,
            'totalEstimated' => $totalEstimated,
            'totalSpent' => $totalSpent,
            'allUsers' => $allUsers,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->hasProjectPermission($project, 'edit_project')) {
            abort(403, 'Unauthorized to update project settings.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,archived',
            'is_public' => 'nullable|boolean',
        ]);

        $validated['is_public'] = $request->boolean('is_public');

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', "Project '{$project->name}' was updated successfully.");
    }

    public function addMember(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $project->members()->updateOrCreate(
            ['user_id' => $validated['user_id']],
            ['role_id' => $validated['role_id']]
        );

        return back()->with('success', 'Project member updated successfully.');
    }
}
