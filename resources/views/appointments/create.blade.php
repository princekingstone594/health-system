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
            <input type="date"
                   name="appointment_date"
                   required class="border p-2 w-full">
                                   
        </div>

        <!-- Time -->
         <select name="appointment_time" class="border p-2 w-full mt-2" required>
            <option value="">Select Time</option>
            @foreach($timeslots as $slot)
                <option value="{{ $slot }}">{{ $slot }}</option>
            @endforeach

        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Book Appointment
        </button>

    </form>
</div>
```

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const doctorSelect = document.querySelector('select[name="doctor_id"]');
    const dateInput = document.querySelector('input[name="appointment_date"]');
    const timeSelect = document.querySelector('select[name="appointment_time"]');

    const allSlots = @json($timeslots);

    function fetchBookedSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) return;

        fetch(`/appointments/booked-slots?doctor_id=${doctorId}&appointment_date=${date}`)
            .then(res => res.json())
            .then(bookedSlots => {
                timeSelect.innerHTML = '<option value="">Select Time</option>';

                allSlots.forEach(slot => {
                    if (!bookedSlots.includes(slot)) {
                        let option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        timeSelect.appendChild(option);
                    }
                });
            });
    }

    doctorSelect.addEventListener('change', fetchBookedSlots);
    dateInput.addEventListener('change', fetchBookedSlots);
});
</script>
