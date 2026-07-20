<x-app-layout>
    <div class="max-w-2xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Set Availability</h2>

        <form method="POST" action="{{ route('availability.store') }}">
            @csrf

            <select name="day" class="w-full mb-3 border p-2">
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
            </select>

            <input type="time" name="start_time" class="w-full mb-3 border p-2">
            <input type="time" name="end_time" class="w-full mb-3 border p-2">

            <input type="number" name="slot_duration" placeholder="Slot (minutes)" class="w-full mb-3 border p-2" value="30">

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>

        </form>
    </div>
</x-app-layout>