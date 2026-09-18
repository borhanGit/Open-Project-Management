<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkPackage;
use App\Models\WorkPackageComment;
use App\Models\WorkPackagePriority;
use App\Models\WorkPackageStatus;
use App\Models\WorkPackageType;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkPackageController extends Controller
{
    public function index(Request $request, ?Project $project = null)
    {
        $query = WorkPackage::with(['project', 'type', 'status', 'priority', 'assignee', 'author']);

        if ($project) {
            if (! $project->isVisibleTo($request->user())) {
                abort(403, 'This is a private project. You must be an assigned project member or administrator to access it.');
            }
            $query->where('project_id', $project->id);
        } else {
            $query->whereHas('project', function ($q) use ($request) {
                $q->visibleTo($request->user());
            });

            if ($request->filled('project_id')) {
                $query->where('project_id', $request->input('project_id'));
            }
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->input('type_id'));
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        if ($request->filled('priority_id')) {
            $query->where('priority_id', $request->input('priority_id'));
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->input('assignee_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $sortField = $request->input('sort', 'updated_at');
        $sortOrder = $request->input('order', 'desc');
        $allowedSorts = ['id', 'subject', 'status_id', 'priority_id', 'type_id', 'due_date', 'updated_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $workPackages = $query->paginate(15)->withQueryString();

        $projects = Project::visibleTo($request->user())->where('status', 'active')->orderBy('name')->get();
        $types = WorkPackageType::orderBy('position')->get();
        $statuses = WorkPackageStatus::orderBy('position')->get();
        $priorities = WorkPackagePriority::orderBy('position')->get();
        $users = User::orderBy('name')->get();

        return view('work_packages.index', [
            'project' => $project,
            'workPackages' => $workPackages,
            'projects' => $projects,
            'types' => $types,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'users' => $users,
        ]);
    }

    public function store(Request $request, ?Project $project = null)
    {
        $validated = $request->validate([
            'project_id' => $project ? 'nullable' : 'required|exists:projects,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required|exists:work_package_types,id',
            'status_id' => 'required|exists:work_package_statuses,id',
            'priority_id' => 'required|exists:work_package_priorities,id',
            'assignee_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:work_packages,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'done_ratio' => 'nullable|integer|between:0,100',
        ]);

        $validated['project_id'] = $project ? $project->id : $validated['project_id'];
        $validated['author_id'] = Auth::id() ?? User::first()->id;
        $validated['done_ratio'] = $validated['done_ratio'] ?? 0;

        $workPackage = WorkPackage::create($validated);

        if ($workPackage->assignee_id) {
            $assignee = User::find($workPackage->assignee_id);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($workPackage, Auth::user()));
            }
        }

        return redirect()->route('work-packages.show', $workPackage)
            ->with('success', "Work Package #{$workPackage->id} created successfully.");
    }

    public function show(WorkPackage $workPackage)
    {
        if (! $workPackage->project->isVisibleTo(auth()->user())) {
            abort(403, 'This is a private project. You must be an assigned project member or administrator to access it.');
        }

        $workPackage->load([
            'project',
            'type',
            'status',
            'priority',
            'author',
            'assignee',
            'parent',
            'children.status',
            'children.type',
            'children.assignee',
            'comments.user',
            'timeEntries.user',
        ]);

        $statuses = WorkPackageStatus::orderBy('position')->get();
        $priorities = WorkPackagePriority::orderBy('position')->get();
        $users = User::orderBy('name')->get();

        return view('work_packages.show', [
            'workPackage' => $workPackage,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'users' => $users,
        ]);
    }

    public function update(Request $request, WorkPackage $workPackage)
    {
        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'sometimes|required|exists:work_package_statuses,id',
            'priority_id' => 'sometimes|required|exists:work_package_priorities,id',
            'assignee_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'done_ratio' => 'nullable|integer|between:0,100',
        ]);

        $originalAssigneeId = $workPackage->assignee_id;

        $workPackage->update($validated);

        if ($workPackage->assignee_id && $workPackage->assignee_id != $originalAssigneeId) {
            $assignee = User::find($workPackage->assignee_id);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($workPackage, Auth::user()));
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Work package updated successfully.',
                'workPackage' => $workPackage->fresh(['status', 'priority', 'assignee']),
            ]);
        }

        return back()->with('success', 'Work Package updated successfully.');
    }

    public function updateStatus(Request $request, WorkPackage $workPackage)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:work_package_statuses,id',
            'position' => 'nullable|integer',
        ]);

        $workPackage->status_id = $validated['status_id'];
        if (isset($validated['position'])) {
            $workPackage->position = $validated['position'];
        }

        // Auto-update done_ratio if closed/resolved
        $status = WorkPackageStatus::find($validated['status_id']);
        if ($status && $status->is_closed && $workPackage->done_ratio < 100) {
            $workPackage->done_ratio = 100;
        }

        $workPackage->save();

        return response()->json([
            'success' => true,
            'message' => "Moved to {$status->name}",
            'status' => $status,
        ]);
    }

    public function addComment(Request $request, WorkPackage $workPackage)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        WorkPackageComment::create([
            'work_package_id' => $workPackage->id,
            'user_id' => Auth::id() ?? User::first()->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function logTime(Request $request, WorkPackage $workPackage)
    {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1|max:24',
            'spent_on' => 'required|date|before_or_equal:today',
            'comments' => 'nullable|string|max:255',
        ]);

        TimeEntry::create([
            'project_id' => $workPackage->project_id,
            'work_package_id' => $workPackage->id,
            'user_id' => Auth::id() ?? User::first()->id,
            'hours' => $validated['hours'],
            'spent_on' => $validated['spent_on'],
            'comments' => $validated['comments'] ?? null,
        ]);

        return back()->with('success', "Logged {$validated['hours']} hours.");
    }
}
