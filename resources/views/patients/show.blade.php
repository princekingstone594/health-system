<x-app-layout>
    <x-slot name="header">
        Patient Profile
    </x-slot>

```
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">{{ $patient->name }}</h2>

        <a href="{{ route('appointments.create', $patient->id) }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Book Appointment
        </a>
    </div>

    <!-- Patient Info -->
    <div class="mb-6">
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Phone:</strong> {{ $patient->phone }}</p>
    </div>

    <!-- Appointments -->
    <div>
        <h3 class="text-lg font-semibold mb-3">Appointments</h3>

        @forelse($appointments as $appointment)
            <div class="border p-4 rounded mb-3">
                <p><strong>Doctor:</strong> {{ $appointment->doctor->name }}</p>
                <p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>

                <p>
                    <strong>Status:</strong>
                    <span class="
                        px-2 py-1 rounded text-white text-sm
                        @if($appointment->status == 'Scheduled') bg-blue-500
                        @elseif($appointment->status == 'Completed') bg-green-500
                        @else bg-red-500
                        @endif
                    ">
                        {{ $appointment->status }}
                    </span>
                </p>
            </div>
        @empty
            <p class="text-gray-500">No appointments yet.</p>
        @endforelse
    </div>

</div>
```

</x-app-layout>
