<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // Master list of system permissions
    public static array $availablePermissions = [
        'view_project' => 'View project timeline, overview, and member roster',
        'edit_project' => 'Edit project settings and descriptions',
        'manage_members' => 'Add, remove, or change team member roles',
        'manage_tasks' => 'Create, edit, assign, and delete work packages',
        'change_status' => 'Move tasks through Kanban workflow statuses',
        'log_time' => 'Log and submit billable/spent hours',
        'view_time_logs' => 'View team-wide time tracking logs',
    ];

    public function index()
    {
        $roles = Role::withCount('members')->orderBy('id')->get();

        return view('admin.roles.index', [
            'roles' => $roles,
            'availablePermissions' => self::$availablePermissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return back()->with('success', "Role '{$role->name}' was created successfully.");
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role->name = $validated['name'];
        $role->description = $validated['description'] ?? null;
        $role->permissions = $validated['permissions'] ?? [];
        $role->save();

        return back()->with('success', "Role '{$role->name}' permissions updated.");
    }
}
