<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Calendar</h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('availability.calendar', ['month' => $start->copy()->subMonth()->month, 'year' => $start->copy()->subMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">←</a>

                <span class="px-4 py-1 font-semibold text-gray-700">
                    {{ $start->format('F Y') }}
                </span>

                <a href="{{ route('availability.calendar', ['month' => $start->copy()->addMonth()->month, 'year' => $start->copy()->addMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">→</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6 bg-white p-6 rounded-xl shadow-sm">

        <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-500 mb-3">
            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
            <div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden">

            @php $current = $start->copy()->startOfWeek(); @endphp

            @while($current <= $end->copy()->endOfWeek())

                @php
                    $date = $current->toDateString();
                    $dayAppointments = $appointments[$date] ?? collect();
                    $isCurrentMonth = $current->month == $start->month;
                @endphp

                <div id="day-{{ $date }}"
                     data-date="{{ $date }}"
                     class="day-cell h-28 p-2 text-xs relative transition
                     {{ $isCurrentMonth ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 text-gray-400' }}">

                    <div class="font-semibold text-sm text-gray-700">
                        {{ $current->day }}
                    </div>

                    <div class="mt-1 space-y-1 overflow-hidden">

                        @foreach($dayAppointments->take(3) as $appt)
                            <div class="appointment bg-blue-500 text-white px-1.5 py-0.5 rounded text-[10px] truncate cursor-move"
                                 draggable="true"
                                 data-id="{{ $appt->id }}">
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
    <div id="dayModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">

            <h3 class="text-lg font-semibold mb-4">Create Appointment</h3>

            <form id="appointmentForm" class="space-y-3">
                @csrf

                <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor->id }}">
                <input type="hidden" name="appointment_date" id="modalDate">
                <input type="hidden" name="appointment_time" id="selectedTimeInput">

                <p id="displayDate" class="text-sm text-gray-500"></p>

                <select name="patient_id" class="w-full border rounded-lg px-3 py-2">
                    @foreach(\App\Models\Patient::all() as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>

                <div id="slotContainer" class="grid grid-cols-3 gap-2"></div>

                <div id="formError" class="text-red-500 text-sm"></div>

                <button class="w-full bg-blue-600 text-white py-2 rounded-lg">
                    Save Appointment
                </button>
            </form>

            <button onclick="closeModal()" class="mt-3 w-full text-gray-500">
                Cancel
            </button>
        </div>
    </div>
</x-app-layout>

<script>
const leaves = @json($leaves ?? []);
let selectedTime = null;
let draggedAppointmentId = null;

function isOnLeave(date) {
    return leaves.some(l => date >= l.start_date && date <= l.end_date);
}

// INIT CELLS
document.querySelectorAll('.day-cell').forEach(cell => {
    const date = cell.dataset.date;

    if (isOnLeave(date)) {
        cell.classList.add('bg-red-100', 'cursor-not-allowed');
        cell.innerHTML += `<div class="text-[10px] text-red-600">Unavailable</div>`;
    } else {
        cell.onclick = () => openDay(date);
    }

    // DROP EVENTS
    cell.addEventListener('dragover', e => {
        e.preventDefault();
        cell.classList.add('bg-blue-100');
    });

    cell.addEventListener('dragleave', () => {
        cell.classList.remove('bg-blue-100');
    });

    cell.addEventListener('drop', () => {
        cell.classList.remove('bg-blue-100');

        if (isOnLeave(date)) {
            alert('Doctor is on leave');
            return;
        }

        fetch(`/appointments/${draggedAppointmentId}/reschedule`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ appointment_date: date })
        })
        .then(res => res.json())
        .then(res => {
            if (res.error) alert(res.error);
            else location.reload();
        });
    });
});

// DRAG
document.querySelectorAll('.appointment').forEach(el => {

    el.addEventListener('dragstart', function (e) {
        e.stopPropagation();
        draggedAppointmentId = this.dataset.id;
        this.classList.add('opacity-50');
    });

    el.addEventListener('dragend', function () {
        this.classList.remove('opacity-50');
    });
});

// MODAL
function openDay(date) {
    document.getElementById('dayModal').classList.remove('hidden');
    document.getElementById('dayModal').classList.add('flex');

    document.getElementById('modalDate').value = date;
    document.getElementById('displayDate').innerText = date;

    loadSlots(date);
}

function closeModal() {
    document.getElementById('dayModal').classList.add('hidden');
    document.getElementById('dayModal').classList.remove('flex');
}

// LOAD SLOTS
function loadSlots(date) {
    const doctorId = "{{ auth()->user()->doctor->id }}";

    fetch(`/booking/slots?doctor_id=${doctorId}&date=${date}`)
        .then(res => res.json())
        .then(slots => {

            let container = document.getElementById('slotContainer');
            container.innerHTML = '';

            if (!slots.length) {
                container.innerHTML = '<p class="text-gray-400">No slots</p>';
                return;
            }

            slots.forEach(slot => {
                let btn = document.createElement('button');
                btn.innerText = slot;

                btn.className = "border px-2 py-1 rounded text-sm";

                btn.onclick = () => {
                    document.querySelectorAll('#slotContainer button')
                        .forEach(b => b.classList.remove('bg-blue-600','text-white'));

                    btn.classList.add('bg-blue-600','text-white');

                    selectedTime = slot;
                    document.getElementById('selectedTimeInput').value = slot;
                };

                container.appendChild(btn);
            });
        });
}

// SAVE
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!selectedTime) {
        document.getElementById('formError').innerText = "Select a slot";
        return;
    }

    let data = new FormData(this);

    fetch("{{ route('appointments.ajax.store') }}", {
        method: "POST",
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.error) document.getElementById('formError').innerText = res.error;
        else location.reload();
    });
});
</script>