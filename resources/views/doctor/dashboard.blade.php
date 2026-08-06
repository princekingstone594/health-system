<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">
                    Doctor Dashboard
                </h2>
                <p class="text-sm text-slate-500">
                    Welcome back, Dr. {{ auth()->user()->name ?? 'Doctor' }}
                </p>
            </div>

            <a href="{{ route('appointments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl shadow hover:shadow-lg transition">
                <x-icon name="plus" class="w-4 h-4"/>
                New Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Total Appointments</p>
                <h3 class="text-2xl font-bold mt-1">{{ $totalAppointments ?? 0 }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Today's Appointments</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $todayCount ?? 0 }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Status</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">Active</h3>
            </div>
        </div>

        {{-- TODAY APPOINTMENTS --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="font-semibold text-lg">Today's Appointments</h3>
                <span class="text-sm text-gray-500">
                    {{ $todayAppointments->count() ?? 0 }} scheduled
                </span>
            </div>

            @forelse($todayAppointments as $appt)
                <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b last:border-0 hover:bg-gray-50 transition">

                    {{-- Patient Info --}}
                    <div>
                        <p class="font-semibold text-slate-800">
                            {{ $appt->patient->name ?? 'Patient' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $appt->date }} at {{ $appt->time }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center gap-3">

                        {{-- STATUS --}}
                        <form method="POST" action="{{ route('doctor.appointment.status', $appt->id) }}">
                            @csrf
                            <select name="status"
                                    onchange="this.form.submit()"
                                    class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500">
                                <option value="pending" {{ $appt->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $appt->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $appt->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="rejected" {{ $appt->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </form>

                        {{-- NOTES --}}
                        <a href="{{ route('doctor.notes', $appt->id) }}"
                           class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200">
                            Notes
                        </a>

                        {{-- AI --}}
                        <form method="POST" action="{{ route('followup.generate', $appt->id) }}">
                            @csrf
                            <button
                                class="px-3 py-1 text-sm text-white bg-gradient-to-r from-violet-600 to-indigo-600 rounded-lg hover:shadow-md">
                                ✨ AI Follow-Up
                            </button>
                        </form>

                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    No appointments today
                </div>
            @endforelse
        </div>

        {{-- TWO COLUMN SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- UPCOMING --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-lg">Upcoming Appointments</h3>
                </div>

                @forelse($upcomingAppointments as $appt)
                    <div class="p-5 flex justify-between items-center border-b last:border-0 hover:bg-gray-50">
                        <div>
                            <p class="font-medium">{{ $appt->patient->name ?? 'Patient' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $appt->date }} at {{ $appt->time }}
                            </p>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full
                            @if($appt->status == 'approved') bg-green-100 text-green-700
                            @elseif($appt->status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($appt->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No upcoming appointments
                    </div>
                @endforelse
            </div>

            {{-- AI FOLLOW UPS (ENHANCED) --}}
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-100 rounded-2xl shadow-sm overflow-hidden">

                <div class="flex justify-between items-center p-5 border-b border-indigo-100">
                    <h3 class="font-semibold text-lg text-indigo-700">
                        AI Follow-Ups
                    </h3>
                    <span class="text-xs bg-indigo-200 text-indigo-700 px-2 py-1 rounded-full">
                        Smart AI
                    </span>
                </div>

                <div class="space-y-3 p-5">
                    @forelse($followUps as $follow)
                        <div class="bg-white p-4 rounded-xl shadow-sm">
                            <div class="flex justify-between mb-1">
                                <p class="font-medium text-sm">
                                    {{ $follow->patient->name ?? 'Patient' }}
                                </p>
                                <span class="text-xs text-gray-400">
                                    {{ $follow->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-700 italic">
                                "{{ $follow->message }}"
                            </p>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 text-sm">
                            No AI follow-ups yet
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

    </div>
</x-app-layout>