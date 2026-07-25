<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Book Appointment
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-xl shadow mt-6">

        <!-- Errors -->
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <!-- Patient -->
            <div class="mb-4">
                <label class="block mb-1">Patient</label>
                <input type="text"
                       value="{{ auth()->user()->name }}"
                       class="w-full border rounded px-3 py-2 bg-gray-100"
                       disabled>
            </div>

            <!-- Doctor -->
            <div class="mb-4">
                <label class="block mb-1">Select Doctor</label>
                <select name="doctor_id" id="doctor_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Doctor --</option>
                    @foreach(\App\Models\User::where('role','doctor')->get() as $doctor)
                        <option value="{{ $doctor->id }}">
                            Dr. {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date -->
            <div class="mb-4">
                <label class="block mb-1">Select Date</label>
                <input type="date"
                       name="date"
                       id="date"
                       class="w-full border rounded px-3 py-2">
            </div>

            <!-- Slots -->
            <div class="mb-4">
                <label class="block mb-1">Available Time Slots</label>

                <div id="slots-container" class="grid grid-cols-3 gap-2">
                    <p class="text-gray-500 text-sm col-span-3">
                        Select doctor & date to load slots
                    </p>
                </div>
            </div>

            <!-- Submit -->
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                Book Appointment
            </button>

        </form>
    </div>

    <!-- 🧠 AJAX SCRIPT -->
    <script>
        const doctorSelect = document.getElementById('doctor_id');
        const dateInput = document.getElementById('date');
        const slotsContainer = document.getElementById('slots-container');

        function loadSlots() {
            const doctorId = doctorSelect.value;
            const date = dateInput.value;

            if (!doctorId || !date) {
                slotsContainer.innerHTML = `
                    <p class="text-gray-500 text-sm col-span-3">
                        Select doctor & date to load slots
                    </p>`;
                return;
            }

            slotsContainer.innerHTML = `<p class="text-blue-500 col-span-3">Loading...</p>`;

            fetch(`/appointments/slots?doctor_id=${doctorId}&date=${date}`)
                .then(res => res.json())
                .then(data => {
                    slotsContainer.innerHTML = '';

                    if (data.length === 0) {
                        slotsContainer.innerHTML = `
                            <p class="text-red-500 text-sm col-span-3">
                                No available slots
                            </p>`;
                        return;
                    }

                    data.forEach(slot => {
                        const label = document.createElement('label');
                        label.className = "border rounded px-2 py-2 text-center cursor-pointer hover:bg-blue-50";

                        label.innerHTML = `
                            <input type="radio" name="time" value="${slot}" class="hidden">
                            <span>${slot}</span>
                        `;

                        label.addEventListener('click', () => {
                            document.querySelectorAll('#slots-container label')
                                .forEach(l => l.classList.remove('bg-blue-100', 'border-blue-500'));

                            label.classList.add('bg-blue-100', 'border-blue-500');
                        });

                        slotsContainer.appendChild(label);
                    });
                })
                .catch(() => {
                    slotsContainer.innerHTML = `
                        <p class="text-red-500 text-sm col-span-3">
                            Failed to load slots
                        </p>`;
                });
        }

        doctorSelect.addEventListener('change', loadSlots);
        dateInput.addEventListener('change', loadSlots);
    </script>

</x-app-layout>