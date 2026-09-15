<x-layouts.app :title="$project->name">
    
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
            <a href="{{ route('projects.index') }}" class="hover:text-blue-600 transition">Projects</a>
            <span>/</span>
            <span class="text-slate-600 dark:text-slate-300 font-mono">{{ $project->identifier }}</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $project->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $project->status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                    @if(!$project->is_public)
                        <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Private
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-3xl leading-relaxed">
                    {{ $project->description ?: 'No detailed project description entered.' }}
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('projects.kanban', $project) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>Kanban Board</span>
                </a>

                <a href="{{ route('projects.work-packages.index', $project) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Work Packages</span>
                </a>

                <button type="button" @click="$dispatch('open-modal', 'quick-create-work-package')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Task</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Status Distribution Pills -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 mb-8 shadow-xs">
        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Work Package Breakdown</div>
        <div class="flex flex-wrap items-center gap-3">
            @foreach($statusCounts as $statusName => $info)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $info['color'] }};"></span>
                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $statusName }}:</span>
                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $info['count'] }}</span>
                </div>
            @endforeach

            <div class="ml-auto flex items-center gap-4 text-xs text-slate-500">
                <div>Estimated: <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($totalEstimated, 1) }}h</span></div>
                <div>Spent: <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($totalSpent, 1) }}h</span></div>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Recent Tasks & Project Members -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Recent Work Packages (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Recent Work Packages</h2>
                    <a href="{{ route('projects.work-packages.index', $project) }}" class="text-xs text-blue-600 hover:underline font-medium">View all {{ $project->workPackages()->count() }} &rarr;</a>
                </div>

                @if($recentWorkPackages->isEmpty())
                    <div class="p-8 text-center text-sm text-slate-400">
                        No work packages in this project yet.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentWorkPackages as $wp)
                            <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <x-badge :color="$wp->type->color">{{ $wp->type->name }}</x-badge>
                                        <span class="text-xs font-mono text-slate-400">#{{ $wp->id }}</span>
                                        <a href="{{ route('work-packages.show', $wp) }}" class="text-sm font-semibold text-slate-900 dark:text-slate-100 hover:text-blue-600 transition">
                                            {{ $wp->subject }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-400 flex items-center gap-3">
                                        <span>Assignee: {{ $wp->assignee->name ?? 'Unassigned' }}</span>
                                        <span>•</span>
                                        <span>Progress: {{ $wp->done_ratio }}%</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-badge :color="$wp->priority->color">{{ $wp->priority->name }}</x-badge>
                                    <x-badge :color="$wp->status->color">{{ $wp->status->name }}</x-badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Project Members & Subprojects -->
        <div class="space-y-6">
            
            <!-- Project Members -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Project Members ({{ $project->members->count() }})</h3>
                    <button type="button" @click="$dispatch('open-modal', 'add-member-modal')" class="text-xs text-blue-600 hover:underline font-medium">
                        + Add Member
                    </button>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($project->members as $member)
                        <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold text-xs flex items-center justify-center">
                                    {{ substr($member->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $member->user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $member->user->email }}</div>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $member->role->name }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Subprojects -->
            @if($project->subprojects->isNotEmpty())
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Subprojects</h3>
                    <div class="space-y-2">
                        @foreach($project->subprojects as $sub)
                            <a href="{{ route('projects.show', $sub) }}" class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between hover:border-blue-500/40 transition">
                                <span class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $sub->name }}</span>
                                <span class="text-xs text-blue-600 font-semibold">&rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- Add Member Modal -->
    <x-modal name="add-member-modal" title="Add Team Member to Project" maxWidth="md">
        <form action="{{ route('projects.members.store', $project) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">User *</label>
                <select name="user_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Role *</label>
                <select name="role_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }} - {{ $r->description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'add-member-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Assign Role</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>
