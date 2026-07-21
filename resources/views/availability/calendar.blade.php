<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Calendar</h2>

            <!-- NAVIGATION -->
            <div class="flex gap-2">
                <a href="{{ route('availability.calendar', ['month' => $start->copy()->subMonth()->month, 'year' => $start->copy()->subMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded">←</a>

                <span class="px-4 py-1 font-semibold">
                    {{ $start->format('F Y') }}
                </span>

                <a href="{{ route('availability.calendar', ['month' => $start->copy()->addMonth()->month, 'year' => $start->copy()->addMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded">→</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6 bg-white dark:bg-gray-800 p-4 rounded-xl shadow">

        <!-- DAYS HEADER -->
        <div class="grid grid-cols-7 text-center font-semibold text-gray-600 mb-2">
            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
            <div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <!-- CALENDAR GRID -->
        <div class="grid grid-cols-7 gap-px bg-gray-300">

            @php
                $current = $start->copy()->startOfWeek();
            @endphp

            @while($current <= $end->copy()->endOfWeek())

                @php
                    $date = $current->toDateString();
                    $dayAppointments = $appointments[$date] ?? collect();
                    $isCurrentMonth = $current->month == $start->month;
                @endphp

                <div class="h-28 bg-white p-1 text-xs relative
                    {{ $isCurrentMonth ? '' : 'bg-gray-100 text-gray-400' }}"
                    onclick="openDay('{{ $date }}')"
                >

                    <!-- DATE -->
                    <div class="font-bold text-sm">
                        {{ $current->day }}
                    </div>

                    <!-- EVENTS -->
                    <div class="mt-1 space-y-1 overflow-hidden">

                        @foreach($dayAppointments->take(3) as $appt)
                            <div class="bg-blue-500 text-white px-1 rounded text-[10px] truncate">
                                {{ $appt->appointment_time }}
                            </div>
                        @endforeach

                        @if($dayAppointments->count() > 3)
                            <div class="text-[10px] text-gray-500">
                                +{{ $dayAppointments->count() - 3 }} more
                            </div>
                        @endif

                    </div>

                </div>

                @php $current->addDay(); @endphp

            @endwhile

        </div>
    </div>

    <!-- MODAL -->
    <div id="dayModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-96 p-6 rounded-xl shadow-lg">

            <h3 class="text-lg font-semibold mb-3">Appointments</h3>

            <div id="dayContent">Loading...</div>

            <button onclick="closeModal()" class="mt-4 bg-gray-700 text-white px-4 py-2 rounded">
                Close
            </button>
        </div>
    </div>

</x-app-layout>

<script>
function openDay(date) {
    document.getElementById('dayModal').classList.remove('hidden');

    fetch(`/appointments/booked-slots?doctor_id={{ auth()->user()->doctor->id }}&appointment_date=${date}`)
        .then(res => res.json())
        .then(slots => {

            let html = `<p class="mb-2"><strong>${date}</strong></p>`;

            if (slots.length === 0) {
                html += `<p class="text-green-600">No appointments</p>`;
            } else {
                html += `<ul class="space-y-1">`;
                slots.forEach(s => {
                    html += `<li class="bg-blue-100 p-2 rounded">🕒 ${s}</li>`;
                });
                html += `</ul>`;
            }

            document.getElementById('dayContent').innerHTML = html;
        });
}

function closeModal() {
    document.getElementById('dayModal').classList.add('hidden');
}
</script>