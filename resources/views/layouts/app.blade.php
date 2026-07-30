<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedFlow') }} — {{ $header ?? 'Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden bg-surface-muted">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         style="display: none;">
    </div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 shadow-sidebar transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">

        @include('layouts.partials.sidebar')

    </aside>

    {{-- Main area --}}
    <div class="flex flex-1 flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-surface-border bg-white px-4 sm:px-6">

            <div class="flex items-center gap-4">
                {{-- Mobile menu toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <div>
                    @isset($header)
                        <div class="page-title text-lg sm:text-xl">{!! $header !!}</div>
                    @else
                        <h1 class="page-title text-lg sm:text-xl">Dashboard</h1>
                    @endisset
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex badge-neutral capitalize">{{ auth()->user()->role ?? '' }}</span>
                <div class="hidden sm:flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">
                    {{ collect(explode(' ', auth()->user()->name ?? 'U'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                </div>
            </div>

        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="shrink-0 border-t border-surface-border bg-white px-6 py-3">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'MedFlow') }}</span>
                <div class="flex gap-4">
                    <a href="{{ route('privacy') }}" class="hover:text-slate-600 transition">Privacy</a>
                    <a href="{{ route('terms') }}" class="hover:text-slate-600 transition">Terms</a>
                </div>
            </div>
        </footer>

    </div>
</div>

</body>
</html>
