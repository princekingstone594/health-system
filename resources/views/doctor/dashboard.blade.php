<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="page-title">Doctor Dashboard</h2>
                <p class="page-subtitle">
                    Welcome back, Dr. {{ auth()->user()->name ?? 'Doctor' }}
                </p>
            </div>

            <a href="{{ route('appointments.create') }}" class="btn-gradient">
                <x-icon name="plus" class="w-4 h-4"/>
                New Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <x-stat-card label="Total Appointments" :value="$totalAppointments ?? 0" icon="calendar" color="brand" />
            <x-stat-card label="Today's Appointments" :value="$todayCount ?? 0" icon="clock" color="sky" />
            <x-stat-card label="Status" value="Active" icon="activity" color="emerald" />
        </div>

        {{-- TODAY APPOINTMENTS --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Today's Appointments</h3>
                </div>
                <span class="badge-brand">{{ $todayAppointments->count() ?? 0 }} scheduled</span>
            </div>

            @forelse($todayAppointments as $appt)
                <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b last:border-0 hover:bg-slate-50/80 transition-colors">

                    {{-- Patient Info --}}
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50">
                            <x-icon name="user" class="w-5 h-5 text-brand-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $appt->patient->name ?? 'Patient' }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} at {{ $appt->time }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center gap-3">

                        {{-- STATUS --}}
                        <form method="POST" action="{{ route('doctor.appointment.status', $appt->id) }}">
                            @csrf
                            <select name="status"
                                    onchange="this.form.submit()"
                                    class="form-input w-auto cursor-pointer">
                                <option value="pending" {{ $appt->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $appt->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $appt->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="rejected" {{ $appt->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </form>

                        {{-- NOTES --}}
                        <a href="{{ route('doctor.notes', $appt->id) }}" class="btn-secondary btn-sm">
                            <x-icon name="document" class="w-4 h-4" />
                            Notes
                        </a>

                        {{-- AI --}}
                        <form method="POST" action="{{ route('followup.generate', $appt->id) }}">
                            @csrf
                            <button class="btn-violet btn-sm">
                                <x-icon name="sparkles" class="w-4 h-4" />
                                AI Follow-Up
                            </button>
                        </form>

                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500">
                    <x-icon name="calendar" class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                    No appointments today
                </div>
            @endforelse
        </div>

        {{-- AI DIAGNOSIS --}}
        <div class="card p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon name="sparkles" class="w-5 h-5 text-violet-600" />
                <h3 class="section-title">AI Diagnosis Assistant</h3>
                <span class="badge-ai ml-auto"><x-icon name="sparkles" class="w-3 h-3" /> AI</span>
            </div>

            <form method="POST" action="{{ route('doctor.ai.diagnosis') }}">
                @csrf 

                <textarea name="symptoms"
                          placeholder="Enter patient symptoms..."
                          class="form-input min-h-[100px] mb-3"></textarea>

                <button class="btn-violet">
                    <x-icon name="sparkles" class="w-4 h-4" />
                    Generate Diagnosis
                </button>
            </form>

            @if(session('diagnosis'))
                <div class="mt-4 p-4 bg-violet-50/60 rounded-xl text-sm text-slate-700 border border-violet-100">
                    {!! nl2br(e(session('diagnosis'))) !!} 
                </div>
            @endif 
        </div>

        {{-- TWO COLUMN SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- UPCOMING --}}
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-5 h-5 text-sky-600" />
                        <h3 class="section-title">Upcoming Appointments</h3>
                    </div>
                </div>

                @forelse($upcomingAppointments as $appt)
                    <div class="p-5 flex justify-between items-center border-b last:border-0 hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50">
                                <x-icon name="user" class="w-4 h-4 text-sky-600" />
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $appt->patient->name ?? 'Patient' }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} at {{ $appt->time }}
                                </p>
                            </div>
                        </div>

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
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500">
                        <x-icon name="clock" class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                        No upcoming appointments
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
                    <span class="badge-ai"><x-icon name="sparkles" class="w-3 h-3" /> Smart AI</span>
                </div>

                <div class="space-y-3 p-5">
                    @forelse($followUps as $follow)
                        <div class="bg-white p-4 rounded-xl shadow-card">
                            <div class="flex justify-between mb-1">
                                <p class="font-medium text-sm text-slate-800">
                                    {{ $follow->patient->name ?? 'Patient' }}
                                </p>
                                <span class="text-xs text-slate-400">
                                    {{ $follow->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-700 italic">
                                "{{ $follow->message }}"
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