<x-app-layout>
    <x-slot name="header">
        Book Appointment
    </x-slot>

```
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <!-- Success / Errors -->
    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf

        <!-- Patient -->
        <div class="mb-4">
            <label class="block mb-1">Select Patient</label>
            <select name="patient_id" class="w-full border rounded px-3 py-2">
                @foreach($patients as $p)
                    <option value="{{ $p->id }}"
                        {{ (isset($patient) && $patient->id == $p->id) || old('patient_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Doctor -->
        <div class="mb-4">
            <label class="block mb-1">Select Doctor</label>
            <select name="doctor_id" class="w-full border rounded px-3 py-2">
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}"
                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div class="mb-4">
            <label class="block mb-1">Appointment Date</label>
            <input type="datetime-local"
                   name="appointment_date"
                   value="{{ old('appointment_date') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Book Appointment
        </button>

    </form>
</div>
```

</x-app-layout>
