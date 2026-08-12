<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="page-title">Patient Dashboard</h2>
                <p class="page-subtitle">
                    Welcome back, {{ auth()->user()->name ?? 'Patient' }}
                </p>
            </div>

            <a href="{{ route('booking.show', 1) }}" class="btn-gradient">
                <x-icon name="plus" class="w-4 h-4"/>
                Book Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert-success">
                <x-icon name="check" class="w-4 h-4 mt-0.5" />
                {{ session('success') }}
            </div>
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

            @forelse($upcomingAppointments as $appt)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b last:border-0 hover:bg-slate-50/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50">
                            <x-icon name="user" class="w-5 h-5 text-brand-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                            <p class="text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} at {{ $appt->time }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @php
                            $badgeClass = match($appt->status) {
                                'approved' => 'badge-success',
                                'pending' => 'badge-warning',
                                'completed' => 'badge-info',
                                'cancelled', 'rejected' => 'badge-danger',
                                default => 'badge-neutral',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}">{{ ucfirst($appt->status) }}</span>

                        @if($appt->is_paid)
                            <span class="badge-success"><x-icon name="check" class="w-3 h-3" /> Paid</span>
                        @else
                            <a href="{{ route('checkout', $appt->id) }}" class="btn-primary btn-sm">
                                <x-icon name="credit-card" class="w-4 h-4" />
                                Pay
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500">
                    <x-icon name="calendar" class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                    No upcoming appointments
                </div>
            @endforelse
        </div>

        {{-- Two Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Medical History --}}
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <x-icon name="document" class="w-5 h-5 text-violet-600" />
                        <h3 class="section-title">Medical History</h3>
                    </div>
                </div>

                @forelse($pastAppointments as $appt)
                    <div class="p-5 border-b last:border-0">
                        <div class="flex justify-between mb-2">
                            <p class="font-semibold text-slate-800">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }}</span>
                        </div>

                        @if($appt->is_shared_with_patient && $appt->diagnosis)
                            <div class="bg-violet-50/60 p-3 rounded-lg mb-2">
                                <p class="text-xs text-violet-600 font-medium mb-0.5">Diagnosis</p>
                                <p class="text-sm text-slate-700">{{ $appt->diagnosis }}</p>
                            </div>
                        @endif

                        @if($appt->is_shared_with_patient && $appt->prescription)
                            <div class="bg-emerald-50/60 p-3 rounded-lg mb-2">
                                <p class="text-xs text-emerald-600 font-medium mb-0.5">Prescription</p>
                                <p class="text-sm text-slate-700">{{ $appt->prescription }}</p>
                            </div>

                            <a href="{{ route('prescription.download', $appt->id) }}"
                               class="inline-flex items-center gap-1 text-brand-600 text-sm font-medium hover:text-brand-700 hover:underline">
                                <x-icon name="download" class="w-4 h-4" />
                                Download Prescription
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500">
                        <x-icon name="document" class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                        No medical history yet
                    </div>
                @endforelse
            </div>

            {{-- AI FOLLOW UPS --}}
            <div class="card overflow-hidden bg-gradient-to-br from-violet-50/60 to-indigo-50/60 border-violet-100">

                <div class="card-header border-violet-100">
                    <div class="flex items-center gap-2">
                        <x-icon name="sparkles" class="w-5 h-5 text-violet-600" />
                        <h3 class="section-title text-violet-700">AI Follow-Ups</h3>
                    </div>
                    <span class="badge-ai"><x-icon name="sparkles" class="w-3 h-3" /> AI Powered</span>
                </div>

                {{-- Button --}}
                <div class="p-5">
                    <form method="POST" action="{{ route('patient.followup.trigger') }}">
                        @csrf
                        <button class="btn-violet w-full justify-center">
                            <x-icon name="sparkles" class="w-4 h-4" />
                            Generate AI Follow-Up
                        </button>
                    </form>
                </div>

                {{-- Messages --}}
                <div class="space-y-3 px-5 pb-5">
                    @forelse($followUps as $follow)
                        <div class="bg-white p-4 rounded-xl shadow-card">
                            <p class="text-sm text-slate-700">{{ $follow->message }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $follow->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 text-sm py-4">
                            No AI follow-ups yet
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>