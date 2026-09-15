<x-layouts.app :title="'#' . $workPackage->id . ' ' . $workPackage->subject">
    
    <!-- Breadcrumbs & Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
            <a href="{{ route('projects.show', $workPackage->project) }}" class="hover:text-blue-600 transition">{{ $workPackage->project->name }}</a>
            <span>/</span>
            <a href="{{ route('projects.work-packages.index', $workPackage->project) }}" class="hover:text-blue-600 transition">Work Packages</a>
            <span>/</span>
            <span class="text-slate-600 dark:text-slate-300 font-mono">#{{ $workPackage->id }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <x-badge :color="$workPackage->type->color">{{ $workPackage->type->name }}</x-badge>
                    <span class="text-xs font-mono font-bold text-slate-400">#{{ $workPackage->id }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ $workPackage->subject }}
                </h1>
                <div class="text-xs text-slate-400 flex items-center gap-2 pt-1">
                    <span>Authored by <span class="font-medium text-slate-600 dark:text-slate-300">{{ $workPackage->author->name }}</span></span>
                    <span>•</span>
                    <span>{{ $workPackage->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="$dispatch('open-modal', 'log-time-modal')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Log Time</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2 Column Layout: Details / Comments & Metadata Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Description, Subtasks, Time Entries, Comments (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Description Box -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Description</h3>
                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $workPackage->description ?: 'No detailed description provided for this work package.' }}
                </div>
            </div>

            <!-- Subtasks Section -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Child Work Packages ({{ $workPackage->children->count() }})</h3>
                    <button type="button" @click="$dispatch('open-modal', 'add-subtask-modal')" class="text-xs text-blue-600 hover:underline font-medium">
                        + Add Subtask
                    </button>
                </div>

                @if($workPackage->children->isEmpty())
                    <p class="text-xs text-slate-400 italic">No child tasks or subtasks linked.</p>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($workPackage->children as $child)
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-badge :color="$child->type->color">{{ $child->type->name }}</x-badge>
                                    <a href="{{ route('work-packages.show', $child) }}" class="text-xs font-medium text-slate-800 dark:text-slate-200 hover:text-blue-600">
                                        #{{ $child->id }} {{ $child->subject }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-badge :color="$child->status->color">{{ $child->status->name }}</x-badge>
                                    <span class="text-xs text-slate-400">{{ $child->assignee->name ?? 'Unassigned' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Spent Time Entries -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Spent Time</h3>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Total: <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($workPackage->total_spent_hours, 1) }} hours</span>
                            @if($workPackage->estimated_hours)
                                / {{ number_format($workPackage->estimated_hours, 1) }}h estimated
                            @endif
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('open-modal', 'log-time-modal')" class="text-xs text-blue-600 hover:underline font-medium">
                        + Log Time
                    </button>
                </div>

                @if($workPackage->timeEntries->isEmpty())
                    <p class="text-xs text-slate-400 italic">No time logs recorded yet.</p>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($workPackage->timeEntries as $te)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div class="space-y-0.5">
                                    <div class="font-medium text-slate-800 dark:text-slate-200">{{ $te->comments ?: 'Work on task' }}</div>
                                    <div class="text-slate-400">{{ $te->user->name }} • {{ $te->spent_on->format('M d, Y') }}</div>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300 font-mono">{{ number_format($te->hours, 1) }}h</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Activity & Discussion -->
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Discussion & History ({{ $workPackage->comments->count() }})</h3>
                
                <!-- Comments Stream -->
                <div class="space-y-4 mb-6">
                    @forelse($workPackage->comments as $c)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ substr($c->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-800/60 rounded-xl p-4 border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $c->user->name }}</span>
                                    <span class="text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ $c->content }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No comments yet. Start the conversation below.</p>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('work-packages.comments.store', $workPackage) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="content" rows="3" required placeholder="Leave a comment or progress update..." 
                              class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 p-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right: Metadata & Attributes Form (1 Col) -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Attributes</h3>

                <form action="{{ route('work-packages.update', $workPackage) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Status</label>
                        <select name="status_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-3 font-semibold">
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ $workPackage->status_id == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Priority</label>
                        <select name="priority_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-3">
                            @foreach($priorities as $pr)
                                <option value="{{ $pr->id }}" {{ $workPackage->priority_id == $pr->id ? 'selected' : '' }}>
                                    {{ $pr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Assignee</label>
                        <select name="assignee_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-3">
                            <option value="">Unassigned</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $workPackage->assignee_id == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Progress % -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold uppercase text-slate-500 mb-1">
                            <span>Progress</span>
                            <span>{{ $workPackage->done_ratio }}%</span>
                        </div>
                        <input type="range" name="done_ratio" min="0" max="100" step="10" value="{{ $workPackage->done_ratio }}" 
                               onchange="this.form.submit()" class="w-full accent-blue-600">
                    </div>

                    <!-- Dates -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="block text-slate-400 font-semibold uppercase text-[10px]">Start Date</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                {{ $workPackage->start_date ? $workPackage->start_date->format('M d, Y') : 'None' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-semibold uppercase text-[10px]">Due Date</span>
                            <span class="font-medium {{ ($workPackage->due_date && $workPackage->due_date->isPast()) ? 'text-rose-500 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $workPackage->due_date ? $workPackage->due_date->format('M d, Y') : 'None' }}
                            </span>
                        </div>
                    </div>

                    <!-- Estimated Hours -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-400 uppercase text-[10px] font-semibold">Estimated Hours</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">
                            {{ $workPackage->estimated_hours ? number_format($workPackage->estimated_hours, 1) . 'h' : '-' }}
                        </span>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Log Time Modal -->
    <x-modal name="log-time-modal" title="Log Spent Time on #{{ $workPackage->id }}" maxWidth="md">
        <form action="{{ route('work-packages.time.store', $workPackage) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Hours *</label>
                <input type="number" step="0.25" min="0.25" max="24" name="hours" required placeholder="e.g. 2.5" 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Date *</label>
                <input type="date" name="spent_on" value="{{ date('Y-m-d') }}" required 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Activity / Notes</label>
                <input type="text" name="comments" placeholder="Describe the work done..." 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'log-time-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Log</button>
            </div>
        </form>
    </x-modal>

    <!-- Add Subtask Modal -->
    <x-modal name="add-subtask-modal" title="Add Subtask to #{{ $workPackage->id }}" maxWidth="lg">
        <form action="{{ route('work-packages.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="project_id" value="{{ $workPackage->project_id }}">
            <input type="hidden" name="parent_id" value="{{ $workPackage->id }}">

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Subject *</label>
                <input type="text" name="subject" required placeholder="Subtask summary" 
                       class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Type *</label>
                    <select name="type_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        @foreach(\App\Models\WorkPackageType::orderBy('position')->get() as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Status *</label>
                    <select name="status_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Priority *</label>
                    <select name="priority_id" required class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        @foreach($priorities as $pr)
                            <option value="{{ $pr->id }}" {{ $pr->is_default ? 'selected' : '' }}>{{ $pr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Assignee</label>
                    <select name="assignee_id" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        <option value="">Unassigned</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="$dispatch('close-modal', 'add-subtask-modal')" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-sm">Save Subtask</button>
            </div>
        </form>
    </x-modal>

</x-layouts.app>
