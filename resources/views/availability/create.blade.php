<x-app-layout>
    <div class="max-w-2xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Set Availability</h2>

        <form method="POST" action="{{ route('availability.store') }}">
            @csrf

            <!-- Day -->
            <select name="day_of_week" class="w-full mb-3 border p-2 rounded">
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
                <option value="sunday">Sunday</option>
            </select>

            <!-- Start -->
            <input type="time" name="start_time"
                   class="w-full mb-3 border p-2 rounded">

            <!-- End -->
            <input type="time" name="end_time"
                   class="w-full mb-3 border p-2 rounded">

            <!-- Slot duration -->
            <input type="number" name="slot_duration"
                   placeholder="Slot duration (minutes)"
                   class="w-full mb-3 border p-2 rounded"
                   value="30">

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Save Availability
            </button>

        </form>
    </div>
</x-app-layout>