<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="page-title">Patient Dashboard</span>
                <p class="page-subtitle">Welcome back, {{ auth()->user()->name ?? 'Patient' }}</p>
            </div>
            <a href="{{ route('booking.show', 1) }}" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Book Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <x-stat-card label="Total Appointments" :value="$totalAppointments" icon="calendar" color="brand" />
            <x-stat-card label="Upcoming" :value="$upcomingCount" icon="clock" color="sky" />
            <x-stat-card label="Completed" :value="$totalAppointments - $upcomingCount" icon="check" color="emerald" />
        </div>

        {{-- Upcoming Appointments --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Upcoming Appointments</h3>
                </div>
                <span class="badge-brand">{{ $upcomingCount }} scheduled</span>
            </div>
            <div class="divide-y divide-surface-border">
                @forelse($upcomingAppointments as $appt)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                                {{ collect(explode(' ', $appt->doctor->name ?? 'D'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('') }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                                <p class="text-sm text-slate-500 flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    {{ $appt->date }} at {{ $appt->time }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-badge :type="match($appt->status, 'approved' => 'success', 'pending' => 'warning', 'cancelled' => 'danger', default => 'neutral')">
                                {{ ucfirst($appt->status) }}
                            </x-badge>

                            @if($appt->is_paid)
                                <span class="badge-success">
                                    <x-icon name="check" class="w-3 h-3" />
                                    Paid
                                </span>
                            @else
                                <a href="{{ route('checkout', $appt->id) }}" class="btn-primary btn-sm">
                                    <x-icon name="credit-card" class="w-3.5 h-3.5" />
                                    Pay Now
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-empty-state title="No upcoming appointments" description="Book your next appointment to get started." icon="calendar">
                        <a href="{{ route('booking.show', 1) }}" class="btn-primary btn-sm">Book Appointment</a>
                    </x-empty-state>
                @endforelse
            </div>
        </div>

        {{-- Medical History + AI Follow-ups --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Medical History --}}
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <x-icon name="document" class="w-5 h-5 text-sky-600" />
                        <h3 class="section-title">Medical History</h3>
                    </div>
                </div>
                <div class="divide-y divide-surface-border">
                    @forelse($pastAppointments as $appt)
                        <div class="p-5 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <p class="font-semibold text-slate-900">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                                <span class="text-xs text-slate-400">{{ $appt->date }}</span>
                            </div>

                            @if($appt->is_shared_with_patient && $appt->diagnosis)
                                <div class="rounded-lg bg-slate-50 p-3 mb-2">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Diagnosis</p>
                                    <p class="text-sm text-slate-700">{{ $appt->diagnosis }}</p>
                                </div>
                            @endif

                            @if($appt->is_shared_with_patient && $appt->prescription)
                                <div class="rounded-lg bg-slate-50 p-3 mb-3">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Prescription</p>
                                    <p class="text-sm text-slate-700">{{ $appt->prescription }}</p>
                                </div>
                                <a href="{{ route('prescription.download', $appt->id) }}" class="btn-secondary btn-sm">
                                    <x-icon name="document" class="w-3.5 h-3.5" />
                                    Download Prescription
                                </a>
                            @endif
                        </div>
                    @empty
                        <x-empty-state title="No history yet" description="Your medical history will appear here after appointments." icon="document" />
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
                <div class="p-5 border-b border-surface-border">
                    <form method="POST" action="{{ route('patient.followup.trigger') }}">
                        @csrf
                        <button class="btn-primary w-full bg-violet-600 hover:bg-violet-700 focus:ring-violet-500 hover:shadow-violet-500/20">
                            <x-icon name="sparkles" class="w-4 h-4" />
                            Generate AI Follow-Up
                        </button>
                    </form>
                </div>
                <div class="divide-y divide-surface-border">
                    @forelse($followUps as $follow)
                        <div class="p-5 hover:bg-slate-50/50 transition-colors">
                            <div class="flex gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-100">
                                    <x-icon name="sparkles" class="w-4 h-4 text-violet-600" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-700">{{ $follow->message }}</p>
                                    <p class="text-xs text-slate-400 mt-1.5">{{ $follow->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-empty-state title="No follow-ups yet" description="Generate AI-powered follow-ups for your care." icon="sparkles" />
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
]]>