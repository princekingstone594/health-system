<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Book Appointment
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-xl shadow mt-6">

        <!-- Errors -->
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
                <select name="doctor_id" id="doctor" class="w-full border rounded px-3 py-2">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
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
                       id="date"
                       value="{{ old('appointment_date') }}"
                       class="border p-2 w-full"
                       required>
            </div>

            <!-- Time -->
            <div class="mb-4">
                <label class="block mb-1">Time Slot</label>
                <select name="appointment_time" id="time" class="border p-2 w-full" required>
                    <option value="">Select Time</option>
                    @foreach($timeslots as $slot)
                        <option value="{{ $slot }}">{{ $slot }}</option>
                    @endforeach
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Book Appointment
            </button>

        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const doctorSelect = document.getElementById('doctor');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');

    function fetchSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) return;

        // STEP 1: Get AVAILABLE slots from server
        fetch(`/appointments/create?doctor_id=${doctorId}&appointment_date=${date}`)
            .then(res => res.text())
            .then(html => {
                // Extract slots (quick hack via DOM parsing)
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let options = doc.querySelectorAll('#time option');

                timeSelect.innerHTML = '';

                options.forEach(opt => {
                    timeSelect.appendChild(opt.cloneNode(true));
                });

                // STEP 2: Remove booked slots
                return fetch(`/appointments/booked-slots?doctor_id=${doctorId}&appointment_date=${date}`);
            })
            .then(res => res.json())
            .then(bookedSlots => {

                Array.from(timeSelect.options).forEach(option => {
                    if (bookedSlots.includes(option.value)) {
                        option.remove();
                    }
                });

            });
    }

    doctorSelect.addEventListener('change', fetchSlots);
    dateInput.addEventListener('change', fetchSlots);
});
</script>