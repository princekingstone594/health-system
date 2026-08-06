<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">
                    Receptionist Dashboard
                </h2>
                <p class="text-sm text-slate-500">
                    Manage patients and appointments efficiently
                </p>
            </div>

            <a href="{{ route('appointments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl shadow hover:shadow-lg transition">
                ➕ New Appointment
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Total Appointments</p>
                <h3 class="text-2xl font-bold mt-1">{{ $totalAppointments }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Today's Appointments</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $todayAppointments }}</h3>
            </div>

            <div class="p-5 bg-white rounded-2xl shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingAppointments }}</h3>
            </div>
        </div>

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- RECENT PATIENTS --}}
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-lg mb-4">Recent Patients</h3>

                <div class="space-y-3">
                    @forelse($patients as $patient)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50">
                            <div>
                                <p class="font-medium text-slate-800">
                                    {{ $patient->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $patient->email ?? 'No email' }}
                                </p>
                            </div>

                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">
                                Patient
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No patients found</p>
                    @endforelse
                </div>
            </div>

            {{-- APPOINTMENTS TABLE --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="flex justify-between items-center p-5 border-b">
                    <h3 class="font-semibold text-lg">All Appointments</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="p-3 text-left">Patient</th>
                                <th class="p-3 text-left">Doctor</th>
                                <th class="p-3 text-left">Date</th>
                                <th class="p-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="p-3 font-medium text-slate-800">
                                        {{ $appointment->patient->name }}
                                    </td>

                                    <td class="p-3 text-gray-600">
                                        Dr. {{ $appointment->doctor->name }}
                                    </td>

                                    <td class="p-3 text-gray-500">
                                        {{ $appointment->appointment_date }}
                                    </td>

                                    <td class="p-3">
                                        <span class="text-xs px-2 py-1 rounded-full
                                            @if($appointment->status == 'approved') bg-green-100 text-green-700
                                            @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($appointment->status == 'cancelled') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-gray-500">
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