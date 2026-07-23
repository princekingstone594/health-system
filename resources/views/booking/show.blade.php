<x-app-layout>
<x-slot name="header">
    Book Appointment with {{ $doctor->name }}
</x-slot>

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow space-y-6">

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Clinic -->
    <div>
        <label class="block text-sm font-medium">Clinic</label>
        <select id="clinic" class="w-full border p-2 rounded">
            <option value="">Select clinic</option>
            @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Date -->
    <div>
        <label class="block text-sm font-medium">Date</label>
        <input type="date" id="date" class="w-full border p-2 rounded">
    </div>

    <!-- Slots -->
    <div>
        <label class="block text-sm font-medium">Available Slots</label>
        <div id="slots" class="grid grid-cols-4 gap-2 mt-2"></div>
    </div>

    <!-- Booking Form -->
    <form method="POST" action="{{ route('payment.checkout') }}">
        @csrf

        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
        <input type="hidden" name="clinic_id" id="clinic_input">
        <input type="hidden" name="date" id="date_input">
        <input type="hidden" name="time" id="time_input">

        <input type="text" name="name" placeholder="Your Name"
               class="w-full border p-2 rounded mb-2">

        <input type="text" name="phone" placeholder="Phone"
               class="w-full border p-2 rounded mb-4">

        <button class="w-full bg-blue-600 text-white py-2 rounded">
            Book Appointment
        </button>
    </form>

</div>

<script>
const clinic = document.getElementById('clinic');
const date = document.getElementById('date');
const slotsDiv = document.getElementById('slots');

clinic.addEventListener('change', loadSlots);
date.addEventListener('change', loadSlots);

function loadSlots() {
    if (!clinic.value || !date.value) return;

    fetch(`/booking/slots?doctor_id={{ $doctor->id }}&clinic_id=${clinic.value}&date=${date.value}`)
        .then(res => res.json())
        .then(data => {
            slotsDiv.innerHTML = '';

            data.forEach(time => {
                const btn = document.createElement('button');
                btn.innerText = time;
                btn.type = "button";
                btn.className = "bg-gray-200 px-2 py-1 rounded";

                btn.onclick = () => selectSlot(time);

                slotsDiv.appendChild(btn);
            });
        });
}

function selectSlot(time) {
    document.getElementById('time_input').value = time;
    document.getElementById('clinic_input').value = clinic.value;
    document.getElementById('date_input').value = date.value;

    document.querySelectorAll('#slots button').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white');
    });

    event.target.classList.add('bg-blue-500', 'text-white');
}
</script>

</x-app-layout>