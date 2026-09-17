<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserCreatedMail;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['projectMemberships.project', 'projectMemberships.role']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->input('role') === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->input('role') === 'standard') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();
        $projects = Project::where('status', 'active')->orderBy('name')->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'is_admin' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,locked'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'role_id' => ['nullable', 'required_with:project_id', 'exists:roles,id'],
            'send_welcome_email' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $request->boolean('is_admin', false),
            'status' => $validated['status'],
        ]);

        // If a project and role were selected during creation, assign immediately
        if (! empty($validated['project_id']) && ! empty($validated['role_id'])) {
            ProjectMember::create([
                'project_id' => $validated['project_id'],
                'user_id' => $user->id,
                'role_id' => $validated['role_id'],
            ]);
        }

        $sendWelcomeEmail = $request->boolean('send_welcome_email', true);
        if ($sendWelcomeEmail) {
            try {
                Mail::to($user->email)->send(new UserCreatedMail(
                    user: $user,
                    plainPassword: $validated['password'],
                    loginUrl: route('login'),
                ));
            } catch (\Throwable $e) {
                Log::warning("Failed to dispatch welcome email to {$user->email}: ".$e->getMessage());
            }
        }

        $notice = "User '{$user->name}' was created successfully".($sendWelcomeEmail ? ' and a welcome email was sent.' : '.');

        return redirect()->route('admin.users.index')->with('success', $notice);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'is_admin' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,locked'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_admin = $request->boolean('is_admin', false);
        $user->status = $validated['status'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' was updated successfully.");
    }

    public function assignProject(Request $request, User $user)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        ProjectMember::updateOrCreate(
            [
                'project_id' => $validated['project_id'],
                'user_id' => $user->id,
            ],
            [
                'role_id' => $validated['role_id'],
            ]
        );

        return back()->with('success', "Assigned role to {$user->name}.");
    }

    public function removeProject(User $user, Project $project)
    {
        ProjectMember::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->delete();

        return back()->with('success', "Removed {$user->name} from project.");
    }
}
