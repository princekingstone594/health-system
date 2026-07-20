<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Edit Appointment
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
                <select name="doctor_id" id="doctor" class="w-full border rounded px-3 py-2">
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
                <input type="date"
                       name="appointment_date"
                       id="date"
                       value="{{ $appointment->appointment_date }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <!-- Time -->
            <div class="mb-4">
                <label class="block mb-1">Time Slot</label>
                <select name="appointment_time" id="time" class="w-full border p-2">
                    @foreach($timeslots as $slot)
                        <option value="{{ $slot }}"
                            {{ $appointment->appointment_time == $slot ? 'selected' : '' }}>
                            {{ $slot }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Appointment
            </button>
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const doctorSelect = document.getElementById('doctor');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');

    function fetchBookedSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) return;

        fetch(`/appointments/booked-slots?doctor_id=${doctorId}&appointment_date=${date}`)
            .then(res => res.json())
            .then(bookedSlots => {

                Array.from(timeSelect.options).forEach(option => {
                    if (bookedSlots.includes(option.value) && option.value !== "{{ $appointment->appointment_time }}") {
                        option.remove();
                    }
                });

            });
    }

    doctorSelect.addEventListener('change', fetchBookedSlots);
    dateInput.addEventListener('change', fetchBookedSlots);
});
</script>