<x-layouts.app title="Dashboard">
    
    <!-- Hero Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Welcome back, {{ $user->name ?? 'Team Member' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Here is an overview of ongoing sprints, work packages, and logged velocity across projects.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span>Browse Projects</span>
            </a>
            <button type="button" @click="$dispatch('open-modal', 'quick-create-work-package')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Work Package</span>
            </button>
        </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Active Projects -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-blue-500/30 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Projects</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $totalProjects }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Healthy</span>
            </div>
        </div>

        <!-- Open Work Packages -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-indigo-500/30 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Work Packages</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $openWorkPackagesCount }}</span>
                <span class="text-xs text-slate-400">of {{ $totalWorkPackages }} total</span>
            </div>
        </div>

        <!-- In Progress -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-amber-500/30 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">In Progress</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $inProgressCount }}</span>
                <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Sprint Active</span>
            </div>
        </div>

        <!-- Logged Time -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-emerald-500/30 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Logged Time</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($totalLoggedHours, 1) }}</span>
                <span class="text-xs text-slate-400">hours</span>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Assigned to Me & Recent Projects -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Left: My Work Packages (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Assigned to You</h2>
                    </div>
                    <a href="{{ route('work-packages.index', ['assignee_id' => $user->id ?? 1]) }}" class="text-xs text-blue-600 hover:underline font-medium">View all &rarr;</a>
                </div>

                @if($myAssignedTasks->isEmpty())
                    <div class="p-8 text-center text-sm text-slate-400">
                        No active work packages currently assigned to you.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($myAssignedTasks as $wp)
                            <a href="{{ route('work-packages.show', $wp) }}" class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">
                                <div class="space-y-1 pr-4">
                                    <div class="flex items-center gap-2">
                                        <x-badge :color="$wp->type->color">{{ $wp->type->name }}</x-badge>
                                        <span class="text-xs font-mono text-slate-400">#{{ $wp->id }}</span>
                                        <span class="text-xs text-slate-400 truncate max-w-[140px]">{{ $wp->project->name }}</span>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 transition">
                                        {{ $wp->subject }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <x-badge :color="$wp->status->color">{{ $wp->status->name }}</x-badge>
                                    @if($wp->due_date)
                                        <span class="text-xs {{ $wp->due_date->isPast() ? 'text-rose-500 font-semibold' : 'text-slate-400' }}">
                                            Due {{ $wp->due_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Work Packages Across All Projects -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Recent Work Packages & Activity</h2>
                    <a href="{{ route('work-packages.index') }}" class="text-xs text-blue-600 hover:underline font-medium">All tasks &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($recentWorkPackages as $rwp)
                        <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <x-badge :color="$rwp->type->color">{{ $rwp->type->name }}</x-badge>
                                    <a href="{{ route('work-packages.show', $rwp) }}" class="text-sm font-medium text-slate-900 dark:text-slate-100 hover:text-blue-600 transition">
                                        {{ $rwp->subject }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <span>{{ $rwp->project->name }}</span>
                                    <span>•</span>
                                    <span>Assignee: {{ $rwp->assignee->name ?? 'Unassigned' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-badge :color="$rwp->priority->color">{{ $rwp->priority->name }}</x-badge>
                                <x-badge :color="$rwp->status->color">{{ $rwp->status->name }}</x-badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right: Projects Quick Access (1 Col) -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Active Projects</h3>
                    <a href="{{ route('projects.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Manage</a>
                </div>

                <div class="space-y-3">
                    @foreach($projects as $p)
                        <div class="p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 hover:border-blue-500/40 transition">
                            <div class="flex items-start justify-between">
                                <a href="{{ route('projects.show', $p) }}" class="text-sm font-semibold text-slate-800 dark:text-slate-200 hover:text-blue-600">
                                    {{ $p->name }}
                                </a>
                            </div>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $p->description }}</p>
                            
                            <div class="mt-3 pt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 text-slate-500">
                                    <span>{{ $p->work_packages_count }} tasks</span>
                                    <span>•</span>
                                    <span>{{ $p->members_count }} members</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('projects.kanban', $p) }}" class="p-1 rounded text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="Open Kanban">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                    <button type="button" @click="$dispatch('open-modal', 'create-project-modal')" class="w-full py-2 text-xs font-semibold rounded-lg border border-dashed border-slate-300 dark:border-slate-700 hover:border-blue-500 hover:text-blue-600 transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create New Project</span>
                    </button>
                </div>
            </div>

            <!-- Open Source Docs Banner -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-md">
                <div class="flex items-center gap-2 mb-2">
                    <span class="p-1.5 rounded-lg bg-white/20 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <h4 class="font-bold text-sm">Maintained Documentation</h4>
                </div>
                <p class="text-xs text-blue-100 leading-relaxed mb-4">
                    Explore system architecture, schema diagrams, REST API specs, and contribution guidelines in our embedded Markdown browser.
                </p>
                <a href="{{ route('docs.show', 'index') }}" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-white text-blue-600 hover:bg-blue-50 transition shadow-xs">
                    Read Documentation &rarr;
                </a>
            </div>

        </div>

    </div>

    <!-- Create Project Modal -->
    <x-modal name="create-project-modal" title="Create New Project" maxWidth="xl">
        <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Project Name *</label>
                <input type="text" name="name" required placeholder="e.g. NextGen Microservices Platform" 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Identifier (Slug)</label>
                <input type="text" name="identifier" placeholder="auto-generated from name if left blank" 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Parent Project (Optional)</label>
                <select name="parent_id" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                    <option value="">-- None (Top Level Project) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Explain the high-level roadmap and scope..." 
                          class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_public" id="is_public" value="1" checked class="rounded text-blue-600">
                <label for="is_public" class="text-xs text-slate-700 dark:text-slate-300">Public project (visible to all team members)</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'create-project-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Project</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>
