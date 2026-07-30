<![CDATA[<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="page-title">Book Appointment</span>
                <p class="page-subtitle">Select a time slot and provide visit details</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        <input type="hidden" id="doctor_id" value="{{ $doctorId }}">

        {{-- Calendar Card --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Select a Time Slot</h3>
                </div>
                <span class="badge-info">Click an available slot</span>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>

        {{-- Booking Form --}}
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <x-icon name="document" class="w-5 h-5 text-brand-600" />
                    <h3 class="section-title">Visit Details</h3>
                </div>
            </div>

            <form method="POST" action="{{ route('appointments.store') }}" class="card-body space-y-5">
                @csrf

                <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
                <input type="hidden" name="date" id="form_date">
                <input type="hidden" name="time" id="form_time">

                {{-- Selected slot indicator --}}
                <div id="slot_indicator" class="hidden rounded-lg bg-brand-50 border border-brand-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100">
                            <x-icon name="check" class="w-4 h-4 text-brand-600" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-brand-900">Selected Slot</p>
                            <p id="slot_text" class="text-sm text-brand-700"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Reason for Visit --}}
                    <div class="form-group md:col-span-2">
                        <label class="form-label">Reason for Visit</label>
                        <textarea name="reason" rows="3" class="form-input" placeholder="Describe why you're visiting...">{{ old('reason', $aiPrefill['reason'] ?? '') }}</textarea>
                    </div>

                    {{-- Symptoms --}}
                    <div class="form-group md:col-span-2">
                        <label class="form-label">Symptoms</label>
                        <textarea name="symptoms" rows="3" class="form-input" placeholder="List symptoms...">{{ old('symptoms', $aiPrefill['symptoms'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- AI Summary --}}
                @if(!empty($aiPrefill['summary']))
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2">
                            <x-icon name="sparkles" class="w-4 h-4 text-violet-600" />
                            AI Summary
                        </label>
                        <textarea class="form-input bg-slate-50" rows="4" readonly>{{ $aiPrefill['summary'] }}</textarea>
                    </div>
                @endif

                {{-- Recurrence --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group">
                        <label class="form-label">Repeat</label>
                        <select name="recurrence_type" class="form-input">
                            <option value="">No Repeat</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Repeat Count</label>
                        <input type="number" name="recurrence_count" min="1" max="30" class="form-input" placeholder="e.g. 4">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button id="bookBtn" class="btn-primary btn-lg w-full" disabled>
                        <x-icon name="check" class="w-5 h-5" />
                        Book Selected Slot
                    </button>
                    <p id="bookHint" class="text-center text-sm text-slate-400 mt-2">Please select a time slot from the calendar above</p>
                </div>
            </form>
        </div>

    </div>

    {{-- FULLCALENDAR --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    {{-- PUSHER + ECHO --}}
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const doctorId = document.getElementById('doctor_id').value;
        const calendarEl = document.getElementById('calendar');

        const formDate = document.getElementById('form_date');
        const formTime = document.getElementById('form_time');
        const bookBtn = document.getElementById('bookBtn');
        const slotIndicator = document.getElementById('slot_indicator');
        const slotText = document.getElementById('slot_text');
        const bookHint = document.getElementById('bookHint');

        let calendar;

        calendar = new FullCalendar.Calendar(calendarEl, {
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
                let time = info.event.startStr.split('T')[1]?.substring(0,5);

                if (!time) return;

                formDate.value = date;
                formTime.value = time;

                bookBtn.disabled = false;
                bookHint.classList.add('hidden');
                slotIndicator.classList.remove('hidden');
                slotText.textContent = date + ' at ' + time;
            }

        });

        calendar.render();

        // REAL-TIME SYNC
        const echo = new Echo({
            broadcaster: 'pusher',
            key: 'local',
            wsHost: window.location.hostname,
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
        });

        echo.channel('doctor.' + doctorId)
            .listen('SlotBooked', () => {
                calendar.refetchEvents();
            });

    });
    </script>

</x-app-layout>
]]>