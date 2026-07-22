<x-app-layout>
<x-slot name="header">Book Appointment</x-slot>

<div class="grid grid-cols-3 gap-6">

    <!-- LEFT: Doctor + Date -->
    <div class="bg-white p-6 rounded-xl shadow space-y-4">

        <div>
            <label class="text-sm font-medium">Doctor</label>
            <select id="doctor" class="w-full border rounded-lg px-3 py-2">
                <option value="">Select Doctor</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}"
                        {{ (isset($selectedDoctor) && $selectedDoctor == $doc->id) ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">Date</label>
            <input type="date"
                   id="date"
                   value="{{ $selectedDate ?? '' }}"
                   min="{{ date('Y-m-d') }}"
                   class="w-full border rounded-lg px-3 py-2">
        </div>

    </div>

    <!-- CENTER: Slots -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-3">Available Slots</h2>

        <div id="slots" class="grid grid-cols-3 gap-2">
            <p class="text-gray-400 text-sm">Select doctor & date</p>
        </div>
    </div>

    <!-- RIGHT: Booking Summary -->
    <div class="bg-white p-6 rounded-xl shadow space-y-4">

        <h2 class="font-semibold">Booking Details</h2>

        <div class="text-sm text-gray-600 space-y-1">
            <p><strong>Doctor:</strong> <span id="selectedDoctor">-</span></p>
            <p><strong>Date:</strong> <span id="selectedDate">-</span></p>
            <p><strong>Time:</strong> <span id="selectedTime">-</span></p>
        </div>

        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <input type="hidden" name="doctor_id" id="doctorInput">
            <input type="hidden" name="appointment_date" id="dateInput">
            <input type="hidden" name="appointment_time" id="timeInput">

            <!-- Patient -->
            <div>
                <label class="text-sm font-medium">Patient</label>
                <select name="patient_id" class="w-full border rounded-lg px-3 py-2">
                    @foreach(\App\Models\Patient::all() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Confirm Booking
            </button>
        </form>

    </div>

</div>

<script>
let selectedDoctor = null;
let selectedDate = null;
let selectedTime = null;

// Update summary panel + hidden inputs
function updateSummary() {
    document.getElementById('selectedDoctor').innerText =
        document.querySelector('#doctor option:checked')?.text || '-';

    document.getElementById('selectedDate').innerText = selectedDate || '-';
    document.getElementById('selectedTime').innerText = selectedTime || '-';

    document.getElementById('doctorInput').value = selectedDoctor || '';
    document.getElementById('dateInput').value = selectedDate || '';
    document.getElementById('timeInput').value = selectedTime || '';
}

// Load slots via AJAX
function loadSlots() {
    if (!selectedDoctor || !selectedDate) return;

    fetch(`/booking/slots?doctor_id=${selectedDoctor}&date=${selectedDate}`)
        .then(res => res.json())
        .then(data => {
            let container = document.getElementById('slots');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-400">No slots available</p>';
                return;
            }

            data.forEach(slot => {
                let btn = document.createElement('button');
                btn.innerText = slot;

                btn.className =
                    "px-3 py-2 rounded border text-sm hover:bg-blue-500 hover:text-white transition";

                btn.onclick = () => {

                    // remove highlight from others
                    document.querySelectorAll('#slots button').forEach(b => {
                        b.classList.remove('bg-blue-600','text-white');
                    });

                    // highlight selected
                    btn.classList.add('bg-blue-600','text-white');

                    selectedTime = slot;
                    updateSummary();
                };

                container.appendChild(btn);
            });
        });
}

// Event listeners
document.getElementById('doctor').addEventListener('change', function() {
    selectedDoctor = this.value;
    selectedTime = null;
    updateSummary();
    loadSlots();
});

document.getElementById('date').addEventListener('change', function() {
    selectedDate = this.value;
    selectedTime = null;
    updateSummary();
    loadSlots();
});

// ✅ AUTO-FILL FROM CALENDAR (IMPORTANT)
window.addEventListener('DOMContentLoaded', () => {

    let doctorEl = document.getElementById('doctor');
    let dateEl = document.getElementById('date');

    if (doctorEl.value) {
        selectedDoctor = doctorEl.value;
    }

    if (dateEl.value) {
        selectedDate = dateEl.value;
    }

    updateSummary();

    if (selectedDoctor && selectedDate) {
        loadSlots();
    }
});
</script>

</x-app-layout>