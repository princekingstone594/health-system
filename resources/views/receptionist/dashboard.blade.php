<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Receptionist Dashboard</h1>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 shadow rounded">
                <p class="text-sm text-gray-500">Total Appointments</p>
                <p class="text-xl font-bold">{{ $totalAppointments }}</p>
            </div>

            <div class="bg-white p-4 shadow rounded">
                <p class="text-sm text-gray-500">Today's Appointments</p>
                <p class="text-xl font-bold">{{ $todayAppointments }}</p>
            </div>

            <div class="bg-white p-4 shadow rounded">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-xl font-bold">{{ $pendingAppointments }}</p>
            </div>
        </div>

        <h2 class="text-lg font-semibold mb-2">All Appointments</h2>

        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="border-b">
                    <th class="p-2 text-left">Patient</th>
                    <th class="p-2 text-left">Doctor</th>
                    <th class="p-2 text-left">Date</th>
                    <th class="p-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr class="border-b">
                        <td class="p-2">{{ $appointment->patient->name }}</td>
                        <td class="p-2">{{ $appointment->doctor->name }}</td>
                        <td class="p-2">{{ $appointment->appointment_date }}</td>
                        <td class="p-2">{{ ucfirst($appointment->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>