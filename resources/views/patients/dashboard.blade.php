<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Patient Dashboard</h2>
                <p class="text-sm text-slate-500">
                    Welcome back, {{ auth()->user()->name ?? 'Patient' }}
                </p>
            </div>

            <a href="{{ route('booking.show', 1) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow hover:shadow-lg transition">
                <x-icon name="plus" class="w-4 h-4"/>
                Book Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-700 rounded-xl shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="p-5 bg-white rounded-2xl shadow-sm hover:shadow-md transition">
                <p class="text-sm text-gray-500">Total Appointments</p>
                <h3 class="text-2xl font-bold mt-1">{{ $totalAppointments }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm hover:shadow-md transition">
                <p class="text-sm text-gray-500">Upcoming</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $upcomingCount }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm hover:shadow-md transition">
                <p class="text-sm text-gray-500">Completed</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">
                    {{ $totalAppointments - $upcomingCount }}
                </h3>
            </div>
        </div>

        {{-- Upcoming Appointments --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="font-semibold text-lg">Upcoming Appointments</h3>
                <span class="text-sm text-gray-500">{{ $upcomingCount }} scheduled</span>
            </div>

            @forelse($upcomingAppointments as $appt)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b last:border-0 hover:bg-gray-50 transition">
                    <div>
                        <p class="font-semibold">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $appt->date }} at {{ $appt->time }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs rounded-full
                            @if($appt->status == 'approved') bg-green-100 text-green-700
                            @elseif($appt->status == 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($appt->status) }}
                        </span>

                        @if($appt->is_paid)
                            <span class="text-green-600 text-sm font-medium">Paid</span>
                        @else
                            <a href="{{ route('checkout', $appt->id) }}"
                               class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                Pay
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    No upcoming appointments
                </div>
            @endforelse
        </div>

        {{-- Two Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Medical History --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-lg">Medical History</h3>
                </div>

                @forelse($pastAppointments as $appt)
                    <div class="p-5 border-b last:border-0">
                        <div class="flex justify-between mb-2">
                            <p class="font-semibold">Dr. {{ $appt->doctor->name ?? 'Doctor' }}</p>
                            <span class="text-xs text-gray-400">{{ $appt->date }}</span>
                        </div>

                        @if($appt->is_shared_with_patient && $appt->diagnosis)
                            <div class="bg-gray-50 p-3 rounded-lg mb-2">
                                <p class="text-xs text-gray-500">Diagnosis</p>
                                <p class="text-sm">{{ $appt->diagnosis }}</p>
                            </div>
                        @endif

                        @if($appt->is_shared_with_patient && $appt->prescription)
                            <div class="bg-gray-50 p-3 rounded-lg mb-2">
                                <p class="text-xs text-gray-500">Prescription</p>
                                <p class="text-sm">{{ $appt->prescription }}</p>
                            </div>

                            <a href="{{ route('prescription.download', $appt->id) }}"
                               class="text-blue-600 text-sm hover:underline">
                                Download Prescription
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No medical history yet
                    </div>
                @endforelse
            </div>

            {{-- AI FOLLOW UPS (UPGRADED 🔥) --}}
            <div class="bg-gradient-to-br from-violet-50 to-indigo-50 rounded-2xl shadow-sm overflow-hidden border border-violet-100">

                <div class="flex items-center justify-between p-5 border-b border-violet-100">
                    <h3 class="font-semibold text-lg text-violet-700">AI Follow-Ups</h3>
                    <span class="text-xs bg-violet-200 text-violet-700 px-2 py-1 rounded-full">
                        AI Powered
                    </span>
                </div>

                {{-- Button --}}
                <div class="p-5">
                    <form method="POST" action="{{ route('patient.followup.trigger') }}">
                        @csrf
                        <button
                            class="w-full py-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-medium hover:shadow-lg transition">
                            ✨ Generate AI Follow-Up
                        </button>
                    </form>
                </div>

                {{-- Messages --}}
                <div class="space-y-3 px-5 pb-5">
                    @forelse($followUps as $follow)
                        <div class="bg-white p-4 rounded-xl shadow-sm">
                            <p class="text-sm text-gray-700">{{ $follow->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $follow->created_at->diffForHumans() }}
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