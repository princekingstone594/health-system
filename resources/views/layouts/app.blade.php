<![CDATA[<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedFlow') }} — {{ $header ?? 'Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/app.js') }}"></script>
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
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-surface-border bg-white/80 px-4 backdrop-blur-md sm:px-6">

            <div class="flex items-center gap-4">
                {{-- Mobile menu toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <div class="animate-fade-in">
                    @isset($header)
                        <div class="page-title text-lg sm:text-xl">{!! $header !!}</div>
                    @else
                        <h1 class="page-title text-lg sm:text-xl">Dashboard</h1>
                    @endisset
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Search (desktop) --}}
                <div class="hidden md:flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-400 w-56 transition-colors focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-500/30">
                    <x-icon name="search" class="w-4 h-4" />
                    <input type="text" placeholder="Search..." class="bg-transparent border-0 p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0 w-full">
                </div>

                {{-- Notifications --}}
                <button class="relative rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700">
                    <x-icon name="bell" class="w-5 h-5" />
                    <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-brand-500 ring-2 ring-white"></span>
                </button>

                {{-- Role badge --}}
                <span class="hidden sm:inline-flex badge-neutral capitalize">{{ auth()->user()->role ?? '' }}</span>

                {{-- Avatar --}}
                <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-full bg-gradient-brand text-xs font-bold text-white shadow-sm">
                    {{ collect(explode(' ', auth()->user()->name ?? 'U'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                </div>
            </div>

        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 animate-fade-in">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="shrink-0 border-t border-surface-border bg-white px-6 py-3">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'MedFlow') }}</span>
                <div class="flex gap-4">
                    <a href="{{ route('privacy') }}" class="transition-colors hover:text-slate-600">Privacy</a>
                    <a href="{{ route('terms') }}" class="transition-colors hover:text-slate-600">Terms</a>
                </div>
            </div>
        </footer>

    </div>
</div>

</body>
</html>
]]>