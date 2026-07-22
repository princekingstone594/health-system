<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Dashboard
        </h2>
    </x-slot>

<div class="p-4 md:p-6 bg-gray-100 min-h-screen">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
            <h3 class="text-gray-500 text-sm">Patients</h3>
            <p class="text-2xl font-bold text-blue-600">
                {{ \App\Models\Patient::count() }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
            <h3 class="text-gray-500 text-sm">Doctors</h3>
            <p class="text-2xl font-bold text-green-600">
                {{ \App\Models\Doctor::count() }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition">
            <h3 class="text-gray-500 text-sm">Appointments</h3>
            <p class="text-2xl font-bold text-purple-600">
                {{ \App\Models\Appointment::count() }}
            </p>
        </div>

    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Search -->
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search patient or doctor..."
                class="border rounded px-3 py-2 w-full">

            <!-- Status -->
            <select name="status" class="border rounded px-3 py-2 w-full">
                <option value="">All Status</option>
                <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <!-- Date -->
            <input type="date" name="date" value="{{ request('date') }}"
                class="border rounded px-3 py-2 w-full">

            <!-- Buttons -->
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                    Filter
                </button>

                <a href="{{ route('dashboard') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 w-full text-center">
                   Reset
                </a>
            </div>

        </div>
    </form>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-4 border-b flex flex-col md:flex-row md:justify-between md:items-center gap-3">
            <h3 class="font-semibold text-gray-700">Recent Appointments</h3>

            <a href="{{ route('appointments.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
               + Book Appointment
            </a>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="p-3">Patient</th>
                        <th class="p-3">Doctor</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Time</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($recentAppointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3">{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $appointment->appointment_date }}</td>
                            <td class="p-3">{{ $appointment->appointment_time }}</td>

                            <td class="p-3">
                                @if($appointment->status == 'Scheduled')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                        Scheduled
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                                        Cancelled
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 flex gap-2">
                                <a href="{{ route('appointments.edit', $appointment->id) }}"
                                   class="bg-yellow-400 text-white px-2 py-1 rounded text-xs hover:bg-yellow-500">
                                   Edit
                                </a>

                                <form action="{{ route('appointments.cancel', $appointment->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Cancel this appointment?')">
                                    @csrf
                                    @method('PATCH')

                                    <button class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No recent appointments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden p-4 space-y-4">
            @forelse($recentAppointments as $appointment)
                <div class="border rounded-lg p-4 shadow-sm">
                    <p><strong>Patient:</strong> {{ $appointment->patient->name ?? 'N/A' }}</p>
                    <p><strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'N/A' }}</p>
                    <p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
                    <p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>

                    <p class="mt-2">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $appointment->status == 'Scheduled' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $appointment->status }}
                        </span>
                    </p>

                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('appointments.edit', $appointment->id) }}"
                           class="bg-yellow-400 text-white px-3 py-1 rounded text-xs w-full text-center">
                           Edit
                        </a>

                        <form action="{{ route('appointments.cancel', $appointment->id) }}"
                              method="POST" class="w-full">
                            @csrf
                            @method('PATCH')

                            <button class="bg-red-500 text-white px-3 py-1 rounded text-xs w-full">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">No recent appointments found</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $recentAppointments->links('pagination::tailwind') }}
        </div>

    </div>

</div>
</x-app-layout>