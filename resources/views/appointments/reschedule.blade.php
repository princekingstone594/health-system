<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Reschedule Appointment</h2>
    </x-slot>

    <div class="p-6 max-w-lg mx-auto bg-white rounded shadow">

        <form method="POST" action="{{ route('appointments.reschedule', $appointment->id) }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">New Date</label>
                <input type="date" name="date" class="w-full border p-2 rounded"
                       value="{{ $appointment->date }}">
            </div>

            <div class="mb-4">
                <label class="block mb-1">New Time</label>
                <input type="time" name="time" class="w-full border p-2 rounded"
                       value="{{ $appointment->time }}">
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update Appointment
            </button>
        </form>

    </div>
</x-app-layout>