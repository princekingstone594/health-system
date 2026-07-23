<x-app-layout>
    <x-slot name="header">Create Schedule</x-slot>

    <form method="POST" action="{{ route('schedules.store') }}" class="space-y-4">
        @csrf

        <select name="clinic_id" class="w-full border p-2 rounded">
            @foreach($clinics as $clinic)
                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
            @endforeach
        </select>

        <select name="day" class="w-full border p-2 rounded">
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
        </select>

        <input type="time" name="start_time" class="w-full border p-2 rounded">
        <input type="time" name="end_time" class="w-full border p-2 rounded">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</x-app-layout>