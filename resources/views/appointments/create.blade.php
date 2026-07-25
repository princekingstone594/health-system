<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-6">Book Appointment</h2>

        {{-- Doctor ID (hidden or from route) --}}
        <input type="hidden" id="doctor_id" value="{{ $doctorId }}">

        {{-- Date Picker --}}
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Select Date</label>
            <input type="date" id="date" class="w-full border p-2 rounded">
        </div>

        {{-- Slots Container --}}
        <div class="mb-6">
            <label class="block mb-2 font-semibold">Available Slots</label>

            <div id="slots" class="grid grid-cols-3 gap-3">
                <p class="text-gray-500">Select a date to load slots</p>
            </div>
        </div>

        {{-- Booking Form --}}
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <input type="hidden" name="doctor_id" id="form_doctor_id" value="{{ $doctorId }}">
            <input type="hidden" name="date" id="form_date">
            <input type="hidden" name="time" id="form_time">

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded disabled:opacity-50"
                id="bookBtn" disabled>
                Book Appointment
            </button>
        </form>

    </div>

    {{-- ✅ AJAX + UI SCRIPT --}}
    <script>
        const dateInput = document.getElementById('date');
        const slotsDiv = document.getElementById('slots');
        const doctorId = document.getElementById('doctor_id').value;

        const formDate = document.getElementById('form_date');
        const formTime = document.getElementById('form_time');
        const bookBtn = document.getElementById('bookBtn');

        dateInput.addEventListener('change', function () {

            const date = this.value;
            formDate.value = date;

            slotsDiv.innerHTML = '<p class="text-gray-500">Loading...</p>';

            fetch(`/appointments/slots?doctor_id=${doctorId}&date=${date}`)
                .then(res => res.json())
                .then(data => {

                    slotsDiv.innerHTML = '';

                    if (data.length === 0) {
                        slotsDiv.innerHTML = '<p class="text-red-500">No slots available</p>';
                        return;
                    }

                    data.forEach(time => {

                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.innerText = time;

                        btn.className = "border px-3 py-2 rounded hover:bg-blue-500 hover:text-white";

                        btn.onclick = () => {
                            formTime.value = time;
                            bookBtn.disabled = false;

                            // Highlight selected
                            document.querySelectorAll('#slots button')
                                .forEach(b => b.classList.remove('bg-blue-600', 'text-white'));

                            btn.classList.add('bg-blue-600', 'text-white');
                        };

                        slotsDiv.appendChild(btn);
                    });
                });
        });
    </script>

</x-app-layout>