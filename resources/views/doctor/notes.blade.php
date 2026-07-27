<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">
            Add Notes for Appointment
        </h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.notes.store', $appointment->id) }}">
            @csrf

            {{-- Notes --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold">Doctor Notes</label>
                <textarea name="doctor_notes" class="w-full border p-2 rounded" rows="4">
                    {{ $appointment->doctor_notes }}
                </textarea>
            </div>

            {{-- Diagnosis --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold">Diagnosis</label>
                <textarea name="diagnosis" class="w-full border p-2 rounded" rows="3">
                    {{ $appointment->diagnosis }}
                </textarea>
            </div>

            {{-- Prescription --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold">Prescription</label>
                <textarea name="prescription" class="w-full border p-2 rounded" rows="3">
                    {{ $appointment->prescription }}
                </textarea>
            </div>

            {{-- Share toggle --}}
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_shared_with_patient" value="1"
                        {{ $appointment->is_shared_with_patient ? 'checked' : '' }}>
                    Share with patient
                </label>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save Notes
            </button>

        </form>

    </div>
</x-app-layout>