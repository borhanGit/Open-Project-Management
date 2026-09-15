<x-layouts.app :title="$project->name . ' - Kanban Board'">
    
    <!-- Kanban Header & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600 transition">{{ $project->name }}</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300">Kanban Board</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>Agile Board</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    {{ $project->workPackages()->count() }} Total Items
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('projects.work-packages.index', $project) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>List View</span>
            </a>

            <button type="button" @click="$dispatch('open-modal', 'quick-create-work-package')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-sm shadow-blue-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Task</span>
            </button>
        </div>
    </div>

    <!-- Kanban Board Container (Alpine.js + SortableJS Integration) -->
    <div 
        x-data="kanbanBoard()" 
        class="flex gap-5 overflow-x-auto pb-6 pt-2 custom-scrollbar min-h-[calc(100vh-220px)] items-start"
    >
        @foreach($statuses as $status)
            <div class="w-80 shrink-0 flex flex-col rounded-2xl bg-slate-100/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs max-h-[calc(100vh-200px)]">
                
                <!-- Column Header -->
                <div class="p-4 flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 bg-white/50 dark:bg-slate-800/30 rounded-t-2xl">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status->color }};"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">{{ $status->name }}</h3>
                    </div>
                    <span id="col-count-{{ $status->id }}" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ isset($groupedPackages[$status->id]) ? $groupedPackages[$status->id]->count() : 0 }}
                    </span>
                </div>

                <!-- Column Cards List (Sortable container) -->
                <div 
                    x-init="initColumn($el, {{ $status->id }})"
                    id="column-{{ $status->id }}"
                    class="p-3 space-y-3 overflow-y-auto flex-1 custom-scrollbar min-h-[140px]"
                >
                    @if(isset($groupedPackages[$status->id]))
                        @foreach($groupedPackages[$status->id] as $wp)
                            <div 
                                data-id="{{ $wp->id }}"
                                class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-md hover:border-blue-500/40 transition cursor-grab active:cursor-grabbing group relative"
                            >
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <x-badge :color="$wp->type->color">{{ $wp->type->name }}</x-badge>
                                        <span class="text-xs font-mono text-slate-400">#{{ $wp->id }}</span>
                                    </div>
                                    <x-badge :color="$wp->priority->color">{{ $wp->priority->name }}</x-badge>
                                </div>

                                <a href="{{ route('work-packages.show', $wp) }}" class="text-sm font-semibold text-slate-800 dark:text-slate-100 group-hover:text-blue-600 transition block line-clamp-2">
                                    {{ $wp->subject }}
                                </a>

                                <!-- Progress Bar if ratio > 0 -->
                                @if($wp->done_ratio > 0)
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $wp->done_ratio }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $wp->done_ratio }}%;"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        @if($wp->assignee)
                                            <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold text-[10px] flex items-center justify-center" title="{{ $wp->assignee->name }}">
                                                {{ substr($wp->assignee->name, 0, 1) }}
                                            </div>
                                            <span class="truncate max-w-[90px] text-slate-600 dark:text-slate-300">{{ $wp->assignee->name }}</span>
                                        @else
                                            <span class="italic text-slate-400">Unassigned</span>
                                        @endif
                                    </div>

                                    @if($wp->due_date)
                                        <span class="text-[11px] {{ $wp->due_date->isPast() ? 'text-rose-500 font-semibold' : 'text-slate-400' }}">
                                            {{ $wp->due_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Quick Inline Task Create inside Column -->
                <div class="p-3 border-t border-slate-200/60 dark:border-slate-800/60 bg-white/30 dark:bg-slate-800/20 rounded-b-2xl" x-data="{ adding: false, subject: '' }">
                    <button x-show="!adding" @click="adding = true" type="button" class="w-full py-1.5 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Add item</span>
                    </button>

                    <form x-show="adding" @submit.prevent="quickAdd({{ $status->id }}, subject); subject = ''; adding = false;" class="space-y-2">
                        <input type="text" x-model="subject" placeholder="What needs to be done?" required 
                               class="w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 focus:ring-1 focus:ring-blue-500">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" @click="adding = false" class="px-2.5 py-1 text-[11px] text-slate-500 hover:text-slate-700">Cancel</button>
                            <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold rounded bg-blue-600 text-white hover:bg-blue-500">Save</button>
                        </div>
                    </form>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Script: Sortable.js handler for drag & drop with instant status update -->
    <script>
        function kanbanBoard() {
            return {
                initColumn(el, statusId) {
                    new window.Sortable(el, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        onEnd: (evt) => {
                            const wpId = evt.item.dataset.id;
                            const targetStatusId = evt.to.id.replace('column-', '');
                            const newIndex = evt.newIndex;

                            // Call API to persist new status
                            fetch(`/work-packages/${wpId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    status_id: targetStatusId,
                                    position: newIndex
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                // Update counts on columns
                                document.querySelectorAll('[id^="column-"]').forEach(col => {
                                    const colId = col.id.replace('column-', '');
                                    const countEl = document.getElementById(`col-count-${colId}`);
                                    if (countEl) {
                                        countEl.textContent = col.children.length;
                                    }
                                });
                            })
                            .catch(err => console.error('Failed to move task:', err));
                        }
                    });
                },

                quickAdd(statusId, subject) {
                    if (!subject.trim()) return;

                    fetch(`{{ route('projects.work-packages.store', $project) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            subject: subject,
                            status_id: statusId,
                            type_id: {{ $types->first()->id }},
                            priority_id: {{ $priorities->where('is_default', true)->first()->id ?? $priorities->first()->id }},
                        })
                    })
                    .then(res => {
                        window.location.reload();
                    })
                    .catch(err => console.error('Failed to quick add:', err));
                }
            };
        }
    </script>

</x-layouts.app>
