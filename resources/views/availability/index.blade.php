<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Doctor Availability</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow">

        @if(session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('availability.store') }}">
            @csrf

            <div class="space-y-4">

                @foreach($days as $day)
                    @php
                        $availability = $availabilities[$day] ?? null;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center border-b pb-3">

                        <!-- Day -->
                        <div class="font-semibold">
                            {{ $day }}
                        </div>

                        <!-- Active -->
                        <div>
                            <input type="checkbox"
                                   name="days[{{ $day }}][active]"
                                   {{ $availability && $availability->is_active ? 'checked' : '' }}>
                            <span class="text-sm">Available</span>
                        </div>

                        <!-- Start -->
                        <div>
                            <input type="time"
                                   name="days[{{ $day }}][start_time]"
                                   value="{{ $availability->start_time ?? '' }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <!-- End -->
                        <div>
                            <input type="time"
                                   name="days[{{ $day }}][end_time]"
                                   value="{{ $availability->end_time ?? '' }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <!-- Slot -->
                        <div>
                            <select name="days[{{ $day }}][slot_duration]" class="w-full border rounded p-2">
                                <option value="15" {{ optional($availability)->slot_duration == 15 ? 'selected' : '' }}>15 min</option>
                                <option value="30" {{ optional($availability)->slot_duration == 30 ? 'selected' : '' }}>30 min</option>
                                <option value="60" {{ optional($availability)->slot_duration == 60 ? 'selected' : '' }}>1 hour</option>
                            </select>
                        </div>

                    </div>
                @endforeach

            </div>

            <button class="mt-6 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Save Availability
            </button>

        </form>
    </div>
</x-app-layout>