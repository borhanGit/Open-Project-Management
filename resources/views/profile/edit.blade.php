<x-layouts.app title="Profile Settings">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
            <span>Settings</span>
            <span>&bull;</span>
            <span>Security</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Account & Security Settings
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Manage your personal profile details, contact email address, and account credentials.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-medium flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: User Summary Card -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm text-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white text-2xl font-bold flex items-center justify-center mx-auto shadow-lg shadow-blue-500/20 mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>

                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    @if($user->isAdmin())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Global Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            Standard User
                        </span>
                    @endif

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        Active Account
                    </span>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 text-left space-y-3 text-xs">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Member Since</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Assigned Tasks</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $user->assignedWorkPackages()->count() }} packages</span>
                    </div>
                </div>
            </div>

            <!-- Security Recommendation Box -->
            <div class="bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/40 rounded-2xl p-5 text-xs text-slate-600 dark:text-slate-300 space-y-2">
                <div class="font-semibold text-blue-900 dark:text-blue-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Password Guidelines
                </div>
                <p>Use at least 8 characters with a mix of letters, numbers, and symbols. If you were provided an initial temporary password by your administrator, please change it below.</p>
            </div>
        </div>

        <!-- Right Column: Edit Forms -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Section 1: Personal Profile Details -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Profile Information</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Update your account's display name and primary email address.
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Full Name *</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                               class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Email Address *</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                               class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                            Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 2: Update Password -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Change Password</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Ensure your account stays secure by using a strong, unique password.
                    </p>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Current Password *</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                               placeholder="Enter your current password"
                               class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('current_password') border-rose-500 @enderror">
                        @error('current_password')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">New Password *</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                   placeholder="Minimum 8 characters"
                                   class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('password') border-rose-500 @enderror">
                            @error('password')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Confirm New Password *</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                   placeholder="Re-enter new password"
                                   class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</x-layouts.app>
