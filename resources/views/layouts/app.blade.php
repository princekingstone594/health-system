<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Health System') }}</title>

```
<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Heroicons -->
<script src="https://unpkg.com/feather-icons"></script>
```

</head>

<body class="bg-gray-100">

<div class="flex h-screen">

```
<!-- Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex flex-col">
    <div class="p-6 text-xl font-bold border-b border-gray-700">
        🏥 Health System
    </div>

    <nav class="flex-1 p-4 space-y-2">
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Patients</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Appointments</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Reports</a>
    </nav>

    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left px-4 py-2 hover:bg-gray-700 rounded">
                Logout
            </button>
        </form>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 flex flex-col">

    <!-- Topbar -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <h1 class="text-lg font-semibold">
            {{ $header ?? 'Dashboard' }}
        </h1>

        <div class="text-sm text-gray-600">
            {{ Auth::user()->name ?? 'Guest' }}
        </div>
    </header>

    <!-- Page Content -->
    <main class="p-6 overflow-y-auto flex-1">
        {{ $slot }}
    </main>

</div>
```

</div>

<script>
    feather.replace()
</script>

</body>
</html>
