<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

```
<!-- Top Buttons -->
<div class="mb-4 flex gap-2">
    <a href="{{ route('patients.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
       + Add Patient
    </a>

    <a href="{{ route('patients.index') }}"
       class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
       View All Patients
    </a>

    <a href="{{ route('appointments.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
       + Book Appointment
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Total Patients</p>
        <h2 class="text-3xl font-bold mt-2">{{ $patientsCount }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Appointments Today</p>
        <h2 class="text-3xl font-bold mt-2">{{ $appointmentsToday }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500 text-sm">Doctors Available</p>
        <h2 class="text-3xl font-bold mt-2">{{ $doctorsCount }}</h2>
    </div>
</div>

<!-- Main Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Recent Patients -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Patients</h3>

        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 text-sm border-b">
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Age</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($recentPatients as $patient)
                    <tr class="border-b">
                        <td class="py-2">{{ $patient->name }}</td>
                        <td>{{ $patient->age }}</td>
                        <td class="
                            @if($patient->status === 'Stable') text-green-600
                            @elseif($patient->status === 'Critical') text-red-600
                            @else text-yellow-600
                            @endif
                        ">
                            {{ $patient->status }}
                        </td>

                        <td class="py-2">
                            <a href="{{ route('patients.edit', $patient->id) }}"
                               class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                Edit
                            </a>

                            <form action="{{ route('patients.destroy', $patient->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Delete this patient?')"
                                        class="bg-red-600 text-white px-2 py-1 rounded text-xs">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 text-gray-500">
                            No patients found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- System Info -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-4">System Info</h3>

        <ul class="space-y-3 text-sm text-gray-600">
            <li>Total Patients: {{ $patientsCount }}</li>
            <li>Doctors: {{ $doctorsCount }}</li>
            <li>Appointments Today: {{ $appointmentsToday }}</li>
        </ul>
    </div>

    <!-- Recent Appointments -->
    <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-bold mb-4">Recent Appointments</h2>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">Patient</th>
                    <th class="p-2 border">Doctor</th>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appointment)
                    <tr>
                        <td class="p-2 border">
                            {{ optional($appointment->patient)->name ?? 'N/A' }}
                        </td>

                        <td class="p-2 border">
                            {{ optional($appointment->doctor)->name ?? 'N/A' }}
                        </td>

                        <td class="p-2 border">
                            {{ $appointment->appointment_date }}
                        </td>

                        <td class="p-2 border">
                            <span class="
                                px-2 py-1 rounded text-white text-sm
                                @if($appointment->status == 'Scheduled') bg-blue-500
                                @elseif($appointment->status == 'Completed') bg-green-500
                                @else bg-red-500
                                @endif
                            ">
                                {{ $appointment->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-2 text-gray-500">
                            No appointments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
```

</x-app-layout>
