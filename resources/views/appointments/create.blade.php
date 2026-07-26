<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-6">Book Appointment</h2>

        <input type="hidden" id="doctor_id" value="{{ $doctorId }}">

        {{-- Calendar --}}
        <div id="calendar"></div>

        {{-- Booking Form --}}
        <form method="POST" action="{{ route('appointments.store') }}" class="mt-6">
            @csrf

            <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
            <input type="hidden" name="date" id="form_date">
            <input type="hidden" name="time" id="form_time">

            <button id="bookBtn"
                class="bg-blue-600 text-white px-6 py-2 rounded disabled:opacity-50"
                disabled>
                Book Selected Slot
            </button>
        </form>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const doctorId = document.getElementById('doctor_id').value;
    const calendarEl = document.getElementById('calendar');

    const formDate = document.getElementById('form_date');
    const formTime = document.getElementById('form_time');
    const bookBtn = document.getElementById('bookBtn');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        selectable: true,
        nowIndicator: true,
        height: "auto",

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: function(fetchInfo, successCallback, failureCallback) {

            let start = fetchInfo.startStr.split('T')[0];
            let end = fetchInfo.endStr.split('T')[0];

            fetch(`/calendar/events?doctor_id=${doctorId}&start=${start}&end=${end}`)
                .then(res => res.json())
                .then(data => successCallback(data))
                .catch(() => failureCallback());
        },

        eventClick: function(info) {

            if (info.event.extendedProps.booked) {
                alert("❌ This slot is already booked");
                return;
            }

            let date = info.event.startStr.split('T')[0];
            let time = info.event.startStr.split('T')[1].substring(0,5);

            formDate.value = date;
            formTime.value = time;

            bookBtn.disabled = false;

            alert("✅ Selected: " + date + " " + time);
        }

    });

    calendar.render();
});
</script>

</x-app-layout>