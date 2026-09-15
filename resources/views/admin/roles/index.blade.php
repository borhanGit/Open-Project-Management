<x-layouts.app title="Roles & Permissions Matrix">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Administration</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Roles & Permissions</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Roles & Permissions Matrix
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Configure granular access control rules for project memberships.
            </p>
        </div>

        <button type="button" @click="$dispatch('open-modal', 'create-role-modal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create Custom Role</span>
        </button>
    </div>

    <!-- Roles Grid / Permissions Matrix -->
    <div class="space-y-6">
        @foreach($roles as $role)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $role->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">{{ $role->slug }}</span>
                            <span class="text-xs text-slate-400">({{ $role->members_count }} assigned members)</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $role->description }}</p>
                    </div>

                    <button type="button" @click="$dispatch('open-modal', 'edit-role-modal-{{ $role->id }}')" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline">
                        Edit Permissions &rarr;
                    </button>
                </div>

                <!-- Active Permissions Pills -->
                <div class="mt-4">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Granted Permissions</div>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $rolePerms = $role->permissions ?? [];
                        @endphp
                        @if(in_array('all', $rolePerms))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Full Administrative Access (All Permissions)
                            </span>
                        @else
                            @forelse($rolePerms as $pKey)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium border border-blue-100 dark:border-blue-800/40">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $availablePermissions[$pKey] ?? $pKey }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">No permissions assigned.</span>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

            <!-- Edit Role Modal -->
            <x-modal name="edit-role-modal-{{ $role->id }}" title="Edit Permissions for {{ $role->name }}" maxWidth="lg">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Role Name</label>
                        <input type="text" name="name" value="{{ $role->name }}" required 
                               class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Description</label>
                        <input type="text" name="description" value="{{ $role->description }}" 
                               class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-2">Check Allowed Permissions</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            @foreach($availablePermissions as $permKey => $permDesc)
                                <label class="flex items-start gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permKey }}" 
                                           {{ in_array($permKey, $role->permissions ?? []) || in_array('all', $role->permissions ?? []) ? 'checked' : '' }}
                                           class="mt-0.5 rounded text-blue-600">
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $permKey }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $permDesc }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="$dispatch('close-modal', 'edit-role-modal-{{ $role->id }}')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Permissions</button>
                    </div>
                </form>
            </x-modal>
        @endforeach
    </div>

    <!-- Modal: Create Custom Role -->
    <x-modal name="create-role-modal" title="Create Custom Project Role" maxWidth="lg">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Role Name *</label>
                <input type="text" name="name" required placeholder="e.g. QA Automation Engineer" 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Description</label>
                <input type="text" name="description" placeholder="Brief explanation of duties and scopes..." 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-2">Granted Permissions</label>
                <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    @foreach($availablePermissions as $permKey => $permDesc)
                        <label class="flex items-start gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permKey }}" class="mt-0.5 rounded text-blue-600">
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $permKey }}</div>
                                <div class="text-[11px] text-slate-400">{{ $permDesc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'create-role-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Role</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>
