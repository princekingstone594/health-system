<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Doctor Calendar</h2>
    </x-slot>

    <div class="p-6">
        <div id="calendar"></div>
    </div>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',

                height: "auto",

                events: @json($appointments),

                eventClick: function(info) {
                    alert(info.event.title);
                },

                dateClick: function(info) {
                    alert("Date: " + info.dateStr);
                }
            });

            calendar.render();
        });
    </script>
</x-app-layout>