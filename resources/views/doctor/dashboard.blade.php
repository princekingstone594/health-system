<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Doctor Dashboard</h2>
    </x-slot>

    <div class="p-6 space-y-6">

        <!-- 📊 Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Total Appointments</h3>
                <p class="text-2xl font-bold">{{ $totalAppointments }}</p>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Today's Appointments</h3>
                <p class="text-2xl font-bold">{{ $todayCount }}</p>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-gray-500">Status</h3>
                <p class="text-green-600 font-semibold">Active</p>
            </div>

        </div>

        <!-- 📅 Today -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="mb-4 font-semibold">Today's Appointments</h3>

            @forelse($todayAppointments as $appt)
                <div class="flex justify-between items-center border-b py-2">
                    <div>
                        <p class="font-semibold">{{ $appt->patient->name ?? 'Patient' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $appt->date }} at {{ $appt->time }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('doctor.appointment.status', $appt->id) }}">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                            <option value="pending" {{ $appt->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $appt->status == 'approved' ? 'selected' : '' }}>Approve</option>
                            <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Complete</option>
                            <option value="cancelled" {{ $appt->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                        </select>
                    </form>
                </div>
            @empty
                <p>No appointments today.</p>
            @endforelse
        </div>

        <!-- 📆 Upcoming -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="mb-4 font-semibold">Upcoming Appointments</h3>

            @forelse($upcomingAppointments as $appt)
                <div class="border-b py-2">
                    <p class="font-semibold">{{ $appt->patient->name ?? 'Patient' }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $appt->date }} at {{ $appt->time }}
                    </p>
                </div>
            @empty
                <p>No upcoming appointments.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>