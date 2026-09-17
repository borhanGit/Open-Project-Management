<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Password - OpenProject</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <a href="{{ route('login') }}" class="inline-flex items-center gap-3 group mb-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </a>

        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Set new password</h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            Please enter your new password to restore access to your account.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white dark:bg-slate-900 py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-slate-200 dark:border-slate-800">
            
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-500 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}" 
                           class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase text-slate-500 mb-1">New Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           placeholder="Minimum 8 characters"
                           class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase text-slate-500 mb-1">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                           placeholder="Re-enter new password"
                           class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        Reset Password & Sign In
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
