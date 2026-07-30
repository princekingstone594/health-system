<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedFlow') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

<div class="flex min-h-screen">

    {{-- Left panel (branding) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between bg-slate-900 p-12 relative overflow-hidden">
        {{-- Decorative blobs --}}
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-brand-600/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-brand-400/10 blur-3xl"></div>

        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 shadow-lg shadow-brand-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-white">{{ config('app.name', 'MedFlow') }}</span>
            </div>
        </div>

        <div class="relative space-y-6">
            <blockquote class="text-2xl font-semibold text-white leading-snug">
                "Healthcare made simple — for doctors, patients, and clinics."
            </blockquote>
            <div class="flex items-center gap-4">
                <div class="flex -space-x-2">
                    @foreach(['JD', 'SK', 'AM'] as $initials)
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white ring-2 ring-slate-900">{{ $initials }}</div>
                    @endforeach
                </div>
                <p class="text-sm text-slate-400">Trusted by 500+ healthcare professionals</p>
            </div>
        </div>

        <p class="relative text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name', 'MedFlow') }}. All rights reserved.</p>
    </div>

    {{-- Right panel (form) --}}
    <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 bg-surface-muted">

        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <span class="text-lg font-bold text-slate-900">{{ config('app.name', 'MedFlow') }}</span>
        </div>

        <div class="w-full max-w-md">
            <div class="card p-8">
                {{ $slot }}
            </div>
        </div>

    </div>

</div>

</body>
</html>
