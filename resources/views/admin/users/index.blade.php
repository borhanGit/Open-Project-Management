<x-layouts.app title="User Management & RBAC">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Users & Access Control</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                User Management & Roles
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage global administrator privileges and assign project-level roles across portfolios.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Roles & Permissions Matrix</span>
            </a>

            <button type="button" @click="$dispatch('open-modal', 'create-user-modal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Add New User</span>
            </button>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-xs">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users by name or email address..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="w-full sm:w-auto flex items-center gap-2">
                <select name="role" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                    <option value="">All Privileges</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>System Administrators</option>
                    <option value="standard" {{ request('role') === 'standard' ? 'selected' : '' }}>Standard Users</option>
                </select>

                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-xs font-semibold rounded-xl text-slate-400 hover:text-slate-600 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Global Privilege</th>
                        <th class="py-3 px-4">Project Memberships & Roles</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition group">
                            <!-- User Name & Email -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold text-xs flex items-center justify-center">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Global Privilege Badge -->
                            <td class="py-3.5 px-4">
                                @if($u->is_admin)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        Standard User
                                    </span>
                                @endif
                            </td>

                            <!-- Assigned Projects with Roles -->
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap items-center gap-1.5 max-w-md">
                                    @forelse($u->projectMemberships as $pm)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $pm->project->name }}:</span>
                                            <span class="font-medium text-slate-500">{{ $pm->role->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">No projects assigned yet</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ ucfirst($u->status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2" x-data="{ openAssign: false }">
                                    <!-- Quick Project Assign button -->
                                    <button type="button" @click="$dispatch('open-modal', 'assign-project-modal-{{ $u->id }}')" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Assign Project</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal: Assign Project & Role for this user -->
                        <x-modal name="assign-project-modal-{{ $u->id }}" title="Assign Project & Role to {{ $u->name }}" maxWidth="md">
                            <form action="{{ route('admin.users.projects.assign', $u) }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Target Project *</label>
                                    <select name="project_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                                        @foreach($projects as $proj)
                                            <option value="{{ $proj->id }}">{{ $proj->name }} ({{ $proj->identifier }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Project Role *</label>
                                    <select name="role_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                                        @foreach($roles as $r)
                                            <option value="{{ $r->id }}">{{ $r->name }} - {{ $r->description }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="$dispatch('close-modal', 'assign-project-modal-{{ $u->id }}')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Role</button>
                                </div>
                            </form>
                        </x-modal>

                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                No users found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal: Create New User with Role Selection -->
    <x-modal name="create-user-modal" title="Create New User & Assign Roles" maxWidth="lg">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. David Miller" 
                           class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="david@example.com" 
                           class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Initial Password *</label>
                    <input type="password" name="password" required placeholder="Minimum 8 characters" 
                           class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Status</label>
                    <select name="status" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        <option value="active">Active</option>
                        <option value="locked">Locked</option>
                    </select>
                </div>
            </div>

            <!-- Global Privilege -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" class="mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                    <div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Grant Global Administrator Access</div>
                        <div class="text-[11px] text-slate-400">Can manage users, permissions, system settings, and all projects.</div>
                    </div>
                </label>
            </div>

            <!-- Optional Initial Project & Role Assignment -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Initial Project & Role Assignment (Optional)</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Assign to Project</label>
                        <select name="project_id" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                            <option value="">-- Do not assign immediately --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Role in Project</label>
                        <select name="role_id" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->slug }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'create-user-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Create User</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>
