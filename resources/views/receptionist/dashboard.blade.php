<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="page-title">Receptionist Dashboard</h2>
                <p class="page-subtitle">Manage patients and appointments efficiently</p>
            </div>

            <a href="{{ route('appointments.create') }}" class="btn-gradient">
                <x-icon name="plus" class="w-4 h-4" />
                New Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <x-stat-card label="Total Appointments" :value="$totalAppointments" icon="calendar" color="brand" />
            <x-stat-card label="Today's Appointments" :value="$todayAppointments" icon="clock" color="sky" />
            <x-stat-card label="Pending" :value="$pendingAppointments" icon="bell" color="amber" />
        </div>

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- RECENT PATIENTS --}}
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-4">
                    <x-icon name="users" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Recent Patients</h3>
                </div>

                <div class="space-y-3">
                    @forelse($patients as $patient)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">
                                    {{ collect(explode(' ', $patient->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 text-sm">
                                        {{ $patient->name }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $patient->email ?? 'No email' }}
                                    </p>
                                </div>
                            </div>

                            <span class="badge-brand">Patient</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">No patients found</p>
                    @endforelse
                </div>
            </div>

            {{-- APPOINTMENTS TABLE --}}
            <div class="lg:col-span-2 card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <x-icon name="calendar" class="w-5 h-5 text-brand-600" />
                        <h3 class="section-title">All Appointments</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="table-header">
                            <tr>
                                <th class="p-3 text-left font-semibold">Patient</th>
                                <th class="p-3 text-left font-semibold">Doctor</th>
                                <th class="p-3 text-left font-semibold">Date</th>
                                <th class="p-3 text-left font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr class="table-row">
                                    <td class="p-3 font-medium text-slate-800">
                                        {{ $appointment->patient->name }}
                                    </td>

                                    <td class="p-3 text-slate-600">
                                        Dr. {{ $appointment->doctor->name }}
                                    </td>

                                    <td class="p-3 text-slate-500">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </td>

                                    <td class="p-3">
                                        @php
                                            $badgeClass = match($appointment->status) {
                                                'approved' => 'badge-success',
                                                'pending' => 'badge-warning',
                                                'cancelled' => 'badge-danger',
                                                'completed' => 'badge-info',
                                                default => 'badge-neutral',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ ucfirst($appointment->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-500">
                                        <x-icon name="calendar" class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                                        No appointments found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>