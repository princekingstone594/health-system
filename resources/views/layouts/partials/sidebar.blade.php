@php
    $role = auth()->user()->role ?? 'guest';
    $initials = collect(explode(' ', auth()->user()->name ?? 'U'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
@endphp

{{-- Logo --}}
<div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 shadow-lg shadow-brand-500/30">
        <x-icon name="heart" class="w-5 h-5 text-white" />
    </div>
    <div>
        <p class="text-sm font-bold text-white tracking-tight">{{ config('app.name', 'MedFlow') }}</p>
        <p class="text-[11px] text-slate-400 capitalize">{{ $role }} portal</p>
    </div>
</div>

{{-- Navigation --}}
<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

    {{-- Dashboard (role-specific) --}}
    @if($role === 'doctor')
        <x-sidebar-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')" icon="home">Dashboard</x-sidebar-link>
    @elseif($role === 'patient')
        <x-sidebar-link :href="route('patient.dashboard')" :active="request()->routeIs('patient.dashboard')" icon="home">Dashboard</x-sidebar-link>
    @elseif($role === 'admin')
        <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" icon="home">Dashboard</x-sidebar-link>
    @else
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Dashboard</x-sidebar-link>
    @endif

    {{-- Patients (admin / receptionist) --}}
    @if(in_array($role, ['admin', 'receptionist']))
        <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Clinic</p>
        <x-sidebar-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" icon="users">Patients</x-sidebar-link>
        <x-sidebar-link :href="route('appointments.create')" :active="request()->routeIs('appointments.*')" icon="calendar">Appointments</x-sidebar-link>
    @endif

    {{-- Doctor tools --}}
    @if($role === 'doctor')
        <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Practice</p>
        <x-sidebar-link :href="route('appointments.create')" :active="request()->routeIs('appointments.*')" icon="calendar">Appointments</x-sidebar-link>
        <x-sidebar-link :href="route('doctor.calendar')" :active="request()->routeIs('doctor.calendar')" icon="calendar">Calendar</x-sidebar-link>
        <x-sidebar-link :href="route('availability.index')" :active="request()->routeIs('availability.*')" icon="clock">Availability</x-sidebar-link>
        <x-sidebar-link :href="route('leaves.index')" :active="request()->routeIs('leaves.*')" icon="document">Leave Requests</x-sidebar-link>
    @endif

    {{-- Patient tools --}}
    @if($role === 'patient')
        <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Care</p>
        <x-sidebar-link :href="route('booking.show', 1)" :active="request()->routeIs('booking.*')" icon="calendar">Book Appointment</x-sidebar-link>
        <x-sidebar-link :href="route('patient.history')" :active="request()->routeIs('patient.history')" icon="document">Medical History</x-sidebar-link>
        <x-sidebar-link :href="route('records.index')" :active="request()->routeIs('records.*')" icon="document">My Records</x-sidebar-link>
    @endif

    {{-- AI & Health tools (all authenticated) --}}
    <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">AI Health</p>
    <x-sidebar-link :href="route('symptom.index')" :active="request()->routeIs('symptom.*')" icon="heart">Symptom Checker</x-sidebar-link>
    <x-sidebar-link :href="route('ai.chat')" :active="request()->routeIs('ai.*')" icon="sparkles">AI Assistant</x-sidebar-link>
    <x-sidebar-link :href="route('followups.index')" :active="request()->routeIs('followups.*')" icon="bell">Follow-ups</x-sidebar-link>

    {{-- Admin --}}
    @if($role === 'admin')
        <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Admin</p>
        <x-sidebar-link :href="route('admin.revenue')" :active="request()->routeIs('admin.revenue')" icon="credit-card">Revenue</x-sidebar-link>
        <x-sidebar-link :href="route('plans')" :active="request()->routeIs('plans')" icon="building">Plans</x-sidebar-link>
    @endif

</nav>

{{-- User footer --}}
<div class="border-t border-white/10 p-4">
    <div class="flex items-center gap-3 mb-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">
            {{ $initials }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('profile.edit') }}" class="flex-1 nav-item justify-center text-xs py-2">
            <x-icon name="settings" class="w-4 h-4" />
            Profile
        </a>
        <form method="POST" action="{{ route('logout') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full nav-item justify-center text-xs py-2 hover:text-red-400">
                <x-icon name="logout" class="w-4 h-4" />
                Logout
            </button>
        </form>
    </div>
</div>
