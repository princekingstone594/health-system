<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="page-title">Dashboard</span>
                <p class="page-subtitle">Overview of your clinic activity</p>
            </div>
            <a href="{{ route('appointments.create') }}" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Book Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <x-stat-card label="Total Patients" :value="\App\Models\Patient::count()" icon="users" color="brand" />
            <x-stat-card label="Active Doctors" :value="\App\Models\Doctor::count()" icon="user" color="emerald" />
            <x-stat-card label="Appointments" :value="\App\Models\Appointment::count()" icon="calendar" color="violet" />
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-700">Filter Appointments</h3>
            </div>
            <form method="GET" class="card-body pt-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Patient or doctor..."
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="">All Status</option>
                            <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-input">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1">Apply</button>
                        <a href="{{ route('dashboard') }}" class="btn-secondary flex-1 text-center">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        {{-- Appointments table --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-700">Recent Appointments</h3>
                <span class="badge-neutral">{{ $recentAppointments->total() ?? $recentAppointments->count() }} total</span>
            </div>

            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left">Patient</th>
                            <th class="px-6 py-3 text-left">Doctor</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Time</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAppointments as $appointment)
                            <tr class="table-row">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $appointment->patient->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $appointment->doctor->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $appointment->appointment_date }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $appointment->appointment_time }}</td>
                                <td class="px-6 py-4">
                                    <x-badge :type="$appointment->status == 'Scheduled' ? 'success' : 'danger'">
                                        {{ $appointment->status }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn-secondary btn-sm">Edit</a>
                                        <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST"
                                              onsubmit="return confirm('Cancel this appointment?')">
                                            @csrf @method('PATCH')
                                            <button class="btn-danger btn-sm">Cancel</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-empty-state title="No appointments yet" description="Book your first appointment to get started." icon="calendar">
                                        <a href="{{ route('appointments.create') }}" class="btn-primary btn-sm">Book Appointment</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-surface-border">
                @forelse($recentAppointments as $appointment)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-slate-900">{{ $appointment->patient->name ?? 'N/A' }}</p>
                                <p class="text-sm text-slate-500">Dr. {{ $appointment->doctor->name ?? 'N/A' }}</p>
                            </div>
                            <x-badge :type="$appointment->status == 'Scheduled' ? 'success' : 'danger'">{{ $appointment->status }}</x-badge>
                        </div>
                        <p class="text-sm text-slate-600">{{ $appointment->appointment_date }} at {{ $appointment->appointment_time }}</p>
                        <div class="flex gap-2">
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn-secondary btn-sm flex-1 text-center">Edit</a>
                            <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button class="btn-danger btn-sm w-full">Cancel</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <x-empty-state title="No appointments yet" icon="calendar" />
                @endforelse
            </div>

            @if($recentAppointments->hasPages())
                <div class="px-6 py-4 border-t border-surface-border">
                    {{ $recentAppointments->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
