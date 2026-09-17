<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password - OpenProject</title>

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
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
        </a>

        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Forgot your password?</h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
            No problem. Enter your registered email address and we will send you a password reset link.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white dark:bg-slate-900 py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-slate-200 dark:border-slate-800">
            
            @if(session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-500 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
                           placeholder="you@example.com" autofocus
                           class="w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        Email Password Reset Link
                    </button>
                </div>

                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition">
                        &larr; Back to Sign In
                    </a>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
