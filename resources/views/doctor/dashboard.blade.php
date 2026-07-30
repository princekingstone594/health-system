<![CDATA[<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="page-title">Doctor Dashboard</span>
                <p class="page-subtitle">Welcome back, Dr. {{ auth()->user()->name ?? 'Doctor' }}</p>
            </div>
            <a href="{{ route('appointments.create') }}" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                New Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <x-stat-card label="Total Appointments" :value="$totalAppointments ?? 0" icon="calendar" color="brand" />
            <x-stat-card label="Today's Appointments" :value="$todayCount ?? 0" icon="clock" color="violet" />
            <x-stat-card label="Status" value="Active" icon="check" color="emerald" />
        </div>

        {{-- Today's Appointments --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Today's Appointments</h3>
                </div>
                <span class="badge-brand">{{ $todayAppointments->count() ?? 0 }} scheduled</span>
            </div>
            <div class="divide-y divide-surface-border">
                @forelse($todayAppointments as $appt)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                                {{ collect(explode(' ', $appt->patient->name ?? 'P'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $appt->patient->name ?? 'Patient' }}</p>
                                <p class="text-sm text-slate-500 flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    {{ $appt->date }} at {{ $appt->time }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Status Dropdown --}}
                            <form method="POST" action="{{ route('doctor.appointment.status', $appt->id) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="form-input text-xs py-1.5 w-auto">
                                    <option value="pending" {{ $appt->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $appt->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $appt->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="rejected" {{ $appt->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>

                            {{-- Add Notes --}}
                            <a href="{{ route('doctor.notes', $appt->id) }}" class="btn-secondary btn-sm">
                                <x-icon name="document" class="w-3.5 h-3.5" />
                                Notes
                            </a>

                            {{-- AI Follow-Up --}}
                            <form method="POST" action="{{ route('followup.generate', $appointment->id) }}">
                                @csrf
                                <button class="btn-primary btn-sm bg-violet-600 hover:bg-violet-700 focus:ring-violet-500 hover:shadow-violet-500/20">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5" />
                                    AI Follow-Up
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <x-empty-state title="No appointments today" description="Your schedule is clear for today." icon="calendar" />
                @endforelse
            </div>
        </div>

        {{-- Upcoming + AI Follow-ups grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Upcoming --}}
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <x-icon name="calendar" class="w-5 h-5 text-sky-600" />
                        <h3 class="section-title">Upcoming Appointments</h3>
                    </div>
                </div>
                <div class="divide-y divide-surface-border">
                    @forelse($upcomingAppointments as $appt)
                        <div class="flex items-center justify-between p-5 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700">
                                    {{ collect(explode(' ', $appt->patient->name ?? 'P'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $appt->patient->name ?? 'Patient' }}</p>
                                    <p class="text-xs text-slate-500">{{ $appt->date }} at {{ $appt->time }}</p>
                                </div>
                            </div>
                            <x-badge :type="match($appt->status, 'approved' => 'success', 'pending' => 'warning', 'cancelled' => 'danger', 'rejected' => 'danger', default => 'neutral')">
                                {{ ucfirst($appt->status) }}
                            </x-badge>
                        </div>
                    @empty
                        <x-empty-state title="No upcoming appointments" icon="calendar" />
                    @endforelse
                </div>
            </div>

            {{-- AI Follow-Ups --}}
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100">
                            <x-icon name="sparkles" class="w-4 h-4 text-violet-600" />
                        </div>
                        <h3 class="section-title">AI Follow-Ups</h3>
                    </div>
                    <span class="badge-ai">AI Powered</span>
                </div>
                <div class="divide-y divide-surface-border">
                    @forelse($followUps as $follow)
                        <div class="p-5 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-medium text-slate-900">{{ $follow->patient->name ?? 'Patient' }}</p>
                                <span class="text-xs text-slate-400">{{ $follow->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-violet-100 mt-0.5">
                                    <x-icon name="sparkles" class="w-3 h-3 text-violet-600" />
                                </div>
                                <p class="text-sm text-slate-600 italic">"{{ $follow->message }}"</p>
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="No follow-ups yet" description="AI-generated follow-ups will appear here." icon="sparkles" />
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
]]>