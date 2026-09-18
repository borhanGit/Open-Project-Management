<x-layouts.app title="Notifications">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                <span>Account</span>
                <span>&bull;</span>
                <span>Activity</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Notifications
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Stay updated with work packages, team assignments, and project mentions.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications->isNotEmpty())
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Mark all as read</span>
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-medium flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        @if($notifications->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No notifications yet</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    You're all caught up! When team members assign you to work packages, you will see notifications here.
                </p>
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $data = $notification->data;
                    @endphp
                    <div class="p-5 flex items-start justify-between gap-4 transition {{ $isUnread ? 'bg-blue-50/40 dark:bg-blue-950/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                        <div class="flex items-start gap-3.5">
                            <div class="mt-1 shrink-0">
                                @if($isUnread)
                                    <span class="flex h-2.5 w-2.5 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"></span>
                                    </span>
                                @else
                                    <span class="inline-block h-2 w-2 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <a href="{{ route('notifications.read', $notification->id) }}" class="text-sm font-semibold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $data['message'] ?? 'You have a new task notification.' }}
                                </a>

                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400 pt-0.5">
                                    @if(!empty($data['project_name']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $data['project_name'] }}
                                        </span>
                                    @endif

                                    @if(!empty($data['priority_name']))
                                        <span class="text-[11px] text-slate-400">
                                            Priority: <strong class="text-slate-700 dark:text-slate-300">{{ $data['priority_name'] }}</strong>
                                        </span>
                                    @endif

                                    <span>&bull;</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <a href="{{ route('notifications.read', $notification->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white shadow-xs transition">
                                <span>Open</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>
