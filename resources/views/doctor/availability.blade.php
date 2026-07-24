<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Manage Availability</h2>
    </x-slot>

    <div class="p-6">

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.availability.store') }}" class="mb-6">
            @csrf

            <select name="day_of_week" required>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
            </select>

            <input type="time" name="start_time" required>
            <input type="time" name="end_time" required>

            <input type="number" name="slot_duration" placeholder="Minutes" required>

            <button class="bg-blue-600 text-white px-4 py-2">Add</button>
        </form>

        <h3 class="font-semibold mb-2">Your Availability</h3>

        @foreach($availabilities as $a)
            <div class="border-b py-2">
                {{ $a->day_of_week }} | {{ $a->start_time }} - {{ $a->end_time }} ({{ $a->slot_duration }} mins)
            </div>
        @endforeach

    </div>
</x-app-layout>