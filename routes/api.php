<?php

use App\Http\Controllers\WorkPackageController;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Projects API
    Route::get('projects', function () {
        return response()->json([
            'data' => Project::withCount(['workPackages', 'members'])->get(),
        ]);
    });

    Route::get('projects/{project:identifier}', function (Project $project) {
        return response()->json([
            'data' => $project->load(['subprojects', 'members.user', 'members.role']),
        ]);
    });

    Route::get('projects/{project:identifier}/work-packages', function (Request $request, Project $project) {
        $query = $project->workPackages()->with(['type', 'status', 'priority', 'assignee']);

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    });

    // Work Packages API
    Route::get('work-packages/{workPackage}', function (WorkPackage $workPackage) {
        return response()->json([
            'data' => $workPackage->load(['project', 'type', 'status', 'priority', 'assignee', 'children', 'timeEntries']),
        ]);
    });

    Route::post('work-packages/{workPackage}/status', [WorkPackageController::class, 'updateStatus']);

    Route::post('work-packages/{workPackage}/time-entries', function (Request $request, WorkPackage $workPackage) {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'spent_on' => 'required|date',
            'comments' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $entry = TimeEntry::create([
            'project_id' => $workPackage->project_id,
            'work_package_id' => $workPackage->id,
            'user_id' => $validated['user_id'] ?? (User::first()->id),
            'hours' => $validated['hours'],
            'spent_on' => $validated['spent_on'],
            'comments' => $validated['comments'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $entry,
        ], 201);
    });
});
