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

          <h3 class="text-lg font-semibold mb-3">Create Appointment</h3>

          <form id="appointmentForm">
             @csrf

             <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor->id }}">
             <input type="hidden" name="appointment_date" id="modalDate">

             <!-- Date Display -->
             <p class="mb-2 text-sm text-gray-600" id="displayDate"></p>

             <!-- Patient -->
             <select name="patient_id" class="w-full border p-2 mb-2">
                @foreach(\App\Models\Patient::all() as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
             </select>

             <!-- Time -->
             <select name="appointment_time" id="modalTime" class="w-full border p-2 mb-2">
                <option value="">Select time</option>
             </select>

             <!-- Errors -->
             <div id="formError" class="text-red-500 text-sm mb-2"></div>

             <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                 Save Appointment
             </button>
            </form>

            <button onclick="closeModal()" class="mt-3 text-gray-600 w-full">
               Cancel
            </button>
        </div>
    </div>

</x-app-layout>

<script>
function openDay(date) {
    document.getElementById('dayModal').classList.remove('hidden');

    document.getElementById('modalDate').value = date;
    document.getElementById('displayDate').innerText = date;

    const doctorId = "{{ auth()->user()->doctor->id }}";
    const timeSelect = document.getElementById('modalTime');

    // Fetch available slots
    fetch(`/appointments/create?doctor_id=${doctorId}&appointment_date=${date}`)
        .then(res => res.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let options = doc.querySelectorAll('#time option');

            timeSelect.innerHTML = '';

            options.forEach(opt => {
                timeSelect.appendChild(opt.cloneNode(true));
            });

            // Remove booked slots
            return fetch(`/appointments/booked-slots?doctor_id=${doctorId}&appointment_date=${date}`);
        })
        .then(res => res.json())
        .then(booked => {
            Array.from(timeSelect.options).forEach(option => {
                if (booked.includes(option.value)) {
                    option.remove();
                }
            });
        });
}

function closeModal() {
    document.getElementById('dayModal').classList.add('hidden');
}

document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let form = e.target;
    let data = new FormData(form);

    fetch("{{ route('appointments.ajax.store') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.error) {
            document.getElementById('formError').innerText = res.error;
        } else {
            location.reload(); // refresh calendar
        }
    });
});
</script>