<x-layouts.app :title="isset($project) ? $project->name . ' - Work Packages' : 'Work Packages'">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            @if(isset($project))
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">{{ $project->name }}</a>
                    <span>/</span>
                    <span class="text-slate-600 dark:text-slate-300">Work Packages</span>
                </div>
            @endif
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                {{ isset($project) ? "Work Packages in {$project->name}" : 'All Work Packages' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Filter, track, and manage issues, bugs, features, and milestones.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(isset($project))
                <a href="{{ route('projects.kanban', $project) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>Kanban View</span>
                </a>
            @endif

            <button type="button" @click="$dispatch('open-modal', 'quick-create-work-package')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Work Package</span>
            </button>
        </div>
    </div>

    <!-- Filter Toolbar Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-xs">
        <form action="{{ isset($project) ? route('projects.work-packages.index', $project) : route('work-packages.index') }}" method="GET" class="space-y-3">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by subject, description or ID..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if(!isset($project))
                        <select name="project_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select name="type_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                        <option value="">All Types</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" {{ request('type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>

                    <select name="status_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" {{ request('status_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select name="priority_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $pr)
                            <option value="{{ $pr->id }}" {{ request('priority_id') == $pr->id ? 'selected' : '' }}>{{ $pr->name }}</option>
                        @endforeach
                    </select>

                    <select name="assignee_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-3">
                        <option value="">All Assignees</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('assignee_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>

                    <a href="{{ isset($project) ? route('projects.work-packages.index', $project) : route('work-packages.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 text-xs font-medium" title="Clear Filters">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Work Packages Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-16">#ID</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Assignee</th>
                        <th class="py-3 px-4">Progress</th>
                        <th class="py-3 px-4">Due Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($workPackages as $wp)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition group">
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-400 group-hover:text-blue-600">
                                #{{ $wp->id }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-900 dark:text-white group-hover:text-blue-600 transition">
                                    <a href="{{ route('work-packages.show', $wp) }}">
                                        {{ $wp->subject }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $wp->project->name }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge :color="$wp->type->color">{{ $wp->type->name }}</x-badge>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge :color="$wp->status->color">{{ $wp->status->name }}</x-badge>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge :color="$wp->priority->color">{{ $wp->priority->name }}</x-badge>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                @if($wp->assignee)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">
                                            {{ substr($wp->assignee->name, 0, 1) }}
                                        </div>
                                        <span>{{ $wp->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 w-28">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ $wp->done_ratio }}%;"></div>
                                    </div>
                                    <span class="text-[11px] font-mono text-slate-400">{{ $wp->done_ratio }}%</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($wp->due_date)
                                    <span class="{{ $wp->due_date->isPast() ? 'text-rose-500 font-semibold' : 'text-slate-500' }}">
                                        {{ $wp->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('work-packages.show', $wp) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                No work packages found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $workPackages->links() }}
        </div>
    </div>

</x-layouts.app>
