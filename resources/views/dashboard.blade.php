<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Dashboard
            </h2>

            <a href="{{ route('appointments.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
               + Book Appointment
            </a>
        </div>
    </x-slot>

<div class="p-4 md:p-6 space-y-6">

    <!-- ===================== -->
    <!-- Stats Cards -->
    <!-- ===================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition">
            <p class="text-sm text-gray-500">Patients</p>
            <h2 class="text-2xl font-bold text-blue-600 mt-2">
                {{ \App\Models\Patient::count() }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition">
            <p class="text-sm text-gray-500">Doctors</p>
            <h2 class="text-2xl font-bold text-green-600 mt-2">
                {{ \App\Models\Doctor::count() }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition">
            <p class="text-sm text-gray-500">Appointments</p>
            <h2 class="text-2xl font-bold text-purple-600 mt-2">
                {{ \App\Models\Appointment::count() }}
            </h2>
        </div>

    </div>

    <!-- ===================== -->
    <!-- Filters -->
    <!-- ===================== -->
    <form method="GET" class="bg-white p-5 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Search -->
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search patient or doctor..."
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">

            <!-- Status -->
            <select name="status"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>
                    Scheduled
                </option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>
            </select>

            <!-- Date -->
            <input type="date" name="date" value="{{ request('date') }}"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">

            <!-- Buttons -->
            <div class="flex gap-2">
                <button
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>

                <a href="{{ route('dashboard') }}"
                   class="w-full bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition text-center">
                   Reset
                </a>
            </div>

        </div>
    </form>

    <!-- ===================== -->
    <!-- Success Message -->
    <!-- ===================== -->
    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- ===================== -->
    <!-- Table Card -->
    <!-- ===================== -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="p-4 border-b flex flex-col md:flex-row md:justify-between md:items-center gap-3">
            <h3 class="font-semibold text-gray-700">
                Recent Appointments
            </h3>
        </div>

        <!-- ===================== -->
        <!-- Desktop Table -->
        <!-- ===================== -->
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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3">{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $appointment->appointment_date }}</td>
                            <td class="p-3">{{ $appointment->appointment_time }}</td>

                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $appointment->status == 'Scheduled'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700' }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>

                            <td class="p-3 flex gap-2">
                                <a href="{{ route('appointments.edit', $appointment->id) }}"
                                   class="bg-yellow-400 text-white px-3 py-1 rounded-md text-xs hover:bg-yellow-500 transition">
                                   Edit
                                </a>

                                <form action="{{ route('appointments.cancel', $appointment->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Cancel this appointment?')">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        class="bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-600 transition">
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

        <!-- ===================== -->
        <!-- Mobile Cards -->
        <!-- ===================== -->
        <div class="md:hidden p-4 space-y-4">
            @forelse($recentAppointments as $appointment)
                <div class="border rounded-xl p-4 shadow-sm space-y-1">
                    <p><strong>Patient:</strong> {{ $appointment->patient->name ?? 'N/A' }}</p>
                    <p><strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'N/A' }}</p>
                    <p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
                    <p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>

                    <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs
                        {{ $appointment->status == 'Scheduled'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ $appointment->status }}
                    </span>

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

        <!-- ===================== -->
        <!-- Pagination -->
        <!-- ===================== -->
        <div class="p-4">
            {{ $recentAppointments->links('pagination::tailwind') }}
        </div>

    </div>

</div>
</x-app-layout>