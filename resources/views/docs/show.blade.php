<x-layouts.app title="Documentation & Architecture">
    
    <div class="flex flex-col md:flex-row gap-8 items-start">
        
        <!-- Docs Left Sidebar Navigation -->
        <aside class="w-full md:w-64 shrink-0 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-xs sticky top-20">
            <div class="flex items-center gap-2 mb-4">
                <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Documentation</h3>
            </div>

            <nav class="space-y-1">
                @foreach($navItems as $item)
                    <a href="{{ route('docs.show', $item['slug']) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition {{ $item['active'] ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <span>{{ $item['title'] }}</span>
                        @if($item['active'])
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 space-y-2">
                <div class="font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Open Source Repository</div>
                <div>
                    Files located at: <br>
                    <code class="font-mono text-blue-600 dark:text-blue-400">/docs/{{ $currentPage }}.md</code>
                </div>
            </div>
        </aside>

        <!-- Markdown Document Viewer -->
        <article class="flex-1 w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-10 shadow-xs">
            <div class="prose prose-slate dark:prose-invert max-w-none 
                        prose-headings:font-bold prose-headings:tracking-tight 
                        prose-h1:text-2xl prose-h2:text-xl prose-h3:text-lg 
                        prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:font-mono prose-code:text-xs prose-code:bg-slate-100 dark:prose-code:bg-slate-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md
                        prose-pre:bg-slate-950 prose-pre:border prose-pre:border-slate-800 prose-pre:rounded-xl
                        prose-table:border prose-table:border-slate-200 dark:prose-table:border-slate-800 prose-th:bg-slate-50 dark:prose-th:bg-slate-800/50 prose-th:p-3 prose-td:p-3">
                {!! $content !!}
            </div>
        </article>

    </div>

</x-layouts.app>
