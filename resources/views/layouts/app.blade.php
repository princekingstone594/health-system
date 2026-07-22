<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Health System') }}</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- ===================== -->
    <!-- SIDEBAR -->
    <!-- ===================== -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">

        <!-- Logo -->
        <div class="p-6 text-xl font-bold border-b border-gray-800 tracking-wide">
            🏥 Health System
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 text-sm">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('dashboard') ? 'bg-gray-800' : 'hover:bg-gray-800' }}">
                <i data-feather="home" class="w-4 h-4"></i>
                Dashboard
            </a>

            <!-- Patients -->
            <a href="{{ route('patients.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('patients.*') ? 'bg-gray-800' : 'hover:bg-gray-800' }}">
                <i data-feather="users" class="w-4 h-4"></i>
                Patients
            </a>

            <!-- Appointments -->
            <a href="{{ route('appointments.create') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('appointments.*') ? 'bg-gray-800' : 'hover:bg-gray-800' }}">
                <i data-feather="calendar" class="w-4 h-4"></i>
                Appointments
            </a>

            <!-- Availability -->
            <a href="{{ route('availability.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('availability.*') ? 'bg-gray-800' : 'hover:bg-gray-800' }}">
                <i data-feather="clock" class="w-4 h-4"></i>
                Availability
            </a>

        </nav>

        <!-- User + Logout -->
        <div class="p-4 border-t border-gray-800">

            <div class="text-sm text-gray-400 mb-3">
                {{ Auth::user()->name ?? 'Guest' }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    Logout
                </button>
            </form>

        </div>

    </aside>

    <!-- ===================== -->
    <!-- MAIN CONTENT -->
    <!-- ===================== -->
    <div class="flex-1 flex flex-col">

        <!-- Topbar -->
        <header class="bg-white border-b px-6 py-4 flex justify-between items-center">

            <h1 class="text-lg font-semibold text-gray-800">
                {{ $header ?? 'Dashboard' }}
            </h1>

            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>{{ Auth::user()->email ?? '' }}</span>
            </div>

        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

    </div>

</div>

<script>
    feather.replace()
</script>

</body>
</html>