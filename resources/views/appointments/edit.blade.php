<x-app-layout>
    <x-slot name="header">
        Edit Appointment
    </x-slot>

```
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
        @csrf
        @method('PUT')

        <!-- Patient -->
        <div class="mb-4">
            <label class="block mb-1">Patient</label>
            <select name="patient_id" class="w-full border rounded px-3 py-2">
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}"
                        {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Doctor -->
        <div class="mb-4">
            <label class="block mb-1">Doctor</label>
            <select name="doctor_id" class="w-full border rounded px-3 py-2">
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}"
                        {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div class="mb-4">
            <label class="block mb-1">Date</label>
            <input type="datetime-local"
                   name="appointment_date"
                   value="{{ $appointment->appointment_date }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update Appointment
        </button>
    </form>
</div>
```

</x-app-layout>
