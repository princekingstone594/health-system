<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Calendar</h2>

            <!-- Navigation -->
            <div class="flex items-center gap-2">
                <a href="{{ route('availability.calendar', ['month' => $start->copy()->subMonth()->month, 'year' => $start->copy()->subMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition">←</a>

                <span class="px-4 py-1 font-semibold text-gray-700">
                    {{ $start->format('F Y') }}
                </span>

                <a href="{{ route('availability.calendar', ['month' => $start->copy()->addMonth()->month, 'year' => $start->copy()->addMonth()->year]) }}"
                   class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition">→</a>
            </div>
        </div>
    </x-slot>

    <!-- Calendar Card -->
    <div class="max-w-7xl mx-auto mt-6 bg-white p-6 rounded-xl shadow-sm">

        <!-- Days -->
        <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-500 mb-3">
            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
            <div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden">

            @php
                $current = $start->copy()->startOfWeek();
            @endphp

            @while($current <= $end->copy()->endOfWeek())

                @php
                    $date = $current->toDateString();
                    $dayAppointments = $appointments[$date] ?? collect();
                    $isCurrentMonth = $current->month == $start->month;
                @endphp

                <div id="day-{{ $date }}"
                     class="h-28 p-2 text-xs relative transition
                     {{ $isCurrentMonth ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 text-gray-400' }}">

                    <!-- Date -->
                    <div class="font-semibold text-sm text-gray-700">
                        {{ $current->day }}
                    </div>

                    <!-- Events -->
                    <div class="mt-1 space-y-1 overflow-hidden">

                        @foreach($dayAppointments->take(3) as $appt)
                            <div class="bg-blue-500 text-white px-1.5 py-0.5 rounded text-[10px] truncate">
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

    <!-- ===================== -->
    <!-- MODAL -->
    <!-- ===================== -->
    <div id="dayModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">

            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Create Appointment
            </h3>

            <form id="appointmentForm" class="space-y-3">
                @csrf

                <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor->id }}">
                <input type="hidden" name="appointment_date" id="modalDate">

                <p class="text-sm text-gray-500" id="displayDate"></p>

                <!-- Patient -->
                <select name="patient_id"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Models\Patient::all() as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>

                <!-- Time -->
                <select name="appointment_time" id="modalTime"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Select time</option>
                </select>

                <!-- Error -->
                <div id="formError" class="text-red-500 text-sm"></div>

                <button
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Save Appointment
                </button>
            </form>

            <button onclick="closeModal()"
                class="mt-3 w-full text-gray-500 hover:text-gray-700">
                Cancel
            </button>
        </div>
    </div>

</x-app-layout>

<script>
const leaves = @json($leaves ?? []);

function isOnLeave(date) {
    return leaves.some(l => date >= l.start_date && date <= l.end_date);
}

// Apply leave + click
document.querySelectorAll('[id^="day-"]').forEach(cell => {
    const date = cell.id.replace('day-', '');

    if (isOnLeave(date)) {
        cell.classList.add('bg-red-100', 'cursor-not-allowed');
        cell.innerHTML += `<div class="text-[10px] text-red-600 mt-1">Unavailable</div>`;
    } else {
        cell.style.cursor = 'pointer';
        cell.onclick = () => openDay(date);
    }
});

function openDay(date) {
    document.getElementById('dayModal').classList.remove('hidden');

    document.getElementById('modalDate').value = date;
    document.getElementById('displayDate').innerText = date;

    const doctorId = "{{ auth()->user()->doctor->id }}";
    const timeSelect = document.getElementById('modalTime');

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
            location.reload();
        }
    });
});
</script>